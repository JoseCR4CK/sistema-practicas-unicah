<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'faculty',
        'phone',
        'access_approved_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function faculties()
    {
        return $this->belongsToMany(AcademicCareer::class)->withPivot('is_primary')->withTimestamps();
    }

    public function scopeApprovedActive($query)
    {
        return $query->whereNotNull('access_approved_at')->whereHas('user', fn ($user) => $user->where('status', 'ACTIVE')->whereHas('role', fn ($role) => $role->where('name', 'DOCENTE_SEGUIMIENTO')));
    }

    public function scopeEligibleForCommittee($query)
    {
        return $query->approvedActive()->whereHas('faculties', fn ($faculty) => $faculty->where('status', 'ACTIVE')->whereIn('type', ['FACULTY', 'GENERAL']));
    }

    public function committeeMemberships(): HasMany { return $this->hasMany(CommitteeMember::class)->whereNull('ended_at'); }
    public function committeeMembershipHistory(): HasMany { return $this->hasMany(CommitteeMember::class); }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }

    public function academicLoads(): HasMany
    {
        return $this->hasMany(AcademicLoad::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class);
    }
}
