<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'artist_id',
        'appointment_datetime',
        'description',
        'status',
    ];

    // Relationship with user (who made the appointment)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with artist (assigned to the appointment)
    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id');
    }
}
