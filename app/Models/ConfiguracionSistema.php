<?php
// app/Models/ConfiguracionSistema.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ConfiguracionSistema extends Model
{
    use HasFactory;

    protected $table = 'configuracion_sistema';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'tipo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener un valor de configuración
     */
    public static function obtenerValor(string $clave, $valorPorDefecto = null)
    {
        $cacheKey = "config_sistema_{$clave}";
        
        return Cache::remember($cacheKey, 3600, function () use ($clave, $valorPorDefecto) {
            $config = self::where('clave', $clave)->first();
            
            if (!$config) {
                return $valorPorDefecto;
            }
            
            return self::convertirValor($config->valor, $config->tipo);
        });
    }

    /**
     * Establecer un valor de configuración
     */
    public static function establecerValor(string $clave, $valor, ?string $descripcion = null, string $tipo = 'string'): void
    {
        $valorConvertido = is_array($valor) || is_object($valor) ? json_encode($valor) : (string) $valor;
        
        self::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valorConvertido,
                'descripcion' => $descripcion,
                'tipo' => $tipo,
                'updated_at' => now(),
            ]
        );
        
        // Limpiar cache
        Cache::forget("config_sistema_{$clave}");
    }

    /**
     * Convertir valor según su tipo
     */
    private static function convertirValor($valor, string $tipo)
    {
        switch ($tipo) {
            case 'boolean':
                return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
            
            case 'integer':
                return (int) $valor;
            
            case 'json':
                return json_decode($valor, true);
            
            case 'string':
            default:
                return (string) $valor;
        }
    }

    /**
     * Obtener todas las configuraciones agrupadas
     */
    public static function todasLasConfiguraciones(): array
    {
        return Cache::remember('todas_configuraciones_sistema', 3600, function () {
            $configuraciones = self::all();
            $resultado = [];
            
            foreach ($configuraciones as $config) {
                $resultado[$config->clave] = [
                    'valor' => self::convertirValor($config->valor, $config->tipo),
                    'descripcion' => $config->descripcion,
                    'tipo' => $config->tipo,
                ];
            }
            
            return $resultado;
        });
    }

    /**
     * Limpiar toda la cache de configuraciones
     */
    public static function limpiarCache(): void
    {
        $configuraciones = self::all();
        
        foreach ($configuraciones as $config) {
            Cache::forget("config_sistema_{$config->clave}");
        }
        
        Cache::forget('todas_configuraciones_sistema');
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Limpiar cache cuando se actualiza una configuración
        static::saved(function ($config) {
            Cache::forget("config_sistema_{$config->clave}");
            Cache::forget('todas_configuraciones_sistema');
        });

        static::deleted(function ($config) {
            Cache::forget("config_sistema_{$config->clave}");
            Cache::forget('todas_configuraciones_sistema');
        });
    }

    /**
     * Scope para buscar configuraciones
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('clave', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope para configuraciones por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}