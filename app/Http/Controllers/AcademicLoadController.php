<?php

namespace App\Http\Controllers;

use App\Models\AcademicLoad;
use App\Models\AcademicCareer;
use App\Services\AcademicClassTime;
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
        $filters = $request->validate(['year' => ['nullable', 'integer', 'between:1900,2200'], 'period' => ['nullable', Rule::in(array_keys(AcademicLoad::PERIODS))]]);
        $query = $teacher->academicLoads()->with(['changes.user', 'catalogSubject', 'catalogCareer'])->orderByDesc('academic_year')->orderBy('academic_period')->orderBy('starts_at');
        if ($filters['year'] ?? null) {
            $query->where('academic_year', $filters['year']);
        }
        if ($filters['period'] ?? null) {
            $query->whereIn('academic_period', AcademicLoad::periodAliases($filters['period']));
        }

        $careers = AcademicCareer::where('status', 'ACTIVE')->with(['subjects' => fn ($query) => $query->where('status', 'ACTIVE')->orderBy('name')])->orderBy('name')->get();
        $catalog = $careers->map(fn ($career) => [
            'id' => $career->id, 'name' => $career->name,
            'subjects' => $career->subjects->map(fn ($subject) => [
                'id' => $subject->id, 'name' => $subject->name, 'code' => $subject->code,
                'credits' => $subject->credits, 'weekly_class_hours' => $subject->confirmedWeeklyHours(),
                'allowed_minutes' => $subject->confirmedWeeklyHours() === null ? null : $subject->confirmedWeeklyHours() * AcademicClassTime::CLASS_MINUTES,
            ])->values(),
        ])->values();

        return view('academic.loads.index', ['teacher' => $teacher->load('user'), 'loads' => $query->get(), 'filters' => $filters, 'careers' => $careers, 'catalog' => $catalog]);
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
            $load = new AcademicLoad(app(AcademicClassTime::class)->catalogFields($fields));
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
            $catalogFields = app(AcademicClassTime::class)->catalogFields($fields);
            $before = $locked->only(array_keys($catalogFields));
            $locked->fill($catalogFields);
            if (!$locked->isDirty()) {
                return;
            }
            $locked->revision++;
            $locked->save();
            $locked->changes()->create(['user_id' => $request->user()->id, 'reason' => $request->reason, 'before' => $before, 'after' => $locked->only(array_keys($catalogFields))]);
        });

        return back()->with('status', 'Carga académica actualizada.');
    }

    private function validateFields(Request $request): array
    {
        $fields = $request->validate([
            'academic_period' => ['required', Rule::in(array_keys(AcademicLoad::PERIODS))],
            'academic_year' => ['required', 'integer', 'between:1900,2200'],
            'academic_career_id' => ['required', 'integer', 'exists:academic_careers,id'],
            'academic_subject_id' => ['required', 'integer', 'exists:academic_subjects,id'],
            'section' => ['nullable', 'string', 'max:80'],
            'weekdays' => ['required', 'array', 'min:1', 'max:7'],
            'weekdays.*' => ['required', 'integer', 'distinct', 'between:1,7'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at', 'before_or_equal:21:50'],
            'valid_from' => ['required', 'date_format:Y-m-d'],
            'valid_until' => ['required', 'date_format:Y-m-d', 'after_or_equal:valid_from'],
            'status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);
        $fields['weekdays'] = array_map('intval', $fields['weekdays']);
        sort($fields['weekdays']);

        return $fields;
    }
}
