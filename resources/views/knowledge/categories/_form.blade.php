@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nombre</label>
        <input name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" @checked(old('is_active', $category->is_active))>
            <label class="form-check-label" for="active">Activo</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Descripcion</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
    </div>
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('knowledge-categories.index') }}">Cancelar</a>
