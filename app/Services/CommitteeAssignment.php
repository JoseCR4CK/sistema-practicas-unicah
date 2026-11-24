<?php

namespace App\Services;

use App\Models\AcademicCommittee;
use App\Models\MonographReview;
use App\Models\Practice;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommitteeAssignment
{
    public function save(Practice $practice, User $actor, array $fields): AcademicCommittee
    {
        abort_unless($actor->isActive() && in_array($actor->role?->name, ['COORDINACION', 'ROOT'], true), 403);
        $members = collect($fields['members'] ?? []);
        if ($members->isNotEmpty() && ($members->count() !== 3 || $members->pluck('user_id')->map(fn ($id) => (int) $id)->unique()->count() !== 3
            || collect(AcademicCommittee::FUNCTIONS)->diff($members->pluck('function'))->isNotEmpty())) {
            throw ValidationException::withMessages(['members' => 'Asigna exactamente tres docentes distintos: coordinador de terna, revisión técnica y revisión de estilo.']);
        }
        if (($fields['status'] === 'Pendiente de asignación') !== $members->isEmpty()) {
            throw ValidationException::withMessages(['members' => 'Una asignación activa requiere los tres docentes y sus funciones.']);
        }

        return DB::transaction(function () use ($practice, $actor, $fields, $members) {
            $practice = Practice::lockForUpdate()->findOrFail($practice->id);
            $committee = AcademicCommittee::firstOrCreate(['practice_id' => $practice->id], ['assigned_by' => $actor->id]);
            $committee = AcademicCommittee::lockForUpdate()->findOrFail($committee->id);
            abort_unless(! $committee->isClosed() && $committee->revision === (int) $fields['revision'], 409, 'La terna cambió o está cerrada. Recarga el detalle.');
            $teachers = Teacher::whereIn('user_id', $members->pluck('user_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('user_id');
            User::whereIn('id', $members->pluck('user_id'))->orderBy('id')->lockForUpdate()->get();
            foreach ($members as $index => $member) {
                $teacher = $teachers->get((int) $member['user_id']);
                if (! $teacher || ! Teacher::eligibleForCommittee()->whereKey($teacher->id)->exists()
                    || ! $teacher->faculties()->whereKey($member['academic_career_id'])->where('status', 'ACTIVE')->whereIn('type', ['FACULTY', 'GENERAL'])->exists()) {
                    throw ValidationException::withMessages(['members.'.$index.'.user_id' => 'Selecciona un docente activo, aprobado y asociado a la facultad o área elegida.']);
                }
            }
            $oldMembers = $committee->members()->get();
            if ($oldMembers->isNotEmpty() && $members->isEmpty()) {
                throw ValidationException::withMessages(['members' => 'Sustituye los integrantes conservando tres docentes; el cierre requiere su procedimiento formal.']);
            }
            $old = $oldMembers->keyBy('function');
            $incoming = $members->keyBy('function');
            $changed = $oldMembers->count() !== $incoming->count() || $oldMembers->contains(fn ($member) => ! $incoming->has($member->function) || (int) $incoming[$member->function]['user_id'] !== $member->user_id
                || (int) $incoming[$member->function]['academic_career_id'] !== $member->academic_career_id);
            if (! $changed && $committee->status === $fields['status']) {
                return $committee;
            }
            $coordinatorTeacher = $members->isEmpty() ? null : $teachers[(int) $incoming[AcademicCommittee::COORDINATOR]['user_id']];
            $changesFollowup = $coordinatorTeacher && $practice->teacher_id !== null && $practice->teacher_id !== $coordinatorTeacher->id;
            if (($old->isNotEmpty() || $changesFollowup) && trim($fields['reason'] ?? '') === '') {
                throw ValidationException::withMessages(['reason' => 'Indica el motivo de la modificación de integrantes, funciones o docente de seguimiento.']);
            }
            $before = $committee->auditSnapshot();
            $revision = $committee->revision + 1;
            $changes = [];
            $unchanged = $oldMembers->filter(fn ($member) => $incoming->has($member->function)
                && $member->user_id === (int) $incoming[$member->function]['user_id']
                && $member->academic_career_id === (int) $incoming[$member->function]['academic_career_id']);
            foreach ($oldMembers as $previous) {
                if ($unchanged->contains('id', $previous->id)) {
                    continue;
                }
                $previous->update(['ended_at' => now()]);
                if (! $incoming->has($previous->function)) {
                    $changes[] = ['function' => $previous->function, 'previous_user_id' => $previous->user_id, 'new_user_id' => null];
                }
            }
            foreach ($incoming as $function => $next) {
                if ($unchanged->contains('function', $function)) {
                    continue;
                }
                $previous = $old->get($function);
                $committee->members()->create([
                    'user_id' => $next['user_id'], 'teacher_id' => $teachers[(int) $next['user_id']]->id,
                    'academic_career_id' => $next['academic_career_id'], 'function' => $function,
                    'started_at' => now(), 'assignment_revision' => $revision,
                ]);
                $changes[] = ['function' => $function, 'previous_user_id' => $previous?->user_id,
                    'new_user_id' => (int) $next['user_id']];
            }
            if ($coordinatorTeacher) {
                $practice->update(['teacher_id' => $coordinatorTeacher->id]);
            }
            $committee->update(['status' => $fields['status'], 'revision' => $revision,
                'assigned_by' => $actor->id, 'assigned_at' => $committee->assigned_at ?? ($members->isNotEmpty() ? now() : null),
                'confirmed_at' => in_array($fields['status'], ['Confirmada', 'Activa'], true) ? ($committee->confirmed_at ?? now()) : null]);
            $committee->refresh();
            $defense = $committee->defense;
            if ($changed && $defense && $defense->status !== 'Realizada') {
                // Las confirmaciones previas no se trasladan a una composición distinta.
                $defense->update(['status' => $defense->options()->exists() ? 'En coordinación' : 'Pendiente',
                    'selected_option_id' => null, 'confirmed_at' => null, 'confirmed_by' => null,
                    'scheduling_round' => $defense->scheduling_round + 1]);
                $committee->unsetRelation('defense');
            }
            $this->syncOpenReviews($committee, $actor, $fields['reason'] ?? 'Asignación inicial de funciones');
            $committee->events()->create(['actor_id' => $actor->id, 'action' => $old->isEmpty() ? 'Asignación inicial' : 'Modificación de integrantes o funciones',
                'reason' => $fields['reason'] ?? 'Asignación inicial', 'before' => $before,
                'after' => $committee->auditSnapshot() + ['changes' => $changes], 'occurred_at' => now()]);

            return $committee;
        });
    }

    private function syncOpenReviews(AcademicCommittee $committee, User $actor, string $reason): void
    {
        $reviews = MonographReview::whereHas('monograph', fn ($q) => $q->where('practice_id', $committee->practice_id))
            ->whereNull('completed_at')->whereIn('status', ['Pendiente', 'En revisión'])->get();
        $before = [];
        $after = [];
        foreach ($reviews as $review) {
            $responsible = $committee->reviewerId($review->stage);
            if ($review->reviewer_id === $responsible) {
                continue;
            }
            $before[] = $review->only(['id', 'stage', 'reviewer_id', 'started_at', 'due_at']);
            $review->update(['reviewer_id' => $responsible]);
            $after[] = $review->only(['id', 'stage', 'reviewer_id', 'started_at', 'due_at']);
        }
        if ($before) {
            $committee->events()->create(['actor_id' => $actor->id, 'action' => 'Responsables de monografía actualizados',
                'reason' => $reason, 'before' => $before, 'after' => $after, 'occurred_at' => now()]);
        }
    }
}
