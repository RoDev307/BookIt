<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessHour;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminBusinessController extends BaseApiController
{
    /**
     * Métricas del dashboard para el administrador del comercio.
     */
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $today = Carbon::today();

        $totalCitas       = Appointment::where('business_id', $businessId)->count();
        $citasConfirmadas = Appointment::where('business_id', $businessId)->where('status', 'confirmed')->count();
        $citasCanceladas  = Appointment::where('business_id', $businessId)->where('status', 'cancelled')->count();
        $citasCompletadas = Appointment::where('business_id', $businessId)->where('status', 'completed')->count();
        $citasHoy         = Appointment::where('business_id', $businessId)->whereDate('appointment_time', $today)->count();
        $totalServicios   = \App\Models\Service::where('business_id', $businessId)->count();

        // Próximas 5 citas
        $proximasCitas = Appointment::where('business_id', $businessId)
            ->where('appointment_time', '>=', now())
            ->where('status', 'confirmed')
            ->with(['service:id,name,price', 'user:id,name,email'])
            ->orderBy('appointment_time', 'asc')
            ->limit(5)
            ->get();

        return $this->sendResponse([
            'metrics' => [
                'total_appointments'     => $totalCitas,
                'confirmed_appointments' => $citasConfirmadas,
                'cancelled_appointments' => $citasCanceladas,
                'completed_appointments' => $citasCompletadas,
                'today_appointments'     => $citasHoy,
                'total_services'         => $totalServicios,
            ],
            'upcoming_appointments' => $proximasCitas,
        ]);
    }

    /**
     * Obtener los datos del comercio propio y sus horarios.
     */
    public function getProfile(): JsonResponse
    {
        $user = Auth::user();
        $business = Business::with('hours')->find($user->business_id);

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        return $this->sendResponse($business);
    }

    /**
     * Actualizar perfil del comercio (nombre, descripción, imagen).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $business = Business::find($user->business_id);

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url'   => ['nullable', 'url', 'max:255'],
        ]);

        if ($business->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . time();
        }

        $business->update($validated);

        return $this->sendResponse($business, 'Perfil del comercio actualizado exitosamente.');
    }

    /**
     * Configurar o actualizar los horarios de atención semanales.
     */
    public function updateHours(Request $request): JsonResponse
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'hours'                  => ['required', 'array'],
            'hours.*.day_of_week'    => ['required', 'integer', 'between:0,6'],
            'hours.*.opening_time'   => ['nullable', 'string'],
            'hours.*.closing_time'   => ['nullable', 'string'],
            'hours.*.is_closed'      => ['required', 'boolean'],
        ]);

        foreach ($validated['hours'] as $item) {
            BusinessHour::updateOrCreate(
                [
                    'business_id' => $businessId,
                    'day_of_week' => $item['day_of_week'],
                ],
                [
                    'opening_time' => $item['opening_time'] ?? null,
                    'closing_time' => $item['closing_time'] ?? null,
                    'is_closed'    => $item['is_closed'],
                ]
            );
        }

        $updatedHours = BusinessHour::where('business_id', $businessId)
            ->orderBy('day_of_week', 'asc')
            ->get();

        return $this->sendResponse($updatedHours, 'Horarios de atención actualizados exitosamente.');
    }
}
