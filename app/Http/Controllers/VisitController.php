<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        $query = Visit::with(['practice.student.user', 'practice.teacher.user', 'practice.company', 'practice.companyResponsible']);
        if ($role === 'DOCENTE_SEGUIMIENTO') {
            $query->whereHas('practice.teacher', fn ($query) => $query->where('user_id', $request->user()->id));
        } elseif ($role === 'ESTUDIANTE') {
            $query->whereHas('practice.student', fn ($query) => $query->where('user_id', $request->user()->id));
        }
        $portal = match ($role) {
            'DOCENTE_SEGUIMIENTO' => 'docente',
            'ESTUDIANTE' => 'estudiante',
            default => 'coordinacion',
        };
        $studentPractice = $role === 'ESTUDIANTE' ? $request->user()->student?->preferredPractice() : null;
        $studentMessage = null;
        if ($role === 'ESTUDIANTE' && (!$studentPractice || !$studentPractice->visits()->exists())) {
            $studentMessage = match (true) {
                !$studentPractice => 'Aún no tienes una práctica registrada.',
                $studentPractice->reachedVisitDay() => 'Tu práctica ya se encuentra habilitada para la visita de seguimiento. Aún no hay una visita registrada.',
                $studentPractice->status !== Practice::ACTIVE => 'Tu práctica no tiene una visita registrada.',
                $studentPractice->start_date === null => 'La fecha real de inicio de tu práctica aún no está registrada.',
                default => 'Tu práctica aún no se encuentra habilitada para la visita de seguimiento. Puede realizarse a partir del día 45.',
            };
        }

        return view('academic.visits', [
            'visitas' => $query->latest('id')->get(),
            'portal' => $portal,
            'studentPractice' => $studentPractice,
            'studentMessage' => $studentMessage,
            'practicas' => $role === 'DOCENTE_SEGUIMIENTO'
                ? Practice::whereHas('teacher', fn ($query) => $query->where('user_id', $request->user()->id))->with('student.user')->get()
                : collect(),
        ]);
    }

    public function show(Request $request, Visit $visit)
    {
        Gate::authorize('view', $visit);
        $portal = match ($request->user()->role->name) {
            'DOCENTE_SEGUIMIENTO' => 'docente',
            'ESTUDIANTE' => 'estudiante',
            default => 'coordinacion',
        };

        return view('academic.visit-detail', [
            'visita' => $visit->load(['practice.student.user', 'practice.company', 'practice.companyResponsible', 'practice.teacher.user', 'performer']),
            'portal' => $portal,
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate(['practice_id' => ['required', 'integer'], 'submission_key' => ['required', 'uuid']]);
        $visit = DB::transaction(function () use ($request, $fields) {
            $practice = Practice::whereHas('teacher', fn ($query) => $query->where('user_id', $request->user()->id))
                ->lockForUpdate()->findOrFail($fields['practice_id']);
            $existing = Visit::where('submission_key', $fields['submission_key'])->first();
            if ($existing) {
                abort_unless($existing->practice_id === $practice->id, 409);
                return $existing;
            }

            return Visit::create(['practice_id' => $practice->id, 'submission_key' => $fields['submission_key']]);
        });

        return redirect()->route('docente.visita', $visit);
    }

    public function update(Request $request, Visit $visit)
    {
        Gate::authorize('update', $visit);
        $fields = $request->validate([
            'status' => ['required', Rule::in(['Programada', 'Realizada'])],
            'date' => ['required', 'date_format:Y-m-d'],
            'observation' => ['nullable', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($visit, $fields, $request) {
            Practice::whereKey($visit->practice_id)->lockForUpdate()->firstOrFail();
            $locked = Visit::whereKey($visit->id)->lockForUpdate()->firstOrFail();
            Gate::authorize('update', $locked);
            abort_if($locked->status === 'Realizada', 409, 'La visita ya fue realizada.');
            $enableDate = $locked->enabledFrom();
            if (!$enableDate || $fields['date'] < $enableDate->format('Y-m-d')) {
                throw ValidationException::withMessages(['date' => 'La visita puede realizarse a partir del día 45 de la práctica.']);
            }
            if ($fields['status'] === 'Realizada' && $fields['date'] > today()->format('Y-m-d')) {
                throw ValidationException::withMessages(['date' => 'La fecha de realización no puede ser futura.']);
            }
            $values = ['status' => $fields['status'], 'observation' => $fields['observation'] ?? null];
            if ($fields['status'] === 'Programada') {
                $values['scheduled_date'] = $fields['date'];
            } else {
                $values['completed_date'] = $fields['date'];
                $values['completed_by'] = $request->user()->id;
            }
            $locked->update($values);
        });

        return redirect()->route('docente.visita', $visit)->with('status', 'Visita actualizada.');
    }
}
