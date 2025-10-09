<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';

    public $timestamps = false;

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
    public function reservaServicios()
    {
        return $this->hasMany(reserva_servicio::class, 'id_servicio', 'id_servicio');
    }

    //Query Scopes Locales

    #[Scope]
    /**
     * Scope que filtra servicios por nombre
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $servicio
     * @return void
     */
    protected function porNombre(Builder $query, string $servicio){
        $query -> where('nombre_servicio', $servicio);
    }


    #[Scope]
    /**
     * Scope que filtra por Tipo de servicio (Definidos en un enum de BD)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return void
     */
    protected function porTipo(Builder $query, string $tipo){
        $query -> where('tipo_servicio', $tipo);
    }
    
    #[Scope]
    /**
     * Scope que filtra los servicios activos
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    protected function activos(Builder $query){
        $query -> where('activo', true);
    }


    #[Scope]
    /**
     * Scope que filtra los servicios contratados para una reserva espécifica
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $idReserva
     * @return void
     */
    protected function conReserva(Builder $query, $idReserva){
        $query -> whereHas('reservaServicios', function ($subquery) use ($idReserva){
            $subquery -> where('id_reserva', $idReserva);
        });
    }

}
