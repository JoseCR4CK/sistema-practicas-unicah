@extends('layouts.app')

@section('title', 'Expediente del estudiante | Coordinación Académica')

@section('content')

@php
    $etapas = \App\Models\Practice::STAGES;
    $iconosEtapas = [
        'bi-check-lg',
        'bi-check-lg',
        'bi-check-lg',
        'bi-briefcase',
        'bi-calendar-check',
        'bi-journal-text',
        'bi-mortarboard',
        'bi-flag',
    ];
    $estadosCompletados = [
        $estudiante['solicitud_estado'],
        $estudiante['documentos_estado'],
        $estudiante['empresa_id'] !== null ? 'Asignada' : 'Sin asignar',
        'Completada',
        'Realizada',
        $closing['monograph_state'],
        $closing['defense_state'],
        'Completada',
    ];
    $indiceActual = array_search($estudiante['etapa'], $etapas, true);
    $finalizado = $estudiante['etapa'] === 'Finalización';
    $pasosTimeline = [];

    foreach ($etapas as $indice => $nombreEtapa) {
        if ($finalizado || ($indiceActual !== false && $indice < $indiceActual)) {
            $pasosTimeline[] = [
                'nombre' => $nombreEtapa,
                'icono' => $iconosEtapas[$indice],
                'clase' => 'completed',
                'estado' => $estadosCompletados[$indice],
            ];
        } elseif ($indice === $indiceActual) {
            $estadoActual = 'Actual';

            if ($nombreEtapa === 'Práctica') {
                $estadoActual = $estudiante['estado'];
            } elseif ($nombreEtapa === 'Monografía') {
                $estadoActual = $closing['monograph_state'];
            }

            $pasosTimeline[] = [
                'nombre' => $nombreEtapa,
                'icono' => $iconosEtapas[$indice],
                'clase' => 'active',
                'estado' => $estadoActual,
            ];
        } else {
            $estadoPendiente = 'Pendiente';

            if ($nombreEtapa === 'Visita' && $estudiante['etapa'] === 'Práctica') {
                $estadoPendiente = $estudiante['visita_estado'];
            }

            $pasosTimeline[] = [
                'nombre' => $nombreEtapa,
                'icono' => $iconosEtapas[$indice],
                'clase' => 'pending',
                'estado' => $estadoPendiente,
            ];
        }
    }
    foreach ($pasosTimeline as &$paso) {
        $estadoPersistente = match ($paso['nombre']) {
            'Visita' => $estudiante['visita_estado'],
            'Monografía' => $closing['monograph_state'],
            'Defensa' => $closing['defense_state'],
            default => null,
        };
        if ($estadoPersistente !== null) {
            $paso['estado'] = $estadoPersistente;
            $paso['clase'] = in_array($estadoPersistente, ['Realizada', 'Aprobada'], true) ? 'completed'
                : (in_array($estadoPersistente, ['Habilitada', 'Programada', 'En revisión', 'Requiere corrección', 'En coordinación'], true) ? 'active' : 'pending');
        }
    }
    unset($paso);
@endphp

<!-- CABECERA -->

<div class="page-header expediente-page-header">

    <div>

        <a
            href="{{ route('coordinacion.estudiantes') }}"
            class="back-link"
        >
            <i class="bi bi-arrow-left"></i>
            Volver a estudiantes
        </a>

        <h1>
            Expediente del estudiante
        </h1>

        <p>
            Seguimiento completo del proceso de práctica profesional.
        </p>

    </div>

    <div class="expediente-actions">

        <button class="secondary-button">
            <i class="bi bi-printer"></i>
            Imprimir
        </button>

        <button class="primary-button">
            <i class="bi bi-pencil"></i>
            Editar información
        </button>

    </div>

</div>


<!-- INFORMACIÓN DEL ESTUDIANTE -->

<section class="student-profile-card">

    <div class="student-profile-main">

        <div class="student-profile-avatar">
            {{ $estudiante['iniciales'] }}
        </div>

        <div class="student-profile-info">

            <div class="student-profile-title">

                <h2>
                    {{ $estudiante['nombre'] }}
                </h2>

                <span class="status-pill {{ $estudiante['estado_codigo'] === 'ACTIVE' ? 'active' : '' }}">
                    {{ $estudiante['estado'] }}
                </span>

            </div>

            <p>
                {{ $estudiante['carrera'] }}
            </p>

            <div class="student-profile-meta">

                <span>
                    <i class="bi bi-person-badge"></i>
                    {{ $estudiante['cuenta'] }}
                </span>

                <span>
                    <i class="bi bi-envelope"></i>
                    {{ $estudiante['correo'] }}
                </span>

                <span>
                    <i class="bi bi-card-text"></i>
                    {{ $estudiante['telefono'] }}
                </span>

            </div>

        </div>

    </div>

    <div class="student-profile-side">

        <span>
            Etapa actual
        </span>

        <strong>
            {{ $estudiante['etapa'] }}
        </strong>

    </div>

</section>


<!-- PROGRESO GENERAL -->

<section class="content-card progress-section">

    <div class="progress-section-header">

        <div>

            <h2>
                Progreso de la práctica
            </h2>

            <p>
                Seguimiento general de las etapas del proceso.
            </p>

        </div>

        <div class="progress-percentage">
            {{ $estudiante['progreso'] === null ? '—' : $estudiante['progreso'].'%' }}
        </div>

    </div>


    <div class="main-progress-bar">

        <div
            class="main-progress-fill"
            style="width: {{ $estudiante['progreso'] ?? 0 }}%"
        ></div>

    </div>




    @php
        $processSteps = collect($pasosTimeline)->map(fn ($step) => [
            'name' => $step['nombre'] === 'Documentación' ? 'Documentos' : $step['nombre'],
            'state' => $step['estado'],
            'icon' => $step['icono'],
            'class' => $step['clase'],
        ]);
    @endphp
    <x-practice-process :steps="$processSteps" />
</section>


<!-- INFORMACIÓN RÁPIDA -->

<section class="expediente-info-grid">


    <div class="info-card">

        <div class="info-card-icon blue">
            <i class="bi bi-buildings"></i>
        </div>

        <div>

            <span>
                Empresa
            </span>

            <strong>
                {{ $estudiante['empresa'] }}
            </strong>

        </div>

    </div>


    <div class="info-card">

        <div class="info-card-icon gold">
            <i class="bi bi-person-vcard"></i>
        </div>

        <div>

            <span>
                Jefe inmediato
            </span>

            <strong>
                {{ $estudiante['responsable'] }}
            </strong>

        </div>

    </div>


    <div class="info-card">

        <div class="info-card-icon green">
            <i class="bi bi-calendar2-check"></i>
        </div>

        <div>

            <span>
                Inicio de práctica
            </span>

            <strong>
                {{ $estudiante['inicio'] }}
            </strong>

        </div>

    </div>


    <div class="info-card">

        <div class="info-card-icon red">
            <i class="bi bi-calendar2-event"></i>
        </div>

        <div>

            <span>
                Finalización
            </span>

            <strong>
                {{ $estudiante['finalizacion'] }}
            </strong>

        </div>

    </div>

</section>


<!-- CONTROL TEMPORAL -->

<section class="practice-time-grid">


    <div class="content-card practice-time-card">

        <div class="time-card-icon">
            <i class="bi bi-clock-history"></i>
        </div>

        <div>

            <span>
                Día actual de práctica
            </span>

            <strong>
                {{ $estudiante['dia_practica'] ?? 'No iniciada' }}
            </strong>

            <p>
                Estado de visita: {{ $estudiante['visita_estado'] }}
            </p>

        </div>

    </div>


    <div class="content-card practice-time-card">

        <div class="time-card-icon gold">
            <i class="bi bi-file-earmark-arrow-up"></i>
        </div>

        <div>

            <span>Entrega de monografía</span>
            <strong>{{ $closing['deadline']?->format('d/m/Y') ?? 'Fecha final de práctica pendiente' }}</strong>

            <p>
                {{ $closing['deadline'] ? 'Una semana antes de finalizar la práctica.' : 'Fecha de entrega no disponible.' }}
            </p>

        </div>

    </div>

</section>


<!-- PESTAÑAS -->

<section class="content-card expediente-tabs-card">

    <div class="expediente-tabs">

        <button class="expediente-tab active" data-tab="resumen">
            Resumen
        </button>

        <button class="expediente-tab" data-tab="documentos">
            Documentos
        </button>

        <button class="expediente-tab" data-tab="empresa">
            Empresa
        </button>

        <button class="expediente-tab" data-tab="terna">
            Terna
        </button>

        <button class="expediente-tab" data-tab="visita">
            Visita
        </button>

        <button class="expediente-tab" data-tab="monografia">
            Monografía
        </button>

        <button class="expediente-tab" data-tab="defensa">
            Defensa
        </button>

        <button class="expediente-tab" data-tab="historial">
            Historial
        </button>

    </div>


    <!-- RESUMEN -->

    <div class="tab-content active" id="resumen">

        <div class="tab-section-title">

            <div>

                <h3>
                    Resumen del proceso
                </h3>

                <p>
                    Información principal del proceso de práctica.
                </p>

            </div>

        </div>


        <div class="summary-grid">


            <div class="summary-block">

                <span>
                    Estado de solicitud
                </span>

                <strong class="text-success">
                    {{ $estudiante['solicitud_estado'] }}
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Documentación
                </span>

                <strong class="text-success">
                    {{ $estudiante['documentos_estado'] }}
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Empresa
                </span>

                <strong>
                    {{ $estudiante['empresa'] }}
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Jefe inmediato
                </span>

                <strong>
                    {{ $estudiante['responsable'] }}
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Fecha de inicio
                </span>

                <strong>
                    {{ $estudiante['inicio'] }}
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Fecha de finalización
                </span>

                <strong>
                    {{ $estudiante['finalizacion'] }}
                </strong>

            </div>

        </div>

    </div>


    <!-- DOCUMENTOS -->

    <div class="tab-content" id="documentos">
        @include('academic.document-list', ['documentos' => $estudiante['documentos'], 'studentPortal' => false])
    </div>

    <!-- EMPRESA -->

    <div class="tab-content" id="empresa">

        <div class="tab-section-title">

            <div>

                <h3>
                    Empresa y jefe inmediato
                </h3>

                <p>
                    Información del lugar donde se desarrolla la práctica.
                </p>

            </div>

        </div>


        <div class="summary-grid">

            <div class="summary-block">
                <span>Empresa</span>
                <strong>{{ $estudiante['empresa'] }}</strong>
            </div>

            <div class="summary-block">
                <span>RTN</span>
                <strong>No disponible</strong>
            </div>

            <div class="summary-block">
                <span>Jefe inmediato</span>
                <strong>{{ $estudiante['responsable'] }}</strong>
            </div>

            <div class="summary-block">
                <span>Docente de seguimiento</span>
                <strong>{{ $estudiante['docente'] }}</strong>
            </div>

            <div class="summary-block">
                <span>Teléfono</span>
                <strong>No disponible</strong>
            </div>

            <div class="summary-block">
                <span>Correo</span>
                <strong>No disponible</strong>
            </div>

        </div>

    </div>


    <!-- TERNA -->

    <div class="tab-content" id="terna">
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h3>{{ $closing['committee_state'] }}</h3>
            @if ($closing['committee'])
                @include('academic.committees.members', ['members' => $closing['committee']->members])
                <a href="{{ route('coordinacion.terna.show', $closing['committee']) }}">Ver terna</a>
            @endif
        </div>
    </div>

    <!-- VISITA -->

    <div class="tab-content" id="visita">

        <div class="empty-state">

            <i class="bi bi-calendar-check"></i>

            <h3>
                @if ($estudiante['visita_estado'] === 'Realizada')
                    Visita realizada
                @elseif ($estudiante['visita_estado'] === 'Habilitada')
                    Visita habilitada
                @else
                    Visita {{ strtolower($estudiante['visita_estado']) }}
                @endif
            </h3>

            <p>
                Estado: {{ $estudiante['visita_estado'] }}
                @if ($estudiante['visita_id'])
                    <a href="{{ route('coordinacion.visita', $estudiante['visita_id']) }}">Ver visita</a>
                @endif
            </p>

            <span class="countdown-badge">
                {{ $estudiante['visita_estado'] }}
            </span>

        </div>

    </div>


    <!-- MONOGRAFÍA -->

    <div class="tab-content" id="monografia">

        <div class="empty-state">

            <i class="bi bi-journal-text"></i>

            <h3>
                Monografía {{ strtolower($closing['monograph_state']) }}
            </h3>

            <p>
                Estado: {{ $closing['monograph_state'] }}
                @if ($closing['monograph'])
                    <a href="{{ route('coordinacion.monografia.show', $closing['monograph']) }}">Ver monografía</a>
                @endif
            </p>

        </div>

    </div>


    <!-- DEFENSA -->

    <div class="tab-content" id="defensa">

        <div class="empty-state">

            <i class="bi bi-mortarboard"></i>

            <h3>
                Defensa {{ strtolower($closing['defense_state']) }}
            </h3>

            <p>
                Estado: {{ $closing['defense_state'] }}
                @if ($closing['defense'])
                    <a href="{{ route('coordinacion.defensa.show', $closing['defense']) }}">Ver defensa</a>
                @endif
            </p>

        </div>

    </div>


    <!-- HISTORIAL -->

    <div class="tab-content" id="historial">

        <div class="history-list">


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        {{ $estudiante['inicio'] === 'Pendiente' ? 'Inicio de práctica pendiente' : 'Inicio de práctica' }}
                    </strong>

                    <span>
                        {{ $estudiante['inicio'] }}
                    </span>

                </div>

            </div>


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        {{ $estudiante['documentos_estado'] }}
                    </strong>

                    <span>
                        Fecha no disponible
                    </span>

                </div>

            </div>


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        {{ $estudiante['empresa_id'] !== null ? 'Empresa asignada' : 'Sin empresa asignada' }}
                    </strong>

                    <span>
                        Fecha no disponible
                    </span>

                </div>

            </div>


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        {{ $estudiante['solicitud_estado'] }}
                    </strong>

                    <span>
                        Fecha no disponible
                    </span>

                </div>

            </div>


        </div>

    </div>


</section>


@endsection


@push('scripts')

<script>

    const tabs =
        document.querySelectorAll('.expediente-tab');

    const contents =
        document.querySelectorAll('.tab-content');


    tabs.forEach(tab => {

        tab.addEventListener('click', function () {

            tabs.forEach(item =>
                item.classList.remove('active')
            );

            contents.forEach(content =>
                content.classList.remove('active')
            );

            this.classList.add('active');

            document
                .getElementById(this.dataset.tab)
                .classList.add('active');

        });

    });

</script>

@endpush
