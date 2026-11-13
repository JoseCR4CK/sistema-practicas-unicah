@php
    $prefix = 'load'.($load?->id ?? 'new');
    $restore = (string) old('form_id') === (string) ($load?->id ?? 'new');
    $value = fn ($field, $default = '') => $restore ? old($field, $default) : ($load?->$field ?? $default);
    $selectedCareer = $careers->firstWhere('id', (int) $value('academic_career_id'));
    $selectedSubject = $selectedCareer?->subjects->firstWhere('id', (int) $value('academic_subject_id'));
@endphp
<input type="hidden" name="form_id" value="{{ $load?->id ?? 'new' }}">
<div class="row g-3" data-academic-catalog>
    <div class="col-md-6"><label for="{{ $prefix }}Year" class="form-label">Año académico</label><input id="{{ $prefix }}Year" name="academic_year" type="number" min="1900" max="2200" class="form-control" required value="{{ $value('academic_year', now()->year) }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Period" class="form-label">Período académico</label><select id="{{ $prefix }}Period" name="academic_period" class="form-select" required><option value="">Seleccione un período</option>@foreach (\App\Models\AcademicLoad::PERIODS as $key => $label)<option value="{{ $key }}" @selected(\App\Models\AcademicLoad::periodKey($value('academic_period')) === $key)>{{ $label }}</option>@endforeach</select></div>
    @if ($load && !$selectedSubject)
        <div class="col-12"><div class="alert alert-warning">{{ $load->academic_subject_id ? 'La asignatura guardada no está disponible en el catálogo activo.' : 'Pendiente de asociación al catálogo.' }} Datos históricos conservados: {{ $load->career ?? 'Sin carrera' }} · {{ $load->subject }} · Créditos: {{ $load->credits ?? 'Sin registrar' }} · Horas semanales: {{ $load->weekly_class_hours ?? 'Sin registrar' }}. Para corregir, selecciona una carrera y asignatura confirmadas.</div></div>
    @endif
    <div class="col-md-6"><label for="{{ $prefix }}Career" class="form-label">Carrera</label><select id="{{ $prefix }}Career" name="academic_career_id" class="form-select" data-catalog-career required><option value="">Seleccione una carrera</option>@foreach ($careers as $career)<option value="{{ $career->id }}" @selected((int) $value('academic_career_id') === $career->id)>{{ $career->name }}</option>@endforeach</select></div>
    <div class="col-md-6"><label for="{{ $prefix }}Subject" class="form-label">Asignatura</label><select id="{{ $prefix }}Subject" name="academic_subject_id" class="form-select" data-catalog-subject data-selected-subject="{{ $value('academic_subject_id') }}" required @disabled(!$selectedCareer || $selectedCareer->subjects->isEmpty())><option value="">{{ $selectedCareer ? 'Seleccione una asignatura' : 'Seleccione primero una carrera' }}</option>@foreach ($selectedCareer?->subjects ?? collect() as $subject)<option value="{{ $subject->id }}" @selected($selectedSubject?->id === $subject->id) @disabled($subject->confirmedWeeklyHours() === null)>{{ $subject->code ? $subject->code.' · ' : '' }}{{ $subject->name }}{{ $subject->confirmedWeeklyHours() === null ? ' · Información académica pendiente' : '' }}</option>@endforeach</select><small class="text-muted" data-catalog-message aria-live="polite"></small></div>
    <div class="col-md-6"><label for="{{ $prefix }}Section" class="form-label">Sección (opcional)</label><input id="{{ $prefix }}Section" name="section" maxlength="80" class="form-control" value="{{ $value('section') }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Status" class="form-label">Estado</label><select id="{{ $prefix }}Status" name="status" class="form-select"><option value="ACTIVE" @selected($value('status', 'ACTIVE') === 'ACTIVE')>Activa</option><option value="INACTIVE" @selected($value('status') === 'INACTIVE')>Inactiva</option></select></div>
    <fieldset class="col-12"><legend class="form-label">Días de clase</legend><div class="teacher-load-days">
        @foreach (\App\Models\AcademicLoad::DAYS as $number => $name)
            <label><input name="weekdays[]" type="checkbox" value="{{ $number }}" @checked(in_array($number, array_map('intval', $value('weekdays', []))))><span>{{ $name }}</span></label>
        @endforeach
    </div></fieldset>
    <div class="col-md-6"><label for="{{ $prefix }}Start" class="form-label">Hora de inicio</label><input id="{{ $prefix }}Start" name="starts_at" type="time" class="form-control" required value="{{ substr($value('starts_at'), 0, 5) }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}End" class="form-label">Hora de finalización</label><input id="{{ $prefix }}End" name="ends_at" type="time" max="21:50" class="form-control" required value="{{ substr($value('ends_at'), 0, 5) }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}From" class="form-label">Clases desde</label><input id="{{ $prefix }}From" name="valid_from" type="date" class="form-control" required value="{{ $restore ? old('valid_from') : $load?->valid_from?->format('Y-m-d') }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Until" class="form-label">Clases hasta</label><input id="{{ $prefix }}Until" name="valid_until" type="date" class="form-control" required value="{{ $restore ? old('valid_until') : $load?->valid_until?->format('Y-m-d') }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Credits" class="form-label">Créditos</label><input id="{{ $prefix }}Credits" class="form-control" data-catalog-credits readonly value="{{ $selectedSubject?->credits }}" placeholder="Se obtienen de la asignatura"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Hours" class="form-label">Horas de clase semanales</label><input id="{{ $prefix }}Hours" class="form-control" data-catalog-hours readonly value="{{ $selectedSubject?->confirmedWeeklyHours() }}" placeholder="Pendientes de selección"></div>
    <div class="col-12"><p class="text-muted mb-0" data-catalog-budget aria-live="polite">Selecciona la asignatura, los días y el horario para comprobar los minutos semanales.</p></div>
    <div class="col-12"><div class="teacher-load-rule"><i class="bi bi-info-circle"></i><span>Registra la vigencia real de las clases. Una hora clase equivale a 50 minutos. El horario puede ser parcial, pero no exceder el máximo confirmado. La última hora académica conocida termina a las 9:50 p. m.</span></div></div>
    @if ($manager && $load)
        <div class="col-12"><label for="{{ $prefix }}Reason" class="form-label">Motivo de la corrección</label><textarea id="{{ $prefix }}Reason" name="reason" class="form-control" maxlength="5000" required>{{ $restore ? old('reason') : '' }}</textarea></div>
    @endif
</div>
