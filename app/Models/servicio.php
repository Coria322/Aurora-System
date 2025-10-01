<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'nombre_servicio',
        'descripcion',
        'precio',
        'tipo_servicio',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function reservas()
    {
        return $this->hasMany(reserva_servicio::class, 'id_servicio', 'id_servicio');
    }
}
