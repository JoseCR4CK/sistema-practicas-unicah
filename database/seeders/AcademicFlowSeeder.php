<?php

namespace Database\Seeders;

use App\Models\AcademicDocument;
use App\Models\DocumentVersion;
use App\Models\Visit;
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

            Visit::firstOrCreate(['practice_id' => $practice->id], ['status' => 'Pendiente']);

            foreach (array_keys(AcademicDocument::TYPES) as $type) {
                $document = AcademicDocument::firstOrCreate(['practice_id' => $practice->id, 'type' => $type]);
                $document->versions()->firstOrCreate(
                    ['version' => 1],
                    ['status' => DocumentVersion::PENDING, 'uploaded_by' => $practice->student->user_id, 'uploaded_at' => now(), 'submission_note' => 'Metadatos de demostración. Sin archivo adjunto.'],
                );
            }
        }
    }
}
