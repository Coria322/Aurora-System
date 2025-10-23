<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class redirectType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if(!$user->tipo_usuario){
            abort(406, "Tu cuenta tiene problemas, Comunicate con soporte");
        }

        $ruta = match ($user->tipo_usuario){
            'admin' => 'admin.dashboard',
            'cliente' => 'dashboard',
            'empleado' => 'employee.dashboard',
            default => 'home'
        };

        if(!$request -> routeIs($ruta)){
            return redirect()->route($ruta);
        }

        return $next($request);
    }
}
