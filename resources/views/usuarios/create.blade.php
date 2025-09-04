{{-- resources/views/usuarios/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Crear Usuario')
@section('page-title', 'Crear Nuevo Usuario')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
<li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus mr-2"></i>
                        Información del Usuario
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user mr-1"></i>
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre') }}" 
                                       required
                                       maxlength="100"
                                       placeholder="Ingrese el nombre">
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Apellido -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido" class="form-label">
                                    <i class="fas fa-user mr-1"></i>
                                    Apellido <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('apellido') is-invalid @enderror" 
                                       id="apellido" 
                                       name="apellido" 
                                       value="{{ old('apellido') }}" 
                                       required
                                       maxlength="100"
                                       placeholder="Ingrese el apellido">
                                @error('apellido')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required
                                       placeholder="ejemplo@correo.com">
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Usuario -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario" class="form-label">
                                    <i class="fas fa-at mr-1"></i>
                                    Nombre de Usuario <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('usuario') is-invalid @enderror" 
                                       id="usuario" 
                                       name="usuario" 
                                       value="{{ old('usuario') }}" 
                                       required
                                       maxlength="50"
                                       placeholder="nombreusuario">
                                @error('usuario')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">
                                    Solo letras, números y guiones bajos. Sin espacios.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Contraseña -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock mr-1"></i>
                                    Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required
                                           minlength="8"
                                           placeholder="Mínimo 8 caracteres">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye" id="passwordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock mr-1"></i>
                                    Confirmar Contraseña <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required
                                       minlength="8"
                                       placeholder="Repita la contraseña">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Rol -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rol_id" class="form-label">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    Rol <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('rol_id') is-invalid @enderror" 
                                        id="rol_id" 
                                        name="rol_id" 
                                        required>
                                    <option value="">Seleccione un rol...</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" 
                                                {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                            {{ $rol->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rol_id')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone mr-1"></i>
                                    Teléfono
                                </label>
                                <input type="text" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}" 
                                       maxlength="20"
                                       placeholder="+595 21 123456">
                                @error('telefono')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
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
                                        Usuario activo
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Los usuarios inactivos no podrán iniciar sesión en el sistema.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Volver
                        </a>
                        <div>
                            <button type="reset" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i>
                                Limpiar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>
                                Crear Usuario
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
    // Toggle password visibility
    $('#togglePassword').click(function() {
        var passwordField = $('#password');
        var passwordIcon = $('#passwordIcon');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Generar usuario automáticamente desde nombre y apellido
    $('#nombre, #apellido').on('input', function() {
        if ($('#usuario').val() === '' || $('#usuario').data('auto-generated')) {
            var nombre = $('#nombre').val().toLowerCase().replace(/\s+/g, '');
            var apellido = $('#apellido').val().toLowerCase().replace(/\s+/g, '');
            var usuario = nombre + (apellido ? '.' + apellido : '');
            
            // Limpiar caracteres especiales
            usuario = usuario.replace(/[^a-z0-9._]/g, '');
            
            $('#usuario').val(usuario).data('auto-generated', true);
        }
    });

    // Marcar como no auto-generado si el usuario modifica manualmente
    $('#usuario').on('input', function() {
        $(this).data('auto-generated', false);
    });

    // Validación de confirmación de contraseña en tiempo real
    $('#password_confirmation').on('input', function() {
        var password = $('#password').val();
        var confirmation = $(this).val();
        
        if (password !== confirmation && confirmation !== '') {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Las contraseñas no coinciden</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Validación del formulario antes del envío
    $('form').on('submit', function(e) {
        var password = $('#password').val();
        var confirmation = $('#password_confirmation').val();
        
        if (password !== confirmation) {
            e.preventDefault();
            $('#password_confirmation').addClass('is-invalid');
            if (!$('#password_confirmation').next('.invalid-feedback').length) {
                $('#password_confirmation').after('<div class="invalid-feedback">Las contraseñas no coinciden</div>');
            }
            $('#password_confirmation').focus();
            return false;
        }
    });
});
</script>
@endpush