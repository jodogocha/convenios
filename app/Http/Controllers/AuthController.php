<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;
use App\Models\IntentoLogin;
use App\Models\Auditoria;
use App\Models\ConfiguracionSistema;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:150',
            'password' => 'required|string|min:6',
        ], [
            'login.required' => 'El usuario o email es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('login'));
        }

        $loginField = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Verificar si la IP está bloqueada por múltiples intentos fallidos
        if (IntentoLogin::ipBloqueada($request->ip())) {
            IntentoLogin::registrarFallido($loginField, 'IP bloqueada por múltiples intentos fallidos');
            return redirect()->back()->with('error', 'Su IP está temporalmente bloqueada por múltiples intentos fallidos.');
        }

        // Determinar si el login es email o username
        $loginType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Buscar usuario
        $usuario = Usuario::where($loginType, $loginField)->first();

        if (!$usuario) {
            IntentoLogin::registrarFallido($loginField, 'Usuario no encontrado');
            return redirect()->back()
                ->with('error', 'Credenciales incorrectas.')
                ->withInput($request->only('login'));
        }

        // Verificar si el usuario está activo
        if (!$usuario->activo) {
            IntentoLogin::registrarFallido($loginField, 'Usuario inactivo');
            return redirect()->back()->with('error', 'Su cuenta está desactivada. Contacte al administrador.');
        }

        // Verificar si el usuario está bloqueado
        if ($usuario->estaBloqueado()) {
            IntentoLogin::registrarFallido($loginField, 'Usuario bloqueado temporalmente');
            return redirect()->back()->with('error', 'Su cuenta está temporalmente bloqueada.');
        }

        // Verificar contraseña
        if (!Hash::check($password, $usuario->password)) {
            $usuario->incrementarIntentosFallidos();
            IntentoLogin::registrarFallido($loginField, 'Contraseña incorrecta');
            
            $intentosRestantes = ConfiguracionSistema::obtenerValor('max_intentos_login', 5) - $usuario->intentos_fallidos;
            $mensaje = $intentosRestantes > 0 
                ? "Credenciales incorrectas. Intentos restantes: {$intentosRestantes}"
                : "Cuenta bloqueada por múltiples intentos fallidos.";
            
            return redirect()->back()
                ->with('error', $mensaje)
                ->withInput($request->only('login'));
        }

        // Login exitoso
        Auth::login($usuario, $remember);
        
        // Resetear intentos fallidos y actualizar datos de sesión
        $usuario->resetearIntentosFallidos();
        $usuario->actualizarUltimaSesion();

        // Registrar login exitoso
        IntentoLogin::registrarExitoso($loginField);
        Auditoria::registrarLogin($usuario->id);

        // Regenerar sesión por seguridad
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', "¡Bienvenido, {$usuario->nombre}!");
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        
        if ($userId) {
            Auditoria::registrarLogout($userId);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Mostrar dashboard principal
     */
    public function dashboard()
    {
        $usuario = Auth::user();
        
        // Datos para el dashboard
        $data = [
            'usuario' => $usuario,
            'ultimoLogin' => $usuario->ultima_sesion,
            'rol' => $usuario->rol->nombre,
            'permisos' => $usuario->rol->permisos->pluck('nombre')->toArray(),
        ];

        return view('dashboard', $data);
    }

    /**
     * Mostrar perfil del usuario
     */
    public function perfil()
    {
        $usuario = Auth::user();
        return view('auth.perfil', compact('usuario'));
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'email' => 'required|email|max:100|unique:usuarios,email,' . $usuario->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $usuario->update($request->only([
            'nombre', 'apellido', 'telefono', 'fecha_nacimiento', 'email'
        ]));

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:' . ConfiguracionSistema::obtenerValor('longitud_minima_password', 8),
                'confirmed'
            ],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $usuario = Auth::user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $usuario->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Validar nueva contraseña según configuraciones
        $passwordErrors = $this->validarPassword($request->password);
        if (!empty($passwordErrors)) {
            return redirect()->back()
                ->withErrors(['password' => $passwordErrors]);
        }

        // Actualizar contraseña
        $usuario->update([
            'password' => Hash::make($request->password)
        ]);

        // Registrar cambio de contraseña
        Auditoria::registrar($usuario->id, 'cambio_password');

        return redirect()->back()->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Validar contraseña según configuraciones del sistema
     */
    private function validarPassword(string $password): array
    {
        $errores = [];

        if (ConfiguracionSistema::obtenerValor('requerir_mayuscula_password', true)) {
            if (!preg_match('/[A-Z]/', $password)) {
                $errores[] = 'La contraseña debe contener al menos una mayúscula.';
            }
        }

        if (ConfiguracionSistema::obtenerValor('requerir_numero_password', true)) {
            if (!preg_match('/[0-9]/', $password)) {
                $errores[] = 'La contraseña debe contener al menos un número.';
            }
        }

        if (ConfiguracionSistema::obtenerValor('requerir_simbolo_password', true)) {
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                $errores[] = 'La contraseña debe contener al menos un símbolo especial.';
            }
        }

        return $errores;
    }
}