<?php

namespace Database\Seeders;

use App\Models\AcademicDocument;
use App\Models\DocumentVersion;
use App\Models\Visit;
use App\Models\Followup;
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

            // Las visitas de ejemplo se generan únicamente para prácticas demo activas.
            if ($practice->status === Practice::ACTIVE) {
                Visit::firstOrCreate(['practice_id' => $practice->id], ['status' => 'Pendiente']);
            }
            if ($practice->teacher_id) {
                Followup::withTrashed()->firstOrCreate(
                    ['submission_key' => '00000000-0000-4000-8000-'.str_pad(dechex($practice->id), 12, '0', STR_PAD_LEFT)],
                    ['practice_id' => $practice->id, 'teacher_id' => $practice->teacher_id, 'title' => 'Seguimiento académico', 'date' => today(), 'type' => 'Seguimiento', 'description' => 'Registro de acompañamiento del proceso de práctica profesional.', 'status' => 'Pendiente'],
                );
            }

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
