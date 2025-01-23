<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id', 'sender_id', 'content'];

    public function chat()
    {
        return $this->belongsTo(Chat::class); // The chat this message belongs to
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id'); // The user/admin who sent the message
    }
}

