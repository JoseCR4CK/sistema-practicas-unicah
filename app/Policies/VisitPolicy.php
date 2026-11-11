<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;

class VisitPolicy
{
    public function view(User $user, Visit $visit): bool
    {
        return in_array($user->role?->name, ['COORDINACION', 'ROOT'], true)
            || $this->update($user, $visit)
            || ($user->hasRole('ESTUDIANTE') && $visit->practice->student->user_id === $user->id);
    }

    public function update(User $user, Visit $visit): bool
    {
        return $user->hasRole('DOCENTE_SEGUIMIENTO') && $visit->practice->teacher?->user_id === $user->id;
    }
}
