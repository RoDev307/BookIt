<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Appointment;
use App\Models\Service;
use App\Services\AppointmentValidationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAppointmentController extends BaseApiController
{
    public function __construct(
        protected AppointmentValidationService $validationService
    ) {}

    /**
     * Listado general de citas del comercio con filtros.
     */
    public function index(Request $request): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $query = Appointment::where('business_id', $businessId)
            ->with(['service:id,name,price,duration_minutes', 'user:id,name,email'])
            ->orderBy('appointment_time', 'desc');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('appointment_time', $request->date);
        }

        if ($request->has('staff_name') && !empty($request->staff_name)) {
            $query->where('staff_name', $request->staff_name);
        }

        $appointments = $query->get();

        return $this->sendResponse($appointments);
    }

    /**
     * Formato optimizado para calendarios (FullCalendar / Vue / Ionic).
     */
    public function calendar(Request $request): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $appointments = Appointment::where('business_id', $businessId)
            ->with(['user:id,name,email', 'service:id,name,duration_minutes'])
            ->get();

        $events = [];

        foreach ($appointments as $app) {
            $rawNotes = $app->notes ?? '';
            $clientName = 'Cliente Externo';
            $cleanNotes = '';

            if (str_contains($rawNotes, 'Cliente Externo:')) {
                $parts = explode('|', str_replace('Cliente Externo:', '', $rawNotes));
                $clientName = trim($parts[0] ?? 'Cliente Externo');
                $cleanNotes = isset($parts[1]) ? trim($parts[1]) : '';
            } else {
                $clientName = $app->user->name ?? 'Cliente Registrado';
                $cleanNotes = $rawNotes;
            }

            $duration = $app->service->duration_minutes ?? 45;
            $start = Carbon::parse($app->appointment_time, 'America/El_Salvador');
            $end = (clone $start)->addMinutes($duration);

            $color = match ($app->status) {
                'confirmed' => '#4f46e5',
                'completed' => '#10b981',
                'cancelled' => '#ef4444',
                default     => '#f59e0b',
            };

            $events[] = [
                'id'              => $app->id,
                'title'           => "{$clientName} - " . ($app->service->name ?? 'Servicio'),
                'start'           => $start->toIso8601String(),
                'end'             => $end->toIso8601String(),
                'status'          => $app->status,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'client_name'  => $clientName,
                    'staff_name'   => $app->staff_name ?? 'Recepción',
                    'service_name' => $app->service->name ?? null,
                    'notes'        => $cleanNotes,
                    'status'       => $app->status,
                ],
            ];
        }

        return $this->sendResponse($events);
    }

    /**
     * Agendamiento manual interno (por recepcionista o administrador).
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $validated = $request->validate([
            'service_id'       => ['required', 'exists:services,id'],
            'appointment_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'fecha_cita'       => ['nullable', 'date_format:Y-m-d'],
            'hora_cita'        => ['nullable', 'string'],
            'client_name'      => ['required', 'string', 'max:255'],
            'staff_name'       => ['required', 'string', 'max:255'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        if (!empty($validated['appointment_time'])) {
            $appointmentTime = $validated['appointment_time'];
        } elseif (!empty($validated['fecha_cita']) && !empty($validated['hora_cita'])) {
            $hora = strlen($validated['hora_cita']) === 5 ? $validated['hora_cita'] . ':00' : $validated['hora_cita'];
            $appointmentTime = "{$validated['fecha_cita']} {$hora}";
        } else {
            return $this->sendError('Debes indicar fecha y hora.', [
                'appointment_time' => ['El campo appointment_time o fecha_cita + hora_cita es requerido.']
            ], 422);
        }

        // Validar servicio propio
        $service = Service::where('id', $validated['service_id'])
            ->where('business_id', $businessId)
            ->firstOrFail();

        // Validar colisiones y horarios SaaS
        $error = $this->validationService->validate(
            $businessId,
            $appointmentTime,
            $validated['staff_name']
        );

        if ($error) {
            return $this->sendError($error, ['appointment_time' => [$error]], 422);
        }

        $structuredNotes = 'Cliente Externo: ' . trim($validated['client_name']);
        if (!empty($validated['notes'])) {
            $structuredNotes .= ' | ' . trim($validated['notes']);
        }

        $parsed = Carbon::parse($appointmentTime);

        $appointment = Appointment::create([
            'user_id'          => $user->id,
            'business_id'      => $businessId,
            'service_id'       => $service->id,
            'staff_name'       => trim($validated['staff_name']),
            'appointment_time' => $appointmentTime,
            'status'           => 'confirmed',
            'notes'            => $structuredNotes,
        ]);

        $appointment->load(['service', 'user']);

        return $this->sendResponse($appointment, 'Cita agendada manualmente con éxito.', 201);
    }

    /**
     * Modificar cita existente.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        $appointment = Appointment::where('id', $id)
            ->where('business_id', $businessId)
            ->first();

        if (!$appointment) {
            return $this->sendError('Cita no encontrada.', [], 404);
        }

        $validated = $request->validate([
            'service_id'       => ['nullable', 'exists:services,id'],
            'appointment_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'staff_name'       => ['nullable', 'string', 'max:255'],
            'status'           => ['nullable', 'in:pending,confirmed,cancelled,completed'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $newTime = $validated['appointment_time'] ?? $appointment->appointment_time;
        $newStaff = $validated['staff_name'] ?? $appointment->staff_name;

        // Validar colisión si cambió hora o staff
        if ($newTime !== $appointment->appointment_time || $newStaff !== $appointment->staff_name) {
            $error = $this->validationService->validate(
                $businessId,
                $newTime,
                $newStaff,
                null,
                $appointment->id
            );

            if ($error) {
                return $this->sendError($error, ['appointment_time' => [$error]], 422);
            }
        }

        $appointment->update($validated);
        $appointment->load(['service', 'user']);

        return $this->sendResponse($appointment, 'Cita actualizada exitosamente.');
    }

    /**
     * Actualizar únicamente el estado de la cita.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $businessId = Auth::user()->business_id;

        $appointment = Appointment::where('id', $id)
            ->where('business_id', $businessId)
            ->first();

        if (!$appointment) {
            return $this->sendError('Cita no encontrada.', [], 404);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
        ]);

        $appointment->update(['status' => $validated['status']]);

        return $this->sendResponse($appointment, 'Estado de la cita actualizado a ' . $validated['status']);
    }

    /**
     * Descarga del comprobante en PDF.
     */
    public function downloadPdf(int $id): Response
    {
        $user = Auth::user();

        if (is_null($user->business_id)) {
            $appointment = Appointment::with(['user', 'service', 'business'])->findOrFail($id);
        } else {
            $appointment = Appointment::where('business_id', $user->business_id)
                ->with(['user', 'service', 'business'])
                ->findOrFail($id);
        }

        $rawNotes = $appointment->notes ?? '';
        $clientName = 'Cliente Externo';
        $cleanNotes = '';

        if (str_contains($rawNotes, 'Cliente Externo:')) {
            $parts = explode('|', str_replace('Cliente Externo:', '', $rawNotes));
            $clientName = trim($parts[0] ?? 'Cliente Externo');
            $cleanNotes = isset($parts[1]) ? trim($parts[1]) : '';
        } else {
            $clientName = $appointment->user->name ?? 'Cliente';
            $cleanNotes = $rawNotes;
        }

        $pdf = Pdf::loadView('admin.appointments.pdf', compact('appointment', 'clientName', 'cleanNotes'));

        return $pdf->download("comprobante-cita-BK-{$appointment->id}.pdf");
    }

    /**
     * Generación del reporte de citas en PDF (semanal o mensual).
     */
    public function generateReportPdf(Request $request): Response
    {
        $user = Auth::user();
        $periodo = $request->query('periodo', 'mes');

        $query = Appointment::where('business_id', $user->business_id)
            ->with(['user', 'service']);

        if ($periodo === 'semana') {
            $query->whereBetween('appointment_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } else {
            $query->whereBetween('appointment_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }

        $citas = $query->orderBy('appointment_time', 'asc')->get();

        $pdf = Pdf::loadView('admin.appointments.reporte', [
            'citas'   => $citas,
            'periodo' => $periodo,
            'negocio' => $user->business->name ?? 'Establecimiento'
        ]);

        return $pdf->download("reporte-citas-{$periodo}-" . date('Y-m-d') . ".pdf");
    }
}
