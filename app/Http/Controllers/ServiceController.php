<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ServiceController extends Controller
{
    /**
     * Filtra y muestra ÚNICAMENTE los servicios del comercio del usuario logueado.
     */
    public function index()
    {
        $user = Auth::user();

        // SEGURIDAD SAAS: Si no tiene comercio asignado, lo rebota para evitar fugas de información
        if (!$user->business_id) {
            return redirect()->route('businesses.index')->with('error', 'Tu cuenta no tiene un comercio asignado.');
        }

        // Solo trae los servicios que pertenezcan a su negocio
        $services = Service::where('business_id', $user->business_id)->get();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Guarda el nuevo servicio asignándole el negocio de forma automática y transparente.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        // Inyección automática del Tenant ID del usuario logueado
        $validated['business_id'] = $user->business_id;

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Servicio publicado en tu comercio con éxito.');
    }

    public function edit(string $id)
    {
        // SEGURIDAD SAAS: FailOrFail combinado con validación de pertenencia
        $service = Service::where('id', $id)
            ->where('business_id', Auth::user()->business_id)
            ->firstOrFail();

        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, string $id)
    {
        $service = Service::where('id', $id)
            ->where('business_id', Auth::user()->business_id)
            ->firstOrFail();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Ficha de servicio actualizada.');
    }

    public function destroy(Request $request, $id)
    {
        // 1. Validamos que la contraseña haya llegado en la petición
        $request->validate([
            'admin_password' => 'required|string',
        ]);

        $user = Auth::user();

        // 2. CORREGIDO: Namespace exacto de Illuminate\Support\Facades\Hash para evitar el error de tipo indefinido
        if (!\Illuminate\Support\Facades\Hash::check($request->admin_password, $user->password)) {
            // Si falla, regresamos inmediatamente con un mensaje de error tipo banner
            return redirect()->back()->with('error', 'La contraseña de seguridad ingresada es incorrecta. No se pudo eliminar el servicio.');
        }

        // 3. Aislamiento SaaS: Buscamos el servicio asegurando que pertenezca al negocio actual
        $service = Service::where('id', $id)
            ->where('business_id', $user->business_id)
            ->firstOrFail();

        // 4. Ejecutamos la eliminación física (esto borrará en cascada si está configurado en la migración)
        $service->delete();

        // 5. Retornamos el éxito que disparará el SweetAlert animado que ya configuramos
        return redirect()->route('services.index')->with('success', 'El servicio y sus dependencias han sido eliminados correctamente.');
    }
}
