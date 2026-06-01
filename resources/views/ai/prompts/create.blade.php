@extends('layouts.app')
@section('title', 'Nuevo prompt IA')
@section('content')<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('ai-prompts.store') }}">@include('ai.prompts._form')</form></div></div>@endsection
