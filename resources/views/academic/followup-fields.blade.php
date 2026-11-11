<label for="followupDate" class="form-label">Fecha</label>
<input id="followupDate" type="date" name="date" class="form-control mb-3" required value="{{ old('date', $seguimiento?->date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}">
<label for="followupType" class="form-label">Tipo</label>
<input id="followupType" name="type" class="form-control mb-3" required maxlength="255" list="followupTypes" value="{{ old('type', $seguimiento?->type ?? 'Seguimiento') }}">
<datalist id="followupTypes">@foreach (['Seguimiento', 'Revisión', 'Orientación', 'Comunicación', 'Otro'] as $tipo)<option>{{ $tipo }}</option>@endforeach</datalist>
<label for="followupTitle" class="form-label">Título</label>
<input id="followupTitle" name="title" class="form-control mb-3" required maxlength="255" value="{{ old('title', $seguimiento?->title) }}">
<label for="followupDescription" class="form-label">Descripción</label>
<textarea id="followupDescription" name="description" class="form-control mb-3" required maxlength="5000">{{ old('description', $seguimiento?->description) }}</textarea>
<label for="followupStatus" class="form-label">Estado</label>
<input id="followupStatus" name="status" class="form-control mb-3" required maxlength="100" list="followupStatuses" value="{{ old('status', $seguimiento?->status ?? 'Pendiente') }}">
<datalist id="followupStatuses"><option>Pendiente</option><option>Realizado</option></datalist>
<label for="followupObservation" class="form-label">Observación</label>
<textarea id="followupObservation" name="observation" class="form-control mb-3" maxlength="5000">{{ old('observation', $seguimiento?->observation) }}</textarea>
