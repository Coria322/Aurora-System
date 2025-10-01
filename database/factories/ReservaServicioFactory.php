<?php

namespace Database\Factories;

use App\Models\Reserva;
use App\Models\servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservaServicioFactory extends Factory
{
    protected $model = servicio::class;

    public function definition(): array
    {
        $cantidad = $this->faker->numberBetween(1, 5);
        $precio_unitario = $this->faker->randomFloat(2, 100, 2000);
        $subtotal = $cantidad * $precio_unitario;

        return [
            'id_reserva' => Reserva::factory(),
            'id_servicio' => servicio::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $subtotal,
            'fecha_servicio' => $this->faker->dateTimeBetween('+1 days', '+2 months'),
        ];
    }
}
