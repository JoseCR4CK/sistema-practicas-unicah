<?php

namespace App\Services;

use App\Http\Middleware\ClosingSchema;
use App\Models\Defense;
use App\Models\Monograph;
use App\Models\Practice;

class AcademicClosingSummary
{
    public function forPractice(?Practice $practice): array
    {
        $monographsReady = ClosingSchema::available('monographs');
        $committeesReady = ClosingSchema::available('committees');
        $defensesReady = ClosingSchema::available('defenses');
        $monograph = $monographsReady ? $practice?->monograph()->with(['currentVersion', 'reviews'])->first() : null;
        $committee = $committeesReady ? $practice?->committee()->with('members.user')->first() : null;
        $defense = $defensesReady ? $committee?->defense()->with('selectedOption')->first() : null;

        return [
            'monograph' => $monograph,
            'committee' => $committee,
            'defense' => $defense,
            'monograph_state' => $monographsReady ? ($monograph?->statusLabel() ?? 'Pendiente') : 'Pendiente de actualización',
            'committee_state' => $committeesReady ? ($committee?->status ?? 'Pendiente de asignación') : 'Pendiente de actualización',
            'defense_state' => $defensesReady ? ($defense?->status ?? 'Pendiente') : 'Pendiente de actualización',
            'deadline' => $practice?->monographDeadline(),
        ];
    }

    public function dashboardCounts(): array
    {
        return [
            'monographsInReview' => ClosingSchema::available('monographs')
                ? Monograph::with('reviews')->get()->filter(fn ($monograph) => $monograph->statusLabel() === 'En revisión')->count()
                : null,
            'defensesPending' => ClosingSchema::available('defenses')
                ? Defense::whereIn('status', ['Pendiente', 'En coordinación'])->count()
                : null,
        ];
    }
}
