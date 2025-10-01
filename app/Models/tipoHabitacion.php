<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    use HasFactory;

    protected $table = 'tipo_habitaciones';
    protected $primaryKey = 'id_tipo_habitacion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'capacidad_maxima',
        'precio_noche',
        'servicios_incluidos',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio_noche' => 'decimal:2',
    ];

    // Relaciones
    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class, 'id_tipo_habitacion', 'id_tipo_habitacion');
    }
}
