<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditoria;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder.');
        }

        $usuario = Auth::user();

        // Verificar si el usuario está activo
        if (!$usuario->activo) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Su cuenta está desactivada.');
        }

        // Verificar si el usuario está bloqueado - USANDO MÉTODO CORREGIDO
        if ($usuario->estaBloqueado()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Su cuenta está temporalmente bloqueada.');
        }

        // Verificar si el usuario tiene uno de los roles requeridos
        $tieneAcceso = false;
        foreach ($roles as $rol) {
            if ($usuario->tieneRol($rol)) {
                $tieneAcceso = true;
                break;
            }
        }

        if (!$tieneAcceso) {
            // Registrar intento de acceso denegado - CON MANEJO DE ERRORES
            try {
                Auditoria::registrarAccesoDenegado($usuario->id, $request->path());
            } catch (\Exception $e) {
                \Log::error('Error registrando acceso denegado: ' . $e->getMessage());
            }
            
            return redirect()->route('dashboard')->with('error', 'No tiene permisos para acceder a este recurso.');
        }

        return $next($request);
    }
}