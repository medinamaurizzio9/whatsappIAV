@extends('layouts.app')

@section('title', 'Menu inicial de WhatsApp')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('menu-options.create') }}" class="btn btn-dark">Nueva opcion</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Orden</th><th>Titulo</th><th>Accion</th><th>Area</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            @foreach($options as $option)
                <tr>
                    <td>{{ $option->sort_order }}</td>
                    <td>{{ $option->title }}</td>
                    <td>{{ $option->action === 'ia' ? 'IA simulada' : 'Derivacion' }}</td>
                    <td>{{ $option->derivationArea?->name ?? '-' }}</td>
                    <td><span class="badge text-bg-{{ $option->is_active ? 'success' : 'secondary' }}">{{ $option->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('menu-options.show', $option) }}">Ver</a>
                        <a class="btn btn-sm btn-outline-dark" href="{{ route('menu-options.edit', $option) }}">Editar</a>
                        <form method="POST" action="{{ route('menu-options.destroy', $option) }}" class="d-inline" onsubmit="return confirm('Eliminar opcion?')">
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
<div class="mt-3">{{ $options->links() }}</div>
@endsection
