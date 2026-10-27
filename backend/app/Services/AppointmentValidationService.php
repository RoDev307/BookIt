<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BusinessHour;
use Carbon\Carbon;

class AppointmentValidationService
{
    /**
     * Valida las reglas de negocio SaaS para agendar o reprogramar una cita.
     * Retorna null si es válida, o un string con el mensaje de error.
     */
    public function validate(
        int $businessId,
        string $appointmentTime,
        ?string $staffName = null,
        ?int $userId = null,
        ?int $ignoreAppointmentId = null
    ): ?string {
        $fecha = Carbon::parse($appointmentTime);

        // 1. No permitir fechas en el pasado
        if ($fecha->isPast()) {
            return 'La fecha y hora seleccionada ya ha transcurrido. Por favor selecciona un horario futuro.';
        }

        // 2. Horario según la tabla business_hours o regla por defecto (Lunes-Viernes 8:00 a 17:00)
        $dayOfWeek = $fecha->dayOfWeek; // 0 = Domingo, 6 = Sábado
        $businessHour = BusinessHour::where('business_id', $businessId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($businessHour) {
            if ($businessHour->is_closed) {
                return 'El establecimiento se encuentra cerrado este día.';
            }

            $horaStr = $fecha->format('H:i:s');
            if ($businessHour->opening_time && $businessHour->closing_time) {
                if ($horaStr < $businessHour->opening_time || $horaStr >= $businessHour->closing_time) {
                    $apertura = substr($businessHour->opening_time, 0, 5);
                    $cierre = substr($businessHour->closing_time, 0, 5);
                    return "El horario de atención para este día es de {$apertura} a {$cierre}.";
                }
            }
        } else {
            // Regla por defecto del sistema
            if ($fecha->isWeekend()) {
                return 'El establecimiento se encuentra cerrado los fines de semana. Por favor, selecciona un día de lunes a viernes.';
            }

            $hora = $fecha->hour;
            if ($hora < 8 || $hora >= 17) {
                return 'El horario de atención es exclusivamente de 8:00 AM a 5:00 PM.';
            }
        }

        // 3. Colisión por especialista / staff
        if ($staffName && $staffName !== 'Asignado por Recepción') {
            $colisionStaff = Appointment::where('business_id', $businessId)
                ->where('staff_name', $staffName)
                ->where('appointment_time', $fecha->toDateTimeString())
                ->where('status', 'confirmed');

            if ($ignoreAppointmentId) {
                $colisionStaff->where('id', '!=', $ignoreAppointmentId);
            }

            if ($colisionStaff->exists()) {
                return "El colaborador '{$staffName}' ya tiene una cita confirmada a esa misma hora.";
            }
        }

        // 4. Colisión por cliente (el mismo usuario no puede tener dos citas simultáneas en el mismo negocio)
        if ($userId) {
            $colisionCliente = Appointment::where('business_id', $businessId)
                ->where('user_id', $userId)
                ->where('appointment_time', $fecha->toDateTimeString())
                ->where('status', 'confirmed');

            if ($ignoreAppointmentId) {
                $colisionCliente->where('id', '!=', $ignoreAppointmentId);
            }

            if ($colisionCliente->exists()) {
                return 'Ya tienes otra cita confirmada exactamente a la misma hora en este establecimiento.';
            }
        }

        return null;
    }
}
