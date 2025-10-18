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
            'descripcion' => $this->faker->sentence(6),
            'capacidad_maxima' => $this->faker->numberBetween(1, 6),
            'precio_noche' => $this->faker->randomFloat(2, 500, 5000),
            'servicios_incluidos' => $this->faker->sentence(10),
            'imagen' => 'images/habitaciones/' . strtolower(str_replace(' ', '_', $this->faker->word())) . '.png',
            'activo' => $this->faker->boolean(90),
        ];
    }
}
