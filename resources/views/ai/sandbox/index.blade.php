@extends('layouts.app')
@section('title', 'Sandbox IA')
@section('content')
<div class="row g-4">
    <div class="col-lg-5"><div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('ai-sandbox.run') }}">
            @csrf
            <label class="form-label">Pregunta del cliente</label>
            <textarea name="question" rows="6" class="form-control" required>{{ old('question', $result['question'] ?? '') }}</textarea>
            <div class="mt-3"><label class="form-label">Proveedor</label><select name="provider" class="form-select"><option value="automatico">Automatico</option><option value="openai">OpenAI</option><option value="deepseek">DeepSeek</option></select></div>
            <div class="form-check mt-3"><input type="hidden" name="use_knowledge" value="0"><input class="form-check-input" type="checkbox" name="use_knowledge" value="1" id="use_knowledge" checked><label class="form-check-label" for="use_knowledge">Usar base de conocimiento</label></div>
            <div class="form-check"><input type="hidden" name="use_intent" value="0"><input class="form-check-input" type="checkbox" name="use_intent" value="1" id="use_intent" checked><label class="form-check-label" for="use_intent">Usar deteccion de intencion</label></div>
            <button class="btn btn-dark mt-3">Generar respuesta</button>
            <button class="btn btn-outline-dark mt-3" name="compare" value="1">Comparar proveedores</button>
        </form>
    </div></div></div>
    <div class="col-lg-7">
        @if($result)
            @include('ai.sandbox._result', ['title' => 'Resultado', 'item' => $result])
        @endif
        @if($comparison)
            <div class="row g-3">
                @foreach($comparison as $provider => $item)
                    <div class="col-md-6">@include('ai.sandbox._result', ['title' => strtoupper($provider), 'item' => $item])</div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
