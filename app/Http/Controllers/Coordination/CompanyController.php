<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyResponsible;
use App\Models\Practice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        return view('coordinacion.empresas', [
            'empresas' => Company::withCount([
                'responsibles',
                'practices as students_count' => fn ($query) => $query->select(DB::raw('count(distinct student_id)')),
            ])->orderBy('official_name')->get(),
            'practicantes' => Practice::whereNotNull('company_id')->distinct()->count('student_id'),
        ]);
    }

    public function show(Company $company): View
    {
        $company->load(['responsibles', 'practices.student.user', 'practices.companyResponsible', 'practices.teacher.user']);

        return view('coordinacion.empresa-detalle', [
            'empresa' => $company,
            'asignaciones' => $company->practices->groupBy('company_responsible_id')
                ->map(fn ($practices) => $practices->unique('student_id')->count()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Company::create($this->companyFields($request));

        return redirect()->route('coordinacion.empresas')->with('status', 'Empresa registrada.');
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $company->update($this->companyFields($request, $company));

        return redirect()->route('coordinacion.empresas')->with('status', 'Empresa actualizada.');
    }

    public function storeResponsible(Request $request, Company $company): RedirectResponse
    {
        $company->responsibles()->create($this->responsibleFields($request));

        return redirect()->route('coordinacion.empresa', $company)->with('status', 'Responsable registrado.');
    }

    public function updateResponsible(Request $request, Company $company, CompanyResponsible $responsible): RedirectResponse
    {
        $responsible->update($this->responsibleFields($request));

        return redirect()->route('coordinacion.empresa', $company)->with('status', 'Responsable actualizado.');
    }

    private function companyFields(Request $request, ?Company $company = null): array
    {
        return $request->validate(\App\Validation\CompanyRules::fields($company));
    }

    private function responsibleFields(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);
    }
}
