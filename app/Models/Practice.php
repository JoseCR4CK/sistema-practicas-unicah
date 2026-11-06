<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Practice extends Model
{
    use HasFactory;

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