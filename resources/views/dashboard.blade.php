{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
<li class="breadcrumb-item active"></li>
@endsection

@section('content')
<div class="row">
    <!-- Tarjeta de bienvenida -->
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title mb-2">
                            <i class="fas fa-user-circle mr-2 text-primary"></i>
                            ¡Bienvenido, {{ $usuario->nombre_completo }}!
                        </h4>
                        <p class="card-text text-muted mb-0">                          
                        </p>
                        @if($ultimoLogin)
                        <p class="card-text text-muted mb-0">
                            <i class="fas fa-clock mr-2"></i>
                            Último acceso: <strong>{{ $ultimoLogin->format('d/m/Y H:i:s') }}</strong>
                        </p>
                        @endif
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group" role="group">
                            <p class="card-text text-muted mb-0">
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-muted" style="text-decoration: none;">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas principales -->
<div class="row">
    @if(Auth::user()->tieneRol('usuario') || Auth::user()->tieneRol('admin') || Auth::user()->tieneRol('super_admin'))
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="totalConvenios">-</h3>
                <p>Total Convenios</p>
            </div>
            <div class="icon">
                <i class="fas fa-handshake"></i>
            </div>
            <a href="{{ route('convenios.index') }}" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3 id="conveniosActivos">-</h3>
                <p>Convenios Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('convenios.index', ['estado' => 'activo']) }}" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 id="conveniosPendientes">-</h3>
                <p>Pendientes Aprobación</p>
            </div>
            <div class="icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <a href="{{ route('convenios.pendientes') }}" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 id="conveniosPorVencer">-</h3>
                <p>Por Vencer (30 días)</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('convenios.index', ['vencimiento' => '30']) }}" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endif

    @if(Auth::user()->tieneRol('admin') || Auth::user()->tieneRol('super_admin'))
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3 id="totalUsuarios">-</h3>
                <p>Total Usuarios</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('usuarios.index') }}" class="small-box-footer">
                Ver más <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endif
</div>

<div class="row">
    @if(Auth::user()->tienePermiso('convenios.leer'))
    <!-- Gráfico de convenios por estado -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Convenios por Estado
                </h3>
            </div>
            <div class="card-body">
                <canvas id="conveniosEstadoChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de convenios por mes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Convenios Creados (Últimos 6 meses)
                </h3>
            </div>
            <div class="card-body">
                <canvas id="conveniosMesChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    @if(Auth::user()->tienePermiso('auditoria.ver'))
    <!-- Actividad reciente -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Actividad Reciente
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="actividadReciente">
                            <tr>
                                <td colspan="3" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('auditoria.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye mr-1"></i> Ver Todo el Historial
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Panel de acciones rápidas -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>
                    Acciones Rápidas
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(Auth::user()->tienePermiso('convenios.crear'))
                    <a href="{{ route('convenios.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Nuevo Convenio
                    </a>
                    @endif

                    @if(Auth::user()->tienePermiso('usuarios.crear'))
                    <a href="{{ route('usuarios.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus mr-2"></i>Nuevo Usuario
                    </a>
                    @endif

                    @if(Auth::user()->tienePermiso('usuarios.leer'))
                    <a href="{{ route('usuarios.index') }}" class="btn btn-info">
                        <i class="fas fa-users mr-2"></i>Gestionar Usuarios
                    </a>
                    @endif

                    @if(Auth::user()->tienePermiso('reportes.ver'))
                    <a href="{{ route('reportes.convenios') }}" class="btn btn-warning">
                        <i class="fas fa-chart-bar mr-2"></i>Ver Reportes
                    </a>
                    @endif

                    @if(Auth::user()->tienePermiso('convenios.aprobar'))
                    <a href="{{ route('convenios.pendientes') }}" class="btn btn-secondary">
                        <i class="fas fa-clock mr-2"></i>Pendientes
                        <span class="badge badge-light ml-1" id="badgePendientes">0</span>
                    </a>
                    @endif
                </div>

                <!-- Estadísticas rápidas de usuarios -->
                @if(Auth::user()->tienePermiso('usuarios.leer'))
                <hr>
                <div class="mt-3">
                    <h6 class="text-muted">
                        <i class="fas fa-users mr-2"></i>Resumen de Usuarios
                    </h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="info-box-content">
                                <span class="info-box-number text-success" id="usuariosActivos">-</span>
                                <span class="info-box-text">Activos</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box-content">
                                <span class="info-box-number text-danger" id="usuariosInactivos">-</span>
                                <span class="info-box-text">Inactivos</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .small-box {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    
    .small-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card-header {
        border-radius: 10px 10px 0 0;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .btn-group .btn {
        border-radius: 8px !important;
        margin-right: 5px;
    }
    
    .d-grid .btn {
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    .badge {
        font-size: 0.75em;
    }

    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .info-box-text {
        display: block;
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: uppercase;
        font-weight: 500;
    }

    .btn-link:hover {
        text-decoration: underline !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
$(document).ready(function() {
    // Cargar estadísticas del dashboard
    cargarEstadisticas();
    cargarActividadReciente();
    cargarGraficos();
    
    // Actualizar cada 5 minutos
    setInterval(function() {
        cargarEstadisticas();
        cargarActividadReciente();
    }, 300000);
});

function cargarEstadisticas() {
    $.get('/api/dashboard/estadisticas')
        .done(function(data) {
            $('#totalConvenios').text(data.total_convenios || 0);
            $('#conveniosActivos').text(data.convenios_activos || 0);
            $('#conveniosPendientes').text(data.convenios_pendientes || 0);
            $('#conveniosPorVencer').text(data.convenios_por_vencer || 0);
            $('#totalUsuarios').text(data.total_usuarios || 0);
            $('#badgePendientes').text(data.convenios_pendientes || 0);
            
            // Estadísticas de usuarios
            $('#usuariosActivos').text(data.usuarios_activos || 0);
            $('#usuariosInactivos').text(data.usuarios_inactivos || 0);
            $('#usuariosRecientes').text(data.usuarios_mes_actual || 0);
        })
        .fail(function() {
            console.log('Error al cargar estadísticas');
        });
}

function cargarActividadReciente() {
    $.get('/api/dashboard/actividad-reciente')
        .done(function(data) {
            var html = '';
            if (data.length > 0) {
                data.forEach(function(item) {
                    html += '<tr>';
                    html += '<td><small>' + item.usuario + '</small></td>';
                    html += '<td><small>' + item.accion + '</small></td>';
                    html += '<td><small>' + item.fecha + '</small></td>';
                    html += '</tr>';
                });
            } else {
                html = '<tr><td colspan="3" class="text-center text-muted">No hay actividad reciente</td></tr>';
            }
            $('#actividadReciente').html(html);
        })
        .fail(function() {
            $('#actividadReciente').html('<tr><td colspan="3" class="text-center text-danger">Error al cargar datos</td></tr>');
        });
}

function cargarGraficos() {
    // Gráfico de convenios por estado
    $.get('/api/dashboard/convenios-estado')
        .done(function(data) {
            var ctx = document.getElementById('conveniosEstadoChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels || ['Activos', 'Pendientes', 'Vencidos'],
                        datasets: [{
                            data: data.values || [0, 0, 0],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        })
        .fail(function() {
            console.log('Error al cargar gráfico de convenios por estado');
        });

    // Gráfico de convenios por mes
    $.get('/api/dashboard/convenios-mes')
        .done(function(data) {
            var ctx = document.getElementById('conveniosMesChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels || [],
                        datasets: [{
                            label: 'Convenios Creados',
                            data: data.values || [],
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        })
        .fail(function() {
            console.log('Error al cargar gráfico de convenios por mes');
        });
}
</script>
@endpush