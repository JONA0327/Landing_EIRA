<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un número de WhatsApp de un agente de ventas de una región. Una región
 * puede tener varios — al mandar un mensaje, la landing elige uno al azar
 * entre los de esa región (para repartir la carga entre agentes).
 */
class RegionWhatsappNumber extends Model
{
    protected $fillable = ['region_id', 'numero'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
