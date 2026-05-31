@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-1">VIANKA GOLD MINING</h1>
                <p class="text-muted mb-4">Ingreso al sistema administrativo</p>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>
                    <button class="btn btn-dark w-100">Ingresar</button>
                </form>
                <div class="small text-muted mt-3">
                    Admin: admin@viankagold.test / password<br>
                    Operador: operador@viankagold.test / password
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
