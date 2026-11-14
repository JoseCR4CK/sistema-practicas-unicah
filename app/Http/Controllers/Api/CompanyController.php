<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Validation\CompanyRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Company::orderBy('official_name')->orderBy('id')->get()]);
    }

    public function show(Company $company): JsonResponse
    {
        return response()->json(['data' => $company]);
    }

    public function store(Request $request): JsonResponse
    {
        $company = Company::create($request->validate(CompanyRules::fields()));

        return response()->json(['data' => $company->fresh()], 201)
            ->header('Location', route('api.companies.show', $company));
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $company->update($request->validate(CompanyRules::fields($company, $request->isMethod('patch'))));

        return response()->json(['data' => $company->fresh()]);
    }

    public function destroy(Company $company): Response
    {
        DB::transaction(function () use ($company): void {
            $locked = Company::lockForUpdate()->findOrFail($company->id);
            // El esquema usa cascada y SET NULL: conservar todos los vínculos existentes.
            abort_if($locked->responsibles()->exists() || $locked->practices()->exists(),
                409, 'La empresa tiene responsables o prácticas vinculadas y no puede eliminarse.');
            $locked->delete();
        });

        return response()->noContent();
    }
}
