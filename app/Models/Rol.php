<?php
// app/Models/Rol.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }

    /**
     * Relación muchos a muchos con permisos
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'rol_permisos', 'rol_id', 'permiso_id')
                    ->withTimestamps();
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function tienePermiso(string $permiso): bool
    {
        return $this->permisos->contains('nombre', $permiso);
    }

    /**
     * Asignar permiso al rol
     */
    public function asignarPermiso(int $permisoId): void
    {
        if (!$this->permisos->contains('id', $permisoId)) {
            $this->permisos()->attach($permisoId);
        }
    }

    /**
     * Revocar permiso del rol
     */
    public function revocarPermiso(int $permisoId): void
    {
        $this->permisos()->detach($permisoId);
    }

    /**
     * Sincronizar permisos del rol
     */
    public function sincronizarPermisos(array $permisosIds): void
    {
        $this->permisos()->sync($permisosIds);
    }

    /**
     * Scope para roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar roles
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }

    /**
     * Obtener cantidad de usuarios con este rol
     */
    public function getCantidadUsuariosAttribute(): int
    {
        return $this->usuarios()->count();
    }
}