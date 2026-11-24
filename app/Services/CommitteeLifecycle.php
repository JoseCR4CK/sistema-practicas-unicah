<?php

namespace App\Services;

use App\Models\AcademicCommittee;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommitteeLifecycle
{
    public function act(AcademicCommittee $committee, User $actor, array $fields): void
    {
        abort_unless($actor->isActive() && in_array($actor->role?->name, ['COORDINACION', 'ROOT'], true), 403);
        DB::transaction(function () use ($committee, $actor, $fields) {
            Practice::whereKey($committee->practice_id)->lockForUpdate()->firstOrFail();
            $locked = AcademicCommittee::lockForUpdate()->findOrFail($committee->id);
            abort_unless(! $locked->isClosed() && $locked->revision === (int) $fields['revision'], 409, 'La terna cambió o está cerrada.');
            $defense = $locked->defense()->lockForUpdate()->first();
            abort_unless($defense && $defense->status === 'Realizada' && $defense->performed_at, 409, 'Primero debe registrarse la realización de la defensa.');
            abort_unless($locked->isFullyAssigned(), 409, 'Se requieren tres docentes con sus funciones precisadas.');
            abort_unless($defense->scheduling_round === (int) $fields['round'], 409, 'La ronda de defensa cambió. Recarga la página.');
            $before = $locked->auditSnapshot();
            if ($fields['action'] === 'result') {
                abort_unless($defense->result === null, 409, 'El resultado de esta ronda ya está registrado.');
                $defense->update(['result' => $fields['result'], 'result_recorded_by' => $actor->id, 'result_recorded_at' => now(), 'result_note' => $fields['reason']]);
                $action = 'Resultado de defensa: '.$fields['result'];
            } elseif ($fields['action'] === 'retry') {
                abort_unless($defense->result === 'Reprobada', 409, 'Esta acción solo corresponde a una defensa reprobada del mismo proceso.');
                $defense->update(['status' => 'En coordinación', 'selected_option_id' => null, 'confirmed_by' => null, 'confirmed_at' => null,
                    'performed_at' => null, 'observation' => null, 'result' => null, 'result_recorded_by' => null, 'result_recorded_at' => null, 'result_note' => null,
                    'scheduling_round' => $defense->scheduling_round + 1]);
                $action = 'Nueva ronda con la misma terna';
            } else {
                abort_unless($defense->result === 'Aprobada' && ! empty($fields['final_records_verified']), 422, 'Se requiere defensa aprobada y verificación de evaluación/documentación final por Coordinación Académica.');
                $locked->update(['status' => 'Cerrada', 'closed_at' => now(), 'closed_by' => $actor->id,
                    'closure_reason' => $fields['reason'], 'final_records_verified' => true]);
                $action = 'Cierre formal de terna';
            }
            $locked->unsetRelation('defense');
            $locked->events()->create(['actor_id' => $actor->id, 'action' => $action, 'reason' => $fields['reason'],
                'before' => $before, 'after' => $locked->auditSnapshot(), 'occurred_at' => now()]);
        });
    }
}
