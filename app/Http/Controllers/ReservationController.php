<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use App\Models\Huesped;
use App\Models\DetalleReserva;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ReservationController extends Controller
{
    /**
     * Obtener todas las reservas del usuario autenticado
     */
    public function index(): JsonResponse
    {
        $reservas = Reserva::where('id_usuario', Auth::id())
            ->with(['huesped', 'detalleReservas.habitacion.tipoHabitacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservas
        ]);
    }

    /**
     * Crear una nueva reserva usando los modelos existentes
     */
    public function store(Request $request): JsonResponse
    {
        // Validar los datos
        $validated = $request->validate([
            'tipo_habitacion_id' => 'required|exists:tipo_habitaciones,id_tipo_habitacion',
            'fecha_checkin' => 'required|date|after_or_equal:today',
            'fecha_checkout' => 'required|date|after:fecha_checkin',
            'cantidad_personas' => 'required|integer|min:1|max:10',
            'nombre_huesped' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'email_huesped' => 'required|email|max:255',
            'telefono_huesped' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Obtener el tipo de habitación
        $tipoHabitacion = TipoHabitacion::find($validated['tipo_habitacion_id']);
        
        // Buscar una habitación disponible del tipo seleccionado
        $habitacionDisponible = Habitacion::where('id_tipo_habitacion', $validated['tipo_habitacion_id'])
            ->where('estado', 'disponible')
            ->first();

        if (!$habitacionDisponible) {
            return response()->json([
                'success' => false,
                'message' => 'No hay habitaciones disponibles de este tipo'
            ], 400);
        }

        // Crear o encontrar el huésped
        $huesped = Huesped::firstOrCreate(
            ['email' => $validated['email_huesped']],
            [
                'nombre' => $validated['nombre_huesped'],
                'apellido_paterno' => $validated['apellido_paterno'],
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'telefono' => $validated['telefono_huesped'] ?? null,
                'email' => $validated['email_huesped'],
            ]
        );

        // Calcular fechas y totales
        $checkIn = Carbon::parse($validated['fecha_checkin']);
        $checkOut = Carbon::parse($validated['fecha_checkout']);
        $noches = $checkIn->diffInDays($checkOut);
        $subtotal = $tipoHabitacion->precio_noche * $noches;
        $impuestos = $subtotal * 0.16; // 16% de impuestos
        $total = $subtotal + $impuestos;

        // Crear la reserva
        $reserva = Reserva::create([
            'id_huesped' => $huesped->id_huesped,
            'id_usuario' => Auth::User()->id_usuario,
            'fecha_checkin' => $checkIn,
            'fecha_checkout' => $checkOut,
            'cantidad_personas' => $validated['cantidad_personas'],
            'estado' => 'pendiente',
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'total' => $total,
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        // Crear el detalle de la reserva
        DetalleReserva::create([
            'id_reserva' => $reserva->id_reserva,
            'id_habitacion' => $habitacionDisponible->id_habitacion,
            'precio_noche' => $tipoHabitacion->precio_noche,
            'cantidad_noches' => $noches,
            'subtotal' => $subtotal,
        ]);

        // Marcar la habitación como ocupada
        $habitacionDisponible->update(['estado' => 'ocupada']);

        // Cargar las relaciones para la respuesta
        $reserva->load(['huesped', 'detalleReservas.habitacion.tipoHabitacion']);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'data' => $reserva
        ], 201);
    }

    /**
     * Obtener una reserva específica
     */
    public function show(Reserva $reserva): JsonResponse
    {
        // Verificar que la reserva pertenece al usuario autenticado
        if ($reserva->id_usuario !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $reserva->load(['huesped', 'detalleReservas.habitacion.tipoHabitacion']);

        return response()->json([
            'success' => true,
            'data' => $reserva
        ]);
    }

    /**
     * Cancelar una reserva
     */
    public function cancel(Reserva $reserva): JsonResponse
    {
        // Verificar que la reserva pertenece al usuario autenticado
        if ($reserva->id_usuario !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        // Solo permitir cancelar si está pendiente o confirmada
        if (!in_array($reserva->estado, ['pendiente', 'confirmada'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar esta reserva'
            ], 400);
        }

        // Liberar la habitación
        foreach ($reserva->detalleReservas as $detalle) {
            $detalle->habitacion->update(['estado' => 'disponible']);
        }

        $reserva->update(['estado' => 'cancelada']);

        return response()->json([
            'success' => true,
            'message' => 'Reserva cancelada exitosamente',
            'data' => $reserva
        ]);
    }

    /**
     * Verificar disponibilidad de habitaciones - VERSIÓN SIMPLIFICADA
     * GET /api/reservas/disponibilidad
     */
public function verificarDisponibilidad(Request $request): JsonResponse
{
    try {
        // Validación básica
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');

        if (!$fechaInicio || !$fechaFin) {
            return response()->json([
                'success' => false,
                'message' => 'Fechas requeridas'
            ], 400);
        }

        // Obtener tipos de habitaciones activas
        $tiposDisponibles = TipoHabitacion::Activas()
            ->withCount(['habitaciones' => function($query) use ($fechaInicio, $fechaFin) {
                $query->utilizables()->entre($fechaInicio, $fechaFin); // Solo las disponibles entre las fechas
            }])
            ->get()
            ->map(function($tipo) {
                return [
                    'id_tipo_habitacion' => $tipo->id_tipo_habitacion,
                    'nombre' => $tipo->nombre,
                    'descripcion' => $tipo->descripcion,
                    'capacidad_maxima' => $tipo->capacidad_maxima,
                    'precio_noche' => $tipo->precio_noche,
                    'servicios_incluidos' => $tipo->servicios_incluidos,
                    'habitaciones_disponibles' => $tipo->habitaciones_count, // conteo filtrado
                ];
            })
            ->values()
            ->toArray();

        // Total de habitaciones disponibles sumando por tipo
        $totalHabitaciones = array_sum(array_column($tiposDisponibles, 'habitaciones_disponibles'));

        Log::info('Tipos de habitaciones disponibles:', $tiposDisponibles);

        return response()->json([
            'success' => true,
            'disponible' => $totalHabitaciones > 0,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'total_habitaciones_disponibles' => $totalHabitaciones,
            'tipos_disponibles' => $tiposDisponibles,
            'message' => 'Disponibilidad verificada correctamente'
        ]);

    } catch (\Exception $e) {
        Log::error('Error verificarDisponibilidad: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'message' => 'Error interno al verificar disponibilidad'
        ], 500);
    }
}


    /**
     * Crear nueva reserva simplificada
     * POST /api/reservas/crear
     */
    public function crearReserva(Request $request): JsonResponse
    {
        // Validar datos de entrada
        $validated = $request->validate([
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'tipo_habitacion_id' => 'required|exists:tipo_habitaciones,id_tipo_habitacion',
            'usuario_id' => 'required|exists:users,id_usuario',
            'cantidad_personas' => 'required|integer|min:1|max:10',
            'nombre_huesped' => 'required|string|max:50',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'nullable|string|max:50',
            'email_huesped' => 'required|email|max:100',
            'telefono_huesped' => 'nullable|string|max:10',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $fechaInicio = Carbon::parse($validated['fecha_inicio']);
        $fechaFin = Carbon::parse($validated['fecha_fin']);

        // Verificar disponibilidad antes de crear la reserva - simplificado
        $habitacionDisponible = Habitacion::where('id_tipo_habitacion', $validated['tipo_habitacion_id'])
            ->where('estado', 'disponible')
            ->first();

        if (!$habitacionDisponible) {
            return response()->json([
                'success' => false,
                'message' => 'No hay habitaciones disponibles para las fechas seleccionadas'
            ], 400);
        }

        // Obtener tipo de habitación para cálculos
        $tipoHabitacion = TipoHabitacion::find($validated['tipo_habitacion_id']);

        // Crear o encontrar el huésped
        $huesped = Huesped::firstOrCreate(
            ['email' => $validated['email_huesped']],
            [
                'nombre' => $validated['nombre_huesped'],
                'apellido_paterno' => $validated['apellido_paterno'],
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'telefono' => $validated['telefono_huesped'] ?? null,
                'email' => $validated['email_huesped'],
                'documento_identidad' => 'TEMP-' . time(), // Temporal, debería ser requerido
                'tipo_documento' => 'Otro', // Temporal
                'fecha_nacimiento' => now()->subYears(25), // Temporal
                'direccion' => 'Temporal', // Temporal
                'ciudad' => 'Temporal', // Temporal
                'pais' => 'México' // Temporal
            ]
        );

        // Calcular totales
        $noches = $fechaInicio->diffInDays($fechaFin);
        $subtotal = $tipoHabitacion->precio_noche * $noches;
        $impuestos = $subtotal * 0.16; // 16% de impuestos
        $total = $subtotal + $impuestos;

        // Crear la reserva
        $reserva = Reserva::create([
            'id_huesped' => $huesped->id_huesped,
            'id_usuario' => $validated['usuario_id'],
            'fecha_checkin' => $fechaInicio,
            'fecha_checkout' => $fechaFin,
            'cantidad_personas' => $validated['cantidad_personas'],
            'estado' => 'pendiente',
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'total' => $total,
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        // Crear el detalle de la reserva
        DetalleReserva::create([
            'id_reserva' => $reserva->id_reserva,
            'id_habitacion' => $habitacionDisponible->id_habitacion,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'precio_noche' => $tipoHabitacion->precio_noche,
            'numero_noches' => $noches,
            'subtotal' => $subtotal,
        ]);

        // Marcar la habitación como ocupada
        $habitacionDisponible->update(['estado' => 'ocupada']);

        // Cargar relaciones para la respuesta
        $reserva->load(['huesped', 'detalleReservas.habitacion.tipoHabitacion']);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'reserva_id' => $reserva->id_reserva,
            'data' => [
                'id_reserva' => $reserva->id_reserva,
                'fecha_checkin' => $reserva->fecha_checkin->format('Y-m-d'),
                'fecha_checkout' => $reserva->fecha_checkout->format('Y-m-d'),
                'noches' => $noches,
                'habitacion' => [
                    'numero_habitacion' => $habitacionDisponible->numero_habitacion,
                    'tipo' => $tipoHabitacion->nombre,
                    'precio_noche' => $tipoHabitacion->precio_noche
                ],
                'huesped' => [
                    'nombre' => $huesped->nombre,
                    'apellido_paterno' => $huesped->apellido_paterno,
                    'email' => $huesped->email
                ],
                'totales' => [
                    'subtotal' => $subtotal,
                    'impuestos' => $impuestos,
                    'total' => $total
                ],
                'estado' => $reserva->estado
            ]
        ], 201);
    }

    /**
     * Listar tipos de habitaciones con precios
     * GET /api/habitaciones/tipos
     */
public function listarTiposHabitaciones(Request $request, $fecha_inicio = null, $fecha_fin = null): JsonResponse
{
    try {

        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');
        $tiposHabitaciones = TipoHabitacion::where('activo', true)
            ->withCount(['habitaciones' => function() use ($fecha_inicio, $fecha_fin) {
                Habitacion::entre($fecha_inicio ?? now()->format('Y-m-d'), $fecha_fin ?? now()->addDay()->format('Y-m-d'));
            }])
            ->get()
            ->map(function($tipo) {
                return [
                    'id_tipo_habitacion'     => $tipo->id_tipo_habitacion,
                    'nombre'                 => $tipo->nombre,
                    'descripcion'            => $tipo->descripcion,
                    'capacidad_maxima'       => $tipo->capacidad_maxima,
                    'precio_noche'           => $tipo->precio_noche,
                    'servicios_incluidos'    => $tipo->servicios_incluidos,
                    'habitaciones_disponibles'=> $tipo->habitaciones_count,
                    'activo'                 => $tipo->activo
                ];
            })
            ->values()   // <- reindexa la colección para evitar serializar como objeto
            ->toArray(); // <- fuerza array plano para el JSO

        return response()->json([
            'success'     => true,
            'data'        => $tiposHabitaciones,
            'total_tipos' => count($tiposHabitaciones)
        ]);
    } catch (\Exception $e) {
        Log::error('listarTiposHabitaciones error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

        return response()->json([
            'success' => false,
            'message' => 'Error interno al listar tipos de habitaciones'
        ], 500);
    }
}


public function crearPublico(Request $request): JsonResponse
{
    try {
        // Validación básica (defaults permitidos para nombre/email)
        $validated = $request->validate([
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'tipo_habitacion_id' => 'required|exists:tipo_habitaciones,id_tipo_habitacion',
            'cantidad_personas' => 'nullable|integer|min:1|max:10',
            'nombre' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $fechaInicio = $validated['fecha_inicio'];
        $fechaFin = $validated['fecha_fin'];
        $tipoHabitacionId = $validated['tipo_habitacion_id'];
        $cantidadPersonas = $validated['cantidad_personas'] ?? 1;
        $nombre = $validated['nombre'] ?? 'Cliente';
        $email = $validated['email'] ?? 'cliente@test.com';

        // Usar transacción para evitar race conditions al reservar la habitación
        return DB::transaction(function() use ($fechaInicio, $fechaFin, $tipoHabitacionId, $cantidadPersonas, $nombre, $email) {

            // Buscar habitación disponible usando los scopes que ya usas en el proyecto
            $habitacionDisponible = Habitacion::Utilizables()
                ->porTipoId($tipoHabitacionId)
                ->entre($fechaInicio, $fechaFin)
                ->lockForUpdate() // evita que otra transacción la tome al mismo tiempo
                ->first();

            if (!$habitacionDisponible) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay habitaciones disponibles'
                ], 400);
            }

            // Crear o reutilizar huésped por email (evita duplicados)
            $huesped = Huesped::firstOrCreate(
                ['email' => $email],
                [
                    'nombre' => $nombre,
                    'apellido_paterno' => 'Temporal',
                    'apellido_materno' => 'Temporal',
                    'telefono' => '0000000000',
                    'tipo_documento' => 'INE',
                    'documento_identidad' => '0000000000',
                    'fecha_nacimiento' => null,
                    'direccion' => 'Dirección temporal',
                    'ciudad' => 'Ciudad temporal',
                    'pais' => 'México'
                ]
            );

            $tipoHabitacion = TipoHabitacion::find($tipoHabitacionId);
            $fi = Carbon::parse($fechaInicio);
            $ff = Carbon::parse($fechaFin);
            $numeroNoches = $fi->diffInDays($ff);
            if ($numeroNoches <= 0) {
                $numeroNoches = 1;
            }

            $subtotal = $tipoHabitacion->precio_noche * $numeroNoches;
            $impuestos = $subtotal * 0.16;
            $total = $subtotal + $impuestos;

            // Crear reserva - PARA USO PÚBLICO dejamos id_usuario en null (o 1 si tu negocio lo requiere)
            $reserva = Reserva::create([
                'id_huesped' => $huesped->id_huesped,
                'id_usuario' => Auth::id(), // <-- ajustar si deseas usar un usuario "sistema" (ej. 1)
                'fecha_checkin' => $fi,
                'fecha_checkout' => $ff,
                'cantidad_personas' => $cantidadPersonas,
                'estado' => 'confirmada', // o 'pendiente' según tu flujo
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total,
                'observaciones' => 'Reserva creada desde sistema público'
            ]);

            // Crear detalle de reserva
            DetalleReserva::create([
                'id_reserva' => $reserva->id_reserva,
                'id_habitacion' => $habitacionDisponible->id_habitacion,
                'fecha_inicio' => $fi,
                'fecha_fin' => $ff,
                'precio_noche' => $tipoHabitacion->precio_noche,
                // Nota: confirmar el nombre real de la columna en tu tabla (ver comentarios más abajo)
                'numero_noches' => $numeroNoches,
                'subtotal' => $subtotal
            ]);

            // Marcar la habitación como ocupada
            $habitacionDisponible->update(['estado' => 'ocupada']);

            // Respuesta
            return response()->json([
                'success' => true,
                'message' => 'Reserva creada exitosamente',
                'data' => [
                    'id_reserva' => $reserva->id_reserva,
                    'numero_reserva' => 'RES-' . str_pad($reserva->id_reserva, 6, '0', STR_PAD_LEFT),
                    'fecha_inicio' => $fi->format('Y-m-d'),
                    'fecha_fin' => $ff->format('Y-m-d'),
                    'total' => $total,
                    'habitacion' => $habitacionDisponible->numero_habitacion
                ]
            ], 201);
        });

    } catch (\Exception $e) {
        Log::info('Auth debug', [
    'auth_id' => Auth::id(),
    'auth_user' => Auth::user(),      // ojo: en producción evita volcar objetos sensibles
    'request_user' => $request->user(),
    'auth_guards' => config('auth.guards'),
    'authorization_header' => $request->header('Authorization'),
]);

        Log::info("id: ". Auth::user() . "\n\n\n\n");
        // Log::error('crearPublico error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al crear reserva: ' . $e->getMessage()
        ], 500);
    }
}


}