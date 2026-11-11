<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicCommittee extends Model
{
    public const STATUSES = ['Pendiente de asignación', 'Asignada', 'Confirmada'];

    protected $fillable = ['practice_id', 'status', 'revision', 'assigned_by', 'assigned_at', 'confirmed_at'];

    protected function casts(): array
    {
        return ['revision' => 'integer', 'assigned_at' => 'datetime', 'confirmed_at' => 'datetime'];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function coordinator()
    {
        return $this->members->firstWhere('function', 'Coordinador')?->user;
    }
}
