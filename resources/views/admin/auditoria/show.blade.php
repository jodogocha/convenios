{{-- resources/views/admin/auditoria/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalle de Auditoría')
@section('page-title', 'Detalle de Auditoría')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('auditoria.index') }}">Auditoría</a></li>
<li class="breadcrumb-item active">Detalle #{{ $auditoria->id }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Información Principal -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="{{ $auditoria->icono_accion }} mr-2"></i>
                    {{ $auditoria->descripcion_accion }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-secondary">#{{ $auditoria->id }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-info-circle mr-1"></i>
                            Información General
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Acción:</td>
                                <td>
                                    <i class="{{ $auditoria->icono_accion }} mr-1"></i>
                                    {{ $auditoria->descripcion_accion }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Usuario:</td>
                                <td>
                                    {{ $auditoria->nombre_usuario }}
                                    @if($auditoria->usuario)
                                        <br>
                                        <small class="text-muted">({{ $auditoria->usuario->username }})</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Fecha y Hora:</td>
                                <td>
                                    {{ $auditoria->fecha_hora->format('d/m/Y H:i:s') }}
                                    <br>
                                    <small class="text-muted">{{ $auditoria->fecha_hora->diffForHumans() }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Tabla Afectada:</td>
                                <td>
                                    @if($auditoria->tabla_afectada)
                                        <span class="badge badge-info">{{ ucfirst($auditoria->tabla_afectada) }}</span>
                                    @else
                                        <span class="text-muted">No especificada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">ID del Registro:</td>
                                <td>
                                    @if($auditoria->registro_id)
                                        <span class="text-monospace">#{{ $auditoria->registro_id }}</span>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-globe mr-1"></i>
                            Información de Conexión
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="font-weight-bold">Dirección IP:</td>
                                <td>
                                    @if($auditoria->ip_address)
                                        <span class="text-monospace">{{ $auditoria->ip_address }}</span>
                                    @else
                                        <span class="text-muted">No registrada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">User Agent:</td>
                                <td>
                                    @if($auditoria->user_agent)
                                        <small class="text-muted">{{ $auditoria->user_agent }}</small>
                                    @else
                                        <span class="text-muted">No registrado</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cambios Realizados -->
        @if($auditoria->valores_anteriores || $auditoria->valores_nuevos)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Cambios Realizados
                </h3>
            </div>
            <div class="card-body">
                @if($auditoria->accion === 'crear_usuario' && $auditoria->valores_nuevos)
                    <h6 class="text-success">
                        <i class="fas fa-plus-circle mr-1"></i>
                        Datos del nuevo registro:
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditoria->valores_nuevos as $campo => $valor)
                                    @if(!in_array($campo, ['password', 'remember_token', 'updated_at']))
                                    <tr>
                                        <td class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $campo)) }}</td>
                                        <td>
                                            @if($campo === 'rol_id')
                                                {{ \App\Models\Rol::find($valor)->descripcion ?? 'Rol eliminado' }}
                                            @elseif($campo === 'activo')
                                                <span class="badge badge-{{ $valor ? 'success' : 'danger' }}">
                                                    {{ $valor ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            @elseif($campo === 'email_verificado')
                                                <span class="badge badge-{{ $valor ? 'success' : 'warning' }}">
                                                    {{ $valor ? 'Verificado' : 'No verificado' }}
                                                </span>
                                            @else
                                                {{ $valor ?: '(vacío)' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @elseif($auditoria->accion === 'actualizar_usuario' && $auditoria->valores_anteriores && $auditoria->valores_nuevos)
                    <h6 class="text-warning">
                        <i class="fas fa-edit mr-1"></i>
                        Cambios realizados:
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor Anterior</th>
                                    <th>Valor Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditoria->valores_nuevos as $campo => $valorNuevo)
                                    @if(isset($auditoria->valores_anteriores[$campo]) && !in_array($campo, ['password', 'remember_token', 'updated_at']))
                                    <tr>
                                        <td class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $campo)) }}</td>
                                        <td>
                                            @if($campo === 'rol_id')
                                                {{ \App\Models\Rol::find($auditoria->valores_anteriores[$campo])->descripcion ?? 'Rol eliminado' }}
                                            @elseif($campo === 'activo')
                                                <span class="badge badge-{{ $auditoria->valores_anteriores[$campo] ? 'success' : 'danger' }}">
                                                    {{ $auditoria->valores_anteriores[$campo] ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            @elseif($campo === 'email_verificado')
                                                <span class="badge badge-{{ $auditoria->valores_anteriores[$campo] ? 'success' : 'warning' }}">
                                                    {{ $auditoria->valores_anteriores[$campo] ? 'Verificado' : 'No verificado' }}
                                                </span>
                                            @else
                                                {{ $auditoria->valores_anteriores[$campo] ?: '(vacío)' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($campo === 'rol_id')
                                                {{ \App\Models\Rol::find($valorNuevo)->descripcion ?? 'Rol eliminado' }}
                                            @elseif($campo === 'activo')
                                                <span class="badge badge-{{ $valorNuevo ? 'success' : 'danger' }}">
                                                    {{ $valorNuevo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            @elseif($campo === 'email_verificado')
                                                <span class="badge badge-{{ $valorNuevo ? 'success' : 'warning' }}">
                                                    {{ $valorNuevo ? 'Verificado' : 'No verificado' }}
                                                </span>
                                            @else
                                                {{ $valorNuevo ?: '(vacío)' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @elseif($auditoria->accion === 'eliminar_usuario' && $auditoria->valores_anteriores)
                    <h6 class="text-danger">
                        <i class="fas fa-trash mr-1"></i>
                        Datos del registro eliminado:
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditoria->valores_anteriores as $campo => $valor)
                                    @if(!in_array($campo, ['password', 'remember_token', 'updated_at']))
                                    <tr>
                                        <td class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $campo)) }}</td>
                                        <td>
                                            @if($campo === 'rol_id')
                                                {{ \App\Models\Rol::find($valor)->descripcion ?? 'Rol eliminado' }}
                                            @elseif($campo === 'activo')
                                                <span class="badge badge-{{ $valor ? 'success' : 'danger' }}">
                                                    {{ $valor ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            @elseif($campo === 'email_verificado')
                                                <span class="badge badge-{{ $valor ? 'success' : 'warning' }}">
                                                    {{ $valor ? 'Verificado' : 'No verificado' }}
                                                </span>
                                            @else
                                                {{ $valor ?: '(vacío)' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        No hay datos de cambios disponibles para esta acción.
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Acciones -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-2"></i>
                    Acciones
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('auditoria.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Lista
                    </a>
                    
                    @if($auditoria->usuario)
                    <a href="{{ route('usuarios.show', $auditoria->usuario) }}" class="btn btn-info btn-block">
                        <i class="fas fa-user mr-1"></i>
                        Ver Usuario
                    </a>
                    @endif
                    
                    @if($auditoria->tabla_afectada === 'usuarios' && $auditoria->registro_id)
                        @php
                            $usuarioAfectado = \App\Models\Usuario::find($auditoria->registro_id);
                        @endphp
                        @if($usuarioAfectado)
                        <a href="{{ route('usuarios.show', $usuarioAfectado) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-user-edit mr-1"></i>
                            Ver Usuario Afectado
                        </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Información Técnica -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-code mr-2"></i>
                    Información Técnica
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="font-weight-bold">ID de Auditoría:</td>
                        <td><span class="text-monospace">#{{ $auditoria->id }}</span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Acción Sistema:</td>
                        <td><span class="text-monospace">{{ $auditoria->accion }}</span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Timestamp:</td>
                        <td><span class="text-monospace">{{ $auditoria->fecha_hora->timestamp }}</span></td>
                    </tr>
                    @if($auditoria->usuario_id)
                    <tr>
                        <td class="font-weight-bold">ID Usuario:</td>
                        <td><span class="text-monospace">#{{ $auditoria->usuario_id }}</span></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Auditorías Relacionadas -->
        @if($auditoria->registro_id && $auditoria->tabla_afectada)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-link mr-2"></i>
                    Auditorías Relacionadas
                </h3>
            </div>
            <div class="card-body">
                @php
                    $relacionadas = \App\Models\Auditoria::where('tabla_afectada', $auditoria->tabla_afectada)
                        ->where('registro_id', $auditoria->registro_id)
                        ->where('id', '!=', $auditoria->id)
                        ->orderBy('fecha_hora', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($relacionadas->count() > 0)
                    <div class="timeline">
                        @foreach($relacionadas as $relacionada)
                        <div class="time-label">
                            <span class="bg-secondary">{{ $relacionada->fecha_hora->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <i class="{{ $relacionada->icono_accion }}"></i>
                            <div class="timeline-item">
                                <span class="time">{{ $relacionada->fecha_hora->format('H:i') }}</span>
                                <h3 class="timeline-header">
                                    <a href="{{ route('auditoria.show', $relacionada) }}">
                                        {{ $relacionada->descripcion_accion }}
                                    </a>
                                </h3>
                                <div class="timeline-body">
                                    <small>Por: {{ $relacionada->nombre_usuario }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No hay otras auditorías relacionadas.</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 18px;
    height: 100%;
    width: 4px;
    background: #dee2e6;
}

.timeline > div {
    position: relative;
    margin: 0 0 15px 0;
}

.timeline > div > .timeline-item {
    box-shadow: 0 1px 1px rgba(0,0,0,.1);
    border-radius: .25rem;
    background: #fff;
    margin-left: 45px;
    margin-right: 15px;
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
</style>
@endpush