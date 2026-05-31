@csrf
<div class="row g-3">
    <div class="col-md-8"><label class="form-label">Nombre</label><input name="name" class="form-control" value="{{ old('name', $raffle->name) }}" required></div>
    <div class="col-md-4"><label class="form-label">Fecha sorteo</label><input type="date" name="raffle_date" class="form-control" value="{{ old('raffle_date', optional($raffle->raffle_date)->format('Y-m-d')) }}"></div>
    <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="raffle_active" @checked(old('is_active', $raffle->is_active))><label class="form-check-label" for="raffle_active">Activo</label></div></div>
    <div class="col-12"><label class="form-label">Descripcion</label><textarea name="description" rows="3" class="form-control">{{ old('description', $raffle->description) }}</textarea></div>
    <div class="col-12"><label class="form-label">Premios</label><textarea name="prizes" rows="3" class="form-control">{{ old('prizes', $raffle->prizes) }}</textarea></div>
    <div class="col-12"><label class="form-label">Reglamento</label><textarea name="rules" rows="4" class="form-control">{{ old('rules', $raffle->rules) }}</textarea></div>
    @include('knowledge._intentions_select', ['model' => $raffle])
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('raffles.index') }}">Cancelar</a>
