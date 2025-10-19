<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran todas las rutas API de la aplicación.
| Se agrupan en rutas protegidas (con autenticación) y públicas.
|
*/

// ----------------------------------------------------------------------
// Rutas protegidas (middleware: web + auth) - Gestión de reservas
// ----------------------------------------------------------------------
Route::middleware(['web', 'auth'])->group(function () {

    // ------------------------------------------------------------------
    // Disponibilidad de habitaciones
    // ------------------------------------------------------------------

    /**
     * GET /api/reservas/disponibilidad
     * Verifica disponibilidad de habitaciones para un rango de fechas.
     * Parámetros:
     *  - fecha_inicio (required) : Fecha inicial (Y-m-d)
     *  - fecha_fin (required)    : Fecha final (Y-m-d)
     *  - tipo_habitacion (optional) : ID del tipo de habitación
     * Respuesta:
     *  - disponible : boolean
     *  - tipos_disponibles : array de tipos disponibles
     *  - total_habitaciones_disponibles : int
     */
    Route::get('/reservas/disponibilidad', [ReservationController::class, 'verificarDisponibilidad'])
        ->name('api.reservas.disponibilidad');

    // ------------------------------------------------------------------
    // Creación de reservas
    // ------------------------------------------------------------------

    /**
     * POST /api/reservas/crear
     * Crea una nueva reserva.
     * Parámetros:
     *  - fecha_inicio, fecha_fin, tipo_habitacion_id, usuario_id, cantidad_personas
     *  - nombre_huesped, apellido_paterno, apellido_materno?, email_huesped
     *  - telefono_huesped?, observaciones?
     * Respuesta:
     *  - success : boolean
     *  - reserva_id : int
     *  - data : información completa de la reserva
     */
    Route::post('/reservas/crear', [ReservationController::class, 'crearReserva'])
        ->name('api.reservas.crear');

    /**
     * POST /api/reservas/crear-publico
     * Crea una reserva desde el sistema público (con autenticación mínima).
     */
    Route::post('/reservas/crear-publico', [ReservationController::class, 'crearPublico'])
        ->name('api.reservas.crear-publico');

    // ------------------------------------------------------------------
    // Información de tipos de habitaciones
    // ------------------------------------------------------------------

    /**
     * GET /api/habitaciones/tipos
     * Lista todos los tipos de habitaciones con su disponibilidad y precio.
     * Respuesta:
     *  - success : boolean
     *  - data : array de tipos de habitaciones
     *  - total_tipos : int
     */
    Route::get('/habitaciones/tipos', [ReservationController::class, 'listarTiposHabitaciones'])
        ->name('api.habitaciones.tipos');

    // ------------------------------------------------------------------
    // Gestión de reservas existentes
    // ------------------------------------------------------------------

    /**
     * GET /api/reservas
     * Obtiene todas las reservas del usuario autenticado.
     */
    Route::get('/reservas', [ReservationController::class, 'index'])
        ->name('api.reservas.index');

    /**
     * GET /api/reservas/activa
     * Obtiene la reserva activa más reciente del usuario.
     */
    Route::get('/reservas/activa', [ReservationController::class, 'active'])
        ->name('api.reservas.activa');

    /**
     * GET /api/reservas/{reserva}
     * Obtiene los detalles de una reserva específica.
     */
    Route::get('/reservas/{reserva}', [ReservationController::class, 'show'])
        ->name('api.reservas.show');

    /**
     * PATCH /api/reservas/{reserva}/cancel
     * Cancela una reserva (si está pendiente o confirmada).
     */
    Route::patch('/reservas/{reserva}/cancel', [ReservationController::class, 'cancel'])
        ->name('api.reservas.cancel');

});

// ----------------------------------------------------------------------
// Rutas públicas (sin autenticación)
// ----------------------------------------------------------------------

/**
 * GET /api/habitaciones/tipos-publico
 * Lista tipos de habitaciones disponibles públicamente.
 */
Route::get('/habitaciones/tipos-publico', [ReservationController::class, 'listarTiposHabitaciones'])
    ->name('api.habitaciones.tipos.publico');

/**
 * GET /api/reservas/disponibilidad-publico
 * Verifica disponibilidad de habitaciones públicamente.
 */
Route::get('/reservas/disponibilidad-publico', [ReservationController::class, 'verificarDisponibilidad'])
    ->name('api.reservas.disponibilidad.publico');

/**
 * GET /api/disponibilidad-test
 * Endpoint alternativo de prueba para verificar disponibilidad.
 */
Route::get('/disponibilidad-test', [ReservationController::class, 'verificarDisponibilidad'])
    ->name('api.reservas.disponibilidad.test');
