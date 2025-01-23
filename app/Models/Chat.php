<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'admin_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // The user who initiated the chat
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id'); // The admin assigned to the chat
    }

    public function messages()
    {
        return $this->hasMany(Message::class); // Messages in this chat
    }
}
