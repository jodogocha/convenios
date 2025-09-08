{{-- resources/views/informes/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalles del Informe')
@section('page-title', 'Informe de Evaluación #' . $informe->id)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('informes.index') }}">Informes</a></li>
<li class="breadcrumb-item active">Informe #{{ $informe->id }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Información Principal del Informe -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="profile-informe-icon mb-3">
                        <i class="fas fa-file-alt fa-5x text-primary"></i>
                    </div>
                    <h3 class="profile-username text-center">Informe #{{ $informe->id }}</h3>
                    <p class="text-muted text-center">
                        {{ $informe->institucion_co_celebrante }}
                    </p>
                    
                    <div class="text-center mb-3">
                        <span class="badge badge-{{ $informe->estado_badge }} badge-lg">
                            <i class="fas fa-{{ $informe->estado === 'borrador' ? 'edit' : ($informe->estado === 'enviado' ? 'paper-plane' : ($informe->estado === 'aprobado' ? 'check-circle' : 'times-circle')) }} mr-1"></i>
                            {{ $informe->estado_texto }}
                        </span>
                    </div>

                    @if($informe->convenio_ejecutado)
                        <div class="alert alert-success alert-sm">
                            <i class="fas fa-check-circle mr-1"></i>
                            Convenio Ejecutado
                        </div>
                    @else
                        <div class="alert alert-warning alert-sm">
                            <i class="fas fa-times-circle mr-1"></i>
                            Convenio No Ejecutado
                        </div>
                    @endif
                </div>

                <hr>

                <strong><i class="fas fa-handshake mr-1"></i> Convenio Asociado</strong>
                <p class="text-muted mb-2">
                    @if($informe->convenio)
                        <a href="{{ route('convenios.show', $informe->convenio) }}" class="text-primary">
                            {{ $informe->convenio->numero_convenio }}
                        </a>
                        <br>
                        <small class="text-muted">{{ $informe->convenio->institucion_contraparte }}</small>
                    @else
                        <span class="text-danger">Convenio eliminado</span>
                    @endif
                </p>

                <strong><i class="fas fa-university mr-1"></i> Unidad Académica</strong>
                <p class="text-muted mb-2">
                    {{ $informe->unidad_academica }}
                    <br>
                    <small class="text-muted">{{ $informe->carrera }}</small>
                </p>

                <strong><i class="fas fa-calendar mr-1"></i> Periodo Evaluado</strong>
                <p class="text-muted mb-2">
                    {{ $informe->periodo_completo }}
                </p>

                <strong><i class="fas fa-user mr-1"></i> Creado por</strong>
                <p class="text-muted mb-2">
                    {{ $informe->usuarioCreador ? $informe->usuarioCreador->nombre_completo : 'Usuario eliminado' }}
                    <br>
                    <small class="text-muted">{{ $informe->created_at->format('d/m/Y H:i:s') }}</small>
                </p>

                @if($informe->usuarioRevisor)
                <strong><i class="fas fa-user-check mr-1"></i> Revisado por</strong>
                <p class="text-muted mb-2">
                    {{ $informe->usuarioRevisor->nombre_completo }}
                    <br>
                    <small class="text-muted">{{ $informe->fecha_revision->format('d/m/Y H:i:s') }}</small>
                </p>
                @endif

                <hr>

                <!-- Acciones -->
                <div class="d-grid gap-2">
                    @if($informe->puedeSerEditado() && ($informe->usuario_creador_id === Auth::id() || Auth::user()->tieneRol('super_admin')))
                    <a href="{{ route('informes.edit', $informe) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit mr-1"></i>
                        Editar Informe
                    </a>
                    @endif

                    @if($informe->estado === 'borrador' && $informe->puedeSerEnviado())
                    <form method="POST" action="{{ route('informes.enviar', $informe) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block btn-enviar">
                            <i class="fas fa-paper-plane mr-1"></i>
                            Enviar para Revisión
                        </button>
                    </form>
                    @endif

                    @if(Auth::user()->tienePermiso('informes.aprobar') && $informe->estado === 'enviado')
                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modalAprobar">
                        <i class="fas fa-check-circle mr-1"></i>
                        Aprobar Informe
                    </button>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#modalRechazar">
                        <i class="fas fa-times-circle mr-1"></i>
                        Rechazar Informe
                    </button>
                    @endif

                    <a href="{{ route('informes.exportar-pdf', $informe) }}" class="btn btn-danger btn-block">
                        <i class="fas fa-file-pdf mr-1"></i>
                        Exportar PDF
                    </a>

                    <a href="{{ route('informes.duplicar', $informe) }}" class="btn btn-info btn-block">
                        <i class="fas fa-copy mr-1"></i>
                        Duplicar Informe
                    </a>

                    <a href="{{ route('informes.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Lista
                    </a>

                    @if($informe->estado === 'borrador' && ($informe->usuario_creador_id === Auth::id() || Auth::user()->tieneRol('super_admin')))
                    <form method="POST" action="{{ route('informes.destroy', $informe) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block btn-delete">
                            <i class="fas fa-trash mr-1"></i>
                            Eliminar Informe
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Detallado del Informe -->
    <div class="col-md-8">
        <!-- Información del Convenio -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-handshake mr-2"></i>
                    Información del Convenio
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Convenio:</td>
                                <td>
                                    @if($informe->convenio)
                                        <a href="{{ route('convenios.show', $informe->convenio) }}" class="text-primary">
                                            {{ $informe->convenio->numero_convenio }}
                                        </a>
                                    @else
                                        <span class="text-danger">Convenio eliminado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Institución:</td>
                                <td>{{ $informe->institucion_co_celebrante }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Fecha Celebración:</td>
                                <td>{{ $informe->fecha_celebracion->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Vigencia:</td>
                                <td>{{ $informe->vigencia ?? 'No especificada' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Unidad Académica:</td>
                                <td>{{ $informe->unidad_academica }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Carrera:</td>
                                <td>{{ $informe->carrera }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Tipo Convenio:</td>
                                <td>
                                    <span class="badge badge-info">{{ $informe->tipo_convenio }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Fecha Presentación:</td>
                                <td>{{ $informe->fecha_presentacion->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Periodo Evaluado -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Periodo Evaluado
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary">Descripción del Periodo</h6>
                        <p class="text-muted">{{ $informe->periodo_evaluado }}</p>
                    </div>
                </div>
                @if($informe->periodo_desde || $informe->periodo_hasta)
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Fecha de Inicio</h6>
                        <p class="text-muted">
                            {{ $informe->periodo_desde ? $informe->periodo_desde->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Fecha de Fin</h6>
                        <p class="text-muted">
                            {{ $informe->periodo_hasta ? $informe->periodo_hasta->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Responsables -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Responsables y Coordinadores
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Dependencia Responsable</h6>
                        <p class="text-muted">{{ $informe->dependencia_responsable }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Convenio Celebrado a Propuesta de</h6>
                        <p class="text-muted">{{ $informe->convenio_celebrado_propuesta }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary">Coordinadores Designados</h6>
                        @if($informe->coordinadores_designados && count($informe->coordinadores_designados) > 0)
                            <ul class="list-unstyled">
                                @foreach($informe->coordinadores_designados as $coordinador)
                                    @if(!empty($coordinador))
                                    <li><i class="fas fa-user-tie mr-2"></i>{{ $coordinador }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No especificados</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Ejecución del Convenio -->
        @if($informe->convenio_ejecutado)
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-check-circle mr-2"></i>
                    Ejecución del Convenio - EJECUTADO
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Número de Actividades Realizadas</h6>
                        <p class="text-muted">
                            <span class="badge badge-success badge-lg">
                                {{ $informe->numero_actividades_realizadas ?? 0 }} actividades
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Logros Obtenidos</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->logros_obtenidos ?? 'No especificados' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Beneficios Alcanzados</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->beneficios_alcanzados ?? 'No especificados' }}
                        </div>
                    </div>
                </div>

                @if($informe->dificultades_incidentes || $informe->responsabilidad_instalaciones)
                <hr>
                <div class="row">
                    @if($informe->dificultades_incidentes)
                    <div class="col-md-6">
                        <h6 class="text-warning">Dificultades e Incidentes</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->dificultades_incidentes }}
                        </div>
                    </div>
                    @endif
                    
                    @if($informe->responsabilidad_instalaciones)
                    <div class="col-md-6">
                        <h6 class="text-info">Responsabilidad con Instalaciones</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->responsabilidad_instalaciones }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                @if($informe->sugerencias_mejoras || $informe->informacion_complementaria)
                <hr>
                <div class="row">
                    @if($informe->sugerencias_mejoras)
                    <div class="col-md-6">
                        <h6 class="text-primary">Sugerencias de Mejoras</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->sugerencias_mejoras }}
                        </div>
                    </div>
                    @endif
                    
                    @if($informe->informacion_complementaria)
                    <div class="col-md-6">
                        <h6 class="text-primary">Información Complementaria</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->informacion_complementaria }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-times-circle mr-2"></i>
                    Ejecución del Convenio - NO EJECUTADO
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-danger">Motivos de No Ejecución</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->motivos_no_ejecucion ?? 'No especificados' }}
                        </div>
                    </div>
                </div>

                @if($informe->propuestas_mejoras || $informe->informacion_complementaria_no_ejecutado)
                <hr>
                <div class="row">
                    @if($informe->propuestas_mejoras)
                    <div class="col-md-6">
                        <h6 class="text-primary">Propuestas de Mejoras</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->propuestas_mejoras }}
                        </div>
                    </div>
                    @endif
                    
                    @if($informe->informacion_complementaria_no_ejecutado)
                    <div class="col-md-6">
                        <h6 class="text-primary">Información Complementaria</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->informacion_complementaria_no_ejecutado }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Evidencias -->
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-paperclip mr-2"></i>
                    Evidencias y Documentación
                </h3>
            </div>
            <div class="card-body">
                @if($informe->enlace_google_drive)
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary">Enlace a Google Drive</h6>
                        <p>
                            <a href="{{ $informe->enlace_google_drive }}" target="_blank" class="btn btn-primary">
                                <i class="fab fa-google-drive mr-1"></i>
                                Ver Evidencias en Google Drive
                            </a>
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Este enlace contiene fotos, videos, documentos y otros materiales de evidencia del convenio.
                        </small>
                    </div>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="fas fa-paperclip fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No se han proporcionado evidencias</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Firmas y Observaciones -->
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-signature mr-2"></i>
                    Firmas y Observaciones
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Firmas</h6>
                        @if($informe->firmas && count($informe->firmas) > 0)
                            <ul class="list-unstyled">
                                @foreach($informe->firmas as $firma)
                                    @if(!empty($firma))
                                    <li><i class="fas fa-pen mr-2"></i>{{ $firma }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Sin firmas registradas</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Fecha de Presentación</h6>
                        <p class="text-muted">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $informe->fecha_presentacion->format('d/m/Y') }}
                            <br>
                            <small>({{ $informe->fecha_presentacion->diffForHumans() }})</small>
                        </p>
                    </div>
                </div>

                @if($informe->observaciones)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary">Observaciones</h6>
                        <div class="border p-3 bg-light">
                            {{ $informe->observaciones }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprobar -->
@if(Auth::user()->tienePermiso('informes.aprobar') && $informe->estado === 'enviado')
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
            <form method="POST" action="{{ route('informes.aprobar', $informe) }}">
                @csrf
                <div class="modal-body">
                    <p>¿Está seguro de que desea aprobar este informe?</p>
                    <p class="text-muted">
                        <strong>Informe:</strong> #{{ $informe->id }} - {{ $informe->institucion_co_celebrante }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
            <form method="POST" action="{{ route('informes.rechazar', $informe) }}">
                @csrf
                <div class="modal-body">
                    <p>¿Está seguro de que desea rechazar este informe?</p>
                    <p class="text-muted">
                        <strong>Informe:</strong> #{{ $informe->id }} - {{ $informe->institucion_co_celebrante }}
                    </p>
                    <div class="form-group">
                        <label for="motivo_rechazo">
                            Motivo del Rechazo <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="motivo_rechazo" 
                                  name="motivo_rechazo" 
                                  rows="3" 
                                  required
                                  placeholder="Explique los motivos del rechazo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>
                        Rechazar Informe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.profile-informe-icon {
    border: 0;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Confirmación para eliminaciones
    $('.btn-delete').click(function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer",
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

    // Confirmación para envío
    $('.btn-enviar').click(function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        
        Swal.fire({
            title: 'Enviar informe',
            text: "Una vez enviado, no podrá editarlo hasta que sea revisado",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush