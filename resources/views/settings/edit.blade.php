@extends('layouts.app')

@section('title', 'Configuracion general')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre de la empresa</label>
                    <input name="company_name" class="form-control" value="{{ old('company_name', $setting->company_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    @if($setting->logo_path)
                        <div class="small mt-2"><a href="{{ asset('storage/'.$setting->logo_path) }}" target="_blank">Ver logo actual</a></div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefono principal</label>
                    <input name="main_phone" class="form-control" value="{{ old('main_phone', $setting->main_phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Horario de atencion</label>
                    <input name="business_hours" class="form-control" value="{{ old('business_hours', $setting->business_hours) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Direccion</label>
                    <input name="address" class="form-control" value="{{ old('address', $setting->address) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Mensaje de bienvenida</label>
                    <textarea name="welcome_message" rows="4" class="form-control">{{ old('welcome_message', $setting->welcome_message) }}</textarea>
                </div>
            </div>
            <button class="btn btn-dark mt-4">Guardar configuracion</button>
        </form>
    </div>
</div>
@endsection
