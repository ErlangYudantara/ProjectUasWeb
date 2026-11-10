<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kantin Digital UPN Veteran Jakarta')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/kantin.css') }}">
    @stack('styles')
</head>
<body>

<header class="navbar">
    <a href="{{ url('/') }}" class="brand">
        <img src="{{ asset('images/logo-upnvj.png') }}" alt="Logo UPN Veteran Jakarta" class="brand-logo">
        <span class="brand-text">
            <span class="brand-name">Kantin UPNVJ</span>
            <span class="brand-sub">Green Campus Food Hall</span>
        </span>
    </a>

    <nav class="nav-links" aria-label="Navigasi utama">
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Beranda</a>
        <a href="{{ url('/#stan') }}">Menu &amp; Pesanan</a>
        <a href="{{ url('/pesanan') }}">Status Pesanan</a>
    </nav>

    <div class="nav-actions">
        <button type="button" class="bell" aria-label="Notifikasi">
            <x-icon name="bell" :size="18" />
            <span class="bell-dot"></span>
        </button>
        <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">Masuk/Daftar</a>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="footer">
    <div class="footer-brand">
        <x-icon name="leaf" :size="20" />
        <span>Kantin UPNVJ Green Campus</span>
    </div>
    <div class="footer-info">
        <span><x-icon name="clock" :size="14" /> Senin - Jumat: 07.30 - 17.00 WIB</span>
        <span class="dot">&bull;</span>
        <span><x-icon name="check" :size="14" class="gold" /> Zona Bebas Plastik</span>
    </div>
    <div class="footer-copy">&copy; {{ date('Y') }} UPN Veteran Jakarta.</div>
</footer>

</body>
</html>