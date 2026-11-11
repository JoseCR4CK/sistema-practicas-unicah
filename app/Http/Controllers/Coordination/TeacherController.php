<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::with(['user', 'practices.visits', 'practices.followups', 'practices.monograph.reviews'])->withCount([
            'practices as students_count' => fn ($query) => $query->select(DB::raw('count(distinct student_id)')),
            'practices as active_practices_count' => fn ($query) => $query->where('status', Practice::ACTIVE),
        ])->orderBy('id')->get();
        foreach ($teachers as $teacher) {
            $teacher->upcoming_visits_count = $teacher->practices->flatMap->visits
                ->filter(fn ($visit) => in_array($visit->effectiveStatus(), ['Habilitada', 'Programada'], true))->count();
            $teacher->has_alerts = $teacher->upcoming_visits_count > 0
                || $teacher->practices->flatMap->followups->contains('status', 'Pendiente')
                || $teacher->practices->contains(fn ($practice) => $practice->monograph?->statusLabel() === 'En revisión');
        }

        return view('coordinacion.docentes', [
            'docentes' => $teachers,
            'docentesAsignados' => $teachers->where('students_count', '>', 0)->count(),
            'practicantes' => Practice::whereNotNull('teacher_id')->distinct()->count('student_id'),
            'visitasPorAtender' => $teachers->sum('upcoming_visits_count'),
            'docentesConAlertas' => $teachers->where('has_alerts', true)->count(),
        ]);
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['user', 'practices.student.user', 'practices.company', 'academicLoads']);

        return view('coordinacion.docente-detalle', [
            'docente' => $teacher,
            'practicantes' => $teacher->practices->unique('student_id')->count(),
            'practicasActivas' => $teacher->practices->where('status', Practice::ACTIVE)->count(),
        ]);
    }
}
