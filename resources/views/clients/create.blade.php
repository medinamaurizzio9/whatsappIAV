@extends('layouts.app')

@section('title', 'Nuevo cliente')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('clients.store') }}">@include('clients._form')</form>
</div></div>
@endsection
