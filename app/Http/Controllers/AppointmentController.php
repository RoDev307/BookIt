<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentController extends Controller
{
    public function misCitas()
    {
        $user = Auth::user();

        // ESCENARIO 1: ADMINISTRADOR MAESTRO (SIN NEGOCIO) -> MÉTRICAS GLOBALES SAAS
        if (is_null($user->business_id) && ($user->role === 'admin_business' || $user->email === 'admin@bookit.com')) {
            $totalComercios   = \App\Models\Business::count();
            $totalCitas       = \App\Models\Appointment::count();
            $citasConfirmadas = \App\Models\Appointment::where('status', 'confirmed')->count();
            $citasCanceladas  = \App\Models\Appointment::where('status', 'cancelled')->count();

            $appointments = \App\Models\Appointment::with(['user', 'business'])
                ->orderBy('appointment_time', 'desc')
                ->get();

            return view('admin.dashboard', compact(
                'appointments',
                'totalComercios',
                'totalCitas',
                'citasConfirmadas',
                'citasCanceladas'
            ));
        }

        // ESCENARIO 2: ADMINISTRADOR DE UN COMERCIO LOCAL -> MÉTRICAS EXCLUSIVAS DE SU NEGOCIO
        if ($user->role === 'admin_business' || $user->business_id !== null) {
            $totalCitas       = Appointment::where('business_id', $user->business_id)->count();
            $citasConfirmadas = Appointment::where('business_id', $user->business_id)->where('status', 'confirmed')->count();
            $citasCanceladas  = Appointment::where('business_id', $user->business_id)->where('status', 'cancelled')->count();

            $appointments = Appointment::with('user')
                ->where('business_id', $user->business_id)
                ->orderBy('appointment_time', 'desc')
                ->get();

            return view('admin.dashboard', compact(
                'appointments',
                'totalCitas',
                'citasConfirmadas',
                'citasCanceladas'
            ));
        }

        // 👤 ESCENARIO 3: CLIENTE COMÚN -> HISTORIAL TRADICIONAL DE SUS RESERVAS
        $appointments = Appointment::where('user_id', $user->id)
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('admin.dashboard', compact('appointments'));
    }

    private function validarCitaSaaS($businessId, $appointmentTime, $staffName = null, $ignoreAppointmentId = null)
    {
        $fecha = Carbon::parse($appointmentTime);

        if ($fecha->isWeekend()) {
            return 'El establecimiento se encuentra cerrado los fines de semana. Por favor, selecciona un día de lunes a viernes.';
        }

        $hora = $fecha->hour;
        if ($hora < 8 || $hora >= 17) {
            return 'El horario de atención es exclusivamente de 8:00 AM a 5:00 PM.';
        }

        if ($staffName) {
            $colisionStaffQuery = Appointment::where('business_id', $businessId)
                ->where('staff_name', $staffName)
                ->where('appointment_time', $fecha->toDateTimeString())
                ->where('status', 'confirmed');

            if ($ignoreAppointmentId) {
                $colisionStaffQuery->where('id', '!=', $ignoreAppointmentId);
            }

            if ($colisionStaffQuery->exists()) {
                return "El colaborador '{$staffName}' ya tiene una cita agendada a esa misma hora.";
            }
        }

        $colisionClienteQuery = Appointment::where('business_id', $businessId)
            ->where('appointment_time', $fecha->toDateTimeString())
            ->where('status', 'confirmed')
            ->where('user_id', Auth::id());

        if ($ignoreAppointmentId) {
            $colisionClienteQuery->where('id', '!=', $ignoreAppointmentId);
        }

        if ($colisionClienteQuery->exists()) {
            return 'Ya tienes otra cita confirmada exactamente a la misma hora en este establecimiento.';
        }

        return null;
    }

    /**
     * Agenda la cita amarrándola de forma dinámica al comercio correspondiente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id'       => 'required|exists:services,id',
            'appointment_time' => 'required|date|after:now',
            'notes'            => 'nullable|string|max:500',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $error = $this->validarCitaSaaS($service->business_id, $validated['appointment_time']);
        if ($error) {
            return redirect()->back()->withInput()->withErrors(['appointment_time' => $error]);
        }

        Appointment::create([
            'user_id'          => Auth::id(),
            'business_id'      => $service->business_id,
            'service_id'       => $service->id,
            'staff_name'       => 'Asignado por Recepción',
            'appointment_time' => $validated['appointment_time'],
            'status'           => 'confirmed',
            'notes'            => $validated['notes'],
        ]);

        return redirect()->route('appointments.success')->with('success', 'Tu reserva ha sido procesada.');
    }

    public function cancelar($id)
    {
        $user = Auth::user();

        $appointment = Appointment::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('business_id', $user->business_id);
            })->firstOrFail();

        $appointment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'La cita ha sido cancelada correctamente.');
    }

    public function createAdmin()
    {
        $user = Auth::user();
        $services = Service::where('business_id', $user->business_id)->get();
        return view('admin.appointments.create', compact('services'));
    }

    /**
     * Procesa y guarda la reserva generada de forma manual por el comercio.
     */
    public function storeAdmin(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'service_id'   => 'required|exists:services,id',
            'fecha_cita'   => 'required|date|after_or_equal:today',
            'hora_cita'    => 'required|string',
            'client_name'  => 'required|string|max:255',
            'staff_name'   => 'required|string|max:255',
            'notes'        => 'nullable|string|max:500',
        ]);

        $appointmentTime = $validated['fecha_cita'] . ' ' . $validated['hora_cita'] . ':00';

        if (\Carbon\Carbon::parse($appointmentTime)->isPast()) {
            return redirect()->back()->withInput()->withErrors(['fecha_cita' => 'La fecha y hora seleccionada ya ha pasado.']);
        }

        $service = Service::where('id', $validated['service_id'])
            ->where('business_id', $user->business_id)
            ->firstOrFail();

        $error = $this->validarCitaSaaS($user->business_id, $appointmentTime, $validated['staff_name']);
        if ($error) {
            return redirect()->back()->withInput()->withErrors(['fecha_cita' => $error]);
        }

        $structuredNotes = 'Cliente Externo: ' . trim($validated['client_name']);
        if (!empty($validated['notes'])) {
            $structuredNotes .= ' | ' . trim($validated['notes']);
        }

        Appointment::create([
            'user_id'          => $user->id,
            'business_id'      => $user->business_id,
            'service_id'       => $service->id,
            'staff_name'       => $validated['staff_name'],
            'appointment_time' => $appointmentTime,
            'status'           => 'confirmed',
            'notes' => $structuredNotes,
        ]);

        return redirect()->route('dashboard')->with('success', 'La cita ha sido agendada e introducida al sistema correctamente.');
    }

    public function editAdmin($id)
    {
        $appointment = Appointment::where('business_id', Auth::user()->business_id)->findOrFail($id);

        $rawNotes = $appointment->notes ?? '';
        $clientNameResult = '';
        $cleanNotesResult = '';

        if (str_contains($rawNotes, 'Cliente Externo:')) {
            $parts = explode('|', str_replace('Cliente Externo:', '', $rawNotes));
            $clientNameResult = trim($parts[0] ?? '');
            $cleanNotesResult = isset($parts[1]) ? trim($parts[1]) : '';
        } else {
            $clientNameResult = '';
            $cleanNotesResult = $rawNotes;
        }

        $appointment->extracted_client_name = $clientNameResult;
        $appointment->extracted_notes       = $cleanNotesResult;

        return view('admin.appointments.edit', compact('appointment'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $appointment = Appointment::where('business_id', Auth::user()->business_id)->findOrFail($id);

        $request->validate([
            'client_name'      => ['required', 'string', 'max:255'],
            'appointment_time' => ['required', 'date', 'after_or_equal:today'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $appointmentTime = $request->input('appointment_time');

        $error = $this->validarCitaSaaS(Auth::user()->business_id, $appointmentTime, $appointment->staff_name, $appointment->id);
        if ($error) {
            return redirect()->back()->withInput()->withErrors(['appointment_time' => $error]);
        }

        $incomingNotes = $request->input('notes') ?? '';

        if (str_contains($incomingNotes, 'Cliente Externo:')) {
            $parts = explode('|', str_replace('Cliente Externo:', '', $incomingNotes));
            $incomingNotes = isset($parts[1]) ? trim($parts[1]) : '';
        }

        $structuredNotes = 'Cliente Externo: ' . trim($request->input('client_name'));
        if (!empty($incomingNotes)) {
            $structuredNotes .= ' | ' . $incomingNotes;
        }

        $appointment->appointment_time = $appointmentTime;
        $appointment->notes            = $structuredNotes;

        $appointment->save();

        return redirect()->route('dashboard')
            ->with('success', "¡La reserva #{$appointment->id} fue reprogramada con éxito!");
    }

    public function calendarioAdmin()
    {
        return view('admin.appointments.calendar');
    }

    public function apiAppointments(Request $request)
    {
        $user = Auth::user();

        $appointments = Appointment::where('business_id', $user->business_id)
            ->where('status', 'confirmed')
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
                $clientName = $app->user->name ?? 'Cliente Externo';
                $cleanNotes = $rawNotes;
            }
            $start = Carbon::parse($app->appointment_time, 'America/El_Salvador');
            $end = (clone $start)->addMinutes(45);

            $events[] = [
                'id' => $app->id,
                'title' => $clientName . " (" . ($app->staff_name ?? 'Recepción') . ")",
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'backgroundColor' => '#4f46e5',
                'borderColor' => '#4338ca',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'notes' => $cleanNotes,
                    'staff' => $app->staff_name ?? 'No asignado'
                ]
            ];
        }

        return response()->json($events);
    }

    /**
     * Genera y descarga el flujo binario del comprobante de cita en formato PDF.
     */
    public function bajarPdfAdmin($id)
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
            $clientName = $appointment->user->name ?? 'Cliente Externo';
            $cleanNotes = $rawNotes;
        }

        $pdf = Pdf::loadView('admin.appointments.pdf', compact('appointment', 'clientName', 'cleanNotes'));

        return $pdf->download("comprobante-cita-BK-{$appointment->id}.pdf");
    }
    public function generarReportePdf(Request $request)
    {
        $user = Auth::user();

        // Validar el rango solicitado
        $periodo = $request->query('periodo', 'mes'); // 'semana' o 'mes'

        $query = Appointment::where('business_id', $user->business_id)
            ->with(['user', 'service']);

        if ($periodo === 'semana') {
            $query->whereBetween('appointment_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } else {
            $query->whereBetween('appointment_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }

        $citas = $query->orderBy('appointment_time', 'asc')->get();

        $pdf = Pdf::loadView('admin.appointments.reporte', [
            'citas' => $citas,
            'periodo' => $periodo,
            'negocio' => $user->business->name ?? 'Establecimiento'
        ]);

        return $pdf->download("reporte-citas-{$periodo}-" . date('Y-m-d') . ".pdf");
    }
}
