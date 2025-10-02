<?php

namespace Database\Factories;

use App\Models\Reserva;
use App\Models\Huesped;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservaFactory extends Factory
{
    protected $model = Reserva::class;

    public function definition(): array
    {
        $fecha_checkin = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $fecha_checkout = (clone $fecha_checkin)->modify('+'.rand(1,10).' days');
        $cantidad_personas = $this->faker->numberBetween(1, 6);
        $subtotal = $this->faker->randomFloat(2, 1000, 10000);
        $impuestos = $subtotal * 0.16;
        $total = $subtotal + $impuestos;

        // Asegurar huesped
        $huesped = Huesped::inRandomOrder()->first();
        if (!$huesped) {
            $huesped = Huesped::factory()->create();
        }

        // Asegurar usuario
        $usuario = User::inRandomOrder()->first();
        if (!$usuario) {
            $usuario = User::factory()->create();
        }

        // Obtener la clave primaria real del usuario (puede ser 'id' o 'id_usuario')
        $usuarioKey = $usuario->{$usuario->getKeyName()};

        return [
            'id_huesped' => $huesped->id_huesped,
            'id_usuario' => $usuarioKey,
            'fecha_checkin' => $fecha_checkin,
            'fecha_checkout' => $fecha_checkout,
            'cantidad_personas' => $cantidad_personas,
            'estado' => $this->faker->randomElement(['pendiente', 'confirmada', 'cancelada']),
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'total' => $total,
            'observaciones' => $this->faker->optional()->sentence(10),
        ];
    }
}
