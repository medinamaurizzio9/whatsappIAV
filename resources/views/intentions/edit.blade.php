@extends('layouts.app')
@section('title', 'Editar intencion')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('intentions.update', $intention) }}">@method('PUT')@include('intentions._form')</form></div></div>
@endsection
