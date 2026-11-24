<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicCareer extends Model
{
    protected $fillable = ['name', 'code', 'slug', 'type', 'status', 'source'];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubject::class, 'academic_career_subject')->withPivot(['active', 'academic_period', 'plan_metadata'])->withTimestamps()->wherePivot('active', true);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class)->withPivot('is_primary')->withTimestamps();
    }

    public function scopeStudentOptions($query)
    {
        return $query->where('status', 'ACTIVE')->where('type', 'FACULTY');
    }
}
