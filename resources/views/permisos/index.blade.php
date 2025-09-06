{{-- resources/views/permisos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Permisos')
@section('page-title', 'Gestión de Permisos')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Permisos</li>
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
                <form method="GET" action="{{ route('permisos.index') }}" class="form-inline">
                    <div class="row w-100">
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="buscar" class="form-control" 
                                       placeholder="Buscar por nombre o descripción..."
                                       value="{{ request('buscar') }}">
                            </div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="modulo" class="form-control">
                                <option value="">Todos los módulos</option>
                                @foreach($modulos as $modulo)
                                    <option value="{{ $modulo }}" 
                                            {{ request('modulo') === $modulo ? 'selected' : '' }}>
                                        {{ ucfirst($modulo) }}
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
                            <a href="{{ route('permisos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de permisos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Lista de Permisos ({{ $permisos->total() }} registros)
                </h3>
                <div class="card-tools">
                    <a href="{{ route('permisos.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Permiso
                    </a>
                    <a href="{{ route('permisos.gestion-masiva') }}" class="btn btn-warning btn-sm ml-1">
                        <i class="fas fa-layer-group mr-1"></i>
                        Creación Masiva
                    </a>
                    <a href="{{ route('permisos.export', request()->query()) }}" class="btn btn-info btn-sm ml-1">
                        <i class="fas fa-download mr-1"></i>
                        Exportar CSV
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($permisos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Permiso</th>
                                <th>Módulo</th>
                                <th>Estado</th>
                                <th>Roles Asignados</th>
                                <th>Fecha Creación</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permisos as $permiso)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="permission-icon mr-2">
                                            <i class="fas fa-key fa-lg text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $permiso->descripcion }}</strong>
                                            <br>
                                            <small class="text-muted text-monospace">{{ $permiso->nombre }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <i class="fas fa-folder mr-1"></i>
                                        {{ ucfirst($permiso->modulo) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input toggle-estado" 
                                               id="estado{{ $permiso->id }}"
                                               data-id="{{ $permiso->id }}"
                                               {{ $permiso->activo ? 'checked' : '' }}>
                                        <label class="custom-control-label" 
                                               for="estado{{ $permiso->id }}">
                                            {{ $permiso->activo ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    @if($permiso->roles_count > 0)
                                        <span class="badge badge-secondary">
                                            {{ $permiso->roles_count }} 
                                            {{ $permiso->roles_count === 1 ? 'rol' : 'roles' }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $permiso->created_at ? $permiso->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Ver detalles --}}
                                        <a href="{{ route('permisos.show', $permiso) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        {{-- Editar --}}
                                        <a href="{{ route('permisos.edit', $permiso) }}" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Eliminar - solo si no tiene roles asignados --}}
                                        @if($permiso->roles_count === 0)
                                        <form method="POST" 
                                              action="{{ route('permisos.destroy', $permiso) }}" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm btn-delete" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" 
                                                class="btn btn-danger btn-sm" 
                                                title="No se puede eliminar - tiene roles asignados"
                                                disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron permisos</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'modulo', 'estado']))
                            Intenta modificar los filtros de búsqueda.
                        @else
                            Comienza creando el primer permiso.
                        @endif
                    </p>
                </div>
                @endif
            </div>
            
            @if($permisos->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando {{ $permisos->firstItem() }} a {{ $permisos->lastItem() }} 
                            de {{ $permisos->total() }} resultados
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $permisos->appends(request()->query())->links() }}
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
    // Toggle estado permiso via AJAX
    $('.toggle-estado').change(function() {
        var checkbox = $(this);
        var permisoId = checkbox.data('id');
        var nuevoEstado = checkbox.is(':checked');
        
        $.ajax({
            url: '/permisos/' + permisoId + '/toggle-estado',
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
                    text: response.message || 'No se pudo actualizar el estado del permiso'
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