@extends('layouts.app')

@section('title', 'Chat interno de prueba')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('chat.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Cliente opcional</label>
                        <select name="client_id" class="form-select">
                            <option value="">Sin cliente asociado</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }} · {{ $client->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opcion del menu inicial</label>
                        <select name="initial_menu_option_id" class="form-select" required>
                            @foreach($options as $option)
                                <option value="{{ $option->id }}" @selected(old('initial_menu_option_id') == $option->id)>{{ $option->sort_order }}. {{ $option->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensaje como cliente</label>
                        <textarea name="client_message" rows="5" class="form-control" required>{{ old('client_message') }}</textarea>
                    </div>
                    <button class="btn btn-dark">Probar flujo y guardar historial</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        @if($result)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">Respuesta simulada</div>
                <div class="card-body chat-surface">
                    <div class="mb-2"><strong>Opcion:</strong> {{ $result['option'] }}</div>
                    <div class="chat-bubble chat-bubble-user ms-lg-5 mb-3">{{ $result['client_message'] }}</div>
                    <div class="mb-2">
                        <strong>Decision:</strong>
                        <span class="badge text-bg-{{ $result['response_type'] === 'derivacion' ? 'warning' : 'success' }}">{{ $result['response_type'] }}</span>
                    </div>
                    <div class="mb-2"><strong>Intencion detectada:</strong> {{ $result['intention'] ?? 'Sin clasificar' }}</div>
                    <div class="mb-2"><strong>Confianza simulada:</strong> {{ $result['confidence'] ?? '-' }}</div>
                    <div class="mb-2"><strong>Accion recomendada:</strong> {{ $result['recommended_action'] ?? '-' }}</div>
                    <div class="mb-2"><strong>Area:</strong> {{ $result['area'] ?? '-' }}</div>
                    <div class="chat-bubble chat-bubble-system mb-0">{{ $result['message'] }}</div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Historial reciente</div>
            <div class="list-group list-group-flush">
                @forelse($latestConversations as $conversation)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $conversation->initialMenuOption?->title ?? 'Sin opcion' }}</strong>
                            <span class="badge text-bg-{{ $conversation->response_type === 'derivacion' ? 'warning' : 'success' }}">{{ $conversation->response_type }}</span>
                        </div>
                        <div class="small text-muted">{{ optional($conversation->responded_at)->format('d/m/Y H:i') }} · {{ $conversation->client?->name ?? 'Sin cliente' }}</div>
                        <div>{{ Str::limit($conversation->client_message, 90) }}</div>
                    </div>
                @empty
                    <div class="list-group-item text-muted">Aun no hay historial.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
