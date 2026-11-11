<?php

namespace App\Http\Controllers;

use App\Models\DocumentVersion;
use App\Models\Practice;
use Illuminate\Http\Request;
use App\Services\AcademicClosingSummary;

class StudentFlowController extends Controller
{
    public function dashboard(Request $request)
    {
        return $this->page($request, 'estudiante.dashboard');
    }

    public function practice(Request $request)
    {
        return $this->page($request, 'estudiante.practica');
    }

    public function company(Request $request)
    {
        return $this->page($request, 'estudiante.empresa');
    }

    public function teacher(Request $request)
    {
        return $this->page($request, 'estudiante.docente');
    }

    private function page(Request $request, string $view)
    {
        $student = $request->user()->student;
        $practice = $student?->practices()
            ->with(['company', 'companyResponsible', 'teacher.user', 'documents.currentVersion', 'visits', 'applications'])
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [Practice::ACTIVE])->latest('id')->first();
        $application = $practice?->applications->sortByDesc('id')->first()
            ?? $student?->applications()->whereNull('practice_id')->latest('id')->first();
        $documents = $practice?->documents ?? collect();

        return view($view, [
            'process' => $practice,
            'student' => $student,
            'applicationStatus' => $application?->status ?? 'Sin solicitud registrada',
            'documentSummary' => $documents->where('currentVersion.status', DocumentVersion::APPROVED)->count().' aprobados de '.$documents->count().' registrados',
            'visitStatus' => $practice?->visits->sortByDesc('id')->first()?->effectiveStatus() ?? 'Sin visita registrada',
            'closing' => app(AcademicClosingSummary::class)->forPractice($practice),
        ]);
    }
}
