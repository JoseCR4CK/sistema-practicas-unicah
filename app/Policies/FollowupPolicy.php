<?php

namespace App\Policies;

use App\Models\Followup;
use App\Models\User;

class FollowupPolicy
{
    public function view(User $user, Followup $followup): bool
    {
        return in_array($user->role?->name, ['COORDINACION', 'ROOT'], true)
            || ($user->hasRole('DOCENTE_SEGUIMIENTO') && $followup->practice->teacher?->user_id === $user->id);
    }

    public function update(User $user, Followup $followup): bool
    {
        return $user->hasRole('DOCENTE_SEGUIMIENTO')
            && $followup->practice->teacher?->user_id === $user->id
            && $followup->teacher->user_id === $user->id;
    }
}
