<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AcademicCommittee extends Model
{
    public const STATUSES = ['Pendiente de asignación', 'Asignada', 'Confirmada', 'Activa', 'Cerrada'];

    public const COORDINATOR = 'Coordinador de terna';

    public const TECHNICAL = 'Revisión técnica';

    public const STYLE = 'Revisión de estilo';

    public const FUNCTIONS = [self::COORDINATOR, self::TECHNICAL, self::STYLE];

    protected $fillable = ['practice_id', 'status', 'revision', 'assigned_by', 'assigned_at', 'confirmed_at', 'origin_period', 'origin_year', 'closed_at', 'closed_by', 'closure_reason', 'final_records_verified'];

    protected static function booted(): void
    {
        static::creating(function (self $committee) {
            $practice = Practice::findOrFail($committee->practice_id);
            $committee->origin_period ??= $practice->academic_period;
            $committee->origin_year ??= $practice->academic_year;
        });
        static::created(fn (self $committee) => $committee->events()->create([
            'actor_id' => $committee->assigned_by, 'action' => 'Creación de terna',
            'reason' => 'Registro de la terna del proceso; no supone autorización automática de defensa.',
            'after' => $committee->only(['practice_id', 'origin_period', 'origin_year', 'status']), 'occurred_at' => now(),
        ]));
    }

    protected function casts(): array
    {
        return ['revision' => 'integer', 'assigned_at' => 'datetime', 'confirmed_at' => 'datetime', 'origin_year' => 'integer', 'closed_at' => 'datetime', 'final_records_verified' => 'boolean'];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMember::class)->whereNull('ended_at');
    }

    public function membershipHistory(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CommitteeEvent::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('closed_at')->where('status', '!=', 'Cerrada')->whereHas('members');
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null || $this->status === 'Cerrada';
    }

    public function isFullyAssigned(): bool
    {
        return $this->members->count() === 3 && collect(self::FUNCTIONS)->diff($this->members->pluck('function'))->isEmpty();
    }

    public function reviewerId(string $stage): ?int
    {
        return $this->members->firstWhere('function', match ($stage) {
            'tecnica' => self::TECHNICAL, 'estilo' => self::STYLE, 'coordinador' => self::COORDINATOR,
        })?->user_id;
    }

    public function originLabel(): string
    {
        return ($this->origin_period ?? 'Período pendiente').' '.($this->origin_year ?? 'Año pendiente');
    }

    public function auditSnapshot(): array
    {
        return [
            'committee' => $this->only(['id', 'practice_id', 'status', 'revision', 'origin_period', 'origin_year', 'closed_at', 'closed_by']),
            'practice_teacher_id' => $this->practice()->value('teacher_id'),
            'members' => $this->members()->with(['user', 'faculty'])->get()->map(fn ($member) => [
                'user_id' => $member->user_id, 'teacher_id' => $member->teacher_id, 'function' => $member->function,
                'academic_career_id' => $member->academic_career_id, 'name' => $member->user->name, 'faculty' => $member->faculty?->name,
            ])->all(),
            'defense' => $this->defense?->only(['id', 'status', 'scheduling_round', 'selected_option_id', 'confirmed_by', 'confirmed_at', 'performed_at', 'observation', 'result', 'result_recorded_by', 'result_recorded_at', 'result_note']),
        ];
    }

    public function coordinator()
    {
        return ($this->members->firstWhere('function', self::COORDINATOR) ?? $this->members->firstWhere('function', 'Coordinador'))?->user;
    }

    public function defense(): HasOne
    {
        return $this->hasOne(Defense::class);
    }
}
