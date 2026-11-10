@extends('layouts.auth')

@section('title', 'Masuk ke Akun - Kantin UPNVJ')

@section('content')
<div class="login-page">
    <div class="login-card">

        <!-- Header -->
        <div class="login-header">
            <div class="login-brand">
                <div class="login-logo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9h18l-1.5-5h-15L3 9Z"/>
                        <path d="M5 9v11h14V9"/>
                        <path d="M3 9c0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0"/>
                    </svg>
                </div>

                <div>
                    <h2>Kantin UPNVJ</h2>
                    <span>LAYANAN TERPADU KAMPUS</span>
                </div>
            </div>

            <div class="sso-badge">
                <span>🛡</span> SSO 256-bit
            </div>
        </div>

        <!-- Title -->
        <div class="login-title">
            <h1>Masuk ke Akun</h1>
            <p>Pesan kuliner favorit tanpa antre atau kelola kedai kantin Anda.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <!-- Email -->
            <div class="auth-field">
                <div class="field-heading">
                    <label for="email">Email</label>
                    <span class="field-link">Gunakan email</span>
                </div>

                <div class="input-wrapper">
                    <span class="input-icon">@</span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                    >
                </div>

                @error('email')
                    <small class="auth-error">{{ $message }}</small>
                @enderror
            </div>

            <!-- Password -->
            <div class="auth-field">
                <div class="field-heading">
                    <label for="password">Kata Sandi</label>
                    <span class="field-link">Lupa Sandi?</span>
                </div>

                <div class="input-wrapper">
                    <span class="input-icon">♙</span>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••••••"
                        required
                        autocomplete="current-password"
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="password"
                        aria-label="Tampilkan kata sandi"
                    >
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                @error('password')
                    <small class="auth-error">{{ $message }}</small>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="login-options">
                <label class="remember-label">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <span class="secure-badge">Aman &amp; Terenkripsi</span>
            </div>

            <!-- Button -->
            <button type="submit" class="login-submit">
                Masuk ke Akun
                <span>→</span>
            </button>

            @if(session('success'))
                <div class="auth-success">
                    {{ session('success') }}
                </div>
            @endif

        </form>

        <!-- Register Link -->
        <div class="login-register">
            <p>Belum punya akun Kantin? <a href="{{ route('register') }}">Daftar di sini</a></p>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            Universitas Pembangunan Nasional Veteran Jakarta • Kampus Bela Negara
        </div>

    </div>
</div>
@endsection
