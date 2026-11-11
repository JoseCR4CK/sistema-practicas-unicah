<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefenseOption extends Model
{
    protected $fillable = ['starts_at', 'ends_at', 'duration_minutes', 'committee_revision', 'proposed_by'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'duration_minutes' => 'integer', 'committee_revision' => 'integer'];
    }

    public function defense(): BelongsTo
    {
        return $this->belongsTo(Defense::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(DefenseAvailability::class);
    }
}
