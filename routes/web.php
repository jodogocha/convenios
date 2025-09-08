<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;  
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\RolController;        // NUEVA LÍNEA
use App\Http\Controllers\PermisoController;    // NUEVA LÍNEA
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\InformeController;    // NUEVA LÍNEA PARA INFORMES

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
    // API ENDPOINTS PARA DASHBOARD
    // ========================================
    Route::prefix('api/dashboard')->group(function () {
        Route::get('/estadisticas', [DashboardApiController::class, 'estadisticas']);
        Route::get('/actividad-reciente', [DashboardApiController::class, 'actividadReciente']);
        Route::get('/estadisticas-usuarios', [DashboardApiController::class, 'estadisticasUsuarios']);
        Route::get('/usuarios-mes', [DashboardApiController::class, 'usuariosPorMes']);
        Route::get('/convenios-mes', [DashboardApiController::class, 'conveniosPorMes']); // NUEVA LÍNEA
        Route::get('/logins-dia', [DashboardApiController::class, 'loginsPorDia']);
        Route::get('/estadisticas-login', [DashboardApiController::class, 'estadisticasLogin']);
    });
    
    // ========================================
    // API ENDPOINTS PARA CONVENIOS
    // ========================================
    Route::prefix('api/convenios')->group(function () {
        Route::get('/estadisticas', [ConvenioController::class, 'estadisticasApi']);
    });
    
    // ========================================
    // API ENDPOINTS PARA INFORMES
    // ========================================
    Route::prefix('api/informes')->group(function () {
        Route::get('/estadisticas', [InformeController::class, 'estadisticasApi']);
    });
    
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
    // GESTIÓN DE ROLES - Solo super_admin
    // ========================================
    Route::middleware('checkrole:super_admin')->group(function () {
        // Rutas especiales que deben ir ANTES del resource
        Route::get('/roles/export', [RolController::class, 'export'])->name('roles.export');
        Route::get('/roles/{role}/clonar', [RolController::class, 'clonar'])->name('roles.clonar');
        Route::post('/roles/{role}/clonar', [RolController::class, 'procesarClon'])->name('roles.procesar-clon');
        Route::post('/roles/{role}/toggle-estado', [RolController::class, 'toggleEstado'])->name('roles.toggle-estado');
        
        // Resource completo de roles
        Route::resource('roles', RolController::class)->parameters([
            'roles' => 'role'
        ]);
        
        // API para obtener permisos de un rol
        Route::get('/api/roles/{role}/permisos', [RolController::class, 'getPermisos'])->name('roles.api.permisos');
    });
    
    // ========================================
    // GESTIÓN DE INFORMES
    // ========================================
    Route::middleware('checkrole:usuario,admin,super_admin')->group(function () {
    
        // Rutas especiales que deben ir ANTES del resource para evitar conflictos
        Route::get('/informes/pendientes', [InformeController::class, 'pendientes'])->name('informes.pendientes');
        Route::get('/informes/exportar-excel', [InformeController::class, 'exportarExcel'])->name('informes.exportar-excel');
        Route::get('/informes/{informe}/exportar-pdf', [InformeController::class, 'exportarPdf'])->name('informes.exportar-pdf');
        Route::get('/informes/{informe}/duplicar', [InformeController::class, 'duplicar'])->name('informes.duplicar');
        Route::get('/api/convenios/{convenio}/datos', [InformeController::class, 'getConvenio'])->name('informes.get-convenio');
        
        // Resource completo de informes
        Route::resource('informes', InformeController::class);
        
        // Rutas de acciones específicas
        Route::post('/informes/{informe}/enviar', [InformeController::class, 'enviar'])->name('informes.enviar');
        Route::post('/informes/{informe}/cambiar-estado', [InformeController::class, 'cambiarEstado'])->name('informes.cambiar-estado');
        
        // Rutas que requieren permisos de aprobación
        Route::middleware('checkrole:admin,super_admin')->group(function () {
            Route::post('/informes/{informe}/aprobar', [InformeController::class, 'aprobar'])->name('informes.aprobar');
            Route::post('/informes/{informe}/rechazar', [InformeController::class, 'rechazar'])->name('informes.rechazar');
        });
    });

    // ========================================
    // GESTIÓN DE PERMISOS - Solo super_admin
    // ========================================
    Route::middleware('checkrole:super_admin')->group(function () {
        // Rutas especiales que deben ir ANTES del resource
        Route::get('/permisos/export', [PermisoController::class, 'export'])->name('permisos.export');
        Route::get('/permisos/gestion-masiva', [PermisoController::class, 'gestionMasiva'])->name('permisos.gestion-masiva');
        Route::post('/permisos/crear-masivo', [PermisoController::class, 'crearMasivo'])->name('permisos.crear-masivo');
        Route::post('/permisos/{permiso}/toggle-estado', [PermisoController::class, 'toggleEstado'])->name('permisos.toggle-estado');
        
        // Resource completo de permisos
        Route::resource('permisos', PermisoController::class)->parameters([
            'permisos' => 'permiso'
        ]);
    });
    
    // ========================================
    // GESTIÓN DE CONVENIOS
    // ========================================
    Route::middleware('checkrole:usuario,admin,super_admin')->group(function () {
        
        // Rutas especiales que deben ir ANTES del resource para evitar conflictos
        Route::get('/convenios/pendientes', [ConvenioController::class, 'pendientes'])->name('convenios.pendientes');
        Route::get('/convenios/exportar', [ConvenioController::class, 'exportar'])->name('convenios.exportar');
        Route::get('/convenios/{convenio}/descargar', [ConvenioController::class, 'descargarArchivo'])->name('convenios.descargar');
        
        // Resource completo de convenios
        Route::resource('convenios', ConvenioController::class);
        
        // Rutas de acciones que requieren permisos especiales
        Route::middleware('checkrole:admin,super_admin')->group(function () {
            Route::post('/convenios/{convenio}/aprobar', [ConvenioController::class, 'aprobar'])->name('convenios.aprobar');
            Route::post('/convenios/{convenio}/activar', [ConvenioController::class, 'activar'])->name('convenios.activar');
            Route::post('/convenios/{convenio}/cancelar', [ConvenioController::class, 'cancelar'])->name('convenios.cancelar');
            Route::post('/convenios/{convenio}/cambiar-estado', [ConvenioController::class, 'cambiarEstado'])->name('convenios.cambiar-estado');
        });
    });
    
    // ========================================
    // AUDITORÍA - Solo super_admin
    // ========================================
    Route::middleware('checkrole:super_admin')->group(function () {
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/{auditoria}', [AuditoriaController::class, 'show'])->name('auditoria.show');
        Route::get('/auditoria/export', [AuditoriaController::class, 'export'])->name('auditoria.export');
        Route::post('/auditoria/clean', [AuditoriaController::class, 'clean'])->name('auditoria.clean');
        
        // API para estadísticas de auditoría
        Route::get('/api/auditoria/estadisticas', [AuditoriaController::class, 'estadisticas']);
        Route::get('/api/auditoria/actividad-reciente', [AuditoriaController::class, 'actividadReciente']);
        Route::get('/api/auditoria/actividad-dias', [AuditoriaController::class, 'actividadPorDias']);
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
});