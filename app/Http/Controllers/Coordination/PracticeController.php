<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use Illuminate\View\View;

class PracticeController extends Controller
{
    private const RELATIONS = ['student.user', 'company', 'companyResponsible', 'teacher.user', 'visits'];

    public function index(): View
    {
        $practices = Practice::with(self::RELATIONS)->latest('id')->get();

        return view('coordinacion.practicas', [
            'practicas' => $practices,
            'activas' => $practices->where('status', Practice::ACTIVE)->count(),
            'umbralVisita' => $practices->filter->reachedVisitDay()->count(),
            'requierenAtencion' => $practices->filter->needsTeacher()->count(),
            'etapas' => $practices->pluck('stage')->unique()->sort(),
        ]);
    }

    public function show(Practice $practice): View
    {
        return view('coordinacion.practica-detalle', [
            'practica' => $practice->load(self::RELATIONS),
            'etapas' => Practice::STAGES,
            'indiceEtapa' => array_search($practice->stage, Practice::STAGES, true),
        ]);
    }
}
