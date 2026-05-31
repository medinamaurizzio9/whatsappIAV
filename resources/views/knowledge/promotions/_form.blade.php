@csrf
<div class="row g-3">
    <div class="col-md-8"><label class="form-label">Nombre</label><input name="name" class="form-control" value="{{ old('name', $promotion->name) }}" required></div>
    <div class="col-md-2"><label class="form-label">Fecha inicio</label><input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', optional($promotion->starts_at)->format('Y-m-d')) }}"></div>
    <div class="col-md-2"><label class="form-label">Fecha fin</label><input type="date" name="ends_at" class="form-control" value="{{ old('ends_at', optional($promotion->ends_at)->format('Y-m-d')) }}"></div>
    <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="promo_active" @checked(old('is_active', $promotion->is_active))><label class="form-check-label" for="promo_active">Activo</label></div></div>
    <div class="col-12"><label class="form-label">Descripcion</label><textarea name="description" rows="4" class="form-control">{{ old('description', $promotion->description) }}</textarea></div>
    @include('knowledge._intentions_select', ['model' => $promotion])
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('promotions.index') }}">Cancelar</a>
