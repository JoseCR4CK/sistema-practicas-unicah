<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use App\Models\AcademicCareer;
use App\Services\RegisterAccessRequest;
use Illuminate\Http\Request;

class PublicAccessController extends Controller
{
    public function student() { return $this->form('STUDENT'); }
    public function teacher() { return $this->form('TEACHER'); }
    public function storeStudent(Request $request) { return $this->store($request, 'STUDENT'); }
    public function storeTeacher(Request $request) { return $this->store($request, 'TEACHER'); }

    private function form(string $type, ?AccessRequest $application = null, ?string $token = null)
    {
        $faculties = AcademicCareer::where('status', 'ACTIVE')->when($type === 'STUDENT', fn ($query) => $query->where('type', 'FACULTY'))
            ->with(['subjects' => fn ($query) => $query->where('status', 'ACTIVE')->orderBy('name')])->orderBy('name')->get();
        return response()->view('access.register', compact('type', 'faculties', 'application', 'token'))
            ->header('Cache-Control', 'private, no-store')->header('Referrer-Policy', 'no-referrer');
    }

    private function store(Request $request, string $type)
    {
        [$application, $token] = app(RegisterAccessRequest::class)->save($request, $type);
        return redirect()->route('registro.estado', [$application, $token])->with('status', 'Solicitud recibida. Conserva este enlace privado para consultar su estado y enviar correcciones. Todavía no se creó una cuenta activa.');
    }

    public static function authorizeToken(AccessRequest $application, string $token): void
    {
        abort_unless(strlen($token) === 64 && hash_equals($application->tracking_token_hash, hash('sha256', $token)), 404);
    }

    public function show(AccessRequest $application, string $token)
    {
        self::authorizeToken($application, $token);
        if ($application->status === 'CORRECTION_REQUIRED') return $this->form($application->type, $application->load(['subjects', 'documents.currentVersion']), $token);
        return response()->view('access.status', ['application' => $application->load(['career', 'subjects', 'documents.currentVersion']), 'token' => $token])
            ->header('Cache-Control', 'private, no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function correct(Request $request, AccessRequest $application, string $token)
    {
        self::authorizeToken($application, $token);
        $request->validate(['revision' => ['required', 'integer', 'min:0']]);
        app(RegisterAccessRequest::class)->save($request, $application->type, $application);
        return redirect()->route('registro.estado', [$application, $token])->with('status', 'Corrección enviada. Las versiones anteriores se conservaron.');
    }
}
