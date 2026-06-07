<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Muestra la tabla con todos los servicios.
     */
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Muestra el formulario para crear un nuevo servicio.
     */
    public function create()
    {
        // Renderiza el formulario de creación que estructuramos antes
        return view('admin.services.create');
    }

    /**
     * Guarda el nuevo servicio en la base de datos de Aiven.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id'      => 'required|integer',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Servicio creado exitosamente.');
    }

    /**
     * Muestra un servicio específico (No requerido si usas el index, pero se mapea por seguridad).
     */
    public function show(string $id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    /**
     * Muestra el formulario para editar un servicio existente.
     */
    public function edit(string $id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Actualiza los datos del servicio en la nube.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'business_id'      => 'required|integer',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Servicio actualizado correctamente.');
    }

    /**
     * Elimina el servicio de forma definitiva.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Servicio eliminado del catálogo de forma definitiva.');
    }
}
