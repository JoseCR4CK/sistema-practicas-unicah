<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicLoad extends Model
{
    public const PERIODS = ['I' => 'I Período Académico', 'II' => 'II Período Académico', 'III' => 'III Período Académico'];
    public const DAYS = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

    protected $fillable = ['academic_career_id', 'academic_subject_id', 'academic_period', 'academic_year', 'subject', 'career', 'section', 'weekdays', 'starts_at', 'ends_at', 'valid_from', 'valid_until', 'credits', 'weekly_class_hours', 'status'];

    public function catalogCareer(): BelongsTo
    {
        return $this->belongsTo(AcademicCareer::class, 'academic_career_id');
    }

    public function catalogSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public static function periodKey(?string $period): ?string
    {
        foreach (self::PERIODS as $key => $label) {
            if (in_array(\Illuminate\Support\Str::ascii(trim($period ?? '')), [$key, \Illuminate\Support\Str::ascii($label), $key.' Periodo'], true)) {
                return $key;
            }
        }

        return null;
    }

    public static function periodAliases(string $key): array
    {
        return [$key, self::PERIODS[$key], $key.' Periodo Académico', $key.' Periodo Academico', $key.' Período', $key.' Periodo'];
    }

    public function scheduledWeeklyMinutes(): int
    {
        return \App\Services\AcademicClassTime::scheduledMinutes($this->starts_at, $this->ends_at, $this->weekdays);
    }

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
