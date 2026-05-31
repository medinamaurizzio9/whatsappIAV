@extends('layouts.app')
@section('title', 'Intenciones')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('intentions.create') }}" class="btn btn-dark">Nueva intencion</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Nombre</th><th>Accion</th><th>Area</th><th>Confianza</th><th>Prioridad</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
@foreach($intentions as $intention)
<tr><td><span class="badge me-2" style="background: {{ $intention->color }}">&nbsp;</span>{{ $intention->name }}</td><td>{{ $intention->default_action }}</td><td>{{ $intention->derivationArea?->name ?? '-' }}</td><td>{{ $intention->minimum_confidence }}</td><td>{{ $intention->priority }}</td><td><span class="badge text-bg-{{ $intention->is_active ? 'success' : 'secondary' }}">{{ $intention->is_active ? 'Activo' : 'Inactivo' }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('intentions.show', $intention) }}">Ver</a> <a class="btn btn-sm btn-outline-dark" href="{{ route('intentions.edit', $intention) }}">Editar</a> <form method="POST" action="{{ route('intentions.destroy', $intention) }}" class="d-inline" onsubmit="return confirm('Eliminar intencion?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>
@endforeach
</tbody></table></div></div><div class="mt-3">{{ $intentions->links() }}</div>
@endsection
