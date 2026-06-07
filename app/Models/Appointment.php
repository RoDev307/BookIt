<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'business_id',
        'service_id',
        'staff_name',
        'client_name',
        'appointment_time',
        'status',
        'notes',
        'business_slug',
        'servicio_nombre',
        'servicio_precio',
        'fecha_cita',
        'hora_cita'
    ];

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
