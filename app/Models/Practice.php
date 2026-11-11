<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Practice extends Model
{
    use HasFactory;

    public const ACTIVE = 'ACTIVE';

    public function applications(): HasMany
    {
        return $this->hasMany(PracticeRequest::class);
    }

    public const STAGES = [
        'Solicitud',
        'Documentación',
        'Empresa',
        'Práctica',
        'Visita',
        'Monografía',
        'Defensa',
        'Finalización',
    ];

    public const FIRST_VISIT_DAY = 45;

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::ACTIVE => 'Activa',
            'COMPLETED' => 'Finalizada',
            default => $this->status,
        };
    }

    public function currentDay(): ?int
    {
        $elapsed = $this->start_date?->diffInDays(today(), false);

        return $elapsed !== null && $elapsed >= 0 ? (int) $elapsed + 1 : null;
    }

    public function reachedVisitDay(): bool
    {
        return $this->status === self::ACTIVE && $this->currentDay() >= self::FIRST_VISIT_DAY;
    }

    public function needsTeacher(): bool
    {
        return $this->status === self::ACTIVE && $this->currentDay() !== null && $this->teacher_id === null;
    }

    protected $fillable = [
        'student_id',
        'company_id',
        'company_responsible_id',
        'teacher_id',
        'academic_period',
        'academic_year',
        'stage',
        'status',
        'progress',
        'proposed_start_date',
        'start_date',
        'end_date',
        'required_hours',
        'completed_hours',
    ];

    protected function casts(): array
    {
        return [
            'academic_year' => 'integer',
            'progress' => 'integer',
            'required_hours' => 'integer',
            'completed_hours' => 'integer',
            'proposed_start_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function companyResponsible(): BelongsTo
    {
        return $this->belongsTo(
            CompanyResponsible::class,
            'company_responsible_id'
        );
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
