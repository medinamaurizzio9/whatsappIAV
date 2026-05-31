@extends('layouts.app')
@section('title', 'Detalle de sorteo')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $raffle->name }}</h2><p>{{ $raffle->description ?: 'Sin descripcion.' }}</p><h3 class="h6">Premios</h3><div class="alert alert-light border">{{ $raffle->prizes ?: '-' }}</div><h3 class="h6">Reglamento</h3><div class="alert alert-light border">{{ $raffle->rules ?: '-' }}</div><p><strong>Fecha:</strong> {{ optional($raffle->raffle_date)->format('d/m/Y') ?? '-' }}</p></div></div>
@endsection
