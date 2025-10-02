<?php

namespace Database\Factories;

use App\Models\TipoHabitacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoHabitacionFactory extends Factory
{
    protected $model = TipoHabitacion::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word() . ' Room',
            'descripcion' => $this->faker->optional()->sentence(6),
            'capacidad_maxima' => $this->faker->numberBetween(1, 6),
            'precio_noche' => $this->faker->randomFloat(2, 500, 5000), // precio entre 500 y 5000
            'servicios_incluidos' => $this->faker->optional()->sentence(10),
            'activo' => $this->faker->boolean(90), // 90% probabilidad de ser true
        ];
    }
}
