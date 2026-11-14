<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicCareer extends Model
{
    protected $fillable = ['name', 'code', 'status', 'source'];

    public function subjects(): HasMany
    {
        return $this->hasMany(AcademicSubject::class);
    }
}
