{{-- resources/views/roles/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Crear Rol')
@section('page-title', 'Crear Nuevo Rol')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-shield mr-2"></i>
                        Información del Rol
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nombre del rol -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-tag mr-1"></i>
                                    Nombre del Rol <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre') }}" 
                                       required
                                       maxlength="50"
                                       placeholder="ej: coordinador_proyectos">
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">
                                    Solo letras minúsculas y guiones bajos. Sin espacios ni caracteres especiales.
                                </small>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="descripcion" class="form-label">
                                    <i class="fas fa-align-left mr-1"></i>
                                    Descripción <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion') is-invalid @enderror" 
                                       id="descripcion" 
                                       name="descripcion" 
                                       value="{{ old('descripcion') }}" 
                                       required
                                       maxlength="255"
                                       placeholder="ej: Coordinador de Proyectos">
                                @error('descripcion')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">
                                    Nombre descriptivo que aparecerá en las interfaces.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="activo" 
                                           name="activo" 
                                           value="1"
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="activo">
                                        <i class="fas fa-toggle-on mr-1"></i>
                                        Rol activo
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Los roles inactivos no aparecerán disponibles para asignar a usuarios.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asignación de Permisos -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Asignación de Permisos
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-outline-light" id="selectAll">
                            <i class="fas fa-check-square mr-1"></i>Seleccionar Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-light ml-1" id="selectNone">
                            <i class="fas fa-square mr-1"></i>Deseleccionar Todos
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($permisosPorModulo->count() > 0)
                        <div class="row">
                            @foreach($permisosPorModulo as $modulo => $permisos)
                            <div class="col-md-6 mb-4">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header">
                                        <h6 class="card-title text-capitalize mb-0">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input module-toggle" 
                                                       id="modulo_{{ $modulo }}"
                                                       data-module="{{ $modulo }}">
                                                <label class="custom-control-label" for="modulo_{{ $modulo }}">
                                                    <i class="fas fa-folder mr-1"></i>
                                                    <strong>{{ ucfirst($modulo) }}</strong>
                                                </label>
                                            </div>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($permisos as $permiso)
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input permission-checkbox" 
                                                   id="permiso_{{ $permiso->id }}"
                                                   name="permisos[]"
                                                   value="{{ $permiso->id }}"
                                                   data-module="{{ $modulo }}"
                                                   {{ in_array($permiso->id, old('permisos', [])) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="permiso_{{ $permiso->id }}">
                                                <span class="permission-name">{{ $permiso->descripcion }}</span>
                                                <br>
                                                <small class="text-muted text-monospace">{{ $permiso->nombre }}</small>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        @error('permisos')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ $message }}
                        </div>
                        @enderror

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Nota:</strong> Los permisos determinan qué acciones puede realizar un usuario con este rol. 
                            Selecciona cuidadosamente los permisos necesarios según las responsabilidades del rol.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            No hay permisos disponibles para asignar. 
                            <a href="{{ route('permisos.index') }}" class="alert-link">Crear permisos</a> primero.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Volver
                        </a>
                        <div>
                            <button type="reset" class="btn btn-outline-secondary mr-2" id="resetForm">
                                <i class="fas fa-undo mr-1"></i>
                                Limpiar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>
                                Crear Rol
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
    // Generar nombre automáticamente desde descripción
    $('#descripcion').on('input', function() {
        if ($('#nombre').val() === '' || $('#nombre').data('auto-generated')) {
            var descripcion = $(this).val().toLowerCase()
                .replace(/[áàäâã]/g, 'a')
                .replace(/[éèëê]/g, 'e')
                .replace(/[íìïî]/g, 'i')
                .replace(/[óòöôõ]/g, 'o')
                .replace(/[úùüû]/g, 'u')
                .replace(/[ñ]/g, 'n')
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '_')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');
            
            $('#nombre').val(descripcion).data('auto-generated', true);
        }
    });

    // Marcar como no auto-generado si el usuario modifica manualmente
    $('#nombre').on('input', function() {
        $(this).data('auto-generated', false);
    });

    // Seleccionar todos los permisos
    $('#selectAll').click(function() {
        $('.permission-checkbox').prop('checked', true);
        $('.module-toggle').prop('checked', true);
        updateCounters();
    });

    // Deseleccionar todos los permisos
    $('#selectNone').click(function() {
        $('.permission-checkbox').prop('checked', false);
        $('.module-toggle').prop('checked', false);
        updateCounters();
    });

    // Toggle de módulo completo
    $('.module-toggle').change(function() {
        var module = $(this).data('module');
        var checked = $(this).is(':checked');
        
        $('[data-module="' + module + '"].permission-checkbox').prop('checked', checked);
        updateCounters();
    });

    // Toggle individual de permisos
    $('.permission-checkbox').change(function() {
        var module = $(this).data('module');
        updateModuleToggle(module);
        updateCounters();
    });

    // Actualizar estado del toggle del módulo
    function updateModuleToggle(module) {
        var totalPermisos = $('[data-module="' + module + '"].permission-checkbox').length;
        var permisosSeleccionados = $('[data-module="' + module + '"].permission-checkbox:checked').length;
        
        var moduleToggle = $('#modulo_' + module);
        
        if (permisosSeleccionados === 0) {
            moduleToggle.prop('checked', false).prop('indeterminate', false);
        } else if (permisosSeleccionados === totalPermisos) {
            moduleToggle.prop('checked', true).prop('indeterminate', false);
        } else {
            moduleToggle.prop('checked', false).prop('indeterminate', true);
        }
    }

    // Actualizar contadores
    function updateCounters() {
        var totalSeleccionados = $('.permission-checkbox:checked').length;
        var totalPermisos = $('.permission-checkbox').length;
        
        // Puedes agregar un contador visual aquí si lo deseas
        console.log('Permisos seleccionados: ' + totalSeleccionados + ' de ' + totalPermisos);
    }

    // Inicializar estado de los toggles de módulo
    @foreach($permisosPorModulo as $modulo => $permisos)
        updateModuleToggle('{{ $modulo }}');
    @endforeach

    // Reset form
    $('#resetForm').click(function() {
        $('.permission-checkbox').prop('checked', false);
        $('.module-toggle').prop('checked', false).prop('indeterminate', false);
        $('#nombre').data('auto-generated', true);
        updateCounters();
    });

    // Validación del formulario
    $('form').on('submit', function(e) {
        var nombre = $('#nombre').val().trim();
        var descripcion = $('#descripcion').val().trim();
        
        if (nombre === '' || descripcion === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos requeridos',
                text: 'El nombre y la descripción son obligatorios.'
            });
            return false;
        }

        // Validar formato del nombre
        var nombreRegex = /^[a-z_]+$/;
        if (!nombreRegex.test(nombre)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Formato de nombre inválido',
                text: 'El nombre del rol solo puede contener letras minúsculas y guiones bajos.'
            });
            $('#nombre').focus();
            return false;
        }
    });
});
</script>
@endpush