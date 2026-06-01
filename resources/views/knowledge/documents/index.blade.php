@extends('layouts.app')
@section('title', 'Documentos de conocimiento')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <form method="POST" action="{{ route('knowledge.reindex') }}" class="d-flex gap-2">@csrf<select name="scope" class="form-select form-select-sm"><option value="all">Todo</option><option value="faqs">Solo FAQs</option><option value="documents">Solo documentos</option><option value="products">Solo productos</option></select><button class="btn btn-sm btn-outline-dark">Reindexar conocimiento</button></form>
    <a href="{{ route('knowledge-documents.create') }}" class="btn btn-dark">Subir documento</a>
</div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
<thead><tr><th>Titulo</th><th>Categoria</th><th>Archivo</th><th>Fecha carga</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
@foreach($documents as $document)
<tr><td>{{ $document->title }}</td><td>{{ $document->category?->name }}</td><td>{{ $document->original_filename }}</td><td>{{ optional($document->uploaded_at)->format('d/m/Y H:i') }}</td><td><span class="badge text-bg-{{ $document->is_active ? 'success' : 'secondary' }}">{{ $document->is_active ? 'Activo' : 'Inactivo' }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('knowledge-documents.show', $document) }}">Ver</a> <a class="btn btn-sm btn-outline-dark" href="{{ route('knowledge-documents.edit', $document) }}">Editar</a> <form method="POST" action="{{ route('knowledge-documents.destroy', $document) }}" class="d-inline" onsubmit="return confirm('Eliminar documento?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>
@endforeach
</tbody></table></div></div><div class="mt-3">{{ $documents->links() }}</div>
@endsection
