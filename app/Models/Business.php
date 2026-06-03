<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = ['name', 'slug', 'email'];
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    /**
     * Relación: Un negocio tiene muchos horarios de atención (uno por cada día de la semana).
     */
    public function hours()
    {
        return $this->hasMany(BusinessHour::class);
    }
}
