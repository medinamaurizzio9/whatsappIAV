@extends('layouts.app')
@section('title', 'Detalle de documento')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $document->title }}</h2><p>{{ $document->description ?: 'Sin descripcion.' }}</p><dl class="row"><dt class="col-sm-3">Categoria</dt><dd class="col-sm-9">{{ $document->category?->name }}</dd><dt class="col-sm-3">Archivo</dt><dd class="col-sm-9"><a href="{{ asset('storage/'.$document->file_path) }}" target="_blank">{{ $document->original_filename }}</a></dd><dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $document->is_active ? 'Activo' : 'Inactivo' }}</dd></dl></div></div>
@endsection
