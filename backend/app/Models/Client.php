<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Client extends Model {
    protected $hidden = ['link_code_hash', 'link_expires_at'];
    protected $casts = ['link_expires_at' => 'datetime'];
    protected $fillable = ['business_id', 'user_id', 'name', 'phone', 'email'];
    public function business() { return $this->belongsTo(Business::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
}
