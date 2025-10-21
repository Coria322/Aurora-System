<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_huesped',
        'id_usuario',
        'fecha_checkin',
        'fecha_checkout',
        'cantidad_personas',
        'estado',
        'subtotal',
        'impuestos',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'fecha_checkin' => 'date',
        'fecha_checkout' => 'date',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function huesped()
    {
        return $this->belongsTo(Huesped::class, 'id_huesped', 'id_huesped');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function detalleReservas()
    {
        return $this->hasMany(DetalleReserva::class, 'id_reserva', 'id_reserva');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_reserva', 'id_reserva');
    }

    public function reservaServicios()
    {
        return $this->hasMany(reserva_servicio::class, 'id_reserva', 'id_reserva');
    }

    public function checkInOuts()
    {
        return $this->hasMany(check_in_out::class, 'id_reserva', 'id_reserva');
    }

    #[Scope]
    protected function deUsuario(Builder $query, string $userId)
    {
        $query->where('id_usuario', $userId);
    }


    #[Scope]
    protected function activas(Builder $query)
    {
        $query->where('estado', '!=', 'cancelada');
    }

    #[Scope]
    protected function enCursoOProximas(Builder $query)
    {
        $now = now();

        $query->where(function ($q) use ($now) {
            $q->where(function ($sub) use ($now) {
                $sub->whereDate('fecha_checkin', '<=', $now)
                    ->whereDate('fecha_checkout', '>=', $now);
            })
                ->orWhereIn('estado', ['pendiente', 'confirmada']);
        });
    }
}
