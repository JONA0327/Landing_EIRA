<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Guarda WhatsApp(s), código 4Life, tienda y dirección de UNA región.
     * Se llama una vez por cada tarjeta de región del panel (cada una es su
     * propio <form>), así un error de validación en una no afecta a las demás.
     *
     * Los números de WhatsApp se reemplazan por completo con lo que llegue en
     * whatsapp_numeros[] (uno por agente de ventas de esa región) — la landing
     * elige uno al azar entre ellos cada vez que alguien manda un mensaje.
     */
    public function update(Request $request, Region $region): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_numeros'   => ['array'],
            'whatsapp_numeros.*' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'codigo_4life'       => ['nullable', 'string', 'max:50'],
            'tienda_url'         => ['nullable', 'url', 'max:255'],
            'direccion'          => ['nullable', 'string', 'max:500'],
            'direccion_corta'    => ['nullable', 'string', 'max:100'],
            'lat'                => ['nullable', 'numeric', 'between:-90,90'],
            'lng'                => ['nullable', 'numeric', 'between:-180,180'],
            'activo'             => ['nullable', 'boolean'],
        ], [
            'whatsapp_numeros.*.regex' => 'Solo dígitos, sin "+", espacios ni guiones (ej. 528116642343).',
        ]);

        $numeros = collect($validated['whatsapp_numeros'] ?? [])
            ->map(fn ($n) => trim((string) $n))
            ->filter(fn ($n) => $n !== '')
            ->unique()
            ->values();

        unset($validated['whatsapp_numeros']);
        $validated['activo'] = $request->boolean('activo');

        // Limpia bytes UTF-8 inválidos (typos de codificación, copy-paste raro)
        // antes de guardar — si no, json_encode() de estos datos en la landing
        // pública puede fallar y romper todo el script de la página.
        foreach (['codigo_4life', 'direccion', 'direccion_corta'] as $campo) {
            if (isset($validated[$campo])) {
                $validated[$campo] = mb_convert_encoding($validated[$campo], 'UTF-8', 'UTF-8');
            }
        }

        $region->update($validated);

        $region->whatsappNumbers()->delete();
        foreach ($numeros as $numero) {
            $region->whatsappNumbers()->create(['numero' => $numero]);
        }

        return back()->with('success', "Región {$region->nombre} actualizada.");
    }
}
