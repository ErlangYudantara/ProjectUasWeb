@extends('layouts.pembeli')

@section('title', 'Profil Pengguna - Kantin UPNVJ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush

@section('topnav')
    <a href="{{ route('menu.index') }}">Menu Kantin</a>
    <a href="#">Status Pesanan</a>
    <a href="#">Riwayat</a>
@endsection

@section('content')
@php
    $peran = ['pembeli' => 'Pembeli', 'penjual' => 'Penjual', 'mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen'][$user->role] ?? ucfirst($user->role);
    $inisial = collect(preg_split('/\s+/', trim($user->nama)))->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $username = \Illuminate\Support\Str::before($user->email, '@');
@endphp

<nav class="pf-crumb" aria-label="Breadcrumb">
    <a href="{{ route('landing') }}"><x-icon name="home" :size="13" /> Beranda</a>
    <span>/</span>
    <span>Pengaturan Akun</span>
    <span>/</span>
    <strong>Profil Pengguna</strong>
</nav>

<div class="pf-top">
    <div>
        <h1>Profil Akun Civitas UPNVJ</h1>
        <p>Kelola informasi data akun, kredensial login, dan keamanan kata sandi Anda.</p>
    </div>
    <span class="pf-sso"><i></i> Akun Aktif &bull; Kampus Pondok Labu</span>
</div>

<div class="pf-grid">

    {{-- KARTU PROFIL --}}
    <aside class="pf-side">
        <div class="pf-ava">
            <span>{{ $inisial }}</span>
            <button type="button" class="pf-cam" aria-label="Ubah foto profil" title="Ubah foto profil (segera hadir)"><x-icon name="camera" :size="14" /></button>
        </div>
        <h2>{{ $user->nama }}</h2>
        <p>{{ $peran }}@if ($user->nomor_identitas) &bull; {{ $user->nomor_identitas }}@endif</p>
        <span class="pf-badge"><x-icon name="check" :size="14" /> Akun Terverifikasi</span>
    </aside>

    {{-- PENGATURAN --}}
    <form method="POST" action="{{ route('profil.password') }}" class="pf-main">
        @csrf
        @method('PUT')

        <header class="pf-main-head">
            <h2><x-icon name="user" :size="22" /> Pengaturan Profil &amp; Keamanan</h2>
            <p>Perbarui informasi kontak dan kata sandi akun Kantin UPNVJ Anda.</p>
        </header>

        @if (session('success'))
            <div class="pf-alert" role="status">{{ session('success') }}</div>
        @endif

        {{-- Informasi akun (hanya baca) --}}
        <section class="pf-sec">
            <h3><x-icon name="id-card" :size="18" /> Informasi Akun</h3>
            <div class="pf-cols">
                <div class="pf-field">
                    <label for="username">Username</label>
                    <div class="pf-ro">
                        <span class="pf-at">@</span>
                        <input type="text" id="username" value="{{ $username }}" readonly>
                        <x-icon name="lock" :size="15" class="pf-lock" />
                    </div>
                </div>
                <div class="pf-field">
                    <label for="email">Email Resmi Kampus</label>
                    <div class="pf-ro">
                        <x-icon name="mail" :size="18" class="pf-lead" />
                        <input type="text" id="email" value="{{ $user->email }}" readonly>
                        <x-icon name="lock" :size="15" class="pf-lock" />
                    </div>
                </div>
            </div>
        </section>

        {{-- Ganti kata sandi --}}
        <section class="pf-sec">
            <h3><x-icon name="lock" :size="18" /> Ganti Kata Sandi</h3>

            <div class="pf-field">
                <label for="current_password">Kata Sandi Saat Ini</label>
                <div class="pf-pw">
                    <input type="password" id="current_password" name="current_password" placeholder="Masukkan kata sandi lama Anda" autocomplete="current-password" required>
                    <button type="button" class="pf-eye" data-target="current_password" aria-label="Tampilkan kata sandi">
                        <x-icon name="eye" :size="18" class="eye-on" /><x-icon name="eye-off" :size="18" class="eye-off" />
                    </button>
                </div>
                @error('current_password', 'password') <small class="pf-error">{{ $message }}</small> @enderror
            </div>

            <div class="pf-cols">
                <div class="pf-field">
                    <label for="password">Kata Sandi Baru</label>
                    <div class="pf-pw">
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter kombinasi" autocomplete="new-password" required>
                        <button type="button" class="pf-eye" data-target="password" aria-label="Tampilkan kata sandi">
                            <x-icon name="eye" :size="18" class="eye-on" /><x-icon name="eye-off" :size="18" class="eye-off" />
                        </button>
                    </div>
                    @error('password', 'password') <small class="pf-error">{{ $message }}</small> @enderror
                </div>
                <div class="pf-field">
                    <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <div class="pf-pw">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang kata sandi baru" autocomplete="new-password" required>
                        <button type="button" class="pf-eye" data-target="password_confirmation" aria-label="Tampilkan kata sandi">
                            <x-icon name="eye" :size="18" class="eye-on" /><x-icon name="eye-off" :size="18" class="eye-off" />
                        </button>
                    </div>
                </div>
            </div>

            <p class="pf-hint"><x-icon name="info" :size="14" /> Gunakan minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, angka, dan simbol.</p>
        </section>

        <footer class="pf-actions">
            <a href="{{ route('menu.index') }}">Batal</a>
            <button type="submit" class="pf-save"><x-icon name="save" :size="16" /> Simpan Perubahan</button>
        </footer>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/profil.js') }}" defer></script>
@endpush
