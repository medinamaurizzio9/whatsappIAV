<div class="col-12">
    <label class="form-label">Intenciones</label>
    <select name="intentions[]" class="form-select" multiple size="6">
        @foreach($intentions as $intention)
            <option value="{{ $intention->id }}" @selected(collect(old('intentions', $model->intentions?->pluck('id')->all() ?? []))->contains($intention->id))>
                {{ $intention->name }}
            </option>
        @endforeach
    </select>
    <div class="form-text">Mantén Ctrl presionado para seleccionar varias opciones.</div>
</div>
