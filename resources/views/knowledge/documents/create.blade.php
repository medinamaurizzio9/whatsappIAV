@extends('layouts.app')
@section('title', 'Subir documento')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('knowledge-documents.store') }}">@include('knowledge.documents._form')</form></div></div>
@endsection
