<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use Illuminate\Http\Request;

class TeacherPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        return $this->page($request, 'docente.dashboard');
    }

    public function practitioners(Request $request)
    {
        return $this->page($request, 'docente.practicantes');
    }

    public function alerts(Request $request)
    {
        return $this->page($request, 'docente.alertas');
    }

    private function page(Request $request, string $view)
    {
        $practices = Practice::whereHas('teacher', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['student.user', 'company', 'visits', 'followups.teacher.user'])->get();
        // Estas tres pantallas conservan sus plantillas HTML de JavaScript.
        // Escapamos los textos antes de interpolarlos en esas plantillas.
        $practiceData = $practices->map(fn ($practice) => [
            'id' => $practice->id,
            'student_id' => $practice->student_id,
            'estudiante' => e($practice->student->user->name),
            'cuenta' => e($practice->student->account_number),
            'carrera' => e($practice->student->career),
            'empresa' => e($practice->company?->official_name ?? 'Sin empresa asignada'),
            'etapa' => e($practice->stage),
            'progreso' => $practice->progress,
            'inicio' => $practice->start_date?->format('Y-m-d'),
            'status' => $practice->status,
        ]);
        $visitData = $practices->flatMap(fn ($practice) => $practice->visits->map(fn ($visit) => [
            'id' => $visit->id,
            'practice_id' => $practice->id,
            'student_id' => $practice->student_id,
            'estudiante' => e($practice->student->user->name),
            'empresa' => e($practice->company?->official_name ?? 'Sin empresa asignada'),
            'inicio_practica' => $practice->start_date?->format('Y-m-d'),
            'estado' => $visit->effectiveStatus() === 'Pendiente' ? 'No habilitada' : $visit->effectiveStatus(),
            'fecha_programada' => $visit->scheduled_date?->format('Y-m-d'),
            'fecha_realizada' => $visit->completed_date?->format('Y-m-d'),
        ]))->values();

        $followupData = $practices->flatMap(fn ($practice) => $practice->followups->map(fn ($followup) => [
            'id' => $followup->id,
            'student_id' => $practice->student_id,
            'estudiante' => e($practice->student->user->name),
            'cuenta' => e($practice->student->account_number),
            'docente' => e($followup->teacher->user->name),
            'fecha' => $followup->date->format('Y-m-d'),
            'tipo' => e($followup->type),
            'titulo' => e($followup->title),
            'observacion' => e($followup->observation ?? $followup->description),
            'estado' => e($followup->status),
        ]))->values();

        return view($view, ['practiceData' => $practiceData, 'visitData' => $visitData, 'followupData' => $followupData]);
    }

    public function practitioner(Request $request, int $id)
    {
        $practice = Practice::where('student_id', $id)
            ->whereHas('teacher', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['student.user', 'company', 'visits'])->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [Practice::ACTIVE])
            ->latest('id')->firstOrFail();

        return view('docente.seguimiento', [
            'practicante' => [
                'nombre' => $practice->student->user->name,
                'cuenta' => $practice->student->account_number,
                'carrera' => $practice->student->career,
                'empresa' => $practice->company?->official_name ?? 'Sin empresa asignada',
                'etapa' => $practice->stage,
                'progreso' => $practice->progress,
                'inicio' => $practice->start_date?->format('d/m/Y') ?? 'Pendiente',
                'finalizacion' => $practice->end_date?->format('d/m/Y') ?? 'Pendiente',
                'dia' => $practice->currentDay(),
                'visita_estado' => $practice->visits->sortByDesc('id')->first()?->effectiveStatus() ?? 'Sin visita registrada',
                'monografia_estado' => 'Pendiente de integración',
                'defensa_estado' => 'Pendiente de integración',
            ],
        ]);
    }
}
