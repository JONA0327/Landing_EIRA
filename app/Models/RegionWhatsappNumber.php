<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un número de WhatsApp de un agente de ventas de una región, con su propia
 * tienda 4Life (su código de referido). Una región puede tener varios — la
 * landing va rotando cuál está "activo" cada 10 minutos (ver
 * Region::agenteActivo()), y ese mismo agente recibe tanto el WhatsApp como
 * el clic de "Comprar" durante esa ventana, para que la venta quede en su
 * tienda y no en la de otro.
 */
class RegionWhatsappNumber extends Model
{
    protected $fillable = ['region_id', 'numero', 'tienda_url'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
