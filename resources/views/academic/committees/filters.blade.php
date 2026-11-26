<form method="GET" class="content-card p-3 mb-4">
<div class="row g-3">
<div class="col-md">
<label class="form-label" for="faculty">Facultad/Área</label>
<select class="form-select" id="faculty" name="faculty">
<option value="">Todas</option>
@foreach($faculties as $faculty)<option value="{{ $faculty->id }}" @selected(($filters['faculty'] ?? '') == $faculty->id)>{{ $faculty->name }}</option>
@endforeach</select>
</div>
<div class="col-md">
<label class="form-label" for="period">Período de origen</label>
<select class="form-select" id="period" name="period">
<option value="">Todos</option>
@foreach(\App\Models\AcademicLoad::PERIODS as $key => $label)<option value="{{ $key }}" @selected(($filters['period'] ?? '') === $key)>{{ $label }}</option>
@endforeach</select>
</div>
<div class="col-md">
<label class="form-label" for="year">Año de origen</label>
<input class="form-control" id="year" name="year" type="number" min="1900" max="2200" value="{{ $filters['year'] ?? '' }}">
</div>
<div class="col-md">
<label class="form-label" for="status">Estado</label>
<select class="form-select" id="status" name="status">
<option value="">Todos</option>
@foreach(['pending'=>'Pendiente de asignación','active'=>'Activa','closed'=>'Cerrada'] as $key=>$label)
@if(!($workload ?? false) || $key !== 'pending')<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
@endif
@endforeach</select>
</div>
<div class="col-md">
<label class="form-label" for="search">{{ ($workload ?? false) ? 'Docente' : 'Nombre, DNI o cuenta' }}</label>
<input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" maxlength="255">
</div>
<div class="col-md-auto d-flex align-items-end">
<button class="btn btn-primary">Filtrar</button>
</div>
</div>
</form>
