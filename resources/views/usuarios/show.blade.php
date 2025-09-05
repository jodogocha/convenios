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
                    <span class="text-monospace">{{ $usuario->username }}</span>
                </p>

                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                <p class="text-muted mb-2">
                    <a href="mailto:{{ $usuario->email }}">{{ $usuario->email }}</a>
                    @if($usuario->email_verificado)
                        <i class="fas fa-check-circle text-success ml-1" title="Email verificado"></i>
                    @else
                        <i class="fas fa-exclamation-triangle text-warning ml-1" title="Email no verificado"></i>
                    @endif
                </p>

                @if($usuario->telefono)
                <strong><i class="fas fa-phone mr-1"></i> Teléfono</strong>
                <p class="text-muted mb-2">
                    <a href="tel:{{ $usuario->telefono }}">{{ $usuario->telefono }}</a>
                </p>
                @endif

                @if($usuario->fecha_nacimiento)
                <strong><i class="fas fa-birthday-cake mr-1"></i> Fecha de Nacimiento</strong>
                <p class="text-muted mb-2">
                    {{ $usuario->fecha_nacimiento->format('d/m/Y') }}
                    <small class="text-muted">({{ $usuario->fecha_nacimiento->age }} años)</small>
                </p>
                @endif

                <strong><i class="fas fa-clock mr-1"></i> Último Acceso</strong>
                <p class="text-muted mb-2">
                    @if($usuario->ultima_sesion)
                        {{ $usuario->ultima_sesion->format('d/m/Y') }} a las {{ $usuario->ultima_sesion->format('H:i:s') }}
                        <br>
                        <small class="text-muted">
                            ({{ $usuario->ultima_sesion->diffForHumans() }})
                        </small>
                        @if($usuario->ip_ultima_sesion)
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                IP: {{ $usuario->ip_ultima_sesion }}
                            </small>
                        @endif
                    @else
                        <span class="text-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Nunca ha iniciado sesión
                        </span>
                    @endif
                </p>

                <hr>

                <!-- Acciones -->
                <div class="d-grid gap-2">
                    @if(Auth::user()->tieneRol('admin') || Auth::user()->tieneRol('super_admin'))
                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit mr-1"></i>
                        Editar Usuario
                    </a>
                    @endif

                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Lista
                    </a>

                    @if(Auth::user()->tieneRol('super_admin') && 
                        $usuario->id !== Auth::id() && 
                        (!$usuario->rol || $usuario->rol->nombre !== 'super_admin'))
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
                        <h6 class="text-primary">
                            <i class="fas fa-user mr-1"></i>
                            Datos Personales
                        </h6>
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
                                <td>
                                    {{ $usuario->email }}
                                    @if($usuario->email_verificado)
                                        <span class="badge badge-success badge-sm ml-1">Verificado</span>
                                    @else
                                        <span class="badge badge-warning badge-sm ml-1">No verificado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Teléfono:</td>
                                <td>{{ $usuario->telefono ?? 'No especificado' }}</td>
                            </tr>
                            @if($usuario->fecha_nacimiento)
                            <tr>
                                <td class="font-weight-bold">Fecha de Nacimiento:</td>
                                <td>{{ $usuario->fecha_nacimiento->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-cog mr-1"></i>
                            Información del Sistema
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Usuario:</td>
                                <td><span class="text-monospace">{{ $usuario->username }}</span></td>
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
                                <td class="font-weight-bold">ID de Usuario:</td>
                                <td><span class="text-monospace">#{{ $usuario->id }}</span></td>
                            </tr>
                            @if($usuario->bloqueado_hasta)
                            <tr>
                                <td class="font-weight-bold">Bloqueado hasta:</td>
                                <td>
                                    <span class="text-danger">
                                        {{ $usuario->bloqueado_hasta->format('d/m/Y H:i:s') }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Acceso -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Estadísticas de Acceso
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Último Acceso</span>
                                <span class="info-box-number">
                                    @if($usuario->ultima_sesion)
                                        {{ $usuario->ultima_sesion->format('d/m/Y') }}
                                        <br>
                                        <small>{{ $usuario->ultima_sesion->format('H:i:s') }}</small>
                                    @else
                                        <small>Nunca</small>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Intentos Fallidos</span>
                                <span class="info-box-number">{{ $usuario->intentos_fallidos }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon {{ $usuario->estaBloqueado() ? 'bg-danger' : 'bg-success' }}">
                                <i class="fas {{ $usuario->estaBloqueado() ? 'fa-lock' : 'fa-unlock' }}"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Estado de Cuenta</span>
                                <span class="info-box-number">
                                    @if($usuario->estaBloqueado())
                                        <small>Bloqueada</small>
                                    @else
                                        <small>Desbloqueada</small>
                                    @endif
                                </span>
                            </div>
                        </div>
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

        <!-- Historial de Auditoría -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Historial de Auditoría
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">
                        {{ $usuario->audits->count() }} cambios registrados
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($usuario->audits->count() > 0)
                <div class="timeline">
                    @php $lastDate = null; @endphp
                    @foreach($usuario->audits->sortByDesc('created_at') as $audit)
                        @php 
                            $currentDate = $audit->created_at->format('Y-m-d');
                            $showDateLabel = $lastDate !== $currentDate;
                            $lastDate = $currentDate;
                        @endphp
                        
                        @if($showDateLabel)
                        <div class="time-label">
                            <span class="{{ $audit->created_at->isToday() ? 'bg-primary' : ($audit->created_at->isYesterday() ? 'bg-info' : 'bg-secondary') }}">
                                @if($audit->created_at->isToday())
                                    Hoy
                                @elseif($audit->created_at->isYesterday())
                                    Ayer
                                @else
                                    {{ $audit->created_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                        @endif
                        
                        <div>
                            <i class="fas {{ $audit->event === 'created' ? 'fa-user-plus bg-green' : ($audit->event === 'updated' ? 'fa-edit bg-yellow' : 'fa-trash bg-red') }}"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $audit->created_at->format('H:i:s') }}
                                </span>
                                <h3 class="timeline-header">
                                    @switch($audit->event)
                                        @case('created')
                                            <i class="fas fa-plus-circle text-success mr-1"></i>
                                            Usuario creado en el sistema
                                            @break
                                        @case('updated')
                                            <i class="fas fa-edit text-warning mr-1"></i>
                                            Información actualizada
                                            @break
                                        @case('deleted')
                                            <i class="fas fa-trash text-danger mr-1"></i>
                                            Usuario eliminado
                                            @break
                                        @default
                                            {{ ucfirst($audit->event) }}
                                    @endswitch
                                </h3>
                                <div class="timeline-body">
                                    @if($audit->event === 'created')
                                        <p>Usuario registrado exitosamente en el sistema.</p>
                                        @if(!empty($audit->new_values))
                                            <strong>Datos iniciales:</strong>
                                            <ul class="list-unstyled ml-3">
                                                @foreach($audit->new_values as $key => $value)
                                                    @if(in_array($key, ['username', 'email', 'nombre', 'apellido', 'rol_id', 'activo']))
                                                        <li>
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                            @if($key === 'rol_id')
                                                                {{ \App\Models\Rol::find($value)->nombre ?? 'Rol eliminado' }}
                                                            @elseif($key === 'activo')
                                                                {{ $value ? 'Activo' : 'Inactivo' }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    @elseif($audit->event === 'updated')
                                        @if(!empty($audit->old_values) && !empty($audit->new_values))
                                            <strong>Cambios realizados:</strong>
                                            <div class="table-responsive mt-2">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Campo</th>
                                                            <th>Valor Anterior</th>
                                                            <th>Valor Nuevo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($audit->new_values as $key => $newValue)
                                                            @if(isset($audit->old_values[$key]) && in_array($key, ['username', 'email', 'nombre', 'apellido', 'telefono', 'rol_id', 'activo']))
                                                                <tr>
                                                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                                    <td>
                                                                        @if($key === 'rol_id')
                                                                            {{ \App\Models\Rol::find($audit->old_values[$key])->nombre ?? 'Rol eliminado' }}
                                                                        @elseif($key === 'activo')
                                                                            <span class="badge badge-{{ $audit->old_values[$key] ? 'success' : 'danger' }}">
                                                                                {{ $audit->old_values[$key] ? 'Activo' : 'Inactivo' }}
                                                                            </span>
                                                                        @else
                                                                            {{ $audit->old_values[$key] ?: '(vacío)' }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($key === 'rol_id')
                                                                            {{ \App\Models\Rol::find($newValue)->nombre ?? 'Rol eliminado' }}
                                                                        @elseif($key === 'activo')
                                                                            <span class="badge badge-{{ $newValue ? 'success' : 'danger' }}">
                                                                                {{ $newValue ? 'Activo' : 'Inactivo' }}
                                                                            </span>
                                                                        @else
                                                                            {{ $newValue ?: '(vacío)' }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted">Sin detalles de cambios disponibles.</p>
                                        @endif
                                    @endif
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user mr-1"></i>
                                            Por: {{ $audit->user->nombre_completo ?? 'Sistema' }}
                                            @if($audit->ip_address)
                                                | <i class="fas fa-map-marker-alt mr-1"></i>IP: {{ $audit->ip_address }}
                                            @endif
                                            | <i class="fas fa-clock mr-1"></i>{{ $audit->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div>
                        <i class="fas fa-flag bg-gray"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Inicio del historial</h3>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Sin historial de auditoría</h5>
                    <p class="text-muted">
                        No se han registrado cambios para este usuario.
                    </p>
                </div>
                @endif
            </div>
            @if($usuario->audits->count() > 10)
            <div class="card-footer text-center">
                <small class="text-muted">
                    Mostrando los últimos cambios. Total: {{ $usuario->audits->count() }} registros
                </small>
            </div>
            @endif
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

.info-box-number {
    font-size: 14px !important;
}

.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Confirmación para eliminaciones
    $('.btn-delete').click(function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush