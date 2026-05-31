@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
@if(auth()->user()->isAdmin())
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('clients.create') }}" class="btn btn-dark">Nuevo cliente</a>
    </div>
@endif
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Nombre</th><th>Celular</th><th>Ciudad</th><th>Tipo</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            @foreach($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>{{ $client->city ?? '-' }}</td>
                    <td>{{ ucfirst($client->type) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('clients.show', $client) }}">Ver</a>
                        @if(auth()->user()->isAdmin())
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('clients.edit', $client) }}">Editar</a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline" onsubmit="return confirm('Eliminar cliente?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $clients->links() }}</div>
@endsection
