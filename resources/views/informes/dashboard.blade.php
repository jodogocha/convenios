{{-- resources/views/informes/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard de Informes')
@section('page-title', 'Dashboard de Informes')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('informes.index') }}">Informes</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Métricas principales -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $metricas['total_mes_actual'] }}</h3>
                <p>Informes Este Mes</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('informes.index') }}?fecha_desde={{ now()->startOfMonth()->format('Y-m-d') }}" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $metricas['aprobados_mes'] }}</h3>
                <p>Aprobados Este Mes</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('informes.index') }}?estado=aprobado" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $metricas['pendientes_revision'] }}</h3>
                <p>Pendientes de Revisión</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('informes.pendientes') }}" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $metricas['urgentes'] }}</h3>
                <p>Revisión Urgente</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('informes.pendientes') }}?urgente=1" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico de tendencias mensuales -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Tendencias de Informes {{ now()->year }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="tendenciasChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Métricas adicionales -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Métricas Adicionales
                </h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-tasks"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Convenios Ejecutados</span>
                        <span class="info-box-number">{{ $metricas['ejecutados_mes'] }}</span>
                        <div class="progress">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $metricas['total_mes_actual'] > 0 ? ($metricas['ejecutados_mes'] / $metricas['total_mes_actual']) * 100 : 0 }}%"></div>
                        </div>
                        <span class="progress-description">
                            Este mes
                        </span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-calculator"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Promedio Actividades</span>
                        <span class="info-box-number">{{ number_format($metricas['promedio_actividades'], 1) }}</span>
                        <span class="progress-description">
                            Por convenio ejecutado
                        </span>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('informes.estadisticas-avanzadas') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Ver Estadísticas Completas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Mis informes recientes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>
                    Mis Informes Recientes
                </h3>
                <div class="card-tools">
                    <a href="{{ route('informes.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Informe
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($informesRecientes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Institución</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($informesRecientes as $informe)
                            <tr>
                                <td><span class="badge badge-secondary">#{{ $informe->id }}</span></td>
                                <td>{{ Str::limit($informe->institucion_co_celebrante, 25) }}</td>
                                <td>
                                    <span class="badge badge-{{ $informe->estado_badge }}">
                                        {{ $informe->estado_texto }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $informe->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('informes.show', $informe) }}" 
                                       class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($informe->puedeSerEditado())
                                    <a href="{{ route('informes.edit', $informe) }}" 
                                       class="btn btn-warning btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3">
                    <p class="text-muted">No tienes informes recientes</p>
                    <a href="{{ route('informes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Crear Primer Informe
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Informes urgentes (solo para revisores) -->
    @if(Auth::user()->tienePermiso('informes.aprobar'))
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Informes Urgentes
                </h3>
                <div class="card-tools">
                    <span class="badge badge-danger">{{ $informesUrgentes->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($informesUrgentes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Institución</th>
                                <th>Días en Revisión</th>
                                <th>Creador</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($informesUrgentes as $informe)
                            <tr class="table-warning">
                                <td><span class="badge badge-warning">#{{ $informe->id }}</span></td>
                                <td>{{ Str::limit($informe->institucion_co_celebrante, 20) }}</td>
                                <td>
                                    <span class="badge badge-danger">
                                        {{ $informe->updated_at->diffInDays(now()) }} días
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $informe->usuarioCreador->nombre_completo ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('informes.show', $informe) }}" 
                                       class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-success btn-xs btn-aprobar-rapido" 
                                            data-id="{{ $informe->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="text-muted">No hay informes urgentes</p>
                </div>
                @endif
            </div>
            @if($informesUrgentes->count() > 0)
            <div class="card-footer text-center">
                <a href="{{ route('informes.pendientes') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-list mr-1"></i>
                    Ver Todos los Pendientes
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Estadísticas por Unidad Académica -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-university mr-2"></i>
                    Informes por Unidad Académica ({{ now()->year }})
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($estadisticasPorUnidad->count() > 0)
                <div class="row">
                    @foreach($estadisticasPorUnidad as $stats)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card card-outline card-primary">
                            <div class="card-body">
                                <h6 class="card-title">{{ $stats->unidad_academica }}</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="description-block">
                                            <h5 class="description-header text-primary">{{ $stats->total }}</h5>
                                            <span class="description-text">Total Informes</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-block">
                                            <h5 class="description-header text-success">{{ $stats->ejecutados }}</h5>
                                            <span class="description-text">Ejecutados</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $stats->total > 0 ? ($stats->ejecutados / $stats->total) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $stats->total > 0 ? round(($stats->ejecutados / $stats->total) * 100) : 0 }}% de ejecución
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-3">
                    <p class="text-muted">No hay datos disponibles para este año</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>
                    Acciones Rápidas
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('informes.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus mr-2"></i>
                            Nuevo Informe
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('informes.exportar-excel') }}" class="btn btn-info btn-block">
                            <i class="fas fa-file-excel mr-2"></i>
                            Exportar Excel
                        </a>
                    </div>
                    @if(Auth::user()->tienePermiso('informes.aprobar'))
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('informes.pendientes') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-clock mr-2"></i>
                            Revisar Pendientes
                        </a>
                    </div>
                    @endif
                    <div class="col-md-3 mb-2">
                        <button type="button" class="btn btn-secondary btn-block" data-toggle="modal" data-target="#modalEstadisticas">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Estadísticas Personalizadas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para estadísticas personalizadas -->
<div class="modal fade" id="modalEstadisticas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Generar Estadísticas Personalizadas
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEstadisticas">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" 
                                       value="{{ now()->subMonths(6)->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_fin">Fecha de Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" 
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unidad_filtro">Unidad Académica</label>
                                <select class="form-control" id="unidad_filtro">
                                    <option value="">Todas las unidades</option>
                                    @foreach($estadisticasPorUnidad as $stats)
                                        <option value="{{ $stats->unidad_academica }}">{{ $stats->unidad_academica }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_reporte">Tipo de Reporte</label>
                                <select class="form-control" id="tipo_reporte">
                                    <option value="general">Resumen General</option>
                                    <option value="ejecutados">Solo Convenios Ejecutados</option>
                                    <option value="no_ejecutados">Solo Convenios No Ejecutados</option>
                                    <option value="por_estado">Agrupado por Estado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarEstadisticas()">
                    <i class="fas fa-chart-line mr-1"></i>
                    Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de resultados de estadísticas -->
<div class="modal fade" id="modalResultados" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Resultados de Estadísticas
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="resultadosEstadisticas">
                    <!-- Los resultados se cargarán aquí via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="exportarResultados()">
                    <i class="fas fa-download mr-1"></i>
                    Exportar Resultados
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Crear gráfico de tendencias
    crearGraficoTendencias();
    
    // Manejar aprobación rápida
    $('.btn-aprobar-rapido').click(function() {
        var informeId = $(this).data('id');
        aprobarRapido(informeId);
    });
});

function crearGraficoTendencias() {
    var ctx = document.getElementById('tendenciasChart').getContext('2d');
    
    // Datos de tendencias mensuales
    var tendencias = @json($tendenciasMensuales);
    var meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    var datos = [];
    for (var i = 1; i <= 12; i++) {
        datos.push(tendencias[i] || 0);
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{
                label: 'Informes Presentados',
                data: datos,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function aprobarRapido(informeId) {
    Swal.fire({
        title: '¿Aprobar informe?',
        text: "¿Está seguro de que desea aprobar este informe?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/informes/' + informeId + '/aprobar',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Informe aprobado',
                        text: 'El informe ha sido aprobado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo aprobar el informe'
                    });
                }
            });
        }
    });
}

function generarEstadisticas() {
    var fechaInicio = $('#fecha_inicio').val();
    var fechaFin = $('#fecha_fin').val();
    var unidad = $('#unidad_filtro').val();
    var tipo = $('#tipo_reporte').val();
    
    if (!fechaInicio || !fechaFin) {
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Por favor seleccione las fechas de inicio y fin'
        });
        return;
    }
    
    // Mostrar loading
    $('#resultadosEstadisticas').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Generando estadísticas...</p></div>');
    $('#modalEstadisticas').modal('hide');
    $('#modalResultados').modal('show');
    
    $.ajax({
        url: '/api/informes/estadisticas-avanzadas',
        type: 'GET',
        data: {
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            unidad_academica: unidad,
            tipo_reporte: tipo
        },
        success: function(response) {
            mostrarResultadosEstadisticas(response);
        },
        error: function(xhr) {
            $('#resultadosEstadisticas').html('<div class="alert alert-danger">Error al generar estadísticas</div>');
        }
    });
}

function mostrarResultadosEstadisticas(data) {
    var html = '';
    
    // Resumen general
    html += '<div class="row mb-4">';
    html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span><div class="info-box-content"><span class="info-box-text">Total Informes</span><span class="info-box-number">' + data.resumen_general.total_informes + '</span></div></div></div>';
    html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-check"></i></span><div class="info-box-content"><span class="info-box-text">Ejecutados</span><span class="info-box-number">' + data.resumen_general.ejecutados + '</span></div></div></div>';
    html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Pendientes</span><span class="info-box-number">' + data.resumen_general.pendientes + '</span></div></div></div>';
    html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span><div class="info-box-content"><span class="info-box-text">Aprobados</span><span class="info-box-number">' + data.resumen_general.aprobados + '</span></div></div></div>';
    html += '</div>';
    
    // Tabla por unidad académica
    if (data.por_unidad_academica && data.por_unidad_academica.length > 0) {
        html += '<h5>Por Unidad Académica</h5>';
        html += '<div class="table-responsive">';
        html += '<table class="table table-striped">';
        html += '<thead><tr><th>Unidad Académica</th><th>Total</th><th>Ejecutados</th><th>Aprobados</th><th>% Ejecución</th></tr></thead>';
        html += '<tbody>';
        
        data.por_unidad_academica.forEach(function(item) {
            var porcentaje = item.total > 0 ? ((item.ejecutados / item.total) * 100).toFixed(1) : 0;
            html += '<tr>';
            html += '<td>' + item.unidad_academica + '</td>';
            html += '<td>' + item.total + '</td>';
            html += '<td>' + item.ejecutados + '</td>';
            html += '<td>' + item.aprobados + '</td>';
            html += '<td><span class="badge badge-' + (porcentaje > 70 ? 'success' : porcentaje > 50 ? 'warning' : 'danger') + '">' + porcentaje + '%</span></td>';
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        html += '</div>';
    }
    
    $('#resultadosEstadisticas').html(html);
}

function exportarResultados() {
    // Implementar exportación de resultados
    Swal.fire({
        icon: 'info',
        title: 'Función en desarrollo',
        text: 'La exportación de resultados estará disponible próximamente'
    });
}
</script>
@endpush

@push('styles')
<style>
.description-block {
    text-align: center;
}

.description-header {
    margin: 0;
    padding: 0;
    font-weight: 600;
}

.description-text {
    font-size: 12px;
    text-transform: uppercase;
    color: #6c757d;
}

.small-box {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.small-box:hover {
    transform: translateY(-2px);
}

.info-box {
    margin-bottom: 15px;
}

.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
    border-radius: 0.2rem;
}

.progress {
    height: 8px;
}

.card-outline {
    border-top: 3px solid;
}
</style>
@endp