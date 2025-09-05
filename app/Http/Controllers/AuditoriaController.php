<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditoria;
use App\Models\Usuario;

class AuditoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkrole:super_admin'); // Solo super admin puede ver auditoría
    }

    /**
     * Mostrar listado de auditoría
     */
    public function index(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->orderBy('fecha_hora', 'desc');

        // Filtros
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->get('usuario_id'));
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->get('accion'));
        }

        if ($request->filled('tabla_afectada')) {
            $query->where('tabla_afectada', $request->get('tabla_afectada'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->get('fecha_hasta'));
        }

        $auditorias = $query->paginate(20);
        $usuarios = Usuario::select('id', 'nombre', 'apellido', 'username')->get();

        // Acciones disponibles para filtrar
        $acciones = [
            'login_exitoso' => 'Login exitoso',
            'logout' => 'Logout',
            'crear_usuario' => 'Crear usuario',
            'actualizar_usuario' => 'Actualizar usuario',
            'eliminar_usuario' => 'Eliminar usuario',
            'cambio_password' => 'Cambio de contraseña',
            'acceso_denegado' => 'Acceso denegado',
        ];

        // Tablas disponibles para filtrar
        $tablas = [
            'usuarios' => 'Usuarios',
            'roles' => 'Roles',
            'permisos' => 'Permisos',
        ];

        return view('admin.auditoria.index', compact('auditorias', 'usuarios', 'acciones', 'tablas'));
    }

    /**
     * Mostrar detalles de una auditoría específica
     */
    public function show(Auditoria $auditoria)
    {
        $auditoria->load('usuario');
        return view('admin.auditoria.show', compact('auditoria'));
    }

    /**
     * Exportar auditoría a CSV
     */
    public function export(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->orderBy('fecha_hora', 'desc');

        // Aplicar los mismos filtros que en index
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->get('usuario_id'));
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->get('accion'));
        }

        if ($request->filled('tabla_afectada')) {
            $query->where('tabla_afectada', $request->get('tabla_afectada'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->get('fecha_hasta'));
        }

        $auditorias = $query->get();

        $filename = 'auditoria_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($auditorias) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Usuario',
                'Acción',
                'Tabla',
                'ID del Registro',
                'IP',
                'Fecha y Hora',
                'Cambios'
            ], ';');

            foreach ($auditorias as $auditoria) {
                $cambios = '';
                if ($auditoria->valores_anteriores && $auditoria->valores_nuevos) {
                    $cambiosArray = [];
                    foreach ($auditoria->valores_nuevos as $key => $newValue) {
                        if (isset($auditoria->valores_anteriores[$key])) {
                            $cambiosArray[] = "$key: '{$auditoria->valores_anteriores[$key]}' → '$newValue'";
                        }
                    }
                    $cambios = implode(' | ', $cambiosArray);
                }

                fputcsv($file, [
                    $auditoria->id,
                    $auditoria->nombre_usuario,
                    $auditoria->descripcion_accion,
                    $auditoria->tabla_afectada,
                    $auditoria->registro_id,
                    $auditoria->ip_address,
                    $auditoria->fecha_hora->format('d/m/Y H:i:s'),
                    $cambios
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Limpiar auditorías antiguas
     */
    public function clean(Request $request)
    {
        $request->validate([
            'dias' => 'required|integer|min:30|max:730' // Entre 30 días y 2 años
        ]);

        $dias = $request->get('dias', 90);
        $fechaLimite = now()->subDays($dias);

        $eliminadas = Auditoria::where('fecha_hora', '<', $fechaLimite)->delete();

        return redirect()->route('auditoria.index')
            ->with('success', "Se eliminaron {$eliminadas} registros de auditoría anteriores a {$dias} días.");
    }

    /**
     * API: Actividad reciente para dashboard
     */
    public function actividadReciente()
    {
        $actividad = Auditoria::with('usuario')
            ->recientes()
            ->limit(10)
            ->get()
            ->map(function ($auditoria) {
                return [
                    'usuario' => $auditoria->nombre_usuario,
                    'accion' => $auditoria->descripcion_accion,
                    'fecha' => $auditoria->fecha_hora->format('d/m/Y H:i:s'),
                    'icono' => $auditoria->icono_accion
                ];
            });

        return response()->json($actividad);
    }

    /**
     * API: Estadísticas de auditoría para dashboard
     */
    public function estadisticas(Request $request)
    {
        $fecha = $request->get('fecha', now()->toDateString());
        
        $estadisticas = [
            'logins' => Auditoria::where('accion', 'login_exitoso')
                ->whereDate('fecha_hora', $fecha)
                ->count(),
                
            'cambios' => Auditoria::whereIn('accion', ['crear_usuario', 'actualizar_usuario', 'eliminar_usuario'])
                ->whereDate('fecha_hora', $fecha)
                ->count(),
                
            'accesos_denegados' => Auditoria::where('accion', 'acceso_denegado')
                ->whereDate('fecha_hora', $fecha)
                ->count(),
                
            'total_dia' => Auditoria::whereDate('fecha_hora', $fecha)->count(),
        ];
        
        return response()->json($estadisticas);
    }

    /**
     * API: Actividad reciente para dashboard
     */
    public function actividadReciente()
    {
        $actividad = Auditoria::with('usuario')
            ->recientes()
            ->limit(10)
            ->get()
            ->map(function ($auditoria) {
                return [
                    'usuario' => $auditoria->nombre_usuario,
                    'accion' => $auditoria->descripcion_accion,
                    'fecha' => $auditoria->fecha_hora->format('d/m/Y H:i:s'),
                    'icono' => $auditoria->icono_accion,
                    'url' => route('auditoria.show', $auditoria)
                ];
            });

        return response()->json($actividad);
    }

    /**
     * API: Gráfico de actividad por días
     */
    public function actividadPorDias(Request $request)
    {
        $dias = $request->get('dias', 7);
        
        $actividad = Auditoria::selectRaw('DATE(fecha_hora) as fecha, COUNT(*) as total')
            ->where('fecha_hora', '>=', now()->subDays($dias))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha');
            
        // Generar fechas faltantes con 0
        $fechas = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->toDateString();
            $fechas[$fecha] = $actividad->get($fecha, 0);
        }
        
        return response()->json([
            'labels' => array_keys($fechas),
            'values' => array_values($fechas)
        ]);
    }
}