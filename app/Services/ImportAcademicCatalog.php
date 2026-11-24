<?php

namespace App\Services;

use App\Models\AcademicCareer;
use App\Models\AcademicSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportAcademicCatalog
{
    public const FACULTIES = ['Mercadotecnia', 'Psicología', 'Ingeniería en Ciencias de la Computación', 'Ingeniería Industrial', 'Ingeniería Civil', 'Gestión Estratégica de Empresas', 'Derecho', 'Enfermería'];

    public function run(): array
    {
        $data = json_decode(file_get_contents(database_path('data/academic_catalog.json')), true, flags: JSON_THROW_ON_ERROR);
        return DB::transaction(function () use ($data) {
            $faculties = [];
            foreach ([...self::FACULTIES, 'Generales'] as $name) {
                $faculties[$name] = AcademicCareer::firstOrCreate(['name' => $name], [
                    'slug' => Str::slug($name), 'type' => $name === 'Generales' ? 'GENERAL' : 'FACULTY',
                    'source' => $data['source'], 'status' => 'ACTIVE',
                ]);
                $faculties[$name]->type = $name === 'Generales' ? 'GENERAL' : 'FACULTY';
                if ($faculties[$name]->isDirty('type')) $faculties[$name]->save();
            }
            // Conservar carreras históricas y sus vínculos, pero no ofrecerlas en nuevos registros.
            AcademicCareer::whereNotIn('name', array_keys($faculties))->where('status', 'ACTIVE')->update(['status' => 'INACTIVE']);
            $rows = [];
            foreach (array_slice($data['sheets']['PLANES_CARRERA'], 1) as $row) {
                if (!in_array($row['A'], self::FACULTIES, true)) continue;
                $rows[] = ['faculty' => $row['A'], 'code' => $row['C'] ?: null, 'name' => $row['D'],
                    'credits' => $row['E'] === '' ? null : (float) $row['E'], 'type' => match ($row['F']) {
                        'General' => 'GENERAL', 'Slot electivo' => 'SLOT', 'Práctica' => 'PRACTICE', default => 'NORMAL',
                    }, 'period' => $row['B'] ?: null, 'metadata' => $row];
            }
            foreach (['GENERALES' => 'GENERAL', 'COPROGRAMATICAS' => 'COPROGRAMMATIC', 'ELECTIVAS' => 'ELECTIVE', 'INGLES' => 'ENGLISH'] as $sheet => $type) {
                foreach (array_slice($data['sheets'][$sheet], 1) as $row) {
                    $rows[] = ['faculty' => 'Generales', 'code' => $row['A'] ?: null, 'name' => $row['B'],
                        'credits' => $row['C'] === '' ? null : (float) $row['C'], 'type' => $type, 'period' => null, 'metadata' => $row];
                }
            }
            $nameKey = fn ($row) => $row['type'].':'.Str::upper(Str::ascii(preg_replace('/\s+/u', ' ', trim($row['name']))));
            $knownCodes = collect($rows)->filter(fn ($row) => $row['code'] !== null)->groupBy($nameKey);
            $groups = collect($rows)->groupBy(function ($row) use ($knownCodes, $nameKey) {
                $code = $row['code'];
                if (!$code) {
                    $matches = ($knownCodes[$nameKey($row)] ?? collect())->filter(fn ($known) => $known['credits'] === $row['credits'])->pluck('code')->unique();
                    if ($matches->count() === 1) $code = $matches->first();
                }
                return hash('sha256', $code ? 'code:'.$code : 'name:'.$nameKey($row));
            });
            foreach ($groups as $key => $group) {
                $row = $group->first();
                $row['code'] = $group->pluck('code')->filter()->first();
                $values = $group->pluck('credits')->filter(fn ($credit) => $credit !== null)->unique()->values();
                $credits = $values->count() === 1 ? $values->first() : null;
                $disputedNames = ['MECANICA DE SOLIDOS I', 'CIENCIA DE LOS MATERIALES', 'CIENCIAS DE LOS MATERIALES',
                    'INGENIERIA HIDRAULICA Y DE RIEGOS', 'INGENIERIA HIDRAULICA Y DE RIESGOS',
                    'DERECHO DE LA NINEZ Y LA MUJER', 'DERECHO INTERNACIONAL PRIVADO', 'PRACTICA FORENSE CIVIL'];
                $review = $values->count() > 1 || in_array($row['code'], ['PS400', 'CT201', 'FS201'], true)
                    || in_array(Str::upper(Str::ascii(trim($row['name']))), $disputedNames, true);
                $type = $group->contains('type', 'GENERAL') ? 'GENERAL' : $row['type'];
                $hours = AcademicClassTime::hoursForCredits($credits, $type);
                $primary = $faculties[$row['faculty']];
                $subject = AcademicSubject::where('catalog_key', $key)->first();
                if (!$subject) {
                    // Adoptar un registro legado solo cuando nombre/código/créditos coinciden sin ambigüedad.
                    $candidates = AcademicSubject::whereNull('catalog_key')->where('code', $row['code'])->get()->filter(fn ($existing) =>
                        Str::upper(Str::ascii($existing->name)) === Str::upper(Str::ascii($row['name']))
                        && ($existing->credits === null ? $credits === null : (float) $existing->credits === (float) $credits));
                    if ($candidates->count() === 1) {
                        $subject = $candidates->first();
                        $subject->update(['catalog_key' => $key, 'subject_type' => $type, 'review_required' => $review]);
                    }
                }
                $subject ??= AcademicSubject::create([
                    'catalog_key' => $key,
                    'academic_career_id' => $primary->id, 'code' => $row['code'], 'name' => $row['name'],
                    'credits' => $credits, 'subject_type' => $type, 'weekly_class_hours' => $hours,
                    'weekly_minutes' => $hours === null ? null : $hours * AcademicClassTime::CLASS_MINUTES,
                    'review_required' => $review, 'catalog_notes' => $review ? 'Conflicto conservado en la fuente académica; consultar CONFLICTOS. No se seleccionó un valor institucional definitivo.' : null,
                    'status' => 'ACTIVE', 'source' => $data['source'].' #'.$data['sha256'],
                ]);
                foreach ($group->groupBy('faculty') as $facultyName => $plans) {
                    $facultyId = $faculties[$facultyName]->id;
                    $pivot = DB::table('academic_career_subject')->where('academic_career_id', $facultyId)->where('academic_subject_id', $subject->id);
                    $metadata = json_encode($plans->pluck('metadata')->all(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                    $fields = ['academic_period' => $plans->pluck('period')->filter()->unique()->implode(', ') ?: null, 'plan_metadata' => $metadata];
                    if (!$pivot->exists()) {
                        DB::table('academic_career_subject')->insert($fields + ['academic_career_id' => $facultyId, 'academic_subject_id' => $subject->id, 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
                    } elseif ($pivot->value('plan_metadata') === null) {
                        $pivot->update($fields); // Completar solo metadatos vacíos; no restaurar estados editados.
                    }
                }
            }
            return ['subjects' => $groups->count(), 'plan_rows' => count($data['sheets']['PLANES_CARRERA']) - 1, 'conflicts' => count($data['sheets']['CONFLICTOS']) - 1];
        });
    }
}
