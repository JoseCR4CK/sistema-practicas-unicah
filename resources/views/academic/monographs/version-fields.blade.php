<label for="monographTitle" class="form-label">Título</label>
<input id="monographTitle" name="title" class="form-control mb-3" required maxlength="255" value="{{ old('title', $version?->title) }}">
<label for="monographObservation" class="form-label">Observaciones de la versión</label>
<textarea id="monographObservation" name="observation" class="form-control mb-3" maxlength="5000">{{ old('observation') }}</textarea>
