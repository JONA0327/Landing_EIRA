@extends('admin.layout')

@section('titulo', 'Configuración')

@section('content')

    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900">Configuración</h2>
        <p class="text-sm text-gray-500">Estado de las conexiones externas — se leen directo del <code class="bg-gray-100 px-1 rounded">.env</code> del servidor. Esta pantalla es de solo lectura por seguridad: las claves se editan en el <code class="bg-gray-100 px-1 rounded">.env</code>, nunca aquí.</p>
    </div>

    <div class="space-y-6">
        @foreach ($grupos as $grupo)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">{{ $grupo['titulo'] }}</h3>
                    @if (!empty($grupo['descripcion']))
                        <p class="text-xs text-gray-500 mt-0.5">{{ $grupo['descripcion'] }}</p>
                    @endif
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($grupo['campos'] as $campo)
                            @php
                                $valor = $campo['valor'];
                                $configurado = $valor !== null && $valor !== '';
                                $mostrar = $configurado
                                    ? ($campo['secreto']
                                        ? str_repeat('•', max(0, mb_strlen((string) $valor) - 4)) . mb_substr((string) $valor, -4)
                                        : $valor)
                                    : null;
                            @endphp
                            <tr>
                                <td class="px-5 py-3.5 align-top" style="width: 260px">
                                    <p class="text-sm font-medium text-gray-800">{{ $campo['label'] }}</p>
                                    <code class="text-[11px] text-gray-400">{{ $campo['env'] }}</code>
                                </td>
                                <td class="px-5 py-3.5 align-top font-mono text-xs text-gray-600 break-all">
                                    {{ $mostrar ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 align-top text-right" style="width: 160px">
                                    @if ($configurado)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                            ✓ Configurado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                                            ✕ No configurado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <div class="mt-6 bg-gray-100 rounded-2xl border border-gray-200 px-5 py-4 text-xs text-gray-500">
        Para cambiar cualquiera de estos valores en producción: edita el archivo <code class="bg-white px-1 rounded border border-gray-200">.env</code> en el servidor y corre
        <code class="bg-white px-1 rounded border border-gray-200">php artisan config:clear</code> (o <code class="bg-white px-1 rounded border border-gray-200">bash deploy.sh</code>) para que tome el cambio.
    </div>

@endsection
