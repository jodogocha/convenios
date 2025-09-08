{{-- resources/views/informes/pdf-multiple.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Múltiple de Informes de Evaluación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #8B0000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #8B0000;
        }
        
        .header h2 {
            font-size: 12px;
            margin: 5px 0;
            color: #666;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        .summary-table th {
            background-color: #8B0000;
            color: white;
            font-weight: bold;
        }
        
        .summary-table .text-center {
            text-align: center;
        }
        
        .informe-section {
            page-break-before: always;
            margin-bottom: 30px;
        }
        
        .informe-section:first-child {
            page-break-before: auto;
        }
        
        .informe-header {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .informe-title {
            font-size: 14px;
            font-weight: bold;
            color: #8B0000;
            margin: 0 0 5px 0;
        }
        
        .informe-subtitle {
            font-size: 11px;
            color: #666;
            margin: 0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 3px 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
        }
        
        .execution-status {
            margin: 10px 0;
            padding: 8px;
            border-radius: 4px;
        }
        
        .executed {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .not-executed {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .content-section {
            margin: 10px 0;
        }
        
        .content-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .content-text {
            border: 1px solid #dee2e6;
            padding: 8px;
            background-color: #f8f9fa;
            margin-bottom: 8px;
            min-height: 20px;
        }
        
        .coordinadores-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .coordinadores-list li {
            padding: 2px 0;
            border-bottom: 1px dotted #ccc;
            font-size: 10px;
        }
        
        .badge {
            background-color: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
        
        .metrics-box {
            border: 1px solid #dee2e6;
            padding: 10px;
            margin: 10px 0;
            background-color: #f8f9fa;
        }
        
        .metrics-title {
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 8px;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
        }
        
        .metrics-row {
            display: table-row;
        }
        
        .metrics-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 5px;
            border-right: 1px solid #dee2e6;
        }
        
        .metrics-cell:last-child {
            border-right: none;
        }
        
        .metrics-number {
            font-size: 18px;
            font-weight: bold;
            color: #8B0000;
        }
        
        .metrics-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        
        .footer {
            margin-top: 20px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            font-size: 9px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Encabezado Principal --}}
    <div class="header">
        <h1>REPORTE MÚLTIPLE DE EVALUACIÓN DE CONVENIOS</h1>
        <h2>UNIVERSIDAD NACIONAL DE ITAPÚA</h2>
        <p style="margin: 10px 0; font-size: 11px;">
            <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }} | 
            <strong>Total de Informes:</strong> {{ $informes->count() }}
        </p>
    </div>

    {{-- Resumen Ejecutivo --}}
    <div class="metrics-box">
        <div class="metrics-title">RESUMEN EJECUTIVO</div>
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metrics-cell">
                    <div class="metrics-number">{{ $informes->count() }}</div>
                    <div class="metrics-label">Total Informes</div>
                </div>
                <div class="metrics-cell">
                    <div class="metrics-number">{{ $informes->where('convenio_ejecutado', true)->count() }}</div>
                    <div class="metrics-label">Ejecutados</div>
                </div>
                <div class="metrics-cell">
                    <div class="metrics-number">{{ $informes->where('estado', 'aprobado')->count() }}</div>
                    <div class="metrics-label">Aprobados</div>
                </div>
                <div class="metrics-cell">
                    <div class="metrics-number">{{ $informes->where('convenio_ejecutado', true)->sum('numero_actividades_realizadas') }}</div>
                    <div class="metrics-label">Total Actividades</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla Resumen de Todos los Informes --}}
    <table class="summary-table">
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 25%;">Institución</th>
                <th style="width: 20%;">Unidad Académica</th>
                <th style="width: 12%;">Estado</th>
                <th style="width: 10%;">Ejecutado</th>
                <th style="width: 10%;">Actividades</th>
                <th style="width: 15%;">Fecha Presentación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($informes as $informe)
            <tr>
                <td class="text-center">#{{ $informe->id }}</td>
                <td>{{ Str::limit($informe->institucion_co_celebrante, 35) }}</td>
                <td>{{ $informe->unidad_academica }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $informe->estado_badge }}">
                        {{ $informe->estado_texto }}
                    </span>
                </td>
                <td class="text-center">
                    @if($informe->convenio_ejecutado)
                        <span class="badge badge-success">SÍ</span>
                    @else
                        <span class="badge badge-warning">NO</span>
                    @endif
                </td>
                <td class="text-center">{{ $informe->numero_actividades_realizadas ?? 'N/A' }}</td>
                <td class="text-center">{{ $informe->fecha_presentacion->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Detalles Individuales de Cada Informe --}}
    @foreach($informes as $index => $informe)
    <div class="informe-section">
        {{-- Encabezado del Informe --}}
        <div class="informe-header">
            <div class="informe-title">
                INFORME #{{ $informe->id }} - {{ $informe->institucion_co_celebrante }}
            </div>
            <div class="informe-subtitle">
                {{ $informe->unidad_academica }} | {{ $informe->carrera }}
            </div>
        </div>

        {{-- Estado de Ejecución --}}
        @if($informe->convenio_ejecutado)
        <div class="execution-status executed">
            <strong>✓ CONVENIO EJECUTADO</strong> - 
            {{ $informe->numero_actividades_realizadas ?? 0 }} actividades realizadas
        </div>
        @else
        <div class="execution-status not-executed">
            <strong>⚠ CONVENIO NO EJECUTADO</strong>
        </div>
        @endif

        {{-- Información Básica --}}
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Convenio:</span>
                    <span class="info-value">
                        @if($informe->convenio)
                            {{ $informe->convenio->numero_convenio }}
                        @else
                            Convenio eliminado
                        @endif
                    </span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">{{ $informe->tipo_convenio }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Fecha Celebración:</span>
                    <span class="info-value">{{ $informe->fecha_celebracion->format('d/m/Y') }}</span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Fecha Presentación:</span>
                    <span class="info-value">{{ $informe->fecha_presentacion->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Dep. Responsable:</span>
                    <span class="info-value">{{ $informe->dependencia_responsable }}</span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        <span class="badge badge-{{ $informe->estado_badge }}">
                            {{ $informe->estado_texto }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Periodo Evaluado --}}
        <div class="content-section">
            <div class="content-title">PERIODO EVALUADO</div>
            <div class="content-text">{{ $informe->periodo_evaluado }}</div>
            @if($informe->periodo_desde && $informe->periodo_hasta)
            <div style="font-size: 10px; color: #666; margin-top: 5px;">
                <strong>Desde:</strong> {{ $informe->periodo_desde->format('d/m/Y') }} | 
                <strong>Hasta:</strong> {{ $informe->periodo_hasta->format('d/m/Y') }}
            </div>
            @endif
        </div>

        {{-- Coordinadores --}}
        @if($informe->coordinadores_designados && count($informe->coordinadores_designados) > 0)
        <div class="content-section">
            <div class="content-title">COORDINADORES DESIGNADOS</div>
            <ul class="coordinadores-list">
                @foreach($informe->coordinadores_designados as $coordinador)
                    @if(!empty($coordinador))
                    <li>{{ $coordinador }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Contenido según el estado de ejecución --}}
        @if($informe->convenio_ejecutado)
            {{-- Convenio Ejecutado --}}
            @if($informe->logros_obtenidos)
            <div class="content-section">
                <div class="content-title">LOGROS OBTENIDOS</div>
                <div class="content-text">{{ $informe->logros_obtenidos }}</div>
            </div>
            @endif

            @if($informe->beneficios_alcanzados)
            <div class="content-section">
                <div class="content-title">BENEFICIOS ALCANZADOS</div>
                <div class="content-text">{{ $informe->beneficios_alcanzados }}</div>
            </div>
            @endif

            @if($informe->dificultades_incidentes)
            <div class="content-section">
                <div class="content-title">DIFICULTADES E INCIDENTES</div>
                <div class="content-text">{{ $informe->dificultades_incidentes }}</div>
            </div>
            @endif

            @if($informe->sugerencias_mejoras)
            <div class="content-section">
                <div class="content-title">SUGERENCIAS DE MEJORAS</div>
                <div class="content-text">{{ $informe->sugerencias_mejoras }}</div>
            </div>
            @endif

            @if($informe->informacion_complementaria)
            <div class="content-section">
                <div class="content-title">INFORMACIÓN COMPLEMENTARIA</div>
                <div class="content-text">{{ $informe->informacion_complementaria }}</div>
            </div>
            @endif
        @else
            {{-- Convenio No Ejecutado --}}
            @if($informe->motivos_no_ejecucion)
            <div class="content-section">
                <div class="content-title">MOTIVOS DE NO EJECUCIÓN</div>
                <div class="content-text">{{ $informe->motivos_no_ejecucion }}</div>
            </div>
            @endif

            @if($informe->propuestas_mejoras)
            <div class="content-section">
                <div class="content-title">PROPUESTAS DE MEJORAS</div>
                <div class="content-text">{{ $informe->propuestas_mejoras }}</div>
            </div>
            @endif

            @if($informe->informacion_complementaria_no_ejecutado)
            <div class="content-section">
                <div class="content-title">INFORMACIÓN COMPLEMENTARIA</div>
                <div class="content-text">{{ $informe->informacion_complementaria_no_ejecutado }}</div>
            </div>
            @endif
        @endif

        {{-- Evidencias --}}
        @if($informe->enlace_google_drive)
        <div class="content-section">
            <div class="content-title">EVIDENCIAS</div>
            <div class="content-text">
                <strong>Google Drive:</strong> {{ $informe->enlace_google_drive }}
            </div>
        </div>
        @endif

        {{-- Firmas y Observaciones --}}
        <div class="info-grid" style="margin-top: 15px;">
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Creado por:</span>
                    <span class="info-value">{{ $informe->usuarioCreador ? $informe->usuarioCreador->nombre_completo : 'Usuario eliminado' }}</span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Fecha Creación:</span>
                    <span class="info-value">{{ $informe->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
            @if($informe->usuarioRevisor)
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Revisado por:</span>
                    <span class="info-value">{{ $informe->usuarioRevisor->nombre_completo }}</span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Fecha Revisión:</span>
                    <span class="info-value">{{ $informe->fecha_revision->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
            @endif
        </div>

        @if($informe->firmas && count($informe->firmas) > 0)
        <div class="content-section">
            <div class="content-title">FIRMAS</div>
            <ul class="coordinadores-list">
                @foreach($informe->firmas as $firma)
                    @if(!empty($firma))
                    <li>{{ $firma }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        @if($informe->observaciones)
        <div class="content-section">
            <div class="content-title">OBSERVACIONES</div>
            <div class="content-text">{{ $informe->observaciones }}</div>
        </div>
        @endif
    </div>
    @endforeach

    {{-- Análisis Comparativo --}}
    <div class="page-break"></div>
    
    <div class="header">
        <h1>ANÁLISIS COMPARATIVO</h1>
    </div>

    {{-- Estadísticas por Unidad Académica --}}
    @php
        $unidadesStats = $informes->groupBy('unidad_academica')->map(function($grupo) {
            return [
                'total' => $grupo->count(),
                'ejecutados' => $grupo->where('convenio_ejecutado', true)->count(),
                'actividades' => $grupo->where('convenio_ejecutado', true)->sum('numero_actividades_realizadas'),
                'aprobados' => $grupo->where('estado', 'aprobado')->count()
            ];
        });
    @endphp

    <div class="content-section">
        <div class="content-title">RESUMEN POR UNIDAD ACADÉMICA</div>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Unidad Académica</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Ejecutados</th>
                    <th class="text-center">% Ejecución</th>
                    <th class="text-center">Total Actividades</th>
                    <th class="text-center">Aprobados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unidadesStats as $unidad => $stats)
                <tr>
                    <td>{{ $unidad }}</td>
                    <td class="text-center">{{ $stats['total'] }}</td>
                    <td class="text-center">{{ $stats['ejecutados'] }}</td>
                    <td class="text-center">
                        {{ $stats['total'] > 0 ? round(($stats['ejecutados'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td class="text-center">{{ $stats['actividades'] }}</td>
                    <td class="text-center">{{ $stats['aprobados'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Estadísticas Generales --}}
    <div class="metrics-box">
        <div class="metrics-title">INDICADORES CLAVE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Porcentaje de Ejecución:</span>
                    <span class="info-value">
                        <strong>{{ $informes->count() > 0 ? round(($informes->where('convenio_ejecutado', true)->count() / $informes->count()) * 100, 1) : 0 }}%</strong>
                    </span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Porcentaje de Aprobación:</span>
                    <span class="info-value">
                        <strong>{{ $informes->count() > 0 ? round(($informes->where('estado', 'aprobado')->count() / $informes->count()) * 100, 1) : 0 }}%</strong>
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Promedio de Actividades:</span>
                    <span class="info-value">
                        <strong>{{ $informes->where('convenio_ejecutado', true)->count() > 0 ? round($informes->where('convenio_ejecutado', true)->avg('numero_actividades_realizadas'), 1) : 0 }}</strong>
                    </span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Unidades Involucradas:</span>
                    <span class="info-value">
                        <strong>{{ $informes->pluck('unidad_academica')->unique()->count() }}</strong>
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Rango de Fechas:</span>
                    <span class="info-value">
                        <strong>{{ $informes->min('fecha_presentacion')->format('d/m/Y') }} - {{ $informes->max('fecha_presentacion')->format('d/m/Y') }}</strong>
                    </span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Convenios con Evidencias:</span>
                    <span class="info-value">
                        <strong>{{ $informes->whereNotNull('enlace_google_drive')->count() }} ({{ $informes->count() > 0 ? round(($informes->whereNotNull('enlace_google_drive')->count() / $informes->count()) * 100, 1) : 0 }}%)</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Top 5 Convenios por Actividades --}}
    @php
        $topActividades = $informes->where('convenio_ejecutado', true)
                                 ->where('numero_actividades_realizadas', '>', 0)
                                 ->sortByDesc('numero_actividades_realizadas')
                                 ->take(5);
    @endphp

    @if($topActividades->count() > 0)
    <div class="content-section">
        <div class="content-title">TOP 5 CONVENIOS POR NÚMERO DE ACTIVIDADES</div>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Institución</th>
                    <th>Unidad Académica</th>
                    <th class="text-center">Actividades</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topActividades as $index => $informe)
                <tr>
                    <td class="text-center">#{{ $index + 1 }}</td>
                    <td>{{ Str::limit($informe->institucion_co_celebrante, 40) }}</td>
                    <td>{{ $informe->unidad_academica }}</td>
                    <td class="text-center"><strong>{{ $informe->numero_actividades_realizadas }}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-{{ $informe->estado_badge }}">
                            {{ $informe->estado_texto }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Conclusiones --}}
    <div class="content-section">
        <div class="content-title">CONCLUSIONES Y RECOMENDACIONES</div>
        <div class="content-text">
            @php
                $totalInformes = $informes->count();
                $ejecutados = $informes->where('convenio_ejecutado', true)->count();
                $porcentajeEjecucion = $totalInformes > 0 ? round(($ejecutados / $totalInformes) * 100, 1) : 0;
                $totalActividades = $informes->where('convenio_ejecutado', true)->sum('numero_actividades_realizadas');
                $promedioActividades = $ejecutados > 0 ? round($totalActividades / $ejecutados, 1) : 0;
            @endphp
            
            <strong>RESUMEN EJECUTIVO:</strong><br>
            • Se analizaron {{ $totalInformes }} informes de evaluación de convenios.<br>
            • El {{ $porcentajeEjecucion }}% de los convenios fueron ejecutados exitosamente.<br>
            • Se realizaron un total de {{ $totalActividades }} actividades, con un promedio de {{ $promedioActividades }} actividades por convenio ejecutado.<br>
            • {{ $informes->where('estado', 'aprobado')->count() }} informes han sido aprobados oficialmente.<br><br>
            
            <strong>OBSERVACIONES:</strong><br>
            @if($porcentajeEjecucion >= 80)
            • Excelente nivel de ejecución de convenios ({{ $porcentajeEjecucion }}%).<br>
            @elseif($porcentajeEjecucion >= 60)
            • Buen nivel de ejecución de convenios ({{ $porcentajeEjecucion }}%), con oportunidades de mejora.<br>
            @else
            • Se recomienda revisar las causas de la baja ejecución de convenios ({{ $porcentajeEjecucion }}%).<br>
            @endif
            
            @if($informes->whereNull('enlace_google_drive')->count() > 0)
            • {{ $informes->whereNull('enlace_google_drive')->count() }} informes no incluyen evidencias documentales.<br>
            @endif
            
            • Las unidades académicas más activas son: {{ $unidadesStats->sortByDesc('total')->take(3)->keys()->implode(', ') }}.<br>
        </div>
    </div>

    {{-- Pie de página --}}
    <div class="footer">
        <div style="text-align: center;">
            <strong>Universidad Nacional de Itapúa - Sistema de Gestión de Convenios</strong><br>
            Reporte generado automáticamente el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i:s') }}<br>
            Este documento contiene información confidencial de la institución.
        </div>
    </div>

</body>
</html>