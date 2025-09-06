{{-- resources/views/convenios/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalles del Convenio')
@section('page-title', 'Detalles del Convenio')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('convenios.index') }}">Convenios</a></li>
<li class="breadcrumb-item active">{{ $convenio->numero_convenio }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Información Principal del Convenio -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-handshake mr-2"></i>
                    {{ $convenio->numero_convenio }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $convenio->estado_badge }} badge-lg">
                        {{ $convenio->estado_texto }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-university mr-1"></i>
                            Información de la Contraparte
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Institución:</td>
                                <td>{{ $convenio->institucion_contraparte }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">País/Región:</td>
                                <td>
                                    <i class="fas fa-globe mr-1"></i>
                                    {{ $convenio->pais_region }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Tipo de Convenio:</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $convenio->tipo_convenio }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-calendar mr-1"></i>
                            Información Temporal
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Fecha de Firma:</td>
                                <td>
                                    {{ $convenio->fecha_firma->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $convenio->fecha_firma->diffForHumans() }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Vigencia:</td>
                                <td>
                                    @if($convenio->vigencia_indefinida)
                                        <span class="badge badge-info">
                                            <i class="fas fa-infinity mr-1"></i>Indefinida
                                        </span>
                                    @elseif($convenio->fecha_vencimiento)
                                        {{ $convenio->fecha_vencimiento->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">{{ $convenio->vigencia_texto }}</small>
                                        @if($convenio->proximoAVencer(30))
                                            <br><span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Por vencer
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">No especificada</span>
                                    @endif
                                </td>
                            </tr>
                            @if($convenio->dias_para_vencimiento !== null)
                            <tr>
                                <td class="font-weight-bold">Días restantes:</td>
                                <td>
                                    @if($convenio->dias_para_vencimiento > 0)
                                        <span class="text-success">{{ $convenio->dias_para_vencimiento }} días</span>
                                    @elseif($convenio->dias_para_vencimiento === 0)
                                        <span class="text-warning">Vence hoy</span>
                                    @else
                                        <span class="text-danger">Vencido hace {{ abs($convenio->dias_para_vencimiento) }} días</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Objeto del Convenio -->
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">
                            <i class="fas fa-clipboard mr-1"></i>
                            Objeto del Convenio
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">{{ $convenio->objeto }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signatarios -->
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">
                            <i class="fas fa-signature mr-1"></i>
                            Signatarios
                        </h6>
                        @if($convenio->signatarios && count($convenio->signatarios) > 0)
                            <div class="row">
                                @foreach($convenio->signatarios as $signatario)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie mr-2 text-muted"></i>
                                            <span>{{ $signatario }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No se han especificado signatarios.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Coordinación y Responsables -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Coordinación y Responsables
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-user-tie mr-1"></i>
                            Coordinación Institucional
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Coordinador:</td>
                                <td>{{ $convenio->coordinador_convenio }}</td>
                            </tr>
                            @if($convenio->usuarioCoordinador)
                            <tr>
                                <td class="font-weight-bold">Usuario Asignado:</td>
                                <td>
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $convenio->usuarioCoordinador->nombre_completo }}
                                    <br>
                                    <small class="text-muted">({{ $convenio->usuarioCoordinador->username }})</small>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-user-cog mr-1"></i>
                            Gestión del Sistema
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Creado por:</td>
                                <td>
                                    @if($convenio->usuarioCreador)
                                        {{ $convenio->usuarioCreador->nombre_completo }}
                                        <br>
                                        <small class="text-muted">{{ $convenio->created_at->format('d/m/Y H:i') }}</small>
                                    @else
                                        <span class="text-muted">Usuario eliminado</span>
                                    @endif
                                </td>
                            </tr>
                            @if($convenio->usuarioAprobador)
                            <tr>
                                <td class="font-weight-bold">Aprobado por:</td>
                                <td>
                                    {{ $convenio->usuarioAprobador->nombre_completo }}
                                    <br>
                                    <small class="text-muted">{{ $convenio->fecha_aprobacion->format('d/m/Y H:i') }}</small>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentación -->
        @if($convenio->archivo_convenio_path || $convenio->dictamen_numero || $convenio->observaciones)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    Documentación y Observaciones
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($convenio->archivo_convenio_path)
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-file-pdf mr-1"></i>
                            Archivo del Convenio
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                                    <div>
                                        <strong>{{ $convenio->archivo_convenio_nombre }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $convenio->archivo_peso_formateado }}</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('convenios.descargar', $convenio) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-download mr-1"></i>Descargar
                                    </a>
                                    <a href="{{ $convenio->archivo_url }}" 
                                       target="_blank" 
                                       class="btn btn-secondary btn-sm ml-1">
                                        <i class="fas fa-external-link-alt mr-1"></i>Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($convenio->dictamen_numero)
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-hashtag mr-1"></i>
                            Información Legal
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Dictamen N°:</td>
                                <td>
                                    <span class="text-monospace">{{ $convenio->dictamen_numero }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Versión Final:</td>
                                <td>
                                    @if($convenio->version_final_firmada)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>Firmada
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i>Pendiente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endif
                </div>

                @if($convenio->observaciones)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">
                            <i class="fas fa-sticky-note mr-1"></i>
                            Observaciones
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-line;">{{ $convenio->observaciones }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Acciones Rápidas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>
                    Acciones
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <!-- Volver -->
                    <a href="{{ route('convenios.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Lista
                    </a>

                    <!-- Editar -->
                    @if($convenio->puedeSerEditado() && Auth::user()->tienePermiso('convenios.actualizar'))
                    <a href="{{ route('convenios.edit', $convenio) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit mr-1"></i>
                        Editar Convenio
                    </a>
                    @endif

                    <!-- Descargar -->
                    @if($convenio->archivo_convenio_path)
                    <a href="{{ route('convenios.descargar', $convenio) }}" class="btn btn-info btn-block">
                        <i class="fas fa-download mr-1"></i>
                        Descargar PDF
                    </a>
                    @endif

                    <hr>

                    <!-- Acciones de Estado (solo para admin/super_admin) -->
                    @if(Auth::user()->tienePermiso('convenios.aprobar'))
                        @if($convenio->puedeSerAprobado())
                        <button type="button" class="btn btn-success btn-block" onclick="aprobarConvenio()">
                            <i class="fas fa-check mr-1"></i>
                            Aprobar Convenio
                        </button>
                        @endif

                        @if($convenio->estado === 'aprobado')
                        <button type="button" class="btn btn-primary btn-block" onclick="activarConvenio()">
                            <i class="fas fa-play mr-1"></i>
                            Activar Convenio
                        </button>
                        @endif

                        @if($convenio->puedeSerCancelado())
                        <button type="button" class="btn btn-outline-warning btn-block" onclick="cancelarConvenio()">
                            <i class="fas fa-ban mr-1"></i>
                            Cancelar Convenio
                        </button>
                        @endif
                    @endif

                    <!-- Eliminar (solo para borradores) -->
                    @if($convenio->estado === 'borrador' && (Auth::user()->tieneRol('super_admin') || $convenio->usuario_creador_id === Auth::id()))
                    <form method="POST" action="{{ route('convenios.destroy', $convenio) }}" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block btn-delete">
                            <i class="fas fa-trash mr-1"></i>
                            Eliminar Convenio
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información Técnica -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Información Técnica
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="font-weight-bold">ID:</td>
                        <td><span class="text-monospace">#{{ $convenio->id }}</span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Número:</td>
                        <td><span class="text-monospace">{{ $convenio->numero_convenio }}</span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Creado:</td>
                        <td>
                            {{ $convenio->created_at->format('d/m/Y') }}
                            <br>
                            <small class="text-muted">{{ $convenio->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Modificado:</td>
                        <td>
                            {{ $convenio->updated_at->format('d/m/Y') }}
                            <br>
                            <small class="text-muted">{{ $convenio->updated_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    @if($convenio->fecha_aprobacion)
                    <tr>
                        <td class="font-weight-bold">Aprobado:</td>
                        <td>
                            {{ $convenio->fecha_aprobacion->format('d/m/Y') }}
                            <br>
                            <small class="text-muted">{{ $convenio->fecha_aprobacion->diffForHumans() }}</small>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Progreso del Convenio -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Estado del Proceso
                </h3>
            </div>
            <div class="card-body">
                <div class="progress-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="progress-text">Registro</span>
                        <span class="badge badge-success"><i class="fas fa-check"></i></span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="progress-text">Versión Final</span>
                        @if($convenio->version_final_firmada)
                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-clock"></i></span>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="progress-text">Aprobación</span>
                        @if(in_array($convenio->estado, ['aprobado', 'activo']))
                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                        @elseif($convenio->estado === 'pendiente_aprobacion')
                            <span class="badge badge-warning"><i class="fas fa-hourglass-half"></i></span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-minus"></i></span>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="progress-text">Activación</span>
                        @if($convenio->estado === 'activo')
                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                        @elseif($convenio->estado === 'cancelado')
                            <span class="badge badge-danger"><i class="fas fa-ban"></i></span>
                        @elseif($convenio->estado === 'vencido')
                            <span class="badge badge-danger"><i class="fas fa-times"></i></span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-minus"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cancelar convenio -->
<div class="modal fade" id="cancelarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cancelarForm" method="POST" action="{{ route('convenios.cancelar', $convenio) }}">
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
                        <strong>¡Atención!</strong> Esta acción cancelará el convenio {{ $convenio->numero_convenio }} permanentemente.
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
function aprobarConvenio() {
    Swal.fire({
        title: '¿Aprobar convenio?',
        text: "El convenio {{ $convenio->numero_convenio }} pasará al estado 'Aprobado'",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ route("convenios.aprobar", $convenio) }}', {
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

function activarConvenio() {
    Swal.fire({
        title: '¿Activar convenio?',
        text: "El convenio {{ $convenio->numero_convenio }} pasará al estado 'Activo'",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ route("convenios.activar", $convenio) }}', {
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

function cancelarConvenio() {
    $('#cancelarModal').modal('show');
}

// Confirmación para eliminaciones
$('.btn-delete').click(function(e) {
    e.preventDefault();
    var form = $(this).closest('form');
    
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará permanentemente el convenio {{ $convenio->numero_convenio }}",
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
.badge-lg {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.progress-group .progress-text {
    font-weight: 500;
}

.card-body .d-grid .btn {
    margin-bottom: 0.5rem;
}

.table-borderless td {
    border: none;
    padding: 0.25rem 0.75rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.text-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.btn-block {
    width: 100%;
}

.card .card-header .card-title {
    font-weight: 500;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .col-md-4 {
        margin-top: 1rem;
    }
}
</style>
@endpush