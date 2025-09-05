<?php
// app/Models/Usuario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

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
    
    protected $hidden = [
        'password',
        'remember_token',
        'token_recuperacion',
    ];

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

    // EVENTO: Registrar auditoría cuando se actualiza un usuario
    protected static function boot()
    {
        parent::boot();

        static::created(function ($usuario) {
            Auditoria::registrarCambioUsuario('created', $usuario, null, $usuario->toArray());
        });

        static::updated(function ($usuario) {
            $valoresOriginales = $usuario->getOriginal();
            $valoresNuevos = $usuario->getChanges();
            
            // Solo registrar si hay cambios significativos
            if (!empty($valoresNuevos) && count($valoresNuevos) > 1) { // Más que solo updated_at
                Auditoria::registrarCambioUsuario('updated', $usuario, $valoresOriginales, $valoresNuevos);
            }
        });

        static::deleted(function ($usuario) {
            Auditoria::registrarCambioUsuario('deleted', $usuario, $usuario->toArray(), null);
        });
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(Auditoria::class, 'usuario_id');
    }

    public function intentosLogin(): HasMany
    {
        return $this->hasMany(IntentoLogin::class, 'email_o_username', 'email');
    }

    public function estaBloqueado(): bool
    {
        return $this->bloqueado_hasta && $this->bloqueado_hasta > now();
    }

    public function tienePermiso(string $permiso): bool
    {
        if (!$this->rol) {
            return false;
        }
        return $this->rol->permisos->contains('nombre', $permiso);
    }

    public function tieneRol(string $rol): bool
    {
        if (!$this->rol) {
            return false;
        }
        return $this->rol->nombre === $rol;
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function incrementarIntentosFallidos(): void
    {
        $this->increment('intentos_fallidos');
        
        $maxIntentos = \App\Models\ConfiguracionSistema::obtenerValor('max_intentos_login', 5);
        $tiempoBloqueo = \App\Models\ConfiguracionSistema::obtenerValor('tiempo_bloqueo_minutos', 30);
        
        if ($this->intentos_fallidos >= $maxIntentos) {
            $this->update([
                'bloqueado_hasta' => now()->addMinutes($tiempoBloqueo)
            ]);
        }
    }

    public function resetearIntentosFallidos(): void
    {
        $this->update([
            'intentos_fallidos' => 0,
            'bloqueado_hasta' => null,
        ]);
    }

    public function actualizarUltimaSesion(): void
    {
        $this->update([
            'ultima_sesion' => now(),
            'ip_ultima_sesion' => request()->ip(),
        ]);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeNoBloqueados($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('bloqueado_hasta')
              ->orWhere('bloqueado_hasta', '<=', now());
        });
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('username', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%")
              ->orWhere('nombre', 'like', "%{$termino}%")
              ->orWhere('apellido', 'like', "%{$termino}%");
        });
    }
}