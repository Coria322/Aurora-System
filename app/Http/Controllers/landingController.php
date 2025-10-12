<?php

namespace App\Http\Controllers;

use App\Models\TipoHabitacion;
use Illuminate\Http\Request;

class landingController extends Controller
{
    public function showHome(){
        $tipoHab = TipoHabitacion::listar()->get();

        return view('LandingPage');
    }
}
