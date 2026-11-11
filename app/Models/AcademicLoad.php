<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicLoad extends Model
{
    public const DAYS = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

    protected $fillable = ['academic_period', 'academic_year', 'subject', 'career', 'section', 'weekdays', 'starts_at', 'ends_at', 'valid_from', 'valid_until', 'credits', 'weekly_class_hours', 'status'];

    protected function casts(): array
    {
        return ['weekdays' => 'array', 'valid_from' => 'date', 'valid_until' => 'date', 'credits' => 'decimal:2', 'weekly_class_hours' => 'decimal:2', 'revision' => 'integer', 'academic_year' => 'integer'];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(AcademicLoadChange::class);
    }
}
