<?php
// app/Models/Convenio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Convenio extends Model
{
    use HasFactory;

    protected $table = 'convenios';

    protected $fillable = [
        'institucion_contraparte',
        'tipo_convenio',
        'objeto',
        'fecha_firma',
        'fecha_vencimiento',
        'vigencia_indefinida',
        'coordinador_convenio',
        'pais_region',
        'signatarios',
        'archivo_convenio_path',
        'archivo_convenio_nombre',
        'archivo_convenio_size',
        'dictamen_numero',
        'version_final_firmada',
        'numero_convenio',
        'estado',
        'notificaciones_enviadas',
        'fecha_notificacion_ori',
        'fecha_notificacion_ua',
        'usuario_creador_id',
        'usuario_coordinador_id',
        'usuario_aprobador_id',
        'fecha_aprobacion',
        'observaciones',
        'metadata',
    ];

    protected $casts = [
        'fecha_firma' => 'date',
        'fecha_vencimiento' => 'date',
        'vigencia_indefinida' => 'boolean',
        'version_final_firmada' => 'boolean',
        'notificaciones_enviadas' => 'boolean',
        'fecha_notificacion_ori' => 'datetime',
        'fecha_notificacion_ua' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'signatarios' => 'array',
        'metadata' => 'array',
        'archivo_convenio_size' => 'integer',
    ];

    // Relaciones
    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id');
    }

    public function usuarioCoordinador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_coordinador_id');
    }

    public function usuarioAprobador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_aprobador_id');
    }

    // Boot para generar número automático
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($convenio) {
            if (!$convenio->numero_convenio) {
                $convenio->numero_convenio = static::generarNumeroConvenio();
            }
        });

        // Registrar auditorías
        static::created(function ($convenio) {
            Auditoria::registrar(
                auth()->id(),
                'crear_convenio',
                'convenios',
                $convenio->id,
                null,
                $convenio->toArray()
            );
        });

        static::updated(function ($convenio) {
            $valoresOriginales = $convenio->getOriginal();
            $valoresNuevos = $convenio->getChanges();
            
            if (!empty($valoresNuevos) && count($valoresNuevos) > 1) {
                Auditoria::registrar(
                    auth()->id(),
                    'actualizar_convenio',
                    'convenios',
                    $convenio->id,
                    $valoresOriginales,
                    $valoresNuevos
                );
            }
        });

        static::deleted(function ($convenio) {
            Auditoria::registrar(
                auth()->id(),
                'eliminar_convenio',
                'convenios',
                $convenio->id,
                $convenio->toArray(),
                null
            );
        });
    }

    /**
     * Generar número único de convenio con formato: UNI/YYYY/NNNN
     */
    public static function generarNumeroConvenio(): string
    {
        $year = now()->year;
        $prefix = "UNI/{$year}/";
        
        // Obtener el último número del año actual
        $ultimoConvenio = static::where('numero_convenio', 'LIKE', $prefix . '%')
            ->orderBy('numero_convenio', 'desc')
            ->first();

        if ($ultimoConvenio) {
            // Extraer el número correlativo
            $ultimoNumero = (int) substr($ultimoConvenio->numero_convenio, strlen($prefix));
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return $prefix . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente_aprobacion');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido')
                     ->orWhere(function($q) {
                         $q->where('fecha_vencimiento', '<', now())
                           ->where('vigencia_indefinida', false)
                           ->whereIn('estado', ['activo', 'aprobado']);
                     });
    }

    public function scopePorVencer($query, $dias = 30)
    {
        return $query->where('fecha_vencimiento', '<=', now()->addDays($dias))
                     ->where('fecha_vencimiento', '>=', now())
                     ->where('vigencia_indefinida', false)
                     ->whereIn('estado', ['activo', 'aprobado']);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('institucion_contraparte', 'LIKE', "%{$termino}%")
              ->orWhere('numero_convenio', 'LIKE', "%{$termino}%")
              ->orWhere('objeto', 'LIKE', "%{$termino}%")
              ->orWhere('tipo_convenio', 'LIKE', "%{$termino}%");
        });
    }

    // Accessors y Mutators
    public function getEstadoBadgeAttribute(): string
    {
        $badges = [
            'borrador' => 'secondary',
            'pendiente_aprobacion' => 'warning',
            'aprobado' => 'info',
            'activo' => 'success',
            'vencido' => 'danger',
            'cancelado' => 'dark'
        ];

        return $badges[$this->estado] ?? 'secondary';
    }

    public function getEstadoTextoAttribute(): string
    {
        $textos = [
            'borrador' => 'Borrador',
            'pendiente_aprobacion' => 'Pendiente de Aprobación',
            'aprobado' => 'Aprobado',
            'activo' => 'Activo',
            'vencido' => 'Vencido',
            'cancelado' => 'Cancelado'
        ];

        return $textos[$this->estado] ?? 'Desconocido';
    }

    public function getVigenciaTextoAttribute(): string
    {
        if ($this->vigencia_indefinida) {
            return 'Indefinida';
        }

        if (!$this->fecha_vencimiento) {
            return 'No especificada';
        }

        $hoy = now();
        $vencimiento = $this->fecha_vencimiento;

        if ($vencimiento->isPast()) {
            return 'Vencido (' . $vencimiento->diffForHumans() . ')';
        }

        if ($vencimiento->isToday()) {
            return 'Vence hoy';
        }

        return 'Vence ' . $vencimiento->diffForHumans();
    }

    public function getDiasParaVencimientoAttribute(): ?int
    {
        if ($this->vigencia_indefinida || !$this->fecha_vencimiento) {
            return null;
        }

        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function getEstaVencidoAttribute(): bool
    {
        if ($this->vigencia_indefinida) {
            return false;
        }

        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }

    public function getArchivoPesoFormateadoAttribute(): ?string
    {
        if (!$this->archivo_convenio_size) {
            return null;
        }

        $bytes = $this->archivo_convenio_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getArchivoUrlAttribute(): ?string
    {
        if (!$this->archivo_convenio_path) {
            return null;
        }

        return Storage::url($this->archivo_convenio_path);
    }

    public function getSignatariosTextoAttribute(): string
    {
        if (!$this->signatarios || !is_array($this->signatarios)) {
            return 'No especificados';
        }

        return implode(', ', array_filter($this->signatarios));
    }

    // Métodos de estado
    public function puedeSerEditado(): bool
    {
        return in_array($this->estado, ['borrador', 'pendiente_aprobacion']);
    }

    public function puedeSerAprobado(): bool
    {
        return $this->estado === 'pendiente_aprobacion' && $this->version_final_firmada;
    }

    public function puedeSerCancelado(): bool
    {
        return !in_array($this->estado, ['cancelado', 'vencido']);
    }

    public function aprobar($usuarioId = null): bool
    {
        if (!$this->puedeSerAprobado()) {
            return false;
        }

        $this->update([
            'estado' => 'aprobado',
            'usuario_aprobador_id' => $usuarioId ?? auth()->id(),
            'fecha_aprobacion' => now(),
        ]);

        return true;
    }

    public function activar(): bool
    {
        if ($this->estado !== 'aprobado') {
            return false;
        }

        $this->update(['estado' => 'activo']);
        return true;
    }

    public function cancelar($observacion = null): bool
    {
        if (!$this->puedeSerCancelado()) {
            return false;
        }

        $observaciones = $this->observaciones ? $this->observaciones . "\n\n" : '';
        $observaciones .= "Cancelado el " . now()->format('d/m/Y H:i:s');
        if ($observacion) {
            $observaciones .= ": " . $observacion;
        }

        $this->update([
            'estado' => 'cancelado',
            'observaciones' => $observaciones
        ]);

        return true;
    }

    /**
     * Verificar si el convenio está próximo a vencer
     */
    public function proximoAVencer($dias = 30): bool
    {
        if ($this->vigencia_indefinida || !$this->fecha_vencimiento) {
            return false;
        }

        $diasRestantes = $this->dias_para_vencimiento;
        return $diasRestantes !== null && $diasRestantes <= $dias && $diasRestantes >= 0;
    }

    /**
     * Obtener tipos de convenio disponibles
     */
    public static function getTiposConvenio(): array
    {
        return [
            'Marco' => 'Convenio Marco',
            'Específico' => 'Convenio Específico',
            'Cooperación' => 'Convenio de Cooperación',
            'Intercambio' => 'Convenio de Intercambio',
            'Investigación' => 'Convenio de Investigación',
            'Prácticas' => 'Convenio de Prácticas',
            'Pasantías' => 'Convenio de Pasantías',
            'Servicios' => 'Convenio de Servicios',
            'Otro' => 'Otro tipo de convenio'
        ];
    }

    /**
     * Obtener coordinadores disponibles
     */
    public static function getCoordinadores(): array
    {
        return [
            'Rectorado' => 'Rectorado',
            'Vicerrectorado Académico' => 'Vicerrectorado Académico',
            'Vicerrectorado de Investigación' => 'Vicerrectorado de Investigación',
            'Facultad de Ingeniería' => 'Facultad de Ingeniería',
            'Facultad de Arquitectura' => 'Facultad de Arquitectura',
            'Facultad de Ciencias Químicas' => 'Facultad de Ciencias Químicas',
            'Facultad de Tecnología' => 'Facultad de Tecnología',
            'Dirección de Relaciones Internacionales' => 'Dirección de Relaciones Internacionales',
            'Dirección de Extensión' => 'Dirección de Extensión',
            'Otro' => 'Otro coordinador'
        ];
    }

    /**
     * Obtener estados disponibles
     */
    public static function getEstados(): array
    {
        return [
            'borrador' => 'Borrador',
            'pendiente_aprobacion' => 'Pendiente de Aprobación',
            'aprobado' => 'Aprobado',
            'activo' => 'Activo',
            'vencido' => 'Vencido',
            'cancelado' => 'Cancelado'
        ];
    }
}