<?php

namespace App\Models;

use App\Services\AcademicClassTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicSubject extends Model
{
    protected $fillable = ['academic_career_id', 'name', 'code', 'credits', 'weekly_class_hours', 'status', 'source', 'catalog_key', 'subject_type', 'weekly_minutes', 'review_required', 'catalog_notes'];

    protected static function booted(): void
    {
        // Compatibilidad con cargas/fixtures previas que todavía especifican una carrera principal.
        static::created(fn (self $subject) => $subject->faculties()->syncWithoutDetaching([$subject->academic_career_id]));
    }

    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(AcademicCareer::class, 'academic_career_subject')->withPivot(['active', 'academic_period', 'plan_metadata'])->withTimestamps();
    }

    protected function casts(): array
    {
        return ['credits' => 'decimal:2', 'weekly_class_hours' => 'decimal:2', 'weekly_minutes' => 'integer', 'review_required' => 'boolean'];
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(AcademicCareer::class, 'academic_career_id');
    }

    public function confirmedWeeklyHours(): ?int
    {
        if ($this->review_required) return null;
        $hours = AcademicClassTime::hoursForCredits($this->credits, $this->subject_type);
        if ($hours === null || ($this->weekly_class_hours !== null && (float) $this->weekly_class_hours !== (float) $hours)) {
            return null;
        }

        return $hours;
    }

    public function selectable(): bool
    {
        return $this->status === 'ACTIVE' && $this->faculties()->where('status', 'ACTIVE')->wherePivot('active', true)->exists() && $this->confirmedWeeklyHours() !== null;
    }

    public function availableFor(int $careerId): bool
    {
        return $this->status === 'ACTIVE' && $this->faculties()->whereKey($careerId)
            ->where('status', 'ACTIVE')->wherePivot('active', true)->exists();
    }
}
