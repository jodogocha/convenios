{{-- resources/views/informes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Crear Informe')
@section('page-title', 'Crear Nuevo Informe')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('informes.index') }}">Informes</a></li>
<li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <form method="POST" action="{{ route('informes.store') }}" id="informeForm">
            @csrf
            
            <!-- Información Básica del Convenio -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-handshake mr-2"></i>
                        Información del Convenio
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Selección de Convenio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="convenio_id">
                                    <i class="fas fa-handshake mr-1"></i>
                                    Convenio <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('convenio_id') is-invalid @enderror" 
                                        id="convenio_id" 
                                        name="convenio_id" 
                                        required>
                                    <option value="">Seleccionar convenio...</option>
                                    @foreach($convenios as $convenio)
                                        <option value="{{ $convenio->id }}" 
                                                {{ old('convenio_id', $convenioSeleccionado?->id) == $convenio->id ? 'selected' : '' }}
                                                data-institucion="{{ $convenio->institucion_contraparte }}"
                                                data-fecha="{{ $convenio->fecha_firma->format('Y-m-d') }}"
                                                data-tipo="{{ $convenio->tipo_convenio }}"
                                                data-coordinador="{{ $convenio->coordinador_convenio }}"
                                                data-vigencia="{{ $convenio->vigencia_texto }}">
                                            {{ $convenio->numero_convenio }} - {{ $convenio->institucion_contraparte }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('convenio_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Institución Co-celebrante -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="institucion_co_celebrante">
                                    <i class="fas fa-building mr-1"></i>
                                    Institución Co-celebrante <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('institucion_co_celebrante') is-invalid @enderror" 
                                       id="institucion_co_celebrante" 
                                       name="institucion_co_celebrante" 
                                       value="{{ old('institucion_co_celebrante', $convenioSeleccionado?->institucion_contraparte) }}" 
                                       required
                                       maxlength="255">
                                @error('institucion_co_celebrante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Unidad Académica -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unidad_academica">
                                    <i class="fas fa-university mr-1"></i>
                                    Unidad Académica <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('unidad_academica') is-invalid @enderror" 
                                        id="unidad_academica" 
                                        name="unidad_academica" 
                                        required>
                                    <option value="">Seleccionar unidad académica...</option>
                                    @foreach($unidadesAcademicas as $valor => $nombre)
                                        <option value="{{ $valor }}" {{ old('unidad_academica') == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unidad_academica')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Carrera -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="carrera">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Carrera <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('carrera') is-invalid @enderror" 
                                       id="carrera" 
                                       name="carrera" 
                                       value="{{ old('carrera') }}" 
                                       required
                                       maxlength="255"
                                       placeholder="Ej: Ingeniería en Sistemas">
                                @error('carrera')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha de Celebración -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_celebracion">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Fecha de Celebración <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_celebracion') is-invalid @enderror" 
                                       id="fecha_celebracion" 
                                       name="fecha_celebracion" 
                                       value="{{ old('fecha_celebracion', $convenioSeleccionado?->fecha_firma->format('Y-m-d')) }}" 
                                       required>
                                @error('fecha_celebracion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Vigencia -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="vigencia">
                                    <i class="fas fa-clock mr-1"></i>
                                    Vigencia
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="vigencia" 
                                       name="vigencia" 
                                       value="{{ old('vigencia', $convenioSeleccionado?->vigencia_texto) }}" 
                                       maxlength="100"
                                       placeholder="Ej: Indefinida, 2 años, etc.">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Periodo Evaluado -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Periodo Evaluado
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Descripción del Periodo -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="periodo_evaluado">
                                    <i class="fas fa-calendar-check mr-1"></i>
                                    Descripción del Periodo Evaluado <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('periodo_evaluado') is-invalid @enderror" 
                                          id="periodo_evaluado" 
                                          name="periodo_evaluado" 
                                          rows="3" 
                                          required
                                          maxlength="500"
                                          placeholder="Describe el periodo específico que se está evaluando...">{{ old('periodo_evaluado') }}</textarea>
                                @error('periodo_evaluado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha Desde -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="periodo_desde">
                                    <i class="fas fa-play mr-1"></i>
                                    Fecha de Inicio del Periodo
                                </label>
                                <input type="date" 
                                       class="form-control @error('periodo_desde') is-invalid @enderror" 
                                       id="periodo_desde" 
                                       name="periodo_desde" 
                                       value="{{ old('periodo_desde') }}">
                                @error('periodo_desde')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fecha Hasta -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="periodo_hasta">
                                    <i class="fas fa-stop mr-1"></i>
                                    Fecha de Fin del Periodo
                                </label>
                                <input type="date" 
                                       class="form-control @error('periodo_hasta') is-invalid @enderror" 
                                       id="periodo_hasta" 
                                       name="periodo_hasta" 
                                       value="{{ old('periodo_hasta') }}">
                                @error('periodo_hasta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsables y Coordinadores -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        Responsables y Coordinadores
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Dependencia Responsable -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dependencia_responsable">
                                    <i class="fas fa-building mr-1"></i>
                                    Dependencia Responsable de la Actividad <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('dependencia_responsable') is-invalid @enderror" 
                                       id="dependencia_responsable" 
                                       name="dependencia_responsable" 
                                       value="{{ old('dependencia_responsable') }}" 
                                       required
                                       maxlength="255"
                                       placeholder="Ej: Facultad de Ingeniería">
                                @error('dependencia_responsable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Propuesta de Convenio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="convenio_celebrado_propuesta">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    Convenio Celebrado a Propuesta de <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('convenio_celebrado_propuesta') is-invalid @enderror" 
                                       id="convenio_celebrado_propuesta" 
                                       name="convenio_celebrado_propuesta" 
                                       value="{{ old('convenio_celebrado_propuesta') }}" 
                                       required
                                       maxlength="500"
                                       placeholder="Ej: Universidad Nacional de Itapúa">
                                @error('convenio_celebrado_propuesta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Coordinadores Designados -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="coordinadores">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    Coordinador/es Designados en el Convenio <span class="text-danger">*</span>
                                </label>
                                <div id="coordinadores-container">
                                    @php
                                        $coordinadores = old('coordinadores_designados', ['']);
                                        if (empty($coordinadores) || (count($coordinadores) == 1 && empty($coordinadores[0]))) {
                                            $coordinadores = [''];
                                        }
                                    @endphp
                                    @foreach($coordinadores as $index => $coordinador)
                                    <div class="input-group mb-2 coordinador-group">
                                        <input type="text" 
                                               class="form-control" 
                                               name="coordinadores_designados[]" 
                                               value="{{ $coordinador }}" 
                                               placeholder="Nombre completo del coordinador"
                                               maxlength="255">
                                        <div class="input-group-append">
                                            @if($index == 0)
                                            <button type="button" class="btn btn-success" id="add-coordinador">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-danger remove-coordinador">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('coordinadores_designados')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('coordinadores_designados.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Convenio -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tipo_convenio">
                                    <i class="fas fa-tags mr-1"></i>
                                    Tipo de Convenio <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('tipo_convenio') is-invalid @enderror" 
                                        id="tipo_convenio" 
                                        name="tipo_convenio" 
                                        required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tiposConvenio as $valor => $nombre)
                                        <option value="{{ $valor }}" {{ old('tipo_convenio', $convenioSeleccionado?->tipo_convenio == 'Marco' ? 'Marco' : 'Específico') == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_convenio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado de Ejecución del Convenio -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle mr-2"></i>
                        Ejecución del Convenio
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Checkbox de ejecución -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="convenio_ejecutado" 
                                           name="convenio_ejecutado" 
                                           value="1"
                                           {{ old('convenio_ejecutado', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="convenio_ejecutado">
                                        <strong>El convenio se ha ejecutado</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Marque esta opción si el convenio se ejecutó durante el periodo evaluado.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: El convenio SE HA ejecutado -->
                    <div id="seccion-ejecutado" style="{{ old('convenio_ejecutado', true) ? '' : 'display: none;' }}">
                        <h5 class="text-success">
                            <i class="fas fa-check mr-2"></i>
                            El convenio se ha ejecutado
                        </h5>
                        <hr>

                        <div class="row">
                            <!-- Número de Actividades -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_actividades_realizadas">
                                        <i class="fas fa-list-ol mr-1"></i>
                                        Número de Actividades Realizadas y/o Proyectos Ejecutados <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('numero_actividades_realizadas') is-invalid @enderror" 
                                           id="numero_actividades_realizadas" 
                                           name="numero_actividades_realizadas" 
                                           value="{{ old('numero_actividades_realizadas') }}" 
                                           min="0"
                                           step="1">
                                    @error('numero_actividades_realizadas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Logros Obtenidos -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logros_obtenidos">
                                        <i class="fas fa-trophy mr-1"></i>
                                        Logros Obtenidos <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('logros_obtenidos') is-invalid @enderror" 
                                              id="logros_obtenidos" 
                                              name="logros_obtenidos" 
                                              rows="4"
                                              placeholder="Describa los principales logros obtenidos...">{{ old('logros_obtenidos') }}</textarea>
                                    @error('logros_obtenidos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Beneficios Alcanzados -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="beneficios_alcanzados">
                                        <i class="fas fa-heart mr-1"></i>
                                        Beneficios Alcanzados <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('beneficios_alcanzados') is-invalid @enderror" 
                                              id="beneficios_alcanzados" 
                                              name="beneficios_alcanzados" 
                                              rows="4"
                                              placeholder="Describa los beneficios alcanzados...">{{ old('beneficios_alcanzados') }}</textarea>
                                    @error('beneficios_alcanzados')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Dificultades -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dificultades_incidentes">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Dificultades y/o Incidentes Durante la Ejecución
                                    </label>
                                    <textarea class="form-control" 
                                              id="dificultades_incidentes" 
                                              name="dificultades_incidentes" 
                                              rows="4"
                                              placeholder="Describa las dificultades encontradas...">{{ old('dificultades_incidentes') }}</textarea>
                                </div>
                            </div>

                            <!-- Responsabilidad con Instalaciones -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="responsabilidad_instalaciones">
                                        <i class="fas fa-building mr-1"></i>
                                        Responsabilidad con Relación al Uso de Instalaciones
                                    </label>
                                    <textarea class="form-control" 
                                              id="responsabilidad_instalaciones" 
                                              name="responsabilidad_instalaciones" 
                                              rows="4"
                                              placeholder="Describa el uso de instalaciones...">{{ old('responsabilidad_instalaciones') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Sugerencias de Mejoras -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sugerencias_mejoras">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        Sugerencias de Mejoras para Fortalecer el Vínculo
                                    </label>
                                    <textarea class="form-control" 
                                              id="sugerencias_mejoras" 
                                              name="sugerencias_mejoras" 
                                              rows="4"
                                              placeholder="Sugerencias para mejorar...">{{ old('sugerencias_mejoras') }}</textarea>
                                </div>
                            </div>

                            <!-- Información Complementaria -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="informacion_complementaria">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Información Complementaria
                                    </label>
                                    <textarea class="form-control" 
                                              id="informacion_complementaria" 
                                              name="informacion_complementaria" 
                                              rows="4"
                                              placeholder="Información adicional...">{{ old('informacion_complementaria') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: El convenio NO SE HA ejecutado -->
                    <div id="seccion-no-ejecutado" style="{{ old('convenio_ejecutado', true) ? 'display: none;' : '' }}">
                        <h5 class="text-warning">
                            <i class="fas fa-times mr-2"></i>
                            El convenio no se ha ejecutado
                        </h5>
                        <hr>

                        <div class="row">
                            <!-- Motivos de No Ejecución -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="motivos_no_ejecucion">
                                        <i class="fas fa-question-circle mr-1"></i>
                                        Motivo/s por los que no se han ejecutado los objetivos propuestos <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('motivos_no_ejecucion') is-invalid @enderror" 
                                              id="motivos_no_ejecucion" 
                                              name="motivos_no_ejecucion" 
                                              rows="5"
                                              placeholder="Explique detalladamente los motivos...">{{ old('motivos_no_ejecucion') }}</textarea>
                                    @error('motivos_no_ejecucion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Propuestas de Mejoras -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="propuestas_mejoras">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        Propuestas de Mejoras
                                    </label>
                                    <textarea class="form-control" 
                                              id="propuestas_mejoras" 
                                              name="propuestas_mejoras" 
                                              rows="4"
                                              placeholder="Propuestas para futura ejecución...">{{ old('propuestas_mejoras') }}</textarea>
                                </div>
                            </div>

                            <!-- Información Complementaria No Ejecutado -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="informacion_complementaria_no_ejecutado">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Información Complementaria
                                    </label>
                                    <textarea class="form-control" 
                                              id="informacion_complementaria_no_ejecutado" 
                                              name="informacion_complementaria_no_ejecutado" 
                                              rows="4"
                                              placeholder="Información adicional...">{{ old('informacion_complementaria_no_ejecutado') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evidencias y Documentación -->
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paperclip mr-2"></i>
                        Evidencias y Documentación
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Enlace Google Drive -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="enlace_google_drive">
                                    <i class="fab fa-google-drive mr-1"></i>
                                    Anexo: Fotos, Filmaciones, Informes Escritos (Enlace Google Drive) <span class="text-danger">*</span>
                                </label>
                                <input type="url" 
                                       class="form-control @error('enlace_google_drive') is-invalid @enderror" 
                                       id="enlace_google_drive" 
                                       name="enlace_google_drive" 
                                       value="{{ old('enlace_google_drive') }}" 
                                       required
                                       placeholder="https://drive.google.com/drive/folders/...">
                                @error('enlace_google_drive')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pegue aquí el enlace de la carpeta de Google Drive que contiene todas las evidencias (fotos, videos, documentos, etc.)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Firmas y Fecha -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-signature mr-2"></i>
                        Firmas y Fecha de Presentación
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Firmas -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firmas">
                                    <i class="fas fa-pen mr-1"></i>
                                    Firmas
                                </label>
                                <div id="firmas-container">
                                    @php
                                        $firmas = old('firmas', ['']);
                                        if (empty($firmas) || (count($firmas) == 1 && empty($firmas[0]))) {
                                            $firmas = [''];
                                        }
                                    @endphp
                                    @foreach($firmas as $index => $firma)
                                    <div class="input-group mb-2 firma-group">
                                        <input type="text" 
                                               class="form-control" 
                                               name="firmas[]" 
                                               value="{{ $firma }}" 
                                               placeholder="Nombre y cargo de quien firma"
                                               maxlength="255">
                                        <div class="input-group-append">
                                            @if($index == 0)
                                            <button type="button" class="btn btn-success" id="add-firma">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-danger remove-firma">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Fecha de Presentación -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_presentacion">
                                    <i class="fas fa-calendar-plus mr-1"></i>
                                    Fecha de Presentación <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_presentacion') is-invalid @enderror" 
                                       id="fecha_presentacion" 
                                       name="fecha_presentacion" 
                                       value="{{ old('fecha_presentacion', date('Y-m-d')) }}" 
                                       required>
                                @error('fecha_presentacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones">
                                    <i class="fas fa-comment mr-1"></i>
                                    Observaciones
                                </label>
                                <textarea class="form-control" 
                                          id="observaciones" 
                                          name="observaciones" 
                                          rows="3"
                                          placeholder="Observaciones adicionales sobre el informe...">{{ old('observaciones') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card">
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('informes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Volver
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" name="accion" value="borrador" class="btn btn-warning mr-2">
                                <i class="fas fa-save mr-1"></i>
                                Guardar como Borrador
                            </button>
                            <button type="submit" name="accion" value="enviar" class="btn btn-success">
                                <i class="fas fa-paper-plane mr-1"></i>
                                Guardar y Enviar
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
    // Autocompletar datos del convenio
    $('#convenio_id').change(function() {
        var option = $(this).find('option:selected');
        if (option.val()) {
            $('#institucion_co_celebrante').val(option.data('institucion'));
            $('#fecha_celebracion').val(option.data('fecha'));
            $('#vigencia').val(option.data('vigencia'));
            
            // Auto-seleccionar tipo de convenio
            var tipoConvenio = option.data('tipo');
            if (tipoConvenio === 'Marco') {
                $('#tipo_convenio').val('Marco');
            } else {
                $('#tipo_convenio').val('Específico');
            }
        }
    });

    // Toggle entre convenio ejecutado y no ejecutado
    $('#convenio_ejecutado').change(function() {
        if ($(this).is(':checked')) {
            $('#seccion-ejecutado').show();
            $('#seccion-no-ejecutado').hide();
            
            // Marcar campos como requeridos
            $('#numero_actividades_realizadas, #logros_obtenidos, #beneficios_alcanzados').prop('required', true);
            $('#motivos_no_ejecucion').prop('required', false);
        } else {
            $('#seccion-ejecutado').hide();
            $('#seccion-no-ejecutado').show();
            
            // Marcar campos como requeridos
            $('#numero_actividades_realizadas, #logros_obtenidos, #beneficios_alcanzados').prop('required', false);
            $('#motivos_no_ejecucion').prop('required', true);
        }
    });

    // Agregar coordinador
    $('#add-coordinador').click(function() {
        var container = $('#coordinadores-container');
        var newGroup = `
            <div class="input-group mb-2 coordinador-group">
                <input type="text" 
                       class="form-control" 
                       name="coordinadores_designados[]" 
                       placeholder="Nombre completo del coordinador"
                       maxlength="255">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-coordinador">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        `;
        container.append(newGroup);
    });

    // Remover coordinador
    $(document).on('click', '.remove-coordinador', function() {
        $(this).closest('.coordinador-group').remove();
    });

    // Agregar firma
    $('#add-firma').click(function() {
        var container = $('#firmas-container');
        var newGroup = `
            <div class="input-group mb-2 firma-group">
                <input type="text" 
                       class="form-control" 
                       name="firmas[]" 
                       placeholder="Nombre y cargo de quien firma"
                       maxlength="255">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-firma">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        `;
        container.append(newGroup);
    });

    // Remover firma
    $(document).on('click', '.remove-firma', function() {
        $(this).closest('.firma-group').remove();
    });

    // Validación de fechas
    $('#periodo_hasta').change(function() {
        var fechaDesde = $('#periodo_desde').val();
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

    // Validación del formulario
    $('#informeForm').submit(function(e) {
        var convenioEjecutado = $('#convenio_ejecutado').is(':checked');
        
        // Validar coordinadores
        var coordinadores = $('input[name="coordinadores_designados[]"]').map(function() {
            return $(this).val().trim();
        }).get().filter(function(val) {
            return val !== '';
        });
        
        if (coordinadores.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Coordinadores requeridos',
                text: 'Debe especificar al menos un coordinador designado.'
            });
            return false;
        }

        // Validar campos específicos según ejecución
        if (convenioEjecutado) {
            var camposRequeridos = ['numero_actividades_realizadas', 'logros_obtenidos', 'beneficios_alcanzados'];
            var campoVacio = false;
            
            camposRequeridos.forEach(function(campo) {
                if (!$('#' + campo).val().trim()) {
                    campoVacio = true;
                    $('#' + campo).addClass('is-invalid');
                } else {
                    $('#' + campo).removeClass('is-invalid');
                }
            });
            
            if (campoVacio) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Campos requeridos',
                    text: 'Complete todos los campos obligatorios para convenios ejecutados.'
                });
                return false;
            }
        } else {
            if (!$('#motivos_no_ejecucion').val().trim()) {
                e.preventDefault();
                $('#motivos_no_ejecucion').addClass('is-invalid');
                Swal.fire({
                    icon: 'error',
                    title: 'Motivos requeridos',
                    text: 'Debe especificar los motivos por los que no se ejecutó el convenio.'
                });
                return false;
            }
        }

        // Validar enlace de Google Drive
        var enlace = $('#enlace_google_drive').val();
        if (enlace && !enlace.includes('drive.google.com')) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Enlace inválido',
                text: 'El enlace debe ser de Google Drive.'
            });
            return false;
        }
    });

    // Inicializar estado al cargar la página
    $('#convenio_ejecutado').trigger('change');
});
</script>
@endpush