@extends('layouts.app')
@section('title', 'Editar proveedor IA')
@section('content')<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('ai-providers.update', $provider) }}">@method('PUT')@include('ai.providers._form')</form></div></div>@endsection
