@extends('layouts.app')
@section('title', 'Editar producto')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('products.update', $product) }}">@method('PUT')@include('knowledge.products._form')</form></div></div>
@endsection
