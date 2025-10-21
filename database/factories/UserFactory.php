<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->optional()->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->optional()->numerify('55#######'),
            'tipo_usuario' => $this->faker->randomElement(['admin', 'empleado', 'cliente']),
            'activo' => $this->faker->boolean(90),
            'password' => bcrypt('password'), // por defecto
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
