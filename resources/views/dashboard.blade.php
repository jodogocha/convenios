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
        <div class="card card-outline welcome-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title mb-2">
                            <i class="fas fa-user-circle mr-2 text-white"></i>
                            ¡Bienvenido, {{ $usuario->nombre_completo }}!
                        </h4>
                        <p class="card-text text-white-50 mb-0">                          
                        </p>
                        @if($ultimoLogin)
                        <p class="card-text text-white-50 mb-0">
                            <i class="fas fa-clock mr-2"></i>
                            Último acceso: <strong class="text-white">{{ $ultimoLogin->format('d/m/Y H:i:s') }}</strong>
                        </p>
                        @endif
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="actualizarEstadisticas()" title="Actualizar estadísticas">
                                <i class="fas fa-sync-alt mr-1"></i>Actualizar
                            </button>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm" title="Cerrar sesión">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Salir
                                </button>
                            </form>
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
                <h3 id="totalConvenios">
                    <i class="fas fa-spinner fa-spin"></i>
                </h3>
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
                <h3 id="conveniosActivos">
                    <i class="fas fa-spinner fa-spin"></i>
                </h3>
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
                <h3 id="conveniosPendientes">
                    <i class="fas fa-spinner fa-spin"></i>
                </h3>
                <p>Pendientes de Aprobación</p>
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
                <h3 id="conveniosPorVencer">
                    <i class="fas fa-spinner fa-spin"></i>
                </h3>
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
                <h3 id="totalUsuarios">
                    <i class="fas fa-spinner fa-spin"></i>
                </h3>
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

    <!-- Gráfico de usuarios registrados por mes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Usuarios Registrados (Últimos 6 meses)
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
                        <span class="badge badge-light ml-1" id="badgePendientes">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
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
                                <span class="info-box-number text-success" id="usuariosActivos">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <span class="info-box-text">Activos</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box-content">
                                <span class="info-box-number text-danger" id="usuariosInactivos">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <span class="info-box-text">Inactivos</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estadísticas adicionales -->
                    <div class="row text-center mt-2">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fas fa-user-plus mr-1"></i>
                                Nuevos esta semana: 
                                <span class="font-weight-bold text-info" id="usuariosRecientes">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Card adicional con estadísticas de sistema para super_admin -->
@if(Auth::user()->tieneRol('super_admin'))
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs mr-2"></i>
                    Estadísticas del Sistema
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Logins Hoy</span>
                                <span class="info-box-number" id="loginsHoy">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Roles</span>
                                <span class="info-box-number" id="totalRoles">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-users"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Activos 30 días</span>
                                <span class="info-box-number" id="activosUltimos30">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger">
                                <i class="fas fa-lock"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Bloqueados</span>
                                <span class="info-box-number" id="usuariosBloqueados">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    /* Tarjeta de bienvenida con el mismo estilo que la barra lateral */
    .welcome-card {
        background: linear-gradient(180deg, rgb(70, 1, 1) 0%, rgb(34, 0, 0) 100%);
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        color: white;
    }
    
    .welcome-card .card-body {
        background: transparent;
    }
    
    .welcome-card .card-title {
        color: white;
    }
    
    .welcome-card .text-white-50 {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    
    .welcome-card .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.3);
        color: white;
        transition: all 0.3s ease;
    }
    
    .welcome-card .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        transform: translateY(-1px);
    }

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
        font-size: 14px !important;
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

    .loading-spinner {
        color: #6c757d;
        font-size: 0.8em;
    }

    .info-box {
        border-radius: 8px;
    }

    .info-box-icon {
        border-radius: 8px 0 0 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
$(document).ready(function() {
    // Cargar estadísticas del dashboard
    cargarEstadisticas();
    cargarEstadisticasUsuarios();
    cargarActividadReciente();
    cargarGraficos();
    
    // Actualizar cada 5 minutos
    setInterval(function() {
        cargarEstadisticas();
        cargarEstadisticasUsuarios();
        cargarActividadReciente();
    }, 300000);
});

function cargarEstadisticas() {
    $.get('/api/dashboard/estadisticas')
        .done(function(data) {
            // Actualizar widgets principales
            $('#totalConvenios').text(data.total_convenios || 0);
            $('#conveniosActivos').text(data.convenios_activos || 0);
            $('#conveniosPendientes').text(data.convenios_pendientes || 0);
            $('#conveniosPorVencer').text(data.convenios_por_vencer || 0);
            
            // Actualizar widget de usuarios
            $('#totalUsuarios').text(data.total_usuarios || 0);
            
            // Actualizar badge de pendientes
            $('#badgePendientes').text(data.convenios_pendientes || 0);
            
            // Estadísticas adicionales para super_admin
            $('#loginsHoy').text(data.logins_hoy || 0);
            $('#totalRoles').text(data.total_roles || 0);
            
            console.log('Estadísticas cargadas:', data);
        })
        .fail(function(xhr, status, error) {
            console.error('Error al cargar estadísticas:', error);
            // Mostrar valores por defecto en caso de error
            $('#totalConvenios').text('N/A');
            $('#conveniosActivos').text('N/A');
            $('#conveniosPendientes').text('N/A');
            $('#conveniosPorVencer').text('N/A');
            $('#totalUsuarios').text('N/A');
            $('#badgePendientes').text('0');
            $('#loginsHoy').text('N/A');
            $('#totalRoles').text('N/A');
        });
}

function cargarEstadisticasUsuarios() {
    $.get('/api/dashboard/estadisticas-usuarios')
        .done(function(data) {
            // Actualizar resumen de usuarios en panel lateral
            $('#usuariosActivos').text(data.activos || 0);
            $('#usuariosInactivos').text(data.inactivos || 0);
            $('#usuariosRecientes').text(data.nuevos_ultimos_7_dias || 0);
            
            // Estadísticas adicionales para super_admin
            $('#activosUltimos30').text(data.activos_ultimos_30_dias || 0);
            $('#usuariosBloqueados').text(data.bloqueados || 0);
            
            console.log('Estadísticas de usuarios cargadas:', data);
        })
        .fail(function(xhr, status, error) {
            console.error('Error al cargar estadísticas de usuarios:', error);
            $('#usuariosActivos').text('N/A');
            $('#usuariosInactivos').text('N/A');
            $('#usuariosRecientes').text('N/A');
            $('#activosUltimos30').text('N/A');
            $('#usuariosBloqueados').text('N/A');
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
                    html += '<td>';
                    html += '<i class="' + item.icono + ' mr-1"></i>';
                    html += '<small>' + item.accion + '</small>';
                    html += '</td>';
                    html += '<td><small>' + item.fecha + '</small></td>';
                    html += '</tr>';
                });
            } else {
                html = '<tr><td colspan="3" class="text-center text-muted">No hay actividad reciente</td></tr>';
            }
            $('#actividadReciente').html(html);
        })
        .fail(function(xhr, status, error) {
            console.error('Error al cargar actividad reciente:', error);
            $('#actividadReciente').html('<tr><td colspan="3" class="text-center text-danger">Error al cargar datos</td></tr>');
        });
}

function cargarGraficos() {
    // Gráfico de convenios por estado (datos simulados por ahora)
    $.get('/api/dashboard/estadisticas')
        .done(function(data) {
            var ctx = document.getElementById('conveniosEstadoChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Activos', 'Pendientes', 'Vencidos'],
                        datasets: [{
                            data: [
                                data.convenios_activos || 0, 
                                data.convenios_pendientes || 0, 
                                data.convenios_por_vencer || 0
                            ],
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

    // Gráfico de usuarios por mes
    $.get('/api/dashboard/usuarios-mes')
        .done(function(data) {
            var ctx = document.getElementById('conveniosMesChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels || [],
                        datasets: [{
                            label: 'Usuarios Registrados',
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
            console.log('Error al cargar gráfico de usuarios por mes');
        });

    // Gráfico de logins por día (opcional)
    $.get('/api/dashboard/logins-dia')
        .done(function(data) {
            // Puedes crear otro gráfico aquí si quieres mostrar logins
            console.log('Datos de logins por día:', data);
        })
        .fail(function() {
            console.log('Error al cargar datos de logins por día');
        });
}

// Función para actualizar manualmente las estadísticas
window.actualizarEstadisticas = function() {
    // Mostrar spinners
    $('#totalUsuarios, #usuariosActivos, #usuariosInactivos, #usuariosRecientes').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#badgePendientes').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#loginsHoy, #totalRoles, #activosUltimos30, #usuariosBloqueados').html('<i class="fas fa-spinner fa-spin"></i>');
    
    // Cargar datos actualizados
    cargarEstadisticas();
    cargarEstadisticasUsuarios();
    cargarActividadReciente();
    
    // Mostrar mensaje de éxito
    $(document).Toasts('create', {
        class: 'bg-success',
        title: 'Actualizado',
        subtitle: 'Dashboard',
        body: 'Estadísticas actualizadas correctamente'
    });
};

// Mostrar indicador de carga
function mostrarCargando(elemento) {
    $(elemento).html('<i class="fas fa-spinner fa-spin"></i>');
}

// Función para formatear números grandes
function formatearNumero(numero) {
    if (numero >= 1000000) {
        return (numero / 1000000).toFixed(1) + 'M';
    } else if (numero >= 1000) {
        return (numero / 1000).toFixed(1) + 'K';
    }
    return numero.toString();
}
</script>
@endpush