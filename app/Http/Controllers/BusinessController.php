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
        $businesses = Business::all();
        return view('businesses.index', compact('businesses'));
    }

    /**
     * Muestra el detalle de un negocio específico junto con sus servicios.
     */
    public function show($slug): View
    {
        // 1. Intentamos buscar el negocio real en la base de datos sin caernos con Fail
        $business = Business::with('services')->where('slug', $slug)->first();

        // 2. PLAN DE RESPALDO: Si no existe en la BD, creamos un objeto genérico al vuelo 
        // para que tu frontend no se rompa y reciba la variable obligatoria.
        if (!$business) {
            $business = new Business();
            $business->slug = $slug;
            $business->name = str_replace('-', ' ', $slug);
        }

        // 3. Retornamos la vista pasando tanto el objeto completo como la cadena $slug independiente
        return view('businesses.show', [
            'business' => $business,
            'slug' => $slug
        ]);
    }
}
