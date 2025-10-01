<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reserva_servicio extends Model
{
    use HasFactory;

    protected $table = 'reservas_servicios';
    protected $primaryKey = 'id_reserva_servicio';

    protected $fillable = [
        'id_reserva',
        'id_servicio',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'fecha_servicio',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'fecha_servicio' => 'date',
    ];

    // Relaciones
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function servicio()
    {
        return $this->belongsTo(servicio::class, 'id_servicio', 'id_servicio');
    }
}
