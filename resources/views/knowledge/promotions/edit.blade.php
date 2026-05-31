@extends('layouts.app')
@section('title', 'Editar promocion')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('promotions.update', $promotion) }}">@method('PUT')@include('knowledge.promotions._form')</form></div></div>
@endsection
