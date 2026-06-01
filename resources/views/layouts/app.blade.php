<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="container-fluid app-shell">
    <div class="row">
        @auth
            @php
                $isAdmin = auth()->user()->isAdmin();
                $sections = [
                    [
                        'id' => 'inicio',
                        'label' => 'Inicio',
                        'icon' => 'IN',
                        'active' => request()->routeIs('dashboard'),
                        'items' => [
                            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                        ],
                    ],
                    [
                        'id' => 'atencion',
                        'label' => 'Atencion',
                        'icon' => 'AT',
                        'active' => request()->routeIs('clients.*') || request()->routeIs('chat.*') || request()->routeIs('conversations.*'),
                        'items' => array_filter([
                            ['label' => 'Clientes', 'route' => 'clients.index', 'active' => 'clients.*'],
                            ['label' => 'Chat interno', 'route' => 'chat.index', 'active' => 'chat.*'],
                            $isAdmin ? ['label' => 'Conversaciones', 'route' => 'conversations.index', 'active' => 'conversations.*'] : null,
                        ]),
                    ],
                    [
                        'id' => 'whatsapp',
                        'label' => 'WhatsApp',
                        'icon' => 'WA',
                        'active' => request()->routeIs('menu-options.*') || request()->routeIs('whatsapp.*'),
                        'items' => array_filter([
                            $isAdmin && Route::has('menu-options.index') ? ['label' => 'Menu WhatsApp', 'route' => 'menu-options.index', 'active' => 'menu-options.*'] : null,
                            $isAdmin && Route::has('whatsapp.settings') ? ['label' => 'Configuracion WhatsApp', 'route' => 'whatsapp.settings', 'active' => 'whatsapp.settings'] : null,
                            Route::has('whatsapp.inbox') ? ['label' => 'Bandeja WhatsApp', 'route' => 'whatsapp.inbox', 'active' => ['whatsapp.inbox', 'whatsapp.conversations.*']] : null,
                            Route::has('whatsapp.templates.index') ? ['label' => 'Plantillas WhatsApp', 'route' => 'whatsapp.templates.index', 'active' => 'whatsapp.templates.*'] : null,
                        ]),
                    ],
                    [
                        'id' => 'ia',
                        'label' => 'Inteligencia Artificial',
                        'icon' => 'IA',
                        'active' => request()->routeIs('ai-sandbox.*') || request()->routeIs('knowledge-search.*') || request()->routeIs('ai-providers.*') || request()->routeIs('ai-prompts.*') || request()->routeIs('ai-interactions.*'),
                        'items' => array_filter([
                            ['label' => 'Sandbox IA', 'route' => 'ai-sandbox.index', 'active' => 'ai-sandbox.*'],
                            $isAdmin ? ['label' => 'Buscador IA', 'route' => 'knowledge-search.index', 'active' => 'knowledge-search.*'] : null,
                            $isAdmin ? ['label' => 'Proveedores IA', 'route' => 'ai-providers.index', 'active' => 'ai-providers.*'] : null,
                            $isAdmin ? ['label' => 'Prompts IA', 'route' => 'ai-prompts.index', 'active' => 'ai-prompts.*'] : null,
                            $isAdmin ? ['label' => 'Historial IA', 'route' => 'ai-interactions.index', 'active' => 'ai-interactions.*'] : null,
                        ]),
                    ],
                    [
                        'id' => 'conocimiento',
                        'label' => 'Base de Conocimiento',
                        'icon' => 'BC',
                        'active' => request()->routeIs('knowledge-categories.*') || request()->routeIs('knowledge-documents.*') || request()->routeIs('knowledge-faqs.*') || request()->routeIs('products.*') || request()->routeIs('promotions.*') || request()->routeIs('raffles.*'),
                        'items' => $isAdmin ? [
                            ['label' => 'Categorias conocimiento', 'route' => 'knowledge-categories.index', 'active' => 'knowledge-categories.*'],
                            ['label' => 'Documentos', 'route' => 'knowledge-documents.index', 'active' => 'knowledge-documents.*'],
                            ['label' => 'FAQs', 'route' => 'knowledge-faqs.index', 'active' => 'knowledge-faqs.*'],
                            ['label' => 'Productos', 'route' => 'products.index', 'active' => 'products.*'],
                            ['label' => 'Promociones', 'route' => 'promotions.index', 'active' => 'promotions.*'],
                            ['label' => 'Sorteos', 'route' => 'raffles.index', 'active' => 'raffles.*'],
                        ] : [],
                    ],
                    [
                        'id' => 'comercial',
                        'label' => 'Comercial',
                        'icon' => 'CO',
                        'active' => request()->routeIs('commercial.*') || request()->routeIs('leads.*') || request()->routeIs('intentions.*') || request()->routeIs('lead-reports.*'),
                        'items' => array_filter([
                            ['label' => 'Comercial', 'route' => 'commercial.dashboard', 'active' => 'commercial.*'],
                            ['label' => 'Leads', 'route' => 'leads.index', 'active' => 'leads.*'],
                            $isAdmin ? ['label' => 'Intenciones', 'route' => 'intentions.index', 'active' => 'intentions.*'] : null,
                            $isAdmin && Route::has('lead-reports.index') ? ['label' => 'Reportes leads', 'route' => 'lead-reports.index', 'active' => 'lead-reports.*'] : null,
                        ]),
                    ],
                    [
                        'id' => 'administracion',
                        'label' => 'Administracion',
                        'icon' => 'AD',
                        'active' => request()->routeIs('settings.*') || request()->routeIs('derivation-areas.*') || request()->routeIs('knowledge-feedback.*') || request()->routeIs('unanswered-questions.*'),
                        'items' => array_filter([
                            $isAdmin ? ['label' => 'Configuracion', 'route' => 'settings.edit', 'active' => 'settings.*'] : null,
                            $isAdmin ? ['label' => 'Areas', 'route' => 'derivation-areas.index', 'active' => 'derivation-areas.*'] : null,
                            $isAdmin ? ['label' => 'Feedback', 'route' => 'knowledge-feedback.index', 'active' => 'knowledge-feedback.*'] : null,
                            $isAdmin ? ['label' => 'Preguntas pendientes', 'route' => 'unanswered-questions.index', 'active' => 'unanswered-questions.*'] : null,
                            Route::has('users.index') ? ['label' => 'Usuarios', 'route' => 'users.index', 'active' => 'users.*'] : null,
                        ]),
                    ],
                ];
            @endphp

            <aside class="col-md-3 col-xl-2 sidebar p-3">
                <div class="brand-panel mb-3">
                    <div class="brand-mark">VIANKA GOLD</div>
                    <div class="brand-subtitle">WhatsApp Business CRM</div>
                </div>

                <div class="accordion sidebar-accordion" id="sidebarAccordion">
                    @foreach($sections as $section)
                        @continue(empty($section['items']))
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $section['active'] ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#section-{{ $section['id'] }}" aria-expanded="{{ $section['active'] ? 'true' : 'false' }}" aria-controls="section-{{ $section['id'] }}">
                                    <span class="section-icon">{{ $section['icon'] }}</span>
                                    <span>{{ $section['label'] }}</span>
                                </button>
                            </h2>
                            <div id="section-{{ $section['id'] }}" class="accordion-collapse collapse {{ $section['active'] ? 'show' : '' }}" data-bs-parent="#sidebarAccordion">
                                <div class="accordion-body">
                                    @foreach($section['items'] as $item)
                                        @continue(! Route::has($item['route']))
                                        @php $activeRules = (array) $item['active']; @endphp
                                        <a class="sidebar-link {{ request()->routeIs(...$activeRules) ? 'active' : '' }}" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="sidebar-user mt-4">
                    <div class="small text-white-50">Usuario</div>
                    <div class="fw-semibold text-white">{{ auth()->user()->name }}</div>
                    <div class="small text-white-50 mb-3">{{ ucfirst(auth()->user()->role) }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-whatsapp w-100 btn-sm">Salir</button>
                    </form>
                </div>
            </aside>
        @endauth

        <main class="@auth col-md-9 col-xl-10 @else col-12 @endauth app-main p-4">
            @auth
                <div class="topbar d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">@yield('title', 'Panel administrativo')</h1>
                        <div class="text-muted small">Gestion de atencion, IA y ventas por WhatsApp</div>
                    </div>
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
