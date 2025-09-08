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
    public function show(Rol $role)  // Cambiado de $rol a $role
    {
        $role->load(['permisos', 'usuarios']);
        $permisosPorModulo = $role->permisos->groupBy('modulo');

        return view('roles.show', compact('role', 'permisosPorModulo'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Rol $role)  // Cambiado de $rol a $role
    {
        // No permitir editar el rol super_admin
        if ($role->nombre === 'super_admin') {
            return redirect()->route('roles.show', $role)
                           ->with('error', 'El rol super_admin no puede ser editado por seguridad.');
        }

        $permisos = Permiso::activos()->orderBy('modulo')->orderBy('nombre')->get();
        $permisosPorModulo = $permisos->groupBy('modulo');
        $permisosAsignados = $role->permisos->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permisos', 'permisosPorModulo', 'permisosAsignados'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, Rol $role)  // Cambiado de $rol a $role
    {
        // No permitir editar el rol super_admin
        if ($role->nombre === 'super_admin') {
            return redirect()->route('roles.show', $role)
                           ->with('error', 'El rol super_admin no puede ser editado por seguridad.');
        }

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z_]+$/',
                Rule::unique('roles')->ignore($role->id)
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

        $valoresAnteriores = $role->toArray();

        $role->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo', true)
        ]);

        // Sincronizar permisos
        $permisosAnteriores = $role->permisos->pluck('id')->toArray();
        $permisosNuevos = $request->get('permisos', []);
        
        $role->permisos()->sync($permisosNuevos);

        // Registrar auditoría
        $cambios = $role->getChanges();
        if (!empty($cambios) || $permisosAnteriores != $permisosNuevos) {
            $cambios['permisos_anteriores'] = $permisosAnteriores;
            $cambios['permisos_nuevos'] = $permisosNuevos;
            
            Auditoria::registrar(
                Auth::id(),
                'actualizar_rol',
                'roles',
                $role->id,
                $valoresAnteriores,
                $cambios
            );
        }

        return redirect()->route('roles.show', $role)
                        ->with('success', "Rol '{$role->descripcion}' actualizado correctamente.");
    }

    /**
     * Eliminar rol
     */
    public function destroy(Rol $role)  // Cambiado de $rol a $role
    {
        // No permitir eliminar ciertos roles críticos
        if (in_array($role->nombre, ['super_admin', 'admin', 'usuario'])) {
            return redirect()->route('roles.index')
                           ->with('error', 'No se puede eliminar este rol porque es crítico para el sistema.');
        }

        // Verificar si tiene usuarios asignados
        if ($role->usuarios()->count() > 0) {
            return redirect()->route('roles.index')
                           ->with('error', "No se puede eliminar el rol '{$role->descripcion}' porque tiene usuarios asignados.");
        }

        $nombre = $role->descripcion;
        $datosAnteriores = $role->toArray();

        // Registrar auditoría antes de eliminar
        Auditoria::registrar(
            Auth::id(),
            'eliminar_rol',
            'roles',
            $role->id,
            $datosAnteriores,
            null
        );

        $role->delete();

        return redirect()->route('roles.index')
                        ->with('success', "Rol '{$nombre}' eliminado correctamente.");
    }

    /**
     * Cambiar estado del rol (activar/desactivar)
     */
    public function toggleEstado(Rol $role)  // Cambiado de $rol a $role
    {
        // No permitir desactivar roles críticos
        if (in_array($role->nombre, ['super_admin', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede desactivar este rol porque es crítico para el sistema.'
            ], 400);
        }

        $estadoAnterior = $role->activo;
        $role->update(['activo' => !$role->activo]);

        // Registrar auditoría
        Auditoria::registrar(
            Auth::id(),
            'cambiar_estado_rol',
            'roles',
            $role->id,
            ['activo' => $estadoAnterior],
            ['activo' => $role->activo]
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado del rol actualizado correctamente',
            'nuevo_estado' => $role->activo
        ]);
    }

    /**
     * Clonar rol (crear una copia)
     */
    public function clonar(Rol $role)  // Cambiado de $rol a $role
    {
        $permisos = Permiso::activos()->orderBy('modulo')->orderBy('nombre')->get();
        $permisosPorModulo = $permisos->groupBy('modulo');
        $permisosAsignados = $role->permisos->pluck('id')->toArray();

        return view('roles.clonar', compact('role', 'permisos', 'permisosPorModulo', 'permisosAsignados'));
    }

    /**
     * Procesar clonación de rol
     */
    public function procesarClon(Request $request, Rol $rolOriginal)  // Mantenemos rolOriginal para claridad
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
    public function getPermisos(Rol $role)  // Cambiado de $rol a $role
    {
        return response()->json([
            'rol' => $role->nombre,
            'permisos' => $role->permisos->pluck('nombre')
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

            foreach ($roles as $role) {
                $permisos = $role->permisos->pluck('descripcion')->implode(', ');
                
                fputcsv($file, [
                    $role->id,
                    $role->nombre,
                    $role->descripcion,
                    $role->activo ? 'Activo' : 'Inactivo',
                    $role->usuarios_count,
                    $permisos,
                    $role->created_at->format('d/m/Y H:i:s')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}