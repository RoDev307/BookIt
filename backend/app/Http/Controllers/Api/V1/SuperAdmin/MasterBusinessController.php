<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterBusinessController extends BaseApiController
{
    /**
     * Métricas globales de la plataforma SaaS para el Super Administrador.
     */
    public function dashboard(): JsonResponse
    {
        $totalComercios    = Business::count();
        $totalCitas        = Appointment::count();
        $citasConfirmadas  = Appointment::where('status', 'confirmed')->count();
        $citasCanceladas   = Appointment::where('status', 'cancelled')->count();
        $citasCompletadas  = Appointment::where('status', 'completed')->count();
        $totalUsuarios     = User::count();
        $totalClientes     = User::where('role', 'client')->count();
        $totalAdmins       = User::where('role', 'admin_business')->count();

        $recentBusinesses = Business::with('owner:id,name,email,business_id')
            ->withCount('services', 'appointments')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return $this->sendResponse([
            'metrics' => [
                'total_businesses'       => $totalComercios,
                'total_appointments'     => $totalCitas,
                'confirmed_appointments' => $citasConfirmadas,
                'cancelled_appointments' => $citasCanceladas,
                'completed_appointments' => $citasCompletadas,
                'total_users'            => $totalUsuarios,
                'total_clients'          => $totalClientes,
                'total_business_admins'  => $totalAdmins,
            ],
            'recent_businesses' => $recentBusinesses,
        ]);
    }

    /**
     * Lista completa de comercios con sus administradores y conteo de servicios.
     */
    public function index(): JsonResponse
    {
        $businesses = Business::with('owner:id,name,email,business_id')
            ->withCount(['services', 'appointments'])
            ->orderBy('id', 'desc')
            ->get();

        return $this->sendResponse($businesses);
    }

    /**
     * Crear un nuevo comercio (Inquilino SaaS) junto con su usuario Administrador.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'business_name'  => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'image_url'      => ['nullable', 'url', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        // 1. Crear el negocio
        $business = Business::create([
            'name'        => $validated['business_name'],
            'slug'        => Str::slug($validated['business_name']) . '-' . time(),
            'email'       => $validated['email'] ?? $validated['admin_email'],
            'description' => $validated['description'] ?? null,
            'image_url'   => $validated['image_url'] ?? null,
        ]);

        // 2. Crear el administrador vinculado
        $admin = User::create([
            'name'        => $validated['admin_name'],
            'email'       => $validated['admin_email'],
            'password'    => Hash::make($validated['admin_password']),
            'role'        => 'admin_business',
            'business_id' => $business->id,
        ]);

        return $this->sendResponse([
            'business' => $business,
            'admin'    => [
                'id'          => $admin->id,
                'name'        => $admin->name,
                'email'       => $admin->email,
                'role'        => $admin->role,
                'business_id' => $admin->business_id,
            ],
        ], "Comercio '{$business->name}' y cuenta de administrador creados exitosamente.", 201);
    }

    /**
     * Consultar detalle completo de un comercio.
     */
    public function show(int $id): JsonResponse
    {
        $business = Business::with(['owner:id,name,email,business_id', 'services', 'hours'])
            ->withCount('appointments')
            ->find($id);

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        return $this->sendResponse($business);
    }

    /**
     * Actualizar datos del comercio y/o de su administrador.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $business = Business::find($id);

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'image_url'      => ['nullable', 'url', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'admin_name'     => ['nullable', 'string', 'max:255'],
            'admin_email'    => ['nullable', 'email', 'max:255'],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ]);

        $oldName = $business->name;

        $business->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? $business->description,
            'image_url'   => $validated['image_url'] ?? $business->image_url,
            'email'       => $validated['email'] ?? $business->email,
            'slug'        => $oldName !== $validated['name'] ? Str::slug($validated['name']) . '-' . time() : $business->slug,
        ]);

        // Actualizar administrador si se proporciona
        if ($business->owner) {
            $adminData = [];
            if (!empty($validated['admin_name'])) {
                $adminData['name'] = $validated['admin_name'];
            }
            if (!empty($validated['admin_email']) && $validated['admin_email'] !== $business->owner->email) {
                $adminData['email'] = $validated['admin_email'];
            }
            if (!empty($validated['admin_password'])) {
                $adminData['password'] = Hash::make($validated['admin_password']);
            }

            if (!empty($adminData)) {
                $business->owner->update($adminData);
            }
        }

        $business->load('owner:id,name,email,business_id');

        return $this->sendResponse($business, 'Comercio actualizado correctamente.');
    }

    /**
     * Eliminar comercio del sistema.
     */
    public function destroy(int $id): JsonResponse
    {
        $business = Business::find($id);

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        $businessName = $business->name;

        // Eliminar administrador asociado
        User::where('business_id', $business->id)->delete();

        // Eliminar negocio (cascada borrará servicios, horas y citas según foreign keys)
        $business->delete();

        return $this->sendResponse(null, "El comercio '{$businessName}' ha sido eliminado exitosamente.");
    }
}
