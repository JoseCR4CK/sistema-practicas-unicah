<?php

namespace App\Services;

use App\Models\AcademicCommittee;
use App\Models\AcademicLoad;
use App\Models\Defense;
use App\Models\DefenseClassBlock;
use App\Models\DefenseOption;
use App\Models\Practice;
use App\Models\Teacher;
use App\Models\User;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class DefenseScheduling
{
    public function create(AcademicCommittee $committee, int $duration): Defense
    {
        return DB::transaction(function () use ($committee, $duration) {
            Practice::whereKey($committee->practice_id)->lockForUpdate()->firstOrFail();
            AcademicCommittee::whereKey($committee->id)->lockForUpdate()->firstOrFail();

            return Defense::firstOrCreate(['academic_committee_id' => $committee->id], ['duration_minutes' => $duration]);
        });
    }

    private function locked(Defense $defense, Closure $action)
    {
        return DB::transaction(function () use ($defense, $action) {
            $practice = Practice::whereKey($defense->committee->practice_id)->lockForUpdate()->firstOrFail();
            $committee = AcademicCommittee::whereKey($defense->academic_committee_id)->lockForUpdate()->firstOrFail();
            $committee->setRelation('practice', $practice);
            $committee->load('members.user');
            $locked = Defense::whereKey($defense->id)->lockForUpdate()->firstOrFail();
            $locked->setRelation('committee', $committee);
            // Todos los escritores de horarios toman los mismos bloqueos, en el mismo orden.
            Teacher::whereIn('user_id', $committee->members->pluck('user_id'))->orderBy('id')->lockForUpdate()->get();

            return $action($locked);
        });
    }

    private function ensureMutable(Defense $defense): void
    {
        abort_unless(in_array($defense->status, ['Pendiente', 'En coordinación'], true), 409, 'La programación ya fue confirmada. La reprogramación requiere definir su procedimiento.');
    }

    public function hasClassConflict(Defense $defense, Carbon $start, Carbon $end, ?int $teacherId = null): bool
    {
        $teacherIds = $teacherId ? [$teacherId] : Teacher::whereIn('user_id', $defense->committee->members->pluck('user_id'))->pluck('id');

        if (DefenseClassBlock::whereIn('teacher_id', $teacherIds)
            ->where('starts_at', '<', $end)->where('ends_at', '>', $start)->exists()) {
            return true;
        }

        // La vigencia explícita evita deducir un calendario institucional de la etiqueta del período.
        $classes = AcademicLoad::whereIn('teacher_id', $teacherIds)->where('status', 'ACTIVE')
            ->whereDate('valid_from', '<=', $start->toDateString())
            ->whereDate('valid_until', '>=', $start->toDateString())
            ->where('starts_at', '<', $end->format('H:i:s'))
            ->where('ends_at', '>', $start->format('H:i:s'))->get();

        return $classes->contains(fn ($class) => in_array($start->dayOfWeekIso, $class->weekdays, true));
    }

    public function setDuration(Defense $defense, int $duration): void
    {
        $this->locked($defense, function ($locked) use ($duration) {
            $this->ensureMutable($locked);
            abort_if($locked->options()->exists(), 409, 'La duración ya está vinculada a opciones de horario registradas.');
            $locked->update(['duration_minutes' => $duration]);
        });
    }

    public function propose(Defense $defense, User $user, string $startsAt): DefenseOption
    {
        return $this->locked($defense, function ($locked) use ($user, $startsAt) {
            $this->ensureMutable($locked);
            $start = Carbon::createFromFormat('!Y-m-d\TH:i', $startsAt);
            $end = $start->copy()->addMinutes($locked->duration_minutes);
            if (!$end->isSameDay($start) || $end->format('H:i') > Defense::LAST_ACADEMIC_TIME) {
                throw ValidationException::withMessages(['starts_at' => 'La opción debe terminar a más tardar a las 9:50 p. m. del mismo día.']);
            }
            if ($this->hasClassConflict($locked, $start, $end)) {
                throw ValidationException::withMessages(['starts_at' => 'La opción coincide con horas de clase registradas.']);
            }
            $option = $locked->options()->firstOrCreate(
                ['starts_at' => $start, 'duration_minutes' => $locked->duration_minutes, 'committee_revision' => $locked->committee->revision],
                ['ends_at' => $end, 'proposed_by' => $user->id],
            );
            $locked->update(['status' => 'En coordinación']);

            return $option;
        });
    }

    public function respond(Defense $defense, DefenseOption $option, User $user, array $fields): void
    {
        $this->locked($defense, function ($locked) use ($option, $user, $fields) {
            $this->ensureMutable($locked);
            Gate::authorize('respond', $locked);
            $currentOption = $locked->options()->findOrFail($option->id);
            abort_unless($currentOption->committee_revision === $locked->committee->revision, 409, 'La composición de la terna cambió. Se requiere una opción de horario actualizada.');
            if ($fields['available'] && $user->teacher && $this->hasClassConflict($locked, $currentOption->starts_at, $currentOption->ends_at, $user->teacher->id)) {
                throw ValidationException::withMessages(['available' => 'La opción coincide con tus horas de clase registradas.']);
            }
            $currentOption->availabilities()->updateOrCreate(
                ['user_id' => $user->id],
                ['available' => (bool) $fields['available'], 'observation' => $fields['observation'] ?? null],
            );
        });
    }

    public function addClassBlock(Defense $defense, User $user, array $fields): void
    {
        $this->locked($defense, function ($locked) use ($user, $fields) {
            $this->ensureMutable($locked);
            Gate::authorize('respond', $locked);
            abort_unless($user->teacher, 403);
            $start = Carbon::createFromFormat('!Y-m-d\TH:i', $fields['starts_at']);
            $end = Carbon::createFromFormat('!Y-m-d\TH:i', $fields['ends_at']);
            if (!$end->isSameDay($start) || $end->format('H:i') > Defense::LAST_ACADEMIC_TIME || $end->lessThanOrEqualTo($start)) {
                throw ValidationException::withMessages(['ends_at' => 'Registra un intervalo de clase válido dentro del día académico.']);
            }
            DefenseClassBlock::firstOrCreate(
                ['teacher_id' => $user->teacher->id, 'starts_at' => $start, 'ends_at' => $end],
                ['course_name' => $fields['course_name'] ?? null, 'recorded_by' => $user->id],
            );
        });
    }

    public function confirm(Defense $defense, DefenseOption $option, User $user): void
    {
        $this->locked($defense, function ($locked) use ($option, $user) {
            $this->ensureMutable($locked);
            $currentOption = $locked->options()->findOrFail($option->id);
            abort_unless($locked->committee->status === 'Confirmada' && $locked->committee->members->count() === 3, 409, 'La terna debe estar confirmada con tres integrantes.');
            abort_unless($currentOption->committee_revision === $locked->committee->revision && $currentOption->duration_minutes === $locked->duration_minutes, 409, 'La opción corresponde a una asignación anterior.');
            $participants = $locked->participants()->pluck('id');
            $agreed = $currentOption->availabilities()->whereIn('user_id', $participants)->where('available', true)->count();
            abort_unless($agreed === $participants->count(), 409, 'Falta la coincidencia de disponibilidad de todos los participantes.');
            if ($this->hasClassConflict($locked, $currentOption->starts_at, $currentOption->ends_at)) {
                throw ValidationException::withMessages(['option' => 'El horario coincide con horas de clase registradas.']);
            }
            $locked->update([
                'status' => 'Programada',
                'selected_option_id' => $currentOption->id,
                'confirmed_by' => $user->id,
                'confirmed_at' => now(),
            ]);
        });
    }

    public function performed(Defense $defense, ?string $observation): void
    {
        $this->locked($defense, function ($locked) use ($observation) {
            abort_unless($locked->status === 'Programada', 409, 'La defensa debe estar programada.');
            abort_if($locked->selectedOption->starts_at->isFuture(), 422, 'La fecha de la defensa aún no ha llegado.');
            $locked->update(['status' => 'Realizada', 'performed_at' => now(), 'observation' => $observation]);
        });
    }
}
