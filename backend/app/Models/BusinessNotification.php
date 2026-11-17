<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessNotification extends Model
{
    protected $fillable = [
        'business_id',
        'appointment_id',
        'type',
        'title',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
