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
        $teachers = Teacher::with('user')->withCount([
            'practices as students_count' => fn ($query) => $query->select(DB::raw('count(distinct student_id)')),
            'practices as active_practices_count' => fn ($query) => $query->where('status', Practice::ACTIVE),
        ])->orderBy('id')->get();

        return view('coordinacion.docentes', [
            'docentes' => $teachers,
            'docentesAsignados' => $teachers->where('students_count', '>', 0)->count(),
            'practicantes' => Practice::whereNotNull('teacher_id')->distinct()->count('student_id'),
        ]);
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['user', 'practices.student.user', 'practices.company']);

        return view('coordinacion.docente-detalle', [
            'docente' => $teacher,
            'practicantes' => $teacher->practices->unique('student_id')->count(),
            'practicasActivas' => $teacher->practices->where('status', Practice::ACTIVE)->count(),
        ]);
    }
}
