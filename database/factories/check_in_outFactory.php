<?php

namespace Database\Factories;

use App\Models\check_in_out;
use App\Models\CheckInOut;
use App\Models\Reserva;
use App\Models\User;
use App\Models\huesped;
use Illuminate\Database\Eloquent\Factories\Factory;

class check_in_outFactory extends Factory
{
    protected $model = check_in_out::class;

    public function definition(): array
    {
        $fecha_check_in = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $fecha_check_out = (clone $fecha_check_in)->modify('+'.rand(1,10).' days');

        $reserva = Reserva::with('huesped','user')->inRandomOrder()->first();
        
        return [
            'id_reserva' => $reserva->id_reserva,
            'id_usuario' => $reserva->id_usuario,
            'fecha_check_in' => $fecha_check_in,
            'fecha_check_out' => $fecha_check_out,
            'observaciones_check_in' => $this->faker->optional()->sentence(8),
            'observaciones_check_out' => $this->faker->optional()->sentence(8),
            'estado_check' => $this->faker->randomElement(['check-in', 'check-out']),
        ];
    }
}
