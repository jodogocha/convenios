{{-- resources/views/usuarios/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalles del Usuario')
@section('page-title', 'Detalles del Usuario')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
<li class="breadcrumb-item active">{{ $usuario->nombre_completo }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Perfil del Usuario -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="profile-user-img mb-3">
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                    </div>
                    <h3 class="profile-username text-center">{{ $usuario->nombre_completo }}</h3>
                    <p class="text-muted text-center">
                        @if($usuario->rol)
                            <span class="badge badge-primary badge-lg">
                                {{ $usuario->rol->descripcion }}
                            </span>
                        @else
                            <span class="badge badge-secondary badge-lg">Sin rol asignado</span>
                        @endif
                    </p>
                    
                    <div class="text-center mb-3">
                        @if($usuario->activo)
                            <span class="badge badge-success badge-lg">
                                <i class="fas fa-check-circle mr-1"></i>Usuario Activo
                            </span>
                        @else
                            <span class="badge badge-danger badge-lg">
                                <i class="fas fa-times-circle mr-1"></i>Usuario Inactivo
                            </span>
                        @endif
                    </div>

                    @if($usuario->id === Auth::id())
                        <div class="alert alert-info alert-sm">
                            <i class="fas fa-info-circle mr-1"></i>
                            Este es tu perfil
                        </div>
                    @endif
                </div>

                <hr>

                <strong><i class="fas fa-at mr-1"></i> Usuario</strong>
                <p class="text-muted mb-2">
                    <span class="text-monospace">{{ $usuario->usuario }}</span>
                </p>

                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                <p class="text-muted mb-2">
                    <a href="mailto:{{ $usuario->email }}">{{ $usuario->email }}</a>
                </p>

                @if($usuario->telefono)
                <strong><i class="fas fa-phone mr-1"></i> Teléfono</strong>
                <p class="text-muted mb-2">
                    <a href="tel:{{ $usuario->telefono }}">{{ $usuario->telefono }}</a>
                </p>
                @endif

                <strong><i class="fas fa-clock mr-1"></i> Último Acceso</strong>
                <p class="text-muted mb-2">
                    @if($usuario->ultimo_login)
                        {{ $usuario->ultimo_login->format('d/m/Y') }} a las {{ $usuario->ultimo_login->format('H:i:s') }}
                        <br>
                        <small class="text-muted">
                            ({{ $usuario->ultimo_login->diffForHumans() }})
                        </small>
                    @else
                        <span class="text-warning">Nunca ha iniciado sesión</span>
                    @endif
                </p>

                <hr>

                <!-- Acciones -->
                <div class="d-grid gap-2">
                    @if(Auth::user()->tienePermiso('usuarios.editar'))
                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit mr-1"></i>
                        Editar Usuario
                    </a>
                    @endif

                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Lista
                    </a>

                    @if(Auth::user()->tienePermiso('usuarios.eliminar') && 
                        $usuario->id !== Auth::id() && 
                        (!$usuario->rol || $usuario->rol->nombre !== 'administrador'))
                    <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block btn-delete">
                            <i class="fas fa-trash mr-1"></i>
                            Eliminar Usuario
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Información Detallada -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Información Detallada
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Datos Personales</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Nombre:</td>
                                <td>{{ $usuario->nombre }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Apellido:</td>
                                <td>{{ $usuario->apellido }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Email:</td>
                                <td>{{ $usuario->email }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Teléfono:</td>
                                <td>{{ $usuario->telefono ?? 'No especificado' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Información del Sistema</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Usuario:</td>
                                <td><span class="text-monospace">{{ $usuario->usuario }}</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Estado:</td>
                                <td>
                                    @if($usuario->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Rol:</td>
                                <td>
                                    @if($usuario->rol)
                                        <span class="badge badge-primary">{{ $usuario->rol->descripcion }}</span>
                                    @else
                                        <span class="badge badge-secondary">Sin rol</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">ID:</td>
                                <td><span class="text-monospace">#{{ $usuario->id }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos del Rol -->
        @if($usuario->rol && $usuario->rol->permisos->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Permisos del Rol: {{ $usuario->rol->descripcion }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($usuario->rol->permisos->groupBy(function($permiso) {
                        return explode('.', $permiso->nombre)[0];
                    }) as $modulo => $permisos)
                    <div class="col-md-6 mb-3">
                        <h6 class="text-primary text-capitalize">
                            <i class="fas fa-folder mr-1"></i>
                            {{ ucfirst($modulo) }}
                        </h6>
                        <div class="ml-3">
                            @foreach($permisos as $permiso)
                            <span class="badge badge-outline-primary mr-1 mb-1">
                                {{ $permiso->descripcion }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Historial y Auditoría -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Información de Auditoría
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="timeline">
                            <!-- Creación -->
                            <div class="time-label">
                                <span class="bg-green">Registro</span>
                            </div>
                            <div>
                                <i class="fas fa-user-plus bg-green"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock mr-1"></i>
                                        @php
                                             // Si por alguna razón $usuario no está, devolvemos colección vacía.
                                            $audits = data_get($usuario ?? null, 'audits', collect());
                                        @endphp

                                        {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                    </span>
                                    <h3 class="timeline-header">Usuario creado</h3>
                                    <div class="timeline-body">
                                        @if($usuario->audits->isEmpty())
                                            <p class="text-sm text-gray-600">No hay cambios registrados.</p>
                                        @else
                                            <ul>
                                                @foreach($usuario->audits as $audit)
                                                    <li>
                                                        <strong>Evento:</strong> {{ $audit->event }} 
                                                        {{-- events: created, updated, deleted, restored --}}
                                                        <br>
                                                        <strong>Por:</strong> {{ optional($audit->user)->nombre ?? 'Sistema/Desconocido' }}
                                                        <br>
                                                        <strong>Fecha:</strong> {{ $audit->created_at->format('d/m/Y H:i') }}
                                                        <br>
                                                        {{-- Cambios (old/new) resumidos --}}
                                                        @if(!empty($audit->modified))
                                                            <details>
                                                                <summary>Ver cambios</summary>
                                                                <pre>@json($audit->modified, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)</pre>
                                                            </details>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Último login -->
                            @if($usuario->ultimo_login)
                            <div class="time-label">
                                <span class="bg-blue">Acceso</span>
                            </div>
                            <div>
                                <i class="fas fa-sign-in-alt bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $usuario->ultimo_login->format('d/m/Y H:i:s') }}
                                    </span>
                                    <h3 class="timeline-header">Último acceso al sistema</h3>
                                    <div class="timeline-body">
                                        Última vez que el usuario inició sesión
                                        <small class="text-muted">({{ $usuario->ultimo_login->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-user-img {
    border: 0;
}

.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 25px;
    height: 100%;
    width: 4px;
    background: #dee2e6;
}

.timeline > div {
    position: relative;
    margin: 0 0 15px 0;
}

.timeline > div > .timeline-item {
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.1);
    box-shadow: 0 1px 1px rgba(0,0,0,.1);
    border-radius: .25rem;
    background: #fff;
    color: #495057;
    margin-left: 60px;
    margin-right: 15px;
    margin-top: 0;
    padding: 0;
    position: relative;
}

.timeline > div > .timeline-item > .time {
    color: #999;
    float: right;
    padding: 10px;
    font-size: 12px;
}

.timeline > div > .timeline-item > .timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline > div > .timeline-item > .timeline-body {
    padding: 10px;
}

.timeline > div > .fa,
.timeline > div > .fab,
.timeline > div > .fad,
.timeline > div > .fal,
.timeline > div > .far,
.timeline > div > .fas {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}

.timeline > .time-label > span {
    font-weight: 600;
    color: #fff;
    border-radius: 4px;
    display: inline-block;
    padding: 5px;
}

.timeline > div > .bg-blue {
    background-color: #007bff !important;
}

.timeline > div > .bg-green {
    background-color: #28a745 !important;
}

.timeline > div > .bg-yellow {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.timeline > div > .bg-gray {
    background-color: #6c757d !important;
}

.badge-outline-primary {
    color: #007bff;
    background-color: transparent;
    border: 1px solid #007bff;
}
</style>
@endpush