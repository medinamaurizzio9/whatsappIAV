@extends('layouts.app')

@section('title', 'Conversaciones simuladas')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Fecha</th><th>Cliente</th><th>Canal</th><th>Mensaje</th><th>Tipo</th><th>Area</th><th></th></tr></thead>
            <tbody>
            @foreach($conversations as $conversation)
                <tr>
                    <td>{{ optional($conversation->responded_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $conversation->client?->name ?? 'Sin cliente' }}</td>
                    <td>{{ $conversation->channel }}</td>
                    <td>{{ Str::limit($conversation->client_message, 60) }}</td>
                    <td>{{ $conversation->response_type }}</td>
                    <td>{{ $conversation->derivationArea?->name ?? '-' }}</td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('conversations.show', $conversation) }}">Ver</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $conversations->links() }}</div>
@endsection
