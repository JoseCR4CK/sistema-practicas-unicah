<?php

namespace App\Services;

use App\Models\AcademicSubject;
use Illuminate\Validation\ValidationException;

class AcademicClassTime
{
    public const CLASS_MINUTES = 50;
    public const CONFIRMED_HOURS = ['2.00' => 3, '3.00' => 4, '4.00' => 5];

    public static function hoursForCredits(int|float|string|null $credits): ?int
    {
        return $credits === null ? null : (self::CONFIRMED_HOURS[number_format((float) $credits, 2, '.', '')] ?? null);
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
        if (!$subject || $subject->academic_career_id !== (int) $fields['academic_career_id'] || !$subject->selectable()) {
            throw ValidationException::withMessages(['academic_subject_id' => 'Selecciona una asignatura activa de esta carrera con créditos y equivalencia confirmados.']);
        }
        $hours = $subject->confirmedWeeklyHours();
        $allowed = $hours * self::CLASS_MINUTES;
        $scheduled = self::scheduledMinutes($fields['starts_at'], $fields['ends_at'], $fields['weekdays']);
        if ($scheduled > $allowed) {
            throw ValidationException::withMessages(['ends_at' => "Esta asignatura permite {$hours} horas clase semanales ({$allowed} minutos). El horario seleccionado suma {$scheduled} minutos."]);
        }

        return array_replace($fields, ['subject' => $subject->name, 'career' => $subject->career->name, 'credits' => $subject->credits, 'weekly_class_hours' => $hours]);
    }
}
