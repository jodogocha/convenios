<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Auditoria;
use App\Models\IntentoLogin;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $usuario = Auth::user();
        
        // Estadísticas para el dashboard
        $estadisticas = [
            'total_usuarios' => Usuario::count(),
            'usuarios_activos' => Usuario::where('activo', 1)->count(),
            'usuarios_bloqueados' => Usuario::where('bloqueado', 1)->count(),
            'total_roles' => DB::table('roles')->count(),
        ];

        // Actividad reciente del usuario
        $actividad_reciente = Auditoria::where('usuario_id', $usuario->id)
            ->orderBy('fecha_hora', 'desc')
            ->limit(10)
            ->get();

        // Últimos intentos de login (solo si tiene permisos)
        $ultimos_logins = null;
        if ($usuario->tienePermiso('sistema.ver')) {
            $ultimos_logins = IntentoLogin::with('usuario')
                ->orderBy('fecha_hora', 'desc')
                ->limit(10)
                ->get();
        }

        // Datos para gráficos
        $logins_por_dia = IntentoLogin::where('exitoso', 1)
            ->where('fecha_hora', '>=', now()->subDays(7))
            ->selectRaw('DATE(fecha_hora) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return view('admin.dashboard', compact(
            'usuario',
            'estadisticas',
            'actividad_reciente',
            'ultimos_logins',
            'logins_por_dia'
        ));
    }
}