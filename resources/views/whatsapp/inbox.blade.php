@extends('layouts.app')
@section('title', 'Bandeja WhatsApp')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Contacto</th><th>Ultimo mensaje</th><th>Modo</th><th>Area</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($conversations as $conversation)
                <tr>
                    <td>{{ $conversation->contact->name ?? $conversation->contact->phone }}<div class="text-muted small">{{ $conversation->contact->phone }}</div></td>
                    <td>{{ $conversation->last_message_preview }}<div class="text-muted small">{{ $conversation->last_message_at?->format('d/m/Y H:i') }}</div></td>
                    <td>{{ $conversation->attention_mode }}</td>
                    <td>{{ $conversation->derivationArea?->name ?? '-' }}</td>
                    <td><span class="badge text-bg-{{ $conversation->status === 'open' ? 'success' : 'secondary' }}">{{ $conversation->status }}</span></td>
                    <td><a class="btn btn-sm btn-outline-dark" href="{{ route('whatsapp.conversations.show', $conversation) }}">Abrir</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Aun no hay conversaciones recibidas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $conversations->links() }}</div>
@endsection
