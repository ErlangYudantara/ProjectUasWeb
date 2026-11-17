@extends('layouts.pembeli')

@section('title', 'Status Pesanan - Kantin UPNVJ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/status-pesanan.css') }}">
@endpush

{{-- NAVIGASI HEADER --}}
@section('topnav')
    <a href="{{ route('landing') }}" class="m-nav-link">
        Beranda
    </a>

    <a href="{{ route('menu.index') }}" class="m-nav-link">
        Menu &amp; Pesanan
    </a>

    <a href="{{ route('status-pesanan.show', $order->id) }}"
       class="m-nav-link active">
        Status Pesanan
    </a>
@endsection

@section('content')

@php
    $subtotal = $order->details->sum('subtotal');
    $totalQty = $order->details->sum('jumlah');
    $diskon = min($totalQty * 500, $subtotal);

    $pembayaranBerhasil = $order->payment &&
        $order->payment->status_pembayaran === 'berhasil';

    $kelompokStan = $order->details->groupBy(
        fn ($detail) => $detail->menu->tenant_id
    );

    $daftarStatus = $order->orderTenants;

    $semuaStanSelesai = $daftarStatus->isNotEmpty()
        && $daftarStatus->every(
            fn ($stan) => $stan->status_pesanan === 'selesai'
        );

    $adaStanMemasak = $daftarStatus->contains(
        fn ($stan) => $stan->status_pesanan === 'sedang_disiapkan'
    );

    $semuaStanSiap = $daftarStatus->isNotEmpty()
        && $daftarStatus->every(
            fn ($stan) => in_array(
                $stan->status_pesanan,
                ['siap_diambil', 'selesai']
            )
        );

    $tahapMemasak = $adaStanMemasak || $semuaStanSiap || $semuaStanSelesai;
    $tahapSiap = $semuaStanSiap || $semuaStanSelesai;

    $stanPertama = $order->details->first()?->menu?->tenant;

    $nomorAntreanDemo = 'K-' . str_pad($order->id, 3, '0', STR_PAD_LEFT);
@endphp

<div class="status-page">

    {{-- NOTIFIKASI PEMBAYARAN --}}
    @if(session('success') && $pembayaranBerhasil)
        <div class="status-notification" id="statusNotification">

            <div class="notification-heading">
                <h2>Notification</h2>

                <button type="button"
                    class="notification-close"
                    id="notificationClose"
                    aria-label="Tutup notifikasi">
                    ×
                </button>
            </div>

            <div class="notification-content">
                <div class="notification-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="4" width="14" height="17" rx="2"/>
                        <path d="M9 4V2h6v2M9 10h6M9 14h6M9 18h3"/>
                    </svg>
                </div>

                <div>
                    <strong>Pembayaran Berhasil</strong>
                    <small>Baru saja</small>
                </div>
            </div>

        </div>
    @endif

    {{-- KARTU UTAMA --}}
    <main class="status-main-card">

        {{-- INFORMASI TRANSAKSI --}}
        <div class="status-transaction">
            <span>• Transaksi #{{ $order->nomor_pesanan }}</span>

            <span class="status-time">
                <svg viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>

                Pukul {{ $order->tanggal_pesanan->format('H.i') }} WIB
            </span>
        </div>

        {{-- NOMOR ANTREAN --}}
        <div class="status-queue">
            <small>NOMOR ANTREAN ANDA</small>

            <div class="queue-number">
                <strong>#{{ $nomorAntreanDemo }}</strong>

                <span>
                    {{ $stanPertama->nama_stan ?? 'KANTIN UPNVJ' }}
                    @if($kelompokStan->count() === 1)
                        • STAN
                    @endif
                </span>
            </div>
        </div>

        {{-- STATUS UTAMA --}}
        <div class="status-title">
            <span class="status-title-icon">
                @if($semuaStanSelesai || $semuaStanSiap)
                    ✓
                @else
                    ⌕
                @endif
            </span>

            <h1>
                @if($semuaStanSelesai)
                    Pesanan Selesai
                @elseif($semuaStanSiap)
                    Pesanan Siap Diambil
                @elseif($tahapMemasak)
                    Pesanan Sedang Dibuat
                @else
                    Pesanan Menunggu Konfirmasi
                @endif
            </h1>
        </div>

        <div class="status-estimate">
            <span>◴</span>

            <strong>
                @if($semuaStanSelesai)
                    Pesanan Selesai
                @elseif($semuaStanSiap)
                    Siap Diambil
                @elseif($tahapMemasak)
                    Sedang Diproses
                @else
                    Menunggu Stan
                @endif
            </strong>

            <small>{{ $kelompokStan->count() }} Stan Kantin</small>
        </div>

        {{-- PROGRESS PESANAN --}}
        <div class="status-progress">

            <div class="progress-item completed">
                <div class="progress-icon">✓</div>

                <strong>1. Pembayaran Berhasil</strong>

                <small>
                    {{ $order->payment?->tanggal_bayar
                        ? $order->payment->tanggal_bayar->format('H.i') . ' WIB (QRIS)'
                        : 'QRIS' }}
                </small>
            </div>

            <div class="progress-item {{ $tahapMemasak ? 'completed' : 'current' }}">
                <div class="progress-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 13h16M6 13l1 7h10l1-7M8 10a4 4 0 0 1 8 0M12 3v2"/>
                    </svg>
                </div>

                <strong>2. Sedang Dimasak</strong>

                <small>
                    {{ $tahapMemasak ? 'Proses Menggoreng' : 'Menunggu Proses' }}
                </small>
            </div>

            <div class="progress-item {{ $tahapSiap ? 'completed' : '' }}">
                <div class="progress-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="9" width="18" height="11" rx="2"/>
                        <path d="M7 9V5h10v4M8 13v3M12 13v3M16 13v3"/>
                    </svg>
                </div>

                <strong>3. Siap Diambil</strong>
                <small>Di Lokasi Stan</small>
            </div>

        </div>

        {{-- LOKASI DAN RINGKASAN MENU --}}
        <div class="status-detail-grid">

            {{-- LOKASI PENGAMBILAN --}}
            <section class="status-detail-box">

                <div class="detail-box-heading">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                        <circle cx="12" cy="10" r="2.5"/>
                    </svg>

                    <strong>Lokasi Pengambilan</strong>
                </div>

                @foreach($kelompokStan as $tenantId => $details)
                    @php
                        $tenant = $details->first()->menu->tenant;
                        $statusStan = $daftarStatus->firstWhere('tenant_id', $tenantId);
                    @endphp

                    <div class="pickup-stan">
                        <strong class="pickup-stan-name">
                            {{ $tenant->nama_stan ?? 'Stan Kantin' }}
                        </strong>

                        <p>
                            {{ $tenant->lokasi ?? 'Lokasi stan belum tersedia' }}
                        </p>

                        <small>
                            <span>⌖</span>
                            Silakan ambil pesanan di stan ini
                        </small>

                        <span class="pickup-status">
                            {{ str_replace(
                                '_',
                                ' ',
                                ucfirst($statusStan?->status_pesanan ?? 'menunggu_konfirmasi')
                            ) }}
                        </span>
                    </div>
                @endforeach

                <div class="pickup-time">
                    Waktu Pemesanan:
                    {{ $order->tanggal_pesanan->format('H.i') }} WIB
                </div>

            </section>

            {{-- RINGKASAN MENU --}}
            <section class="status-detail-box">

                <div class="detail-box-heading">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="3" width="16" height="18" rx="2"/>
                        <path d="M8 7h8M8 11h8M8 15h5"/>
                    </svg>

                    <strong>Ringkasan Menu</strong>

                    <span class="menu-count">{{ $totalQty }} Item</span>
                </div>

                @foreach($order->details as $detail)
                    <div class="status-menu-row">

                        <span class="menu-qty">
                            {{ $detail->jumlah }}x
                        </span>

                        <div class="menu-info">
                            <strong>{{ $detail->menu->nama_menu }}</strong>

                            @if($detail->catatan)
                                <small>{{ $detail->catatan }}</small>
                            @endif
                        </div>

                        <strong class="menu-price">
                            Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                        </strong>

                    </div>
                @endforeach

                <div class="status-menu-total">
                    <span>Total Pembayaran</span>

                    <strong>
                        Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                        <small>(Lunas)</small>
                    </strong>
                </div>

            </section>

        </div>

        {{-- TOMBOL PESAN MENU LAIN --}}
        <div class="status-actions">
            <a href="{{ route('menu.index') }}" class="status-order-again">
                <span>⊕</span>
                Pesan Menu Lain
            </a>
        </div>

    </main>

    {{-- INFORMASI TAMBAHAN --}}
    <div class="status-benefits">

        <div class="benefit-item">
            <span>☻</span>
            <strong>Tanpa Antre Berdiri</strong>
            <p>Duduk santai di bangku kantin hingga notifikasi berbunyi.</p>
        </div>

        <div class="benefit-item">
            <span>▦</span>
            <strong>Verifikasi Cepat</strong>
            <p>Cukup sebutkan nomor antrean atau tunjukkan layar ini.</p>
        </div>

        <div class="benefit-item">
            <span>♻</span>
            <strong>Green Campus UPNVJ</strong>
            <p>Kembalikan baki makanan ke drop-point terdekat.</p>
        </div>

    </div>

</div>

@endsection

{{-- NOTIFIKASI OTOMATIS HILANG --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const notification = document.getElementById('statusNotification');
    const closeButton = document.getElementById('notificationClose');

    if (!notification) return;

    let removed = false;

    function closeNotification() {
        if (removed) return;

        removed = true;
        notification.classList.add('notification-hide');

        setTimeout(() => {
            notification.remove();
        }, 300);
    }

    const notificationTimer = setTimeout(closeNotification, 2000);

    closeButton?.addEventListener('click', function () {
        clearTimeout(notificationTimer);
        closeNotification();
    });
});
</script>
@endpush
