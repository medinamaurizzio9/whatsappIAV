@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Categoria</label>
        <select name="knowledge_category_id" class="form-select" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('knowledge_category_id', $document->knowledge_category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Titulo</label>
        <input name="title" class="form-control" value="{{ old('title', $document->title) }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label">Archivo PDF, DOCX o TXT</label>
        <input type="file" name="file" class="form-control" accept=".pdf,.docx,.txt,.png,.jpg,.jpeg" {{ $document->exists ? '' : 'required' }}>
        @if($document->file_path)
            <div class="small mt-2"><a href="{{ asset('storage/'.$document->file_path) }}" target="_blank">{{ $document->original_filename }}</a></div>
        @endif
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="doc_active" @checked(old('is_active', $document->is_active))><label class="form-check-label" for="doc_active">Activo</label></div>
    </div>
    <div class="col-12">
        <label class="form-label">Descripcion</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $document->description) }}</textarea>
    </div>
    @include('knowledge._intentions_select', ['model' => $document])
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('knowledge-documents.index') }}">Cancelar</a>
