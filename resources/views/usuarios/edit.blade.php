{{-- resources/views/usuarios/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')
            
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit mr-2"></i>
                        Editar Usuario: {{ $usuario->nombre_completo }}
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
                                       value="{{ old('nombre', $usuario->nombre) }}" 
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
                                       value="{{ old('apellido', $usuario->apellido) }}" 
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
                                       value="{{ old('email', $usuario->email) }}" 
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
                                       value="{{ old('usuario', $usuario->usuario) }}" 
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
                                    Nueva Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           minlength="8"
                                           placeholder="Dejar en blanco para mantener actual">
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
                                <small class="form-text text-muted">
                                    Deje este campo vacío si no desea cambiar la contraseña.
                                </small>
                            </div>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock mr-1"></i>
                                    Confirmar Nueva Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       minlength="8"
                                       placeholder="Confirme la nueva contraseña">
                                <small class="form-text text-muted">
                                    Solo requerido si ingresó una nueva contraseña.
                                </small>
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
                                                {{ old('rol_id', $usuario->rol_id) == $rol->id ? 'selected' : '' }}>
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
                                       value="{{ old('telefono', $usuario->telefono) }}" 
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
                                           {{ old('activo', $usuario->activo) ? 'checked' : '' }}
                                           {{ $usuario->id === Auth::id() ? 'disabled' : '' }}>
                                    <label class="custom-control-label" for="activo">
                                        <i class="fas fa-toggle-on mr-1"></i>
                                        Usuario activo
                                    </label>
                                </div>
                                @if($usuario->id === Auth::id())
                                    <small class="form-text text-warning">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        No puedes desactivar tu propio usuario.
                                    </small>
                                @else
                                    <small class="form-text text-muted">
                                        Los usuarios inactivos no podrán iniciar sesión en el sistema.
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle mr-2"></i>Información del Usuario</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>
                                            <strong>Creado:</strong> 
                                            {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                            @if($usuario->creador)
                                                por {{ $usuario->creador->nombre_completo }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small>
                                            <strong>Última modificación:</strong> 
                                            {{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                            @if($usuario->editor)
                                                por {{ $usuario->editor->nombre_completo }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                @if($usuario->ultimo_login)
                                <div class="row mt-1">
                                    <div class="col-md-6">
                                        <small>
                                            <strong>Último acceso:</strong> 
                                            {{ $usuario->ultimo_login->format('d/m/Y H:i:s') }}
                                        </small>
                                    </div>
                                </div>
                                @endif
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
                            <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-info mr-2">
                                <i class="fas fa-eye mr-1"></i>
                                Ver Usuario
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i>
                                Actualizar Usuario
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

    // Validación de confirmación de contraseña
    $('#password, #password_confirmation').on('input', function() {
        var password = $('#password').val();
        var confirmation = $('#password_confirmation').val();
        
        // Solo validar si ambos campos tienen contenido
        if (password !== '' || confirmation !== '') {
            if (password !== confirmation && confirmation !== '') {
                $('#password_confirmation').addClass('is-invalid');
                if (!$('#password_confirmation').next('.invalid-feedback').length) {
                    $('#password_confirmation').after('<div class="invalid-feedback">Las contraseñas no coinciden</div>');
                }
            } else {
                $('#password_confirmation').removeClass('is-invalid');
                $('#password_confirmation').next('.invalid-feedback').remove();
            }
        }
    });

    // Limpiar validación cuando se vacía el campo de contraseña
    $('#password').on('input', function() {
        if ($(this).val() === '') {
            $('#password_confirmation').val('').removeClass('is-invalid');
            $('#password_confirmation').next('.invalid-feedback').remove();
        }
    });

    // Validación del formulario antes del envío
    $('form').on('submit', function(e) {
        var password = $('#password').val();
        var confirmation = $('#password_confirmation').val();
        
        // Si se ingresó contraseña, debe confirmarla
        if (password !== '' && password !== confirmation) {
            e.preventDefault();
            $('#password_confirmation').addClass('is-invalid');
            if (!$('#password_confirmation').next('.invalid-feedback').length) {
                $('#password_confirmation').after('<div class="invalid-feedback">Las contraseñas no coinciden</div>');
            }
            $('#password_confirmation').focus();
            return false;
        }
        
        // Si se ingresó confirmación pero no contraseña
        if (password === '' && confirmation !== '') {
            e.preventDefault();
            $('#password').addClass('is-invalid');
            if (!$('#password').next('.invalid-feedback').length) {
                $('#password').after('<div class="invalid-feedback">Debe ingresar la nueva contraseña</div>');
            }
            $('#password').focus();
            return false;
        }
    });
});
</script>
@endpush