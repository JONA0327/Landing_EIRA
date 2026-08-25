@extends('admin.layout')

@section('titulo', 'Catálogo')

@section('content')

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
            Faltan datos de conexión al CRM. Revisa la pestaña
            <a href="{{ route('admin.configuracion') }}" class="underline font-medium">Configuración</a> para ver exactamente qué falta.
        </div>
    @endunless

    @unless ($groqConfigurado)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl px-4 py-3 mb-4">
            Falta configurar Groq — sin eso no se generan descripciones simplificadas automáticas (pero puedes seguir escribiéndolas a mano abajo).
            Revisa la pestaña <a href="{{ route('admin.configuracion') }}" class="underline font-medium">Configuración</a>.
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

@endsection
