<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AcademicDocument extends Model
{
    public const TYPES = [
        'DNI_ESTUDIANTE' => 'DNI del estudiante',
        'DNI_TESTIGO_1' => 'DNI del testigo 1',
        'DNI_TESTIGO_2' => 'DNI del testigo 2',
        'SOLVENCIA_PAGO' => 'Solvencia de pago',
        'SOLICITUD_EMPRESA' => 'Solicitud de empresa (opcional)',
    ];

    protected $fillable = ['practice_id', 'type'];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(DocumentVersion::class)->latestOfMany();
    }
}
