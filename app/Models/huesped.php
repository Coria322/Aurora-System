<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class huesped extends Model
{
    /** @use HasFactory<\Database\Factories\HuespedFactory> */
    use HasFactory;
    protected $table = 'huespedes';
    protected $primaryKey = 'id_huesped';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'documento_identidad',
        'tipo_documento',
        'telefono',
        'email',
        'fecha_nacimiento',
        'direccion',
        'ciudad',
        'pais'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date'

    ];

    //relaciones
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_huesped', 'id_huesped');
    }

    // Relación indirecta a través de Reserva
    public function pagos()
    {
        return $this->hasManyThrough(
            Pago::class,      // Modelo final
            Reserva::class,   // Modelo intermedio
            'id_huesped',     // FK en Reserva que apunta a Huesped
            'id_reserva',     // FK en Pago que apunta a Reserva
            'id_huesped',     // PK local en Huesped
            'id_reserva'      // PK local en Reserva
        );
    }
}
