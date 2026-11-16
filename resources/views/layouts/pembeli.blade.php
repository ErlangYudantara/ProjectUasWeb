<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kantin UPNVJ')</title>
    <meta name="user-id" content="{{ auth()->id() ?? 'guest' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/kantin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-menu.css') }}">
    @stack('styles')
</head>
<body>

<header class="m-topbar">
    <a href="{{ route('landing') }}" class="m-brand">
        <img src="{{ asset('images/logo-upnvj.png') }}" alt="Logo UPN Veteran Jakarta">
        <span><strong>Kantin UPNVJ</strong><small>Green Campus</small></span>
    </a>

    @hasSection('topnav')
        <nav class="m-topnav" aria-label="Navigasi utama">@yield('topnav')</nav>
    @endif

    <div class="m-actions">
        <a href="#" class="m-mitra"><x-icon name="store" :size="16" /> Mode Mitra Warung</a>

        @auth
            <x-user-menu />
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk/Daftar</a>
        @endauth
    </div>
</header>

<main class="m-page">
    @yield('content')
</main>

<footer class="m-footer">
    <div class="m-footer-brand">
        <span class="m-footer-icon"><x-icon name="leaf" :size="18" /></span>
        <span><strong>UPNVJ Green Cafeteria</strong><small>Pusat Kuliner Sehat &amp; Berkelanjutan Kampus Bela Negara</small></span>
    </div>
    <div class="m-footer-links">
        <a href="#">Pusat Bantuan</a>
        <a href="#">Ketentuan Layanan</a>
        <span>&copy; {{ date('Y') }} UPN Veteran Jakarta</span>
    </div>
</footer>

<script src="{{ asset('js/dropdown.js') }}" defer></script>
@stack('scripts')
</body>
</html>