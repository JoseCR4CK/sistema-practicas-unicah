<?php

namespace App\Services;

use App\Models\AcademicSubject;
use Illuminate\Validation\ValidationException;

class AcademicClassTime
{
    public const CLASS_MINUTES = 50;

    public static function hoursForCredits(int|float|string|null $credits, ?string $type = 'NORMAL'): ?int
    {
        if ($credits === null || !is_numeric($credits) || (float) $credits < 1 || (float) $credits !== (float) (int) $credits) return null;
        $credits = (int) $credits;
        if ($credits === 1) return 1;
        if ($credits === 2 && $type === 'LABORATORY') return 2;
        return $credits + 1;
    }

    public static function scheduledMinutes(string $start, string $end, array $days): int
    {
        $minutes = fn ($time) => (int) substr($time, 0, 2) * 60 + (int) substr($time, 3, 2);

        return ($minutes($end) - $minutes($start)) * count(array_unique($days));
    }

    public function catalogFields(array $fields): array
    {
        // Se vuelve a consultar dentro de la transacción: nunca confiar en créditos del request.
        $subject = AcademicSubject::with('career')->find($fields['academic_subject_id']);
        if (!$subject || !$subject->availableFor((int) $fields['academic_career_id']) || $subject->confirmedWeeklyHours() === null) {
            throw ValidationException::withMessages(['academic_subject_id' => 'Selecciona una asignatura activa de esta carrera con créditos y equivalencia confirmados.']);
        }
        $hours = $subject->confirmedWeeklyHours();
        $allowed = $hours * self::CLASS_MINUTES;
        $scheduled = self::scheduledMinutes($fields['starts_at'], $fields['ends_at'], $fields['weekdays']);
        if ($scheduled > $allowed) {
            throw ValidationException::withMessages(['ends_at' => "Esta asignatura permite {$hours} horas clase semanales ({$allowed} minutos). El horario seleccionado suma {$scheduled} minutos."]);
        }

        return array_replace($fields, ['subject' => $subject->name, 'career' => \App\Models\AcademicCareer::findOrFail($fields['academic_career_id'])->name, 'credits' => $subject->credits, 'weekly_class_hours' => $hours]);
    }
}
