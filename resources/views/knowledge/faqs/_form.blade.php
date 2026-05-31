@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Categoria</label>
        <select name="knowledge_category_id" class="form-select">
            <option value="">Sin categoria</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('knowledge_category_id', $faq->knowledge_category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Prioridad</label>
        <input type="number" min="0" name="priority" class="form-control" value="{{ old('priority', $faq->priority) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Pregunta</label>
        <textarea name="question" rows="2" class="form-control" required>{{ old('question', $faq->question) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Respuesta</label>
        <textarea name="answer" rows="5" class="form-control" required>{{ old('answer', $faq->answer) }}</textarea>
    </div>
    <div class="col-md-8">
        <label class="form-label">Palabras clave</label>
        <input name="keywords" class="form-control" value="{{ old('keywords', $faq->keywords) }}" placeholder="oro, anillo, garantia">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="faq_active" @checked(old('is_active', $faq->is_active))><label class="form-check-label" for="faq_active">Activo</label></div>
    </div>
    @include('knowledge._intentions_select', ['model' => $faq])
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('knowledge-faqs.index') }}">Cancelar</a>
