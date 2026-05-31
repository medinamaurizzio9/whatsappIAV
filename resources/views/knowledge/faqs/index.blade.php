@extends('layouts.app')
@section('title', 'Preguntas frecuentes')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('knowledge-faqs.create') }}" class="btn btn-dark">Nueva FAQ</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
<thead><tr><th>Pregunta</th><th>Categoria</th><th>Prioridad</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
@foreach($faqs as $faq)
<tr><td>{{ Str::limit($faq->question, 90) }}</td><td>{{ $faq->category?->name ?? '-' }}</td><td>{{ $faq->priority }}</td><td><span class="badge text-bg-{{ $faq->is_active ? 'success' : 'secondary' }}">{{ $faq->is_active ? 'Activo' : 'Inactivo' }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('knowledge-faqs.show', $faq) }}">Ver</a> <a class="btn btn-sm btn-outline-dark" href="{{ route('knowledge-faqs.edit', $faq) }}">Editar</a> <form method="POST" action="{{ route('knowledge-faqs.destroy', $faq) }}" class="d-inline" onsubmit="return confirm('Eliminar FAQ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>
@endforeach
</tbody></table></div></div><div class="mt-3">{{ $faqs->links() }}</div>
@endsection
