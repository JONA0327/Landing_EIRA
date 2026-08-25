<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Panel 4Life — @yield('titulo')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased text-gray-900">

    <div class="flex min-h-screen">
        {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
        <aside class="w-60 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col">
            <div class="px-5 py-5 border-b border-gray-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 leading-tight">Panel 4Life</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                @php
                    $enlaces = [
                        ['ruta' => 'admin.regiones',      'activo' => ['admin.dashboard', 'admin.regiones'], 'icono' => '🌎', 'texto' => 'Regiones'],
                        ['ruta' => 'admin.catalogo',       'activo' => ['admin.catalogo'],                    'icono' => '📦', 'texto' => 'Catálogo'],
                        ['ruta' => 'admin.configuracion',  'activo' => ['admin.configuracion'],               'icono' => '⚙️', 'texto' => 'Configuración'],
                    ];
                @endphp
                @foreach ($enlaces as $enlace)
                    <a href="{{ route($enlace['ruta']) }}"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs($enlace['activo']) ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <span class="text-base leading-none">{{ $enlace['icono'] }}</span>
                        {{ $enlace['texto'] }}
                    </a>
                @endforeach
            </nav>

            <div class="px-3 py-4 border-t border-gray-200">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── CONTENIDO ────────────────────────────────────────────────── --}}
        <main class="flex-1 px-8 py-8 max-w-6xl">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')

</body>
</html>
