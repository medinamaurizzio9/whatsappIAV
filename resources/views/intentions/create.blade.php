@extends('layouts.app')
@section('title', 'Nueva intencion')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('intentions.store') }}">@include('intentions._form')</form></div></div>
@endsection
