<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Auditoria extends Model
{
    use HasFactory;

    protected $table = 'auditoria';
    public $timestamps = false;

    /**
     * CAMPOS CORREGIDOS PARA COINCIDIR CON LA MIGRACIÓN REAL
     */
    protected $fillable = [
        'usuario_id',
        'accion',
        'tabla_afectada',        // MIGRACIÓN USA: tabla_afectada
        'registro_id',
        'valores_anteriores',
        'valores_nuevos',
        'ip_address',           // MIGRACIÓN USA: ip_address
        'user_agent',
        'fecha_hora',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
        'fecha_hora' => 'datetime',
        'registro_id' => 'integer',
    ];

    /**
     * Relación con usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Método estático para registrar - CORREGIDO CON CAMPOS REALES
     */
    public static function registrar(
        ?int $usuarioId,
        string $accion,
        ?string $tablaAfectada = null,
        ?int $registroId = null,
        ?array $valoresAnteriores = null,
        ?array $valoresNuevos = null
    ): void {
        try {
            self::create([
                'usuario_id' => $usuarioId,
                'accion' => $accion,
                'tabla_afectada' => $tablaAfectada,    // CAMPO REAL DE MIGRACIÓN
                'registro_id' => $registroId,
                'valores_anteriores' => $valoresAnteriores,
                'valores_nuevos' => $valoresNuevos,
                'ip_address' => request()->ip(),        // CAMPO REAL DE MIGRACIÓN
                'user_agent' => request()->userAgent(),
                'fecha_hora' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error registrando auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Registrar cambios en usuarios con más detalle
     */
    public static function registrarCambioUsuario(
        string $evento,
        Usuario $usuario,
        ?array $valoresAnteriores = null,
        ?array $valoresNuevos = null
    ): void {
        $usuarioActual = Auth::id();
        
        // Filtrar campos sensibles
        $camposExcluidos = ['password', 'remember_token', 'token_recuperacion', 'updated_at'];
        
        if ($valoresAnteriores) {
            $valoresAnteriores = array_diff_key($valoresAnteriores, array_flip($camposExcluidos));
        }
        
        if ($valoresNuevos) {
            $valoresNuevos = array_diff_key($valoresNuevos, array_flip($camposExcluidos));
        }

        $accion = match($evento) {
            'created' => 'crear_usuario',
            'updated' => 'actualizar_usuario', 
            'deleted' => 'eliminar_usuario',
            default => $evento
        };

        self::registrar(
            $usuarioActual,
            $accion,
            'usuarios',
            $usuario->id,
            $valoresAnteriores,
            $valoresNuevos
        );
    }

    /**
     * Registrar login exitoso
     */
    public static function registrarLogin(int $usuarioId): void
    {
        self::registrar($usuarioId, 'login_exitoso');
    }

    /**
     * Registrar logout
     */
    public static function registrarLogout(int $usuarioId): void
    {
        self::registrar($usuarioId, 'logout');
    }

    /**
     * Registrar intento de acceso denegado
     */
    public static function registrarAccesoDenegado(int $usuarioId, string $recurso): void
    {
        self::registrar($usuarioId, 'acceso_denegado', null, null, null, ['recurso' => $recurso]);
    }

    // Scopes útiles
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    public function scopePorTabla($query, $tabla)
    {
        return $query->where('tabla_afectada', $tabla);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
    }

    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_hora', 'desc');
    }

    /**
     * Obtener el nombre del usuario que realizó la acción
     */
    public function getNombreUsuarioAttribute(): string
    {
        return $this->usuario ? $this->usuario->nombre_completo : 'Usuario eliminado';
    }

    /**
     * Obtener descripción amigable de la acción
     */
    public function getDescripcionAccionAttribute(): string
    {
        $acciones = [
            'login_exitoso' => 'Inicio de sesión exitoso',
            'login_fallido' => 'Intento de inicio de sesión fallido',
            'logout' => 'Cierre de sesión',
            'crear_usuario' => 'Creación de usuario',
            'actualizar_usuario' => 'Actualización de usuario',
            'eliminar_usuario' => 'Eliminación de usuario',
            'cambio_password' => 'Cambio de contraseña',
            'acceso_denegado' => 'Acceso denegado',
        ];

        return $acciones[$this->accion] ?? ucfirst(str_replace('_', ' ', $this->accion));
    }

    /**
     * Obtener icono para la acción
     */
    public function getIconoAccionAttribute(): string
    {
        $iconos = [
            'login_exitoso' => 'fas fa-sign-in-alt text-success',
            'logout' => 'fas fa-sign-out-alt text-info',
            'crear_usuario' => 'fas fa-user-plus text-success',
            'actualizar_usuario' => 'fas fa-edit text-warning',
            'eliminar_usuario' => 'fas fa-user-times text-danger',
            'cambio_password' => 'fas fa-key text-info',
            'acceso_denegado' => 'fas fa-ban text-danger',
        ];

        return $iconos[$this->accion] ?? 'fas fa-info-circle text-muted';
    }
}