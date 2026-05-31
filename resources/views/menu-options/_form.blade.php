@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Titulo</label>
        <input name="title" class="form-control" value="{{ old('title', $option->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Orden</label>
        <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $option->sort_order) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Accion</label>
        <select name="action" class="form-select" required>
            <option value="ia" @selected(old('action', $option->action) === 'ia')>Responder con IA simulada</option>
            <option value="derivacion" @selected(old('action', $option->action) === 'derivacion')>Derivar a area</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Area de derivacion opcional</label>
        <select name="derivation_area_id" class="form-select">
            <option value="">Sin area fija</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" @selected((string) old('derivation_area_id', $option->derivation_area_id) === (string) $area->id)>{{ $area->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="option_active" @checked(old('is_active', $option->is_active))>
            <label class="form-check-label" for="option_active">Activo</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Descripcion</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $option->description) }}</textarea>
    </div>
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('menu-options.index') }}">Cancelar</a>
