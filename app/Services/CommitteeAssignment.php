<?php

namespace App\Services;

use App\Models\AcademicCommittee;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class CommitteeAssignment
{
    public function save(Practice $practice, User $actor, array $fields): AcademicCommittee
    {
        $members = collect($fields['members'] ?? []);
        if ($fields['status'] === 'Confirmada' && $members->count() !== 3) {
            throw ValidationException::withMessages(['members' => 'Una terna confirmada debe tener exactamente tres integrantes.']);
        }
        if ($members->where('function', 'Coordinador')->count() > 1) {
            throw ValidationException::withMessages(['members' => 'La terna permite como máximo un coordinador.']);
        }

        return DB::transaction(function () use ($practice, $actor, $fields, $members) {
            Practice::whereKey($practice->id)->lockForUpdate()->firstOrFail();
            $committee = AcademicCommittee::firstOrCreate(['practice_id' => $practice->id]);
            $committee = AcademicCommittee::whereKey($committee->id)->lockForUpdate()->firstOrFail();
            abort_unless($committee->revision === (int) $fields['revision'], 409, 'La asignación cambió. Actualiza la página.');
            if (Schema::hasTable('defenses')) {
                abort_if(DB::table('defenses')->where('academic_committee_id', $committee->id)->whereIn('status', ['Programada', 'Realizada'])->exists(),
                    409, 'La terna tiene una defensa programada. Su modificación requiere definir el procedimiento de reprogramación.');
            }
            $userIds = $members->pluck('user_id')->map(fn ($id) => (int) $id);
            foreach ($committee->members as $existing) {
                if (!$userIds->contains($existing->user_id)) {
                    $existing->delete();
                }
            }
            foreach ($members as $member) {
                $committee->members()->updateOrCreate(['user_id' => $member['user_id']], ['function' => $member['function']]);
            }
            $committee->update([
                'status' => $fields['status'],
                'revision' => $committee->revision + 1,
                'assigned_by' => $actor->id,
                'assigned_at' => $members->isNotEmpty() ? now() : null,
                'confirmed_at' => $fields['status'] === 'Confirmada' ? now() : null,
            ]);

            return $committee;
        });
    }
}
