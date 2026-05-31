@extends('layouts.app')
@section('title', 'Nuevo producto')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('products.store') }}">@include('knowledge.products._form')</form></div></div>
@endsection
