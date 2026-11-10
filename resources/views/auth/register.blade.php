@extends('layouts.auth')

@section('title', 'Daftar Akun Baru - Kantin UPNVJ')

@section('content')

<div class="register-page">

    <!-- Header Universitas -->
    <div class="register-university">
        <div class="university-logo">UPN</div>

        <div class="university-text">
            <strong>GREEN CAMPUS KANTIN</strong>
            <span>Universitas Pembangunan Nasional Veteran Jakarta</span>
        </div>
    </div>

    <!-- Register Card -->
    <div class="register-card">

        <!-- Icon -->
        <div class="register-icon">
            <svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.3"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 3v7"/>
                <path d="M4 3v4a3 3 0 0 0 6 0V3"/>
                <path d="M7 10v11"/>
                <path d="M17 3v18"/>
                <path d="M17 3c3 3 3 7 0 9"/>
            </svg>
        </div>

        <!-- Title -->
        <div class="register-heading">
            <h1>Daftar Akun Baru</h1>
            <p>
                Buat akun untuk memesan makanan di Kantin
                <br>
                UPNVJ tanpa antre
            </p>
        </div>

        <!-- Register Form -->
        <form action="{{ route('register.process') }}" method="POST">
            @csrf

            <!-- Nama Lengkap -->
            <div class="register-field">
                <label for="nama">Nama Lengkap</label>

                <div class="register-input">
                    <span class="register-input-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M5 21v-2a7 7 0 0 1 14 0v2Z"/>
                        </svg>
                    </span>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Nama Lengkap Anda"
                        value="{{ old('nama') }}"
                        required
                        autocomplete="name"
                    >
                </div>

                @error('nama')
                    <small class="auth-error">{{ $message }}</small>
                @enderror
            </div>

            <!-- Email -->
            <div class="register-field">
                <label for="register-email">Email</label>

                <div class="register-input">
                    <span class="register-input-icon email-icon">@</span>

                    <input
                        type="email"
                        id="register-email"
                        name="email"
                        placeholder="nama@upnvj.ac.id"
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
            <div class="register-field">
                <label for="register-password">Kata Sandi</label>

                <div class="register-input">
                    <span class="register-input-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="10" width="14" height="11" rx="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            <path d="M12 14v3"/>
                        </svg>
                    </span>

                    <input
                        type="password"
                        id="register-password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        required
                        autocomplete="new-password"
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="register-password"
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

            <!-- Konfirmasi Password -->
            <div class="register-field">
                <label for="password-confirmation">
                    Konfirmasi Kata Sandi
                </label>

                <div class="register-input">
                    <span class="register-input-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 3-6.7L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>
                    </span>

                    <input
                        type="password"
                        id="password-confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi kata sandi"
                        required
                        autocomplete="new-password"
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="password-confirmation"
                        aria-label="Tampilkan konfirmasi kata sandi"
                    >
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Ketentuan Layanan -->
            <div class="register-terms">
                <input
                    type="checkbox"
                    id="terms"
                    name="terms"
                    value="1"
                    required
                >

                <label for="terms">
                    Saya menyetujui
                    <a href="#" onclick="return false;">Ketentuan Layanan</a>
                    &amp;
                    <a href="#" onclick="return false;">Tata Tertib Green Kantin</a>
                    UPNVJ.
                </label>
            </div>

            @error('terms')
                <small class="auth-error">{{ $message }}</small>
            @enderror

            <!-- Register Button -->
            <button type="submit" class="register-submit">
                Daftar Sekarang
                <span>→</span>
            </button>

        </form>

        <!-- Divider -->
        <div class="register-divider">
            <span></span>
            <p>ATAU</p>
            <span></span>
        </div>

        <!-- Login Link -->
        <div class="register-login">
            <p>Sudah punya akun Kantin?</p>
            <a href="{{ route('login') }}">
                Masuk Sekarang <span>⇥</span>
            </a>
        </div>

    </div>

    <!-- SSO Footer -->
    <div class="register-sso">
        <svg viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
        <span>Single Sign-On Terintegrasi UPNVJ</span>
    </div>

    <!-- Copyright -->
    <div class="register-copyright">
        © 2025 UPN Veteran Jakarta. Sistem Pemesanan Kantin Terpadu.
        <span>•</span>
        Pusat Bantuan Kampus
    </div>

</div>

@endsection
