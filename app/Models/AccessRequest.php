<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    public const STATES = ['PENDING' => 'Pendiente', 'IN_REVIEW' => 'En revisión', 'CORRECTION_REQUIRED' => 'Requiere corrección', 'APPROVED' => 'Aprobada', 'REJECTED' => 'Rechazada'];
    protected $guarded = ['id'];
    protected $hidden = ['password_hash', 'tracking_token_hash'];

    protected function casts(): array
    {
        return ['password_hash' => 'hashed', 'induction_verified' => 'boolean', 'has_pending_subjects' => 'boolean',
            'reviewed_at' => 'datetime', 'induction_verified_at' => 'datetime', 'revision' => 'integer'];
    }

    public function career() { return $this->belongsTo(AcademicCareer::class, 'academic_career_id'); }
    public function subjects() { return $this->belongsToMany(AcademicSubject::class, 'access_request_subject'); }
    public function documents() { return $this->hasMany(AcademicDocument::class); }
    public function events() { return $this->hasMany(AccessRequestEvent::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function getFullNameAttribute(): string { return $this->first_names.' '.$this->last_names; }
    public function statusLabel(): string { return self::STATES[$this->status]; }
    public function isStudent(): bool { return $this->type === 'STUDENT'; }
}
