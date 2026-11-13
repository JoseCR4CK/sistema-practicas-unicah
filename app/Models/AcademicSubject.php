<?php

namespace App\Models;

use App\Services\AcademicClassTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicSubject extends Model
{
    protected $fillable = ['academic_career_id', 'name', 'code', 'credits', 'weekly_class_hours', 'status', 'source'];

    protected function casts(): array
    {
        return ['credits' => 'decimal:2', 'weekly_class_hours' => 'decimal:2'];
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(AcademicCareer::class, 'academic_career_id');
    }

    public function confirmedWeeklyHours(): ?int
    {
        $hours = AcademicClassTime::hoursForCredits($this->credits);
        if ($hours === null || ($this->weekly_class_hours !== null && (float) $this->weekly_class_hours !== (float) $hours)) {
            return null;
        }

        return $hours;
    }

    public function selectable(): bool
    {
        return $this->status === 'ACTIVE' && $this->career?->status === 'ACTIVE' && $this->confirmedWeeklyHours() !== null;
    }
}
