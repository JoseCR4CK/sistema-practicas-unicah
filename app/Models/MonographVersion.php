<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonographVersion extends Model
{
    protected $fillable = ['number', 'title', 'status', 'observation', 'submitted_by', 'submitted_at', 'submission_key'];

    protected function casts(): array
    {
        return ['number' => 'integer', 'submitted_at' => 'datetime'];
    }

    public function monograph(): BelongsTo
    {
        return $this->belongsTo(Monograph::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
