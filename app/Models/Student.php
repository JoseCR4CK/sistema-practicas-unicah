<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'career',
        'phone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(PracticeRequest::class);
    }

    public function latestPractice(): HasOne
    {
        return $this->hasOne(Practice::class)->latestOfMany();
    }

    public function preferredPractice(): ?Practice
    {
        return $this->latestActivePractice ?? $this->latestPractice;
    }

    public function latestActivePractice(): HasOne
    {
        return $this->hasOne(Practice::class)->ofMany(
            ['id' => 'max'],
            fn (Builder $query) => $query->where('status', Practice::ACTIVE),
        );
    }
}
