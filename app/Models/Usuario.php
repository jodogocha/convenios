<?php
// app/Models/Usuario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'nombre',
        'apellido',
        'telefono',
        'fecha_nacimiento',
        'rol_id',
        'activo',
        'email_verificado',
        'fecha_verificacion_email',
        'intentos_fallidos',
        'bloqueado_hasta',
        'token_recuperacion',
        'token_expiracion',
        'ultima_sesion',
        'ip_ultima_sesion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token_recuperacion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verificado' => 'boolean',
        'activo' => 'boolean',
        'fecha_verificacion_email' => 'datetime',
        'fecha_nacimiento' => 'date',
        'bloqueado_hasta' => 'datetime',
        'token_expiracion' => 'datetime',
        'ultima_sesion' => 'datetime',
        'password' => 'hashed',
        'intentos_fallidos' => 'integer',
    ];

    /**
     * Relación con el rol
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación con auditoría
     */
    public function auditorias(): HasMany
    {
        return $this->hasMany(Auditoria::class, 'usuario_id');
    }

    /**
     * Relación con intentos de login
     */
    public function intentosLogin(): HasMany
    {
        return $this->hasMany(IntentoLogin::class, 'email_o_username', 'email');
    }

    /**
     * Verificar si el usuario está bloqueado
     */
    public function estaBloqueado(): bool
    {
        return $this->bloqueado_hasta && $this->bloqueado_hasta > now();
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function tienePermiso(string $permiso): bool
    {
        return $this->rol->permisos->contains('nombre', $permiso);
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function tieneRol(string $rol): bool
    {
        return $this->rol->nombre === $rol;
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Incrementar intentos fallidos
     */
    public function incrementarIntentosFallidos(): void
    {
        $this->increment('intentos_fallidos');
        
        $maxIntentos = ConfiguracionSistema::obtenerValor('max_intentos_login', 5);
        $tiempoBloqueo = ConfiguracionSistema::obtenerValor('tiempo_bloqueo_minutos', 30);
        
        if ($this->intentos_fallidos >= $maxIntentos) {
            $this->update([
                'bloqueado_hasta' => now()->addMinutes($tiempoBloqueo)
            ]);
        }
    }

    /**
     * Resetear intentos fallidos
     */
    public function resetearIntentosFallidos(): void
    {
        $this->update([
            'intentos_fallidos' => 0,
            'bloqueado_hasta' => null,
        ]);
    }

    /**
     * Actualizar última sesión
     */
    public function actualizarUltimaSesion(): void
    {
        $this->update([
            'ultima_sesion' => now(),
            'ip_ultima_sesion' => request()->ip(),
        ]);
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para usuarios no bloqueados
     */
    public function scopeNoBloqueados($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('bloqueado_hasta')
              ->orWhere('bloqueado_hasta', '<=', now());
        });
    }

    /**
     * Scope para buscar usuarios
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('username', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%")
              ->orWhere('nombre', 'like', "%{$termino}%")
              ->orWhere('apellido', 'like', "%{$termino}%");
        });
    }

    /**
     * Boot del modelo para eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Evento al crear usuario
        static::created(function ($usuario) {
            Auditoria::registrar($usuario->id, 'crear_usuario', 'usuarios', $usuario->id, null, [
                'username' => $usuario->username,
                'email' => $usuario->email,
                'rol_id' => $usuario->rol_id,
            ]);
        });

        // Evento al actualizar usuario
        static::updated(function ($usuario) {
            if ($usuario->isDirty()) {
                $cambios = $usuario->getDirty();
                $original = $usuario->getOriginal();
                
                Auditoria::registrar($usuario->id, 'actualizar_usuario', 'usuarios', $usuario->id, $original, $cambios);
            }
        });
    }
}