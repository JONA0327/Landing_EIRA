@extends('admin.layout')

@section('titulo', 'Regiones')

@section('content')

    <div class="mb-6">
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

@endsection

@section('scripts')
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
@endsection
