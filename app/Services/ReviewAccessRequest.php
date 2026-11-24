<?php

namespace App\Services;

use App\Models\AccessRequest;
use App\Models\AcademicCareer;
use App\Models\AcademicDocument;
use App\Models\DocumentVersion;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewAccessRequest
{
    public function execute(AccessRequest $application, User $actor, array $fields): void
    {
        abort_unless($actor->isActive() && in_array($actor->role?->name, ['COORDINACION', 'ROOT'], true), 403);
        try {
            DB::transaction(function () use ($application, $actor, $fields) {
                $locked = AccessRequest::lockForUpdate()->findOrFail($application->id);
                abort_unless($locked->revision === (int) $fields['revision'], 409, 'La solicitud cambió. Recarga el detalle.');
                $before = $locked->status;
                $action = $fields['action'];
                if ($action === 'start') {
                    abort_unless($before === 'PENDING', 409, 'Solo se inicia una solicitud pendiente.');
                    $locked->status = 'IN_REVIEW';
                } else {
                    abort_unless($before === 'IN_REVIEW', 409, 'Inicia la revisión antes de gestionar la solicitud.');
                    if ($action === 'induction') {
                        abort_unless($locked->isStudent(), 422);
                        $locked->induction_verified = (bool) $fields['induction_verified'];
                        $locked->induction_verified_at = $locked->induction_verified ? now() : null;
                        $locked->induction_verified_by = $locked->induction_verified ? $actor->id : null;
                    } elseif ($action === 'document') {
                        $document = $locked->documents()->findOrFail($fields['document_id']);
                        $version = $document->versions()->latest('version')->firstOrFail();
                        abort_unless($version->id === (int) $fields['version_id'] && $version->status !== $fields['result'], 409, 'La versión cambió o ya tiene ese resultado.');
                        $version->update(['status' => $fields['result'], 'observation' => $fields['note'] ?? null, 'reviewed_by' => $actor->id, 'reviewed_at' => now()]);
                    } elseif ($action === 'correct') {
                        $locked->status = 'CORRECTION_REQUIRED';
                        $locked->correction_notes = $fields['note'];
                    } elseif ($action === 'reject') {
                        $locked->status = 'REJECTED';
                        $locked->rejection_reason = $fields['note'];
                    } elseif ($action === 'approve') {
                        $this->approve($locked);
                    }
                }
                $locked->reviewer_id = $actor->id;
                $locked->reviewed_at = now();
                $locked->revision++;
                $locked->save();
                $locked->events()->create(['actor_id' => $actor->id, 'action' => match ($action) {
                    'start' => 'Revisión iniciada', 'induction' => 'Verificación de lista física de inducción',
                    'document' => 'Documento #'.$fields['document_id'].': '.$fields['result'], 'correct' => 'Corrección solicitada',
                    'reject' => 'Solicitud rechazada', 'approve' => 'Acceso aprobado',
                }, 'before_status' => $before, 'after_status' => $locked->status, 'note' => $fields['note'] ?? null]);
            });
        } catch (UniqueConstraintViolationException $error) {
            throw ValidationException::withMessages(['approval' => 'El DNI o correo electrónico ya está asociado a un registro existente. No se creó un usuario parcial.']);
        }
    }

    private function approve(AccessRequest $application): void
    {
        $career = AcademicCareer::where('status', 'ACTIVE')->findOrFail($application->academic_career_id);
        if ($application->isStudent()) {
            if (!$application->induction_verified || $career->type !== 'FACULTY') {
                throw ValidationException::withMessages(['approval' => 'Coordinación Académica debe verificar la asistencia en la lista física de inducción y una facultad válida antes de aprobar.']);
            }
            foreach (AcademicDocument::ACCESS_TYPES as $key => $label) {
                $current = $application->documents()->where('type', $key)->first()?->currentVersion;
                if (!$current || $current->status !== DocumentVersion::APPROVED || !$current->files) {
                    throw ValidationException::withMessages(['approval' => 'Revisa y aprueba todos los documentos obligatorios antes de aprobar el acceso.']);
                }
            }
            if ($application->has_pending_subjects && !$application->subjects()->exists()) {
                throw ValidationException::withMessages(['approval' => 'La declaración de asignaturas está incompleta.']);
            }
            foreach ($application->subjects as $subject) {
                if (!$subject->availableFor($career->id)) throw ValidationException::withMessages(['approval' => 'El catálogo cambió; solicita corregir las asignaturas declaradas.']);
            }
        }
        if ($application->isStudent()) $application->account_number = $application->dni;
        if (User::where('dni', $application->dni)->orWhere('email', $application->email)->exists()
            || ($application->isStudent() && Student::where('account_number', $application->account_number)->exists())) {
            throw ValidationException::withMessages(['approval' => 'Ya existe un registro asociado al DNI o correo electrónico indicado. No se modificaron datos existentes.']);
        }
        $user = User::create(['name' => $application->full_name, 'dni' => $application->dni, 'email' => $application->email,
            'password' => $application->password_hash, 'status' => 'ACTIVE',
            'role_id' => Role::where('name', $application->isStudent() ? 'ESTUDIANTE' : 'DOCENTE_SEGUIMIENTO')->firstOrFail()->id]);
        if ($application->isStudent()) {
            $student = Student::create(['user_id' => $user->id, 'academic_career_id' => $career->id, 'career' => $career->name,
                'account_number' => $application->account_number, 'phone' => $application->phone]);
            foreach ($application->subjects as $subject) $student->pendingSubjects()->attach($subject->id, ['access_request_id' => $application->id]);
            $application->documents()->update(['student_id' => $student->id]);
        } else {
            $teacher = Teacher::create(['user_id' => $user->id, 'faculty' => $career->name, 'phone' => $application->phone, 'access_approved_at' => now()]);
            $teacher->faculties()->attach($career->id, ['is_primary' => true]);
        }
        $application->user_id = $user->id;
        $application->status = 'APPROVED';
    }
}
