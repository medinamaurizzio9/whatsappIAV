@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    @foreach([
        'Conversaciones simuladas' => $totalConversations,
        'Clientes registrados' => $totalClients,
        'Derivaciones' => $totalDerivations,
        'Pruebas IA realizadas' => $totalAiTests,
        'Documentos cargados' => $totalKnowledgeDocuments,
        'FAQs activas' => $totalActiveFaqs,
        'Productos activos' => $totalActiveProducts,
        'Consultas realizadas' => $totalKnowledgeQueries,
        'Consultas derivadas' => $derivedKnowledgeQueries,
        'Consultas IA simulada' => $aiKnowledgeQueries,
    ] as $label => $value)
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="display-6 fw-semibold">{{ $value }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Intencion mas consultada</div>
                <div class="h5 mb-0">{{ ($topIntention?->queries_count ?? 0) > 0 ? $topIntention->name : 'Sin consultas' }}</div>
                <div class="text-muted">{{ $topIntention?->queries_count ?? 0 }} consultas</div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Consultas por intencion</div>
            <div class="card-body">
                @forelse($queriesByIntention as $row)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $row->intention?->name ?? 'Sin intencion' }}</span>
                        <strong>{{ $row->total }}</strong>
                    </div>
                @empty
                    <div class="text-muted">Aun no hay consultas clasificadas.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">Ultimas conversaciones de prueba</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Fecha</th><th>Cliente</th><th>Opcion</th><th>Tipo</th><th>Area</th></tr></thead>
            <tbody>
            @forelse($latestConversations as $conversation)
                <tr>
                    <td>{{ optional($conversation->responded_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $conversation->client?->name ?? 'Sin cliente' }}</td>
                    <td>{{ $conversation->initialMenuOption?->title ?? '-' }}</td>
                    <td><span class="badge text-bg-{{ $conversation->response_type === 'derivacion' ? 'warning' : 'success' }}">{{ $conversation->response_type }}</span></td>
                    <td>{{ $conversation->derivationArea?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Aun no hay conversaciones registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
