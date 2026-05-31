@extends('layouts.app')

@section('title', 'Areas de derivacion')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('derivation-areas.create') }}" class="btn btn-dark">Nueva area</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Nombre</th><th>WhatsApp</th><th>Correo</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            @foreach($areas as $area)
                <tr>
                    <td>{{ $area->name }}</td>
                    <td>{{ $area->whatsapp_number }}</td>
                    <td>{{ $area->email ?? '-' }}</td>
                    <td><span class="badge text-bg-{{ $area->is_active ? 'success' : 'secondary' }}">{{ $area->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('derivation-areas.show', $area) }}">Ver</a>
                        <a class="btn btn-sm btn-outline-dark" href="{{ route('derivation-areas.edit', $area) }}">Editar</a>
                        <form method="POST" action="{{ route('derivation-areas.destroy', $area) }}" class="d-inline" onsubmit="return confirm('Eliminar area?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $areas->links() }}</div>
@endsection
