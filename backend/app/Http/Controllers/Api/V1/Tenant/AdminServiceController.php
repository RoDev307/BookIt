<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminServiceController extends BaseApiController
{
    /**
     * Listar todos los servicios del comercio del administrador actual.
     */
    public function index(): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $services = Service::where('business_id', $businessId)
            ->withCount('appointments')
            ->orderBy('id', 'desc')
            ->get();

        return $this->sendResponse($services);
    }

    /**
     * Registrar un nuevo servicio para el comercio.
     */
    public function store(Request $request): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $validated['business_id'] = $businessId;
        $validated['is_active']   = $validated['is_active'] ?? true;

        $service = Service::create($validated);

        return $this->sendResponse($service, 'Servicio creado y publicado con éxito.', 201);
    }

    /**
     * Consultar detalle de un servicio propio.
     */
    public function show(int $id): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        $service = Service::where('id', $id)
            ->where('business_id', $businessId)
            ->first();

        if (!$service) {
            return $this->sendError('Servicio no encontrado.', [], 404);
        }

        return $this->sendResponse($service);
    }

    /**
     * Actualizar datos del servicio.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        $service = Service::where('id', $id)
            ->where('business_id', $businessId)
            ->first();

        if (!$service) {
            return $this->sendError('Servicio no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $service->update($validated);

        return $this->sendResponse($service, 'Servicio actualizado correctamente.');
    }

    /**
     * Eliminar servicio del catálogo.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();

        $service = Service::where('id', $id)
            ->where('business_id', $user->business_id)
            ->first();

        if (!$service) {
            return $this->sendError('Servicio no encontrado.', [], 404);
        }

        // Si se provee admin_password para confirmar eliminación
        if ($request->has('admin_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->admin_password, $user->password)) {
                return $this->sendError('La contraseña de seguridad ingresada es incorrecta.', [], 422);
            }
        }

        $service->delete();

        return $this->sendResponse(null, 'El servicio ha sido eliminado correctamente.');
    }
}
