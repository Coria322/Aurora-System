<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class check_in_out extends Model
{
    use HasFactory;

    protected $table = 'check_in_out';
    protected $primaryKey = 'id_check';

    public $timestamps = false;

    protected $fillable = [
        'id_reserva',
        'id_usuario',
        'fecha_check_in',
        'fecha_check_out',
        'observaciones_check_in',
        'observaciones_check_out',
        'estado_check',
    ];

    protected $casts = [
        'fecha_check_in' => 'datetime',
        'fecha_check_out' => 'datetime',
    ];

    // Relaciones
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
