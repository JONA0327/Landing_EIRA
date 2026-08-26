<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Guarda cada agente (WhatsApp + su tienda + su código 4Life + su ciudad
     * corta para el footer), y los valores generales (código/tienda/ciudad)
     * de respaldo de UNA región. Se llama una vez por cada tarjeta de región
     * del panel (cada una es su propio <form>), así un error de validación
     * en una no afecta a las demás.
     *
     * Los agentes se reemplazan por completo con lo que llegue en
     * whatsapp_numeros[] / whatsapp_tiendas[] / whatsapp_codigos[] /
     * whatsapp_direcciones_cortas[] — todos van en el MISMO índice por fila
     * (el JS del panel los agrupa en la misma fila removible), así quedan
     * correctamente emparejados aunque se borren filas de en medio. La
     * landing rota cuál está "de turno" cada 10 minutos — ver
     * Region::agenteActivo().
     */
    public function update(Request $request, Region $region): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_numeros'              => ['array'],
            'whatsapp_numeros.*'            => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'whatsapp_tiendas'               => ['array'],
            'whatsapp_tiendas.*'             => ['nullable', 'url', 'max:255'],
            'whatsapp_codigos'               => ['array'],
            'whatsapp_codigos.*'             => ['nullable', 'string', 'max:50'],
            'whatsapp_direcciones_cortas'    => ['array'],
            'whatsapp_direcciones_cortas.*'  => ['nullable', 'string', 'max:100'],
            'codigo_4life'                   => ['nullable', 'string', 'max:50'],
            'tienda_url'                     => ['nullable', 'url', 'max:255'],
            'direccion_corta'                => ['nullable', 'string', 'max:100'],
            'activo'                         => ['nullable', 'boolean'],
        ], [
            'whatsapp_numeros.*.regex' => 'Solo dígitos, sin "+", espacios ni guiones (ej. 528116642343).',
        ]);

        $numerosInput    = $validated['whatsapp_numeros'] ?? [];
        $tiendasInput    = $validated['whatsapp_tiendas'] ?? [];
        $codigosInput    = $validated['whatsapp_codigos'] ?? [];
        $direccionCInput = $validated['whatsapp_direcciones_cortas'] ?? [];

        $agentes = collect($numerosInput)
            ->map(fn ($numero, $i) => [
                'numero'          => trim((string) $numero),
                'tienda_url'      => trim((string) ($tiendasInput[$i] ?? '')) ?: null,
                'codigo_4life'    => trim((string) ($codigosInput[$i] ?? '')) ?: null,
                'direccion_corta' => trim((string) ($direccionCInput[$i] ?? '')) ?: null,
            ])
            ->filter(fn ($a) => $a['numero'] !== '')
            ->unique('numero')
            ->values();

        unset(
            $validated['whatsapp_numeros'], $validated['whatsapp_tiendas'],
            $validated['whatsapp_codigos'], $validated['whatsapp_direcciones_cortas'],
        );
        $validated['activo'] = $request->boolean('activo');

        // Limpia bytes UTF-8 inválidos (typos de codificación, copy-paste raro)
        // antes de guardar — si no, json_encode() de estos datos en la landing
        // pública puede fallar y romper todo el script de la página.
        foreach (['codigo_4life', 'direccion_corta'] as $campo) {
            if (isset($validated[$campo])) {
                $validated[$campo] = mb_convert_encoding($validated[$campo], 'UTF-8', 'UTF-8');
            }
        }

        $region->update($validated);

        $region->whatsappNumbers()->delete();
        foreach ($agentes as $agente) {
            $region->whatsappNumbers()->create($agente);
        }

        return back()->with('success', "Región {$region->nombre} actualizada.");
    }
}
