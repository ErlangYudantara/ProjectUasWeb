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
        'foto_stan',
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

    /** Foto cadangan (dipakai menu); kalau kosong pakai logo. */
    public function getFotoUrlAttribute(): string
    {
        return $this->resolveFotoPath($this->foto);
    }

    /** Foto tampak stan untuk kartu daftar stan; cadangan: foto menu, lalu logo. */
    public function getFotoStanUrlAttribute(): string
    {
        return $this->resolveFotoPath($this->foto_stan ?: $this->foto);
    }

    private function resolveFotoPath(?string $path): string
    {
        if (! $path) {
            return asset('images/logo-upnvj.png');
        }

        return str_starts_with($path, 'images/') ? asset($path) : asset('storage/' . $path);
    }
}