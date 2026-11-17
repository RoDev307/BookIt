<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'business_id',
        'service_id',
        'staff_name',
        'appointment_time',
        'status',
        'notes',
        'business_slug',
        'servicio_nombre',
        'servicio_precio',
        'fecha_cita',
        'hora_cita'
    ];

    protected static function booted(): void
    {
        static::created(function (Appointment $appointment) {
            $appointment->notifyBusiness('created');
        });
        static::updated(function (Appointment $appointment) {
            if ($appointment->wasChanged('status')) {
                $appointment->notifyBusiness($appointment->status);
            } elseif ($appointment->wasChanged(['appointment_time', 'service_id', 'staff_name'])) {
                $appointment->notifyBusiness('updated');
            }
        });
    }

    private function notifyBusiness(string $type): void
    {
        if (!$this->business_id) {
            return;
        }

        $titles = [
            'created' => 'Nueva cita',
            'cancelled' => 'Cita cancelada',
            'confirmed' => 'Cita confirmada',
            'completed' => 'Cita completada',
            'pending' => 'Cita pendiente',
            'updated' => 'Cita modificada',
        ];
        $service = $this->service?->name ?? 'Servicio';
        $date = $this->appointment_time
            ? \Carbon\Carbon::parse($this->appointment_time)->format('d/m/Y H:i')
            : 'fecha sin especificar';

        BusinessNotification::create([
            'business_id' => $this->business_id,
            'appointment_id' => $this->id,
            'type' => $type,
            'title' => $titles[$type] ?? 'Cita actualizada',
            'message' => $service . ' · ' . $date,
        ]);
    }

    public function client() { return $this->belongsTo(Client::class); }

    public function getClientNameAttribute(): string {
        if ($this->client) return $this->client->name;
        if (str_starts_with($this->notes ?? '', 'Cliente Externo:')) {
            return trim(explode('|', substr($this->notes, 16))[0]);
        }
        return $this->user?->name ?? 'Cliente';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
