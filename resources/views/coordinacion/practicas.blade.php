@extends('layouts.app')

@section('title', 'Prácticas | Coordinación Académica')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO GENERAL
        </span>

        <h1>
            Prácticas
        </h1>

        <p>
            Consulta el estado general de los procesos
            de práctica profesional actualmente registrados.
        </p>

    </div>

</div>



<section class="row g-3 mb-4">

    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-briefcase"></i>
            </div>

            <div>
                <span>Prácticas</span>
                <strong id="practiceStatTotal">{{ $practicas->count() }}</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-play-circle"></i>
            </div>

            <div>
                <span>Activas</span>
                <strong id="practiceStatActive">{{ $activas }}</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>
                <span>Día 45 alcanzado</span>
                <strong id="practiceStatVisits">{{ $umbralVisita }}</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>

            <div>
                <span>Requieren atención</span>
                <strong id="practiceStatAlerts">{{ $requierenAtencion }}</strong>
            </div>

        </div>

    </div>

</section>



<section class="content-card">

    <div class="coord-practice-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="practiceSearch"
                placeholder="Buscar estudiante, cuenta o empresa..."
            >

        </div>


        <select
            id="practiceStage"
            class="form-select"
        >
            <option value="">Todas las etapas</option>
            @foreach ($etapas as $etapa)
                <option>{{ $etapa }}</option>
            @endforeach

        </select>


        <select
            id="practiceAttention"
            class="form-select"
        >

            <option value="">
                Todos
            </option>

            <option value="attention">
                Requieren atención
            </option>

            <option value="normal">
                Sin alertas
            </option>

        </select>

    </div>


    <div id="practicesList">
        @foreach ($practicas as $practica)
            @php($requiereDocente = $practica->needsTeacher())
            <article class="coord-practice-row"
                data-search="{{ $practica->student->user->name }} {{ $practica->student->account_number }} {{ $practica->company?->official_name }}"
                data-stage="{{ $practica->stage }}"
                data-attention="{{ $requiereDocente ? 'attention' : 'normal' }}">
                <div class="coord-request-avatar">{{ Str::initials($practica->student->user->name, true) }}</div>
                <div class="coord-practice-student">
                    <span>ESTUDIANTE</span>
                    <strong>{{ $practica->student->user->name }}</strong>
                    <small>{{ $practica->student->account_number }}</small>
                </div>
                <div>
                    <span class="coord-request-label">Empresa</span>
                    <strong class="coord-request-value">{{ $practica->company?->official_name ?? 'Sin empresa asignada' }}</strong>
                </div>
                <div>
                    <span class="coord-request-label">Docente de seguimiento</span>
                    <strong class="coord-request-value">{{ $practica->teacher?->user?->name ?? 'Sin docente asignado' }}</strong>
                </div>
                <div>
                    <span class="coord-request-label">Etapa · Estado</span>
                    <strong class="coord-request-value">{{ $practica->stage }} · {{ $practica->statusLabel() }}</strong>
                </div>
                <div>
                    <span class="coord-request-label">Día</span>
                    <strong class="coord-request-value">{{ $practica->currentDay() ?? '—' }}</strong>
                </div>
                <div class="coord-practice-progress">
                    <span>Progreso</span>
                    <strong>{{ $practica->progress }}%</strong>
                    <div><span style="width: {{ $practica->progress }}%"></span></div>
                </div>
                <div>
                    @if ($requiereDocente)
                        <span class="coord-practice-alert danger">Sin docente asignado</span>
                    @elseif ($practica->reachedVisitDay())
                        <span class="coord-practice-alert normal">Día 45 alcanzado</span>
                    @else
                        <span class="coord-practice-alert normal">Sin alertas</span>
                    @endif
                </div>
                <a href="{{ route('coordinacion.practica', $practica) }}" class="coord-teacher-view">
                    <i class="bi bi-eye"></i> Ver
                </a>
            </article>
        @endforeach
        <div id="noPractices" class="teacher-load-empty {{ $practicas->isEmpty() ? '' : 'd-none' }}">
            <i class="bi bi-search"></i>
            <strong>No se encontraron prácticas</strong>
            <span>Intenta modificar los filtros de búsqueda.</span>
        </div>
    </div>

</section>

@endsection



@push('scripts')
<script>
    const practiceSearch = document.getElementById('practiceSearch');
    const practiceStage = document.getElementById('practiceStage');
    const practiceAttention = document.getElementById('practiceAttention');
    const practiceRows = Array.from(document.querySelectorAll('#practicesList .coord-practice-row'));

    function filterPractices() {
        const search = practiceSearch.value.trim().toLocaleLowerCase();
        const stage = practiceStage.value;
        const attention = practiceAttention.value;
        let visible = 0;

        for (const row of practiceRows) {
            const matches = row.dataset.search.toLocaleLowerCase().includes(search)
                && (!stage || row.dataset.stage === stage)
                && (!attention || row.dataset.attention === attention);

            row.classList.toggle('d-none', !matches);
            if (matches) visible++;
        }

        document.getElementById('noPractices').classList.toggle('d-none', visible > 0);
    }

    practiceSearch.addEventListener('input', filterPractices);
    practiceStage.addEventListener('change', filterPractices);
    practiceAttention.addEventListener('change', filterPractices);
</script>
@endpush
