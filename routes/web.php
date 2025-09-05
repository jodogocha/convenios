<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;  
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta raíz - redirige según autenticación
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ========================================
// RUTAS DE AUTENTICACIÓN (sin autenticación requerida)
// ========================================
Route::middleware('guest')->group(function () {
    // Login - USANDO AuthController que coincide con tu vista
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    // Recuperación de contraseña (placeholder)
    Route::get('/password/reset', function () {
        return view('auth.passwords.email');
    })->name('password.request');
});

// ========================================
// RUTAS AUTENTICADAS  
// ========================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal - USANDO AuthController que tiene el método dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Logout - USANDO AuthController
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Perfil de usuario - USANDO AuthController que tiene estos métodos
    Route::get('/perfil', [AuthController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    Route::put('/perfil/password', [AuthController::class, 'cambiarPassword'])->name('perfil.password');
    
    // ========================================
    // GESTIÓN DE USUARIOS - Usando CheckRole
    // ========================================
    Route::middleware('checkrole:admin,super_admin')->group(function () {
        // Ruta para toggle estado (debe ir ANTES del resource)
        Route::post('usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])
            ->name('usuarios.toggle-estado');
            
        // Resource completo de usuarios
        Route::resource('usuarios', UsuarioController::class);
    });
    
    // ========================================
    // CONVENIOS
    // ========================================
    Route::middleware('checkrole:usuario,admin,super_admin')->group(function () {
        Route::get('/convenios', function () {
            return view('admin.convenios.index', ['convenios' => []]);
        })->name('convenios.index');
        
        Route::get('/convenios/create', function () {
            return view('admin.convenios.create');
        })->name('convenios.create');
        
        Route::get('/convenios/pendientes', function () {
            return view('admin.convenios.pendientes', ['convenios' => []]);
        })->name('convenios.pendientes');
    });
    
    // ========================================
    // REPORTES
    // ========================================
    Route::middleware('checkrole:usuario,admin,super_admin')->group(function () {
        Route::get('/reportes', function () {
            return view('admin.reportes.index');
        })->name('reportes.index');
        
        Route::get('/reportes/convenios', function () {
            return view('admin.reportes.convenios');
        })->name('reportes.convenios');
        
        Route::get('/reportes/usuarios', function () {
            return view('admin.reportes.usuarios');
        })->name('reportes.usuarios');
    });
    
    // ========================================
    // CONFIGURACIÓN DEL SISTEMA
    // ========================================
    Route::middleware('checkrole:super_admin')->group(function () {
        Route::get('/configuracion', function () {
            return view('configuracion-temp', [
                'usuario' => Auth::user()
            ]);
        })->name('configuracion.index');
        
        Route::post('/configuracion', function () {
            return redirect()->route('configuracion.index')->with('success', 'Configuración actualizada');
        })->name('configuracion.update');
        
        Route::get('/auditoria', function () {
            $auditorias = \App\Models\Auditoria::with('usuario')->latest()->paginate(20);
            return view('admin.auditoria.index', compact('auditorias'));
        })->name('auditoria.index');
        
        Route::get('/roles', function () {
            return view('admin.roles.index');
        })->name('roles.index');
    });
});

// super admin
Route::middleware('checkrole:super_admin')->group(function () {
    // ... otras rutas existentes de super_admin ...
    
    // AUDITORÍA - Rutas completas
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    Route::get('/auditoria/{auditoria}', [AuditoriaController::class, 'show'])->name('auditoria.show');
    Route::get('/auditoria/export', [AuditoriaController::class, 'export'])->name('auditoria.export');
    Route::post('/auditoria/clean', [AuditoriaController::class, 'clean'])->name('auditoria.clean');
    
    // API para estadísticas (opcional)
    Route::get('/api/auditoria/estadisticas', [AuditoriaController::class, 'estadisticas'])->name('api.auditoria.estadisticas');
    Route::get('/api/auditoria/actividad-reciente', [AuditoriaController::class, 'actividadReciente'])->name('api.auditoria.actividad-reciente');
});

// Ruta catch-all para errores 404
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route('dashboard')->with('error', 'Página no encontrada');
    }
    return redirect()->route('login');
});