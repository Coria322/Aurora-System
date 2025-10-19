<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';
    protected $primaryKey = 'id_habitacion';

    public $timestamps = false;

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

    //Query scopes locales. Utilizados para traer datos al usuario

    #[Scope]
    /**
     * Scope para filtrar habitaciones disponibles
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    protected function Disponibles(Builder $query)
    {
        $query->where('estado', 'disponible');
    }

    #[Scope]
    protected function Utilizables(Builder $query)
    {
        $query->where('estado', '!=', 'no disponible');
    }


    #[Scope]
    /**
     * Scope para Filtrar las habitaciónes según su tipo (Definido como enum en base de datos)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return void
     */
    protected function Tipo(Builder $query, string $tipo)
    {
        $query->whereHas(
            'tipoHabitacion',
            function ($q) use ($tipo) {
                $q->where('nombre', $tipo);
            }
        );
    }

    #[Scope]
    /**
     * Scope que permite hacer un filtrado fino por fechas
     * Recibe las fechas y devuelve las habitaciones
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $fechaInicio
     * @param mixed $fechaFin
     * @return void
     */
    protected function Entre(Builder $query, $fechaInicio, $fechaFin)
    {
        $query->whereDoesntHave('detalleReservas', function ($subquery) use ($fechaInicio, $fechaFin) {
            $subquery->where('fecha_inicio', '<', $fechaFin)
                ->where('fecha_fin', '>', $fechaInicio)
                ->whereHas('reserva', function ($q) {
                    $q->where('estado', '!=', 'cancelada');
                });
        });
    }


    #[Scope]
    /**
     * Scope que filtra las habitaciones por su tipo (En string almacenado en la tabla tipo_habitaciones)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return void
     */
    protected function porTipo(Builder $query, string $tipo)
    {
        $query->whereHas(
            'tipoHabitacion',
            function ($subquery) use ($tipo) {
                $subquery->where('nombre', $tipo);
            }
        );
    }

    #[Scope]
    /**
     * Scope que filtra habitaciones por su tipo (Id, almacenado como llave Foranea)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id_tipo
     * @return void
     */
    protected function porTipoId(Builder $query, int $id_tipo)
    {
        $query->where('id_tipo_habitacion', $id_tipo);
    }
}
