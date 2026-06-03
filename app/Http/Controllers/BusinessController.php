<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessController extends Controller
{
    /**
     * Muestra el catálogo de todos los negocios disponibles.
     */
    public function index(): View
    {
        // Traemos todos los negocios de la nube de Aiven
        $businesses = Business::all();

        // Retornamos la vista pasando los datos
        return view('businesses.index', compact('businesses'));
    }

    /**
     * Muestra el detalle de un negocio específico junto con sus servicios.
     */
    public function show($slug): View
    {
        // BUSQUEDA CRÍTICA: Buscar el negocio por su slug e incluir sus servicios cargados (Eager Loading)
        $business = Business::with('services')->where('slug', $slug)->firstOrFail();

        // Retornamos la vista pasando el OBJETO real del negocio
        return view('businesses.show', compact('business'));
    }
}
