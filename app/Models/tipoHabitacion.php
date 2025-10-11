<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    use HasFactory;

    protected $table = 'tipo_habitaciones';
    protected $primaryKey = 'id_tipo_habitacion';

    public $timestamps = false;



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

    #[Scope]
    /**
     * Summary of Activas
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    protected function Activas(Builder $query){
        $query -> where('activo', true);
    }

    #[Scope] 
    protected function Inactivas(Builder $query){
        $query -> whereNot('activo', true);
    }
}
