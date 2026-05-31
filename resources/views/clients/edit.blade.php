@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('clients.update', $client) }}">
        @method('PUT')
        @include('clients._form')
    </form>
</div></div>
@endsection
