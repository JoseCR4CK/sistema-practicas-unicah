<?php

namespace App\Policies;

use App\Models\AcademicLoad;
use App\Models\User;

class AcademicLoadPolicy
{
    public function update(User $user, AcademicLoad $load): bool
    {
        return $user->isActive() && (in_array($user->role?->name, ['COORDINACION', 'ROOT'], true)
            || ($user->hasRole('DOCENTE_SEGUIMIENTO') && $load->teacher->user_id === $user->id));
    }
}
