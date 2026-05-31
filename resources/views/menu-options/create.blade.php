@extends('layouts.app')

@section('title', 'Nueva opcion')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('menu-options.store') }}">@include('menu-options._form')</form>
</div></div>
@endsection
