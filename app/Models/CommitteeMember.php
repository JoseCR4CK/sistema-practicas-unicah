<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    protected $fillable = ['user_id', 'function'];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(AcademicCommittee::class, 'academic_committee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
