<?php

namespace App\Http\Controllers;

use App\Models\AcademicCareer;
use App\Models\AcademicCommittee;
use App\Models\AcademicLoad;
use App\Models\Practice;
use App\Models\Teacher;
use App\Services\CommitteeAssignment;
use App\Services\CommitteeLifecycle;
use App\Services\CommitteeWorkload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CommitteeController extends Controller
{
    private function filters(Request $request): array
    {
        return $request->validate([
            'faculty' => ['nullable', 'integer', 'exists:academic_careers,id'],
            'period' => ['nullable', Rule::in(array_keys(AcademicLoad::PERIODS))], 'year' => ['nullable', 'integer', 'between:1900,2200'],
            'status' => ['nullable', Rule::in(['pending', 'active', 'closed'])], 'search' => ['nullable', 'string', 'max:255'],
        ], ['in' => 'Seleccione una opción válida para :attribute.', 'integer' => 'Indique un número válido para :attribute.',
            'exists' => 'La facultad seleccionada no existe.', 'between' => 'El año debe estar entre 1900 y 2200.', 'max' => 'La búsqueda no debe superar 255 caracteres.']);
    }

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $manager = in_array($request->user()->role->name, ['COORDINACION', 'ROOT'], true);
        $query = Practice::with(['student.user', 'student.academicCareer', 'committee.members.user']);
        if ($request->user()->hasRole('ESTUDIANTE')) {
            $query->whereHas('student', fn ($q) => $q->where('user_id', $request->user()->id));
        } elseif (! $manager) {
            $query->whereHas('committee.members', fn ($q) => $q->where('user_id', $request->user()->id));
        }
        if ($filters['faculty'] ?? null) {
            $faculty = AcademicCareer::findOrFail($filters['faculty']);
            $query->whereHas('student', fn ($q) => $q->where('academic_career_id', $faculty->id)
                ->orWhere(fn ($legacy) => $legacy->whereNull('academic_career_id')->where('career', $faculty->name)));
        }
        foreach (preg_split('/\s+/u', trim($filters['search'] ?? ''), flags: PREG_SPLIT_NO_EMPTY) as $term) {
            $query->whereHas('student', fn ($q) => $q->where('account_number', 'like', '%'.$term.'%')->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$term.'%')->orWhere('dni', 'like', '%'.$term.'%')));
        }
        if ($filters['period'] ?? null) {
            $query->where(fn ($q) => $q->whereHas('committee', fn ($c) => $c->whereIn('origin_period', AcademicLoad::periodAliases($filters['period'])))
                ->orWhere(fn ($p) => $p->whereDoesntHave('committee')->whereIn('academic_period', AcademicLoad::periodAliases($filters['period']))));
        }
        if ($filters['year'] ?? null) {
            $query->where(fn ($q) => $q->whereHas('committee', fn ($c) => $c->where('origin_year', $filters['year']))
                ->orWhere(fn ($p) => $p->whereDoesntHave('committee')->where('academic_year', $filters['year'])));
        }
        if (($filters['status'] ?? '') === 'pending') {
            $query->where(fn ($q) => $q->whereDoesntHave('committee')->orWhereHas('committee', fn ($c) => $c->whereDoesntHave('members')->whereNull('closed_at')));
        } elseif (($filters['status'] ?? '') === 'active') {
            $query->whereHas('committee', fn ($q) => $q->active());
        } elseif (($filters['status'] ?? '') === 'closed') {
            $query->whereHas('committee', fn ($q) => $q->whereNotNull('closed_at')->orWhere('status', 'Cerrada'));
        }

        return view('academic.committees.index', ['practicas' => $query->orderBy('student_id')->orderByDesc('id')->get()->sortBy(fn ($practice) => ($practice->student->academicCareer?->name ?? $practice->student->career ?? '').'|'.$practice->student->user->name)->values(),
            'filters' => $filters, 'faculties' => AcademicCareer::studentOptions()->orderBy('name')->get(), 'manager' => $manager]);
    }

    public function show(AcademicCommittee $committee, CommitteeWorkload $workload)
    {
        Gate::authorize('view', $committee);
        $manager = Gate::allows('update', $committee);
        $teachers = $manager ? Teacher::eligibleForCommittee()->with(['user', 'faculties'])->get()->sortBy('user.name') : collect();
        $loads = $manager ? $workload->active() : [];
        $catalog = $teachers->map(fn ($teacher) => ['user_id' => $teacher->user_id, 'name' => $teacher->user->name,
            'faculty_ids' => $teacher->faculties->where('status', 'ACTIVE')->pluck('id')->values(),
            'faculty' => $teacher->faculties->firstWhere('pivot.is_primary', true)?->name ?? $teacher->faculty ?? 'Sin área principal',
            'total' => $loads[$teacher->user_id]['total'] ?? 0, 'periods' => $loads[$teacher->user_id]['periods'] ?? []])->values();

        return view('academic.committees.show', ['terna' => $committee->load(['practice.student.user', 'practice.teacher.user', 'members.user', 'members.faculty', 'membershipHistory.user', 'membershipHistory.faculty', 'events.actor', 'defense']),
            'faculties' => $manager ? AcademicCareer::where('status', 'ACTIVE')->whereIn('type', ['FACULTY', 'GENERAL'])->orderBy('name')->get() : collect(),
            'catalog' => $catalog, 'originLabel' => CommitteeWorkload::periodLabel($committee->origin_period, $committee->origin_year)]);
    }

    private function fields(Request $request): array
    {
        return $request->validate([
            'status' => ['required', Rule::in(['Pendiente de asignación', 'Asignada', 'Confirmada', 'Activa'])],
            'revision' => ['required', 'integer', 'min:0'], 'reason' => ['nullable', 'string', 'max:5000'],
            'members' => ['nullable', 'array', 'max:3'], 'members.*' => ['array:user_id,function,academic_career_id'],
            'members.*.user_id' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'members.*.function' => ['required', Rule::in(AcademicCommittee::FUNCTIONS)],
            'members.*.academic_career_id' => ['required', 'integer', Rule::exists('academic_careers', 'id')->where('status', 'ACTIVE')],
        ], ['required' => 'Debe completar :attribute.', 'in' => 'Seleccione una función o estado válido.',
            'exists' => 'El docente o facultad seleccionado no existe o está inactivo.', 'distinct' => 'Los tres docentes deben ser distintos.',
            'max' => 'La información supera el límite permitido.', 'integer' => 'Seleccione un identificador válido.']);
    }

    public function store(Request $request, CommitteeAssignment $assignment)
    {
        $request->validate(['practice_id' => ['required', 'integer', 'exists:practices,id']], ['required' => 'Seleccione una práctica.', 'exists' => 'La práctica seleccionada no existe.']);
        $committee = $assignment->save(Practice::findOrFail($request->integer('practice_id')), $request->user(), $this->fields($request));

        return redirect()->route('coordinacion.terna.show', $committee)->with('status', 'Terna registrada. Puede asignarse desde el inicio de la práctica.');
    }

    public function update(Request $request, AcademicCommittee $committee, CommitteeAssignment $assignment)
    {
        Gate::authorize('update', $committee);
        $assignment->save($committee->practice, $request->user(), $this->fields($request));

        return redirect()->route('coordinacion.terna.show', $committee)->with('status', 'Asignación actualizada con historial.');
    }

    public function lifecycle(Request $request, AcademicCommittee $committee, CommitteeLifecycle $workflow)
    {
        Gate::authorize('update', $committee);
        $fields = $request->validate(['action' => ['required', Rule::in(['result', 'retry', 'close'])],
            'revision' => ['required', 'integer', 'min:0'], 'round' => ['required', 'integer', 'min:1'], 'reason' => ['required', 'string', 'max:5000'],
            'result' => ['required_if:action,result', Rule::in(['Aprobada', 'Reprobada'])],
            'final_records_verified' => ['required_if:action,close', 'boolean'],
        ], ['required' => 'Debe completar :attribute.', 'required_if' => 'Debe completar :attribute para esta acción.',
            'in' => 'Seleccione un resultado o acción válido.', 'boolean' => 'Indique una verificación válida.', 'integer' => 'La revisión o ronda no es válida.'],
            ['reason' => 'el motivo', 'result' => 'el resultado de defensa', 'final_records_verified' => 'la verificación de evaluación/documentación final']);
        $workflow->act($committee, $request->user(), $fields);

        return back()->with('status', 'Acción registrada en el historial de la terna.');
    }

    public function workload(Request $request, CommitteeWorkload $workload)
    {
        $filters = $this->filters($request);
        $teachers = Teacher::with(['user', 'faculties', 'committeeMembershipHistory.committee.practice.student.user'])->get();
        if ($filters['faculty'] ?? null) {
            $teachers = $teachers->filter(fn ($teacher) => $teacher->faculties->contains('id', (int) $filters['faculty']));
        }
        if ($search = trim($filters['search'] ?? '')) {
            $teachers = $teachers->filter(fn ($teacher) => str_contains(mb_strtolower($teacher->user->name), mb_strtolower($search)));
        }
        $loads = $workload->active();
        $histories = [];
        foreach ($teachers as $teacher) {
            $history = $teacher->committeeMembershipHistory->pluck('committee')->unique('id');
            if ($filters['period'] ?? null) {
                $history = $history->filter(fn ($committee) => AcademicLoad::periodKey($committee->origin_period) === $filters['period']);
            }
            if ($filters['year'] ?? null) {
                $history = $history->where('origin_year', (int) $filters['year']);
            }
            if (($filters['status'] ?? '') === 'closed') {
                $history = $history->filter->isClosed();
            } elseif (($filters['status'] ?? '') === 'active') {
                $history = $history->reject->isClosed();
            } elseif (($filters['status'] ?? '') === 'pending') {
                $history = $history->filter(fn ($committee) => $committee->members()->doesntExist() && ! $committee->isClosed());
            }
            $histories[$teacher->id] = $history;
        }
        if (($filters['period'] ?? null) || ($filters['year'] ?? null) || ($filters['status'] ?? null)) {
            $teachers = $teachers->filter(fn ($teacher) => $histories[$teacher->id]->isNotEmpty());
        }

        return view('academic.committees.workload', ['teachers' => $teachers->sortBy('user.name'), 'loads' => $loads, 'histories' => $histories,
            'filters' => $filters, 'faculties' => AcademicCareer::where('status', 'ACTIVE')->orderBy('name')->get()]);
    }
}
