<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use App\Models\PracticeRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PracticeRequestController extends Controller
{
    public function index()
    {
        return view('coordinacion.solicitudes', [
            'solicitudes' => PracticeRequest::with(['student.user', 'practice.company'])->latest('submitted_at')->get(),
        ]);
    }

    public function show(PracticeRequest $application)
    {
        abort_if($application->practice && $application->practice->student_id !== $application->student_id, 404);

        return view('coordinacion.solicitud-detalle', [
            'solicitud' => $application->load(['student.user', 'practice.company', 'reviewer']),
        ]);
    }

    public function review(Request $request, PracticeRequest $application)
    {
        abort_if($application->practice && $application->practice->student_id !== $application->student_id, 404);
        $fields = $request->validate([
            'status' => ['required', Rule::in(['Aprobada', 'Rechazada'])],
            'observation' => ['nullable', 'required_if:status,Rechazada', 'string', 'max:5000'],
        ]);

        $changed = PracticeRequest::whereKey($application->id)->where('status', 'Pendiente')->update([
            'status' => $fields['status'],
            'observation' => $fields['observation'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'deadline_restarted_at' => $fields['status'] === 'Rechazada' ? now() : null,
        ]);
        abort_unless($changed === 1, 409, 'La solicitud ya fue gestionada. Actualiza la página.');

        return redirect()->route('coordinacion.solicitud', $application)->with('status', 'Solicitud actualizada.');
    }
}
