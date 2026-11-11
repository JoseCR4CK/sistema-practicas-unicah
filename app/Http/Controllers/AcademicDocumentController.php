<?php

namespace App\Http\Controllers;

use App\Models\AcademicDocument;
use App\Models\DocumentVersion;
use App\Models\Practice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AcademicDocumentController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->hasRole('ESTUDIANTE');
        $query = AcademicDocument::with(['practice.student.user', 'currentVersion']);
        if ($student) {
            $query->whereHas('practice.student', fn ($query) => $query->where('user_id', $request->user()->id));
        }

        return view($student ? 'estudiante.documentos' : 'coordinacion.documentos', [
            'documentos' => $query->latest('id')->get(),
            'practicas' => $student
                ? Practice::whereHas('student', fn ($query) => $query->where('user_id', $request->user()->id))->get()
                : collect(),
            'preferredPracticeId' => $student ? $request->user()->student?->preferredPractice()?->id : null,
        ]);
    }

    public function show(Request $request, AcademicDocument $document)
    {
        Gate::authorize('view', $document);

        return view('coordinacion.documento-detalle', [
            'documento' => $document->load(['practice.student.user', 'currentVersion', 'versions.uploader', 'versions.reviewer']),
            'studentPortal' => $request->user()->hasRole('ESTUDIANTE'),
        ]);
    }

    public function storeVersion(Request $request)
    {
        $fields = $request->validate([
            'practice_id' => ['required', 'integer'],
            'type' => ['required', Rule::in(array_keys(AcademicDocument::TYPES))],
            'submission_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $document = DB::transaction(function () use ($request, $fields) {
            $student = Student::where('user_id', $request->user()->id)->lockForUpdate()->firstOrFail();
            $practice = $student->practices()->findOrFail($fields['practice_id']);
            $document = AcademicDocument::firstOrCreate(['practice_id' => $practice->id, 'type' => $fields['type']]);
            $current = $document->versions()->latest('version')->first();
            abort_if($current && $current->status !== DocumentVersion::CORRECTION, 409, 'La versión actual debe revisarse antes de registrar otra.');
            $document->versions()->create([
                'version' => ($current?->version ?? 0) + 1,
                'status' => DocumentVersion::PENDING,
                'uploaded_by' => $request->user()->id,
                'uploaded_at' => now(),
                'submission_note' => $fields['submission_note'] ?? null,
            ]);

            return $document;
        });

        return redirect()->route('estudiante.documento', $document)->with('status', 'Versión registrada.');
    }

    public function review(Request $request, AcademicDocument $document)
    {
        Gate::authorize('review', $document);
        $fields = $request->validate([
            'version_id' => ['required', 'integer'],
            'status' => ['required', Rule::in([DocumentVersion::APPROVED, DocumentVersion::CORRECTION])],
            'observation' => ['nullable', 'required_if:status,Requiere corrección', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($document, $request, $fields) {
            // Serializa la revisión con la creación de versiones del mismo estudiante.
            Student::whereKey($document->practice->student_id)->lockForUpdate()->firstOrFail();
            $current = $document->versions()->latest('version')->firstOrFail();
            abort_unless($current->id === (int) $fields['version_id'] && $current->status === DocumentVersion::PENDING, 409, 'La versión ya cambió o fue revisada.');
            $current->update([
                'status' => $fields['status'],
                'observation' => $fields['observation'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('coordinacion.documento', $document)->with('status', 'Revisión registrada.');
    }
}
