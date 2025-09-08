{{-- resources/views/roles/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Rol')
@section('page-title', 'Editar Rol')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item"><a href="{{ route('roles.show', $rol) }}">{{ $rol->descripcion }}</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')
            
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Rol: {{ $role->descripcion }}
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
                                       value="{{ old('nombre', $role->nombre) }}" 
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
                                       value="{{ old('descripcion', $role->descripcion) }}" 
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
                                           {{ old('activo', $role->activo) ? 'checked' : '' }}>
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

                    <!-- Información adicional -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle mr-2"></i>Información del Rol</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small>
                                            <strong>Creado:</strong> 
                                            {{ $role->created_at ? $role->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small>
                                            <strong>Última modificación:</strong> 
                                            {{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small>
                                            <strong>Usuarios asignados:</strong> 
                                            <span class="badge badge-info">{{ $role->usuarios->count() }}</span>
                                        </small>
                                    </div>
                                </div>
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
                        Gestión de Permisos
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary mr-2">
                            <span id="permisos-count">{{ count($permisosAsignados) }}</span> seleccionados
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-light" id="selectAll">
                            <i class="fas fa-check-square mr-1"></i>Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-light ml-1" id="selectNone">
                            <i class="fas fa-square mr-1"></i>Ninguno
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
                                                    <span class="badge badge-secondary badge-sm ml-1" id="count-{{ $modulo }}">0</span>
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
                                                   {{ in_array($permiso->id, old('permisos', $permisosAsignados)) ? 'checked' : '' }}>
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

                        <!-- Comparación de cambios -->
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle mr-2"></i>Cambios en Permisos</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Permisos actuales:</strong>
                                    <span class="badge badge-info">{{ count($permisosAsignados) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Permisos seleccionados:</strong>
                                    <span class="badge badge-warning" id="nuevos-count">{{ count($permisosAsignados) }}</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Los cambios en los permisos afectarán a todos los usuarios con este rol.
                                </small>
                            </div>
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
                            <a href="{{ route('roles.index') }}" class="btn btn-info mr-2">
                                <i class="fas fa-list mr-1"></i>
                                Lista de Roles
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i>
                                Actualizar Rol
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
        var moduleCounter = $('#count-' + module);
        
        moduleCounter.text(permisosSeleccionados + '/' + totalPermisos);
        
        if (permisosSeleccionados === 0) {
            moduleToggle.prop('checked', false).prop('indeterminate', false);
            moduleCounter.removeClass('badge-primary badge-warning').addClass('badge-secondary');
        } else if (permisosSeleccionados === totalPermisos) {
            moduleToggle.prop('checked', true).prop('indeterminate', false);
            moduleCounter.removeClass('badge-secondary badge-warning').addClass('badge-primary');
        } else {
            moduleToggle.prop('checked', false).prop('indeterminate', true);
            moduleCounter.removeClass('badge-secondary badge-primary').addClass('badge-warning');
        }
    }

    // Actualizar contadores generales
    function updateCounters() {
        var totalSeleccionados = $('.permission-checkbox:checked').length;
        var totalPermisos = $('.permission-checkbox').length;
        
        $('#permisos-count').text(totalSeleccionados);
        $('#nuevos-count').text(totalSeleccionados);
        
        // Actualizar contadores por módulo
        @foreach($permisosPorModulo as $modulo => $permisos)
            updateModuleToggle('{{ $modulo }}');
        @endforeach
    }

    // Inicializar estado de los toggles de módulo
    @foreach($permisosPorModulo as $modulo => $permisos)
        updateModuleToggle('{{ $modulo }}');
    @endforeach
    
    // Inicializar contador general
    updateCounters();

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

        // Confirmación de cambios importantes
        var permisosActuales = {{ count($permisosAsignados) }};
        var permisosNuevos = $('.permission-checkbox:checked').length;
        var usuariosAfectados = {{ $rol->usuarios->count() }};
        
        if (Math.abs(permisosActuales - permisosNuevos) > 0 && usuariosAfectados > 0) {
            e.preventDefault();
            
            var mensaje = 'Los cambios en permisos afectarán a ' + usuariosAfectados + ' usuario(s) con este rol. ¿Continuar?';
            
            Swal.fire({
                title: 'Confirmar cambios',
                text: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).off('submit').submit();
                }
            });
            
            return false;
        }
    });

    // Destacar cambios en tiempo real
    $('.permission-checkbox').change(function() {
        var originalState = {{ json_encode($permisosAsignados) }};
        var currentState = [];
        
        $('.permission-checkbox:checked').each(function() {
            currentState.push(parseInt($(this).val()));
        });
        
        // Resaltar cambios
        $('.permission-checkbox').each(function() {
            var permisoId = parseInt($(this).val());
            var estabaSeleccionado = originalState.includes(permisoId);
            var estaSeleccionado = $(this).is(':checked');
            
            var label = $(this).next('label');
            
            if (estabaSeleccionado !== estaSeleccionado) {
                // Hay cambio
                if (estaSeleccionado) {
                    // Nuevo permiso agregado
                    label.addClass('text-success font-weight-bold');
                    label.removeClass('text-danger');
                } else {
                    // Permiso removido
                    label.addClass('text-danger');
                    label.removeClass('text-success font-weight-bold');
                }
            } else {
                // Sin cambios
                label.removeClass('text-success text-danger font-weight-bold');
            }
        });
    });
});
</script>
@endpush