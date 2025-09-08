{{-- resources/views/informes/pendientes.blade.php --}}
@extends('layouts.app')

@section('title', 'Informes Pendientes de Revisión')
@section('page-title', 'Informes Pendientes de Revisión')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('informes.index') }}">Informes</a></li>
<li class="breadcrumb-item active">Pendientes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Información del panel -->
        <div class="alert alert-warning">
            <h5><i class="fas fa-clock mr-2"></i>Panel de Revisión de Informes</h5>
            <p class="mb-0">
                Esta sección muestra todos los informes que han sido enviados y están esperando revisión. 
                Como revisor, puede aprobar o rechazar cada informe según su contenido y calidad.
            </p>
        </div>

        <!-- Filtros -->
        <div class="card card-outline card-warning collapsed-card">
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
                <form method="GET" action="{{ route('informes.pendientes') }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="buscar">Buscar</label>
                            <input type="text" name="buscar" class="form-control" 
                                   placeholder="Buscar por institución, carrera, etc."
                                   value="{{ request('buscar') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="unidad_academica">Unidad Académica</label>
                            <select name="unidad_academica" class="form-control">
                                <option value="">Todas las unidades</option>
                                @foreach($unidadesAcademicas as $valor => $nombre)
                                    <option value="{{ $valor }}" 
                                            {{ request('unidad_academica') == $valor ? 'selected' : '' }}>
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
                        <div class="col-md-2 mb-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <a href="{{ route('informes.pendientes') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times mr-1"></i>Limpiar
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
                        <h3>{{ $informes->total() }}</h3>
                        <p>Informes Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $informes->where('convenio_ejecutado', true)->count() }}</h3>
                        <p>Convenios Ejecutados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $informes->where('convenio_ejecutado', false)->count() }}</h3>
                        <p>Convenios No Ejecutados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de informes pendientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    Informes Pendientes de Revisión ({{ $informes->total() }} registros)
                </h3>
                <div class="card-tools">
                    <a href="{{ route('informes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Lista Completa
                    </a>
                    <a href="{{ route('informes.exportar-excel') }}?estado=enviado" class="btn btn-success btn-sm ml-1">
                        <i class="fas fa-file-excel mr-1"></i>
                        Exportar Excel
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($informes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Convenio</th>
                                <th>Institución</th>
                                <th>Unidad Académica</th>
                                <th>Estado Ejecución</th>
                                <th>Fecha Presentación</th>
                                <th>Creado por</th>
                                <th width="200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($informes as $informe)
                            <tr class="informe-row" data-id="{{ $informe->id }}">
                                <td>
                                    <span class="badge badge-warning">#{{ $informe->id }}</span>
                                </td>
                                <td>
                                    @if($informe->convenio)
                                        <div>
                                            <strong>{{ $informe->convenio->numero_convenio }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($informe->convenio->institucion_contraparte, 30) }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-danger">Convenio eliminado</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($informe->institucion_co_celebrante, 30) }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $informe->unidad_academica }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($informe->carrera, 25) }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($informe->convenio_ejecutado)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>Ejecutado
                                        </span>
                                        @if($informe->numero_actividades_realizadas)
                                            <br><small class="text-muted">{{ $informe->numero_actividades_realizadas }} actividades</small>
                                        @endif
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-times mr-1"></i>No ejecutado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $informe->fecha_presentacion->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $informe->fecha_presentacion->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $informe->usuarioCreador ? $informe->usuarioCreador->nombre_completo : 'Usuario eliminado' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        {{-- Ver detalles --}}
                                        <a href="{{ route('informes.show', $informe) }}" 
                                           class="btn btn-info btn-sm mb-1" title="Ver detalles">
                                            <i class="fas fa-eye mr-1"></i>Ver Detalles
                                        </a>
                                        
                                        {{-- Aprobar --}}
                                        <button type="button" 
                                                class="btn btn-success btn-sm mb-1 btn-aprobar" 
                                                data-id="{{ $informe->id }}"
                                                data-institucion="{{ $informe->institucion_co_celebrante }}"
                                                title="Aprobar informe">
                                            <i class="fas fa-check mr-1"></i>Aprobar
                                        </button>

                                        {{-- Rechazar --}}
                                        <button type="button" 
                                                class="btn btn-danger btn-sm btn-rechazar" 
                                                data-id="{{ $informe->id }}"
                                                data-institucion="{{ $informe->institucion_co_celebrante }}"
                                                title="Rechazar informe">
                                            <i class="fas fa-times mr-1"></i>Rechazar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-muted">¡Excelente trabajo!</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'unidad_academica']))
                            No hay informes pendientes que coincidan con los filtros aplicados.
                        @else
                            No hay informes pendientes de revisión en este momento.
                        @endif
                    </p>
                    <a href="{{ route('informes.index') }}" class="btn btn-primary">
                        <i class="fas fa-list mr-1"></i>
                        Ver Todos los Informes
                    </a>
                </div>
                @endif
            </div>
            
            @if($informes->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando {{ $informes->firstItem() }} a {{ $informes->lastItem() }} 
                            de {{ $informes->total() }} resultados
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $informes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">
                    <i class="fas fa-check-circle mr-2"></i>
                    Aprobar Informe
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h5 class="text-center">¿Está seguro de que desea aprobar este informe?</h5>
                    <div class="alert alert-info mt-3">
                        <strong>Informe:</strong> #<span id="aprobar-id"></span><br>
                        <strong>Institución:</strong> <span id="aprobar-institucion"></span>
                    </div>
                    <p class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Una vez aprobado, el informe quedará marcado como revisado y aprobado.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>
                        Aprobar Informe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title text-white">
                    <i class="fas fa-times-circle mr-2"></i>
                    Rechazar Informe
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-times-circle fa-3x text-danger"></i>
                    </div>
                    <h5 class="text-center">¿Está seguro de que desea rechazar este informe?</h5>
                    <div class="alert alert-warning mt-3">
                        <strong>Informe:</strong> #<span id="rechazar-id"></span><br>
                        <strong>Institución:</strong> <span id="rechazar-institucion"></span>
                    </div>
                    <div class="form-group">
                        <label for="motivo_rechazo">
                            <i class="fas fa-comment mr-1"></i>
                            Motivo del Rechazo <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="motivo_rechazo" 
                                  name="motivo_rechazo" 
                                  rows="4" 
                                  required
                                  placeholder="Explique detalladamente los motivos del rechazo para que el creador pueda corregir el informe..."></textarea>
                        <small class="form-text text-muted">
                            Sea específico sobre los aspectos que deben corregirse.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>
                        Rechazar Informe
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
    // Manejar clic en botón aprobar
    $('.btn-aprobar').click(function() {
        var informeId = $(this).data('id');
        var institucion = $(this).data('institucion');
        
        $('#aprobar-id').text(informeId);
        $('#aprobar-institucion').text(institucion);
        $('#formAprobar').attr('action', '/informes/' + informeId + '/aprobar');
        $('#modalAprobar').modal('show');
    });

    // Manejar clic en botón rechazar
    $('.btn-rechazar').click(function() {
        var informeId = $(this).data('id');
        var institucion = $(this).data('institucion');
        
        $('#rechazar-id').text(informeId);
        $('#rechazar-institucion').text(institucion);
        $('#formRechazar').attr('action', '/informes/' + informeId + '/rechazar');
        $('#motivo_rechazo').val(''); // Limpiar textarea
        $('#modalRechazar').modal('show');
    });

    // Validar formulario de rechazo
    $('#formRechazar').submit(function(e) {
        var motivo = $('#motivo_rechazo').val().trim();
        if (motivo.length < 10) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Motivo insuficiente',
                text: 'Por favor proporcione un motivo más detallado (mínimo 10 caracteres).'
            });
            $('#motivo_rechazo').focus();
            return false;
        }
    });

    // Limpiar modales al cerrar
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    // Auto-expandir filtros si hay filtros aplicados
    @if(request()->hasAny(['buscar', 'unidad_academica']))
    $('[data-card-widget="collapse"]').click();
    @endif

    // Resaltar filas al hacer hover
    $('.informe-row').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
});
</script>
@endpush

@push('styles')
<style>
.informe-row {
    transition: background-color 0.2s ease;
}

.btn-group-vertical .btn {
    margin-bottom: 0;
}

.btn-group-vertical .btn:not(:last-child) {
    margin-bottom: 2px;
}

.small-box {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.modal-content {
    border-radius: 10px;
}

.modal-header {
    border-radius: 10px 10px 0 0;
}
</style>
@endpush