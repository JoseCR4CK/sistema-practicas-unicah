<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Practice;
use App\Models\Student;
use App\Models\PracticeRequest;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Services\AcademicClosingSummary;

class StudentController extends Controller
{
    private const RELATIONS = [
        'user',
        'latestActivePractice.company',
        'latestActivePractice.companyResponsible',
        'latestActivePractice.teacher.user',
        'latestPractice.company',
        'latestPractice.companyResponsible',
        'latestPractice.teacher.user',
        'applications',
        'latestActivePractice.applications',
        'latestPractice.applications',
        'latestActivePractice.documents.currentVersion',
        'latestPractice.documents.currentVersion',
        'latestActivePractice.visits',
        'latestPractice.visits',
    ];

    public function dashboard(): View
    {
        return view('coordinacion.dashboard', [
            'solicitudesPendientes' => PracticeRequest::where('status', 'Pendiente')->with('student.user')->latest('submitted_at')->get(),
            'visitasProgramadas' => Visit::where('status', 'Programada')->count(),
            'visitasHabilitadas' => Visit::with('practice')->get()->filter(fn ($visit) => $visit->effectiveStatus() === 'Habilitada')->count(),
        ] + app(AcademicClosingSummary::class)->dashboardCounts());
    }

    public function index(Request $request): View
    {
        $query = Student::with(self::RELATIONS)->orderBy('id');
        $search = trim($request->string('buscar')->toString());
        $career = $request->string('carrera')->toString();
        $status = $request->string('estado')->toString();
        $stage = $request->string('etapa')->toString();

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search) {
                $query->where('account_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn (Builder $user) => $user
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%"));
            });
        }

        if ($career !== '') {
            $query->where('career', $career);
        }

        if ($status === 'sin-practica') {
            $query->doesntHave('practices');
            if ($stage !== '') {
                $query->whereRaw('1 = 0');
            }
        } elseif ($status !== '' || $stage !== '') {
            $matches = function (Builder $practice) use ($status, $stage) {
                if ($status !== '') {
                    $practice->where('status', $status);
                }
                if ($stage !== '') {
                    $practice->where('stage', $stage);
                }
            };

            $query->where(fn (Builder $query) => $query
                ->whereHas('latestActivePractice', $matches)
                ->orWhere(fn (Builder $query) => $query
                    ->doesntHave('latestActivePractice')
                    ->whereHas('latestPractice', $matches)));
        }

        return view('coordinacion.estudiantes', [
            'estudiantes' => $query->paginate(10)->withQueryString()->through($this->studentData(...)),
            'carreras' => Student::query()->distinct()->orderBy('career')->pluck('career'),
            'etapas' => Practice::query()->distinct()->orderBy('stage')->pluck('stage'),
            'estados' => Practice::query()->select('status')->distinct()->orderBy('status')->get(),
            'empresas' => Company::query()->orderBy('official_name')->pluck('official_name', 'id'),
            'periodos' => Practice::query()->select('academic_period', 'academic_year')
                ->distinct()->orderByDesc('academic_year')->orderByDesc('academic_period')->get(),
        ]);
    }

    public function show(Student $student): View
    {
        return view('coordinacion.expediente', [
            'estudiante' => $this->studentData($student->load(self::RELATIONS)),
            'closing' => app(AcademicClosingSummary::class)->forPractice($student->preferredPractice()),
        ]);
    }

    public function report(Request $request): View
    {
        $type = $request->string('tipo', 'todos')->toString();
        $period = $request->string('periodo')->toString();
        $stage = $request->string('etapa')->toString();
        $career = $request->string('carrera')->toString();
        $company = $request->string('empresa')->toString();

        $students = Student::with(self::RELATIONS)->orderBy('id')->get()
            ->map($this->studentData(...))
            ->filter(function (array $student) use ($type, $period, $stage, $career, $company) {
                if ($period !== '' && $student['periodo'] !== $period && $student['periodo_anterior'] !== $period) {
                    return false;
                }

                return match ($type) {
                    'activas' => $student['estado_codigo'] === Practice::ACTIVE,
                    'etapa' => $stage === '' || $student['etapa'] === $stage,
                    'carrera' => $career === '' || $student['carrera'] === $career,
                    'empresa' => $company === '' || (string) $student['empresa_id'] === $company || $student['empresa'] === $company,
                    default => true,
                };
            });

        return view('coordinacion.informe-estudiantes', [
            'estudiantes' => $students,
            'periodo' => $period === '' ? 'Todos los períodos' : (preg_replace('/^([IVX]+)-(\d{4})$/', '$1 Período $2', $period)),
            'tipo' => match ($type) {
                'activas' => 'Prácticas activas',
                'etapa' => 'Por etapa',
                'carrera' => 'Por carrera',
                'empresa' => 'Por empresa',
                default => 'Todos los estudiantes',
            },
        ]);
    }

    private function studentData(Student $student): array
    {
        $practice = $student->latestActivePractice ?? $student->latestPractice;
        $visit = $practice?->visits->sortByDesc('id')->first();
        $application = $practice?->applications->sortByDesc('id')->first()
            ?? $student->applications->whereNull('practice_id')->sortByDesc('id')->first();
        $documents = $practice?->documents ?? collect();

        return [
            'id' => $student->id,
            'nombre' => $student->user->name,
            'iniciales' => Str::initials($student->user->name, true),
            'cuenta' => $student->account_number,
            'correo' => $student->user->email,
            'telefono' => $student->phone ?? 'No registrado',
            'carrera' => $student->career,
            'estado' => $practice?->statusLabel() ?? 'Sin práctica',
            'estado_codigo' => $practice?->status,
            'etapa' => $practice?->stage ?? 'Sin etapa',
            'progreso' => $practice?->progress,
            'empresa' => $practice?->company?->official_name ?? 'Sin empresa asignada',
            'empresa_id' => $practice?->company_id,
            'responsable' => $practice?->companyResponsible?->full_name ?? 'Sin responsable asignado',
            'docente' => $practice?->teacher?->user?->name ?? 'Sin docente asignado',
            'inicio' => $practice?->start_date?->format('d/m/Y') ?? 'Pendiente',
            'dia_practica' => $practice?->currentDay(),
            'visita_estado' => $visit?->effectiveStatus() ?? 'Sin visita registrada',
            'visita_id' => $visit?->id,
            'solicitud_estado' => $application?->status ?? 'Sin solicitud registrada',
            'solicitud_id' => $application?->id,
            'documentos' => $documents,
            'documentos_estado' => $documents->where('currentVersion.status', 'Aprobado')->count().' aprobados de '.$documents->count().' registrados',
            'finalizacion' => $practice?->end_date?->format('d/m/Y') ?? 'Pendiente',
            'periodo' => $practice ? "$practice->academic_period $practice->academic_year" : null,
            'periodo_anterior' => $practice ? Str::before($practice->academic_period, ' ').'-'.$practice->academic_year : null,
        ];
    }
}
