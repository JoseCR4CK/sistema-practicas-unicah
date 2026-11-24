<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\AcademicCareer;
use App\Models\DocumentVersion;
use App\Services\ReviewAccessRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccessRequestController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['type' => ['nullable', Rule::in(['STUDENT', 'TEACHER'])],
            'faculty' => ['nullable', 'integer', 'exists:academic_careers,id'], 'status' => ['nullable', Rule::in(array_keys(AccessRequest::STATES))], 'search' => ['nullable', 'string', 'max:255']]);
        $query = AccessRequest::with('career')->withCount('subjects')->latest('id');
        foreach (['type' => 'type', 'faculty' => 'academic_career_id', 'status' => 'status'] as $filter => $column) {
            if ($filters[$filter] ?? null) $query->where($column, $filters[$filter]);
        }
        foreach (preg_split('/\s+/u', trim($filters['search'] ?? ''), flags: PREG_SPLIT_NO_EMPTY) as $search) {
            $query->where(fn ($q) => $q->where('first_names', 'like', '%'.$search.'%')->orWhere('last_names', 'like', '%'.$search.'%')->orWhere('dni', 'like', '%'.$search.'%')->orWhere('account_number', 'like', '%'.$search.'%'));
        }
        return view('access.index', ['applications' => $query->paginate(20)->withQueryString(),
            'faculties' => AcademicCareer::where('status', 'ACTIVE')->orderBy('name')->get(), 'filters' => $filters,
            'counts' => AccessRequest::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')]);
    }

    public function show(AccessRequest $application)
    {
        return view('access.show', ['application' => $application->load(['career', 'subjects', 'documents.versions', 'documents.currentVersion', 'events.actor', 'reviewer'])]);
    }

    public function review(Request $request, AccessRequest $application)
    {
        $fields = $request->validate([
            'revision' => ['required', 'integer', 'min:0'],
            'action' => ['required', Rule::in(['start', 'induction', 'document', 'correct', 'reject', 'approve'])],
            'note' => ['nullable', 'required_if:action,correct,reject', Rule::requiredIf($request->input('action') === 'document' && $request->input('result') === DocumentVersion::CORRECTION), 'string', 'max:5000'],
            'induction_verified' => ['required_if:action,induction', 'boolean'],
            'document_id' => ['required_if:action,document', 'integer'], 'version_id' => ['required_if:action,document', 'integer'],
            'result' => ['required_if:action,document', Rule::in([DocumentVersion::APPROVED, DocumentVersion::CORRECTION])],
        ]);
        app(ReviewAccessRequest::class)->execute($application, $request->user(), $fields);
        return back()->with('status', 'Acción registrada.');
    }
}
