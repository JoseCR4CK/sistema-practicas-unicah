<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefenseAvailability extends Model
{
    protected $fillable = ['user_id', 'available', 'observation'];

    protected function casts(): array
    {
        return ['available' => 'boolean'];
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(DefenseOption::class, 'defense_option_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
