@php
    $prefix = 'load'.($load?->id ?? 'new');
    $restore = (string) old('form_id') === (string) ($load?->id ?? 'new');
    $value = fn ($field, $default = '') => $restore ? old($field, $default) : ($load?->$field ?? $default);
@endphp
<input type="hidden" name="form_id" value="{{ $load?->id ?? 'new' }}">
<div class="row g-3">
    <div class="col-md-6"><label for="{{ $prefix }}Year" class="form-label">Año académico</label><input id="{{ $prefix }}Year" name="academic_year" type="number" min="1900" max="2200" class="form-control" required value="{{ $value('academic_year', now()->year) }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Period" class="form-label">Período académico</label><input id="{{ $prefix }}Period" name="academic_period" maxlength="80" class="form-control" required value="{{ $value('academic_period') }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Subject" class="form-label">Asignatura</label><input id="{{ $prefix }}Subject" name="subject" maxlength="255" class="form-control" required value="{{ $value('subject') }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Career" class="form-label">Carrera (opcional)</label><input id="{{ $prefix }}Career" name="career" maxlength="255" class="form-control" value="{{ $value('career') }}"></div>
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
    <div class="col-md-6"><label for="{{ $prefix }}Credits" class="form-label">Créditos (opcional)</label><input id="{{ $prefix }}Credits" name="credits" type="number" step="0.01" min="0" max="9999.99" class="form-control" value="{{ $value('credits') }}"></div>
    <div class="col-md-6"><label for="{{ $prefix }}Hours" class="form-label">Horas de clase semanales (opcional)</label><input id="{{ $prefix }}Hours" name="weekly_class_hours" type="number" step="0.01" min="0" max="9999.99" class="form-control" value="{{ $value('weekly_class_hours') }}"></div>
    <div class="col-12"><div class="teacher-load-rule"><i class="bi bi-info-circle"></i><span>Registra la vigencia real de las clases. La última hora académica conocida termina a las 9:50 p. m. Créditos y horas se conservan independientemente.</span></div></div>
    @if ($manager && $load)
        <div class="col-12"><label for="{{ $prefix }}Reason" class="form-label">Motivo de la corrección</label><textarea id="{{ $prefix }}Reason" name="reason" class="form-control" maxlength="5000" required>{{ $restore ? old('reason') : '' }}</textarea></div>
    @endif
</div>
