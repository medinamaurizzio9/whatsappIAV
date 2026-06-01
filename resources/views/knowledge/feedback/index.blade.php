@extends('layouts.app')
@section('title', 'Feedback de conocimiento')
@section('content')
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Fecha</th><th>Pregunta</th><th>Intencion</th><th>Proveedor</th><th>Calificacion</th><th>Operador</th></tr></thead><tbody>@forelse($feedback as $item)<tr><td>{{ $item->created_at->format('d/m/Y H:i') }}</td><td>{{ Str::limit($item->question, 70) }}</td><td>{{ $item->intention?->name ?? '-' }}</td><td>{{ $item->provider ?? '-' }}</td><td>{{ $item->rating }}</td><td>{{ $item->user?->name ?? '-' }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">Sin feedback registrado.</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $feedback->links() }}</div>
@endsection
