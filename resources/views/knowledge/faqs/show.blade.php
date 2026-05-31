@extends('layouts.app')
@section('title', 'Detalle de FAQ')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $faq->question }}</h2><div class="alert alert-light border">{{ $faq->answer }}</div><dl class="row"><dt class="col-sm-3">Categoria</dt><dd class="col-sm-9">{{ $faq->category?->name ?? '-' }}</dd><dt class="col-sm-3">Palabras clave</dt><dd class="col-sm-9">{{ $faq->keywords ?? '-' }}</dd><dt class="col-sm-3">Prioridad</dt><dd class="col-sm-9">{{ $faq->priority }}</dd></dl></div></div>
@endsection
