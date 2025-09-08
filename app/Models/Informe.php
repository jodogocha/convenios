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

    /**
     * Obtener el progreso de completitud del informe (porcentaje)
     */
    public function getProgresoCompletitudAttribute(): int
    {
        $camposObligatorios = [
            'convenio_id', 'institucion_co_celebrante', 'unidad_academica', 
            'carrera', 'fecha_celebracion', 'periodo_evaluado', 
            'dependencia_responsable', 'tipo_convenio', 'fecha_presentacion'
        ];
        
        $camposCondicionales = [];
        if ($this->convenio_ejecutado) {
            $camposCondicionales = [
                'numero_actividades_realizadas', 'logros_obtenidos', 'beneficios_alcanzados'
            ];
        } else {
            $camposCondicionales = ['motivos_no_ejecucion'];
        }
        
        $totalCampos = count($camposObligatorios) + count($camposCondicionales);
        $camposCompletos = 0;
        
        // Verificar campos obligatorios
        foreach ($camposObligatorios as $campo) {
            if (!empty($this->$campo)) {
                $camposCompletos++;
            }
        }
        
        // Verificar campos condicionales
        foreach ($camposCondicionales as $campo) {
            if (!empty($this->$campo)) {
                $camposCompletos++;
            }
        }
        
        // Verificar coordinadores (array)
        if (!empty($this->coordinadores_designados) && is_array($this->coordinadores_designados)) {
            $coordinadoresValidos = array_filter($this->coordinadores_designados, function($coord) {
                return !empty(trim($coord));
            });
            if (count($coordinadoresValidos) > 0) {
                $camposCompletos++;
            }
        }
        $totalCampos++; // Agregar coordinadores al total
        
        // Verificar enlace Google Drive
        if (!empty($this->enlace_google_drive)) {
            $camposCompletos++;
        }
        $totalCampos++; // Agregar enlace al total
        
        return round(($camposCompletos / $totalCampos) * 100);
    }

    /**
     * Verificar si el informe puede ser enviado automáticamente
     */
    public function puedeSerEnviadoAutomaticamente(): bool
    {
        return $this->estado === 'borrador' && 
            $this->progreso_completitud >= 85 && 
            $this->validarCamposObligatorios();
    }

    /**
     * Obtener estadísticas del informe
     */
    public function getEstadisticasAttribute(): array
    {
        $stats = [
            'dias_desde_creacion' => $this->created_at->diffInDays(now()),
            'dias_desde_presentacion' => $this->fecha_presentacion->diffInDays(now()),
            'progreso_completitud' => $this->progreso_completitud,
            'tiempo_en_revision' => null,
            'es_urgente' => false
        ];
        
        // Calcular tiempo en revisión si está enviado
        if ($this->estado === 'enviado') {
            $stats['tiempo_en_revision'] = $this->updated_at->diffInDays(now());
            $stats['es_urgente'] = $stats['tiempo_en_revision'] > 7; // Más de 7 días
        }
        
        return $stats;
    }

    /**
     * Scope para informes urgentes (en revisión más de 7 días)
     */
    public function scopeUrgentes($query)
    {
        return $query->where('estado', 'enviado')
                    ->where('updated_at', '<', now()->subDays(7));
    }

    /**
     * Scope para informes del mes actual
     */
    public function scopeDelMesActual($query)
    {
        return $query->whereMonth('fecha_presentacion', now()->month)
                    ->whereYear('fecha_presentacion', now()->year);
    }

    /**
     * Scope para informes por año
     */
    public function scopePorAno($query, $ano)
    {
        return $query->whereYear('fecha_presentacion', $ano);
    }

    /**
     * Obtener resumen ejecutivo del informe
     */
    public function getResumenEjecutivoAttribute(): array
    {
        $resumen = [
            'convenio_ejecutado' => $this->convenio_ejecutado,
            'numero_actividades' => $this->numero_actividades_realizadas ?? 0,
            'tiene_dificultades' => !empty($this->dificultades_incidentes) || !empty($this->motivos_no_ejecucion),
            'tiene_evidencias' => !empty($this->enlace_google_drive),
            'coordinadores_count' => $this->coordinadores_designados ? count(array_filter($this->coordinadores_designados)) : 0,
            'firmas_count' => $this->firmas ? count(array_filter($this->firmas)) : 0,
        ];
        
        // Clasificación de calidad
        $puntos = 0;
        if ($resumen['convenio_ejecutado']) $puntos += 3;
        if ($resumen['numero_actividades'] > 0) $puntos += 2;
        if ($resumen['tiene_evidencias']) $puntos += 2;
        if (!$resumen['tiene_dificultades']) $puntos += 1;
        if ($resumen['coordinadores_count'] > 0) $puntos += 1;
        if ($resumen['firmas_count'] > 0) $puntos += 1;
        
        if ($puntos >= 8) {
            $resumen['calidad'] = 'excelente';
        } elseif ($puntos >= 6) {
            $resumen['calidad'] = 'buena';
        } elseif ($puntos >= 4) {
            $resumen['calidad'] = 'regular';
        } else {
            $resumen['calidad'] = 'deficiente';
        }
        
        return $resumen;
    }

    /**
     * Generar notificaciones automáticas
     */
    public function generarNotificaciones(): array
    {
        $notificaciones = [];
        
        // Notificación por informe vencido sin enviar
        if ($this->estado === 'borrador' && $this->fecha_presentacion->addDays(30)->isPast()) {
            $notificaciones[] = [
                'tipo' => 'warning',
                'mensaje' => 'Este informe lleva más de 30 días sin ser enviado',
                'accion' => 'Considere enviarlo para revisión'
            ];
        }
        
        // Notificación por baja completitud
        if ($this->progreso_completitud < 70) {
            $notificaciones[] = [
                'tipo' => 'info',
                'mensaje' => 'El informe está ' . $this->progreso_completitud . '% completo',
                'accion' => 'Complete los campos faltantes'
            ];
        }
        
        // Notificación por falta de evidencias
        if (empty($this->enlace_google_drive)) {
            $notificaciones[] = [
                'tipo' => 'warning',
                'mensaje' => 'No se han agregado evidencias',
                'accion' => 'Agregue enlace a Google Drive con evidencias'
            ];
        }
        
        return $notificaciones;
    }

    /**
     * Validación avanzada de campos según contexto
     */
    public function validarSegunContexto(): array
    {
        $errores = [];
        
        // Validaciones específicas para convenios ejecutados
        if ($this->convenio_ejecutado) {
            if (empty($this->numero_actividades_realizadas) || $this->numero_actividades_realizadas <= 0) {
                $errores[] = 'Debe especificar el número de actividades realizadas';
            }
            
            if (empty($this->logros_obtenidos)) {
                $errores[] = 'Debe describir los logros obtenidos';
            }
            
            if (empty($this->beneficios_alcanzados)) {
                $errores[] = 'Debe describir los beneficios alcanzados';
            }
        } else {
            if (empty($this->motivos_no_ejecucion)) {
                $errores[] = 'Debe explicar los motivos de no ejecución';
            }
        }
        
        // Validaciones de coordinadores
        if (empty($this->coordinadores_designados) || !is_array($this->coordinadores_designados)) {
            $errores[] = 'Debe especificar al menos un coordinador';
        } else {
            $coordinadoresValidos = array_filter($this->coordinadores_designados, function($coord) {
                return !empty(trim($coord));
            });
            if (count($coordinadoresValidos) === 0) {
                $errores[] = 'Debe especificar al menos un coordinador válido';
            }
        }
        
        // Validación de enlace Google Drive
        if (!empty($this->enlace_google_drive)) {
            if (!filter_var($this->enlace_google_drive, FILTER_VALIDATE_URL)) {
                $errores[] = 'El enlace de Google Drive debe ser una URL válida';
            } elseif (!str_contains($this->enlace_google_drive, 'drive.google.com')) {
                $errores[] = 'El enlace debe ser de Google Drive';
            }
        }
        
        // Validación de fechas
        if ($this->periodo_desde && $this->periodo_hasta) {
            if ($this->periodo_desde > $this->periodo_hasta) {
                $errores[] = 'La fecha de inicio del periodo debe ser anterior a la fecha de fin';
            }
        }
        
        if ($this->fecha_celebracion > now()) {
            $errores[] = 'La fecha de celebración no puede ser futura';
        }
        
        return $errores;
    }

    /**
     * Obtener métricas del informe para dashboard
     */
    public static function getMetricasDashboard(): array
    {
        $hoy = now();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();
        
        return [
            'total_mes_actual' => self::whereBetween('fecha_presentacion', [$inicioMes, $finMes])->count(),
            'aprobados_mes' => self::where('estado', 'aprobado')
                                ->whereBetween('fecha_presentacion', [$inicioMes, $finMes])
                                ->count(),
            'pendientes_revision' => self::where('estado', 'enviado')->count(),
            'urgentes' => self::urgentes()->count(),
            'ejecutados_mes' => self::where('convenio_ejecutado', true)
                                    ->whereBetween('fecha_presentacion', [$inicioMes, $finMes])
                                    ->count(),
            'promedio_actividades' => self::where('convenio_ejecutado', true)
                                        ->whereNotNull('numero_actividades_realizadas')
                                        ->avg('numero_actividades_realizadas') ?? 0,
        ];
    }

    /**
     * Obtener informes similares (misma institución o unidad académica)
     */
    public function getInformesSimilares($limite = 5)
    {
        return self::where('id', '!=', $this->id)
                ->where(function($query) {
                    $query->where('institucion_co_celebrante', $this->institucion_co_celebrante)
                            ->orWhere('unidad_academica', $this->unidad_academica);
                })
                ->where('estado', 'aprobado')
                ->orderBy('fecha_presentacion', 'desc')
                ->limit($limite)
                ->get();
    }

    /**
     * Generar recomendaciones automáticas
     */
    public function getRecomendacionesAttribute(): array
    {
        $recomendaciones = [];
        
        // Recomendaciones basadas en informes similares
        $similares = $this->getInformesSimilares(3);
        if ($similares->count() > 0) {
            $promedioActividades = $similares->where('convenio_ejecutado', true)
                                            ->avg('numero_actividades_realizadas');
            
            if ($this->convenio_ejecutado && $this->numero_actividades_realizadas) {
                if ($this->numero_actividades_realizadas < $promedioActividades * 0.7) {
                    $recomendaciones[] = [
                        'tipo' => 'mejora',
                        'mensaje' => 'Informes similares reportan un promedio de ' . round($promedioActividades) . ' actividades',
                        'sugerencia' => 'Considere incrementar las actividades en futuros periodos'
                    ];
                }
            }
        }
        
        // Recomendaciones por completitud
        if ($this->progreso_completitud < 90) {
            $recomendaciones[] = [
                'tipo' => 'completitud',
                'mensaje' => 'El informe puede mejorarse completando más campos',
                'sugerencia' => 'Agregue información en campos opcionales para mayor detalle'
            ];
        }
        
        // Recomendaciones por evidencias
        if (empty($this->enlace_google_drive)) {
            $recomendaciones[] = [
                'tipo' => 'evidencias',
                'mensaje' => 'Las evidencias fortalecen significativamente el informe',
                'sugerencia' => 'Agregue fotos, documentos o videos de las actividades realizadas'
            ];
        }
        
        return $recomendaciones;
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