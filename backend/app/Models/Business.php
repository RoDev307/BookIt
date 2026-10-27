<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'description',
        'image_url'
    ];

    /**
     * Relación: Un negocio posee un catálogo de muchos servicios.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Relación: Un negocio administra un historial de citas recibidas.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Relación: Un negocio tiene muchos horarios de atención parametrizados.
     */
    public function hours(): HasMany
    {
        return $this->hasMany(BusinessHour::class);
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'business_id')->where('role', 'admin_business');
    }
}
