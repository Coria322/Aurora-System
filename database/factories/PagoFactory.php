<?php

namespace Database\Factories;

use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Database\Eloquent\Factories\Factory;

class PagoFactory extends Factory
{
    protected $model = Pago::class;

    public function definition(): array
    {
        $monto = $this->faker->randomFloat(2, 500, 10000);

        return [
            'id_reserva' => Reserva::factory(),
            'monto' => $monto,
            'metodo_pago' => $this->faker->randomElement(['tarjeta_credito', 'paypal', 'transferencia_bancaria', 'efectivo']),
            'estado_pago' => $this->faker->randomElement(['pendiente', 'completado', 'fallido']),
            'referencia_transaccion' => $this->faker->unique()->uuid(),
            'observaciones' => $this->faker->optional()->sentence(8),
        ];
    }
}
