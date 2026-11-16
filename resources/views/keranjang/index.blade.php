@extends('layouts.pembeli')

@section('title', 'Keranjang Pesanan - Kantin UPNVJ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/keranjang.css') }}">
@endpush

@section('content')

{{-- STEPPER --}}
<ol class="k-steps" aria-label="Langkah pemesanan">
    <li class="done">
        <span class="k-step-dot">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </span>
        <span><small>Langkah 01</small><strong>Pilih Menu</strong></span>
    </li>
    <li class="line done"></li>
    <li class="on">
        <span class="k-step-dot">2</span>
        <span><small>Langkah 02</small><strong>Keranjang Pesanan</strong></span>
    </li>
    <li class="line"></li>
    <li>
        <span class="k-step-dot">3</span>
        <span><small>Langkah 03</small><strong>Pembayaran QRIS</strong></span>
    </li>
</ol>

<div class="k-top">
    <a href="{{ route('menu.index') }}" class="k-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Tambah Menu Lainnya
    </a>
    <span class="k-badge-campus">Kantin Sehat Bina Graha Bela Negara UPNVJ</span>
</div>

<div class="k-layout" id="kLayout"
     data-detail-url="{{ route('keranjang.detail') }}"
     data-login-url="{{ route('login') }}"
     data-auth="{{ auth()->check() ? '1' : '0' }}">

    <div class="k-main">
        {{-- OPSI PENYAJIAN --}}
        <section class="k-card k-opsi">
            <div>
                <h2>Opsi Penyajian Pesanan</h2>
                <p>Pilih apakah kamu ingin makan langsung di food court UPNVJ atau bawa pulang.</p>
            </div>
            <div class="k-toggle" role="group" aria-label="Opsi penyajian">
                <button type="button" class="on" data-opsi="makan">Makan di Tempat</button>
                <button type="button" data-opsi="bungkus">Bungkus (Take Away)</button>
            </div>
        </section>

        {{-- DAFTAR MENU PER STAN (diisi JavaScript) --}}
        <div id="kList" aria-live="polite">
            <div class="k-card k-loading">Memuat keranjang...</div>
        </div>
    </div>

    {{-- RINGKASAN BIAYA --}}
    <aside class="k-summary k-card">
        <div class="k-sum-head">
            <h2>Ringkasan Biaya</h2>
            <span class="k-count" id="kCount">0 Item</span>
        </div>

        <dl class="k-rows">
            <div><dt id="kSubLabel">Subtotal Menu</dt><dd id="kSubtotal">Rp 0</dd></div>
            <div class="green"><dt>Diskon Wadah Hijau</dt><dd id="kDiskon">-Rp 0</dd></div>
            <div><dt>Biaya Layanan &amp; Kebersihan</dt><dd class="green">Gratis (Rp 0)</dd></div>
            <div><dt>Biaya Pengemasan</dt><dd id="kPack">Rp 0</dd></div>
        </dl>

        <div class="k-total">
            <div><small>Total Tagihan</small><em>SUDAH TERMASUK PAJAK</em></div>
            <strong id="kTotal">Rp 0</strong>
        </div>

        <button type="button" class="k-pay" id="kPay" disabled>
            Lanjut ke Pembayaran QRIS
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
        <p class="k-warn" id="kWarn" hidden></p>

        <p class="k-note">Nomor antrean dan QR pickup terbit otomatis setelah verifikasi pembayaran QRIS berhasil.</p>
    </aside>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/keranjang.js') }}" defer></script>
@endpush