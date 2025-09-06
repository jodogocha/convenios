{{-- resources/views/convenios/pendientes.blade.php --}}
@extends('layouts.app')

@section('title', 'Convenios Pendientes de Aprobación')
@section('page-title', 'Convenios Pendientes de Aprobación')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('convenios.index') }}">Convenios</a></li>
<li class="breadcrumb-item active">Pendientes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Información sobre convenios pendientes -->
        <div class="alert alert-warning">
            <h5><i class="fas fa-hourglass-half mr-2"></i>Convenios Pendientes de Aprobación</h5>
            <p class="mb-0">
                Esta sección muestra todos los convenios que han sido marcados como "Versión Final Firmada" 
                y están esperando aprobación para pasar al estado activo.
            </p>
        </div>

        <!-- Filtros específicos para pendientes -->
        <div class="card card-outline card-warning">
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
                <form method="GET" action="{{ route('convenios.pendientes') }}">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="buscar" class="form-control" 
                                       placeholder="Buscar por institución, número, objeto..."
                                       value="{{ request('buscar') }}">
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
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
                            <select name="orden" class="form-control">
                                <option value="created_at" {{ request('orden') == 'created_at' ? 'selected' : '' }}>Más recientes primero</option>
                                <option value="fecha_firma" {{ request('orden') == 'fecha_firma' ? 'selected' : '' }}>Por fecha de firma</option>
                                <option value="institucion_contraparte" {{ request('orden') == 'institucion_contraparte' ? 'selected' : '' }}>Por institución</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-search mr-1"></i>Buscar
                                </button>
                                <a href="{{ route('convenios.pendientes') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $convenios->total() }}</h3>
                        <p>Convenios Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="conArchivo">{{ $convenios->where('archivo_convenio_path', '!=', null)->count() }}</h3>
                        <p>Con Archivo Adjunto</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="conDictamen">{{ $convenios->where('dictamen_numero', '!=', null)->count() }}</h3>
                        <p>Con Dictamen</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-stamp"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de convenios pendientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>
                    Convenios Pendientes ({{ $convenios->total() }} registros)
                </h3>
                <div class="card-tools">
                    @if(Auth::user()->tienePermiso('reportes.exportar'))
                    <a href="{{ route('convenios.exportar') }}?estado=pendiente_aprobacion{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}" 
                       class="btn btn-info btn-sm">
                        <i class="fas fa-download mr-1"></i>
                        Exportar
                    </a>
                    @endif
                    
                    <a href="{{ route('convenios.index') }}" class="btn btn-secondary btn-sm ml-1">
                        <i class="fas fa-list mr-1"></i>
                        Ver Todos
                    </a>
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
                                <th>Coordinador</th>
                                <th>Documentos</th>
                                <th>Creado</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($convenios as $convenio)
                            <tr>
                                <td>
                                    <strong class="text-monospace">{{ $convenio->numero_convenio }}</strong>
                                    <br>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-hourglass-half mr-1"></i>Pendiente
                                    </span>
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
                                    <div class="d-flex flex-column">
                                        @if($convenio->archivo_convenio_path)
                                            <span class="badge badge-success badge-sm mb-1">
                                                <i class="fas fa-file-pdf mr-1"></i>PDF: {{ $convenio->archivo_peso_formateado }}
                                            </span>
                                        @else
                                            <span class="badge badge-danger badge-sm mb-1">
                                                <i class="fas fa-times mr-1"></i>Sin archivo
                                            </span>
                                        @endif
                                        
                                        @if($convenio->dictamen_numero)
                                            <span class="badge badge-info badge-sm">
                                                <i class="fas fa-hashtag mr-1"></i>{{ $convenio->dictamen_numero }}
                                            </span>
                                        @else
                                            <span class="badge badge-warning badge-sm">
                                                <i class="fas fa-exclamation mr-1"></i>Sin dictamen
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $convenio->created_at->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">{{ $convenio->created_at->diffForHumans() }}</small>
                                        @if($convenio->usuarioCreador)
                                            <br>
                                            <small class="text-muted">
                                                por {{ $convenio->usuarioCreador->nombre }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm d-flex" role="group">
                                        <!-- Ver detalles -->
                                        <a href="{{ route('convenios.show', $convenio) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Descargar archivo -->
                                        @if($convenio->archivo_convenio_path)
                                        <a href="{{ route('convenios.descargar', $convenio) }}" 
                                           class="btn btn-secondary btn-sm" title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endif

                                        <!-- Aprobar -->
                                        @if($convenio->puedeSerAprobado())
                                        <button type="button" 
                                                class="btn btn-success btn-sm" 
                                                title="Aprobar convenio"
                                                onclick="aprobarConvenio({{ $convenio->id }}, '{{ $convenio->numero_convenio }}')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif

                                        <!-- Más acciones -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if($convenio->puedeSerEditado())
                                                <a class="dropdown-item" href="{{ route('convenios.edit', $convenio) }}">
                                                    <i class="fas fa-edit mr-2"></i>Editar
                                                </a>
                                                @endif

                                                <a class="dropdown-item text-warning" 
                                                   href="#" onclick="rechazarConvenio({{ $convenio->id }}, '{{ $convenio->numero_convenio }}')">
                                                    <i class="fas fa-times mr-2"></i>Rechazar
                                                </a>

                                                <a class="dropdown-item text-info" 
                                                   href="#" onclick="solicitarCambios({{ $convenio->id }}, '{{ $convenio->numero_convenio }}')">
                                                    <i class="fas fa-edit mr-2"></i>Solicitar Cambios
                                                </a>
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
                    <i class="fas fa-hourglass-half fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay convenios pendientes de aprobación</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'coordinador']))
                            Intenta modificar los filtros de búsqueda.
                        @else
                            Todos los convenios han sido procesados.
                        @endif
                    </p>
                    <a href="{{ route('convenios.index') }}" class="btn btn-primary">
                        <i class="fas fa-list mr-1"></i>
                        Ver Todos los Convenios
                    </a>
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

<!-- Modal para aprobar convenio con comentarios -->
<div class="modal fade" id="aprobarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="aprobarForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title">
                        <i class="fas fa-check mr-2"></i>
                        Aprobar Convenio
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Confirmación:</strong> El convenio <span id="numeroConvenioAprobar"></span> será aprobado y pasará al estado "Aprobado".
                    </div>
                    <div class="form-group">
                        <label for="comentarios_aprobacion">Comentarios de aprobación (opcional):</label>
                        <textarea class="form-control" 
                                  name="comentarios_aprobacion" 
                                  id="comentarios_aprobacion" 
                                  rows="3" 
                                  placeholder="Comentarios adicionales sobre la aprobación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>Aprobar Convenio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para rechazar convenio -->
<div class="modal fade" id="rechazarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rechazarForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h4 class="modal-title">
                        <i class="fas fa-times mr-2"></i>
                        Rechazar Convenio
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>¡Atención!</strong> El convenio <span id="numeroConvenioRechazar"></span> será rechazado y volverá al estado "Borrador".
                    </div>
                    <div class="form-group">
                        <label for="motivo_rechazo">Motivo del rechazo:</label>
                        <textarea class="form-control" 
                                  name="motivo_rechazo" 
                                  id="motivo_rechazo" 
                                  rows="3" 
                                  required
                                  placeholder="Especifique las razones del rechazo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>Rechazar Convenio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para solicitar cambios -->
<div class="modal fade" id="cambiosModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cambiosForm" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h4 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>
                        Solicitar Cambios
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Información:</strong> El convenio <span id="numeroConvenioCambios"></span> volverá al estado "Borrador" para que puedan realizarse los cambios solicitados.
                    </div>
                    <div class="form-group">
                        <label for="cambios_solicitados">Cambios solicitados:</label>
                        <textarea class="form-control" 
                                  name="cambios_solicitados" 
                                  id="cambios_solicitados" 
                                  rows="4" 
                                  required
                                  placeholder="Describa detalladamente los cambios que deben realizarse..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i>Solicitar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function aprobarConvenio(convenioId, numeroConvenio) {
    $('#numeroConvenioAprobar').text(numeroConvenio);
    $('#aprobarForm').attr('action', `/convenios/${convenioId}/aprobar`);
    $('#comentarios_aprobacion').val('');
    $('#aprobarModal').modal('show');
}

function rechazarConvenio(convenioId, numeroConvenio) {
    $('#numeroConvenioRechazar').text(numeroConvenio);
    $('#rechazarForm').attr('action', `/convenios/${convenioId}/rechazar`);
    $('#motivo_rechazo').val('');
    $('#rechazarModal').modal('show');
}

function solicitarCambios(convenioId, numeroConvenio) {
    $('#numeroConvenioCambios').text(numeroConvenio);
    $('#cambiosForm').attr('action', `/convenios/${convenioId}/solicitar-cambios`);
    $('#cambios_solicitados').val('');
    $('#cambiosModal').modal('show');
}

// Manejo de formularios
$('#aprobarForm').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var formData = form.serialize();
    
    $.post(form.attr('action'), formData)
        .done(function(response) {
            $('#aprobarModal').modal('hide');
            Swal.fire('¡Aprobado!', 'El convenio ha sido aprobado correctamente.', 'success');
            location.reload();
        })
        .fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo aprobar el convenio', 'error');
        });
});

$('#rechazarForm').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var formData = form.serialize();
    
    $.post(form.attr('action'), formData)
        .done(function(response) {
            $('#rechazarModal').modal('hide');
            Swal.fire('¡Rechazado!', 'El convenio ha sido rechazado y vuelve a estado borrador.', 'info');
            location.reload();
        })
        .fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo rechazar el convenio', 'error');
        });
});

$('#cambiosForm').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var formData = form.serialize();
    
    $.post(form.attr('action'), formData)
        .done(function(response) {
            $('#cambiosModal').modal('hide');
            Swal.fire('¡Enviado!', 'Se ha solicitado realizar cambios en el convenio.', 'info');
            location.reload();
        })
        .fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo solicitar los cambios', 'error');
        });
});
</script>
@endpush

@push('styles')
<style>
.badge-sm {
    font-size: 0.7em;
    padding: 0.2rem 0.4rem;
}

.table td {
    vertical-align: middle;
}

.d-flex.flex-column .badge {
    align-self: flex-start;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.dropdown-menu {
    font-size: 0.875rem;
}

.alert .badge {
    font-size: 0.8em;
}

.small-box {
    border-radius: 10px;
    transition: transform 0.2s ease;
}

.small-box:hover {
    transform: translateY(-2px);
}

.text-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
    }
}
</style>
@endpush