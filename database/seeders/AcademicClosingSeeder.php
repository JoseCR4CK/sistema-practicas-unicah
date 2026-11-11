<?php

namespace Database\Seeders;

use App\Models\Monograph;
use App\Models\AcademicCommittee;
use App\Models\Practice;
use App\Services\MonographWorkflow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AcademicClosingSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('monographs')) {
            return;
        }
        $practices = Practice::where('status', Practice::ACTIVE)->where('stage', 'Monografía')
            ->whereDate('start_date', '<=', today())->whereNotNull('end_date')->with('student.user')->get();
        foreach ($practices as $practice) {
            if (!str_ends_with($practice->student->user->email, '.demo')) {
                continue;
            }
            if (Schema::hasTable('academic_committees')) {
                AcademicCommittee::firstOrCreate(['practice_id' => $practice->id]);
            }
            if (Monograph::where('practice_id', $practice->id)->exists()) {
                continue;
            }
            app(MonographWorkflow::class)->submit($practice, $practice->student->user, [
                'title' => 'Informe de práctica profesional',
                'submission_key' => '10000000-0000-4000-8000-'.str_pad(dechex($practice->id), 12, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
