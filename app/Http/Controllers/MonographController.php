<?php

namespace App\Http\Controllers;

use App\Models\Monograph;
use App\Models\Practice;
use App\Services\MonographWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MonographController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        $query = Monograph::with(['practice.student.user', 'currentVersion', 'reviews']);
        $practices = Practice::with('student.user');
        if ($role === 'ESTUDIANTE') {
            $query->whereHas('practice.student', fn ($query) => $query->where('user_id', $request->user()->id));
            $practices->whereHas('student', fn ($query) => $query->where('user_id', $request->user()->id));
        } elseif ($role === 'DOCENTE_SEGUIMIENTO') {
            $query->whereHas('practice.teacher', fn ($query) => $query->where('user_id', $request->user()->id));
            $practices->whereHas('teacher', fn ($query) => $query->where('user_id', $request->user()->id));
        }

        return view('academic.monographs.index', [
            'monografias' => $query->latest('id')->get(),
            'practicas' => $practices->get(),
        ]);
    }

    public function show(Monograph $monograph)
    {
        Gate::authorize('view', $monograph);

        return view('academic.monographs.show', [
            'monografia' => $monograph->load(['practice.student.user', 'currentVersion', 'versions.submitter', 'reviews.reviewer', 'reviews.recorder']),
        ]);
    }

    public function submit(Request $request, MonographWorkflow $workflow)
    {
        $fields = $request->validate([
            'practice_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'observation' => ['nullable', 'string', 'max:5000'],
            'submission_key' => ['required', 'uuid'],
        ]);
        $practice = Practice::whereHas('student', fn ($query) => $query->where('user_id', $request->user()->id))
            ->findOrFail($fields['practice_id']);
        $monograph = $workflow->submit($practice, $request->user(), $fields);

        return redirect()->route('estudiante.monografia.show', $monograph)->with('status', 'Versión registrada.');
    }

    public function review(Request $request, Monograph $monograph, MonographWorkflow $workflow)
    {
        Gate::authorize('view', $monograph);
        $fields = $request->validate([
            'review_id' => ['required', 'integer'],
            'status' => ['required', Rule::in(['Aprobada', 'Requiere corrección'])],
            'observation' => ['nullable', 'required_if:status,Requiere corrección', 'string', 'max:5000'],
        ]);
        $workflow->review($monograph, $request->user(), $fields);
        $portal = $request->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'docente' : 'coordinacion';

        return redirect()->route($portal.'.monografia.show', $monograph)->with('status', 'Revisión registrada.');
    }
}
