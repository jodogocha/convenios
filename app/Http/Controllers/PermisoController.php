<?php
// app/Http/Controllers/PermisoController.php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PermisoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkrole:super_admin'); // Solo super_admin puede gestionar permisos
    }

    /**
     * Mostrar listado de permisos
     */
    public function index(Request $request)
    {
        $query = Permiso::withCount('roles');

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->get('modulo'));
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('activo', $request->get('estado'));
        }

        $permisos = $query->orderBy('modulo')->orderBy('nombre')->paginate(20);
        
        // Obtener módulos únicos para el filtro
        $modulos = Permiso::select('modulo')
                         ->whereNotNull('modulo')
                         ->distinct()
                         ->orderBy('modulo')
                         ->pluck('modulo');

        return view('permisos.index', compact('permisos', 'modulos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        // Obtener módulos existentes para sugerencias
        $modulosExistentes = Permiso::select('modulo')
                                  ->whereNotNull('modulo')
                                  ->distinct()
                                  ->orderBy('modulo')
                                  ->pluck('modulo');

        return view('permisos.create', compact('modulosExistentes'));
    }

    /**
     * Guardar nuevo permiso
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:permisos,nombre|regex:/^[a-z_\.]+$/',
            'descripcion' => 'required|string|max:255',
            'modulo' => 'required|string|max:50|regex:/^[a-z_]+$/',
            'activo' => 'boolean'
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio.',
            'nombre.unique' => 'Ya existe un permiso con este nombre.',
            'nombre.regex' => 'El nombre solo puede contener letras minúsculas, guiones bajos y puntos.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'modulo.required' => 'El módulo es obligatorio.',
            'modulo.regex' => 'El módulo solo puede contener letras minúsculas y guiones bajos.'
        ]);

        $permiso = Permiso::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'modulo' => $request->modulo,
            'activo' => $request->boolean('activo', true)
        ]);

        // Registrar auditoría
        Auditoria::registrar(
            Auth::id(),
            'crear_permiso',
            'permisos',
            $permiso->id,
            null,
            $permiso->toArray()
        );

        return redirect()->route('permisos.index')
                        ->with('success', "Permiso '{$permiso->descripcion}' creado correctamente.");
    }

    /**
     * Mostrar permiso específico
     */
    public function show(Permiso $permiso)
    {
        $permiso->load('roles.usuarios');

        return view('permisos.show', compact('permiso'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Permiso $permiso)
    {
        // Obtener módulos existentes para sugerencias
        $modulosExistentes = Permiso::select('modulo')
                                  ->whereNotNull('modulo')
                                  ->distinct()
                                  ->orderBy('modulo')
                                  ->pluck('modulo');

        return view('permisos.edit', compact('permiso', 'modulosExistentes'));
    }

    /**
     * Actualizar permiso
     */
    public function update(Request $request, Permiso $permiso)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z_\.]+$/',
                Rule::unique('permisos')->ignore($permiso->id)
            ],
            'descripcion' => 'required|string|max:255',
            'modulo' => 'required|string|max:50|regex:/^[a-z_]+$/',
            'activo' => 'boolean'
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio.',
            'nombre.unique' => 'Ya existe un permiso con este nombre.',
            'nombre.regex' => 'El nombre solo puede contener letras minúsculas, guiones bajos y puntos.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'modulo.required' => 'El módulo es obligatorio.',
            'modulo.regex' => 'El módulo solo puede contener letras minúsculas y guiones bajos.'
        ]);

        $valoresAnteriores = $permiso->toArray();

        $permiso->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'modulo' => $request->modulo,
            'activo' => $request->boolean('activo', true)
        ]);

        // Registrar auditoría
        $cambios = $permiso->getChanges();
        if (!empty($cambios)) {
            Auditoria::registrar(
                Auth::id(),
                'actualizar_permiso',
                'permisos',
                $permiso->id,
                $valoresAnteriores,
                $cambios
            );
        }

        return redirect()->route('permisos.show', $permiso)
                        ->with('success', "Permiso '{$permiso->descripcion}' actualizado correctamente.");
    }

    /**
     * Eliminar permiso
     */
    public function destroy(Permiso $permiso)
    {
        // Verificar si el permiso está siendo usado por algún rol
        if ($permiso->roles()->count() > 0) {
            $roles = $permiso->roles->pluck('descripcion')->implode(', ');
            return redirect()->route('permisos.index')
                           ->with('error', "No se puede eliminar el permiso '{$permiso->descripcion}' porque está asignado a los roles: {$roles}.");
        }

        $nombre = $permiso->descripcion;
        $datosAnteriores = $permiso->toArray();

        // Registrar auditoría antes de eliminar
        Auditoria::registrar(
            Auth::id(),
            'eliminar_permiso',
            'permisos',
            $permiso->id,
            $datosAnteriores,
            null
        );

        $permiso->delete();

        return redirect()->route('permisos.index')
                        ->with('success', "Permiso '{$nombre}' eliminado correctamente.");
    }

    /**
     * Cambiar estado del permiso (activar/desactivar)
     */
    public function toggleEstado(Permiso $permiso)
    {
        $estadoAnterior = $permiso->activo;
        $permiso->update(['activo' => !$permiso->activo]);

        // Registrar auditoría
        Auditoria::registrar(
            Auth::id(),
            'cambiar_estado_permiso',
            'permisos',
            $permiso->id,
            ['activo' => $estadoAnterior],
            ['activo' => $permiso->activo]
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado del permiso actualizado correctamente',
            'nuevo_estado' => $permiso->activo
        ]);
    }

    /**
     * Vista de gestión masiva de permisos por módulo
     */
    public function gestionMasiva()
    {
        $permisosPorModulo = Permiso::activos()
                                  ->orderBy('modulo')
                                  ->orderBy('nombre')
                                  ->get()
                                  ->groupBy('modulo');

        return view('permisos.gestion-masiva', compact('permisosPorModulo'));
    }

    /**
     * Crear múltiples permisos para un módulo
     */
    public function crearMasivo(Request $request)
    {
        $request->validate([
            'modulo' => 'required|string|max:50|regex:/^[a-z_]+$/',
            'acciones' => 'required|array|min:1',
            'acciones.*' => 'required|string|max:20|regex:/^[a-z_]+$/',
            'descripcion_base' => 'required|string|max:100'
        ], [
            'modulo.required' => 'El módulo es obligatorio.',
            'modulo.regex' => 'El módulo solo puede contener letras minúsculas y guiones bajos.',
            'acciones.required' => 'Debe especificar al menos una acción.',
            'acciones.*.required' => 'Todas las acciones son obligatorias.',
            'acciones.*.regex' => 'Las acciones solo pueden contener letras minúsculas y guiones bajos.',
            'descripcion_base.required' => 'La descripción base es obligatoria.'
        ]);

        $creados = 0;
        $errores = [];

        foreach ($request->acciones as $accion) {
            $nombrePermiso = $request->modulo . '.' . $accion;
            
            // Verificar si ya existe
            if (Permiso::where('nombre', $nombrePermiso)->exists()) {
                $errores[] = "El permiso '{$nombrePermiso}' ya existe.";
                continue;
            }

            // Crear permiso
            $descripcion = ucfirst($accion) . ' ' . $request->descripcion_base;
            
            $permiso = Permiso::create([
                'nombre' => $nombrePermiso,
                'descripcion' => $descripcion,
                'modulo' => $request->modulo,
                'activo' => true
            ]);

            // Registrar auditoría
            Auditoria::registrar(
                Auth::id(),
                'crear_permiso_masivo',
                'permisos',
                $permiso->id,
                null,
                $permiso->toArray()
            );

            $creados++;
        }

        $mensaje = "Se crearon {$creados} permisos correctamente.";
        if (!empty($errores)) {
            $mensaje .= ' Errores: ' . implode(' ', $errores);
        }

        return redirect()->route('permisos.gestion-masiva')
                        ->with($creados > 0 ? 'success' : 'warning', $mensaje);
    }

    /**
     * Exportar permisos a CSV
     */
    public function export(Request $request)
    {
        $query = Permiso::withCount('roles');

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->get('modulo'));
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->get('estado'));
        }

        $permisos = $query->orderBy('modulo')->orderBy('nombre')->get();

        $filename = 'permisos_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($permisos) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Nombre',
                'Descripción',
                'Módulo',
                'Estado',
                'Roles Asignados',
                'Fecha Creación'
            ], ';');

            foreach ($permisos as $permiso) {
                $roles = $permiso->roles->pluck('descripcion')->implode(', ');
                
                fputcsv($file, [
                    $permiso->id,
                    $permiso->nombre,
                    $permiso->descripcion,
                    $permiso->modulo,
                    $permiso->activo ? 'Activo' : 'Inactivo',
                    $roles,
                    $permiso->created_at->format('d/m/Y H:i:s')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}