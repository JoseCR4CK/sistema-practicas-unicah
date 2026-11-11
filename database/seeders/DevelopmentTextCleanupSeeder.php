<?php

namespace Database\Seeders;

use App\Models\DocumentVersion;
use App\Models\PracticeRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DevelopmentTextCleanupSeeder extends Seeder
{
    public function run(): void
    {
        // Sustituir únicamente textos originales exactos en cuentas de desarrollo.
        // Las observaciones editadas y las cuentas reales se conservan.
        DB::transaction(function () {
            $requests = PracticeRequest::whereHas('student.user', fn ($query) => $query->where('email', 'like', '%.demo'))
                ->where('student_observation', 'Solicitud de demostración para desarrollo.')->lockForUpdate()->get();
            foreach ($requests as $request) {
                if ($request->student_observation === 'Solicitud de demostración para desarrollo.') {
                    $request->update(['student_observation' => 'Solicitud de práctica profesional.']);
                }
            }
            $versions = DocumentVersion::whereHas('document.practice.student.user', fn ($query) => $query->where('email', 'like', '%.demo'))
                ->where('submission_note', 'Metadatos de demostración. Sin archivo adjunto.')->lockForUpdate()->get();
            foreach ($versions as $version) {
                if ($version->submission_note === 'Metadatos de demostración. Sin archivo adjunto.') {
                    $version->update(['submission_note' => 'Información de la versión del documento.']);
                }
            }
        });
    }
}
