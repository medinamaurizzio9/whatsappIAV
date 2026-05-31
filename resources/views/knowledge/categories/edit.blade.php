@extends('layouts.app')
@section('title', 'Editar categoria')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('knowledge-categories.update', $category) }}">@method('PUT')@include('knowledge.categories._form')</form></div></div>
@endsection
