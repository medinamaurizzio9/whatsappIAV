@extends('layouts.app')

@section('title', 'Detalle de opcion')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <h2 class="h5">{{ $option->sort_order }}. {{ $option->title }}</h2>
    <p class="text-muted">{{ $option->description ?: 'Sin descripcion.' }}</p>
    <dl class="row mb-0">
        <dt class="col-sm-3">Accion</dt><dd class="col-sm-9">{{ $option->action === 'ia' ? 'Responder con IA simulada' : 'Derivar a area' }}</dd>
        <dt class="col-sm-3">Area</dt><dd class="col-sm-9">{{ $option->derivationArea?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $option->is_active ? 'Activo' : 'Inactivo' }}</dd>
    </dl>
</div></div>
@endsection
