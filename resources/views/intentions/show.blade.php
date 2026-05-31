@extends('layouts.app')
@section('title', 'Detalle de intencion')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5"><span class="badge me-2" style="background: {{ $intention->color }}">&nbsp;</span>{{ $intention->name }}</h2><p>{{ $intention->description ?: 'Sin descripcion.' }}</p><dl class="row"><dt class="col-sm-3">Slug</dt><dd class="col-sm-9">{{ $intention->slug }}</dd><dt class="col-sm-3">Accion</dt><dd class="col-sm-9">{{ $intention->default_action }}</dd><dt class="col-sm-3">Area</dt><dd class="col-sm-9">{{ $intention->derivationArea?->name ?? '-' }}</dd><dt class="col-sm-3">Confianza minima</dt><dd class="col-sm-9">{{ $intention->minimum_confidence }}</dd><dt class="col-sm-3">Palabras clave</dt><dd class="col-sm-9">{{ $intention->keywords ?? '-' }}</dd></dl></div></div>
@endsection
