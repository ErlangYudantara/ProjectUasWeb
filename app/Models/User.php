<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nomor_identitas',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relasi dengan tabel tenants
    public function tenant()
    {
        return $this->hasOne(Tenant::class, 'user_id');
    }

    // Relasi dengan tabel orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // Relasi dengan tabel notifications
    public function notifikasi()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}
