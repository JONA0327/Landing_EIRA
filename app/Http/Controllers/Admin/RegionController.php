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
     * corta para el footer) de UNA región. Se llama una vez por cada tarjeta
     * de región del panel (cada una es su propio <form>), así un error de
     * validación en una no afecta a las demás.
     *
     * El código/tienda/ciudad "generales" del país ya no se editan desde
     * aquí — cada uno vive en su agente para que la venta quede siempre
     * atribuida a alguien específico, no a un link genérico del país; si un
     * valor general quedó de antes (ej. México lo trae del seeder), sigue
     * funcionando como respaldo (ver Region::tiendaActiva() etc.) hasta que
     * un agente ponga el suyo propio.
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

        $validated = ['activo' => $request->boolean('activo')];

        $region->update($validated);

        $region->whatsappNumbers()->delete();
        foreach ($agentes as $agente) {
            $region->whatsappNumbers()->create($agente);
        }

        return back()->with('success', "Región {$region->nombre} actualizada.");
    }
}
