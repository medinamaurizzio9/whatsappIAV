@extends('layouts.app')
@section('title', 'Buscador interno IA')
@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <form method="POST" action="{{ route('knowledge-search.search') }}">
                @csrf
                <label class="form-label">Pregunta para la base de conocimiento</label>
                <textarea name="question" rows="5" class="form-control" required>{{ old('question', $result['question'] ?? '') }}</textarea>
                <button class="btn btn-dark mt-3">Consultar base local</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-6">
        @if($result)
            <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white">Respuesta simulada</div><div class="card-body">
                <div class="alert alert-light border" style="white-space: pre-line">{{ $result['answer'] }}</div>
                <div class="mb-2"><strong>Intencion:</strong> {{ $result['intention'] ?? 'Sin clasificar' }}</div>
                <div class="mb-2"><strong>Confianza:</strong> {{ $result['confidence'] ?? '-' }}</div>
                <div class="mb-2"><strong>Accion recomendada:</strong> {{ $result['recommended_action'] ?? '-' }}</div>
                <div class="mb-2"><strong>Area:</strong> {{ $result['derivation_area'] ?? '-' }}</div>
                <h3 class="h6">Documentos utilizados</h3>
                <ul>@forelse($result['documents'] as $document)<li>{{ $document['title'] }} <span class="text-muted">({{ $document['file'] }})</span></li>@empty<li class="text-muted">Ninguno</li>@endforelse</ul>
                <h3 class="h6">FAQs utilizadas</h3>
                <ul>@forelse($result['faqs'] as $faq)<li>{{ $faq['question'] }}</li>@empty<li class="text-muted">Ninguna</li>@endforelse</ul>
                <h3 class="h6">Fuentes</h3>
                <ul>@forelse($result['sources'] as $source)<li>{{ ucfirst($source['type']) }}: {{ $source['title'] }}</li>@empty<li class="text-muted">Sin fuentes</li>@endforelse</ul>
            </div></div>
        @endif
        <div class="card border-0 shadow-sm"><div class="card-header bg-white">Filtros de auditoria</div><div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-6"><select name="intention_id" class="form-select"><option value="">Todas las intenciones</option>@foreach($intentions as $intention)<option value="{{ $intention->id }}" @selected(request('intention_id') == $intention->id)>{{ $intention->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><select name="recommended_action" class="form-select"><option value="">Todas las acciones</option>@foreach(['responder_ia', 'derivar', 'responder_y_derivar'] as $action)<option value="{{ $action }}" @selected(request('recommended_action') === $action)>{{ $action }}</option>@endforeach</select></div>
                <div class="col-md-6"><select name="derivation_area_id" class="form-select"><option value="">Todas las areas</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(request('derivation_area_id') == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
                <div class="col-md-3"><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
                <div class="col-md-3"><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
                <div class="col-12"><button class="btn btn-outline-dark btn-sm">Filtrar</button> <a href="{{ route('knowledge-search.index') }}" class="btn btn-link btn-sm">Limpiar</a></div>
            </form>
        </div></div>

        <div class="card border-0 shadow-sm mt-4"><div class="card-header bg-white">Auditoria reciente</div><div class="list-group list-group-flush">
            @forelse($audits as $audit)
                <div class="list-group-item">
                    <strong>{{ Str::limit($audit->question, 80) }}</strong>
                    <div class="small text-muted">{{ optional($audit->queried_at)->format('d/m/Y H:i') }} · {{ $audit->user?->name }}</div>
                    <div class="small">Intencion: {{ $audit->intention?->name ?? '-' }} · Accion: {{ $audit->recommended_action ?? '-' }} · Area: {{ $audit->derivationArea?->name ?? '-' }}</div>
                </div>
            @empty
                <div class="list-group-item text-muted">Sin consultas registradas.</div>
            @endforelse
        </div></div>
        <div class="mt-3">{{ $audits->links() }}</div>
    </div>
</div>
@endsection
