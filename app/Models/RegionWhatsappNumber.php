<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un agente de ventas de una región: su número de WhatsApp, su propia tienda
 * 4Life, su código de referido y su ubicación (dirección + mapa). Una región
 * puede tener varios — la landing va rotando cuál está "activo" cada 10
 * minutos (ver Region::agenteActivo()), y ese mismo agente recibe el
 * WhatsApp, el clic de "Comprar", el código Y la ubicación que se muestran,
 * todo junto, para que la venta quede en su tienda y no en la de otro.
 */
class RegionWhatsappNumber extends Model
{
    protected $fillable = [
        'region_id', 'numero', 'tienda_url', 'codigo_4life',
        'direccion', 'direccion_corta', 'lat', 'lng',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
