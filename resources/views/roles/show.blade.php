{{-- resources/views/roles/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalles del Rol')
@section('page-title', 'Detalles del Rol')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">{{ $rol->descripcion }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Información del Rol -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="profile-role-icon mb-3">
                        <i class="fas fa-user-shield fa-5x 
                           {{ $rol->nombre === 'super_admin' ? 'text-danger' : 
                              ($rol->nombre === 'admin' ? 'text-warning' : 'text-primary') }}"></i>
                    </div>
                    <h3 class="profile-username text-center">{{ $rol->descripcion }}</h3>
                    <p class="text-muted text-center">
                        <span class="text-monospace">{{ $rol->nombre }}</span>
                    </p>
                    
                    <div class="text-center mb-3">
                        @if($rol->activo)
                            <span class="badge badge-success badge-lg">
                                <i class="fas fa-check-circle mr-1"></i>Rol Activo
                            </span>
                        @else
                            <span class="badge badge-danger badge-lg">
                                <i class="fas fa-times-circle mr-1"></i>Rol Inactivo
                            </span>
                        @endif
                    </div>

                    @if($rol->nombre === 'super_admin')
                        <div class="alert alert-danger alert-sm">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Rol crítico del sistema
                        </div>
                    @elseif($rol->nombre === 'admin')
                        <div class="alert alert-warning alert-sm">
                            <i class="fas fa-cog mr-1"></i>
                            Rol de administración
                        </div>
                    @endif
                </div>

                <hr>

                <strong><i class="fas fa-users mr-1"></i> Usuarios Asignados</strong>
                <p class="text-muted mb-2">
                    @if($rol->usuarios->count() > 0)
                        <span class="badge badge-info">{{ $rol->usuarios->count() }} usuarios</span>
                    @else
                        <span class="text-muted">Sin usuarios asignados</span>
                    @endif
                </p>

                <strong><i class="fas fa-shield-alt mr-1"></i> Permisos Asignados</strong>
                <p class="text-muted mb-2">
                    @if($rol->permisos->count() > 0)
                        <span class="badge badge-primary">{{ $rol->permisos->count() }} permisos</span>
                    @else
                        <span class="text-muted">Sin permisos asignados</span>
                    @endif
                </p>

                <strong><i class="fas fa-clock mr-1"></i> Fecha de Creación</strong>
                <p class="text-muted mb-2">
                    {{ $rol->created_at ? $rol->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
                    @if($rol->created_at)
                        <br>
                        <small class="text-muted">
                            ({{ $rol->created_at->diffForHumans() }})
                        </small>
                    @endif
                </p>

                <hr>

                <!-- Acciones -->
                <div class="d-grid gap-2">
                    @if($rol->nombre !== 'super_admin')
                    <a href="{{ route('roles.edit', $rol) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit mr-1"></i>
                        Editar Rol
                    </a>
                    @endif

                    <a href="{{ route('roles.clonar', $rol) }}" class="btn btn-info btn-block">
                        <i class="fas fa-copy mr-1"></i>
                        Clonar Rol
                    </a>

                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Lista
                    </a>

                    @if(!in_array($rol->nombre, ['super_admin', 'admin', 'usuario']) && $rol->usuarios->count() === 0)
                    <form method="POST" action="{{ route('roles.destroy', $rol) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block btn-delete">
                            <i class="fas fa-trash mr-1"></i>
                            Eliminar Rol
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
                            <i class="fas fa-tag mr-1"></i>
                            Datos del Rol
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Nombre:</td>
                                <td><span class="text-monospace">{{ $rol->nombre }}</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Descripción:</td>
                                <td>{{ $rol->descripcion }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Estado:</td>
                                <td>
                                    @if($rol->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">ID de Rol:</td>
                                <td><span class="text-monospace">#{{ $rol->id }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Estadísticas
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Usuarios Asignados:</td>
                                <td>
                                    <span class="badge badge-info">{{ $rol->usuarios->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Permisos Asignados:</td>
                                <td>
                                    <span class="badge badge-primary">{{ $rol->permisos->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Módulos Cubiertos:</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $permisosPorModulo->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Última Modificación:</td>
                                <td>
                                    @if($rol->updated_at)
                                        {{ $rol->updated_at->format('d/m/Y H:i') }}
                                    @else
                                        No disponible
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos Asignados -->
        @if($permisosPorModulo->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Permisos Asignados por Módulo
                </h3>
                <div class="card-tools">
                    <span class="badge badge-primary">
                        {{ $rol->permisos->count() }} permisos en {{ $permisosPorModulo->count() }} módulos
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($permisosPorModulo as $modulo => $permisos)
                    <div class="col-md-6 mb-3">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-folder mr-1"></i>
                                    <strong class="text-capitalize">{{ $modulo }}</strong>
                                    <span class="badge badge-info badge-sm ml-2">{{ $permisos->count() }}</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($permisos as $permiso)
                                <div class="mb-2">
                                    <span class="badge badge-outline-primary mr-1">
                                        <i class="fas fa-check mr-1"></i>
                                        {{ $permiso->descripcion }}
                                    </span>
                                    <br>
                                    <small class="text-muted text-monospace ml-2">{{ $permiso->nombre }}</small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Permisos Asignados
                </h3>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Sin permisos asignados</h5>
                    <p class="text-muted">
                        Este rol no tiene permisos asignados actualmente.
                    </p>
                    @if($rol->nombre !== 'super_admin')
                    <a href="{{ route('roles.edit', $rol) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i>
                        Asignar Permisos
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Usuarios con este Rol -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Usuarios con este Rol
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">
                        {{ $rol->usuarios->count() }} usuarios
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($rol->usuarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Último Acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rol->usuarios as $usuario)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-lg text-muted mr-2"></i>
                                        <div>
                                            <strong>{{ $usuario->nombre_completo }}</strong>
                                            <br>
                                            <small class="text-muted text-monospace">{{ $usuario->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    @if($usuario->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->ultima_sesion)
                                        <small>{{ $usuario->ultima_sesion->format('d/m/Y H:i') }}</small>
                                    @else
                                        <small class="text-muted">Nunca</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('usuarios.show', $usuario) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Ver detalles del usuario">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Sin usuarios asignados</h5>
                    <p class="text-muted">
                        Ningún usuario tiene asignado este rol actualmente.
                    </p>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-primary">
                        <i class="fas fa-users mr-1"></i>
                        Gestionar Usuarios
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-role-icon {
    border: 0;
}

.badge-outline-primary {
    color: #007bff;
    background-color: transparent;
    border: 1px solid #007bff;
}

.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.card-outline.card-info {
    border-top: 3px solid #17a2b8;
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