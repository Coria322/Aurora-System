<?php

namespace Database\Factories;

use App\Models\servicio;
use App\Models\ServicioAdicional;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicioFactory extends Factory
{
    protected $model = servicio::class;

    public function definition(): array
    {
        return [
            'nombre_servicio' => $this->faker->unique()->word() . ' Service',
            'descripcion' => $this->faker->optional()->sentence(8),
            'precio' => $this->faker->randomFloat(2, 100, 2000),
            'tipo_servicio' => $this->faker->randomElement(['basico','premium']),
            'activo' => $this->faker->boolean(90),
        ];
    }
}
