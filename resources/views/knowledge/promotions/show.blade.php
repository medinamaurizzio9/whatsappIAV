@extends('layouts.app')
@section('title', 'Detalle de promocion')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $promotion->name }}</h2><p>{{ $promotion->description ?: 'Sin descripcion.' }}</p><p><strong>Vigencia:</strong> {{ optional($promotion->starts_at)->format('d/m/Y') ?? '-' }} - {{ optional($promotion->ends_at)->format('d/m/Y') ?? '-' }}</p></div></div>
@endsection
