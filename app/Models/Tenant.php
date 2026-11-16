<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'nama_stan',
        'deskripsi',
        'lokasi',
        'foto',
        'jam_buka',
        'jam_tutup',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(
            Order::class,
            'order_tenants',
            'tenant_id',
            'order_id'
        )
        ->withPivot('status_pesanan')
        ->withTimestamps();
    }

    public function orderTenants(): HasMany
    {
        return $this->hasMany(OrderTenant::class);
    }
}
