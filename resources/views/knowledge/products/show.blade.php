@extends('layouts.app')
@section('title', 'Detalle de producto')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $product->name }}</h2><p>{{ $product->description ?: 'Sin descripcion.' }}</p><p><strong>Precio:</strong> {{ $product->price ? 'Bs. '.$product->price : '-' }}</p>@if($product->main_image_path)<img src="{{ asset('storage/'.$product->main_image_path) }}" class="img-fluid rounded mb-3" style="max-height:260px" alt="{{ $product->name }}">@endif @if($product->catalog_pdf_path)<div><a target="_blank" href="{{ asset('storage/'.$product->catalog_pdf_path) }}">Ver catalogo PDF</a></div>@endif</div></div>
@endsection
