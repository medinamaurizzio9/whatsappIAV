@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Celular</label>
        <input name="phone" class="form-control" value="{{ old('phone', $client->phone) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Ciudad</label>
        <input name="city" class="form-control" value="{{ old('city', $client->city) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Tipo</label>
        <select name="type" class="form-select" required>
            @foreach(['prospecto', 'comprador', 'inversionista', 'trabajador/interesado'] as $type)
                <option value="{{ $type }}" @selected(old('type', $client->type) === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Observaciones</label>
        <textarea name="observations" rows="3" class="form-control">{{ old('observations', $client->observations) }}</textarea>
    </div>
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('clients.index') }}">Cancelar</a>
