@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input name="name" class="form-control" value="{{ old('name', $area->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Numero de WhatsApp</label>
        <input name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $area->whatsapp_number) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Correo opcional</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $area->email) }}">
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="area_active" @checked(old('is_active', $area->is_active))>
            <label class="form-check-label" for="area_active">Activo</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Descripcion</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $area->description) }}</textarea>
    </div>
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('derivation-areas.index') }}">Cancelar</a>
