<?php

namespace Database\Seeders;

use App\Models\check_in_out;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Huesped;
use App\Models\TipoHabitacion;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use App\Models\Pago;
use App\Models\reserva_servicio;
use App\Models\servicio;
use Exception;
use Illuminate\Support\Facades\DB;

class Seeder_general extends Seeder
{
    public function run(): void
    {

        try {
            DB::beginTransaction();
            // -----------------------------
            // 1️⃣ Usuarios
            // -----------------------------
            $this->command->info("Creando usuarios...");

            $admins = User::factory()->count(5)->state(['tipo_usuario' => 'admin'])->create();
            $this->command->info("✅ 5 Admins creados");

            $empleados = User::factory()->count(15)->state(['tipo_usuario' => 'empleado'])->create();
            $this->command->info("✅ 15 Empleados creados");

            $clientes = User::factory()->count(10)->state(['tipo_usuario' => 'cliente'])->create();
            $this->command->info("✅ 10 Clientes creados");

            // -----------------------------
            // 2️⃣ Huespedes (solo clientes)
            // -----------------------------
            foreach ($clientes as $cliente) {
                Huesped::factory()->create([
                    'nombre' => $cliente->name ?? $cliente->nombre,
                    'apellido_paterno' => $cliente->apellido_paterno ?? 'Apellido',
                    'apellido_materno' => $cliente->apellido_materno ?? 'Apellido',
                    'email' => $cliente->email,
                ]);
            }
            $this->command->info("✅ Huespedes creados para los clientes");

            // -----------------------------
            // 3️⃣ TipoHabitacion y Habitaciones
            // -----------------------------
            $tipos = TipoHabitacion::factory()->count(5)->create();
            $this->command->info("✅ 5 Tipos de habitación creados");

            $habitaciones = collect();
            foreach ($tipos as $tipo) {
                $habitaciones = $habitaciones->merge(Habitacion::factory()->count(rand(3,5))->create([
                    'id_tipo_habitacion' => $tipo->id_tipo_habitacion
                ]));
            }
            $this->command->info("✅ Habitaciones creadas para cada tipo");

            // -----------------------------
            // 4️⃣ Servicios
            // -----------------------------
            $servicios = servicio::factory()->count(10)->create();
            $this->command->info("✅ 10 Servicios adicionales creados");

            // -----------------------------
            // 5️⃣ Reservas y relaciones
            // -----------------------------
            foreach ($habitaciones as $habitacion) {
                $cantidad_reservas = rand(1,3);

                for ($i=0; $i < $cantidad_reservas; $i++) {
                    $huesped = Huesped::inRandomOrder()->first();
                    $usuario = User::whereIn('tipo_usuario', ['admin','empleado'])->inRandomOrder()->first();

                    $reserva = Reserva::factory()->create([
                        'id_huesped' => $huesped->id_huesped,
                        'id_usuario' => $usuario->id_usuario,
                    ]);

                    DetalleReserva::factory()->create([
                        'id_reserva' => $reserva->id_reserva,
                        'id_habitacion' => $habitacion->id_habitacion,
                    ]);

                    Pago::factory()->create([
                        'id_reserva' => $reserva->id_reserva,
                    ]);

                    $cantidad_servicios = rand(0,3);
                    if ($cantidad_servicios > 0) {
                        $serviciosSeleccionados = $servicios->random($cantidad_servicios);
                        foreach ($serviciosSeleccionados as $servicio) {
                            reserva_servicio::factory()->create([
                                'id_reserva' => $reserva->id_reserva,
                                'id_servicio' => $servicio->id_servicio,
                            ]);
                        }
                    }

                    check_in_out::factory()->create([
                        'id_reserva' => $reserva->id_reserva,
                        'id_usuario' => $usuario->id_usuario,
                    ]);
                }
            }
            DB::commit();
            $this->command->info("🎉 Seeder completo ejecutado correctamente");

        } catch (Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error al ejecutar el seeder: " . $e->getMessage() . " stack trace " . $e->getTraceAsString());
        }
    }
}
