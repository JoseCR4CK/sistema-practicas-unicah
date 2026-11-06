<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'official_name',
        'commercial_name',
        'rtn',
        'sector',
        'address',
        'city',
        'department',
        'phone',
        'email',
        'status',
    ];

    public function responsibles(): HasMany
    {
        return $this->hasMany(CompanyResponsible::class);
    }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }
}