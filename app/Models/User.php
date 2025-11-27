<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;   // ← ADD THIS

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;   // ← ADD HasApiTokens here

    protected $fillable = [
        'first_name',
        'email',
        'last_name',
        'dob',
        'phone',
        'phone_country_code',
        'location_country_code',
        'location',
        'gender',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
