<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Defense extends Model
{
    public const DURATIONS = [60, 90, 120];
    public const LAST_ACADEMIC_TIME = '21:50';

    protected $fillable = ['academic_committee_id', 'duration_minutes', 'status', 'selected_option_id', 'confirmed_by', 'confirmed_at', 'performed_at', 'observation'];

    protected function casts(): array
    {
        return ['duration_minutes' => 'integer', 'confirmed_at' => 'datetime', 'performed_at' => 'datetime'];
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(AcademicCommittee::class, 'academic_committee_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(DefenseOption::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(DefenseOption::class, 'selected_option_id');
    }

    public function participants()
    {
        return $this->committee->members->pluck('user')
            ->push($this->committee->practice->student->user)->unique('id')->values();
    }
}
