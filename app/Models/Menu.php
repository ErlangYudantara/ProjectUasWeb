<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'tenant_id', 'category_id', 'nama_menu', 'deskripsi', 'harga', 'stok',
        'foto', 'label', 'label_tipe', 'rating', 'jumlah_ulasan', 'status',
    ];

    protected $casts = ['harga' => 'decimal:2', 'rating' => 'float'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /** Foto menu; kalau kosong pakai foto stan, lalu logo. */
    public function getFotoUrlAttribute(): string
    {
        $path = $this->foto ?: optional($this->tenant)->foto;

        if (! $path) {
            return asset('images/logo-upnvj.png');
        }

        return str_starts_with($path, 'images/') ? asset($path) : asset('storage/' . $path);
    }

    /** 1200 -> "1.2k" */
    public function getUlasanSingkatAttribute(): string
    {
        $n = (int) $this->jumlah_ulasan;

        return $n >= 1000 ? rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'k' : (string) $n;
    }
}