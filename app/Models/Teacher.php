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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
