<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Rutas del Sistema de Reservas
|--------------------------------------------------------------------------
|
| Rutas API para el manejo de reservas del hotel Aurora
| Todas las rutas están protegidas por middleware de autenticación
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Endpoints de Disponibilidad
    |--------------------------------------------------------------------------
    */
    
    /**
     * Verificar disponibilidad de habitaciones
     * GET /api/reservas/disponibilidad
     * 
     * Parámetros:
     * - fecha_inicio (required): Fecha de inicio en formato Y-m-d
     * - fecha_fin (required): Fecha de fin en formato Y-m-d
     * - tipo_habitacion (optional): ID del tipo de habitación
     * 
     * Respuesta:
     * - disponible: boolean
     * - tipos_disponibles: array con información de tipos disponibles
     * - total_habitaciones_disponibles: número total
     */
    Route::get('/reservas/disponibilidad', [ReservationController::class, 'verificarDisponibilidad'])
        ->name('api.reservas.disponibilidad');

    /*
    |--------------------------------------------------------------------------
    | Endpoints de Creación de Reservas
    |--------------------------------------------------------------------------
    */
    
    /**
     * Crear nueva reserva
     * POST /api/reservas/crear
     * 
     * Parámetros:
     * - fecha_inicio (required): Fecha de inicio
     * - fecha_fin (required): Fecha de fin
     * - tipo_habitacion_id (required): ID del tipo de habitación
     * - usuario_id (required): ID del usuario que hace la reserva
     * - cantidad_personas (required): Número de personas
     * - nombre_huesped (required): Nombre del huésped
     * - apellido_paterno (required): Apellido paterno
     * - apellido_materno (optional): Apellido materno
     * - email_huesped (required): Email del huésped
     * - telefono_huesped (optional): Teléfono del huésped
     * - observaciones (optional): Observaciones adicionales
     * 
     * Respuesta:
     * - success: boolean
     * - reserva_id: ID de la reserva creada
     * - data: información completa de la reserva
     */
    Route::post('/reservas/crear', [ReservationController::class, 'crearReserva'])
        ->name('api.reservas.crear');

    /*
    |--------------------------------------------------------------------------
    | Endpoints de Información de Habitaciones
    |--------------------------------------------------------------------------
    */
    
    /**
     * Listar tipos de habitaciones con precios
     * GET /api/habitaciones/tipos
     * 
     * Respuesta:
     * - success: boolean
     * - data: array con tipos de habitaciones
     * - total_tipos: número total de tipos
     */
    Route::get('/habitaciones/tipos', [ReservationController::class, 'listarTiposHabitaciones'])
        ->name('api.habitaciones.tipos');

    /*
    |--------------------------------------------------------------------------
    | Endpoints de Gestión de Reservas Existentes
    |--------------------------------------------------------------------------
    */
    
    /**
     * Obtener todas las reservas del usuario autenticado
     * GET /api/reservas
     */
    Route::get('/reservas', [ReservationController::class, 'index'])
        ->name('api.reservas.index');

    /**
     * Obtener la reserva activa más reciente
     * GET /api/reservas/activa
     */
    Route::get('/reservas/activa', [ReservationController::class, 'active'])
        ->name('api.reservas.activa');

    /**
     * Obtener una reserva específica
     * GET /api/reservas/{reserva}
     */
    Route::get('/reservas/{reserva}', [ReservationController::class, 'show'])
        ->name('api.reservas.show');

    /**
     * Cancelar una reserva
     * PATCH /api/reservas/{reserva}/cancel
     */
    Route::patch('/reservas/{reserva}/cancel', [ReservationController::class, 'cancel'])
        ->name('api.reservas.cancel');

});

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Sin Autenticación)
|--------------------------------------------------------------------------
|
| Rutas que no requieren autenticación para consultas básicas
|
*/

/**
 * Listar tipos de habitaciones (público)
 * GET /api/habitaciones/tipos-publico
 * 
 * Versión pública para mostrar tipos de habitaciones sin autenticación
 */
Route::get('/habitaciones/tipos-publico', [ReservationController::class, 'listarTiposHabitaciones'])
    ->name('api.habitaciones.tipos.publico');

/**
 * Verificar disponibilidad (público) - VERSIÓN FUNCIONAL
 * GET /api/reservas/disponibilidad-publico
 */

/**
 * Endpoint alternativo de disponibilidad - VERSIÓN FUNCIONAL
 * GET /api/disponibilidad-test
 */
Route::get('/disponibilidad-test', [ReservationController::class, 'verificarDisponibilidad']);

/**
 * Crear reserva pública - VERSIÓN SIMPLIFICADA
 * POST /api/reservas/crear-publico
 */
Route::middleware(['web', 'auth'])->post('/reservas/crear-publico', [ReservationController::class, 'crearPublico']);

/**
 * Endpoint de prueba simple
 * GET /api/test
 */
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now()
    ]);
});

/**
 * Endpoint de prueba con modelo directo
 * GET /api/test-modelo
 */
Route::get('/test-modelo', function () {
    try {
        $count = \App\Models\Habitacion::count();
        return response()->json([
            'success' => true,
            'total_habitaciones' => $count,
            'message' => 'Modelo funcionando correctamente'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});
