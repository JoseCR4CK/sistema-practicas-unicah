<?php

namespace App\Http\Controllers;

use App\Models\AcademicCommittee;
use App\Models\Defense;
use App\Models\DefenseClassBlock;
use App\Models\DefenseOption;
use App\Models\Teacher;
use App\Services\DefenseScheduling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class DefenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Defense::with(['committee.practice.student.user', 'selectedOption']);
        $manager = in_array($request->user()->role->name, ['COORDINACION', 'ROOT'], true);
        if ($request->user()->hasRole('ESTUDIANTE')) {
            $query->whereHas('committee.practice.student', fn ($query) => $query->where('user_id', $request->user()->id));
        } elseif (!$manager) {
            $query->whereHas('committee.members', fn ($query) => $query->where('user_id', $request->user()->id));
        }

        return view('academic.defenses.index', [
            'defensas' => $query->latest('id')->get(),
            'ternas' => $manager ? AcademicCommittee::with('practice.student.user')->get() : collect(),
        ]);
    }

    public function show(Defense $defense, DefenseScheduling $workflow)
    {
        Gate::authorize('view', $defense);
        $defense->load(['committee.practice.student.user', 'committee.members.user', 'selectedOption', 'options.availabilities.user']);
        $teacherIds = Teacher::whereIn('user_id', $defense->committee->members->pluck('user_id'))->pluck('id');

        return view('academic.defenses.show', [
            'defensa' => $defense,
            'clases' => DefenseClassBlock::whereIn('teacher_id', $teacherIds)->with('teacher.user')->orderBy('starts_at')->get(),
            'classConflicts' => $defense->options->mapWithKeys(fn ($option) => [
                $option->id => $workflow->hasClassConflict($defense, $option->starts_at, $option->ends_at),
            ]),
        ]);
    }

    public function store(Request $request, DefenseScheduling $workflow)
    {
        $fields = $request->validate([
            'academic_committee_id' => ['required', 'integer', 'exists:academic_committees,id'],
            'duration_minutes' => ['required', 'integer', Rule::in(Defense::DURATIONS)],
        ]);
        $defense = $workflow->create(AcademicCommittee::findOrFail($fields['academic_committee_id']), (int) $fields['duration_minutes']);

        return redirect()->route('coordinacion.defensa.show', $defense);
    }

    public function duration(Request $request, Defense $defense, DefenseScheduling $workflow)
    {
        Gate::authorize('update', $defense);
        $fields = $request->validate(['duration_minutes' => ['required', 'integer', Rule::in(Defense::DURATIONS)]]);
        $workflow->setDuration($defense, (int) $fields['duration_minutes']);

        return back()->with('status', 'Duración actualizada.');
    }

    public function propose(Request $request, Defense $defense, DefenseScheduling $workflow)
    {
        Gate::authorize('update', $defense);
        $fields = $request->validate(['starts_at' => ['required', 'date_format:Y-m-d\TH:i']]);
        $workflow->propose($defense, $request->user(), $fields['starts_at']);

        return redirect()->route('coordinacion.defensa.show', $defense)->with('status', 'Opción de horario registrada.');
    }

    public function respond(Request $request, Defense $defense, DefenseOption $option, DefenseScheduling $workflow)
    {
        Gate::authorize('respond', $defense);
        $fields = $request->validate(['available' => ['required', 'boolean'], 'observation' => ['nullable', 'string', 'max:5000']]);
        $workflow->respond($defense, $option, $request->user(), $fields);

        return back()->with('status', 'Disponibilidad registrada.');
    }

    public function addClassBlock(Request $request, Defense $defense, DefenseScheduling $workflow)
    {
        Gate::authorize('respond', $defense);
        $fields = $request->validate([
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'ends_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'course_name' => ['nullable', 'string', 'max:255'],
        ]);
        $workflow->addClassBlock($defense, $request->user(), $fields);

        return back()->with('status', 'Horas de clase registradas.');
    }

    public function confirm(Request $request, Defense $defense, DefenseOption $option, DefenseScheduling $workflow)
    {
        Gate::authorize('update', $defense);
        $workflow->confirm($defense, $option, $request->user());

        return redirect()->route('coordinacion.defensa.show', $defense)->with('status', 'Programación confirmada.');
    }

    public function performed(Request $request, Defense $defense, DefenseScheduling $workflow)
    {
        Gate::authorize('update', $defense);
        $fields = $request->validate(['observation' => ['nullable', 'string', 'max:5000']]);
        $workflow->performed($defense, $fields['observation'] ?? null);

        return redirect()->route('coordinacion.defensa.show', $defense)->with('status', 'Realización registrada.');
    }
}
