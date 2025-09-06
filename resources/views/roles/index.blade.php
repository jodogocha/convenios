{{-- resources/views/roles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Roles')
@section('page-title', 'Gestión de Roles')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Roles</li>
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
                <form method="GET" action="{{ route('roles.index') }}" class="form-inline">
                    <div class="row w-100">
                        <div class="col-md-6 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="buscar" class="form-control" 
                                       placeholder="Buscar por nombre o descripción..."
                                       value="{{ request('buscar') }}">
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i>Buscar
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de roles -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-shield mr-2"></i>
                    Lista de Roles ({{ $roles->total() }} registros)
                </h3>
                <div class="card-tools">
                    <a href="{{ route('roles.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Rol
                    </a>
                    <a href="{{ route('roles.export', request()->query()) }}" class="btn btn-info btn-sm ml-1">
                        <i class="fas fa-download mr-1"></i>
                        Exportar CSV
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($roles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nombre del Rol</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Usuarios Asignados</th>
                                <th>Fecha Creación</th>
                                <th width="180">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $rol)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="role-icon mr-2">
                                            <i class="fas fa-user-shield fa-2x 
                                               {{ $rol->nombre === 'super_admin' ? 'text-danger' : 
                                                  ($rol->nombre === 'admin' ? 'text-warning' : 'text-primary') }}"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $rol->descripcion }}</strong>
                                            <br>
                                            <small class="text-muted text-monospace">{{ $rol->nombre }}</small>
                                            @if($rol->nombre === 'super_admin')
                                                <span class="badge badge-danger badge-sm ml-1">Crítico</span>
                                            @elseif($rol->nombre === 'admin')
                                                <span class="badge badge-warning badge-sm ml-1">Sistema</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $rol->descripcion }}</td>
                                <td>
                                    @if(in_array($rol->nombre, ['super_admin', 'admin']))
                                        {{-- Roles críticos no se pueden desactivar --}}
                                        @if($rol->activo)
                                            <span class="badge badge-success">Activo</span>
                                        @else
                                            <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    @else
                                        {{-- Roles normales con toggle --}}
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" 
                                                   class="custom-control-input toggle-estado" 
                                                   id="estado{{ $rol->id }}"
                                                   data-id="{{ $rol->id }}"
                                                   {{ $rol->activo ? 'checked' : '' }}>
                                            <label class="custom-control-label" 
                                                   for="estado{{ $rol->id }}">
                                                {{ $rol->activo ? 'Activo' : 'Inactivo' }}
                                            </label>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($rol->usuarios_count > 0)
                                        <span class="badge badge-info">
                                            {{ $rol->usuarios_count }} 
                                            {{ $rol->usuarios_count === 1 ? 'usuario' : 'usuarios' }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin usuarios</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $rol->created_at ? $rol->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Ver detalles --}}
                                        <a href="{{ route('roles.show', $rol) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        {{-- Editar - no permitir editar super_admin --}}
                                        @if($rol->nombre !== 'super_admin')
                                        <a href="{{ route('roles.edit', $rol) }}" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif

                                        {{-- Clonar --}}
                                        <a href="{{ route('roles.clonar', $rol) }}" 
                                           class="btn btn-secondary btn-sm" title="Clonar rol">
                                            <i class="fas fa-copy"></i>
                                        </a>

                                        {{-- Eliminar - solo roles no críticos sin usuarios --}}
                                        @if(!in_array($rol->nombre, ['super_admin', 'admin', 'usuario']) && $rol->usuarios_count === 0)
                                        <form method="POST" 
                                              action="{{ route('roles.destroy', $rol) }}" 
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
                    <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron roles</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'estado']))
                            Intenta modificar los filtros de búsqueda.
                        @else
                            Comienza creando el primer rol.
                        @endif
                    </p>
                </div>
                @endif
            </div>
            
            @if($roles->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando {{ $roles->firstItem() }} a {{ $roles->lastItem() }} 
                            de {{ $roles->total() }} resultados
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $roles->appends(request()->query())->links() }}
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
    // Toggle estado rol via AJAX
    $('.toggle-estado').change(function() {
        var checkbox = $(this);
        var rolId = checkbox.data('id');
        var nuevoEstado = checkbox.is(':checked');
        
        $.ajax({
            url: '/roles/' + rolId + '/toggle-estado',
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
                    text: response.message || 'No se pudo actualizar el estado del rol'
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