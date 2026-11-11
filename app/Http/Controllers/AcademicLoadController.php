<?php

namespace App\Http\Controllers;

use App\Models\AcademicLoad;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AcademicLoadController extends Controller
{
    private function teacher(Request $request, ?Teacher $teacher): Teacher
    {
        if ($request->user()->hasRole('DOCENTE_SEGUIMIENTO')) {
            return $request->user()->teacher ?? abort(403, 'No existe un perfil docente asociado.');
        }

        return $teacher ?? abort(404);
    }

    public function index(Request $request, ?Teacher $teacher = null)
    {
        $teacher = $this->teacher($request, $teacher);
        $filters = $request->validate(['year' => ['nullable', 'integer', 'between:1900,2200'], 'period' => ['nullable', 'string', 'max:80']]);
        $query = $teacher->academicLoads()->with('changes.user')->orderByDesc('academic_year')->orderBy('academic_period')->orderBy('starts_at');
        if ($filters['year'] ?? null) {
            $query->where('academic_year', $filters['year']);
        }
        if ($filters['period'] ?? null) {
            $query->where('academic_period', $filters['period']);
        }

        return view('academic.loads.index', ['teacher' => $teacher->load('user'), 'loads' => $query->get(), 'filters' => $filters]);
    }

    public function store(Request $request, ?Teacher $teacher = null)
    {
        $teacher = $this->teacher($request, $teacher);
        $fields = $this->validateFields($request);
        $request->validate(['submission_key' => ['required', 'uuid']]);
        DB::transaction(function () use ($request, $teacher, $fields) {
            Teacher::whereKey($teacher->id)->lockForUpdate()->firstOrFail();
            $existing = AcademicLoad::where('submission_key', $request->submission_key)->first();
            if ($existing) {
                abort_unless($existing->teacher_id === $teacher->id, 409);
                return;
            }
            $load = new AcademicLoad($fields);
            $load->teacher_id = $teacher->id;
            $load->submission_key = $request->submission_key;
            $load->recorded_by = $request->user()->id;
            $load->save();
        });

        return back()->with('status', 'Clase registrada.');
    }

    public function update(Request $request, AcademicLoad $load)
    {
        Gate::authorize('update', $load);
        $fields = $this->validateFields($request);
        $request->validate([
            'revision' => ['required', 'integer', 'min:0'],
            'reason' => [Rule::requiredIf(in_array($request->user()->role?->name, ['COORDINACION', 'ROOT'], true)), 'nullable', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($request, $load, $fields) {
            Teacher::whereKey($load->teacher_id)->lockForUpdate()->firstOrFail();
            $locked = AcademicLoad::whereKey($load->id)->lockForUpdate()->firstOrFail();
            Gate::authorize('update', $locked);
            abort_unless($locked->revision === (int) $request->revision, 409, 'La clase cambió. Recarga la página antes de corregirla.');
            $before = $locked->only(array_keys($fields));
            $locked->fill($fields);
            if (!$locked->isDirty()) {
                return;
            }
            $locked->revision++;
            $locked->save();
            $locked->changes()->create(['user_id' => $request->user()->id, 'reason' => $request->reason, 'before' => $before, 'after' => $locked->only(array_keys($fields))]);
        });

        return back()->with('status', 'Carga académica actualizada.');
    }

    private function validateFields(Request $request): array
    {
        $fields = $request->validate([
            'academic_period' => ['required', 'string', 'max:80'],
            'academic_year' => ['required', 'integer', 'between:1900,2200'],
            'subject' => ['required', 'string', 'max:255'],
            'career' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:80'],
            'weekdays' => ['required', 'array', 'min:1', 'max:7'],
            'weekdays.*' => ['required', 'integer', 'distinct', 'between:1,7'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at', 'before_or_equal:21:50'],
            'valid_from' => ['required', 'date_format:Y-m-d'],
            'valid_until' => ['required', 'date_format:Y-m-d', 'after_or_equal:valid_from'],
            'credits' => ['nullable', 'numeric', 'between:0,9999.99'],
            'weekly_class_hours' => ['nullable', 'numeric', 'between:0,9999.99'],
            'status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);
        $fields['weekdays'] = array_map('intval', $fields['weekdays']);
        sort($fields['weekdays']);

        return $fields;
    }
}
