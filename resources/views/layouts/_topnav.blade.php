@php
    $statusUrl = auth()->check()
        ? (\Illuminate\Support\Facades\Route::has('pesanan.status') ? route('pesanan.status') : url('/status-pesanan'))
        : route('login');
@endphp

<a href="{{ route('landing') }}"
   class="m-nav-link {{ request()->routeIs('landing') ? 'active' : '' }}">Beranda</a>

<a href="{{ route('menu.index') }}"
   class="m-nav-link {{ request()->routeIs('menu.index') ? 'active' : '' }}">Menu &amp; Pesanan</a>

<a href="{{ $statusUrl }}"
   class="m-nav-link {{ request()->is('status-pesanan*') ? 'active' : '' }}">Status Pesanan</a>