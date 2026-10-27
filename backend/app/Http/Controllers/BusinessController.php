<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    /**
     * Muestra el catálogo de todos los negocios disponibles.
     */
    public function index()
    {
        // 1. Si hay un usuario logueado, verificamos su rol multi-tenant
        if (Auth::check()) {
            $user = Auth::user();

            // Si es administrador de un negocio y tiene un comercio asignado
            if ($user->role === 'admin_business' && $user->business_id !== null) {
                // REDIRECCIÓN INTELIGENTE: Lo mandamos directo a gestionar sus propios servicios
                return redirect()->route('services.index');
            }
        }

        // 2. Si es un visitante anónimo o un cliente común (Esmeralda/Omar), muestra el catálogo normal
        $businesses = Business::all();

        // Retorna tu vista pública actual (ajusta el nombre si es 'index' o 'businesses.index')
        return view('businesses.index', compact('businesses'));
    }

    /**
     * Muestra el detalle de un negocio específico (Catálogo para reservar).
     */
    public function show($slug)
    {
        // Buscamos el comercio por su slug o lanzamos un error 404 si no existe
        $business = \App\Models\Business::where('slug', $slug)->firstOrFail();

        // Enviamos el objeto compactado a la vista
        return view('businesses.show', compact('business'));
    }
}
