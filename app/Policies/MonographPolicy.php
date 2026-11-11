<?php

namespace App\Policies;

use App\Models\Monograph;
use App\Models\User;

class MonographPolicy
{
    public function view(User $user, Monograph $monograph): bool
    {
        return in_array($user->role?->name, ['COORDINACION', 'ROOT'], true)
            || ($user->hasRole('ESTUDIANTE') && $monograph->practice->student->user_id === $user->id)
            || ($user->hasRole('DOCENTE_SEGUIMIENTO') && $monograph->practice->teacher?->user_id === $user->id);
    }
}
