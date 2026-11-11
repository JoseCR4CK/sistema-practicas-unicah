<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicLoadChange extends Model
{
    protected $fillable = ['user_id', 'reason', 'before', 'after'];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
