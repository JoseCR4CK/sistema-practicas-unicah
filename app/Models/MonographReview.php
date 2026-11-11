<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonographReview extends Model
{
    protected $fillable = ['monograph_version_id', 'stage', 'attempt', 'status', 'reviewer_id', 'recorded_by', 'started_at', 'due_at', 'completed_at', 'observation'];

    protected function casts(): array
    {
        return ['attempt' => 'integer', 'started_at' => 'datetime', 'due_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function monograph(): BelongsTo
    {
        return $this->belongsTo(Monograph::class);
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(MonographVersion::class, 'monograph_version_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
