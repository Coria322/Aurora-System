<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';
    protected $primaryKey = 'id_reporte';

    protected $fillable = [
        'Habitaciones_ocupadas',
        'Habitaciones_disponibles',
        'tasa_ocupacion',
        'ingresos_totales',
    ];

    protected $casts = [
        'tasa_ocupacion' => 'decimal:2',
        'ingresos_totales' => 'decimal:2',
    ];
}
