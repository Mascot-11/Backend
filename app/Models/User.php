<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Make sure 'role' is included for mass assignment
    ];
    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }

    public function assignedChats()
    {
        return $this->hasMany(Chat::class, 'admin_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    /**
     * Check if the user has an admin role.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has a tattoo artist role.
     *
     * @return bool
     */
    public function isTattooArtist()
    {
        return $this->role === 'tattoo_artist';
    }

    /**
     * Check if the user has a regular user role.
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

        public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }
    public function artistAppointments()
    {
    return $this->hasMany(Appointment::class, 'artist_id');
    }
    // In User Model
public function artistProfile()
{
    return $this->hasOne(ArtistProfile::class);
}

}
