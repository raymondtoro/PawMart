<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar', // add this if you store avatar path
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * User's orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * User's ratings
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function lastMessage()
{
    return $this->hasOne(Message::class, 'sender_id')
        ->orWhere('receiver_id', $this->id)
        ->latestOfMany();
}

public function isAdmin()
{
    return $this->role === 'admin';
}

public function isUser()
{
    return $this->role === 'user';
}


}
