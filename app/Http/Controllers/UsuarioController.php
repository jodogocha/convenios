<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // El middleware CheckRole ya está aplicado en las rutas
    }

    /**
     * Mostrar listado de usuarios
     */
    public function index(Request $request)
    {
        $query = Usuario::with('rol');

        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('email', 'LIKE', "%{$buscar}%")
                  ->orWhere('username', 'LIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('rol')) {
            $query->where('rol_id', $request->get('rol'));
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->get('estado'));
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Rol::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $roles = Rol::where('activo', true)->get();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'usuario' => 'required|string|max:50|unique:usuarios,username',
            'password' => 'required|string|min:8|confirmed',
            'rol_id' => 'required|exists:roles,id',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Este email ya está registrado',
            'usuario.required' => 'El nombre de usuario es obligatorio',
            'usuario.unique' => 'Este nombre de usuario ya está en uso',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'La confirmación de contraseña no coincide',
            'rol_id.required' => 'Debe seleccionar un rol',
            'rol_id.exists' => 'El rol seleccionado no es válido'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'username' => $request->usuario,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'telefono' => $request->telefono,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('usuarios.index')
                        ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar usuario específico
     */
    public function show(Usuario $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Usuario $usuario)
    {
        $roles = Rol::where('activo', true)->get();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('usuarios')->ignore($usuario->id)],
            'usuario' => ['required', 'string', 'max:50', Rule::unique('usuarios', 'username')->ignore($usuario->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'rol_id' => 'required|exists:roles,id',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Este email ya está registrado',
            'usuario.required' => 'El nombre de usuario es obligatorio',
            'usuario.unique' => 'Este nombre de usuario ya está en uso',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'La confirmación de contraseña no coincide',
            'rol_id.required' => 'Debe seleccionar un rol',
            'rol_id.exists' => 'El rol seleccionado no es válido'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'username' => $request->usuario,
            'rol_id' => $request->rol_id,
            'telefono' => $request->telefono,
            'activo' => $request->has('activo'),
        ];

        // Solo actualizar contraseña si se proporciona
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
                        ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(Usuario $usuario)
    {
        // Prevenir eliminación del usuario actual
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                           ->with('error', 'No puedes eliminar tu propio usuario');
        }

        // Prevenir eliminación de usuarios con rol de super_admin
        if ($usuario->rol && $usuario->rol->nombre === 'super_admin') {
            return redirect()->route('usuarios.index')
                           ->with('error', 'No se puede eliminar un usuario super administrador');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
                        ->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Cambiar estado del usuario (activar/desactivar)
     */
    public function toggleEstado(Usuario $usuario)
    {
        // Verificar permisos
        if (!Auth::user()->tieneRol('admin') && !Auth::user()->tieneRol('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para cambiar el estado de usuarios'
            ], 403);
        }

        // No puede desactivar su propio usuario
        if ($usuario->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes cambiar el estado de tu propio usuario'
            ], 400);
        }

        // Cambiar estado
        $usuario->update([
            'activo' => !$usuario->activo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del usuario actualizado correctamente',
            'nuevo_estado' => $usuario->activo
        ]);
    }
}