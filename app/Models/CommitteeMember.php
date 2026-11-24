<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    protected $fillable = ['user_id', 'function', 'teacher_id', 'academic_career_id', 'started_at', 'ended_at', 'assignment_revision'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime', 'assignment_revision' => 'integer'];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(AcademicCareer::class, 'academic_career_id');
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(AcademicCommittee::class, 'academic_committee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
