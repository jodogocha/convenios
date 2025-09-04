<?php
// app/Http/Middleware/AuditMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Auditoria;
use App\Models\ConfiguracionSistema;
use Illuminate\Support\Facades\Auth;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si la auditoría está habilitada
        if (!ConfiguracionSistema::obtenerValor('auditoria_habilitada', true)) {
            return $next($request);
        }

        $response = $next($request);

        // Solo auditar si el usuario está autenticado
        if (Auth::check()) {
            $this->registrarAccion($request, $response);
        }

        return $response;
    }

    /**
     * Registrar la acción en auditoría
     */
    private function registrarAccion(Request $request, Response $response): void
    {
        $usuario = Auth::user();
        $metodo = $request->method();
        $ruta = $request->path();
        $statusCode = $response->getStatusCode();

        // Solo registrar ciertas acciones importantes
        $rutasAuditadas = [
            'usuarios', 'roles', 'permisos', 'configuracion', 'convenios'
        ];

        $debeAuditar = false;
        foreach ($rutasAuditadas as $rutaAuditada) {
            if (str_contains($ruta, $rutaAuditada)) {
                $debeAuditar = true;
                break;
            }
        }

        if (!$debeAuditar || $statusCode >= 400) {
            return;
        }

        // Determinar la acción basada en el método HTTP
        $accion = $this->determinarAccion($metodo, $ruta);

        if ($accion) {
            Auditoria::registrar(
                $usuario->id,
                $accion,
                $this->extraerTablaDeRuta($ruta),
                $this->extraerIdDeRuta($request),
                null,
                $this->obtenerDatosRelevantes($request)
            );
        }
    }

    /**
     * Determinar la acción basada en el método HTTP y la ruta
     */
    private function determinarAccion(string $metodo, string $ruta): ?string
    {
        switch ($metodo) {
            case 'GET':
                return str_contains($ruta, 'create') || str_contains($ruta, 'edit') ? null : 'ver_' . $this->extraerTablaDeRuta($ruta);
            
            case 'POST':
                return 'crear_' . $this->extraerTablaDeRuta($ruta);
            
            case 'PUT':
            case 'PATCH':
                return 'actualizar_' . $this->extraerTablaDeRuta($ruta);
            
            case 'DELETE':
                return 'eliminar_' . $this->extraerTablaDeRuta($ruta);
            
            default:
                return null;
        }
    }

    /**
     * Extraer la tabla principal de la ruta
     */
    private function extraerTablaDeRuta(string $ruta): string
    {
        $segmentos = explode('/', $ruta);
        
        // Buscar el primer segmento que sea una tabla conocida
        $tablasConocidas = ['usuarios', 'roles', 'permisos', 'convenios', 'configuracion'];
        
        foreach ($segmentos as $segmento) {
            if (in_array($segmento, $tablasConocidas)) {
                return $segmento;
            }
        }
        
        return $segmentos[0] ?? 'desconocido';
    }

    /**
     * Extraer ID del registro de la ruta
     */
    private function extraerIdDeRuta(Request $request): ?int
    {
        // Intentar obtener ID de los parámetros de ruta
        $parametros = $request->route()?->parameters() ?? [];
        
        foreach ($parametros as $valor) {
            if (is_numeric($valor)) {
                return (int) $valor;
            }
        }
        
        return null;
    }

    /**
     * Obtener datos relevantes del request
     */
    private function obtenerDatosRelevantes(Request $request): ?array
    {
        $datos = $request->except(['_token', '_method', 'password', 'password_confirmation']);
        
        return empty($datos) ? null : $datos;
    }
}