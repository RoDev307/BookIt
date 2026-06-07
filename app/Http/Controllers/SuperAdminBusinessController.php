<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SuperAdminBusinessController extends Controller
{
    /**
     * Muestra la lista de todos los comercios en el sistema.
     */
    public function index()
    {
        // Traemos todos los comercios de Aiven contando sus servicios anexados
        $businesses = Business::withCount('services')->get();
        return view('admin.master.businesses.index', compact('businesses'));
    }

    /**
     * Muestra el formulario de edición de un comercio específico.
     */
    public function edit($id)
    {
        $business = Business::findOrFail($id);
        return view('admin.master.businesses.edit', compact('business'));
    }

    /**
     * Aplica los cambios a cualquier negocio desde la cuenta maestra.
     */
    public function update(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:255'],
        ]);

        $oldName = $business->name;

        $business->update([
            'name' => $request->name,
            'description' => $request->description,
            'image_url' => $request->image_url,
            'slug' => $oldName !== $request->name ? Str::slug($request->name) . '-' . time() : $business->slug,
        ]);

        return redirect()->route('master.businesses.index')->with('success', "¡El comercio '{$business->name}' fue actualizado con éxito desde la consola maestra!");
    }
}
