<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f7fb; }
        .sidebar { min-height: 100vh; background: #161616; }
        .sidebar a { color: #ddd; text-decoration: none; }
        .sidebar a.active, .sidebar a:hover { color: #fff; background: #2b2b2b; }
        .brand-mark { color: #d8b44c; letter-spacing: .04em; }
        .stat-card { border: 0; border-left: 4px solid #d8b44c; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        @auth
            <aside class="col-md-3 col-xl-2 sidebar p-3">
                <h5 class="brand-mark mb-4">VIANKA GOLD</h5>
                <nav class="d-grid gap-1">
                    <a class="rounded px-3 py-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="rounded px-3 py-2 {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">Clientes</a>
                    <a class="rounded px-3 py-2 {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">Chat interno</a>
                    <a class="rounded px-3 py-2 {{ request()->routeIs('ai-sandbox.*') ? 'active' : '' }}" href="{{ route('ai-sandbox.index') }}">Sandbox IA</a>
                    @if(auth()->user()->isAdmin())
                        <hr class="border-secondary">
                        <a class="rounded px-3 py-2 {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">Configuracion</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('menu-options.*') ? 'active' : '' }}" href="{{ route('menu-options.index') }}">Menu WhatsApp</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('derivation-areas.*') ? 'active' : '' }}" href="{{ route('derivation-areas.index') }}">Areas</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('conversations.*') ? 'active' : '' }}" href="{{ route('conversations.index') }}">Conversaciones</a>
                        <hr class="border-secondary">
                        <a class="rounded px-3 py-2 {{ request()->routeIs('knowledge-search.*') ? 'active' : '' }}" href="{{ route('knowledge-search.index') }}">Buscador IA</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('intentions.*') ? 'active' : '' }}" href="{{ route('intentions.index') }}">Intenciones</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('knowledge-categories.*') ? 'active' : '' }}" href="{{ route('knowledge-categories.index') }}">Categorias conocimiento</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('knowledge-documents.*') ? 'active' : '' }}" href="{{ route('knowledge-documents.index') }}">Documentos</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('knowledge-faqs.*') ? 'active' : '' }}" href="{{ route('knowledge-faqs.index') }}">FAQs</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Productos</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('promotions.*') ? 'active' : '' }}" href="{{ route('promotions.index') }}">Promociones</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('raffles.*') ? 'active' : '' }}" href="{{ route('raffles.index') }}">Sorteos</a>
                        <hr class="border-secondary">
                        <a class="rounded px-3 py-2 {{ request()->routeIs('ai-providers.*') ? 'active' : '' }}" href="{{ route('ai-providers.index') }}">Proveedores IA</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('ai-prompts.*') ? 'active' : '' }}" href="{{ route('ai-prompts.index') }}">Prompts IA</a>
                        <a class="rounded px-3 py-2 {{ request()->routeIs('ai-interactions.*') ? 'active' : '' }}" href="{{ route('ai-interactions.index') }}">Historial IA</a>
                    @endif
                </nav>
            </aside>
        @endauth

        <main class="@auth col-md-9 col-xl-10 @else col-12 @endauth p-4">
            @auth
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">@yield('title', 'Panel administrativo')</h1>
                        <div class="text-muted small">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-dark btn-sm">Salir</button>
                    </form>
                </div>
            @endauth

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Revisa los datos:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
