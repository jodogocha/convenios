<?php
// app/Http/Controllers/InformeController.php

namespace App\Http\Controllers;

use App\Models\Informe;
use App\Models\Convenio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InformesExport;

class InformeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de informes
     */
    public function index(Request $request)
    {
        $query = Informe::with(['convenio', 'usuarioCreador', 'usuarioRevisor']);

        // Filtros
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('convenio_id')) {
            $query->where('convenio_id', $request->get('convenio_id'));
        }

        if ($request->filled('unidad_academica')) {
            $query->where('unidad_academica', $request->get('unidad_academica'));
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_presentacion', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_presentacion', '<=', $request->get('fecha_hasta'));
        }

        // Ordenamiento
        $orden = $request->get('orden', 'fecha_presentacion');
        $direccion = $request->get('direccion', 'desc');
        $query->orderBy($orden, $direccion);

        $informes = $query->paginate(15);

        // Datos para filtros
        $estados = Informe::getEstados();
        $unidadesAcademicas = Informe::getUnidadesAcademicas();
        $convenios = Convenio::select('id', 'numero_convenio', 'institucion_contraparte')
                            ->orderBy('numero_convenio')
                            ->get();

        return view('informes.index', compact(
            'informes', 
            'estados', 
            'unidadesAcademicas',
            'convenios'
        ));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(Request $request)
    {
        $convenioId = $request->get('convenio_id');
        $convenioSeleccionado = null;
        
        if ($convenioId) {
            $convenioSeleccionado = Convenio::findOrFail($convenioId);
        }

        $convenios = Convenio::where('estado', 'activo')
                            ->orderBy('numero_convenio')
                            ->get();
        
        $tiposConvenio = Informe::getTiposConvenio();
        $unidadesAcademicas = Informe::getUnidadesAcademicas();

        return view('informes.create', compact(
            'convenios', 
            'convenioSeleccionado',
            'tiposConvenio', 
            'unidadesAcademicas'
        ));
    }

    /**
     * Guardar nuevo informe
     */
    public function store(Request $request)
    {
        $rules = [
            'convenio_id' => 'required|exists:convenios,id',
            'institucion_co_celebrante' => 'required|string|max:255',
            'unidad_academica' => 'required|string|max:255',
            'carrera' => 'required|string|max:255',
            'fecha_celebracion' => 'required|date',
            'vigencia' => 'nullable|string|max:100',
            'periodo_evaluado' => 'required|string|max:500',
            'periodo_desde' => 'nullable|date',
            'periodo_hasta' => 'nullable|date|after_or_equal:periodo_desde',
            'dependencia_responsable' => 'required|string|max:255',
            'coordinadores_designados' => 'required|array|min:1',
            'coordinadores_designados.*' => 'required|string|max:255',
            'convenio_celebrado_propuesta' => 'required|string|max:500',
            'tipo_convenio' => ['required', Rule::in(array_keys(Informe::getTiposConvenio()))],
            'convenio_ejecutado' => 'required|boolean',
            'enlace_google_drive' => 'required|url',
            'fecha_presentacion' => 'required|date',
        ];

        // Reglas condicionales según si el convenio se ejecutó
        if ($request->boolean('convenio_ejecutado')) {
            $rules += [
                'numero_actividades_realizadas' => 'required|integer|min:0',
                'logros_obtenidos' => 'required|string',
                'beneficios_alcanzados' => 'required|string',
                'dificultades_incidentes' => 'nullable|string',
                'responsabilidad_instalaciones' => 'nullable|string',
                'sugerencias_mejoras' => 'nullable|string',
                'informacion_complementaria' => 'nullable|string',
            ];
        } else {
            $rules += [
                'motivos_no_ejecucion' => 'required|string',
                'propuestas_mejoras' => 'nullable|string',
                'informacion_complementaria_no_ejecutado' => 'nullable|string',
            ];
        }

        $messages = [
            'convenio_id.required' => 'Debe seleccionar un convenio.',
            'convenio_id.exists' => 'El convenio seleccionado no es válido.',
            'institucion_co_celebrante.required' => 'La institución co-celebrante es obligatoria.',
            'unidad_academica.required' => 'La unidad académica es obligatoria.',
            'carrera.required' => 'La carrera es obligatoria.',
            'fecha_celebracion.required' => 'La fecha de celebración es obligatoria.',
            'periodo_evaluado.required' => 'El periodo evaluado es obligatorio.',
            'periodo_hasta.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'dependencia_responsable.required' => 'La dependencia responsable es obligatoria.',
            'coordinadores_designados.required' => 'Debe especificar al menos un coordinador.',
            'coordinadores_designados.*.required' => 'Todos los coordinadores son obligatorios.',
            'convenio_celebrado_propuesta.required' => 'Debe especificar la propuesta del convenio.',
            'tipo_convenio.required' => 'El tipo de convenio es obligatorio.',
            'enlace_google_drive.required' => 'El enlace de Google Drive con evidencias es obligatorio.',
            'enlace_google_drive.url' => 'El enlace de Google Drive debe ser una URL válida.',
            'fecha_presentacion.required' => 'La fecha de presentación es obligatoria.',
            'numero_actividades_realizadas.required' => 'El número de actividades realizadas es obligatorio.',
            'logros_obtenidos.required' => 'Los logros obtenidos son obligatorios.',
            'beneficios_alcanzados.required' => 'Los beneficios alcanzados son obligatorios.',
            'motivos_no_ejecucion.required' => 'Los motivos de no ejecución son obligatorios.',
        ];

        $request->validate($rules, $messages);

        $data = $request->except(['firmas']);
        $data['usuario_creador_id'] = Auth::id();
        $data['coordinadores_designados'] = array_filter($request->get('coordinadores_designados', []));
        
        // Manejar firmas
        if ($request->filled('firmas')) {
            $data['firmas'] = array_filter($request->get('firmas', []));
        }

        $informe = Informe::create($data);

        return redirect()->route('informes.show', $informe)
                        ->with('success', 'Informe creado correctamente.');
    }

    /**
     * Mostrar informe específico
     */
    public function show(Informe $informe)
    {
        $informe->load(['convenio', 'usuarioCreador', 'usuarioRevisor']);
        
        return view('informes.show', compact('informe'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Informe $informe)
    {
        if (!$informe->puedeSerEditado()) {
            return redirect()->route('informes.show', $informe)
                           ->with('error', 'Este informe no puede ser editado en su estado actual.');
        }

        $convenios = Convenio::where('estado', 'activo')
                            ->orderBy('numero_convenio')
                            ->get();
        
        $tiposConvenio = Informe::getTiposConvenio();
        $unidadesAcademicas = Informe::getUnidadesAcademicas();

        return view('informes.edit', compact(
            'informe',
            'convenios', 
            'tiposConvenio', 
            'unidadesAcademicas'
        ));
    }

    /**
     * Actualizar informe
     */
    public function update(Request $request, Informe $informe)
    {
        if (!$informe->puedeSerEditado()) {
            return redirect()->route('informes.show', $informe)
                           ->with('error', 'Este informe no puede ser editado en su estado actual.');
        }

        $rules = [
            'convenio_id' => 'required|exists:convenios,id',
            'institucion_co_celebrante' => 'required|string|max:255',
            'unidad_academica' => 'required|string|max:255',
            'carrera' => 'required|string|max:255',
            'fecha_celebracion' => 'required|date',
            'vigencia' => 'nullable|string|max:100',
            'periodo_evaluado' => 'required|string|max:500',
            'periodo_desde' => 'nullable|date',
            'periodo_hasta' => 'nullable|date|after_or_equal:periodo_desde',
            'dependencia_responsable' => 'required|string|max:255',
            'coordinadores_designados' => 'required|array|min:1',
            'coordinadores_designados.*' => 'required|string|max:255',
            'convenio_celebrado_propuesta' => 'required|string|max:500',
            'tipo_convenio' => ['required', Rule::in(array_keys(Informe::getTiposConvenio()))],
            'convenio_ejecutado' => 'required|boolean',
            'enlace_google_drive' => 'required|url',
            'fecha_presentacion' => 'required|date',
        ];

        // Reglas condicionales
        if ($request->boolean('convenio_ejecutado')) {
            $rules += [
                'numero_actividades_realizadas' => 'required|integer|min:0',
                'logros_obtenidos' => 'required|string',
                'beneficios_alcanzados' => 'required|string',
                'dificultades_incidentes' => 'nullable|string',
                'responsabilidad_instalaciones' => 'nullable|string',
                'sugerencias_mejoras' => 'nullable|string',
                'informacion_complementaria' => 'nullable|string',
            ];
        } else {
            $rules += [
                'motivos_no_ejecucion' => 'required|string',
                'propuestas_mejoras' => 'nullable|string',
                'informacion_complementaria_no_ejecutado' => 'nullable|string',
            ];
        }

        $request->validate($rules);

        $data = $request->except(['firmas']);
        $data['coordinadores_designados'] = array_filter($request->get('coordinadores_designados', []));
        
        // Manejar firmas
        if ($request->filled('firmas')) {
            $data['firmas'] = array_filter($request->get('firmas', []));
        }

        $informe->update($data);

        return redirect()->route('informes.show', $informe)
                        ->with('success', 'Informe actualizado correctamente.');
    }

    /**
     * Eliminar informe
     */
    public function destroy(Informe $informe)
    {
        // Solo se pueden eliminar borradores
        if ($informe->estado !== 'borrador') {
            return redirect()->route('informes.index')
                           ->with('error', 'Solo se pueden eliminar informes en estado borrador.');
        }

        // Verificar permisos
        if ($informe->usuario_creador_id !== Auth::id() && !Auth::user()->tieneRol('super_admin')) {
            return redirect()->route('informes.index')
                           ->with('error', 'No tiene permisos para eliminar este informe.');
        }

        $informe->delete();

        return redirect()->route('informes.index')
                        ->with('success', 'Informe eliminado correctamente.');
    }

    /**
     * Enviar informe para revisión
     */
    public function enviar(Informe $informe)
    {
        if (!$informe->puedeSerEnviado()) {
            return redirect()->route('informes.show', $informe)
                           ->with('error', 'Este informe no puede ser enviado. Verifique que todos los campos obligatorios estén completos.');
        }

        $informe->enviar();

        return redirect()->route('informes.show', $informe)
                        ->with('success', 'Informe enviado para revisión correctamente.');
    }

    /**
     * Aprobar informe
     */
    public function aprobar(Informe $informe)
    {
        if (!Auth::user()->tienePermiso('informes.aprobar')) {
            return redirect()->route('informes.show', $informe)
                           ->with('error', 'No tiene permisos para aprobar informes.');
        }

        if (!$informe->puedeSerAprobado()) {
            return redirect()->route('informes.show', $informe)
                           ->with('error', 'Este informe no puede ser aprobado en su estado actual.');
        }

        $informe->aprobar(Auth::id());

        return redirect()->route('informes.show', $informe)
                        ->with('success', 'Informe aprobado correctamente.');
    }

    /**
     * Rechazar informe
     */
    public function rechazar(Request $request, Informe $informe)
    {
        if (!Auth::user()->tienePermiso('informes.aprobar')) {
            return redirect()->route('informes.show', $informe)
                           ->with('error', 'No tiene permisos para rechazar informes.');
        }

        $request->validate([
            'motivo_rechazo' => 'required|string|max:1000'
        ], [
            'motivo_rechazo.required' => 'Debe especificar el motivo del rechazo.'
        ]);

        if (!$informe->puedeSerRechazado()) {
            return redirect()->route('informes.show', $informe)
                           ->with('error', 'Este informe no puede ser rechazado en su estado actual.');
        }

        $informe->rechazar(Auth::id(), $request->get('motivo_rechazo'));

        return redirect()->route('informes.show', $informe)
                        ->with('success', 'Informe rechazado correctamente.');
    }

    /**
     * Exportar informes a Excel
     */
    public function exportarExcel(Request $request)
    {
        $query = Informe::with(['convenio', 'usuarioCreador']);

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('convenio_id')) {
            $query->where('convenio_id', $request->get('convenio_id'));
        }

        if ($request->filled('unidad_academica')) {
            $query->where('unidad_academica', $request->get('unidad_academica'));
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_presentacion', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_presentacion', '<=', $request->get('fecha_hasta'));
        }

        $informes = $query->orderBy('fecha_presentacion', 'desc')->get();

        $filename = 'informes_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new InformesExport($informes), $filename);
    }

    /**
     * Exportar informe individual a PDF
     */
    public function exportarPdf(Informe $informe)
    {
        $informe->load(['convenio', 'usuarioCreador', 'usuarioRevisor']);

        $pdf = Pdf::loadView('informes.pdf', compact('informe'));
        
        $filename = 'informe_' . $informe->id . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Obtener convenio por AJAX
     */
    public function getConvenio(Request $request)
    {
        $convenioId = $request->get('convenio_id');
        
        if (!$convenioId) {
            return response()->json(['error' => 'ID de convenio requerido'], 400);
        }

        $convenio = Convenio::find($convenioId);
        
        if (!$convenio) {
            return response()->json(['error' => 'Convenio no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'convenio' => [
                'id' => $convenio->id,
                'numero_convenio' => $convenio->numero_convenio,
                'institucion_contraparte' => $convenio->institucion_contraparte,
                'fecha_firma' => $convenio->fecha_firma->format('Y-m-d'),
                'tipo_convenio' => $convenio->tipo_convenio,
                'coordinador_convenio' => $convenio->coordinador_convenio,
                'vigencia_texto' => $convenio->vigencia_texto,
            ]
        ]);
    }

    /**
     * Mostrar informes pendientes de aprobación
     */
    public function pendientes(Request $request)
    {
        if (!Auth::user()->tienePermiso('informes.aprobar')) {
            return redirect()->route('informes.index')
                           ->with('error', 'No tiene permisos para ver informes pendientes.');
        }

        $query = Informe::with(['convenio', 'usuarioCreador'])
                        ->where('estado', 'enviado');

        // Filtros adicionales para pendientes
        if ($request->filled('buscar')) {
            $query->buscar($request->get('buscar'));
        }

        if ($request->filled('unidad_academica')) {
            $query->where('unidad_academica', $request->get('unidad_academica'));
        }

        $informes = $query->orderBy('fecha_presentacion', 'desc')->paginate(15);
        $unidadesAcademicas = Informe::getUnidadesAcademicas();

        return view('informes.pendientes', compact('informes', 'unidadesAcademicas'));
    }

    /**
     * API: Estadísticas de informes para dashboard
     */
    public function estadisticasApi()
    {
        $estadisticas = [
            'total' => Informe::count(),
            'borradores' => Informe::where('estado', 'borrador')->count(),
            'enviados' => Informe::where('estado', 'enviado')->count(),
            'aprobados' => Informe::where('estado', 'aprobado')->count(),
            'rechazados' => Informe::where('estado', 'rechazado')->count(),
            'este_mes' => Informe::whereMonth('fecha_presentacion', now()->month)
                                ->whereYear('fecha_presentacion', now()->year)
                                ->count(),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Duplicar informe
     */
    public function duplicar(Informe $informe)
    {
        // Crear una copia del informe
        $nuevoInforme = $informe->replicate();
        $nuevoInforme->estado = 'borrador';
        $nuevoInforme->usuario_creador_id = Auth::id();
        $nuevoInforme->usuario_revisor_id = null;
        $nuevoInforme->fecha_revision = null;
        $nuevoInforme->fecha_presentacion = now()->toDateString();
        $nuevoInforme->observaciones = null;
        $nuevoInforme->save();

        return redirect()->route('informes.edit', $nuevoInforme)
                        ->with('success', 'Informe duplicado correctamente. Puede editarlo antes de enviarlo.');
    }

    /**
     * Cambiar estado de un informe via AJAX
     */
    public function cambiarEstado(Request $request, Informe $informe)
    {
        if (!Auth::user()->tienePermiso('informes.aprobar')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para cambiar el estado de informes'
            ], 403);
        }

        $request->validate([
            'estado' => ['required', 'string', Rule::in(array_keys(Informe::getEstados()))]
        ]);

        $estadoAnterior = $informe->estado;
        $nuevoEstado = $request->get('estado');

        switch ($nuevoEstado) {
            case 'aprobado':
                if (!$informe->puedeSerAprobado()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este informe no puede ser aprobado'
                    ], 400);
                }
                $informe->aprobar(Auth::id());
                break;

            case 'rechazado':
                if (!$informe->puedeSerRechazado()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este informe no puede ser rechazado'
                    ], 400);
                }
                $informe->rechazar(Auth::id(), 'Rechazado desde el sistema');
                break;

            default:
                $informe->update(['estado' => $nuevoEstado]);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Estado cambiado de {$estadoAnterior} a {$nuevoEstado}",
            'nuevo_estado' => $informe->fresh()->estado,
            'nuevo_estado_texto' => $informe->fresh()->estado_texto,
            'nuevo_estado_badge' => $informe->fresh()->estado_badge
        ]);
    }
}