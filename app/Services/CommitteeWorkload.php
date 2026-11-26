<?php

namespace App\Services;

use App\Models\AcademicLoad;
use App\Models\CommitteeMember;

class CommitteeWorkload
{
    public static function periodLabel(?string $period, ?int $year): string
    {
        return (AcademicLoad::periodKey($period) ?? $period ?? 'Período pendiente').' · '.($year ?? 'Año pendiente');
    }

    public function active(): array
    {
        $result = [];
        $members = CommitteeMember::whereNull('ended_at')->whereHas('committee', fn ($q) => $q->active())
            ->with('committee:id,origin_period,origin_year')->get();
        foreach ($members->groupBy('user_id') as $userId => $assignments) {
            $assignments = $assignments->unique('academic_committee_id');
            $periods = $assignments->groupBy(fn ($member) => self::periodLabel($member->committee->origin_period, $member->committee->origin_year))->map->count()->all();
            $result[$userId] = ['total' => $assignments->count(), 'periods' => $periods];
        }

        return $result;
    }
}
