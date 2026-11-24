<?php

namespace App\Services;

use App\Models\AccessRequest;
use App\Models\AcademicDocument;
use App\Models\AcademicSubject;
use App\Models\DocumentVersion;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegisterAccessRequest
{
    public function save(Request $request, string $type, ?AccessRequest $application = null): array
    {
        $student = $type === 'STUDENT';
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $rules = [
            'first_names' => ['required', 'string', 'max:120'], 'last_names' => ['required', 'string', 'max:120'],
            'dni' => ['required', 'string', 'max:40', Rule::unique('users'), Rule::unique('access_requests')->ignore($application?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users'), Rule::unique('access_requests')->ignore($application?->id)],
            'phone' => ['required', 'string', 'max:40'],
            'academic_career_id' => ['required', 'integer', Rule::exists('academic_careers', 'id')->where(fn ($query) => $query->where('status', 'ACTIVE')->when($student, fn ($query) => $query->where('type', 'FACULTY')))],
            'password' => [$application ? 'nullable' : 'required', 'string', 'min:8', 'max:128', 'confirmed'],
        ];
        if ($student) {
            $rules += [
                'account_number' => ['required', 'string', 'max:60', Rule::unique('students'), Rule::unique('access_requests')->ignore($application?->id)],
                'has_pending_subjects' => ['required', 'boolean'],
                'subjects' => ['required_if:has_pending_subjects,1', 'array'], 'subjects.*' => ['required', 'integer', 'distinct', 'exists:academic_subjects,id'],
                'documents' => [$application ? 'sometimes' : 'required', 'array:'.implode(',', array_keys(AcademicDocument::ACCESS_TYPES))],
            ];
            foreach (AcademicDocument::ACCESS_TYPES as $key => $label) {
                $mustUpload = !$application || $application->documents()->where('type', $key)->whereHas('currentVersion', fn ($q) => $q->where('status', DocumentVersion::CORRECTION))->exists();
                $rules['documents.'.$key] = [$mustUpload ? 'required' : 'sometimes', 'array', 'min:1', 'max:2'];
                $rules['documents.'.$key.'.*'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'extensions:pdf,jpg,jpeg,png', 'max:8192'];
            }
        }
        $fields = $request->validate($rules);
        $subjects = $student && $request->boolean('has_pending_subjects') ? ($fields['subjects'] ?? []) : [];
        if ($subjects && AcademicSubject::whereIn('id', $subjects)->where('status', 'ACTIVE')->whereHas('faculties', fn ($query) => $query
            ->where('academic_careers.id', $fields['academic_career_id'])->where('status', 'ACTIVE')->where('academic_career_subject.active', true))->count() !== count($subjects)) {
            throw ValidationException::withMessages(['subjects' => 'Selecciona únicamente asignaturas activas del plan de tu facultad.']);
        }
        if ($student) {
            foreach (['DNI_ESTUDIANTE', 'TITULO_COLEGIO'] as $key) {
                $files = $request->file('documents.'.$key, []);
                if (count($files) === 1 && $files[0]->getMimeType() !== 'application/pdf') {
                    throw ValidationException::withMessages(['documents.'.$key => 'Adjunta un PDF con ambos lados o dos imágenes (frente y reverso).']);
                }
            }
        }
        $stored = [];
        $token = $application ? null : Str::random(64);
        try {
            $result = DB::transaction(function () use ($application, $type, $student, $fields, $request, $subjects, $token, &$stored) {
                $locked = $application ? AccessRequest::lockForUpdate()->findOrFail($application->id) : new AccessRequest();
                if ($application) {
                    abort_unless($locked->status === 'CORRECTION_REQUIRED' && $locked->revision === (int) $request->input('revision'), 409, 'La solicitud cambió o no admite correcciones.');
                }
                $before = $locked->status;
                $locked->fill(collect($fields)->only(['first_names', 'last_names', 'dni', 'email', 'phone', 'academic_career_id'])->all());
                $locked->type = $type;
                $locked->account_number = $student ? $fields['account_number'] : null;
                $locked->has_pending_subjects = $student && $request->boolean('has_pending_subjects');
                if (!empty($fields['password'])) $locked->password_hash = $fields['password'];
                if ($token) $locked->tracking_token_hash = hash('sha256', $token);
                $locked->status = 'PENDING';
                if ($application) $locked->revision++;
                $locked->save();
                $locked->subjects()->sync($subjects);
                foreach ($student ? $request->file('documents', []) : [] as $key => $files) {
                    $document = $locked->documents()->firstOrCreate(['type' => $key]);
                    $previous = $document->currentVersion;
                    if ($application && $previous && $previous->status !== DocumentVersion::CORRECTION) {
                        throw ValidationException::withMessages(['documents.'.$key => 'Solo se reemplazan mediante nueva versión los documentos que requieren corrección.']);
                    }
                    $metadata = [];
                    foreach ($files as $file) {
                        $path = $file->store((string) $locked->id, 'access_documents');
                        $stored[] = $path;
                        $metadata[] = ['path' => $path, 'name' => basename(str_replace('\\', '/', $file->getClientOriginalName())), 'mime' => $file->getMimeType(), 'size' => $file->getSize()];
                    }
                    $document->versions()->create(['version' => ($previous?->version ?? 0) + 1, 'uploaded_at' => now(),
                        'status' => DocumentVersion::PENDING, 'files' => $metadata]);
                }
                $locked->events()->create(['action' => $application ? 'Corrección enviada' : 'Solicitud registrada',
                    'before_status' => $before, 'after_status' => 'PENDING']);
                return $locked;
            });
        } catch (\Throwable $error) {
            foreach ($stored as $path) Storage::disk('access_documents')->delete($path); // Solo cargas nuevas de una transacción fallida.
            if ($error instanceof UniqueConstraintViolationException) {
                throw ValidationException::withMessages(['dni' => 'Ya existe una cuenta o solicitud con el DNI, correo o número de cuenta indicado.']);
            }
            if ($error instanceof \League\Flysystem\FilesystemException) {
                throw ValidationException::withMessages(['documents' => 'No se pudieron guardar los archivos. Intenta nuevamente o comunícate con CA.']);
            }
            throw $error;
        }
        return [$result, $token];
    }
}
