<?php

namespace App\Http\Controllers;

use App\Models\Business;
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
}
