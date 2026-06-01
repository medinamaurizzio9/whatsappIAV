@extends('layouts.app')
@section('title', 'Detalle prompt IA')
@section('content')<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $prompt->name }}</h2><div class="text-muted mb-3">{{ $prompt->type }}</div><pre class="bg-light border rounded p-3" style="white-space: pre-wrap">{{ $prompt->content }}</pre></div></div>@endsection
