<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
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
    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'professional_id');
    }

    public function bookedSessions()
     {
      return $this->hasMany(Session::class, 'client_id');
     }

    public function professionalSessions()
     {
      return $this->hasMany(Session::class, 'professional_id');
     }

     public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isPsychologist()
    {
        return $this->role === 'psychologist';
    }

    public function isVolunteer()
    {
        return $this->role === 'volunteer';
    }
    
}
