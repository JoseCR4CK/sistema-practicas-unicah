<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    public const PENDING = 'Pendiente de revisión';
    public const APPROVED = 'Aprobado';
    public const CORRECTION = 'Requiere corrección';

    protected $fillable = ['version', 'status', 'uploaded_by', 'uploaded_at', 'submission_note', 'observation', 'reviewed_by', 'reviewed_at'];

    protected function casts(): array
    {
        return ['version' => 'integer', 'uploaded_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(AcademicDocument::class, 'academic_document_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
