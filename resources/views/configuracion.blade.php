@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Configuración del Sistema</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Usuario</h5>
                            <p><strong>Nombre:</strong> {{ $usuario->nombre }}</p>
                            <p><strong>Email:</strong> {{ $usuario->email }}</p>
                            <p><strong>Rol:</strong> {{ $usuario->rol->nombre ?? 'Sin rol asignado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Configuración General</h5>
                            <form method="POST" action="{{ route('configuracion.update') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="timezone">Zona Horaria</label>
                                    <select class="form-control" id="timezone" name="timezone" required>
                                        <option value="America/Asuncion">America/Asuncion</option>
                                        <option value="America/Buenos_Aires">America/Buenos_Aires</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="language">Idioma</label>
                                    <select class="form-control" id="language" name="language" required>
                                        <option value="es">Español</option>
                                        <option value="en">English</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection