@extends('layouts.app')

@section('title', 'Kantin Digital UPN Veteran Jakarta')

@section('content')

{{-- HERO --}}
<section class="hero">
    <span class="pill">
        <x-icon name="leaf" :size="14" /> Green Campus &bull; UPN Veteran Jakarta
    </span>

    <h1>Kantin Digital UPN Veteran Jakarta</h1>
    <p class="hero-text">
        Pesan santapan favorit langsung dari stan kantin kampus tanpa antre. Hemat waktu kuliah,
        higienis, dan dukung gerakan ramah lingkungan Green Campus.
    </p>

    <div class="hero-cta">
        <a href="#stan" class="btn btn-primary btn-lg">
            <x-icon name="utensils" :size="18" /> Pesan Sekarang
        </a>
        @guest
            <a href="{{ route('login') }}" class="btn btn-soft btn-lg">
                <x-icon name="log-in" :size="18" /> Masuk / Daftar Akun
            </a>
        @endguest
    </div>

    <div class="stats">
        <div class="stat">
            <strong>{{ count($tenants) }} Stan Mitra Aktif</strong>
            <span>Pilihan Menu Kampus</span>
        </div>
        <div class="stat">
            <strong class="gold">8&ndash;10 Mnt</strong>
            <span>Rata-rata Siap Saji</span>
        </div>
        <div class="stat">
            <strong>100% Bebas Antre</strong>
            <span>Sistem Pemesanan Cerdas</span>
        </div>
    </div>
</section>

{{-- STAN FAVORIT --}}
<section class="section" id="stan">
    <div class="section-head">
        <span class="eyebrow">Pilihan Makanan &amp; Minuman</span>
        <h2>Stan Kantin Favorit UPNVJ</h2>
    </div>

    <div class="grid-3 tenants">
        @foreach ($tenants as $t)
            <article class="card tenant">
                <div class="tenant-photo">
                    <img src="{{ asset($t['foto']) }}" alt="{{ $t['nama'] }}" loading="lazy">
                    <span class="rating"><x-icon name="star" :size="12" /> {{ $t['rating'] }}</span>
                    <span class="tag tag-{{ $t['tag_tipe'] }}">{{ $t['tag'] }}</span>
                </div>
                <div class="tenant-body">
                    <span class="tenant-no">Stan {{ $t['no'] }}</span>
                    <h3>{{ $t['nama'] }}</h3>
                    <p>{{ $t['deskripsi'] }}</p>
                    <div class="tenant-meta">
                        <span class="jam"><x-icon name="clock" :size="14" /> {{ $t['jam'] }}</span>
                        <span class="harga">Mulai Rp {{ number_format($t['harga'], 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ auth()->check() ? url('/#stan') : route('login') }}" class="btn btn-primary btn-block">Lihat Menu &amp; Pesan</a>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- ALUR PEMESANAN --}}
<section class="steps-wrap">
    <div class="section-head">
        <span class="eyebrow">Alur Pemesanan Praktis</span>
        <h2>3 Langkah Mudah Tanpa Antre</h2>
    </div>

    <div class="grid-3 steps">
        <div class="step">
            <span class="step-icon green"><x-icon name="pointer" :size="26" /></span>
            <h3>1. Pilih Stan &amp; Menu Favorit</h3>
            <p>Telusuri stan favorit Anda di UPNVJ dan pilih makanan atau minuman tanpa harus berdiri antre di lokasi.</p>
        </div>
        <div class="step">
            <span class="step-icon gold-bg"><x-icon name="qr-code" :size="26" /></span>
            <h3>2. Bayar Mudah Pakai QRIS</h3>
            <p>Selesaikan pembayaran instan melalui QRIS atau saldo U-Pay kampus terintegrasi yang 100% nir-tunai.</p>
        </div>
        <div class="step">
            <span class="step-icon green"><x-icon name="bell-ring" :size="26" /></span>
            <h3>3. Ambil saat Makanan Siap</h3>
            <p>Terima notifikasi real-time saat pesanan siap di stan. Datang dan ambil santapan hangat Anda tepat waktu.</p>
        </div>
    </div>
</section>

@endsection