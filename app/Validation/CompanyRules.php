<?php

namespace App\Validation;

use App\Models\Company;
use Illuminate\Validation\Rule;

class CompanyRules
{
    public static function fields(?Company $company = null, bool $partial = false): array
    {
        $presence = $partial ? ['sometimes', 'required'] : ['required'];

        return [
            'official_name' => [...$presence, 'string', 'max:255', Rule::unique('companies')->ignore($company?->id)],
            'commercial_name' => ['nullable', 'string', 'max:255'],
            'rtn' => ['nullable', 'string', 'max:255', Rule::unique('companies')->ignore($company?->id)],
            'sector' => [...$presence, 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => [...$presence, Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }
}
