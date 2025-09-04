<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfiguracionController extends Controller
{
    /**
     * Constructor - aplicar middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar la página de configuración
     */
    public function index()
    {
        $usuario = Auth::user();
        
        return view('configuracion', [
            'usuario' => $usuario
        ]);
    }

    /**
     * Actualizar configuración del usuario
     */
    public function update(Request $request)
    {
        // Validar datos
        $request->validate([
            'timezone' => 'required|string',
            'language' => 'required|string'
        ]);

        // Aquí puedes agregar lógica para guardar las configuraciones
        
        return redirect()->route('configuracion')->with('success', 'Configuración actualizada correctamente.');
    }
}