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
use Carbon\Carbon;

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
            'id_usuario' => Auth::id(),
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
     * Verificar disponibilidad de habitaciones
     * GET /api/reservas/disponibilidad
     */
    public function verificarDisponibilidad(Request $request): JsonResponse
    {
        try {
            // Validar parámetros de entrada
            $validated = $request->validate([
                'fecha_inicio' => 'required|date|after_or_equal:today',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'tipo_habitacion' => 'nullable|exists:tipo_habitaciones,id_tipo_habitacion'
            ]);

            $fechaInicio = Carbon::parse($validated['fecha_inicio']);
            $fechaFin = Carbon::parse($validated['fecha_fin']);

            // Construir query base para habitaciones disponibles
            $query = Habitacion::where('estado', 'disponible');

            // Filtrar por tipo de habitación si se especifica
            if (isset($validated['tipo_habitacion'])) {
                $query->where('id_tipo_habitacion', $validated['tipo_habitacion']);
            }

            // Verificar disponibilidad - simplificado sin scope por ahora
            $habitacionesDisponibles = $query->get();

            // Verificar si hay habitaciones disponibles
            $disponible = $habitacionesDisponibles->count() > 0;

            // Obtener información adicional
            $tiposDisponibles = [];
            if ($disponible) {
                $tiposDisponibles = $habitacionesDisponibles
                    ->groupBy('id_tipo_habitacion')
                    ->map(function($habitaciones, $tipoId) {
                        $tipo = TipoHabitacion::find($tipoId);
                        return [
                            'id_tipo_habitacion' => $tipoId,
                            'nombre' => $tipo->nombre,
                            'precio_noche' => $tipo->precio_noche,
                            'capacidad_maxima' => $tipo->capacidad_maxima,
                            'habitaciones_disponibles' => $habitaciones->count(),
                            'servicios_incluidos' => $tipo->servicios_incluidos
                        ];
                    })
                    ->values();
            }

            return response()->json([
                'success' => true,
                'disponible' => $disponible,
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
                'noches' => $fechaInicio->diffInDays($fechaFin),
                'tipos_disponibles' => $tiposDisponibles,
                'total_habitaciones_disponibles' => $habitacionesDisponibles->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar disponibilidad: ' . $e->getMessage()
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
    public function listarTiposHabitaciones(): JsonResponse
    {
        $tiposHabitaciones = TipoHabitacion::where('activo', true)
            ->withCount(['habitaciones' => function($query) {
                $query->where('estado', 'disponible');
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
                    'habitaciones_disponibles' => $tipo->habitaciones_count,
                    'activo' => $tipo->activo
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tiposHabitaciones,
            'total_tipos' => $tiposHabitaciones->count()
        ]);
    }
}