{{-- resources/views/convenios/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Convenio')
@section('page-title', 'Editar Convenio')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('convenios.index') }}">Convenios</a></li>
<li class="breadcrumb-item"><a href="{{ route('convenios.show', $convenio) }}">{{ $convenio->numero_convenio }}</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <form method="POST" action="{{ route('convenios.update', $convenio) }}" enctype="multipart/form-data" id="convenioForm">
            @csrf
            @method('PUT')
            
            <!-- Información del Convenio -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Convenio: {{ $convenio->numero_convenio }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $convenio->estado_badge }} badge-lg">
                            {{ $convenio->estado_texto }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if(!$convenio->puedeSerEditado())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Atención:</strong> Este convenio está en estado "{{ $convenio->estado_texto }}" y solo puede editarse en modo de consulta.
                        </div>
                    @endif

                    <div class="row">
                        <!-- Institución Contraparte -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="institucion_contraparte" class="form-label">
                                    <i class="fas fa-university mr-1"></i>
                                    Institución Contraparte <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('institucion_contraparte') is-invalid @enderror" 
                                       id="institucion_contraparte" 
                                       name="institucion_contraparte" 
                                       value="{{ old('institucion_contraparte', $convenio->institucion_contraparte) }}" 
                                       required
                                       placeholder="Ej: Universidad Nacional de Asunción">
                                @error('institucion_contraparte')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tipo de Convenio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_convenio" class="form-label">
                                    <i class="fas fa-tag mr-1"></i>
                                    Tipo de Convenio <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('tipo_convenio') is-invalid @enderror" 
                                        id="tipo_convenio" 
                                        name="tipo_convenio" 
                                        required>
                                    <option value="">Seleccione un tipo...</option>
                                    @foreach($tipos as $key => $tipo)
                                        <option value="{{ $key }}" 
                                                {{ old('tipo_convenio', $convenio->tipo_convenio) == $key ? 'selected' : '' }}>
                                            {{ $tipo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_convenio')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Objeto del Convenio -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="objeto" class="form-label">
                                    <i class="fas fa-clipboard mr-1"></i>
                                    Objeto del Convenio <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('objeto') is-invalid @enderror" 
                                          id="objeto" 
                                          name="objeto" 
                                          rows="4" 
                                          required
                                          placeholder="Describa el objeto o propósito del convenio...">{{ old('objeto', $convenio->objeto) }}</textarea>
                                @error('objeto')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha de Firma -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_firma" class="form-label">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Fecha de Firma <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_firma') is-invalid @enderror" 
                                       id="fecha_firma" 
                                       name="fecha_firma" 
                                       value="{{ old('fecha_firma', $convenio->fecha_firma->format('Y-m-d')) }}" 
                                       required
                                       max="{{ date('Y-m-d') }}">
                                @error('fecha_firma')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Vencimiento -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_vencimiento" class="form-label">
                                    <i class="fas fa-calendar-times mr-1"></i>
                                    Vencimiento
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                       id="fecha_vencimiento" 
                                       name="fecha_vencimiento" 
                                       value="{{ old('fecha_vencimiento', $convenio->fecha_vencimiento ? $convenio->fecha_vencimiento->format('Y-m-d') : '') }}"
                                       {{ $convenio->vigencia_indefinida ? 'disabled' : '' }}>
                                @error('fecha_vencimiento')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Vigencia Indefinida -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="vigencia_indefinida" 
                                               name="vigencia_indefinida" 
                                               value="1"
                                               {{ old('vigencia_indefinida', $convenio->vigencia_indefinida) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="vigencia_indefinida">
                                            <i class="fas fa-infinity mr-1"></i>
                                            Vigencia Indefinida
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coordinación y Responsables -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        Coordinación y Responsables
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Coordinador del Convenio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="coordinador_convenio" class="form-label">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    Coordinador/a del Convenio <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('coordinador_convenio') is-invalid @enderror" 
                                        id="coordinador_convenio" 
                                        name="coordinador_convenio" 
                                        required>
                                    <option value="">Seleccione un coordinador...</option>
                                    @foreach($coordinadores as $key => $coordinador)
                                        <option value="{{ $key }}" 
                                                {{ old('coordinador_convenio', $convenio->coordinador_convenio) == $key ? 'selected' : '' }}>
                                            {{ $coordinador }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('coordinador_convenio')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- País/Región -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pais_region" class="form-label">
                                    <i class="fas fa-globe mr-1"></i>
                                    País/Región <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('pais_region') is-invalid @enderror" 
                                       id="pais_region" 
                                       name="pais_region" 
                                       value="{{ old('pais_region', $convenio->pais_region) }}" 
                                       required
                                       placeholder="Ej: Paraguay, Argentina, Brasil">
                                @error('pais_region')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Usuario Coordinador (Opcional) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario_coordinador_id" class="form-label">
                                    <i class="fas fa-user-cog mr-1"></i>
                                    Usuario Coordinador (Opcional)
                                </label>
                                <select class="form-control @error('usuario_coordinador_id') is-invalid @enderror" 
                                        id="usuario_coordinador_id" 
                                        name="usuario_coordinador_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}" 
                                                {{ old('usuario_coordinador_id', $convenio->usuario_coordinador_id) == $usuario->id ? 'selected' : '' }}>
                                            {{ $usuario->nombre_completo }} ({{ $usuario->username }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('usuario_coordinador_id')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">
                                    Usuario del sistema que coordinará este convenio.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Signatarios -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-signature mr-1"></i>
                                    Signatarios <span class="text-danger">*</span>
                                </label>
                                <div id="signatarios-container">
                                    @php 
                                        $signatarios = old('signatarios', $convenio->signatarios ?: []);
                                        if (empty($signatarios)) $signatarios = [''];
                                    @endphp
                                    @foreach($signatarios as $index => $signatario)
                                        <div class="input-group mb-2 signatario-item">
                                            <input type="text" 
                                                   class="form-control @error('signatarios.'.$index) is-invalid @enderror" 
                                                   name="signatarios[]" 
                                                   value="{{ $signatario }}" 
                                                   placeholder="Nombre completo del signatario">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger btn-remove-signatario">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @error('signatarios.'.$index)
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-signatario">
                                    <i class="fas fa-plus mr-1"></i>Agregar Signatario
                                </button>
                                @error('signatarios')
                                    <div class="text-danger mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentación -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Documentación
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Archivo Actual -->
                    @if($convenio->archivo_convenio_path)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-file-pdf mr-2"></i>Archivo Actual</h6>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                                    <div>
                                        <strong>{{ $convenio->archivo_convenio_nombre }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $convenio->archivo_peso_formateado }}</small>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="{{ route('convenios.descargar', $convenio) }}" 
                                           class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-download mr-1"></i>Descargar
                                        </a>
                                        <a href="{{ $convenio->archivo_url }}" 
                                           target="_blank" 
                                           class="btn btn-secondary btn-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i>Ver
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <!-- Nuevo Archivo del Convenio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archivo_convenio" class="form-label">
                                    <i class="fas fa-upload mr-1"></i>
                                    {{ $convenio->archivo_convenio_path ? 'Reemplazar Archivo (PDF)' : 'Adjuntar Convenio Firmado (PDF)' }}
                                </label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input @error('archivo_convenio') is-invalid @enderror" 
                                           id="archivo_convenio" 
                                           name="archivo_convenio" 
                                           accept=".pdf">
                                    <label class="custom-file-label" for="archivo_convenio">
                                        Seleccionar archivo PDF...
                                    </label>
                                </div>
                                @error('archivo_convenio')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ $convenio->archivo_convenio_path ? 'Deje vacío para mantener el archivo actual.' : 'Archivo PDF escaneado del convenio firmado.' }} Máximo 10MB.
                                </small>
                            </div>
                        </div>

                        <!-- Número de Dictamen -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dictamen_numero" class="form-label">
                                    <i class="fas fa-hashtag mr-1"></i>
                                    Dictamen N°
                                </label>
                                <input type="text" 
                                       class="form-control @error('dictamen_numero') is-invalid @enderror" 
                                       id="dictamen_numero" 
                                       name="dictamen_numero" 
                                       value="{{ old('dictamen_numero', $convenio->dictamen_numero) }}" 
                                       placeholder="Ej: DAJ-001/2025">
                                @error('dictamen_numero')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Versión Final y Observaciones -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="version_final_firmada" 
                                           name="version_final_firmada" 
                                           value="1"
                                           {{ old('version_final_firmada', $convenio->version_final_firmada) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="version_final_firmada">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Versión Final Firmada
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Marque si este es el documento final firmado por ambas partes.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones" class="form-label">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    Observaciones
                                </label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" 
                                          name="observaciones" 
                                          rows="3" 
                                          placeholder="Observaciones adicionales sobre el convenio...">{{ old('observaciones', $convenio->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Información de Modificación
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb mr-2"></i>Información sobre la Edición</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><strong>Creado:</strong> {{ $convenio->created_at->format('d/m/Y H:i:s') }} por {{ $convenio->usuarioCreador ? $convenio->usuarioCreador->nombre_completo : 'Usuario eliminado' }}</li>
                                    <li><strong>Última modificación:</strong> {{ $convenio->updated_at->format('d/m/Y H:i:s') }}</li>
                                    @if($convenio->fecha_aprobacion)
                                    <li><strong>Aprobado:</strong> {{ $convenio->fecha_aprobacion->format('d/m/Y H:i:s') }} por {{ $convenio->usuarioAprobador ? $convenio->usuarioAprobador->nombre_completo : 'Usuario eliminado' }}</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><strong>Número:</strong> {{ $convenio->numero_convenio }}</li>
                                    <li><strong>Estado actual:</strong> <span class="badge badge-{{ $convenio->estado_badge }}">{{ $convenio->estado_texto }}</span></li>
                                    @if($convenio->version_final_firmada && $convenio->dictamen_numero)
                                        <li><strong>Cambio de estado:</strong> Al guardar pasará a "Pendiente de Aprobación"</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card">
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('convenios.show', $convenio) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Volver
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('convenios.index') }}" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-list mr-1"></i>
                                Ver Lista
                            </a>
                            <button type="reset" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i>
                                Restaurar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i>
                                Actualizar Convenio
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Manejo de archivo personalizado
    $('#archivo_convenio').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Seleccionar archivo PDF...');
        
        // Validar tamaño del archivo
        if (this.files[0]) {
            var fileSize = this.files[0].size / 1024 / 1024; // MB
            if (fileSize > 10) {
                alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
                $(this).val('');
                $(this).next('.custom-file-label').html('Seleccionar archivo PDF...');
            }
        }
    });

    // Manejo de vigencia indefinida
    $('#vigencia_indefinida').change(function() {
        if ($(this).is(':checked')) {
            $('#fecha_vencimiento').val('').prop('disabled', true);
        } else {
            $('#fecha_vencimiento').prop('disabled', false);
        }
    });

    // Agregar signatario
    $('#btn-add-signatario').click(function() {
        var newSignatario = `
            <div class="input-group mb-2 signatario-item">
                <input type="text" 
                       class="form-control" 
                       name="signatarios[]" 
                       placeholder="Nombre completo del signatario">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger btn-remove-signatario">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        $('#signatarios-container').append(newSignatario);
    });

    // Remover signatario
    $(document).on('click', '.btn-remove-signatario', function() {
        if ($('.signatario-item').length > 1) {
            $(this).closest('.signatario-item').remove();
        } else {
            alert('Debe mantener al menos un signatario.');
        }
    });

    // Validación de fechas
    $('#fecha_firma').change(function() {
        var fechaFirma = $(this).val();
        $('#fecha_vencimiento').attr('min', fechaFirma);
    });

    // Validación del formulario
    $('#convenioForm').on('submit', function(e) {
        var signatarios = $('input[name="signatarios[]"]').filter(function() {
            return $(this).val().trim() !== '';
        });
        
        if (signatarios.length === 0) {
            e.preventDefault();
            alert('Debe especificar al menos un signatario.');
            $('input[name="signatarios[]"]').first().focus();
            return false;
        }

        // Validar vigencia
        var vigenciaIndefinida = $('#vigencia_indefinida').is(':checked');
        var fechaVencimiento = $('#fecha_vencimiento').val();
        
        if (!vigenciaIndefinida && !fechaVencimiento) {
            e.preventDefault();
            alert('Debe especificar una fecha de vencimiento o marcar "Vigencia Indefinida".');
            $('#fecha_vencimiento').focus();
            return false;
        }

        // Confirmar si hay cambios importantes
        if ($('#version_final_firmada').is(':checked')) {
            var dictamen = $('#dictamen_numero').val();
            if (!dictamen) {
                e.preventDefault();
                alert('Si marca "Versión Final Firmada", debe proporcionar el número de dictamen.');
                $('#dictamen_numero').focus();
                return false;
            }
        }
    });

    // Auto-focus en primer campo con error
    if ($('.is-invalid').length > 0) {
        $('.is-invalid').first().focus();
    }

    // Inicializar estado de vigencia indefinida
    if ($('#vigencia_indefinida').is(':checked')) {
        $('#fecha_vencimiento').prop('disabled', true);
    }

    // Inicializar fecha mínima de vencimiento
    var fechaFirma = $('#fecha_firma').val();
    if (fechaFirma) {
        $('#fecha_vencimiento').attr('min', fechaFirma);
    }
});
</script>
@endpush

@push('styles')
<style>
.signatario-item {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.custom-file-label::after {
    content: "Buscar";
}

.badge-lg {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.alert ul {
    padding-left: 1.2rem;
}

.alert li {
    margin-bottom: 0.5rem;
}

.card-header .card-title {
    font-weight: 500;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 0.5rem;
}

.input-group .btn-outline-danger:hover {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

.alert .d-flex {
    align-items: center;
}

.alert .ml-auto {
    margin-left: auto !important;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
    }
    
    .d-flex.justify-content-between > div {
        margin-bottom: 0.5rem;
        width: 100%;
    }
    
    .btn-block {
        width: 100% !important;
    }
}
</style>
@endpush