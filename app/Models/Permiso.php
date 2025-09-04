<?php
// app/Models/Permiso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'modulo',
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
     * Relación muchos a muchos con roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'rol_permisos', 'permiso_id', 'rol_id')
                    ->withTimestamps();
    }

    /**
     * Scope para permisos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para permisos por módulo
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para buscar permisos
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%")
              ->orWhere('modulo', 'like', "%{$termino}%");
        });
    }

    /**
     * Obtener permisos agrupados por módulo
     */
    public static function porModulos(): array
    {
        return self::activos()
            ->orderBy('modulo')
            ->orderBy('nombre')
            ->get()
            ->groupBy('modulo')
            ->toArray();
    }

    /**
     * Obtener cantidad de roles que tienen este permiso
     */
    public function getCantidadRolesAttribute(): int
    {
        return $this->roles()->count();
    }
}