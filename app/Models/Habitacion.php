<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';
    protected $primaryKey = 'id_habitacion';

    protected $fillable = [
        'numero_habitacion',
        'id_tipo_habitacion',
        'estado',
        'piso',
        'descripcion',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    // Relaciones
    public function tipoHabitacion()
    {
        return $this->belongsTo(TipoHabitacion::class, 'id_tipo_habitacion', 'id_tipo_habitacion');
    }

    public function detalleReservas()
    {
        return $this->hasMany(DetalleReserva::class, 'id_habitacion', 'id_habitacion');
    }
}
