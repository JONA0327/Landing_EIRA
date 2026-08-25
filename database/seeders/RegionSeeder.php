<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

/**
 * Siembra las 4 regiones fijas del negocio. México trae los datos que ya
 * estaban fijos en el diseño original de la landing (EIRA, Apodaca NL) —
 * las otras quedan con teléfono/dirección vacíos hasta que el admin los
 * llene desde el panel (Regiones).
 */
class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regiones = [
            [
                'slug' => 'mx', 'nombre' => 'México', 'bandera' => '🇲🇽', 'orden' => 1,
                'whatsapp_numero' => '528116642343',
                'codigo_4life'    => null,
                'tienda_url'      => 'https://mexico.4life.com',
                'direccion'       => 'Calle Los Amarantos 201, Col. Los Amarantos, Apodaca, Nuevo León 66613',
                'direccion_corta' => 'Apodaca, NL',
                'lat' => 25.822388160195274, 'lng' => -100.25555491449096,
            ],
            [
                'slug' => 'co', 'nombre' => 'Colombia', 'bandera' => '🇨🇴', 'orden' => 2,
                'whatsapp_numero' => null,
                'codigo_4life'    => null,
                'tienda_url'      => null,
                'direccion'       => null,
                'direccion_corta' => null,
                'lat' => 4.710988600000, 'lng' => -74.072092000000,
            ],
            [
                'slug' => 'us', 'nombre' => 'Estados Unidos', 'bandera' => '🇺🇸', 'orden' => 3,
                'whatsapp_numero' => null,
                'codigo_4life'    => null,
                'tienda_url'      => null,
                'direccion'       => null,
                'direccion_corta' => null,
                'lat' => null, 'lng' => null,
            ],
            [
                'slug' => 'cl', 'nombre' => 'Chile', 'bandera' => '🇨🇱', 'orden' => 4,
                'whatsapp_numero' => null,
                'codigo_4life'    => null,
                'tienda_url'      => null,
                'direccion'       => null,
                'direccion_corta' => null,
                'lat' => -33.447487000000, 'lng' => -70.673676000000,
            ],
        ];

        foreach ($regiones as $region) {
            Region::updateOrCreate(['slug' => $region['slug']], $region);
        }

        $this->command?->info('Regiones listas: ' . implode(', ', array_column($regiones, 'nombre')));
    }
}
