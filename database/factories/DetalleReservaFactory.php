<?php

namespace Database\Factories;

use App\Models\DetalleReserva;
use App\Models\Reserva;
use App\Models\Habitacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetalleReservaFactory extends Factory
{
    protected $model = DetalleReserva::class;

    public function definition(): array
    {
        $fecha_inicio = $this->faker->dateTimeBetween('+1 days', '+2 months');
        $numero_noches = $this->faker->numberBetween(1, 10);
        $precio_noche = $this->faker->randomFloat(2, 500, 5000);
        $subtotal = $precio_noche * $numero_noches;
        $fecha_fin = (clone $fecha_inicio)->modify("+{$numero_noches} days");

        return [
            'id_reserva' => Reserva::factory(),
            'id_habitacion' => Habitacion::factory(),
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'precio_noche' => $precio_noche,
            'numero_noches' => $numero_noches,
            'subtotal' => $subtotal,
        ];
    }
}
