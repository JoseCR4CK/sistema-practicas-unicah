<?php

namespace App\Services;

use App\Models\AcademicLoad;
use App\Models\AcademicSubject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssociateLegacyAcademicLoads
{
    public function run(): array
    {
        $result = ['associated' => [], 'pending' => []];
        foreach (AcademicLoad::whereNull('academic_subject_id')->get() as $load) {
            $associated = DB::transaction(function () use ($load) {
                Teacher::whereKey($load->teacher_id)->lockForUpdate()->firstOrFail();
                $load = AcademicLoad::whereKey($load->id)->lockForUpdate()->firstOrFail();
                if ($load->academic_subject_id !== null) return true;
                $normalize = fn ($text) => Str::lower(preg_replace('/\s+/u', ' ', trim($text ?? '')));
                $matches = AcademicSubject::with('career')->where('status', 'ACTIVE')->get()->filter(fn ($subject) =>
                    $normalize($subject->name) === $normalize($load->subject)
                    && $normalize($subject->career->name) === $normalize($load->career));
                if ($matches->count() !== 1) return false;
                $subject = $matches->first();
                if (!$subject->selectable() || $load->credits === null || $load->weekly_class_hours === null
                    || (float) $load->credits !== (float) $subject->credits
                    || (float) $load->weekly_class_hours !== (float) $subject->confirmedWeeklyHours()
                    || $load->scheduledWeeklyMinutes() > $subject->confirmedWeeklyHours() * AcademicClassTime::CLASS_MINUTES) return false;
                $before = $load->only(['academic_career_id', 'academic_subject_id']);
                $load->academic_career_id = $subject->academic_career_id;
                $load->academic_subject_id = $subject->id;
                $load->revision++;
                $load->save();
                $load->changes()->create([
                    'user_id' => $load->recorded_by,
                    'reason' => 'Asociación técnica automática al catálogo por coincidencia única de carrera, asignatura, créditos y horas. Usuario referenciado: autor original de la carga; datos originales conservados.',
                    'before' => $before, 'after' => $load->only(['academic_career_id', 'academic_subject_id']),
                ]);

                return true;
            });
            $result[$associated ? 'associated' : 'pending'][] = $load->id;
        }

        return $result;
    }
}
