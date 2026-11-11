<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Monograph extends Model
{
    public const REVIEW_DAYS = ['tecnica' => 10, 'estilo' => 3, 'coordinador' => 1];
    public const REVIEW_LABELS = ['tecnica' => 'Revisión técnica', 'estilo' => 'Revisión de estilo', 'coordinador' => 'Revisión del coordinador'];

    protected $fillable = ['practice_id'];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MonographVersion::class);
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(MonographVersion::class)->latestOfMany();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MonographReview::class);
    }

    public function currentReviews()
    {
        return $this->reviews->sortBy('id')->keyBy('stage')
            ->sortBy(fn ($review) => array_search($review->stage, array_keys(self::REVIEW_DAYS), true));
    }

    public function statusLabel(): string
    {
        $reviews = $this->currentReviews();
        if ($reviews->contains('status', 'Requiere corrección')) {
            return 'Requiere corrección';
        }
        if ($reviews->count() === 3 && $reviews->every(fn ($review) => $review->status === 'Aprobada')) {
            return 'Aprobada';
        }

        return $reviews->contains('status', 'En revisión') ? 'En revisión' : 'Pendiente';
    }
}
