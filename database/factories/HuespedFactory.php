<?php

namespace Database\Factories;

use App\Models\Huesped;
use Illuminate\Database\Eloquent\Factories\Factory;

class HuespedFactory extends Factory
{
    protected $model = Huesped::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->optional()->lastName(),
            'documento_identidad' => $this->faker->numerify('##########'),
            'tipo_documento' => $this->faker->randomElement(['INE', 'Pasaporte', 'Cédula Profesional', 'Otro']),
            'telefono' => $this->faker->optional()->numerify('55#######'),
            'email' => $this->faker->optional()->unique()->safeEmail(),
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '-18 years'),
            'direccion' => $this->faker->streetAddress(),
            'ciudad' => $this->faker->city(),
            'pais' => $this->faker->country(),
        ];
    }
}
