<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use App\Models\DocumentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccessDocumentController extends Controller
{
    public function publicFile(AccessRequest $application, string $token, DocumentVersion $version, int $file)
    {
        PublicAccessController::authorizeToken($application, $token);
        abort_unless($version->document->access_request_id === $application->id, 404);
        return $this->download($version, $file);
    }

    public function privateFile(Request $request, DocumentVersion $version, int $file)
    {
        abort_unless($version->document->access_request_id !== null, 404);
        $user = $request->user();
        abort_unless($user->isActive() && (in_array($user->role?->name, ['COORDINACION', 'ROOT'], true)
            || ($user->hasRole('ESTUDIANTE') && $version->document->student?->user_id === $user->id)), 403);
        return $this->download($version, $file);
    }

    private function download(DocumentVersion $version, int $file)
    {
        $metadata = $version->files[$file] ?? abort(404);
        abort_unless(Storage::disk('access_documents')->exists($metadata['path']), 404);
        return Storage::disk('access_documents')->download($metadata['path'], $metadata['name'], [
            'Content-Type' => $metadata['mime'], 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store', 'Referrer-Policy' => 'no-referrer',
        ]);
    }
}
