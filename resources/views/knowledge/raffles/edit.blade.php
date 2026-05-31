@extends('layouts.app')
@section('title', 'Editar sorteo')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('raffles.update', $raffle) }}">@method('PUT')@include('knowledge.raffles._form')</form></div></div>
@endsection
