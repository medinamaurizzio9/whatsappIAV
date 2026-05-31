@extends('layouts.app')

@section('title', 'Editar area')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('derivation-areas.update', $area) }}">
        @method('PUT')
        @include('derivation-areas._form')
    </form>
</div></div>
@endsection
