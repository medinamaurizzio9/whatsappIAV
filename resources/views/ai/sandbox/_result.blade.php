<div class="card border-0 shadow-sm mb-3"><div class="card-header bg-white">{{ $title }}</div><div class="card-body">
    <div class="mb-2"><strong>Proveedor:</strong> {{ $item['provider'] ?? '-' }} · <strong>Modelo:</strong> {{ $item['model'] ?? '-' }}</div>
    <div class="mb-2"><strong>Intencion:</strong> {{ $item['intention']?->name ?? '-' }} · <strong>Confianza:</strong> {{ $item['confidence'] ?? '-' }}</div>
    <div class="mb-2"><strong>Accion:</strong> {{ $item['recommended_action'] ?? '-' }} · <strong>Area:</strong> {{ $item['derivation_area']?->name ?? '-' }}</div>
    <div class="alert alert-light border" style="white-space: pre-line">{{ $item['content'] ?? ($item['error'] ?? 'Sin respuesta') }}</div>
    <div class="small text-muted">Tokens: {{ data_get($item, 'usage.total_tokens', 0) }} (in {{ data_get($item, 'usage.input_tokens', 0) }} / out {{ data_get($item, 'usage.output_tokens', 0) }}) · Costo: {{ $item['cost_estimated'] ?? 0 }} · Tiempo: {{ $item['response_time_ms'] ?? 0 }} ms</div>
    <div class="mt-2"><strong>Fuentes:</strong><ul>@forelse(($item['sources'] ?? []) as $source)<li>{{ $source['type'] ?? '' }}: {{ $source['title'] ?? '' }}</li>@empty<li class="text-muted">Sin fuentes</li>@endforelse</ul></div>
</div></div>
