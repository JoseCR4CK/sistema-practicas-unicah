<?php

namespace App\Http\Controllers;

use App\Models\PracticeRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentApplicationController extends Controller
{
    public function index(Request $request)
    {
        return view('coordinacion.solicitudes', [
            'solicitudes' => PracticeRequest::whereHas('student', fn ($query) => $query->where('user_id', $request->user()->id))
                ->with(['student.user', 'practice.company'])->latest('submitted_at')->get(),
            'studentPortal' => true,
            'practicas' => $request->user()->student?->practices()->get() ?? collect(),
        ]);
    }

    public function show(Request $request, PracticeRequest $application)
    {
        abort_unless($application->student->user_id === $request->user()->id, 403);

        return view('coordinacion.solicitud-detalle', [
            'solicitud' => $application->load(['student.user', 'practice.company', 'reviewer']),
            'studentPortal' => true,
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'practice_id' => ['nullable', 'integer'],
            'student_observation' => ['nullable', 'string', 'max:5000'],
        ]);
        $application = DB::transaction(function () use ($request, $fields) {
            $student = Student::where('user_id', $request->user()->id)->lockForUpdate()->firstOrFail();
            $practice = empty($fields['practice_id']) ? null : $student->practices()->findOrFail($fields['practice_id']);
            $latest = $student->applications()->where('practice_id', $practice?->id)->latest('id')->first();
            if ($latest && $latest->status !== 'Rechazada') {
                return $latest;
            }

            return $student->applications()->create([
                'practice_id' => $practice?->id,
                'submitted_at' => now(),
                'student_observation' => $fields['student_observation'] ?? null,
            ]);
        });

        return redirect()->route('estudiante.solicitud', $application);
    }
}
