<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Panel 4Life — Catálogo y Regiones</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased text-gray-900">

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-gray-900">Panel 4Life</h1>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-800 transition-colors">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8 space-y-10">

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── REGIONES ─────────────────────────────────────────────────── --}}
        <section>
            <div class="mb-4">
                <h2 class="text-lg font-bold text-gray-900">Regiones</h2>
                <p class="text-sm text-gray-500">WhatsApp, código 4Life, tienda y dirección de cada país — la landing los muestra según el país que elija el visitante.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($regiones as $region)
                    <form method="POST" action="{{ route('admin.regiones.update', $region) }}"
                          class="bg-white rounded-2xl border border-gray-200 p-5 space-y-3">
                        @csrf
                        @method('PUT')

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/flags/' . $region->slug . '.png') }}" alt="{{ $region->nombre }}"
                                     class="w-7 h-5 object-cover rounded shadow-sm ring-1 ring-black/5">
                                <h3 class="font-semibold text-gray-900">{{ $region->nombre }}</h3>
                            </div>
                            <label class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                                <input type="checkbox" name="activo" value="1" {{ $region->activo ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                Activa en la landing
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Números de WhatsApp (uno por agente de ventas — se elige uno al azar en cada mensaje)</label>
                            <div class="whatsapp-numeros-list space-y-2">
                                @forelse ($region->whatsappNumbers as $numero)
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="whatsapp_numeros[]" value="{{ $numero->numero }}"
                                               placeholder="521XXXXXXXXXX"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <button type="button" onclick="this.parentElement.remove()"
                                                class="text-red-500 hover:text-red-700 text-lg leading-none px-1.5" title="Quitar">×</button>
                                    </div>
                                @empty
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="whatsapp_numeros[]" value=""
                                               placeholder="521XXXXXXXXXX"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <button type="button" onclick="this.parentElement.remove()"
                                                class="text-red-500 hover:text-red-700 text-lg leading-none px-1.5" title="Quitar">×</button>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" onclick="agregarNumeroWhatsapp(this)"
                                    class="mt-2 text-xs text-emerald-700 hover:text-emerald-800 font-medium">+ Agregar número</button>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Código de referido 4Life</label>
                            <input type="text" name="codigo_4life" value="{{ old('codigo_4life', $region->codigo_4life) }}"
                                   placeholder="Ej. 12345678"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">URL de la tienda 4Life de este país</label>
                            <input type="url" name="tienda_url" value="{{ old('tienda_url', $region->tienda_url) }}"
                                   placeholder="https://pais.4life.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Dirección completa (sección Ubicación)</label>
                            <textarea name="direccion" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('direccion', $region->direccion) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Dirección corta (footer)</label>
                                <input type="text" name="direccion_corta" value="{{ old('direccion_corta', $region->direccion_corta) }}"
                                       placeholder="Ej. Apodaca, NL"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Lat</label>
                                    <input type="text" name="lat" value="{{ old('lat', $region->lat) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Lng</label>
                                    <input type="text" name="lng" value="{{ old('lng', $region->lng) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-colors">
                            Guardar {{ $region->nombre }}
                        </button>
                    </form>
                @endforeach
            </div>
        </section>

        {{-- ── CATÁLOGO ─────────────────────────────────────────────────── --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Catálogo</h2>
                    <p class="text-sm text-gray-500">Elige en qué país(es) se muestra cada producto y en qué orden.</p>
                    <p class="text-xs text-gray-400 mt-0.5">Un producto sin ninguna región marcada se pre-marca solo según el campo "país" del catálogo al sincronizar — en cuanto tú marques/desmarques algo, un re-sync ya no lo vuelve a tocar.</p>
                </div>
                <form method="POST" action="{{ route('admin.sync') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Sincronizar con CRM
                    </button>
                </form>
            </div>

            @unless ($crmConfigurado)
                <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl px-4 py-3 mb-4">
                    Faltan datos de conexión al CRM en el <code class="bg-amber-100 px-1 rounded">.env</code>:
                    <code class="bg-amber-100 px-1 rounded">CRM_BASE_URL</code>,
                    <code class="bg-amber-100 px-1 rounded">CRM_TENANT_SLUG</code>,
                    <code class="bg-amber-100 px-1 rounded">CRM_CATALOG_API_KEY</code> y
                    <code class="bg-amber-100 px-1 rounded">CRM_CATALOG_MODULE</code>.
                </div>
            @endunless

            @unless ($groqConfigurado)
                <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl px-4 py-3 mb-4">
                    Falta <code class="bg-amber-100 px-1 rounded">GROQ_API_KEY</code> en el <code class="bg-amber-100 px-1 rounded">.env</code> —
                    sin eso no se generan descripciones simplificadas automáticas (pero puedes seguir escribiéndolas a mano abajo).
                </div>
            @endunless

            @if ($productos->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                    <p class="text-gray-500 text-sm">Aún no hay productos sincronizados. Pulsa "Sincronizar con CRM" para traerlos.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.productos-regiones') }}" class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 text-gray-500 text-left text-xs uppercase tracking-wide">
                                    <th class="px-5 py-3 font-medium">Producto</th>
                                    <th class="px-5 py-3 font-medium" style="min-width: 260px">Descripción simplificada (IA)</th>
                                    <th class="px-5 py-3 font-medium">Orden</th>
                                    @foreach ($regiones as $region)
                                        <th class="px-3 py-3 font-medium text-center" title="{{ $region->nombre }}">
                                            <img src="{{ asset('images/flags/' . $region->slug . '.png') }}" alt="{{ $region->nombre }}"
                                                 class="w-6 h-4 object-cover rounded shadow-sm ring-1 ring-black/5 inline-block">
                                        </th>
                                    @endforeach
                                    <th class="px-5 py-3 font-medium">Datos del CRM</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($productos as $producto)
                                    @php $regionIds = $producto->regions->pluck('id')->all(); @endphp
                                    <tr>
                                        <td class="px-5 py-4 align-top">
                                            <p class="font-semibold text-gray-900">{{ $producto->nombre }}</p>
                                            @if ($producto->precio)
                                                <p class="text-xs text-emerald-700 font-medium mt-0.5">{{ $producto->precio }}</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 align-top">
                                            <textarea name="descripcion_simple[{{ $producto->id }}]" rows="4"
                                                      placeholder="{{ $producto->descripcion ? 'Vacío = se genera con IA al guardar' : 'Este producto no tiene descripción original' }}"
                                                      class="w-full px-2.5 py-2 border border-gray-300 rounded-lg text-xs leading-relaxed">{{ old('descripcion_simple.' . $producto->id, $producto->descripcion_simple) }}</textarea>
                                            @if ($producto->descripcion_simple_generada_en)
                                                <p class="text-[10px] text-gray-400 mt-1">Generado {{ $producto->descripcion_simple_generada_en->diffForHumans() }}</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 align-top">
                                            <input type="number" name="orden[{{ $producto->id }}]" value="{{ $producto->orden }}"
                                                   class="w-16 px-2 py-1.5 border border-gray-300 rounded-lg text-sm">
                                        </td>
                                        @foreach ($regiones as $region)
                                            <td class="px-3 py-4 align-top text-center">
                                                <input type="checkbox"
                                                       name="regiones[{{ $producto->id }}][]" value="{{ $region->id }}"
                                                       {{ in_array($region->id, $regionIds, true) ? 'checked' : '' }}
                                                       class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                                       title="Mostrar en {{ $region->nombre }}">
                                            </td>
                                        @endforeach
                                        <td class="px-5 py-4 align-top">
                                            <details class="text-xs text-gray-500">
                                                <summary class="cursor-pointer text-gray-400 hover:text-gray-600">ver datos crudos</summary>
                                                <pre class="mt-2 bg-gray-50 rounded-lg p-2 overflow-x-auto max-w-xs">{{ json_encode($producto->datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </details>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                        <button type="submit"
                                class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-colors">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            @endif
        </section>

    </main>

    <script>
        function agregarNumeroWhatsapp(boton) {
            const lista = boton.previousElementSibling;
            const fila = document.createElement('div');
            fila.className = 'flex items-center gap-2';
            fila.innerHTML = '<input type="text" name="whatsapp_numeros[]" placeholder="521XXXXXXXXXX" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">'
                + '<button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 text-lg leading-none px-1.5" title="Quitar">×</button>';
            lista.appendChild(fila);
            fila.querySelector('input').focus();
        }
    </script>

</body>
</html>
