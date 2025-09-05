<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit;
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
        $query = Audit::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->filled('event')) {
            $query->where('event', $request->get('event'));
        }

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->get('auditable_type'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->get('fecha_hasta'));
        }

        $auditorias = $query->paginate(20);
        $usuarios = Usuario::select('id', 'nombre', 'apellido', 'username')->get();

        return view('admin.auditoria.index', compact('auditorias', 'usuarios'));
    }

    /**
     * Mostrar detalles de una auditoría específica
     */
    public function show(Audit $auditoria)
    {
        $auditoria->load(['user', 'auditable']);
        return view('admin.auditoria.show', compact('auditoria'));
    }

    /**
     * Exportar auditoría a CSV
     */
    public function export(Request $request)
    {
        $query = Audit::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc');

        // Aplicar los mismos filtros que en index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->filled('event')) {
            $query->where('event', $request->get('event'));
        }

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->get('auditable_type'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->get('fecha_hasta'));
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
                'Evento',
                'Modelo',
                'ID del Registro',
                'IP',
                'Fecha y Hora',
                'Cambios'
            ], ';');

            foreach ($auditorias as $auditoria) {
                $cambios = '';
                if ($auditoria->old_values && $auditoria->new_values) {
                    $cambiosArray = [];
                    foreach ($auditoria->new_values as $key => $newValue) {
                        if (isset($auditoria->old_values[$key])) {
                            $cambiosArray[] = "$key: '{$auditoria->old_values[$key]}' → '$newValue'";
                        }
                    }
                    $cambios = implode(' | ', $cambiosArray);
                }

                fputcsv($file, [
                    $auditoria->id,
                    $auditoria->user ? $auditoria->user->nombre_completo : 'Sistema',
                    ucfirst($auditoria->event),
                    class_basename($auditoria->auditable_type),
                    $auditoria->auditable_id,
                    $auditoria->ip_address,
                    $auditoria->created_at->format('d/m/Y H:i:s'),
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

        $eliminadas = Audit::where('created_at', '<', $fechaLimite)->delete();

        return redirect()->route('auditoria.index')
            ->with('success', "Se eliminaron {$eliminadas} registros de auditoría anteriores a {$dias} días.");
    }
}