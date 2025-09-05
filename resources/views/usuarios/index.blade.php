{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filtros -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filtros de búsqueda
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('usuarios.index') }}" class="form-inline">
                    <div class="row w-100">
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="buscar" class="form-control" 
                                       placeholder="Buscar por nombre, apellido, email o usuario..."
                                       value="{{ request('buscar') }}">
                            </div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="rol" class="form-control">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}" 
                                            {{ request('rol') == $rol->id ? 'selected' : '' }}>
                                        {{ $rol->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i>Buscar
                            </button>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Lista de Usuarios ({{ $usuarios->total() }} registros)
                </h3>
                <div class="card-tools">
                    {{-- Solo admin y super_admin pueden crear usuarios --}}
                    @if(Auth::user()->tieneRol('admin') || Auth::user()->tieneRol('super_admin'))
                    <a href="{{ route('usuarios.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Usuario
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($usuarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Último Login</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar mr-2">
                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $usuario->nombre_completo }}</strong>
                                            @if($usuario->id === Auth::id())
                                                <span class="badge badge-info badge-sm ml-1">Tú</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-monospace">{{ $usuario->username }}</span>
                                </td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    @if($usuario->rol)
                                        <span class="badge badge-primary">{{ $usuario->rol->descripcion }}</span>
                                    @else
                                        <span class="badge badge-secondary">Sin rol</span>
                                    @endif
                                </td>
                                <td>{{ $usuario->telefono ?? '-' }}</td>
                                <td>
                                    @if(Auth::user()->tieneRol('admin') || Auth::user()->tieneRol('super_admin'))
                                        {{-- Solo admin y super_admin pueden cambiar estados --}}
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" 
                                                   class="custom-control-input toggle-estado" 
                                                   id="estado{{ $usuario->id }}"
                                                   data-id="{{ $usuario->id }}"
                                                   {{ $usuario->activo ? 'checked' : '' }}
                                                   {{ $usuario->id === Auth::id() ? 'disabled' : '' }}>
                                            <label class="custom-control-label" 
                                                   for="estado{{ $usuario->id }}">
                                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                            </label>
                                        </div>
                                    @else
                                        {{-- Solo mostrar el estado para usuarios sin permisos de edición --}}
                                        @if($usuario->activo)
                                            <span class="badge badge-success">Activo</span>
                                        @else
                                            <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->ultima_sesion)
                                        <small class="text-muted">
                                            {{ $usuario->ultima_sesion->format('d/m/Y H:i') }}
                                        </small>
                                    @else
                                        <small class="text-muted">Nunca</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Ver detalles - todos pueden ver --}}
                                        <a href="{{ route('usuarios.show', $usuario) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        {{-- Editar - solo admin y super_admin --}}
                                        @if(Auth::user()->tieneRol('admin') || Auth::user()->tieneRol('super_admin'))
                                        <a href="{{ route('usuarios.edit', $usuario) }}" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif

                                        {{-- Eliminar - solo super_admin y con restricciones --}}
                                        @if(Auth::user()->tieneRol('super_admin') && 
                                            $usuario->id !== Auth::id() && 
                                            (!$usuario->rol || $usuario->rol->nombre !== 'super_admin'))
                                        <form method="POST" 
                                              action="{{ route('usuarios.destroy', $usuario) }}" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm btn-delete" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron usuarios</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'rol', 'estado']))
                            Intenta modificar los filtros de búsqueda.
                        @else
                            Comienza creando el primer usuario.
                        @endif
                    </p>
                </div>
                @endif
            </div>
            
            @if($usuarios->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} 
                            de {{ $usuarios->total() }} resultados
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $usuarios->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle estado usuario via AJAX
    $('.toggle-estado').change(function() {
        var checkbox = $(this);
        var usuarioId = checkbox.data('id');
        var nuevoEstado = checkbox.is(':checked');
        
        $.ajax({
            url: '/usuarios/' + usuarioId + '/toggle-estado',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                checkbox.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar etiqueta
                    var label = checkbox.next('label');
                    label.text(response.nuevo_estado ? 'Activo' : 'Inactivo');
                    
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                // Revertir checkbox
                checkbox.prop('checked', !nuevoEstado);
                
                var response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el estado del usuario'
                });
            },
            complete: function() {
                checkbox.prop('disabled', false);
            }
        });
    });

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