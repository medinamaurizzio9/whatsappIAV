@extends('layouts.app')
@section('title', 'Detalle de categoria')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $category->name }}</h2><p>{{ $category->description ?: 'Sin descripcion.' }}</p><span class="badge text-bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Activo' : 'Inactivo' }}</span></div></div>
@endsection
