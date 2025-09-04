@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Configuración del Sistema</h4>
                    <small class="text-muted">Página temporal</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Información:</strong> Esta es una página temporal de configuración. 
                        Las funcionalidades completas se implementarán próximamente.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Usuario</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td>{{ $usuario->nombre ?? 'No definido' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $usuario->email ?? 'No definido' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rol:</strong></td>
                                    <td>{{ $usuario->rol->nombre ?? 'Sin rol asignado' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Última conexión:</strong></td>
                                    <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Configuración General</h5>
                            <form method="POST" action="{{ route('configuracion.update') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Zona Horaria</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="America/Asuncion" selected>America/Asuncion</option>
                                        <option value="America/Buenos_Aires">America/Buenos_Aires</option>
                                        <option value="America/Santiago">America/Santiago</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="language" class="form-label">Idioma</label>
                                    <select class="form-select" id="language" name="language">
                                        <option value="es" selected>Español</option>
                                        <option value="en">English</option>
                                        <option value="pt">Português</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="theme" class="form-label">Tema</label>
                                    <select class="form-select" id="theme" name="theme">
                                        <option value="light" selected>Claro</option>
                                        <option value="dark">Oscuro</option>
                                        <option value="auto">Automático</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Guardar Cambios
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Volver
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection