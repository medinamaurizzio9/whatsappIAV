@extends('layouts.app')
@section('title', 'Nuevo sorteo')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('raffles.store') }}">@include('knowledge.raffles._form')</form></div></div>
@endsection
