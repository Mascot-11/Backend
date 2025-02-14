<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Add 'image_url' to the fillable array
    protected $fillable = ['name', 'description', 'date', 'price', 'available_tickets', 'image_url'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
