<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function misCitas()
    {
        $user = Auth::user();

        // ESCENARIO 1: ADMINISTRADOR MAESTRO (SIN NEGOCIO) -> MÉTRICAS GLOBALES SAAS
        if (is_null($user->business_id) && ($user->role === 'super_admin' || $user->email === 'admin@bookit.com')) {
            $totalComercios   = \App\Models\Business::count();
            $totalCitas       = \App\Models\Appointment::count();
            $citasConfirmadas = \App\Models\Appointment::where('status', 'confirmed')->count();
            $citasCanceladas  = \App\Models\Appointment::where('status', 'cancelled')->count();

            $appointments = \App\Models\Appointment::with(['user', 'business'])
                ->orderBy('appointment_time', 'desc')
                ->take(5)
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
                ->orderBy('appointment_time', 'asc')
                ->take(5)
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

    private function validarCitaSaaS($businessId, $appointmentTime, $staffName = null)
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
            $colisionStaff = Appointment::where('business_id', $businessId)
                ->where('staff_name', $staffName)
                ->where('appointment_time', $fecha->toDateTimeString())
                ->where('status', 'confirmed')
                ->exists();

            if ($colisionStaff) {
                return "El colaborador '{$staffName}' ya tiene una cita agendada a esa misma hora.";
            }
        }

        $colisionCliente = Appointment::where('business_id', $businessId)
            ->where('appointment_time', $fecha->toDateTimeString())
            ->where('status', 'confirmed')
            ->where('user_id', Auth::id())
            ->exists();

        if ($colisionCliente) {
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
            'client_name'      => Auth::user()->name, // 🚨 ASIGNADO: Guarda el nombre del usuario logueado
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

        // 🚨 PERSISTENCIA CORREGIDA: Guarda client_name en su celda física de la BD de Aiven
        Appointment::create([
            'user_id'          => $user->id,
            'business_id'      => $user->business_id,
            'service_id'       => $service->id,
            'staff_name'       => $validated['staff_name'],
            'client_name'      => $validated['client_name'], // Guardado limpiamente
            'appointment_time' => $appointmentTime,
            'status'           => 'confirmed',
            'notes'            => $validated['notes'],
        ]);

        return redirect()->route('dashboard')->with('success', 'La cita ha sido agendada e introducida al sistema correctamente.');
    }

    public function editAdmin($id)
    {
        $appointment = Appointment::where('business_id', Auth::user()->business_id)->findOrFail($id);
        return view('admin.appointments.edit', compact('appointment'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $appointment = Appointment::where('business_id', Auth::user()->business_id)->findOrFail($id);

        $request->validate([
            'client_name'      => ['required', 'string', 'max:255'],
            'appointment_time' => ['required', 'date', 'after:now'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // 🚨 Reconstruimos la cadena estructurada exactamente igual que en storeAdmin
        $appointment->update([
            'appointment_time' => $request->appointment_time,
            'notes'            => "Cliente Externo: " . $request->client_name . " | " . $request->notes,
        ]);

        return redirect()->route('dashboard')
            ->with('success', "¡La reserva #{$appointment->id} fue reprogramada con éxito!");
    }
}
