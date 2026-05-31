@extends('layouts.app')
@section('title', 'Nueva FAQ')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('knowledge-faqs.store') }}">@include('knowledge.faqs._form')</form></div></div>
@endsection
