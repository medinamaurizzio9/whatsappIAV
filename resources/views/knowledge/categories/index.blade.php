@extends('layouts.app')

@section('title', 'Categorias de conocimiento')

@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('knowledge-categories.create') }}" class="btn btn-dark">Nueva categoria</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>Nombre</th><th>Descripcion</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ Str::limit($category->description, 80) }}</td>
                <td><span class="badge text-bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('knowledge-categories.show', $category) }}">Ver</a>
                    <a class="btn btn-sm btn-outline-dark" href="{{ route('knowledge-categories.edit', $category) }}">Editar</a>
                    <form method="POST" action="{{ route('knowledge-categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Eliminar categoria?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection
