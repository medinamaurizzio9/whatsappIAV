@extends('layouts.app')
@section('title', 'Conversacion WhatsApp')
@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5>{{ $conversation->contact->name ?? $conversation->contact->phone }}</h5>
                <div class="text-muted">{{ $conversation->contact->phone }}</div>
            </div>
        </div>
        <div class="d-grid gap-2 mb-3 chat-surface">
            @foreach($conversation->messages as $message)
                <div class="chat-bubble {{ $message->direction === 'inbound' ? 'chat-bubble-system' : 'chat-bubble-user ms-lg-5' }}">
                    <div class="small opacity-75">{{ strtoupper($message->direction) }} · {{ $message->type }} · {{ $message->created_at->format('d/m/Y H:i') }} · {{ $message->status ?? 'recibido' }}</div>
                    <div class="mt-1">{{ $message->body ?: '[multimedia]' }}</div>
                    @if($message->intention)<div class="small mt-2">Intencion: {{ $message->intention->name }} · Confianza: {{ $message->confidence }}</div>@endif
                    @if($message->derivationArea)<div class="small">Area: {{ $message->derivationArea->name }}</div>@endif
                    @if($message->requires_approval)
                        <form method="POST" action="{{ route('whatsapp.messages.approve', $message) }}" class="mt-2">@csrf<button class="btn btn-sm btn-warning">Aprobar y enviar</button></form>
                    @endif
                </div>
            @endforeach
        </div>
        <form method="POST" action="{{ route('whatsapp.conversations.reply', $conversation) }}" enctype="multipart/form-data" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body">
                <label class="form-label">Respuesta manual</label>
                <textarea name="body" rows="4" class="form-control"></textarea>
                <label class="form-label mt-3">Archivo opcional</label>
                <input type="file" name="file" class="form-control">
            </div>
            <div class="card-footer bg-white"><button class="btn btn-dark">Enviar</button></div>
        </form>
    </div>
    <div class="col-lg-4">
        <form method="POST" action="{{ route('whatsapp.conversations.update', $conversation) }}" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')
            <div class="card-body g-3 row">
                <div class="col-12"><label class="form-label">Area asignada</label><select name="derivation_area_id" class="form-select"><option value="">Sin area</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected($conversation->derivation_area_id === $area->id)>{{ $area->name }}</option>@endforeach</select></div>
                <div class="col-12"><label class="form-label">Modo</label><select name="attention_mode" class="form-select"><option value="manual" @selected($conversation->attention_mode==='manual')>Manual</option><option value="supervisado" @selected($conversation->attention_mode==='supervisado')>Supervisado</option><option value="automatico" @selected($conversation->attention_mode==='automatico')>Automatico</option></select></div>
                <div class="col-12"><label class="form-label">Estado</label><select name="status" class="form-select"><option value="open" @selected($conversation->status==='open')>Abierta</option><option value="closed" @selected($conversation->status==='closed')>Cerrada</option></select></div>
            </div>
            <div class="card-footer bg-white"><button class="btn btn-outline-dark">Actualizar</button></div>
        </form>
    </div>
</div>
@endsection
