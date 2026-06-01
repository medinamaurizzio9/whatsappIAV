@extends('layouts.app')
@section('title', 'Detalle pregunta pendiente')
@section('content')<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">{{ $question->question }}</h2><dl class="row"><dt class="col-sm-3">Intencion</dt><dd class="col-sm-9">{{ $question->intention?->name ?? '-' }}</dd><dt class="col-sm-3">Motivo</dt><dd class="col-sm-9">{{ $question->reason }}</dd><dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $question->status }}</dd></dl></div></div>@endsection
