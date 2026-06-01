@extends('layouts.app')
@section('title', 'Dashboard comercial')
@section('content')
<div class="row g-3 mb-4">
@foreach(['Leads nuevos'=>$newLeads,'Leads calientes'=>$hotLeads,'Leads muy calientes'=>$veryHotLeads,'Compradores potenciales'=>$buyers,'Inversionistas potenciales'=>$investors,'Afiliados potenciales'=>$affiliates] as $label=>$value)
<div class="col-md-4 col-xl-2"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="h3 mb-0">{{ $value }}</div></div></div></div>
@endforeach
</div>
<div class="row g-4">
<div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-header bg-white">Alertas</div><div class="list-group list-group-flush">@forelse($alerts as $alert)<a class="list-group-item list-group-item-action" href="{{ $alert->client ? route('leads.show', $alert->client) : '#' }}"><strong>{{ $alert->title }}</strong><div class="small text-muted">{{ $alert->client?->name }} · {{ $alert->message }}</div></a>@empty<div class="list-group-item text-muted">Sin alertas pendientes.</div>@endforelse</div></div></div>
<div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-header bg-white">Conversión por intención</div><div class="card-body">@foreach($conversionByIntent as $row)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $row->evento }}</span><strong>{{ $row->total }}</strong></div>@endforeach</div></div></div>
<div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-header bg-white">Derivaciones por área</div><div class="card-body">@foreach($derivationsByArea as $row)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $row->derivationArea?->name ?? '-' }}</span><strong>{{ $row->total }}</strong></div>@endforeach</div></div></div>
<div class="col-12"><div class="card border-0 shadow-sm"><div class="card-header bg-white">Evolución de score</div><div class="card-body">@forelse($scoreEvolution as $row)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $row->date }}</span><strong>{{ $row->total }}</strong></div>@empty<div class="text-muted">Sin eventos.</div>@endforelse</div></div></div>
</div>
@endsection
