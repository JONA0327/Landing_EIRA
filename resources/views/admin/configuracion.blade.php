@extends('admin.layout')

@section('titulo', 'Configuración')

@section('content')

    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900">Configuración</h2>
        <p class="text-sm text-gray-500">Edita aquí las conexiones externas — tiene prioridad sobre el <code class="bg-gray-100 px-1 rounded">.env</code> del servidor y se aplica al instante, sin redeploy.</p>
    </div>

    <form method="POST" action="{{ route('admin.configuracion.guardar') }}" class="space-y-6">
        @csrf

        @foreach ($campos as $nombreGrupo => $camposDelGrupo)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">{{ $nombreGrupo }}</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($camposDelGrupo as $campo)
                        @php
                            $configurado = $campo['valor'] !== null && $campo['valor'] !== '';
                            $mostrarComoPlaceholder = $campo['secreto'] && $configurado
                                ? str_repeat('•', max(0, mb_strlen((string) $campo['valor']) - 4)) . mb_substr((string) $campo['valor'], -4)
                                : $campo['placeholder'];
                        @endphp
                        <div class="px-5 py-4 flex items-start gap-4">
                            <div class="w-56 flex-shrink-0 pt-2">
                                <p class="text-sm font-medium text-gray-800">{{ $campo['label'] }}</p>
                                <code class="text-[11px] text-gray-400">{{ $campo['env'] }}</code>
                                <div class="mt-1">
                                    @if ($configurado)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700">✓ Configurado</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700">✕ No configurado</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="text"
                                       name="{{ $campo['key'] }}"
                                       value="{{ old($campo['key'], $campo['secreto'] ? '' : $campo['valor']) }}"
                                       placeholder="{{ $mostrarComoPlaceholder }}"
                                       autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono">
                                @if ($campo['secreto'])
                                    <label class="inline-flex items-center gap-1.5 text-xs text-gray-500 mt-1.5">
                                        <input type="checkbox" name="borrar[]" value="{{ $campo['key'] }}"
                                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        Borrar esta clave (déjalo sin marcar si solo quieres dejarla como está)
                                    </label>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit"
                class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-colors">
            Guardar configuración
        </button>
    </form>

    {{-- ── Solo lectura — riesgo de auto-bloqueo si se editan mal ─────────── --}}
    <div class="mt-8 bg-gray-100 rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Panel admin</h3>
            <p class="text-xs text-gray-500 mt-0.5">Solo lectura — cambiar esto mal te puede dejar fuera del panel, así que se edita únicamente en el <code class="bg-white px-1 rounded border border-gray-200">.env</code> del servidor.</p>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-200">
                @foreach ($soloLectura as $campo)
                    <tr>
                        <td class="px-5 py-3.5 align-top" style="width: 260px">
                            <p class="text-sm font-medium text-gray-800">{{ $campo['label'] }}</p>
                            <code class="text-[11px] text-gray-400">{{ $campo['env'] }}</code>
                        </td>
                        <td class="px-5 py-3.5 align-top font-mono text-xs text-gray-600 break-all">{{ $campo['valor'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 bg-gray-100 rounded-2xl border border-gray-200 px-5 py-4 text-xs text-gray-500">
        Los campos de arriba se guardan en la base de datos y tienen prioridad sobre el <code class="bg-white px-1 rounded border border-gray-200">.env</code> — si dejas uno vacío (o marcas "Borrar" en un secreto), vuelve a usarse lo que haya en el <code class="bg-white px-1 rounded border border-gray-200">.env</code> del servidor como respaldo.
    </div>

@endsection
