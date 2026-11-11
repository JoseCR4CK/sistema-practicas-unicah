<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use Illuminate\Http\Request;

class CompanyPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        // Consulta general ya presente en la interfaz; no concede gestión académica.
        $practices = Practice::whereHas('companyResponsible', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['student.user', 'company', 'companyResponsible', 'visits'])->latest('id')->get();

        return view('jefe_inmediato.dashboard', ['practices' => $practices]);
    }
}
