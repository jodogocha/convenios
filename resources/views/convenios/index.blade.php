{{-- resources/views/convenios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Convenios')
@section('page-title', 'Gestión de Convenios')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Convenios</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filtros -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filtros de búsqueda
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('convenios.index') }}">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="buscar" class="form-control" 
                                       placeholder="Buscar por institución, número, objeto..."
                                       value="{{ request('buscar') }}">
                            </div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                @foreach($estados as $key => $estado)
                                    <option value="{{ $key }}" 
                                            {{ request('estado') == $key ? 'selected' : '' }}>
                                        {{ $estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="tipo" class="form-control">
                                <option value="">Todos los tipos</option>
                                @foreach($tipos as $key => $tipo)
                                    <option value="{{ $key }}" 
                                            {{ request('tipo') == $key ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="coordinador" class="form-control">
                                <option value="">Todos los coordinadores</option>
                                @foreach($coordinadores as $key => $coordinador)
                                    <option value="{{ $key }}" 
                                            {{ request('coordinador') == $key ? 'selected' : '' }}>
                                        {{ $coordinador }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>Buscar
                                </button>
                                <a href="{{ route('convenios.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtros adicionales (colapsables) -->
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label for="fecha_desde" class="form-label">Fecha desde:</label>
                            <input type="date" name="fecha_desde" class="form-control" 
                                   value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_hasta" class="form-label">Fecha hasta:</label>
                            <input type="date" name="fecha_hasta" class="form-control" 
                                   value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="vencimiento" class="form-label">Por vencer en:</label>
                            <select name="vencimiento" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="7" {{ request('vencimiento') == '7' ? 'selected' : '' }}>7 días</option>
                                <option value="30" {{ request('vencimiento') == '30' ? 'selected' : '' }}>30 días</option>
                                <option value="60" {{ request('vencimiento') == '60' ? 'selected' : '' }}>60 días</option>
                                <option value="90" {{ request('vencimiento') == '90' ? 'selected' : '' }}>90 días</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="orden" class="form-label">Ordenar por:</label>
                            <select name="orden" class="form-control">
                                <option value="fecha_firma" {{ request('orden') == 'fecha_firma' ? 'selected' : '' }}>Fecha de Firma</option>
                                <option value="created_at" {{ request('orden') == 'created_at' ? 'selected' : '' }}>Fecha de Creación</option>
                                <option value="numero_convenio" {{ request('orden') == 'numero_convenio' ? 'selected' : '' }}>Número</option>
                                <option value="institucion_contraparte" {{ request('orden') == 'institucion_contraparte' ? 'selected' : '' }}>Institución</option>
                            </select>
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
                        <h3>{{ $convenios->total() }}</h3>
                        <p>Total de Convenios</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="conveniosActivos">0</h3>
                        <p>Convenios Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="conveniosPendientes">0</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="conveniosPorVencer">0</h3>
                        <p>Por Vencer</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de convenios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>
                    Lista de Convenios ({{ $convenios->total() }} registros)
                </h3>
                <div class="card-tools">
                    @if(Auth::user()->tienePermiso('convenios.crear'))
                    <a href="{{ route('convenios.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Convenio
                    </a>
                    @endif
                    
                    @if(Auth::user()->tienePermiso('reportes.exportar'))
                    <a href="{{ route('convenios.exportar') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                       class="btn btn-info btn-sm ml-1">
                        <i class="fas fa-download mr-1"></i>
                        Exportar
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($convenios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Número</th>
                                <th>Institución Contraparte</th>
                                <th>Tipo</th>
                                <th>Fecha Firma</th>
                                <th>Vigencia</th>
                                <th>Estado</th>
                                <th>Coordinador</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($convenios as $convenio)
                            <tr class="{{ $convenio->esta_vencido ? 'table-danger' : ($convenio->proximoAVencer(30) ? 'table-warning' : '') }}">
                                <td>
                                    <strong class="text-monospace">{{ $convenio->numero_convenio }}</strong>
                                    @if($convenio->archivo_convenio_path)
                                        <br><small class="text-muted">
                                            <i class="fas fa-file-pdf text-danger"></i> 
                                            {{ $convenio->archivo_peso_formateado }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $convenio->institucion_contraparte }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-globe mr-1"></i>{{ $convenio->pais_region }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $convenio->tipo_convenio }}</span>
                                </td>
                                <td>
                                    {{ $convenio->fecha_firma->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $convenio->fecha_firma->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($convenio->vigencia_indefinida)
                                        <span class="badge badge-info">
                                            <i class="fas fa-infinity mr-1"></i>Indefinida
                                        </span>
                                    @elseif($convenio->fecha_vencimiento)
                                        <div>
                                            <strong>{{ $convenio->fecha_vencimiento->format('d/m/Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $convenio->vigencia_texto }}</small>
                                        </div>
                                        @if($convenio->proximoAVencer(30))
                                            <br><span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Por vencer
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">No especificada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $convenio->estado_badge }}">
                                        {{ $convenio->estado_texto }}
                                    </span>
                                    @if($convenio->version_final_firmada)
                                        <br><small class="text-success">
                                            <i class="fas fa-check mr-1"></i>Firmado
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $convenio->coordinador_convenio }}</small>
                                    @if($convenio->usuarioCoordinador)
                                        <br>
                                        <small class="text-primary">
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $convenio->usuarioCoordinador->nombre }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Ver detalles -->
                                        <a href="{{ route('convenios.show', $convenio) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Editar (solo borradores y pendientes) -->
                                        @if($convenio->puedeSerEditado() && Auth::user()->tienePermiso('convenios.actualizar'))
                                        <a href="{{ route('convenios.edit', $convenio) }}" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif

                                        <!-- Descargar archivo -->
                                        @if($convenio->archivo_convenio_path)
                                        <a href="{{ route('convenios.descargar', $convenio) }}" 
                                           class="btn btn-secondary btn-sm" title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endif

                                        <!-- Más acciones -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if($convenio->puedeSerAprobado() && Auth::user()->tienePermiso('convenios.aprobar'))
                                                <a class="dropdown-item text-success" 
                                                   href="#" onclick="aprobarConvenio({{ $convenio->id }})">
                                                    <i class="fas fa-check mr-2"></i>Aprobar
                                                </a>
                                                @endif

                                                @if($convenio->estado === 'aprobado' && Auth::user()->tienePermiso('convenios.aprobar'))
                                                <a class="dropdown-item text-info" 
                                                   href="#" onclick="activarConvenio({{ $convenio->id }})">
                                                    <i class="fas fa-play mr-2"></i>Activar
                                                </a>
                                                @endif

                                                @if($convenio->puedeSerCancelado() && Auth::user()->tienePermiso('convenios.aprobar'))
                                                <a class="dropdown-item text-warning" 
                                                   href="#" onclick="cancelarConvenio({{ $convenio->id }})">
                                                    <i class="fas fa-ban mr-2"></i>Cancelar
                                                </a>
                                                @endif

                                                @if($convenio->estado === 'borrador' && (Auth::user()->tieneRol('super_admin') || $convenio->usuario_creador_id === Auth::id()))
                                                <div class="dropdown-divider"></div>
                                                <form method="POST" action="{{ route('convenios.destroy', $convenio) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger btn-delete">
                                                        <i class="fas fa-trash mr-2"></i>Eliminar
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron convenios</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'estado', 'tipo', 'coordinador', 'fecha_desde', 'fecha_hasta']))
                            Intenta modificar los filtros de búsqueda.
                        @else
                            Comienza creando el primer convenio.
                        @endif
                    </p>
                    @if(Auth::user()->tienePermiso('convenios.crear'))
                    <a href="{{ route('convenios.create') }}" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i>
                        Crear Primer Convenio
                    </a>
                    @endif
                </div>
                @endif
            </div>
            
            @if($convenios->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando {{ $convenios->firstItem() }} a {{ $convenios->lastItem() }} 
                            de {{ $convenios->total() }} resultados
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $convenios->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para cancelar convenio -->
<div class="modal fade" id="cancelarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cancelarForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-ban mr-2"></i>
                        Cancelar Convenio
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>¡Atención!</strong> Esta acción cancelará el convenio permanentemente.
                    </div>
                    <div class="form-group">
                        <label for="motivo_cancelacion">Motivo de cancelación:</label>
                        <textarea class="form-control" 
                                  name="motivo_cancelacion" 
                                  id="motivo_cancelacion" 
                                  rows="3" 
                                  required
                                  placeholder="Especifique el motivo de la cancelación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-ban mr-1"></i>Cancelar Convenio
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
    // Cargar estadísticas
    cargarEstadisticasConvenios();
    
    // Confirmación para eliminaciones
    $('.btn-delete').click(function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción eliminará permanentemente el convenio",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

function cargarEstadisticasConvenios() {
    // Simular carga de estadísticas (puedes implementar endpoints reales)
    $.get('/api/convenios/estadisticas')
        .done(function(data) {
            $('#conveniosActivos').text(data.activos || 0);
            $('#conveniosPendientes').text(data.pendientes || 0);
            $('#conveniosPorVencer').text(data.por_vencer || 0);
        })
        .fail(function() {
            console.log('Error cargando estadísticas de convenios');
        });
}

function aprobarConvenio(convenioId) {
    Swal.fire({
        title: '¿Aprobar convenio?',
        text: "El convenio pasará al estado 'Aprobado'",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/convenios/${convenioId}/aprobar`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                Swal.fire('¡Aprobado!', 'El convenio ha sido aprobado correctamente.', 'success');
                location.reload();
            })
            .fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo aprobar el convenio', 'error');
            });
        }
    });
}

function activarConvenio(convenioId) {
    Swal.fire({
        title: '¿Activar convenio?',
        text: "El convenio pasará al estado 'Activo'",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/convenios/${convenioId}/activar`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                Swal.fire('¡Activado!', 'El convenio ha sido activado correctamente.', 'success');
                location.reload();
            })
            .fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo activar el convenio', 'error');
            });
        }
    });
}

function cancelarConvenio(convenioId) {
    $('#cancelarForm').attr('action', `/convenios/${convenioId}/cancelar`);
    $('#motivo_cancelacion').val('');
    $('#cancelarModal').modal('show');
}

// Manejo del formulario de cancelación
$('#cancelarForm').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var formData = form.serialize();
    
    $.post(form.attr('action'), formData)
        .done(function(response) {
            $('#cancelarModal').modal('hide');
            Swal.fire('¡Cancelado!', 'El convenio ha sido cancelado correctamente.', 'success');
            location.reload();
        })
        .fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo cancelar el convenio', 'error');
        });
});
</script>
@endpush

@push('styles')
<style>
.table-responsive .table th {
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

.btn-group .dropdown-menu {
    font-size: 0.875rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.text-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.small-box {
    border-radius: 10px;
    transition: transform 0.2s ease;
}

.small-box:hover {
    transform: translateY(-2px);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.card-header .btn {
    font-size: 0.875rem;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.dropdown-toggle::after {
    margin-left: 0.255em;
}
</style>
@endpush