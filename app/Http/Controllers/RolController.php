<?php
// app/Http/Controllers/RolController.php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkrole:super_admin'); // Solo super_admin puede gestionar roles
    }

    /**
     * Mostrar listado de roles
     */
    public function index(Request $request)
    {
        $query = Rol::withCount('usuarios');

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('activo', $request->get('estado'));
        }

        $roles = $query->orderBy('nombre')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $permisos = Permiso::activos()->orderBy('modulo')->orderBy('nombre')->get();
        $permisosPorModulo = $permisos->groupBy('modulo');

        return view('roles.create', compact('permisos', 'permisosPorModulo'));
    }

    /**
     * Guardar nuevo rol
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre|regex:/^[a-z_]+$/',
            'descripcion' => 'required|string|max:255',
            'activo' => 'boolean',
            'permisos' => 'array',
            'permisos.*' => 'exists:permisos,id'
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con este nombre.',
            'nombre.regex' => 'El nombre solo puede contener letras minúsculas y guiones bajos.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'permisos.*.exists' => 'Uno o más permisos seleccionados no son válidos.'
        ]);

        $rol = Rol::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo', true)
        ]);

        // Asignar permisos si se seleccionaron
        if ($request->filled('permisos')) {
            $rol->permisos()->sync($request->permisos);
        }

        // Registrar auditoría
        Auditoria::registrar(
            Auth::id(),
            'crear_rol',
            'roles',
            $rol->id,
            null,
            $rol->toArray()
        );

        return redirect()->route('roles.index')
                        ->with('success', "Rol '{$rol->descripcion}' creado correctamente.");
    }

    /**
     * Mostrar rol específico
     */
    public function show(Rol $rol)
    {
        $rol->load(['permisos', 'usuarios']);
        $permisosPorModulo = $rol->permisos->groupBy('modulo');

        return view('roles.show', compact('rol', 'permisosPorModulo'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Rol $rol)
    {
        // No permitir editar el rol super_admin
        if ($rol->nombre === 'super_admin') {
            return redirect()->route('roles.show', $rol)
                           ->with('error', 'El rol super_admin no puede ser editado por seguridad.');
        }

        $permisos = Permiso::activos()->orderBy('modulo')->orderBy('nombre')->get();
        $permisosPorModulo = $permisos->groupBy('modulo');
        $permisosAsignados = $rol->permisos->pluck('id')->toArray();

        return view('roles.edit', compact('rol', 'permisos', 'permisosPorModulo', 'permisosAsignados'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, Rol $rol)
    {
        // No permitir editar el rol super_admin
        if ($rol->nombre === 'super_admin') {
            return redirect()->route('roles.show', $rol)
                           ->with('error', 'El rol super_admin no puede ser editado por seguridad.');
        }

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z_]+$/',
                Rule::unique('roles')->ignore($rol->id)
            ],
            'descripcion' => 'required|string|max:255',
            'activo' => 'boolean',
            'permisos' => 'array',
            'permisos.*' => 'exists:permisos,id'
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con este nombre.',
            'nombre.regex' => 'El nombre solo puede contener letras minúsculas y guiones bajos.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'permisos.*.exists' => 'Uno o más permisos seleccionados no son válidos.'
        ]);

        $valoresAnteriores = $rol->toArray();

        $rol->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo', true)
        ]);

        // Sincronizar permisos
        $permisosAnteriores = $rol->permisos->pluck('id')->toArray();
        $permisosNuevos = $request->get('permisos', []);
        
        $rol->permisos()->sync($permisosNuevos);

        // Registrar auditoría
        $cambios = $rol->getChanges();
        if (!empty($cambios) || $permisosAnteriores != $permisosNuevos) {
            $cambios['permisos_anteriores'] = $permisosAnteriores;
            $cambios['permisos_nuevos'] = $permisosNuevos;
            
            Auditoria::registrar(
                Auth::id(),
                'actualizar_rol',
                'roles',
                $rol->id,
                $valoresAnteriores,
                $cambios
            );
        }

        return redirect()->route('roles.show', $rol)
                        ->with('success', "Rol '{$rol->descripcion}' actualizado correctamente.");
    }

    /**
     * Eliminar rol
     */
    public function destroy(Rol $rol)
    {
        // No permitir eliminar ciertos roles críticos
        if (in_array($rol->nombre, ['super_admin', 'admin', 'usuario'])) {
            return redirect()->route('roles.index')
                           ->with('error', 'No se puede eliminar este rol porque es crítico para el sistema.');
        }

        // Verificar si tiene usuarios asignados
        if ($rol->usuarios()->count() > 0) {
            return redirect()->route('roles.index')
                           ->with('error', "No se puede eliminar el rol '{$rol->descripcion}' porque tiene usuarios asignados.");
        }

        $nombre = $rol->descripcion;
        $datosAnteriores = $rol->toArray();

        // Registrar auditoría antes de eliminar
        Auditoria::registrar(
            Auth::id(),
            'eliminar_rol',
            'roles',
            $rol->id,
            $datosAnteriores,
            null
        );

        $rol->delete();

        return redirect()->route('roles.index')
                        ->with('success', "Rol '{$nombre}' eliminado correctamente.");
    }

    /**
     * Cambiar estado del rol (activar/desactivar)
     */
    public function toggleEstado(Rol $rol)
    {
        // No permitir desactivar roles críticos
        if (in_array($rol->nombre, ['super_admin', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede desactivar este rol porque es crítico para el sistema.'
            ], 400);
        }

        $estadoAnterior = $rol->activo;
        $rol->update(['activo' => !$rol->activo]);

        // Registrar auditoría
        Auditoria::registrar(
            Auth::id(),
            'cambiar_estado_rol',
            'roles',
            $rol->id,
            ['activo' => $estadoAnterior],
            ['activo' => $rol->activo]
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado del rol actualizado correctamente',
            'nuevo_estado' => $rol->activo
        ]);
    }

    /**
     * Clonar rol (crear una copia)
     */
    public function clonar(Rol $rol)
    {
        $permisos = Permiso::activos()->orderBy('modulo')->orderBy('nombre')->get();
        $permisosPorModulo = $permisos->groupBy('modulo');
        $permisosAsignados = $rol->permisos->pluck('id')->toArray();

        return view('roles.clonar', compact('rol', 'permisos', 'permisosPorModulo', 'permisosAsignados'));
    }

    /**
     * Procesar clonación de rol
     */
    public function procesarClon(Request $request, Rol $rolOriginal)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre|regex:/^[a-z_]+$/',
            'descripcion' => 'required|string|max:255',
            'activo' => 'boolean',
            'permisos' => 'array',
            'permisos.*' => 'exists:permisos,id'
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con este nombre.',
            'nombre.regex' => 'El nombre solo puede contener letras minúsculas y guiones bajos.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'permisos.*.exists' => 'Uno o más permisos seleccionados no son válidos.'
        ]);

        $nuevoRol = Rol::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo', true)
        ]);

        // Asignar permisos
        if ($request->filled('permisos')) {
            $nuevoRol->permisos()->sync($request->permisos);
        }

        // Registrar auditoría
        Auditoria::registrar(
            Auth::id(),
            'clonar_rol',
            'roles',
            $nuevoRol->id,
            ['rol_original_id' => $rolOriginal->id],
            $nuevoRol->toArray()
        );

        return redirect()->route('roles.show', $nuevoRol)
                        ->with('success', "Rol '{$nuevoRol->descripcion}' clonado correctamente desde '{$rolOriginal->descripcion}'.");
    }

    /**
     * API: Obtener permisos de un rol
     */
    public function getPermisos(Rol $rol)
    {
        return response()->json([
            'rol' => $rol->nombre,
            'permisos' => $rol->permisos->pluck('nombre')
        ]);
    }

    /**
     * Exportar roles a CSV
     */
    public function export(Request $request)
    {
        $query = Rol::withCount('usuarios');

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->get('estado'));
        }

        $roles = $query->orderBy('nombre')->get();

        $filename = 'roles_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($roles) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Nombre',
                'Descripción',
                'Estado',
                'Usuarios Asignados',
                'Permisos',
                'Fecha Creación'
            ], ';');

            foreach ($roles as $rol) {
                $permisos = $rol->permisos->pluck('descripcion')->implode(', ');
                
                fputcsv($file, [
                    $rol->id,
                    $rol->nombre,
                    $rol->descripcion,
                    $rol->activo ? 'Activo' : 'Inactivo',
                    $rol->usuarios_count,
                    $permisos,
                    $rol->created_at->format('d/m/Y H:i:s')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}