@extends('layouts.app')

@section('title', 'Detalle de cliente')

@section('content')
<div class="card border-0 shadow-sm mb-4"><div class="card-body">
    <h2 class="h5">{{ $client->name }}</h2>
    <dl class="row mb-0">
        <dt class="col-sm-3">Celular</dt><dd class="col-sm-9">{{ $client->phone }}</dd>
        <dt class="col-sm-3">Ciudad</dt><dd class="col-sm-9">{{ $client->city ?? '-' }}</dd>
        <dt class="col-sm-3">Tipo</dt><dd class="col-sm-9">{{ ucfirst($client->type) }}</dd>
        <dt class="col-sm-3">Observaciones</dt><dd class="col-sm-9">{{ $client->observations ?? '-' }}</dd>
    </dl>
</div></div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">Historial de pruebas</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Fecha</th><th>Mensaje</th><th>Tipo</th><th>Area</th></tr></thead>
            <tbody>
            @forelse($client->simulatedConversations as $conversation)
                <tr>
                    <td>{{ optional($conversation->responded_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ Str::limit($conversation->client_message, 70) }}</td>
                    <td>{{ $conversation->response_type }}</td>
                    <td>{{ $conversation->derivationArea?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Sin pruebas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
