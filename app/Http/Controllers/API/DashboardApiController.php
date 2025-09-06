<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Auditoria;
use App\Models\IntentoLogin;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Estadísticas generales del dashboard
     */
    public function estadisticas()
    {
        $estadisticas = [
            // Estadísticas de usuarios
            'total_usuarios' => Usuario::count(),
            'usuarios_activos' => Usuario::where('activo', true)->count(),
            'usuarios_inactivos' => Usuario::where('activo', false)->count(),
            'usuarios_mes_actual' => Usuario::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            
            // Estadísticas de convenios
            'total_convenios' => \App\Models\Convenio::count(),
            'convenios_activos' => \App\Models\Convenio::where('estado', 'activo')->count(),
            'convenios_pendientes' => \App\Models\Convenio::where('estado', 'pendiente_aprobacion')->count(),
            'convenios_por_vencer' => \App\Models\Convenio::porVencer(30)->count(),
            'convenios_vencidos' => \App\Models\Convenio::vencidos()->count(),
            'convenios_borradores' => \App\Models\Convenio::where('estado', 'borrador')->count(),
            
            // Estadísticas adicionales
            'total_roles' => DB::table('roles')->count(),
            'logins_hoy' => Auditoria::where('accion', 'login_exitoso')
                ->whereDate('fecha_hora', today())
                ->count(),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Actividad reciente para el dashboard
     */
    public function actividadReciente()
    {
        $actividad = Auditoria::with('usuario')
            ->orderBy('fecha_hora', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($auditoria) {
                return [
                    'usuario' => $auditoria->nombre_usuario,
                    'accion' => $auditoria->descripcion_accion,
                    'fecha' => $auditoria->fecha_hora->format('d/m/Y H:i:s'),
                    'icono' => $auditoria->icono_accion,
                ];
            });

        return response()->json($actividad);
    }

    /**
     * Estadísticas detalladas de usuarios
     */
    public function estadisticasUsuarios()
    {
        $estadisticas = [
            'total' => Usuario::count(),
            'activos' => Usuario::where('activo', true)->count(),
            'inactivos' => Usuario::where('activo', false)->count(),
            'bloqueados' => Usuario::where('bloqueado_hasta', '>', now())->count(),
            'verificados' => Usuario::where('email_verificado', true)->count(),
            'no_verificados' => Usuario::where('email_verificado', false)->count(),
            
            // Usuarios por rol
            'por_rol' => Usuario::select('roles.nombre as rol', DB::raw('count(*) as total'))
                ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->groupBy('roles.id', 'roles.nombre')
                ->get()
                ->pluck('total', 'rol'),
                
            // Nuevos usuarios en los últimos 7 días
            'nuevos_ultimos_7_dias' => Usuario::where('created_at', '>=', now()->subDays(7))->count(),
            
            // Usuarios que han iniciado sesión en los últimos 30 días
            'activos_ultimos_30_dias' => Usuario::where('ultima_sesion', '>=', now()->subDays(30))->count(),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Gráfico de usuarios registrados por mes (últimos 6 meses)
     */
    public function usuariosPorMes()
    {
        $usuarios = Usuario::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];
        
        // Generar los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $year = $fecha->year;
            $month = $fecha->month;
            
            $labels[] = $fecha->format('M Y');
            
            $usuario = $usuarios->first(function ($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            });
            
            $values[] = $usuario ? $usuario->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    /**
     * Actividad de login por días (últimos 7 días)
     */
    public function loginsPorDia()
    {
        $logins = Auditoria::selectRaw('DATE(fecha_hora) as fecha, COUNT(*) as total')
            ->where('accion', 'login_exitoso')
            ->where('fecha_hora', '>=', now()->subDays(7))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->pluck('total', 'fecha');

        $labels = [];
        $values = [];
        
        // Generar los últimos 7 días
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('d/m');
            $values[] = $logins->get($fecha, 0);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    /**
     * Estadísticas de intentos de login
     */
    public function estadisticasLogin()
    {
        $hoy = today();
        
        $estadisticas = [
            'intentos_exitosos_hoy' => IntentoLogin::where('exitoso', true)
                ->whereDate('fecha_intento', $hoy)
                ->count(),
                
            'intentos_fallidos_hoy' => IntentoLogin::where('exitoso', false)
                ->whereDate('fecha_intento', $hoy)
                ->count(),
                
            'intentos_exitosos_semana' => IntentoLogin::where('exitoso', true)
                ->where('fecha_intento', '>=', now()->subDays(7))
                ->count(),
                
            'intentos_fallidos_semana' => IntentoLogin::where('exitoso', false)
                ->where('fecha_intento', '>=', now()->subDays(7))
                ->count(),
                
            'ips_unicas_hoy' => IntentoLogin::whereDate('fecha_intento', $hoy)
                ->distinct('ip_address')
                ->count(),
        ];

        return response()->json($estadisticas);
    }
}