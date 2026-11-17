@extends('layouts.pembeli')

@section('title', 'Menu & Pesanan - Kantin UPNVJ')

@section('content')
@section('topnav')
    @include('layouts._topnav')
@endsection

{{-- HERO --}}
<section class="m-hero">
    <div class="m-hero-info">
        <div class="m-hero-badges">
            <span class="m-open"><x-icon name="zap" :size="12" /> BUKA &bull; 07.30 - 16.30 WIB</span>
            <span class="m-loc">&bull; Gedung Dr. Soetomo Lt. 1</span>
        </div>
        <h1>Kantin Green Campus UPNVJ</h1>
        <p>Pesan makanan &amp; minuman langsung tanpa antre. Ambil dengan nomor token saat pesanan siap saji.</p>
    </div>
    <div class="m-stats">
        <div class="m-stat">
            <x-icon name="timer" :size="22" />
            <div><small>Estimasi Rata-rata</small><strong>8&ndash;12 Menit</strong></div>
        </div>
        <div class="m-stat">
            <x-icon name="leaf" :size="22" class="gold" />
            <div><small>Wadah Ramah Lingkungan</small><strong class="gold">100% Biodegradable</strong></div>
        </div>
    </div>
</section>

<div id="menuAjax">

{{-- PENCARIAN --}}
<section class="m-search">
    <form method="GET" action="{{ route('menu.index') }}" class="m-search-row" role="search">
        @if ($kategoriId)
            <input type="hidden" name="kategori" value="{{ $kategoriId }}">
        @endif
        <label class="m-input">
            <x-icon name="search" :size="20" />
            <input type="text" id="searchInput" name="q" value="{{ $q }}" placeholder="Cari menu makanan atau minuman..." autocomplete="off" aria-label="Cari menu">
            @if ($q !== '')
                <a href="{{ route('menu.index', array_filter(['kategori' => $kategoriId])) }}" class="m-clear" aria-label="Hapus pencarian"><x-icon name="x-circle" :size="18" /></a>
            @endif
        </label>
        <button type="submit" class="btn btn-primary m-search-btn"><x-icon name="search" :size="16" /> Cari Menu</button>
    </form>

    <div class="m-popular">
        <span><x-icon name="flame" :size="14" /> Pencarian Populer:</span>
        @foreach (['Ayam Geprek', 'Soto Ayam', 'Es Teh', 'Paket Hemat'] as $kata)
            <a href="{{ route('menu.index', array_filter(['q' => $kata, 'kategori' => $kategoriId])) }}">{{ $kata }}</a>
        @endforeach
    </div>
</section>

{{-- KATEGORI --}}
<nav class="m-chips" aria-label="Kategori menu">
    <a href="{{ route('menu.index', array_filter(['q' => $q])) }}" class="{{ $kategoriId ? '' : 'on' }}">Semua Menu</a>
    @foreach ($categories as $c)
        <a href="{{ route('menu.index', array_filter(['q' => $q, 'kategori' => $c->id])) }}" class="{{ $kategoriId == $c->id ? 'on' : '' }}">{{ $c->nama_kategori }}</a>
    @endforeach
</nav>

{{-- DAFTAR MENU --}}
@if ($menus->count())
    <div class="m-grid">
        @foreach ($menus as $m)
            <article class="m-card">
                <div class="m-photo">
                    <img src="{{ $m->foto_url }}" alt="{{ $m->nama_menu }}" loading="lazy">
                    <span class="m-stan">Stan #{{ str_pad($m->tenant_id, 2, '0', STR_PAD_LEFT) }} &bull; {{ $m->tenant->nama_stan }}</span>
                    @if ($m->rating > 0)
                        <span class="m-rate"><x-icon name="star" :size="12" /> {{ number_format($m->rating, 1) }}{{ $m->jumlah_ulasan ? ' (' . $m->ulasan_singkat . ')' : '' }}</span>
                    @endif
                </div>
                <div class="m-body">
                    @if ($m->label)
                        <span class="m-label {{ $m->label_tipe ?? 'hijau' }}">{{ $m->label }}</span>
                    @endif
                    <h3>{{ $m->nama_menu }}</h3>
                    <p>{{ $m->deskripsi }}</p>
                    <div class="m-buy">
                        <div>
                            <small>Harga Satuan</small>
                            <strong>Rp {{ number_format($m->harga, 0, ',', '.') }}</strong>
                        </div>
                        @if ($m->status === 'tersedia' && $m->stok > 0)
                            <button type="button" class="m-add"
                                data-id="{{ $m->id }}"
                                data-nama="{{ $m->nama_menu }}"
                                data-harga="{{ (int) $m->harga }}"
                                data-stan-id="{{ $m->tenant_id }}"
                                data-stan="{{ $m->tenant->nama_stan }}">
                                <x-icon name="plus" :size="14" /> Tambah
                            </button>
                        @else
                            <button type="button" class="m-add" disabled>Habis</button>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="m-results">
        <span class="m-info">
            <x-icon name="utensils" :size="16" />
            Menampilkan <b>{{ $menus->firstItem() }}</b> &ndash; <b>{{ $menus->lastItem() }}</b>
            dari <b>{{ $menus->total() }}</b> menu {{ $stanTunggal ? $stanTunggal->nama_stan : 'semua stan' }}
        </span>
        @if ($menus->hasPages())
            @include('menu._pagination')
        @endif
    </div>
@else
    <div class="m-empty">
        <x-icon name="search" :size="32" />
        <h3>Menu tidak ditemukan</h3>
        <p>Coba kata kunci lain, atau tampilkan semua menu.</p>
        <a href="{{ route('menu.index') }}" class="btn btn-primary btn-sm">Lihat Semua Menu</a>
    </div>
@endif

</div>{{-- /menuAjax --}}

{{-- BAR KERANJANG (muncul setelah tombol + ditekan) --}}
<aside class="m-cartbar" id="cartBar" aria-live="polite" aria-hidden="true">
    <span class="m-cart-icon"><x-icon name="bag" :size="22" /></span>
    <div class="m-cart-info">
        <div class="m-cart-line">
            <strong id="cartCount">0 Menu Dipilih</strong>
            <span class="m-cart-pill" id="cartStan"></span>
        </div>
        <strong class="m-cart-total" id="cartTotal">Rp 0</strong>
    </div>
    <a href="{{ url('/keranjang') }}" class="btn btn-primary m-cart-btn">Lihat Keranjang <x-icon name="arrow-right" :size="16" /></a>
</aside>

@endsection

@push('scripts')
    <script src="{{ asset('js/menu.js') }}" defer></script>
@endpush