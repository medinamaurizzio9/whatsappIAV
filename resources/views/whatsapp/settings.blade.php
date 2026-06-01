@extends('layouts.app')
@section('title', 'Configuracion WhatsApp')
@section('content')
<form method="POST" action="{{ route('whatsapp.settings.update') }}" class="card border-0 shadow-sm">
    @csrf
    @method('PUT')
    <div class="card-body row g-3">
        <div class="col-md-4"><label class="form-label">Business Account ID</label><input name="business_account_id" class="form-control" value="{{ old('business_account_id', $setting->business_account_id) }}"></div>
        <div class="col-md-4"><label class="form-label">Phone Number ID</label><input name="phone_number_id" class="form-control" value="{{ old('phone_number_id', $setting->phone_number_id) }}"></div>
        <div class="col-md-4"><label class="form-label">Telefono mostrado</label><input name="display_phone_number" class="form-control" value="{{ old('display_phone_number', $setting->display_phone_number) }}"></div>
        <div class="col-md-6"><label class="form-label">Access token</label><input type="password" name="access_token" class="form-control"><div class="form-text">Actual: {{ $setting->exists ? $setting->maskedAccessToken() : 'Sin token' }}</div></div>
        <div class="col-md-6"><label class="form-label">App secret opcional</label><input type="password" name="app_secret" class="form-control"><div class="form-text">Se usa para validar X-Hub-Signature-256 si esta configurado.</div></div>
        <div class="col-md-4"><label class="form-label">Verify token</label><input name="verify_token" class="form-control" value="{{ old('verify_token', $setting->verify_token) }}" required></div>
        <div class="col-md-4"><label class="form-label">API version</label><input name="api_version" class="form-control" value="{{ old('api_version', $setting->api_version) }}" required></div>
        <div class="col-md-4"><label class="form-label">Modo atencion</label><select name="attention_mode" class="form-select"><option value="manual" @selected(old('attention_mode', $setting->attention_mode)==='manual')>Manual</option><option value="supervisado" @selected(old('attention_mode', $setting->attention_mode)==='supervisado')>Supervisado</option><option value="automatico" @selected(old('attention_mode', $setting->attention_mode)==='automatico')>Automatico</option></select></div>
        <div class="col-md-8"><label class="form-label">Webhook URL</label><input name="webhook_url" class="form-control" value="{{ old('webhook_url', $setting->webhook_url) }}" placeholder="{{ url('/webhook/whatsapp') }}"></div>
        <div class="col-md-4 d-flex align-items-end"><div class="form-check"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $setting->is_active))><label class="form-check-label" for="is_active">Activo</label></div></div>
    </div>
    <div class="card-footer bg-white"><button class="btn btn-dark">Guardar configuracion</button></div>
</form>
@endsection
