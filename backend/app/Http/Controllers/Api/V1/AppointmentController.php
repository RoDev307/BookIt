<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Appointment;
use App\Models\Service;
use App\Services\AppointmentValidationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends BaseApiController
{
    public function __construct(
        protected AppointmentValidationService $validationService
    ) {}

    /**
     * Historial de citas del cliente autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::where('user_id', Auth::id())
            ->with(['business:id,name,slug,image_url', 'service:id,name,price,duration_minutes'])
            ->orderBy('appointment_time', 'desc');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $appointments = $query->get();

        return $this->sendResponse($appointments);
    }

    /**
     * Crear una nueva reserva (Cliente autenticado).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id'       => ['required', 'exists:services,id'],
            'appointment_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'fecha_cita'       => ['nullable', 'date_format:Y-m-d'],
            'hora_cita'        => ['nullable', 'string'],
            'staff_name'       => ['nullable', 'string', 'max:255'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // Determinar fecha/hora ya sea por 'appointment_time' o combinación 'fecha_cita' + 'hora_cita'
        if (!empty($validated['appointment_time'])) {
            $appointmentTime = $validated['appointment_time'];
        } elseif (!empty($validated['fecha_cita']) && !empty($validated['hora_cita'])) {
            $hora = strlen($validated['hora_cita']) === 5 ? $validated['hora_cita'] . ':00' : $validated['hora_cita'];
            $appointmentTime = "{$validated['fecha_cita']} {$hora}";
        } else {
            return $this->sendError('Debes especificar la fecha y hora de la cita.', [
                'appointment_time' => ['El campo appointment_time o fecha_cita + hora_cita es requerido.']
            ], 422);
        }

        $service = Service::findOrFail($validated['service_id']);

        if (!$service->is_active) {
            return $this->sendError('El servicio seleccionado ya no está disponible.', [], 422);
        }

        $staffName = !empty($validated['staff_name']) ? trim($validated['staff_name']) : 'Asignado por Recepción';

        // Validaciones estrictas de reglas de negocio SaaS
        $error = $this->validationService->validate(
            $service->business_id,
            $appointmentTime,
            $staffName,
            Auth::id()
        );

        if ($error) {
            return $this->sendError($error, ['appointment_time' => [$error]], 422);
        }

        $parsed = Carbon::parse($appointmentTime);

        $appointment = Appointment::create([
            'user_id'          => Auth::id(),
            'business_id'      => $service->business_id,
            'service_id'       => $service->id,
            'staff_name'       => $staffName,
            'appointment_time' => $appointmentTime,
            'status'           => 'confirmed',
            'notes'            => $validated['notes'] ?? null,
        ]);

        $appointment->load(['business:id,name,slug,image_url', 'service:id,name,price,duration_minutes']);

        return $this->sendResponse($appointment, 'Cita reservada y confirmada exitosamente.', 201);
    }

    /**
     * Ver detalle de una cita específica del cliente.
     */
    public function show(int $id): JsonResponse
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['business', 'service'])
            ->first();

        if (!$appointment) {
            return $this->sendError('Cita no encontrada o no te pertenece.', [], 404);
        }

        return $this->sendResponse($appointment);
    }

    /**
     * Cancelar cita propia.
     */
    public function cancel(int $id): JsonResponse
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$appointment) {
            return $this->sendError('Cita no encontrada o no te pertenece.', [], 404);
        }

        if ($appointment->status === 'cancelled') {
            return $this->sendError('La cita ya ha sido cancelada previamente.', [], 400);
        }

        if ($appointment->status === 'completed') {
            return $this->sendError('No es posible cancelar una cita que ya fue completada.', [], 400);
        }

        $appointment->update(['status' => 'cancelled']);

        return $this->sendResponse($appointment, 'La cita ha sido cancelada correctamente.');
    }
}
