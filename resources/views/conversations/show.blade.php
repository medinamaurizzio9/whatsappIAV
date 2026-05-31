@extends('layouts.app')

@section('title', 'Detalle de conversacion')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Fecha</dt><dd class="col-sm-9">{{ optional($conversation->responded_at)->format('d/m/Y H:i') }}</dd>
            <dt class="col-sm-3">Cliente</dt><dd class="col-sm-9">{{ $conversation->client?->name ?? 'Sin cliente' }}</dd>
            <dt class="col-sm-3">Canal</dt><dd class="col-sm-9">{{ $conversation->channel }}</dd>
            <dt class="col-sm-3">Opcion</dt><dd class="col-sm-9">{{ $conversation->initialMenuOption?->title ?? '-' }}</dd>
            <dt class="col-sm-3">Tipo de respuesta</dt><dd class="col-sm-9">{{ $conversation->response_type }}</dd>
            <dt class="col-sm-3">Area</dt><dd class="col-sm-9">{{ $conversation->derivationArea?->name ?? '-' }}</dd>
        </dl>
        <h2 class="h6">Mensaje del cliente</h2>
        <div class="alert alert-light border">{{ $conversation->client_message }}</div>
        <h2 class="h6">Respuesta del sistema</h2>
        <div class="alert alert-light border mb-0">{{ $conversation->system_response }}</div>
    </div>
</div>
@endsection
