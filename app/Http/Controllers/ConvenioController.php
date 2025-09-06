<?php
// app/Http/Controllers/ConvenioController.php

namespace App\Http\Controllers;

use App\Models\Convenio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ConvenioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de convenios
     */
    public function index(Request $request)
    {
        $query = Convenio::with(['usuarioCreador', 'usuarioCoordinador']);

        // Filtros
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_convenio', $request->get('tipo'));
        }

        if ($request->filled('coordinador')) {
            $query->where('coordinador_convenio', $request->get('coordinador'));
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_firma', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_firma', '<=', $request->get('fecha_hasta'));
        }

        if ($request->filled('vencimiento')) {
            $dias = (int) $request->get('vencimiento');
            $query->porVencer($dias);
        }

        // Ordenamiento
        $orden = $request->get('orden', 'fecha_firma');
        $direccion = $request->get('direccion', 'desc');
        $query->orderBy($orden, $direccion);

        $convenios = $query->paginate(15);

        // Datos para filtros
        $tipos = Convenio::getTiposConvenio();
        $coordinadores = Convenio::getCoordinadores();
        $estados = Convenio::getEstados();

        return view('convenios.index', compact(
            'convenios', 
            'tipos', 
            'coordinadores', 
            'estados'
        ));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $tipos = Convenio::getTiposConvenio();
        $coordinadores = Convenio::getCoordinadores();
        $usuarios = Usuario::activos()->orderBy('nombre')->get();

        return view('convenios.create', compact('tipos', 'coordinadores', 'usuarios'));
    }

    /**
     * Guardar nuevo convenio
     */
    public function store(Request $request)
    {
        $rules = [
            'institucion_contraparte' => 'required|string|max:255',
            'tipo_convenio' => ['required', 'string', Rule::in(array_keys(Convenio::getTiposConvenio()))],
            'objeto' => 'required|string',
            'fecha_firma' => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'nullable|date|after:fecha_firma',
            'vigencia_indefinida' => 'boolean',
            'coordinador_convenio' => ['required', 'string', Rule::in(array_keys(Convenio::getCoordinadores()))],
            'pais_region' => 'required|string|max:100',
            'signatarios' => 'required|array|min:1',
            'signatarios.*' => 'required|string|max:255',
            'archivo_convenio' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'dictamen_numero' => 'nullable|string|max:100',
            'version_final_firmada' => 'boolean',
            'usuario_coordinador_id' => 'nullable|exists:usuarios,id',
            'observaciones' => 'nullable|string'
        ];

        $messages = [
            'institucion_contraparte.required' => 'La institución contraparte es obligatoria.',
            'tipo_convenio.required' => 'El tipo de convenio es obligatorio.',
            'tipo_convenio.in' => 'El tipo de convenio seleccionado no es válido.',
            'objeto.required' => 'El objeto del convenio es obligatorio.',
            'fecha_firma.required' => 'La fecha de firma es obligatoria.',
            'fecha_firma.before_or_equal' => 'La fecha de firma no puede ser posterior a hoy.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de firma.',
            'coordinador_convenio.required' => 'El coordinador del convenio es obligatorio.',
            'coordinador_convenio.in' => 'El coordinador seleccionado no es válido.',
            'pais_region.required' => 'El país o región es obligatorio.',
            'signatarios.required' => 'Debe especificar al menos un signatario.',
            'signatarios.*.required' => 'Todos los signatarios son obligatorios.',
            'archivo_convenio.mimes' => 'El archivo debe ser un PDF.',
            'archivo_convenio.max' => 'El archivo no puede superar los 10MB.',
            'usuario_coordinador_id.exists' => 'El usuario coordinador seleccionado no es válido.'
        ];

        $request->validate($rules, $messages);

        // Validación condicional de vigencia
        if (!$request->boolean('vigencia_indefinida') && !$request->filled('fecha_vencimiento')) {
            return back()->withErrors([
                'fecha_vencimiento' => 'La fecha de vencimiento es obligatoria si no es vigencia indefinida.'
            ])->withInput();
        }

        $data = $request->except(['archivo_convenio']);
        $data['usuario_creador_id'] = Auth::id();
        $data['signatarios'] = array_filter($request->get('signatarios', []));

        // Manejar archivo
        if ($request->hasFile('archivo_convenio')) {
            $file = $request->file('archivo_convenio');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('convenios', $filename, 'public');
            
            $data['archivo_convenio_path'] = $path;
            $data['archivo_convenio_nombre'] = $file->getClientOriginalName();
            $data['archivo_convenio_size'] = $file->getSize();
        }

        // Determinar estado inicial
        if ($request->boolean('version_final_firmada') && $request->filled('dictamen_numero')) {
            $data['estado'] = 'pendiente_aprobacion';
        } else {
            $data['estado'] = 'borrador';
        }

        $convenio = Convenio::create($data);

        return redirect()->route('convenios.index')
                        ->with('success', "Convenio {$convenio->numero_convenio} creado correctamente.");
    }

    /**
     * Mostrar convenio específico
     */
    public function show(Convenio $convenio)
    {
        $convenio->load(['usuarioCreador', 'usuarioCoordinador', 'usuarioAprobador']);
        
        return view('convenios.show', compact('convenio'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Convenio $convenio)
    {
        // Verificar permisos de edición
        if (!$convenio->puedeSerEditado()) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'Este convenio no puede ser editado en su estado actual.');
        }

        $tipos = Convenio::getTiposConvenio();
        $coordinadores = Convenio::getCoordinadores();
        $usuarios = Usuario::activos()->orderBy('nombre')->get();

        return view('convenios.edit', compact('convenio', 'tipos', 'coordinadores', 'usuarios'));
    }

    /**
     * Actualizar convenio
     */
    public function update(Request $request, Convenio $convenio)
    {
        // Verificar permisos de edición
        if (!$convenio->puedeSerEditado()) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'Este convenio no puede ser editado en su estado actual.');
        }

        $rules = [
            'institucion_contraparte' => 'required|string|max:255',
            'tipo_convenio' => ['required', 'string', Rule::in(array_keys(Convenio::getTiposConvenio()))],
            'objeto' => 'required|string',
            'fecha_firma' => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'nullable|date|after:fecha_firma',
            'vigencia_indefinida' => 'boolean',
            'coordinador_convenio' => ['required', 'string', Rule::in(array_keys(Convenio::getCoordinadores()))],
            'pais_region' => 'required|string|max:100',
            'signatarios' => 'required|array|min:1',
            'signatarios.*' => 'required|string|max:255',
            'archivo_convenio' => 'nullable|file|mimes:pdf|max:10240',
            'dictamen_numero' => 'nullable|string|max:100',
            'version_final_firmada' => 'boolean',
            'usuario_coordinador_id' => 'nullable|exists:usuarios,id',
            'observaciones' => 'nullable|string'
        ];

        $messages = [
            'institucion_contraparte.required' => 'La institución contraparte es obligatoria.',
            'tipo_convenio.required' => 'El tipo de convenio es obligatorio.',
            'tipo_convenio.in' => 'El tipo de convenio seleccionado no es válido.',
            'objeto.required' => 'El objeto del convenio es obligatorio.',
            'fecha_firma.required' => 'La fecha de firma es obligatoria.',
            'fecha_firma.before_or_equal' => 'La fecha de firma no puede ser posterior a hoy.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de firma.',
            'coordinador_convenio.required' => 'El coordinador del convenio es obligatorio.',
            'coordinador_convenio.in' => 'El coordinador seleccionado no es válido.',
            'pais_region.required' => 'El país o región es obligatorio.',
            'signatarios.required' => 'Debe especificar al menos un signatario.',
            'signatarios.*.required' => 'Todos los signatarios son obligatorios.',
            'archivo_convenio.mimes' => 'El archivo debe ser un PDF.',
            'archivo_convenio.max' => 'El archivo no puede superar los 10MB.',
            'usuario_coordinador_id.exists' => 'El usuario coordinador seleccionado no es válido.'
        ];

        $request->validate($rules, $messages);

        // Validación condicional de vigencia
        if (!$request->boolean('vigencia_indefinida') && !$request->filled('fecha_vencimiento')) {
            return back()->withErrors([
                'fecha_vencimiento' => 'La fecha de vencimiento es obligatoria si no es vigencia indefinida.'
            ])->withInput();
        }

        $data = $request->except(['archivo_convenio']);
        $data['signatarios'] = array_filter($request->get('signatarios', []));

        // Manejar archivo nuevo
        if ($request->hasFile('archivo_convenio')) {
            // Eliminar archivo anterior si existe
            if ($convenio->archivo_convenio_path && Storage::disk('public')->exists($convenio->archivo_convenio_path)) {
                Storage::disk('public')->delete($convenio->archivo_convenio_path);
            }
            
            $file = $request->file('archivo_convenio');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('convenios', $filename, 'public');
            
            $data['archivo_convenio_path'] = $path;
            $data['archivo_convenio_nombre'] = $file->getClientOriginalName();
            $data['archivo_convenio_size'] = $file->getSize();
        }

        // Actualizar estado si corresponde
        if ($request->boolean('version_final_firmada') && $request->filled('dictamen_numero')) {
            $data['estado'] = 'pendiente_aprobacion';
        } elseif ($convenio->estado === 'pendiente_aprobacion') {
            $data['estado'] = 'borrador';
        }

        $convenio->update($data);

        return redirect()->route('convenios.show', $convenio)
                        ->with('success', 'Convenio actualizado correctamente.');
    }

    /**
     * Eliminar convenio
     */
    public function destroy(Convenio $convenio)
    {
        // Solo se pueden eliminar borradores
        if ($convenio->estado !== 'borrador') {
            return redirect()->route('convenios.index')
                           ->with('error', 'Solo se pueden eliminar convenios en estado borrador.');
        }

        // Verificar permisos (solo el creador o super_admin)
        if ($convenio->usuario_creador_id !== Auth::id() && !Auth::user()->tieneRol('super_admin')) {
            return redirect()->route('convenios.index')
                           ->with('error', 'No tiene permisos para eliminar este convenio.');
        }

        $numeroConvenio = $convenio->numero_convenio;

        // Eliminar archivo si existe
        if ($convenio->archivo_convenio_path && Storage::disk('public')->exists($convenio->archivo_convenio_path)) {
            Storage::disk('public')->delete($convenio->archivo_convenio_path);
        }

        $convenio->delete();

        return redirect()->route('convenios.index')
                        ->with('success', "Convenio {$numeroConvenio} eliminado correctamente.");
    }

    /**
     * Aprobar convenio
     */
    public function aprobar(Request $request, Convenio $convenio)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('convenios.aprobar')) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'No tiene permisos para aprobar convenios.');
        }

        if (!$convenio->puedeSerAprobado()) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'Este convenio no puede ser aprobado en su estado actual.');
        }

        $convenio->aprobar(Auth::id());

        return redirect()->route('convenios.show', $convenio)
                        ->with('success', 'Convenio aprobado correctamente.');
    }

    /**
     * Activar convenio
     */
    public function activar(Convenio $convenio)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('convenios.aprobar')) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'No tiene permisos para activar convenios.');
        }

        if (!$convenio->activar()) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'Este convenio no puede ser activado.');
        }

        return redirect()->route('convenios.show', $convenio)
                        ->with('success', 'Convenio activado correctamente.');
    }

    /**
     * Cancelar convenio
     */
    public function cancelar(Request $request, Convenio $convenio)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('convenios.aprobar')) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'No tiene permisos para cancelar convenios.');
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:500'
        ], [
            'motivo_cancelacion.required' => 'Debe especificar el motivo de cancelación.'
        ]);

        if (!$convenio->cancelar($request->get('motivo_cancelacion'))) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'Este convenio no puede ser cancelado.');
        }

        return redirect()->route('convenios.show', $convenio)
                        ->with('success', 'Convenio cancelado correctamente.');
    }

    /**
     * Descargar archivo del convenio
     */
    public function descargarArchivo(Convenio $convenio)
    {
        if (!$convenio->archivo_convenio_path || !Storage::disk('public')->exists($convenio->archivo_convenio_path)) {
            return redirect()->back()->with('error', 'Archivo no encontrado.');
        }

        return Storage::disk('public')->download(
            $convenio->archivo_convenio_path,
            $convenio->archivo_convenio_nombre ?: 'convenio.pdf'
        );
    }

    /**
     * Mostrar convenios pendientes de aprobación
     */
    public function pendientes(Request $request)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('convenios.aprobar')) {
            return redirect()->route('convenios.index')
                           ->with('error', 'No tiene permisos para ver convenios pendientes.');
        }

        $query = Convenio::with(['usuarioCreador', 'usuarioCoordinador'])
                         ->pendientes();

        // Filtros adicionales para pendientes
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('coordinador')) {
            $query->where('coordinador_convenio', $request->get('coordinador'));
        }

        $convenios = $query->orderBy('created_at', 'desc')->paginate(15);
        $coordinadores = Convenio::getCoordinadores();

        return view('convenios.pendientes', compact('convenios', 'coordinadores'));
    }

    /**
     * Exportar listado de convenios
     */
    public function exportar(Request $request)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('reportes.exportar')) {
            return redirect()->route('convenios.index')
                           ->with('error', 'No tiene permisos para exportar convenios.');
        }

        $query = Convenio::with(['usuarioCreador', 'usuarioCoordinador']);

        // Aplicar los mismos filtros que en index
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_convenio', $request->get('tipo'));
        }

        if ($request->filled('coordinador')) {
            $query->where('coordinador_convenio', $request->get('coordinador'));
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_firma', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_firma', '<=', $request->get('fecha_hasta'));
        }

        $convenios = $query->orderBy('fecha_firma', 'desc')->get();

        $filename = 'convenios_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($convenios) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'Número',
                'Institución Contraparte',
                'Tipo',
                'Objeto',
                'Fecha Firma',
                'Fecha Vencimiento',
                'Estado',
                'Coordinador',
                'País/Región',
                'Creador',
                'Fecha Creación'
            ], ';');

            foreach ($convenios as $convenio) {
                fputcsv($file, [
                    $convenio->numero_convenio,
                    $convenio->institucion_contraparte,
                    $convenio->tipo_convenio,
                    substr($convenio->objeto, 0, 100) . (strlen($convenio->objeto) > 100 ? '...' : ''),
                    $convenio->fecha_firma->format('d/m/Y'),
                    $convenio->vigencia_indefinida ? 'Indefinida' : ($convenio->fecha_vencimiento ? $convenio->fecha_vencimiento->format('d/m/Y') : 'No especificada'),
                    $convenio->estado_texto,
                    $convenio->coordinador_convenio,
                    $convenio->pais_region,
                    $convenio->usuarioCreador ? $convenio->usuarioCreador->nombre_completo : 'Usuario eliminado',
                    $convenio->created_at->format('d/m/Y H:i:s')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Estadísticas de convenios para dashboard
     */
    public function estadisticasApi()
    {
        $estadisticas = [
            'total' => Convenio::count(),
            'activos' => Convenio::where('estado', 'activo')->count(),
            'pendientes' => Convenio::where('estado', 'pendiente_aprobacion')->count(),
            'por_vencer' => Convenio::porVencer(30)->count(),
            'vencidos' => Convenio::vencidos()->count(),
            'borradores' => Convenio::where('estado', 'borrador')->count(),
            'aprobados' => Convenio::where('estado', 'aprobado')->count(),
            'cancelados' => Convenio::where('estado', 'cancelado')->count(),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Rechazar convenio (volver a borrador)
     */
    public function rechazar(Request $request, Convenio $convenio)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('convenios.aprobar')) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'No tiene permisos para rechazar convenios.');
        }

        if ($convenio->estado !== 'pendiente_aprobacion') {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'Solo se pueden rechazar convenios pendientes de aprobación.');
        }

        $request->validate([
            'motivo_rechazo' => 'required|string|max:1000'
        ], [
            'motivo_rechazo.required' => 'Debe especificar el motivo del rechazo.'
        ]);

        $observaciones = $convenio->observaciones ? $convenio->observaciones . "\n\n" : '';
        $observaciones .= "RECHAZADO el " . now()->format('d/m/Y H:i:s') . " por " . Auth::user()->nombre_completo;
        $observaciones .= "\nMotivo: " . $request->get('motivo_rechazo');

        $convenio->update([
            'estado' => 'borrador',
            'version_final_firmada' => false,
            'observaciones' => $observaciones
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Convenio rechazado correctamente'
        ]);
    }

    /**
     * Solicitar cambios en un convenio
     */
    public function solicitarCambios(Request $request, Convenio $convenio)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('convenios.aprobar')) {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'No tiene permisos para solicitar cambios en convenios.');
        }

        if ($convenio->estado !== 'pendiente_aprobacion') {
            return redirect()->route('convenios.show', $convenio)
                           ->with('error', 'Solo se pueden solicitar cambios en convenios pendientes de aprobación.');
        }

        $request->validate([
            'cambios_solicitados' => 'required|string|max:2000'
        ], [
            'cambios_solicitados.required' => 'Debe especificar los cambios solicitados.'
        ]);

        $observaciones = $convenio->observaciones ? $convenio->observaciones . "\n\n" : '';
        $observaciones .= "CAMBIOS SOLICITADOS el " . now()->format('d/m/Y H:i:s') . " por " . Auth::user()->nombre_completo;
        $observaciones .= "\nCambios requeridos: " . $request->get('cambios_solicitados');

        $convenio->update([
            'estado' => 'borrador',
            'version_final_firmada' => false,
            'observaciones' => $observaciones
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cambios solicitados correctamente'
        ]);
    }

    /**
     * Cambiar estado de un convenio
     */
    public function cambiarEstado(Request $request, Convenio $convenio)
    {
        // Verificar permisos
        if (!Auth::user()->tienePermiso('convenios.aprobar')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para cambiar el estado de convenios'
            ], 403);
        }

        $request->validate([
            'estado' => ['required', 'string', Rule::in(array_keys(Convenio::getEstados()))]
        ]);

        $estadoAnterior = $convenio->estado;
        $nuevoEstado = $request->get('estado');

        // Validaciones específicas según el estado
        switch ($nuevoEstado) {
            case 'aprobado':
                if (!$convenio->puedeSerAprobado()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este convenio no puede ser aprobado en su estado actual'
                    ], 400);
                }
                $convenio->aprobar(Auth::id());
                break;

            case 'activo':
                if ($convenio->estado !== 'aprobado') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Solo se pueden activar convenios aprobados'
                    ], 400);
                }
                $convenio->activar();
                break;

            case 'cancelado':
                if (!$convenio->puedeSerCancelado()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este convenio no puede ser cancelado'
                    ], 400);
                }
                $convenio->cancelar('Cancelado desde el sistema');
                break;

            default:
                $convenio->update(['estado' => $nuevoEstado]);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Estado cambiado de {$estadoAnterior} a {$nuevoEstado}",
            'nuevo_estado' => $convenio->fresh()->estado,
            'nuevo_estado_texto' => $convenio->fresh()->estado_texto,
            'nuevo_estado_badge' => $convenio->fresh()->estado_badge
        ]);
    }
}