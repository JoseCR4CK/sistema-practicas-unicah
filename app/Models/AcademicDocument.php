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

    public const ACCESS_TYPES = [
        'DATOS_LUGAR_PRACTICA' => 'Datos Generales del lugar de práctica',
        'CARTA_ACEPTACION' => 'Carta de Aceptación de Práctica',
        'DNI_ESTUDIANTE' => 'Identidad del estudiante, ambos lados',
        'DNI_TESTIGO_1' => 'Identidad Testigo #1',
        'DNI_TESTIGO_2' => 'Identidad Testigo #2',
        'TITULO_COLEGIO' => 'Copia del Título de Colegio, ambos lados',
        'SOLVENCIA_PAGO' => 'Solvencia de pago',
    ];

    protected $fillable = ['practice_id', 'type', 'access_request_id', 'student_id'];

    public function accessRequest(): BelongsTo { return $this->belongsTo(AccessRequest::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }

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
