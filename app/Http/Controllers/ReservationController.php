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
     * Obtener tipos de habitación disponibles
     */
    public function getRoomTypes(): JsonResponse
    {
        $tiposHabitacion = TipoHabitacion::where('activo', true)
            ->withCount(['habitaciones' => function($query) {
                $query->where('estado', 'disponible');
            }])
            ->get()
            ->map(function($tipo) {
                return [
                    'id' => $tipo->id_tipo_habitacion,
                    'name' => $tipo->nombre,
                    'description' => $tipo->descripcion,
                    'price' => $tipo->precio_noche,
                    'capacity' => $tipo->capacidad_maxima,
                    'available_rooms' => $tipo->habitaciones_count,
                    'services' => $tipo->servicios_incluidos,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tiposHabitacion
        ]);
    }
}