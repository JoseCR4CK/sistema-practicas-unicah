<?php

namespace App\Policies;

use App\Models\Monograph;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class MonographPolicy
{
    public function view(User $user, Monograph $monograph): bool
    {
        if ($user->hasRole('ESTUDIANTE')) {
            return $monograph->practice->student->user_id === $user->id;
        }

        return in_array($user->role?->name, ['COORDINACION', 'ROOT'], true)
            || ($user->hasRole('DOCENTE_SEGUIMIENTO') && $monograph->practice->teacher?->user_id === $user->id)
            || (Schema::hasTable('academic_committees') && $monograph->practice->committee?->members()->where('user_id', $user->id)->exists());
    }
}
