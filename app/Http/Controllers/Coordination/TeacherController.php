<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\AcademicCareer;
use App\Validation\AccessMessages;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $fields = $request->validate(['faculty' => ['nullable', 'integer', \Illuminate\Validation\Rule::exists('academic_careers', 'id')->where('status', 'ACTIVE')]], AccessMessages::messages($request), AccessMessages::attributes());
        $teachers = Teacher::with(['user', 'practices.visits', 'practices.followups', 'practices.monograph.reviews'])->withCount([
            'practices as students_count' => fn ($query) => $query->select(DB::raw('count(distinct student_id)')),
            'practices as active_practices_count' => fn ($query) => $query->where('status', Practice::ACTIVE),
        ])->when($fields['faculty'] ?? null, fn ($query, $faculty) => $query->approvedActive()->whereHas('faculties', fn ($units) => $units->where('academic_careers.id', $faculty)))->orderBy('id')->get();
        foreach ($teachers as $teacher) {
            $teacher->upcoming_visits_count = $teacher->practices->flatMap->visits
                ->filter(fn ($visit) => in_array($visit->effectiveStatus(), ['Habilitada', 'Programada'], true))->count();
            $teacher->has_alerts = $teacher->upcoming_visits_count > 0
                || $teacher->practices->flatMap->followups->contains('status', 'Pendiente')
                || $teacher->practices->contains(fn ($practice) => $practice->monograph?->statusLabel() === 'En revisión');
        }

        return view('coordinacion.docentes', [
            'faculties' => AcademicCareer::where('status', 'ACTIVE')->orderBy('name')->get(), 'selectedFaculty' => $fields['faculty'] ?? null,
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
            'faculties' => AcademicCareer::where('status', 'ACTIVE')->orderBy('name')->get(),
            'docente' => $teacher,
            'practicantes' => $teacher->practices->unique('student_id')->count(),
            'practicasActivas' => $teacher->practices->where('status', Practice::ACTIVE)->count(),
        ]);
    }

    public function updateFaculty(Request $request, Teacher $teacher)
    {
        $fields = $request->validate(['academic_career_id' => ['required', 'integer', \Illuminate\Validation\Rule::exists('academic_careers', 'id')->where('status', 'ACTIVE')]], AccessMessages::messages($request), AccessMessages::attributes());
        DB::transaction(function () use ($teacher, $fields, $request) {
            $locked = Teacher::lockForUpdate()->findOrFail($teacher->id);
            $career = AcademicCareer::findOrFail($fields['academic_career_id']);
            $previous = $locked->faculties()->wherePivot('is_primary', true)->pluck('academic_careers.id')->all();
            $locked->faculties()->detach(array_values(array_diff($previous, [$career->id])));
            $locked->faculties()->syncWithoutDetaching([$career->id => ['is_primary' => true]]);
            $locked->update(['faculty' => $career->name]);
            $application = \App\Models\AccessRequest::where('user_id', $locked->user_id)->where('status', 'APPROVED')->first();
            $application?->events()->create(['actor_id' => $request->user()->id, 'action' => 'Facultad principal corregida', 'before_status' => 'APPROVED', 'after_status' => 'APPROVED', 'note' => $career->name]);
        });
        return back()->with('status', 'Facultad principal del docente actualizada.');
    }
}
