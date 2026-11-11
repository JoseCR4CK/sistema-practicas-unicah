<?php

namespace App\Policies;

use App\Models\Defense;
use App\Models\User;

class DefensePolicy
{
    public function view(User $user, Defense $defense): bool
    {
        return app(AcademicCommitteePolicy::class)->view($user, $defense->committee);
    }

    public function update(User $user, Defense $defense): bool
    {
        return in_array($user->role?->name, ['COORDINACION', 'ROOT'], true);
    }

    public function respond(User $user, Defense $defense): bool
    {
        if ($user->hasRole('ESTUDIANTE')) {
            return $defense->committee->practice->student->user_id === $user->id;
        }

        return $defense->committee->members()->where('user_id', $user->id)->exists();
    }
}
