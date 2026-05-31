@csrf
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nombre</label><input name="name" class="form-control" value="{{ old('name', $intention->name) }}" required></div>
    <div class="col-md-6"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug', $intention->slug) }}" placeholder="se genera si queda vacio"></div>
    <div class="col-md-3"><label class="form-label">Color</label><input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $intention->color ?: '#6c757d') }}"></div>
    <div class="col-md-3"><label class="form-label">Icono</label><input name="icon" class="form-control" value="{{ old('icon', $intention->icon) }}" placeholder="tag, cart, shield"></div>
    <div class="col-md-3"><label class="form-label">Prioridad</label><input type="number" min="0" name="priority" class="form-control" value="{{ old('priority', $intention->priority) }}" required></div>
    <div class="col-md-3"><label class="form-label">Confianza minima</label><input type="number" min="0" max="1" step="0.01" name="minimum_confidence" class="form-control" value="{{ old('minimum_confidence', $intention->minimum_confidence) }}" required></div>
    <div class="col-md-6"><label class="form-label">Accion por defecto</label><select name="default_action" class="form-select" required><option value="responder_ia" @selected(old('default_action', $intention->default_action) === 'responder_ia')>Responder IA</option><option value="derivar" @selected(old('default_action', $intention->default_action) === 'derivar')>Derivar</option><option value="responder_y_derivar" @selected(old('default_action', $intention->default_action) === 'responder_y_derivar')>Responder y derivar</option></select></div>
    <div class="col-md-6"><label class="form-label">Area de derivacion opcional</label><select name="derivation_area_id" class="form-select"><option value="">Sin area</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(old('derivation_area_id', $intention->derivation_area_id) == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Palabras clave</label><textarea name="keywords" rows="3" class="form-control">{{ old('keywords', $intention->keywords) }}</textarea></div>
    <div class="col-md-6"><label class="form-label">Prompt especifico opcional</label><textarea name="specific_prompt" rows="3" class="form-control">{{ old('specific_prompt', $intention->specific_prompt) }}</textarea></div>
    <div class="col-12"><label class="form-label">Descripcion</label><textarea name="description" rows="3" class="form-control">{{ old('description', $intention->description) }}</textarea></div>
    <div class="col-12"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="intention_active" @checked(old('is_active', $intention->is_active))><label class="form-check-label" for="intention_active">Activo</label></div></div>
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a href="{{ route('intentions.index') }}" class="btn btn-link mt-4">Cancelar</a>
