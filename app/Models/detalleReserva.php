<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReserva extends Model
{
    use HasFactory;

    protected $table = 'detalle_reservas';
    protected $primaryKey = 'id_detalle_reserva';

    protected $fillable = [
        'id_reserva',
        'id_habitacion',
        'fecha_inicio',
        'fecha_fin',
        'precio_noche',
        'numero_noches',
        'subtotal',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'precio_noche' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion', 'id_habitacion');
    }
}
