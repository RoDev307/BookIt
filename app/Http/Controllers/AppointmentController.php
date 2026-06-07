<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        return view('admin.dashboard', compact('appointments'));
    }

    private function validarCitaSaaS($businessId, $appointmentTime, $staffName = null)
    {
        $fecha = Carbon::parse($appointmentTime);

        // 1. COMPROBACIÓN DE DÍAS QUE NO SE TRABAJA (Sábados y Domingos cerrados)
        if ($fecha->isWeekend()) {
            return 'El establecimiento se encuentra cerrado los fines de semana. Por favor, selecciona un día de lunes a viernes.';
        }

        // 2. COMPROBACIÓN DE HORARIOS (Solo se atiende de 8:00 AM a 5:00 PM)
        $hora = $fecha->hour;
        if ($hora < 8 || $hora >= 17) {
            return 'El horario de atención es exclusivamente de 8:00 AM a 5:00 PM.';
        }

        // 3. EVITAR CITAS DUPLICADAS A LA MISMA HORA PARA EL MISMO TRABAJADOR
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

        // 4. EVITAR QUE EL MISMO CLIENTE SAQUE DOS CITAS AL MISMO TIEMPO
        $colisionCliente = Appointment::where('business_id', $businessId)
            ->where('appointment_time', $fecha->toDateTimeString())
            ->where('status', 'confirmed')
            ->where('user_id', Auth::id())
            ->exists();

        if ($colisionCliente) {
            return 'Ya tienes otra cita confirmada exactamente a la misma hora en este establecimiento.';
        }

        return null; // Todo en orden
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

        // Ejecutar las validaciones SaaS
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
    public function createAdmin()
    {
        $user = Auth::user();

        // Recupera ÚNICAMENTE los servicios que vende este comercio específico (Aislamiento SaaS)
        $services = Service::where('business_id', $user->business_id)->get();

        return view('admin.appointments.create', compact('services'));
    }

    /**
     * Procesa y guarda la reserva generada de forma manual por el comercio.
     */
    public function storeAdmin(Request $request)
    {
        $user = Auth::user();

        // 1. Validamos los campos de la interfaz por separado
        $validated = $request->validate([
            'service_id'   => 'required|exists:services,id',
            'fecha_cita'   => 'required|date|after_or_equal:today',
            'hora_cita'    => 'required|string',
            'client_name'  => 'required|string|max:255',
            'staff_name'   => 'required|string|max:255',
            'notes'        => 'nullable|string|max:500',
        ]);

        // 2. CONCATENACIÓN CRÍTICA: Fusionamos fecha y hora en una sola estructura cronológica
        $appointmentTime = $validated['fecha_cita'] . ' ' . $validated['hora_cita'] . ':00';

        // Comprobación de seguridad: verificar que la fecha generada sea posterior al momento actual
        if (\Carbon\Carbon::parse($appointmentTime)->isPast()) {
            return redirect()->back()->withInput()->withErrors(['fecha_cita' => 'La fecha y hora seleccionada ya ha pasado.']);
        }

        // 3. Verificación de pertenencia del servicio (Aislamiento SaaS)
        $service = Service::where('id', $validated['service_id'])
            ->where('business_id', $user->business_id)
            ->firstOrFail();

        // 4. Ejecución del motor de reglas y colisiones que programamos antes
        $error = $this->validarCitaSaaS($user->business_id, $appointmentTime, $validated['staff_name']);
        if ($error) {
            // Devolvemos el error directamente sobre el campo de la fecha para alertar en la interfaz
            return redirect()->back()->withInput()->withErrors(['fecha_cita' => $error]);
        }

        // 5. Inserción limpia en la base de datos de Aiven
        Appointment::create([
            'user_id'          => $user->id,
            'business_id'      => $user->business_id,
            'service_id'       => $service->id,
            'staff_name'       => $validated['staff_name'],
            'appointment_time' => $appointmentTime,
            'status'           => 'confirmed',
            'notes'            => "Cliente Externo: " . $validated['client_name'] . " | " . $validated['notes'],
        ]);

        return redirect()->route('dashboard')->with('success', 'La cita externa ha sido agendada e introducida al sistema correctamente.');
    }
}
