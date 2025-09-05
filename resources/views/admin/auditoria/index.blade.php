{{-- resources/views/admin/auditoria/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Auditoría del Sistema')
@section('page-title', 'Auditoría del Sistema')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Auditoría</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filtros -->
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filtros de búsqueda
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none;">
                <form method="GET" action="{{ route('auditoria.index') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="usuario_id">Usuario</label>
                            <select name="usuario_id" class="form-control">
                                <option value="">Todos los usuarios</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" 
                                            {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->nombre_completo }} ({{ $usuario->username }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="accion">Acción</label>
                            <select name="accion" class="form-control">
                                <option value="">Todas las acciones</option>
                                @foreach($acciones as $valor => $nombre)
                                    <option value="{{ $valor }}" 
                                            {{ request('accion') == $valor ? 'selected' : '' }}>
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tabla_afectada">Tabla</label>
                            <select name="tabla_afectada" class="form-control">
                                <option value="">Todas las tablas</option>
                                @foreach($tablas as $valor => $nombre)
                                    <option value="{{ $valor }}" 
                                            {{ request('tabla_afectada') == $valor ? 'selected' : '' }}>
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search mr-1"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="fecha_desde">Fecha desde</label>
                            <input type="date" name="fecha_desde" class="form-control" 
                                   value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fecha_hasta">Fecha hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" 
                                   value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <a href="{{ route('auditoria.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times mr-1"></i>Limpiar filtros
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <a href="{{ route('auditoria.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                                   class="btn btn-success btn-block">
                                    <i class="fas fa-download mr-1"></i>Exportar CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $auditorias->total() }}</h3>
                        <p>Total de registros</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="loginHoy">0</h3>
                        <p>Logins hoy</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="cambiosHoy">0</h3>
                        <p>Cambios hoy</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="accesoDenegadoHoy">0</h3>
                        <p>Accesos denegados hoy</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de auditoría -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Registro de Auditoría ({{ $auditorias->total() }} registros)
                </h3>
                <div class="card-tools">
                    @if(Auth::user()->tieneRol('super_admin'))
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#cleanModal">
                        <i class="fas fa-broom mr-1"></i>
                        Limpiar Antiguos
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($auditorias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="80">ID</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Tabla</th>
                                <th width="100">Registro</th>
                                <th>IP</th>
                                <th>Fecha</th>
                                <th width="100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditorias as $auditoria)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">#{{ $auditoria->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-lg mr-2 text-muted"></i>
                                        <div>
                                            <strong>{{ $auditoria->nombre_usuario }}</strong>
                                            @if($auditoria->usuario)
                                                <br>
                                                <small class="text-muted">{{ $auditoria->usuario->username }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $auditoria->icono_accion }} mr-2"></i>
                                        <span>{{ $auditoria->descripcion_accion }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($auditoria->tabla_afectada)
                                        <span class="badge badge-info">{{ ucfirst($auditoria->tabla_afectada) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($auditoria->registro_id)
                                        <span class="text-monospace">#{{ $auditoria->registro_id }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($auditoria->ip_address)
                                        <small class="text-monospace">{{ $auditoria->ip_address }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $auditoria->fecha_hora->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $auditoria->fecha_hora->format('H:i:s') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $auditoria->fecha_hora->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('auditoria.show', $auditoria) }}" 
                                       class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay registros de auditoría</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['usuario_id', 'accion', 'tabla_afectada', 'fecha_desde', 'fecha_hasta']))
                            Intenta modificar los filtros de búsqueda.
                        @else
                            Los registros de auditoría aparecerán aquí.
                        @endif
                    </p>
                </div>
                @endif
            </div>
            
            @if($auditorias->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando {{ $auditorias->firstItem() }} a {{ $auditorias->lastItem() }} 
                            de {{ $auditorias->total() }} resultados
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $auditorias->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para limpiar registros antiguos -->
<div class="modal fade" id="cleanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('auditoria.clean') }}">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-broom mr-2"></i>
                        Limpiar Registros Antiguos
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>¡Atención!</strong> Esta acción eliminará permanentemente los registros 
                        de auditoría más antiguos que el número de días especificado.
                    </div>
                    <div class="form-group">
                        <label for="dias">Eliminar registros anteriores a:</label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control" 
                                   name="dias" 
                                   id="dias" 
                                   value="90" 
                                   min="30" 
                                   max="730" 
                                   required>
                            <div class="input-group-append">
                                <span class="input-group-text">días</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Mínimo 30 días, máximo 2 años (730 días)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-broom mr-1"></i>Limpiar Registros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Cargar estadísticas del día
    cargarEstadisticasAuditoria();
    
    // Confirmación para limpiar registros
    $('#cleanModal form').on('submit', function(e) {
        e.preventDefault();
        var dias = $('#dias').val();
        
        Swal.fire({
            title: '¿Está seguro?',
            text: `Se eliminarán todos los registros de auditoría anteriores a ${dias} días`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});

function cargarEstadisticasAuditoria() {
    // Simular carga de estadísticas (puedes implementar endpoints reales)
    var hoy = new Date().toISOString().split('T')[0];
    
    $.get('/api/auditoria/estadisticas', { fecha: hoy })
        .done(function(data) {
            $('#loginHoy').text(data.logins || 0);
            $('#cambiosHoy').text(data.cambios || 0);
            $('#accesoDenegadoHoy').text(data.accesos_denegados || 0);
        })
        .fail(function() {
            // Si falla, mantener en 0
            console.log('Error cargando estadísticas de auditoría');
        });
}
</script>
@endpush