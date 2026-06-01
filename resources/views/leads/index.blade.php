@extends('layouts.app')
@section('title', 'Leads')
@section('content')
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Cliente</th><th>Ciudad</th><th>Score</th><th>Categoría</th><th>Etapa</th><th>Área</th><th></th></tr></thead><tbody>@foreach($clients as $client)<tr><td>{{ $client->name }}</td><td>{{ $client->city ?? '-' }}</td><td>{{ $client->leadScore?->score ?? 0 }}</td><td>{{ $client->leadScore?->categoria ?? 'Frio' }}</td><td>{{ $client->leadPipeline?->stage ?? 'Nuevo' }}</td><td>{{ $client->leadPipeline?->assignedArea?->name ?? '-' }}</td><td class="text-end"><a class="btn btn-sm btn-outline-dark" href="{{ route('leads.show', $client) }}">Ver lead</a></td></tr>@endforeach</tbody></table></div></div><div class="mt-3">{{ $clients->links() }}</div>
@endsection
