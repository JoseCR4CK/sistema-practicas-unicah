<?php

namespace App\Policies;

use App\Models\AcademicCommittee;
use App\Models\User;

class AcademicCommitteePolicy
{
    public function view(User $user, AcademicCommittee $committee): bool
    {
        if ($user->hasRole('ESTUDIANTE')) {
            return $committee->practice->student->user_id === $user->id;
        }

        return $this->update($user, $committee) || $committee->members()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, AcademicCommittee $committee): bool
    {
        return in_array($user->role?->name, ['COORDINACION', 'ROOT'], true);
    }
}
