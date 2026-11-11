<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class FollowupController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user()->hasRole('DOCENTE_SEGUIMIENTO');
        $query = Followup::with(['practice.student.user', 'teacher.user']);
        if ($teacher) {
            $query->whereHas('practice.teacher', fn ($query) => $query->where('user_id', $request->user()->id));
        }

        return view('docente.seguimientos', [
            'seguimientos' => $query->latest('date')->latest('id')->get(),
            'teacherPortal' => $teacher,
            'practicas' => $teacher
                ? Practice::whereHas('teacher', fn ($query) => $query->where('user_id', $request->user()->id))->with('student.user')->get()
                : collect(),
        ]);
    }

    public function show(Request $request, Followup $followup)
    {
        Gate::authorize('view', $followup);

        return view('academic.followup-detail', [
            'seguimiento' => $followup->load(['practice.student.user', 'teacher.user']),
            'teacherPortal' => $request->user()->hasRole('DOCENTE_SEGUIMIENTO'),
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate($this->rules() + [
            'practice_id' => ['required', 'integer'],
            'submission_key' => ['required', 'uuid'],
        ]);
        $followup = DB::transaction(function () use ($fields, $request) {
            $practice = Practice::whereHas('teacher', fn ($query) => $query->where('user_id', $request->user()->id))
                ->lockForUpdate()->findOrFail($fields['practice_id']);
            $existing = Followup::withTrashed()->where('submission_key', $fields['submission_key'])->first();
            if ($existing) {
                Gate::authorize('update', $existing);
                abort_unless($existing->practice_id === $practice->id, 409, 'La clave corresponde a otro proceso.');
                abort_if($existing->trashed(), 409, 'El seguimiento fue eliminado.');
                return $existing;
            }

            return Followup::create($fields + ['teacher_id' => $practice->teacher_id]);
        });

        return redirect()->route('docente.seguimiento', $followup)->with('status', 'Seguimiento registrado.');
    }

    public function update(Request $request, Followup $followup)
    {
        Gate::authorize('update', $followup);
        $fields = $request->validate($this->rules() + ['revision' => ['required', 'integer', 'min:0']]);
        $this->change($followup, $fields);

        return redirect()->route('docente.seguimiento', $followup)->with('status', 'Seguimiento actualizado.');
    }

    public function changeStatus(Request $request, Followup $followup)
    {
        Gate::authorize('update', $followup);
        $fields = $request->validate(['status' => ['required', 'string', 'max:100'], 'revision' => ['required', 'integer', 'min:0']]);
        $this->change($followup, $fields);

        return redirect()->route('docente.seguimientos')->with('status', 'Estado actualizado.');
    }

    public function destroy(Request $request, Followup $followup)
    {
        Gate::authorize('update', $followup);
        $fields = $request->validate(['revision' => ['required', 'integer', 'min:0']]);
        $this->change($followup, $fields, true);

        return redirect()->route('docente.seguimientos')->with('status', 'Seguimiento eliminado de la lista.');
    }

    private function change(Followup $followup, array $fields, bool $delete = false): void
    {
        DB::transaction(function () use ($followup, $fields, $delete) {
            Practice::whereKey($followup->practice_id)->lockForUpdate()->firstOrFail();
            $locked = Followup::whereKey($followup->id)->lockForUpdate()->firstOrFail();
            Gate::authorize('update', $locked);
            abort_unless($locked->revision === (int) $fields['revision'], 409, 'El seguimiento cambió. Actualiza la página.');
            $fields['revision'] = $locked->revision + 1;
            $locked->update($fields);
            if ($delete) {
                $locked->delete();
            }
        });
    }

    private function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'string', 'max:100'],
            'observation' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
