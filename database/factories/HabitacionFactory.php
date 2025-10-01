<?php

namespace Database\Factories;

use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class HabitacionFactory extends Factory
{
    protected $model = Habitacion::class;

    public function definition(): array
    {
        return [
            'numero_habitacion' => $this->faker->unique()->numberBetween(100, 999),
            'id_tipo_habitacion' => TipoHabitacion::factory(),
            'estado' => $this->faker->randomElement(['disponible','ocupada','no disponible']),
            'piso' => $this->faker->numberBetween(1, 10),
            'descripcion' => $this->faker->optional()->sentence(8),
        ];
    }
}
