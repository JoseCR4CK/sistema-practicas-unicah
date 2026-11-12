@extends('layouts.app')

@section('title', 'Detalle de práctica | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO DE PRÁCTICA
        </span>

        <h1>{{ $practica->student->user->name }}</h1>

        <p>
            Consulta el estado general y avance
            del proceso de práctica profesional.
        </p>

    </div>


    <div class="d-flex gap-2">

        <a
            href="{{ route('coordinacion.practicas') }}"
            class="btn btn-light coord-back-button"
        >
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>


        <a
            href="{{ route('coordinacion.estudiante', $practica->student) }}"
            class="btn btn-primary coord-back-button"
        >
            <i class="bi bi-folder2-open"></i>
            Ver expediente
        </a>

    </div>

</div>



<div id="practiceDetailContent">


    {{-- RESUMEN PRINCIPAL --}}

    <section class="coord-practice-detail-profile mb-4">

        <div class="coord-practice-detail-avatar">{{ Str::initials($practica->student->user->name, true) }}</div>


        <div>

            <span>ESTUDIANTE</span>

            <strong>{{ $practica->student->user->name }}</strong>

            <small>{{ $practica->student->account_number }}</small>
            <small>{{ $practica->academic_period }} {{ $practica->academic_year }}</small>

        </div>


        <div class="coord-practice-detail-summary">

            <div>
                <span>Etapa actual</span>
                <strong>{{ $practica->stage }} · {{ $practica->statusLabel() }}</strong>
            </div>

            <div>
                <span>Día de práctica</span>
                <strong>{{ $practica->currentDay() ?? '—' }}</strong>
            </div>

            <div>
                <span>Progreso</span>
                <strong>{{ $practica->progress }}%</strong>
            </div>

        </div>

    </section>



    @if ($practica->needsTeacher() || $practica->reachedVisitDay())
        <div class="coord-practice-detail-alert {{ $practica->needsTeacher() ? 'danger' : 'warning' }}">
            <i class="bi bi-exclamation-circle"></i>
            <div>
                @if ($practica->needsTeacher())
                    <strong>Docente de seguimiento pendiente de asignación</strong>
                    <span>La práctica ya inició y todavía no tiene docente asignado.</span>
                @endif
                @if ($practica->reachedVisitDay())
                    <strong>Día 45 alcanzado</strong>
                    <span>La visita de seguimiento puede realizarse a partir del día 45.</span>
                @endif
            </div>
        </div>
    @endif

    <section class="row g-3 mb-4">

        <div class="col-12 col-xl-4">

            <div class="content-card h-100">

                <div class="card-header">

                    <div>
                        <h2>Empresa</h2>
                    </div>

                </div>


                <div class="coord-practice-info-card">

                    <i class="bi bi-buildings"></i>

                    <div>

                        <span>
                            Empresa
                        </span>

                        <strong>
                            @if ($practica->company)
                                <a href="{{ route('coordinacion.empresa', $practica->company) }}">{{ $practica->company->official_name }}</a>
                            @else
                                Sin empresa asignada
                            @endif
                        </strong>

                        <small>{{ $practica->companyResponsible?->full_name ?? 'Sin responsable asignado' }}</small>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="content-card h-100">

                <div class="card-header">

                    <div>
                        <h2>Docente de seguimiento</h2>
                    </div>

                </div>


                <div class="coord-practice-info-card">

                    <i class="bi bi-person-video3"></i>

                    <div>

                        <span>
                            Docente de seguimiento
                        </span>

                        <strong>
                            @if ($practica->teacher)
                                <a href="{{ route('coordinacion.docente', $practica->teacher) }}">{{ $practica->teacher->user->name }}</a>
                            @else
                                Sin docente asignado
                            @endif
                        </strong>

                        <small>
                            Seguimiento académico
                        </small>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="content-card h-100">

                <div class="card-header">

                    <div>
                        <h2>Duración</h2>
                    </div>

                </div>


                <div class="coord-practice-dates">

                    <div>
                        <span>Inicio</span>
                        <strong>{{ $practica->start_date?->format('d/m/Y') ?? 'Pendiente' }}</strong>
                    </div>

                    <i class="bi bi-arrow-right"></i>

                    <div>
                        <span>Finalización</span>
                        <strong>{{ $practica->end_date?->format('d/m/Y') ?? 'Pendiente' }}</strong>
                    </div>

                </div>

                <div class="coord-practice-dates">
                    <div>
                        <span>Horas completadas</span>
                        <strong>{{ $practica->completed_hours ?? 'Sin registro' }}</strong>
                    </div>
                    <div>
                        <span>Horas requeridas</span>
                        <strong>{{ $practica->required_hours ?? 'Sin registro' }}</strong>
                    </div>
                </div>

            </div>

        </div>

    </section>



    {{-- PROGRESO --}}

    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Avance general
                </h2>

                <p>
                    Vista general de las etapas conocidas
                    del proceso de práctica profesional.
                </p>

            </div>

        </div>


        <div class="coord-practice-main-progress">

            <div class="coord-practice-main-progress-header">

                <span>
                    Progreso del proceso
                </span>

                <strong>{{ $practica->progress }}%</strong>

            </div>


            <div class="coord-practice-main-progress-bar">

                <span style="width: {{ $practica->progress }}%"></span>

            </div>

        </div>


        @php
            $processSteps = [];
        @endphp
            @foreach ($etapas as $indice => $etapa)
                @php
                    $estadoPersistente = match ($etapa) {
                        'Visita' => $practica->visits->sortByDesc('id')->first()?->effectiveStatus() ?? 'Sin visita registrada',
                        'Monografía' => $closing['monograph_state'],
                        'Defensa' => $closing['defense_state'],
                        default => null,
                    };
                    $completada = $estadoPersistente !== null ? in_array($estadoPersistente, ['Realizada', 'Aprobada'], true) : ($indiceEtapa !== false && $indice < $indiceEtapa);
                    $actual = $estadoPersistente !== null ? in_array($estadoPersistente, ['Habilitada', 'Programada', 'En revisión', 'Requiere corrección', 'En coordinación'], true) : $indice === $indiceEtapa;
                    $processSteps[] = [
                        'name' => $etapa === 'Documentación' ? 'Documentos' : $etapa,
                        'state' => $estadoPersistente ?? ($actual ? 'Actual' : ($completada ? 'Etapa anterior' : 'Pendiente')),
                        'icon' => match ($etapa) {
                            'Solicitud' => 'bi-file-earmark-text', 'Documentación' => 'bi-files',
                            'Empresa' => 'bi-buildings', 'Práctica' => 'bi-briefcase',
                            'Visita' => 'bi-calendar-check', 'Monografía' => 'bi-journal-text',
                            'Defensa' => 'bi-mortarboard', default => 'bi-flag',
                        },
                        'class' => trim(($completada ? 'completed' : '').' '.($actual ? 'active' : '')),
                    ];
                @endphp
            @endforeach
        <x-practice-process :steps="$processSteps" />
</section>



    {{-- ESTADOS DE SEGUIMIENTO --}}

    <section class="row g-3 mb-4">

        <div class="col-12 col-md-6">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Visita</h2>
                    </div>
                </div>

                <div class="coord-practice-status-box">

                    <i class="bi bi-calendar-check"></i>

                    <div>
                        <span>Estado</span>
                        <strong>{{ $practica->visits->sortByDesc('id')->first()?->effectiveStatus() ?? 'Sin visita registrada' }}</strong>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-md-6">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Monografía</h2>
                    </div>
                </div>

                <div class="coord-practice-status-box">

                    <i class="bi bi-journal-text"></i>

                    <div>
                        <span>Estado</span>
                        <strong>{{ $closing['monograph_state'] }}</strong>
                        @if ($closing['monograph'])
                            <a href="{{ route('coordinacion.monografia.show', $closing['monograph']) }}">Ver monografía</a>
                        @endif
                    </div>

                </div>

            </div>

        </div>

    </section>



    {{-- OBSERVACIÓN --}}

    <section class="content-card">

        <div class="card-header">

            <div>
                <h2>Observaciones</h2>
            </div>

        </div>


        <div class="coord-request-observation">

            <p>Consulta las observaciones registradas en cada solicitud, documento y actividad del proceso.</p>

        </div>

    </section>

</div>

@endsection
