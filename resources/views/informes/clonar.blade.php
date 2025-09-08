{{-- resources/views/informes/clonar.blade.php --}}
@extends('layouts.app')

@section('title', 'Clonar Informe')
@section('page-title', 'Clonar Informe #' . $informe->id)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('informes.index') }}">Informes</a></li>
<li class="breadcrumb-item"><a href="{{ route('informes.show', $informe) }}">Informe #{{ $informe->id }}</a></li>
<li class="breadcrumb-item active">Clonar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Información del Informe Original -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-copy mr-2"></i>
                    Información del Informe Original
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">ID:</td>
                                <td>#{{ $informe->id }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Institución:</td>
                                <td>{{ $informe->institucion_co_celebrante }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Unidad Académica:</td>
                                <td>{{ $informe->unidad_academica }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Carrera:</td>
                                <td>{{ $informe->carrera }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Estado:</td>
                                <td>
                                    <span class="badge badge-{{ $informe->estado_badge }}">
                                        {{ $informe->estado_texto }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Convenio Ejecutado:</td>
                                <td>
                                    @if($informe->convenio_ejecutado)
                                        <span class="text-success"><i class="fas fa-check mr-1"></i>Sí</span>
                                    @else
                                        <span class="text-warning"><i class="fas fa-times mr-1"></i>No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Fecha Presentación:</td>
                                <td>{{ $informe->fecha_presentacion->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Periodo Evaluado:</td>
                                <td>{{ Str::limit($informe->periodo_evaluado, 50) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Clonación -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-2"></i>
                    Configuración de Clonación
                </h3>
            </div>
            <form method="POST" action="{{ route('informes.procesar-clon', $informe) }}" id="clonarForm">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle mr-2"></i>¿Qué es la clonación?</h6>
                        <p class="mb-0">
                            La clonación crea un nuevo informe basado en el actual, permitiendo reutilizar 
                            la estructura y datos básicos mientras actualizas la información específica del 
                            nuevo periodo o convenio.
                        </p>
                    </div>

                    <!-- Opciones de Clonación -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                Selecciona qué información mantener
                            </h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="mantener_convenio" 
                                           name="mantener_convenio" 
                                           value="1" 
                                           checked>
                                    <label class="custom-control-label" for="mantener_convenio">
                                        <strong>Mantener Convenio Asociado</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Mantiene la referencia al mismo convenio y su información básica.
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="mantener_datos_institucionales" 
                                           name="mantener_datos_institucionales" 
                                           value="1" 
                                           checked>
                                    <label class="custom-control-label" for="mantener_datos_institucionales">
                                        <strong>Mantener Datos Institucionales</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Unidad académica, carrera y dependencia responsable.
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="mantener_coordinadores" 
                                           name="mantener_coordinadores" 
                                           value="1" 
                                           checked>
                                    <label class="custom-control-label" for="mantener_coordinadores">
                                        <strong>Mantener Coordinadores</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Lista de coordinadores designados en el convenio original.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="mantener_estructura_contenido" 
                                           name="mantener_estructura_contenido" 
                                           value="1">
                                    <label class="custom-control-label" for="mantener_estructura_contenido">
                                        <strong>Mantener Estructura de Contenido</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Mantiene las plantillas de logros, beneficios y dificultades como guía.
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="copiar_como_borrador" 
                                           name="copiar_como_borrador" 
                                           value="1" 
                                           checked>
                                    <label class="custom-control-label" for="copiar_como_borrador">
                                        <strong>Crear como Borrador</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    El nuevo informe se creará como borrador para permitir ediciones.
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Información Específica del Nuevo Informe -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-edit mr-2"></i>
                                Información específica del nuevo informe
                            </h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nuevo_periodo_evaluado">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Nuevo Periodo Evaluado <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('nuevo_periodo_evaluado') is-invalid @enderror" 
                                          id="nuevo_periodo_evaluado" 
                                          name="nuevo_periodo_evaluado" 
                                          rows="3" 
                                          required
                                          placeholder="Describe el nuevo periodo que se evaluará...">{{ old('nuevo_periodo_evaluado') }}</textarea>
                                @error('nuevo_periodo_evaluado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nueva_fecha_presentacion">
                                    <i class="fas fa-calendar-plus mr-1"></i>
                                    Nueva Fecha de Presentación <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('nueva_fecha_presentacion') is-invalid @enderror" 
                                       id="nueva_fecha_presentacion" 
                                       name="nueva_fecha_presentacion" 
                                       value="{{ old('nueva_fecha_presentacion', date('Y-m-d')) }}" 
                                       required>
                                @error('nueva_fecha_presentacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nuevo_nombre_identificador">
                                    <i class="fas fa-tag mr-1"></i>
                                    Identificador Interno (opcional)
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nuevo_nombre_identificador" 
                                       name="nuevo_nombre_identificador" 
                                       value="{{ old('nuevo_nombre_identificador') }}"
                                       maxlength="100"
                                       placeholder="Ej: Segundo semestre 2024, Evaluación trimestral...">
                                <small class="form-text text-muted">
                                    Nombre interno para identificar fácilmente este informe.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración Avanzada (Colapsable) -->
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h6 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>
                                Configuración Avanzada
                            </h6>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nuevo_periodo_desde">Periodo Desde (opcional)</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="nuevo_periodo_desde" 
                                               name="nuevo_periodo_desde" 
                                               value="{{ old('nuevo_periodo_desde') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nuevo_periodo_hasta">Periodo Hasta (opcional)</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="nuevo_periodo_hasta" 
                                               name="nuevo_periodo_hasta" 
                                               value="{{ old('nuevo_periodo_hasta') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones_clonacion">Observaciones de la Clonación</label>
                                        <textarea class="form-control" 
                                                  id="observaciones_clonacion" 
                                                  name="observaciones_clonacion" 
                                                  rows="2"
                                                  placeholder="Notas sobre por qué se clonó este informe...">{{ old('observaciones_clonacion') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de lo que se creará -->
                    <div class="alert alert-light border">
                        <h6 class="text-primary">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Resumen de la clonación
                        </h6>
                        <ul class="mb-0" id="resumen-clonacion">
                            <li>Se creará un nuevo informe basado en el informe #{{ $informe->id }}</li>
                            <li>El nuevo informe tendrá estado: <strong>Borrador</strong></li>
                            <li>Se mantendrán los datos seleccionados arriba</li>
                            <li>Se resetearán todos los datos de ejecución y evidencias</li>
                            <li>Podrás editarlo completamente antes de enviarlo</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('informes.show', $informe) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Volver al Informe
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" 
                                    class="btn btn-info mr-2" 
                                    onclick="previewClonacion()">
                                <i class="fas fa-eye mr-1"></i>
                                Vista Previa
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-copy mr-1"></i>
                                Clonar Informe
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Vista Previa -->
<div class="modal fade" id="modalVistaPrevia" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    Vista Previa de la Clonación
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="contenido-preview">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="$('#clonarForm').submit();">
                    <i class="fas fa-copy mr-1"></i>
                    Proceder con la Clonación
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validación de fechas
    $('#nuevo_periodo_hasta').change(function() {
        var fechaDesde = $('#nuevo_periodo_desde').val();
        var fechaHasta = $(this).val();
        
        if (fechaDesde && fechaHasta && fechaHasta < fechaDesde) {
            Swal.fire({
                icon: 'warning',
                title: 'Fechas incorrectas',
                text: 'La fecha de fin debe ser posterior a la fecha de inicio.'
            });
            $(this).val('');
        }
    });

    // Actualizar resumen dinámicamente
    $('input[type="checkbox"]').change(function() {
        actualizarResumen();
    });

    // Validación del formulario
    $('#clonarForm').submit(function(e) {
        var periodo = $('#nuevo_periodo_evaluado').val().trim();
        var fecha = $('#nueva_fecha_presentacion').val();
        
        if (!periodo || periodo.length < 10) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Periodo incompleto',
                text: 'Debe describir el nuevo periodo evaluado (mínimo 10 caracteres).'
            });
            $('#nuevo_periodo_evaluado').focus();
            return false;
        }

        if (!fecha) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Fecha requerida',
                text: 'Debe especificar la fecha de presentación del nuevo informe.'
            });
            $('#nueva_fecha_presentacion').focus();
            return false;
        }

        // Confirmación final
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar clonación?',
            text: "Se creará un nuevo informe basado en el actual",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, clonar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón para evitar doble envío
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Clonando...');
                this.submit();
            }
        });
    });

    function actualizarResumen() {
        var resumen = [];
        
        resumen.push('Se creará un nuevo informe basado en el informe #{{ $informe->id }}');
        resumen.push('El nuevo informe tendrá estado: <strong>Borrador</strong>');
        
        if ($('#mantener_convenio').is(':checked')) {
            resumen.push('Se mantendrá la referencia al convenio original');
        } else {
            resumen.push('<span class="text-warning">Se deberá seleccionar un nuevo convenio</span>');
        }
        
        if ($('#mantener_datos_institucionales').is(':checked')) {
            resumen.push('Se mantendrán unidad académica, carrera y dependencia');
        } else {
            resumen.push('<span class="text-warning">Se deberán completar nuevamente los datos institucionales</span>');
        }
        
        if ($('#mantener_coordinadores').is(':checked')) {
            resumen.push('Se mantendrá la lista de coordinadores');
        } else {
            resumen.push('<span class="text-warning">Se deberá especificar nuevos coordinadores</span>');
        }
        
        resumen.push('Se resetearán todos los datos de ejecución y evidencias');
        resumen.push('Podrás editarlo completamente antes de enviarlo');
        
        var html = '<ul class="mb-0">';
        resumen.forEach(function(item) {
            html += '<li>' + item + '</li>';
        });
        html += '</ul>';
        
        $('#resumen-clonacion').html(html);
    }
});

function previewClonacion() {
    var configuracion = {
        mantener_convenio: $('#mantener_convenio').is(':checked'),
        mantener_datos_institucionales: $('#mantener_datos_institucionales').is(':checked'),
        mantener_coordinadores: $('#mantener_coordinadores').is(':checked'),
        mantener_estructura_contenido: $('#mantener_estructura_contenido').is(':checked'),
        nuevo_periodo: $('#nuevo_periodo_evaluado').val(),
        nueva_fecha: $('#nueva_fecha_presentacion').val()
    };
    
    var html = '<div class="row">';
    html += '<div class="col-md-6">';
    html += '<h6 class="text-primary">Información Original</h6>';
    html += '<ul class="list-unstyled">';
    html += '<li><strong>ID:</strong> #{{ $informe->id }}</li>';
    html += '<li><strong>Institución:</strong> {{ $informe->institucion_co_celebrante }}</li>';
    html += '<li><strong>Periodo Original:</strong> {{ Str::limit($informe->periodo_evaluado, 50) }}</li>';
    html += '<li><strong>Fecha Original:</strong> {{ $informe->fecha_presentacion->format("d/m/Y") }}</li>';
    html += '</ul>';
    html += '</div>';
    
    html += '<div class="col-md-6">';
    html += '<h6 class="text-success">Nuevo Informe</h6>';
    html += '<ul class="list-unstyled">';
    html += '<li><strong>Estado:</strong> <span class="badge badge-secondary">Borrador</span></li>';
    html += '<li><strong>Convenio:</strong> ' + (configuracion.mantener_convenio ? 'Se mantiene' : '<span class="text-warning">Por definir</span>') + '</li>';
    html += '<li><strong>Nuevo Periodo:</strong> ' + (configuracion.nuevo_periodo || '<span class="text-danger">No especificado</span>') + '</li>';
    html += '<li><strong>Nueva Fecha:</strong> ' + (configuracion.nueva_fecha || '<span class="text-danger">No especificada</span>') + '</li>';
    html += '</ul>';
    html += '</div>';
    html += '</div>';
    
    html += '<hr>';
    html += '<h6 class="text-info">Configuración Seleccionada</h6>';
    html += '<div class="row">';
    html += '<div class="col-md-6">';
    html += '<ul class="list-unstyled">';
    html += '<li>' + (configuracion.mantener_convenio ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') + ' Mantener convenio</li>';
    html += '<li>' + (configuracion.mantener_datos_institucionales ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') + ' Mantener datos institucionales</li>';
    html += '</ul>';
    html += '</div>';
    html += '<div class="col-md-6">';
    html += '<ul class="list-unstyled">';
    html += '<li>' + (configuracion.mantener_coordinadores ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') + ' Mantener coordinadores</li>';
    html += '<li>' + (configuracion.mantener_estructura_contenido ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') + ' Mantener estructura de contenido</li>';
    html += '</ul>';
    html += '</div>';
    html += '</div>';
    
    $('#contenido-preview').html(html);
    $('#modalVistaPrevia').modal('show');
}
</script>
@endpush

@push('styles')
<style>
.custom-control-label {
    cursor: pointer;
}

.custom-control-label strong {
    color: #495057;
}

.form-text {
    font-size: 0.875rem;
}

.card-outline {
    border-top: 3px solid;
}

.alert-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush