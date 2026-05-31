@csrf
<div class="row g-3">
    <div class="col-md-8"><label class="form-label">Nombre</label><input name="name" class="form-control" value="{{ old('name', $product->name) }}" required></div>
    <div class="col-md-4"><label class="form-label">Precio</label><input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price) }}"></div>
    <div class="col-md-6"><label class="form-label">Imagen principal</label><input type="file" name="main_image" class="form-control" accept="image/*">@if($product->main_image_path)<div class="small mt-2"><a target="_blank" href="{{ asset('storage/'.$product->main_image_path) }}">Ver imagen actual</a></div>@endif</div>
    <div class="col-md-6"><label class="form-label">Catalogo PDF opcional</label><input type="file" name="catalog_pdf" class="form-control" accept=".pdf">@if($product->catalog_pdf_path)<div class="small mt-2"><a target="_blank" href="{{ asset('storage/'.$product->catalog_pdf_path) }}">Ver catalogo actual</a></div>@endif</div>
    <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="product_active" @checked(old('is_active', $product->is_active))><label class="form-check-label" for="product_active">Activo</label></div></div>
    <div class="col-12"><label class="form-label">Descripcion</label><textarea name="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea></div>
    @include('knowledge._intentions_select', ['model' => $product])
</div>
<button class="btn btn-dark mt-4">Guardar</button>
<a class="btn btn-link mt-4" href="{{ route('products.index') }}">Cancelar</a>
