{{-- resources/views/informes/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Evaluación #{{ $informe->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #8B0000;
        }
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .section-title {
            background-color: #8B0000;
            color: white;
            padding: 8px 12px;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
            font-size: 13px;
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-label {
            font-weight: bold;
            display: inline-block;
            width: 180px;
            vertical-align: top;
        }
        .form-value {
            display: inline-block;
            width: calc(100% - 190px);
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        .form-row {
            display: flex;
            margin-bottom: 12px;
        }
        .form-col {
            flex: 1;
            margin-right: 20px;
        }
        .form-col:last-child {
            margin-right: 0;
        }
        .checkbox-group {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .checkbox-label {
            font-weight: bold;
            font-size: 13px;
        }
        .textarea-content {
            border: 1px solid #ddd;
            padding: 8px;
            min-height: 60px;
            background-color: #f9f9f9;
            margin-top: 5px;
        }
        .signature-section {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 15px;
        }
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin: 20px 2.5%;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        .coordinadores-list, .firmas-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .coordinadores-list li, .firmas-list li {
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }
        .badge {
            background-color: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .page-break {
            page-break-before: always;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .number-box {
            border: 1px solid #333;
            width: 80px;
            height: 25px;
            display: inline-block;
            text-align: center;
            line-height: 23px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- Encabezado --}}
    <div class="header">
        <h1>EVALUACIÓN DE CONVENIOS CELEBRADOS POR LA<br>UNIVERSIDAD NACIONAL DE ITAPÚA</h1>
        <h2>Informe de Evaluación #{{ $informe->id }}</h2>
    </div>

    {{-- Información Básica --}}
    <div class="section">
        <div class="section-title">INFORMACIÓN BÁSICA DEL CONVENIO</div>
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Institución Co-celebrante:</span>
                    <span class="form-value">{{ $informe->institucion_co_celebrante }}</span>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Unidad Académica:</span>
                    <span class="form-value">{{ $informe->unidad_academica }}</span>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Carrera:</span>
                    <span class="form-value">{{ $informe->carrera }}</span>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Fecha de Celebración:</span>
                    <span class="form-value">{{ $informe->fecha_celebracion->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Vigencia:</span>
                    <span class="form-value">{{ $informe->vigencia ?? 'No especificada' }}</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <span class="form-label">Periodo Evaluado:</span>
            <div class="textarea-content">{{ $informe->periodo_evaluado }}</div>
        </div>

        @if($informe->periodo_desde && $informe->periodo_hasta)
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Fecha Desde:</span>
                    <span class="form-value">{{ $informe->periodo_desde->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Fecha Hasta:</span>
                    <span class="form-value">{{ $informe->periodo_hasta->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="form-group">
            <span class="form-label">Dependencia Responsable:</span>
            <span class="form-value">{{ $informe->dependencia_responsable }}</span>
        </div>

        <div class="form-group">
            <span class="form-label">Coordinador/es Designados:</span>
            @if($informe->coordinadores_designados && count($informe->coordinadores_designados) > 0)
                <ul class="coordinadores-list">
                    @foreach($informe->coordinadores_designados as $coordinador)
                        @if(!empty($coordinador))
                        <li>{{ $coordinador }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                <span class="form-value">No especificados</span>
            @endif
        </div>

        <div class="form-group">
            <span class="form-label">Convenio Celebrado a Propuesta de:</span>
            <span class="form-value">{{ $informe->convenio_celebrado_propuesta }}</span>
        </div>

        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Tipo de Convenio:</span>
                    <span class="form-value"><span class="badge">{{ $informe->tipo_convenio }}</span></span>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <span class="form-label">Fecha de Presentación:</span>
                    <span class="form-value">{{ $informe->fecha_presentacion->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Estado de Ejecución --}}
    <div class="section">
        <div class="section-title">ESTADO DE EJECUCIÓN DEL CONVENIO</div>
        
        @if($informe->convenio_ejecutado)
            <div class="alert alert-success">
                <strong>✓ EL CONVENIO SE HA EJECUTADO</strong>
            </div>

            <div class="form-group">
                <span class="form-label">N° de Actividades Realizadas:</span>
                <span class="number-box">{{ $informe->numero_actividades_realizadas ?? 0 }}</span>
            </div>

            <div class="form-group">
                <span class="form-label">Logros Obtenidos:</span>
                <div class="textarea-content">{{ $informe->logros_obtenidos ?? 'No especificados' }}</div>
            </div>

            <div class="form-group">
                <span class="form-label">Beneficios Alcanzados:</span>
                <div class="textarea-content">{{ $informe->beneficios_alcanzados ?? 'No especificados' }}</div>
            </div>

            @if($informe->dificultades_incidentes)
            <div class="form-group">
                <span class="form-label">Dificultades/Incidentes:</span>
                <div class="textarea-content">{{ $informe->dificultades_incidentes }}</div>
            </div>
            @endif

            @if($informe->responsabilidad_instalaciones)
            <div class="form-group">
                <span class="form-label">Responsabilidad con Instalaciones:</span>
                <div class="textarea-content">{{ $informe->responsabilidad_instalaciones }}</div>
            </div>
            @endif

            @if($informe->sugerencias_mejoras)
            <div class="form-group">
                <span class="form-label">Sugerencias de Mejoras:</span>
                <div class="textarea-content">{{ $informe->sugerencias_mejoras }}</div>
            </div>
            @endif

            @if($informe->informacion_complementaria)
            <div class="form-group">
                <span class="form-label">Información Complementaria:</span>
                <div class="textarea-content">{{ $informe->informacion_complementaria }}</div>
            </div>
            @endif

        @else
            <div class="alert alert-warning">
                <strong>⚠ EL CONVENIO NO SE HA EJECUTADO</strong>
            </div>

            <div class="form-group">
                <span class="form-label">Motivos de No Ejecución:</span>
                <div class="textarea-content">{{ $informe->motivos_no_ejecucion ?? 'No especificados' }}</div>
            </div>

            @if($informe->propuestas_mejoras)
            <div class="form-group">
                <span class="form-label">Propuestas de Mejoras:</span>
                <div class="textarea-content">{{ $informe->propuestas_mejoras }}</div>
            </div>
            @endif

            @if($informe->informacion_complementaria_no_ejecutado)
            <div class="form-group">
                <span class="form-label">Información Complementaria:</span>
                <div class="textarea-content">{{ $informe->informacion_complementaria_no_ejecutado }}</div>
            </div>
            @endif
        @endif
    </div>

    {{-- Evidencias --}}
    <div class="section">
        <div class="section-title">ANEXOS Y EVIDENCIAS</div>
        
        @if($informe->enlace_google_drive)
        <div class="form-group">
            <span class="form-label">Enlace Google Drive:</span>
            <span class="form-value">{{ $informe->enlace_google_drive }}</span>
        </div>
        @else
        <div class="form-group">
            <span class="form-label">Evidencias:</span>
            <span class="form-value">No se proporcionaron evidencias</span>
        </div>
        @endif
    </div>

    {{-- Firmas y Fecha --}}
    <div class="section">
        <div class="section-title">FIRMAS Y OBSERVACIONES</div>
        
        <div class="form-group">
            <span class="form-label">Firmas:</span>
            @if($informe->firmas && count($informe->firmas) > 0)
                <ul class="firmas-list">
                    @foreach($informe->firmas as $firma)
                        @if(!empty($firma))
                        <li>{{ $firma }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                <span class="form-value">Sin firmas registradas</span>
            @endif
        </div>

        <div class="form-group">
            <span class="form-label">Fecha de Presentación:</span>
            <span class="form-value">{{ $informe->fecha_presentacion->format('d/m/Y') }}</span>
        </div>

        @if($informe->observaciones)
        <div class="form-group">
            <span class="form-label">Observaciones:</span>
            <div class="textarea-content">{{ $informe->observaciones }}</div>
        </div>
        @endif
    </div>

    {{-- Información del Sistema --}}
    <div class="signature-section">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; text-align: left;">
                    <strong>Estado del Informe:</strong> {{ $informe->estado_texto }}<br>
                    <strong>Creado por:</strong> {{ $informe->usuarioCreador ? $informe->usuarioCreador->nombre_completo : 'Usuario eliminado' }}<br>
                    <strong>Fecha de Creación:</strong> {{ $informe->created_at->format('d/m/Y H:i:s') }}
                </td>
                <td style="width: 50%; border: none; text-align: right;">
                    @if($informe->usuarioRevisor)
                        <strong>Revisado por:</strong> {{ $informe->usuarioRevisor->nombre_completo }}<br>
                        <strong>Fecha de Revisión:</strong> {{ $informe->fecha_revision->format('d/m/Y H:i:s') }}
                    @endif
                    <br>
                    <small style="color: #666;">Generado el {{ now()->format('d/m/Y H:i:s') }}</small>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>