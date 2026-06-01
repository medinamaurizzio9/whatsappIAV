<div class="card border-0 shadow-sm mb-3"><div class="card-header bg-white">{{ $title }}</div><div class="card-body">
    <div class="mb-2"><strong>Proveedor:</strong> {{ $item['provider'] ?? '-' }} · <strong>Modelo:</strong> {{ $item['model'] ?? '-' }}</div>
    <div class="mb-2"><strong>Intencion:</strong> {{ $item['intention']?->name ?? '-' }} · <strong>Confianza:</strong> {{ $item['confidence'] ?? '-' }}</div>
    <div class="mb-2"><strong>Accion:</strong> {{ $item['recommended_action'] ?? '-' }} · <strong>Area:</strong> {{ $item['derivation_area']?->name ?? '-' }}</div>
    <div class="alert alert-light border" style="white-space: pre-line">{{ $item['content'] ?? ($item['error'] ?? 'Sin respuesta') }}</div>
    <div class="small text-muted">Tokens: {{ data_get($item, 'usage.total_tokens', 0) }} (in {{ data_get($item, 'usage.input_tokens', 0) }} / out {{ data_get($item, 'usage.output_tokens', 0) }}) · Costo: {{ $item['cost_estimated'] ?? 0 }} · Tiempo: {{ $item['response_time_ms'] ?? 0 }} ms</div>
    <div class="mt-2"><strong>Fuentes:</strong><ul>@forelse(($item['sources'] ?? []) as $source)<li>{{ $source['type'] ?? '' }}: {{ $source['title'] ?? '' }}</li>@empty<li class="text-muted">Sin fuentes</li>@endforelse</ul></div>
    <div class="mt-2"><strong>Chunks semanticos:</strong><ul>@forelse(($item['semantic_context'] ?? []) as $chunk)<li>Chunk {{ $chunk['chunk_index'] }} · score {{ $chunk['score'] }} · {{ $chunk['source_type'] }} #{{ $chunk['source_id'] }} · intencion {{ $chunk['intention'] ?: '-' }}<div class="small text-muted">{{ Str::limit($chunk['content'], 160) }}</div></li>@empty<li class="text-muted">Sin chunks</li>@endforelse</ul></div>
    <form method="POST" action="{{ route('knowledge-feedback.store') }}" class="mt-3">
        @csrf
        <input type="hidden" name="question" value="{{ request('question', '') }}">
        <input type="hidden" name="generated_answer" value="{{ $item['content'] ?? '' }}">
        <input type="hidden" name="intention_id" value="{{ $item['intention']?->id }}">
        <input type="hidden" name="provider" value="{{ $item['provider'] ?? '' }}">
        <button class="btn btn-sm btn-outline-success" name="rating" value="5">Correcta</button>
        <button class="btn btn-sm btn-outline-danger" name="rating" value="1">Incorrecta</button>
        <textarea name="correct_answer" class="form-control form-control-sm mt-2" rows="2" placeholder="Correccion si es incorrecta"></textarea>
    </form>
</div></div>
