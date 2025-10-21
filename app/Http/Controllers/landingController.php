<?php

namespace App\Http\Controllers;

use App\Models\servicio;
use App\Models\TipoHabitacion;
use Illuminate\Http\Request;

class landingController extends Controller
{
    public function showHome(){
        $tipoHab = TipoHabitacion::activas()->get();
        $servicios = servicio::activos()->get();

        return view('LandingPage', compact('tipoHab', 'servicios'));
    }
}
