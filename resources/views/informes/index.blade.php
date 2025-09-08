{{-- resources/views/informes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Informes')
@section('page-title', 'Gestión de Informes')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Informes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filtros -->
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filtros de búsqueda
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none;">
                <form method="GET" action="{{ route('informes.index') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="buscar">Buscar</label>
                            <input type="text" name="buscar" class="form-control" 
                                   placeholder="Buscar por institución, carrera, etc."
                                   value="{{ request('buscar') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                @foreach($estados as $valor => $nombre)
                                    <option value="{{ $valor }}" 
                                            {{ request('estado') == $valor ? 'selected' : '' }}>
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="convenio_id">Convenio</label>
                            <select name="convenio_id" class="form-control">
                                <option value="">Todos los convenios</option>
                                @foreach($convenios as $convenio)
                                    <option value="{{ $convenio->id }}" 
                                            {{ request('convenio_id') == $convenio->id ? 'selected' : '' }}>
                                        {{ $convenio->numero_convenio }} - {{ $convenio->institucion_contraparte }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="unidad_academica">Unidad Académica</label>
                            <select name="unidad_academica" class="form-control">
                                <option value="">Todas las unidades</option>
                                @foreach($unidadesAcademicas as $valor => $nombre)
                                    <option value="{{ $valor }}" 
                                            {{ request('unidad_academica') == $valor ? 'selected' : '' }}>
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search mr-1"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="fecha_desde">Fecha desde</label>
                            <input type="date" name="fecha_desde" class="form-control" 
                                   value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="fecha_hasta">Fecha hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" 
                                   value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <a href="{{ route('informes.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times mr-1"></i>Limpiar
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <a href="{{ route('informes.exportar-excel') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                                   class="btn btn-success btn-block">
                                    <i class="fas fa-file-excel mr-1"></i>Exportar Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $informes->total() }}</h3>
                        <p>Total de Informes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $informes->where('estado', 'enviado')->count() }}</h3>
                        <p>Pendientes de Revisión</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $informes->where('estado', 'aprobado')->count() }}</h3>
                        <p>Aprobados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $informes->where('estado', 'borrador')->count() }}</h3>
                        <p>Borradores</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de informes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    Lista de Informes ({{ $informes->total() }} registros)
                </h3>
                <div class="card-tools">
                    <a href="{{ route('informes.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Informe
                    </a>
                    @if(Auth::user()->tienePermiso('informes.aprobar'))
                    <a href="{{ route('informes.pendientes') }}" class="btn btn-warning btn-sm ml-1">
                        <i class="fas fa-clock mr-1"></i>
                        Pendientes
                        <span class="badge badge-light ml-1">
                            {{ $informes->where('estado', 'enviado')->count() }}
                        </span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($informes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Convenio</th>
                                <th>Institución</th>
                                <th>Unidad Académica</th>
                                <th>Periodo Evaluado</th>
                                <th>Estado</th>
                                <th>Fecha Presentación</th>
                                <th>Creado por</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($informes as $informe)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">#{{ $informe->id }}</span>
                                </td>
                                <td>
                                    @if($informe->convenio)
                                        <div>
                                            <strong>{{ $informe->convenio->numero_convenio }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($informe->convenio->institucion_contraparte, 30) }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-danger">Convenio eliminado</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($informe->institucion_co_celebrante, 25) }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $informe->unidad_academica }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $informe->carrera }}</small>
                                    </div>
                                </td>
                                <td>
                                    <small>{{ $informe->periodo_completo }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $informe->estado_badge }}">
                                        {{ $informe->estado_texto }}
                                    </span>
                                    @if($informe->convenio_ejecutado)
                                        <br><small class="text-success">
                                            <i class="fas fa-check mr-1"></i>Ejecutado
                                        </small>
                                    @else
                                        <br><small class="text-warning">
                                            <i class="fas fa-times mr-1"></i>No ejecutado
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $informe->fecha_presentacion->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $informe->fecha_presentacion->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $informe->usuarioCreador ? $informe->usuarioCreador->nombre_completo : 'Usuario eliminado' }}
                                        @if($informe->usuarioRevisor)
                                            <br>
                                            <small class="text-muted">
                                                Rev: {{ $informe->usuarioRevisor->nombre_completo }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Ver detalles --}}
                                        <a href="{{ route('informes.show', $informe) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        {{-- Editar --}}
                                        @if($informe->puedeSerEditado() && ($informe->usuario_creador_id === Auth::id() || Auth::user()->tieneRol('super_admin')))
                                        <a href="{{ route('informes.edit', $informe) }}" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif

                                        {{-- Exportar PDF --}}
                                        <a href="{{ route('informes.exportar-pdf', $informe) }}" 
                                           class="btn btn-danger btn-sm" title="Exportar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                        {{-- Duplicar --}}
                                        <a href="{{ route('informes.duplicar', $informe) }}" 
                                           class="btn btn-secondary btn-sm" title="Duplicar">
                                            <i class="fas fa-copy"></i>
                                        </a>

                                        {{-- Eliminar - solo borradores del creador --}}
                                        @if($informe->estado === 'borrador' && ($informe->usuario_creador_id === Auth::id() || Auth::user()->tieneRol('super_admin')))
                                        <form method="POST" 
                                              action="{{ route('informes.destroy', $informe) }}" 
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
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay informes disponibles</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'estado', 'convenio_id', 'unidad_academica', 'fecha_desde', 'fecha_hasta']))
                            Intenta modificar los filtros de búsqueda.
                        @else
                            Comienza creando el primer informe.
                        @endif
                    </p>
                    <a href="{{ route('informes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Crear Primer Informe
                    </a>
                </div>
                @endif
            </div>
            
            @if($informes->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando {{ $informes->firstItem() }} a {{ $informes->lastItem() }} 
                            de {{ $informes->total() }} resultados
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $informes->appends(request()->query())->links() }}
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

    // Auto-colapsar filtros si no hay filtros aplicados
    @if(!request()->hasAny(['buscar', 'estado', 'convenio_id', 'unidad_academica', 'fecha_desde', 'fecha_hasta']))
    // Los filtros permanecen colapsados por defecto
    @else
    // Expandir filtros si hay filtros aplicados
    $('[data-card-widget="collapse"]').click();
    @endif
});
</script>
@endpush