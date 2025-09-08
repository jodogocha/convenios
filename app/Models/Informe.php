<?php
// app/Models/Informe.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Informe extends Model
{
    use HasFactory;

    protected $table = 'informes';

    protected $fillable = [
        'convenio_id',
        'institucion_co_celebrante',
        'unidad_academica',
        'carrera',
        'fecha_celebracion',
        'vigencia',
        'periodo_evaluado',
        'periodo_desde',
        'periodo_hasta',
        'dependencia_responsable',
        'coordinadores_designados',
        'convenio_celebrado_propuesta',
        'tipo_convenio',
        'convenio_ejecutado',
        'numero_actividades_realizadas',
        'logros_obtenidos',
        'beneficios_alcanzados',
        'dificultades_incidentes',
        'responsabilidad_instalaciones',
        'sugerencias_mejoras',
        'anexo_evidencias',
        'informacion_complementaria',
        'motivos_no_ejecucion',
        'propuestas_mejoras',
        'informacion_complementaria_no_ejecutado',
        'anexos',
        'enlace_google_drive',
        'firmas',
        'fecha_presentacion',
        'observaciones',
        'estado',
        'usuario_creador_id',
        'usuario_revisor_id',
        'fecha_revision',
        'metadata',
    ];

    protected $casts = [
        'fecha_celebracion' => 'date',
        'periodo_desde' => 'date',
        'periodo_hasta' => 'date',
        'fecha_presentacion' => 'date',
        'fecha_revision' => 'datetime',
        'convenio_ejecutado' => 'boolean',
        'coordinadores_designados' => 'array',
        'anexos' => 'array',
        'firmas' => 'array',
        'metadata' => 'array',
        'numero_actividades_realizadas' => 'integer',
    ];

    // Relaciones
    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class);
    }

    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id');
    }

    public function usuarioRevisor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_revisor_id');
    }

    // Boot para auditorías
    protected static function boot()
    {
        parent::boot();

        static::created(function ($informe) {
            Auditoria::registrar(
                auth()->id(),
                'crear_informe',
                'informes',
                $informe->id,
                null,
                $informe->toArray()
            );
        });

        static::updated(function ($informe) {
            $valoresOriginales = $informe->getOriginal();
            $valoresNuevos = $informe->getChanges();
            
            if (!empty($valoresNuevos) && count($valoresNuevos) > 1) {
                Auditoria::registrar(
                    auth()->id(),
                    'actualizar_informe',
                    'informes',
                    $informe->id,
                    $valoresOriginales,
                    $valoresNuevos
                );
            }
        });

        static::deleted(function ($informe) {
            Auditoria::registrar(
                auth()->id(),
                'eliminar_informe',
                'informes',
                $informe->id,
                $informe->toArray(),
                null
            );
        });
    }

    // Scopes
    public function scopeBorradores($query)
    {
        return $query->where('estado', 'borrador');
    }

    public function scopeEnviados($query)
    {
        return $query->where('estado', 'enviado');
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado');
    }

    public function scopePorPeriodo($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_presentacion', [$fechaInicio, $fechaFin]);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('institucion_co_celebrante', 'LIKE', "%{$termino}%")
              ->orWhere('unidad_academica', 'LIKE', "%{$termino}%")
              ->orWhere('carrera', 'LIKE', "%{$termino}%")
              ->orWhere('periodo_evaluado', 'LIKE', "%{$termino}%")
              ->orWhereHas('convenio', function($convQuery) use ($termino) {
                  $convQuery->where('numero_convenio', 'LIKE', "%{$termino}%");
              });
        });
    }

    // Accessors
    public function getEstadoBadgeAttribute(): string
    {
        $badges = [
            'borrador' => 'secondary',
            'enviado' => 'warning',
            'aprobado' => 'success',
            'rechazado' => 'danger'
        ];

        return $badges[$this->estado] ?? 'secondary';
    }

    public function getEstadoTextoAttribute(): string
    {
        $textos = [
            'borrador' => 'Borrador',
            'enviado' => 'Enviado',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado'
        ];

        return $textos[$this->estado] ?? 'Desconocido';
    }

    public function getCoordinadoresTextoAttribute(): string
    {
        if (!$this->coordinadores_designados || !is_array($this->coordinadores_designados)) {
            return 'No especificados';
        }

        return implode(', ', array_filter($this->coordinadores_designados));
    }

    public function getFirmasTextoAttribute(): string
    {
        if (!$this->firmas || !is_array($this->firmas)) {
            return 'Sin firmas';
        }

        return implode(', ', array_filter($this->firmas));
    }

    public function getPeriodoCompletoAttribute(): string
    {
        if ($this->periodo_desde && $this->periodo_hasta) {
            return $this->periodo_desde->format('d/m/Y') . ' - ' . $this->periodo_hasta->format('d/m/Y');
        }
        
        return $this->periodo_evaluado ?? 'No especificado';
    }

    // Métodos de estado
    public function puedeSerEditado(): bool
    {
        return in_array($this->estado, ['borrador', 'rechazado']);
    }

    public function puedeSerEnviado(): bool
    {
        return $this->estado === 'borrador' && $this->validarCamposObligatorios();
    }

    public function puedeSerAprobado(): bool
    {
        return $this->estado === 'enviado';
    }

    public function puedeSerRechazado(): bool
    {
        return $this->estado === 'enviado';
    }

    public function enviar(): bool
    {
        if (!$this->puedeSerEnviado()) {
            return false;
        }

        $this->update(['estado' => 'enviado']);
        return true;
    }

    public function aprobar($usuarioId = null): bool
    {
        if (!$this->puedeSerAprobado()) {
            return false;
        }

        $this->update([
            'estado' => 'aprobado',
            'usuario_revisor_id' => $usuarioId ?? auth()->id(),
            'fecha_revision' => now(),
        ]);

        return true;
    }

    public function rechazar($usuarioId = null, $observacion = null): bool
    {
        if (!$this->puedeSerRechazado()) {
            return false;
        }

        $observaciones = $this->observaciones ? $this->observaciones . "\n\n" : '';
        $observaciones .= "Rechazado el " . now()->format('d/m/Y H:i:s');
        if ($observacion) {
            $observaciones .= ": " . $observacion;
        }

        $this->update([
            'estado' => 'rechazado',
            'usuario_revisor_id' => $usuarioId ?? auth()->id(),
            'fecha_revision' => now(),
            'observaciones' => $observaciones
        ]);

        return true;
    }

    // Validaciones
    private function validarCamposObligatorios(): bool
    {
        $camposObligatorios = [
            'convenio_id',
            'institucion_co_celebrante',
            'unidad_academica',
            'carrera',
            'fecha_celebracion',
            'periodo_evaluado',
            'dependencia_responsable',
            'tipo_convenio',
            'fecha_presentacion'
        ];

        foreach ($camposObligatorios as $campo) {
            if (empty($this->$campo)) {
                return false;
            }
        }

        // Validar que tenga al menos un enlace de Google Drive para evidencias
        if (empty($this->enlace_google_drive)) {
            return false;
        }

        // Validar periodo
        if ($this->periodo_desde && $this->periodo_hasta) {
            if ($this->periodo_desde > $this->periodo_hasta) {
                return false;
            }
        }

        return true;
    }

    // Métodos estáticos
    public static function getTiposConvenio(): array
    {
        return [
            'Marco' => 'Marco',
            'Específico' => 'Específico'
        ];
    }

    public static function getEstados(): array
    {
        return [
            'borrador' => 'Borrador',
            'enviado' => 'Enviado',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado'
        ];
    }

    public static function getUnidadesAcademicas(): array
    {
        return [
            'Facultad de Ingeniería' => 'Facultad de Ingeniería',
            'Facultad de Arquitectura' => 'Facultad de Arquitectura',
            'Facultad de Ciencias Químicas' => 'Facultad de Ciencias Químicas',
            'Facultad de Tecnología' => 'Facultad de Tecnología',
            'Rectorado' => 'Rectorado',
            'Vicerrectorado Académico' => 'Vicerrectorado Académico',
            'Vicerrectorado de Investigación' => 'Vicerrectorado de Investigación',
        ];
    }
}