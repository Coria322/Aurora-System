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
            $this->command->info("Creando tipos de habitación...");

            // Limpiamos la tabla para evitar duplicados

            $tiposHabitaciones = [
                [
                    'nombre' => 'Aurora Essential',
                    'descripcion' => 'Espacio moderno y acogedor con diseño minimalista. Ideal para estancias cortas o escapadas de fin de semana.',
                    'capacidad_maxima' => 2,
                    'precio_noche' => 700,
                    'servicios_incluidos' => 'Cama Queen, Ducha tipo lluvia, Wi-Fi, Smart TV, Iluminación ambiental',
                ],
                [
                    'nombre' => 'Aurora Balance',
                    'descripcion' => 'Habitación amplia con tonos cálidos y mobiliario de diseño escandinavo.',
                    'capacidad_maxima' => 2,
                    'precio_noche' => 1200,
                    'servicios_incluidos' => 'Cama King o 2 Twin, Espacio de trabajo, Minibar artesanal, Vista parcial',
                ],
                [
                    'nombre' => 'Aurora Flow',
                    'descripcion' => 'Habitación superior con área de estar integrada, perfecta para desconectar o trabajar cómodamente.',
                    'capacidad_maxima' => 3,
                    'precio_noche' => 1800,
                    'servicios_incluidos' => 'Cama King, Zona lounge, Cafetera premium, Ducha tipo cascada o tina',
                ],
                [
                    'nombre' => 'Aurora Tribe',
                    'descripcion' => 'Habitación familiar o grupal con diseño cálido, espacios conectados y tecnología domótica.',
                    'capacidad_maxima' => 4,
                    'precio_noche' => 2500,
                    'servicios_incluidos' => '2 camas Queen + sofá cama, Baño amplio, Área social, Smart TV 65"',
                ],
                [
                    'nombre' => 'Aurora Suite',
                    'descripcion' => 'Suite amplia con áreas separadas para descanso y relajación.',
                    'capacidad_maxima' => 3,
                    'precio_noche' => 3200,
                    'servicios_incluidos' => 'Dormitorio principal, Sala lounge, Tina de hidromasaje, Balcón privado',
                ],
                [
                    'nombre' => 'Aurora Executive',
                    'descripcion' => 'Suite ejecutiva moderna con espacio para reuniones, descanso y trabajo.',
                    'capacidad_maxima' => 2,
                    'precio_noche' => 4500,
                    'servicios_incluidos' => 'Dormitorio + sala de estar, Escritorio, Ducha y tina independientes, Lounge privado',
                ],
                [
                    'nombre' => 'Aurora Zenith',
                    'descripcion' => 'Suite presidencial diseñada para experiencias únicas, con interiores inspirados en el cielo nocturno.',
                    'capacidad_maxima' => 2,
                    'precio_noche' => 8000,
                    'servicios_incluidos' => 'Sala, Comedor, Dormitorio principal, Jacuzzi panorámico, Mayordomo y chef personal',
                ],
                [
                    'nombre' => 'Aurora Sky Villa',
                    'descripcion' => 'Ofrece una experiencia sensorial completa con amplios ventanales panorámicos, piscina infinita privada y una estética moderna que combina mármol, madera clara y luz natural.',
                    'capacidad_maxima' => 3,
                    'precio_noche' => 15000,
                    'servicios_incluidos' => '1 dormitorio principal con cama King Signature, Piscina infinita privada, Terraza panorámica, Ducha tipo lluvia + bañera, Sala de estar y cocina gourmet, Servicio de mayordomo y chef a solicitud',
                ],
                [
                    'nombre' => 'Aurora Terra Villa',
                    'descripcion' => 'Una experiencia de calma absoluta con techos altos, piedra natural, madera local y vegetación integrada para la serenidad y el bienestar.',
                    'capacidad_maxima' => 4,
                    'precio_noche' => 12000,
                    'servicios_incluidos' => '2 dormitorios con camas King Premium, Jardín interior con jacuzzi al aire libre, Ducha al exterior rodeada de bambú, Zona lounge con fogata privada, Cocina equipada y comedor interior/exterior, Asistente personal y spa a solicitud',
                ],
            ];

            $tipos = collect();
            foreach ($tiposHabitaciones as $t) {
                $imagen = 'images/habitaciones/' . strtolower(str_replace(' ', '_', $t['nombre'])) . '.png';
                $tipos->push(TipoHabitacion::create(array_merge($t, ['imagen' => $imagen, 'activo' => 1])));
            }

            $this->command->info("✅ 7 Tipos de habitación creados con sus imágenes");

            // Creamos entre 3 y 5 habitaciones físicas por cada tipo
            $habitaciones = collect();
            foreach ($tipos as $tipo) {
                $habitaciones = $habitaciones->merge(Habitacion::factory()->count(rand(3, 5))->create([
                    'id_tipo_habitacion' => $tipo->id_tipo_habitacion
                ]));
            }
            $this->command->info("✅ Habitaciones creadas para cada tipo");
            // -----------------------------
            // 4️⃣ Servicios
            // -----------------------------
            $this->command->info("Creando servicios adicionales...");

            $serviciosData = [
                [
                    'nombre_servicio' => 'Servicio a la habitación',
                    'descripcion' => 'Disfruta tus alimentos sin salir de la habitación. Disponible las 24 horas con menú completo.',
                    'precio' => 120,
                    'imagen' => 'Room_Service.png',
                    'tipo_servicio' => 'basico',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Spa relajante',
                    'descripcion' => 'Masajes, aromaterapia y tratamientos de relajación en un ambiente zen.',
                    'precio' => 450,
                    'imagen' => 'Relax_spa.png',
                    'tipo_servicio' => 'premium',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Cena romántica',
                    'descripcion' => 'Cena privada para dos, con decoración especial, música y menú gourmet.',
                    'precio' => 700,
                    'imagen' => 'Romantic_Dinner.png',
                    'tipo_servicio' => 'premium',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Lavandería express',
                    'descripcion' => 'Servicio de lavado, planchado y entrega en menos de 12 horas.',
                    'precio' => 180,
                    'imagen' => 'Express_Laundry.png',
                    'tipo_servicio' => 'basico',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Transporte al aeropuerto',
                    'descripcion' => 'Traslado cómodo y seguro al aeropuerto, disponible las 24 horas.',
                    'precio' => 250,
                    'imagen' => 'Airport_Taxi.png',
                    'tipo_servicio' => 'premium',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Clases de yoga al amanecer',
                    'descripcion' => 'Sesiones guiadas en la terraza con vista al amanecer, perfectas para comenzar el día con energía.',
                    'precio' => 150,
                    'imagen' => 'Sunrise_Yoga.png',
                    'tipo_servicio' => 'basico',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Tour local guiado',
                    'descripcion' => 'Recorrido cultural por los principales atractivos de la ciudad, con guía certificado.',
                    'precio' => 350,
                    'imagen' => 'Guided_Tour.png',
                    'tipo_servicio' => 'premium',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Cuidado infantil',
                    'descripcion' => 'Niñeras capacitadas disponibles por hora, con actividades recreativas y supervisión constante.',
                    'precio' => 200,
                    'imagen' => 'Child_Care.png',
                    'tipo_servicio' => 'basico',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Aromaterapia nocturna',
                    'descripcion' => 'Ambientación de la habitación con esencias relajantes y música suave antes de dormir.',
                    'precio' => 100,
                    'imagen' => 'Night_Aromatherapy.png',
                    'tipo_servicio' => 'basico',
                    'activo' => true,
                ],
                [
                    'nombre_servicio' => 'Desayuno en la terraza',
                    'descripcion' => 'Desayuno continental o gourmet servido con vista panorámica al amanecer.',
                    'precio' => 180,
                    'imagen' => 'Breakfast.png',
                    'tipo_servicio' => 'premium',
                    'activo' => true,
                ],
            ];

            $servicios = collect();
            foreach ($serviciosData as $s) {
                $servicios->push(servicio::create($s));
            }

            $this->command->info("✅ 10 Servicios adicionales creados correctamente");

            // -----------------------------
            // 5️⃣ Reservas y relaciones
            // -----------------------------
            foreach ($habitaciones as $habitacion) {
                $cantidad_reservas = rand(1, 3);

                for ($i = 0; $i < $cantidad_reservas; $i++) {
                    $huesped = Huesped::inRandomOrder()->first();
                    $usuario = User::whereIn('tipo_usuario', ['admin', 'empleado'])->inRandomOrder()->first();

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

                    $cantidad_servicios = rand(0, 3);
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
