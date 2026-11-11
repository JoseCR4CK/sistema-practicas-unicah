<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\PracticeRequest;
use Illuminate\Database\Seeder;

class AcademicFlowSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Practice::with('student.user')->get() as $practice) {
            if (!str_ends_with($practice->student->user->email, '.demo')) {
                continue;
            }

            PracticeRequest::firstOrCreate(
                ['practice_id' => $practice->id, 'student_id' => $practice->student_id],
                ['status' => 'Pendiente', 'submitted_at' => now(), 'student_observation' => 'Solicitud de demostración para desarrollo.'],
            );
        }
    }
}
