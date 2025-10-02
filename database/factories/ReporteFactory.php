<?php

namespace Database\Factories;

use App\Models\reporte;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReporteFactory extends Factory
{
    protected $model = reporte::class;

    public function definition(): array
    {
        $ocupadas = $this->faker->numberBetween(0, 50);
        $disponibles = $this->faker->numberBetween(0, 50);
        $total = $ocupadas + $disponibles;
        $tasa = $total > 0 ? ($ocupadas / $total) * 100 : 0;
        $ingresos = $this->faker->randomFloat(2, 1000, 50000);

        return [
            'Habitaciones_ocupadas' => $ocupadas,
            'Habitaciones_disponibles' => $disponibles,
            'tasa_ocupacion' => round($tasa, 2),
            'ingresos_totales' => $ingresos,
        ];
    }
}
