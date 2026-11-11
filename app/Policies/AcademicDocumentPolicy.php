<?php

namespace App\Policies;

use App\Models\AcademicDocument;
use App\Models\User;

class AcademicDocumentPolicy
{
    public function view(User $user, AcademicDocument $document): bool
    {
        return $this->review($user, $document)
            || ($user->hasRole('ESTUDIANTE') && $document->practice->student->user_id === $user->id);
    }

    public function review(User $user, AcademicDocument $document): bool
    {
        return in_array($user->role?->name, ['COORDINACION', 'ROOT'], true);
    }
}
