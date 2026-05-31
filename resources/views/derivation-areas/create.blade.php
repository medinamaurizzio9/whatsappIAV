@extends('layouts.app')

@section('title', 'Nueva area')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('derivation-areas.store') }}">@include('derivation-areas._form')</form>
</div></div>
@endsection
