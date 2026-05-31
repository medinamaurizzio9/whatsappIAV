@extends('layouts.app')

@section('title', 'Detalle de area')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <h2 class="h5">{{ $area->name }}</h2>
    <p class="text-muted">{{ $area->description ?: 'Sin descripcion.' }}</p>
    <dl class="row mb-0">
        <dt class="col-sm-3">WhatsApp</dt><dd class="col-sm-9">{{ $area->whatsapp_number }}</dd>
        <dt class="col-sm-3">Correo</dt><dd class="col-sm-9">{{ $area->email ?? '-' }}</dd>
        <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $area->is_active ? 'Activo' : 'Inactivo' }}</dd>
    </dl>
</div></div>
@endsection
