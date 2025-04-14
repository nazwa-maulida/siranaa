<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Notifications\Notifiable;//
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory; //Notifiable//

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

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
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMitra()
    {
        return $this->role === 'mitra';
    }

    public function isPerusahaan()
    {
        return $this->role === 'perusahaan';
    }

    public function hasRole($role)
    {
        return $this->role === $role; // Menambahkan metode hasRole
    }

    public function mitra()
    {
        return $this->hasOne(Mitra::class, 'user_id', 'id');
    }

    public function perusahaan()
    {
        return $this->hasOne(Perusahaan::class, 'user_id', 'id');
    }
}


