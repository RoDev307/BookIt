<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Procesa la reserva y redirige a la pantalla de éxito.
     */
    public function store(Request $request)
    {
        // Validamos los datos que vienen del formulario Frontend
        $request->validate([
            'servicio_nombre' => 'required|string',
            'servicio_precio' => 'required|numeric',
            'business_slug'   => 'required|string',
            'fecha_cita'      => 'required|date|after_or_equal:today',
            'hora_cita'       => 'required|string',
        ]);

        // Adaptación de campos a la nube
        $appointmentTime = $request->input('fecha_cita') . ' ' . $request->input('hora_cita') . ':00';

        $cita = Appointment::create([
            'user_id'          => Auth::id(),
            'business_id'      => 1,
            'service_id'       => 1,
            'appointment_time' => $appointmentTime,
            'status'           => 'pending',
            'notes'            => 'Creado desde el formulario dinámico del cliente',
        ]);

        // Pasamos los datos reales para que la pantalla muestre lo que el usuario eligió
        return redirect()->route('appointments.success')->with([
            'success'  => '¡Tu cita ha sido agendada con éxito!',
            'servicio' => $request->input('servicio_nombre'),
            'fecha'    => $request->input('fecha_cita'),
            'hora'     => $request->input('hora_cita')
        ]);
    }


    public function descargarPDF(Request $request)
    {

        $horaCruda = $request->query('hora', '00:00');
        $horaFormateada = $horaCruda;


        try {
            $horaFormateada = Carbon::createFromFormat('H:i', $horaCruda)->format('g:i A');
        } catch (\Exception $e) {
            try {

                $horaFormateada = Carbon::createFromFormat('H:i:s', $horaCruda)->format('g:i A');
            } catch (\Exception $ex) {

                $horaFormateada = $horaCruda;
            }
        }

        $data = [
            'fecha' => $request->query('fecha', date('Y-m-d')),
            'hora'  => $horaFormateada
        ];


        $dompdf = app('dompdf.wrapper');
        $dompdf->loadView('appointments.pdf', $data);


        return $dompdf->download('Comprobante_Cita_Databox.pdf');
    }
//Esmeradda Mis Citas punto 
    public function misCitas()
    {
        $appointments = Appointment::where('user_id',Auth::id())->orderBy('appointment_time', 'desc')->get();

            return view('dashboard', compact('appointments'));
    }

   public function cancelar(int $id)
    {
        $appointment = Appointment::findOrFail($id);

            if ($appointment->user_id != Auth::id()) {
                abort(403);
            }
            if ($appointment->status == 'cancelled') {
                return back();
            }

            $appointment->status = 'cancelled';
            $appointment->save();

            return back()->with('success', 'Cita cancelada correctamente');
    }
}