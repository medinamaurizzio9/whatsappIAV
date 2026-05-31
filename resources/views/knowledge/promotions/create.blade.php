@extends('layouts.app')
@section('title', 'Nueva promocion')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('promotions.store') }}">@include('knowledge.promotions._form')</form></div></div>
@endsection
