<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
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
        $reservations = Reservation::forUser(Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Crear una nueva reserva
     */
    public function store(Request $request): JsonResponse
    {
        // Validar los datos
        $validated = $request->validate([
            'room_type' => 'required|string|in:suite-deluxe,habitacion-estandar,suite-familiar',
            'room_name' => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        // Verificar disponibilidad (opcional - por ahora permitimos todas)
        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);

        // Crear la reserva
        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'room_type' => $validated['room_type'],
            'room_name' => $validated['room_name'],
            'price_per_night' => $validated['price_per_night'],
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'] ?? null,
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'data' => $reservation
        ], 201);
    }

    /**
     * Obtener una reserva específica
     */
    public function show(Reservation $reservation): JsonResponse
    {
        // Verificar que la reserva pertenece al usuario autenticado
        if ($reservation->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Actualizar una reserva
     */
    public function update(Request $request, Reservation $reservation): JsonResponse
    {
        // Verificar que la reserva pertenece al usuario autenticado
        if ($reservation->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        // Solo permitir actualizar si está pendiente
        if ($reservation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar una reserva confirmada'
            ], 400);
        }

        $validated = $request->validate([
            'check_in_date' => 'sometimes|date|after_or_equal:today',
            'check_out_date' => 'sometimes|date|after:check_in_date',
            'guest_name' => 'sometimes|string|max:255',
            'guest_email' => 'sometimes|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $reservation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada exitosamente',
            'data' => $reservation
        ]);
    }

    /**
     * Cancelar una reserva
     */
    public function cancel(Reservation $reservation): JsonResponse
    {
        // Verificar que la reserva pertenece al usuario autenticado
        if ($reservation->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        // Solo permitir cancelar si está pendiente o confirmada
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar esta reserva'
            ], 400);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Reserva cancelada exitosamente',
            'data' => $reservation
        ]);
    }

    /**
     * Obtener tipos de habitación disponibles
     */
    public function getRoomTypes(): JsonResponse
    {
        $roomTypes = [
            [
                'id' => 'suite-deluxe',
                'name' => 'Suite Deluxe',
                'description' => 'Habitación amplia con vista al mar',
                'price' => 95,
                'image' => '/images/suite-deluxe.jpg'
            ],
            [
                'id' => 'habitacion-estandar',
                'name' => 'Habitación Estándar',
                'description' => 'Cómoda habitación con todas las comodidades',
                'price' => 65,
                'image' => '/images/habitacion-estandar.jpg'
            ],
            [
                'id' => 'suite-familiar',
                'name' => 'Suite Familiar',
                'description' => 'Perfecta para familias hasta 4 personas',
                'price' => 120,
                'image' => '/images/suite-familiar.jpg'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $roomTypes
        ]);
    }
}
