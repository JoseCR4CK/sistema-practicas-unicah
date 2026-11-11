<?php

namespace App\Http\Controllers;

use App\Models\AcademicCommittee;
use App\Models\Practice;
use App\Models\User;
use App\Services\CommitteeAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CommitteeController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicCommittee::with(['practice.student.user', 'members.user']);
        $manager = in_array($request->user()->role->name, ['COORDINACION', 'ROOT'], true);
        if ($request->user()->hasRole('ESTUDIANTE')) {
            $query->whereHas('practice.student', fn ($query) => $query->where('user_id', $request->user()->id));
        } elseif (!$manager) {
            $query->whereHas('members', fn ($query) => $query->where('user_id', $request->user()->id));
        }

        return view('academic.committees.index', [
            'ternas' => $query->latest('id')->get(),
            'practicas' => $manager ? Practice::with('student.user')->get() : collect(),
        ]);
    }

    public function show(AcademicCommittee $committee)
    {
        Gate::authorize('view', $committee);

        return view('academic.committees.show', [
            'terna' => $committee->load(['practice.student.user', 'members.user']),
            'personas' => User::where('status', 'ACTIVE')->orWhereIn('id', $committee->members->pluck('user_id'))->orderBy('name')->get(),
        ]);
    }

    private function fields(Request $request): array
    {
        $fields = $request->validate([
            'status' => ['required', Rule::in(AcademicCommittee::STATUSES)],
            'revision' => ['required', 'integer', 'min:0'],
            'members' => ['nullable', 'array', 'max:3'],
            'members.*' => ['array:user_id,function'],
            'members.*.user_id' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'members.*.function' => ['required', Rule::in(['Coordinador', 'Miembro'])],
        ]);

        return $fields;
    }

    public function store(Request $request, CommitteeAssignment $assignment)
    {
        $request->validate(['practice_id' => ['required', 'integer', 'exists:practices,id']]);
        $fields = $this->fields($request);
        $practice = Practice::findOrFail($request->integer('practice_id'));
        $committee = $assignment->save($practice, $request->user(), $fields);

        return redirect()->route('coordinacion.terna.show', $committee)->with('status', 'Terna registrada.');
    }

    public function update(Request $request, AcademicCommittee $committee, CommitteeAssignment $assignment)
    {
        Gate::authorize('update', $committee);
        $assignment->save($committee->practice, $request->user(), $this->fields($request));

        return redirect()->route('coordinacion.terna.show', $committee)->with('status', 'Asignación actualizada.');
    }
}
