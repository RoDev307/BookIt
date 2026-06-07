<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentController extends Controller
{
    /**
     * MULTI-TENANT: Filtra el historial dependiendo de quién consulte.
     * Si es Admin, ve las citas de SU comercio. Si es Cliente, ve SUS propias reservas.
     */
    public function misCitas()
    {
        $user = Auth::user();

        // Escenario A: Si el usuario es Administrador de un Comercio (Alexander)
        if ($user->role === 'admin_business' || $user->business_id !== null) {
            $appointments = Appointment::where('business_id', $user->business_id)
                ->orderBy('appointment_time', 'asc')
                ->get();

            // Retorna la vista del panel administrativo interno que gestiona el comercio
            return view('admin.appointments.index', compact('appointments'));
        }

        // Escenario B: Si es un Cliente común (Esmeralda)
        $appointments = Appointment::where('user_id', $user->id)
            ->orderBy('appointment_time', 'desc')
            ->get();

        // Retorna la vista del cliente común (El dashboard de "Mis Reservas")
        return view('dashboard', compact('appointments'));
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

        // Recuperamos el servicio para saber a qué comercio (Tenant) le pertenece
        $service = Service::findOrFail($validated['service_id']);

        // Creamos la cita vinculando al cliente y al negocio automáticamente
        $appointment = Appointment::create([
            'user_id'          => Auth::id(),
            'business_id'      => $service->business_id, // 👈 Amarra la cita al comercio dueño del servicio
            'service_id'       => $validated['service_id'],
            'appointment_time' => $validated['appointment_time'],
            'status'           => 'confirmed',
            'notes'            => $validated['notes'],
        ]);

        return redirect()->route('appointments.success')->with('success', 'Tu reserva ha sido procesada.');
    }

    /**
     * Cancela la cita de forma segura evaluando la pertenencia.
     */
    public function cancelar($id)
    {
        $user = Auth::user();

        // El admin de un comercio o el dueño de la cita pueden cancelarla
        $appointment = Appointment::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('business_id', $user->business_id);
            })->firstOrFail();

        $appointment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'La cita ha sido cancelada correctamente.');
    }
}
