<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class landingController extends Controller
{
    public function showHome(){
        return view('LandingPage');
    }
}
