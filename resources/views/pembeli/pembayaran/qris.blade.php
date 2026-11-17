@extends('layouts.pembeli') {{-- CEK: samakan dengan @extends di versi sebelumnya (jangan diubah kalau tadi sudah jalan) --}}

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pembayaran-qris.css') }}">
@endpush

@section('content') {{-- CEK: samakan nama section dengan versi sebelumnya --}}
@php
    $user = auth()->user();
    $namaPemesan = $user->name ?? $user->nama ?? 'Pembeli';
    $nim = $user->nim ?? null;
    $fakultas = $user->fakultas ?? null;

    // CEK: nama kolom metode konsumsi di tabel orders (kalau tidak ada, kotaknya disembunyikan)
    $metode = $order->metode_konsumsi ?? $order->tipe_pesanan ?? null;
    $noMeja = $order->nomor_meja ?? $order->no_meja ?? null;
    $isDineIn = $metode && str_contains(strtolower($metode), 'dine');

    $perStan = $order->details->groupBy(fn ($d) => $d->menu->tenant_id ?? 0);
    $subtotal = $order->details->sum('subtotal');
    $biayaLayanan = 2000;
    $total = $subtotal; // biaya layanan gratis, subsidi Rp 0
    $jumlahMenu = $order->details->count();
    $referensi = 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
@endphp

<div class="qp">

    <div class="qp-topbar">
        {{-- CEK: ganti url('/') dengan halaman menu makanan --}}
        <a href="{{ url('/menu') }}" class="qp-back">&larr; Kembali ke Menu Makanan</a>
        <span class="qp-secure"><i></i> SESI PEMBAYARAN AMAN &bull; MODE DEMO</span>
    </div>

    @if (session('success')) <div class="qp-alert">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="qp-alert error">{{ session('error') }}</div> @endif

    <div class="qp-grid">

        {{-- ================= KIRI ================= --}}
        <div class="qp-left">

            <section class="qp-card">
                <div class="qp-card-head">
                    <h2>Informasi Pemesanan</h2>
                    <span class="qp-pill green">SIVITAS UPNVJ</span>
                </div>

                <div class="qp-info-grid {{ $metode ? '' : 'single' }}">
                    <div class="qp-box">
                        <small>PEMESAN</small>
                        <strong>{{ $namaPemesan }}</strong>
                        @if ($nim || $fakultas)
                            <span>{{ $nim }}{{ $nim && $fakultas ? ' • ' : '' }}{{ $fakultas }}</span>
                        @endif
                    </div>

                    @if ($metode)
                        <div class="qp-box blue">
                            <small>METODE KONSUMSI</small>
                            <div class="qp-toggle">
                                <span class="{{ $isDineIn ? 'on' : '' }}">
                                    Dine In{{ $noMeja ? ' (Meja '.$noMeja.')' : '' }}
                                </span>
                                <span class="{{ $isDineIn ? '' : 'on' }}">Bawa Pulang</span>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <section class="qp-card">
                <div class="qp-card-head">
                    <h2>Rincian Menu</h2>
                    <span class="qp-muted">{{ $jumlahMenu }} Menu &bull; {{ $perStan->count() }} Stand Kantin</span>
                </div>

                @foreach ($perStan as $tenantId => $items)
                    @php
                        $tenant = optional($items->first()->menu)->tenant;
                        $lokasi = $tenant->lokasi ?? null; // CEK: nama kolom lokasi stan
                    @endphp
                    <div class="qp-stan">
                        <div class="qp-stan-head">
                            <span class="qp-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3>{{ $tenant->nama_stan ?? $tenant->nama ?? 'Stan #'.$tenantId }}</h3>
                                @if ($lokasi) <small>{{ $lokasi }}</small> @endif
                            </div>
                            <span class="qp-pill orange">Stand #{{ str_pad($tenantId, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        @foreach ($items as $d)
                            @php
                                $foto = $d->menu->foto ?? $d->menu->gambar ?? null; // CEK: nama kolom foto menu
                                $opsi = $d->opsi_penyajian ?? null;                  // CEK: kolom opsi_penyajian
                            @endphp
                            <div class="qp-item">
                                @if ($foto)
                                    <img src="{{ asset('storage/'.$foto) }}" alt="" onerror="this.style.visibility='hidden'">
                                @else
                                    <div class="qp-noimg"></div>
                                @endif
                                <div class="qp-item-info">
                                    <strong>{{ $d->menu->nama_menu ?? $d->menu->nama ?? 'Menu' }}</strong>
                                    @if ($opsi || $d->catatan)
                                        <small>{{ collect([$opsi, $d->catatan])->filter()->implode(' • ') }}</small>
                                    @endif
                                </div>
                                <div class="qp-item-price">
                                    <small>{{ $d->jumlah }}x</small>
                                    <strong>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <div class="qp-sum">
                    <div class="qp-sum-row"><span>Subtotal Pesanan</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                    <div class="qp-sum-row">
                        <span>Biaya Layanan Platform</span>
                        <span><s>Rp {{ number_format($biayaLayanan, 0, ',', '.') }}</s> <b class="qp-free">GRATIS</b></span>
                    </div>
                    <div class="qp-sum-row"><span>Subsidi Mahasiswa Bela Negara</span><span>- Rp 0</span></div>
                    <div class="qp-sum-total">
                        <div>
                            <strong>Total Pembayaran</strong>
                            <small>Termasuk pajak &amp; izin resmi UPNVJ</small>
                        </div>
                        <strong class="qp-total-amount">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </section>
        </div>

        {{-- ================= KANAN ================= --}}
        <aside class="qp-right">
            <section class="qp-card qp-pay">
                <div class="qp-pay-head">
                    <div>
                        <strong>QRIS</strong>
                        <small>Pembayaran Digital Kantin</small>
                    </div>
                    <span class="qp-pill blue">DEMO</span>
                </div>

                <div class="qp-merchant"><span>Merchant:</span><b>KANTIN TERPADU UPNVJ - JAKARTA</b></div>

                <div class="qp-timer">
                    <span>Selesaikan dalam</span>
                    <strong id="qp-countdown">05:00</strong>
                </div>

                <div class="qp-qr">
                    <svg viewBox="0 0 29 29" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges">
                        <g fill="#0f172a">
                            <path d="M1 1h9v9H1z M2 2v7h7V2z"/><rect x="3.5" y="3.5" width="4" height="4"/>
                            <path d="M19 1h9v9h-9z M20 2v7h7V2z"/><rect x="21.5" y="3.5" width="4" height="4"/>
                            <path d="M1 19h9v9H1z M2 20v7h7v-7z"/><rect x="3.5" y="21.5" width="4" height="4"/>
                            <rect x="12" y="1" width="2" height="2"/><rect x="15" y="2" width="3" height="1"/>
                            <rect x="12" y="4" width="2" height="3"/><rect x="15" y="5" width="3" height="2"/>
                            <rect x="1" y="12" width="3" height="2"/><rect x="5" y="12" width="2" height="2"/>
                            <rect x="9" y="12" width="3" height="2"/><rect x="14" y="12" width="2" height="2"/>
                            <rect x="19" y="12" width="2" height="2"/><rect x="23" y="12" width="3" height="3"/>
                            <rect x="2" y="15" width="3" height="3"/><rect x="7" y="16" width="2" height="2"/>
                            <rect x="12" y="15" width="3" height="3"/><rect x="17" y="16" width="2" height="2"/>
                            <rect x="21" y="16" width="3" height="2"/><rect x="26" y="17" width="2" height="2"/>
                            <rect x="12" y="20" width="2" height="2"/><rect x="15" y="21" width="3" height="2"/>
                            <rect x="20" y="20" width="3" height="3"/><rect x="25" y="21" width="3" height="2"/>
                            <rect x="12" y="24" width="3" height="2"/><rect x="17" y="25" width="2" height="3"/>
                            <rect x="21" y="25" width="4" height="2"/><rect x="26" y="25" width="2" height="2"/>
                        </g>
                        <circle cx="14.5" cy="14.5" r="2.4" fill="#fff"/>
                        <circle cx="14.5" cy="14.5" r="1.8" fill="#166534"/>
                    </svg>
                    <small>REF: {{ $referensi }} &bull; BUKAN QR SUNGGUHAN</small>
                </div>

                <div class="qp-wallets">
                    @foreach (['GoPay','OVO','DANA','ShopeePay','BCA Mobile','Livin Mandiri','BRImo'] as $w)
                        <span>{{ $w }}</span>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('pembayaran.simulasi', $order->id) }}">
                    @csrf
                    <button type="submit" id="qp-pay-btn" class="qp-btn primary">&#10003; Saya Sudah Membayar</button>
                </form>
                <button type="button" class="qp-btn soft" onclick="window.location.reload()">&#8635; Cek Status Otomatis</button>

                <p class="qp-demo-note">Ini simulasi pembayaran untuk keperluan tugas. Tidak ada transaksi uang sungguhan.</p>
            </section>

            <section class="qp-card qp-steps">
                <h4>3 Langkah Mudah Pembayaran</h4>
                <div><span>1</span><p>Buka aplikasi mobile banking atau dompet digital (e-wallet) favoritmu.</p></div>
                <div><span>2</span><p>Pilih menu <b>Bayar / QRIS</b>, lalu pindai (scan) kode QR di atas.</p></div>
                <div><span>3</span><p>Periksa nama penerima <b>Kantin UPNVJ</b>, masukkan PIN Anda, lalu klik tombol verifikasi di atas.</p></div>
            </section>
        </aside>

    </div>
</div>

<script>
    (function () {
        var sisa = 5 * 60; // 5 menit, dihitung dari saat halaman dibuka
        var el = document.getElementById('qp-countdown');
        var btn = document.getElementById('qp-pay-btn');
        function tampil() {
            var m = String(Math.floor(sisa / 60)).padStart(2, '0');
            var s = String(sisa % 60).padStart(2, '0');
            el.textContent = m + ':' + s;
        }
        tampil();
        var t = setInterval(function () {
            sisa--;
            if (sisa <= 0) {
                clearInterval(t);
                el.textContent = '00:00';
                btn.disabled = true;
                btn.textContent = 'Waktu habis, muat ulang halaman';
                return;
            }
            tampil();
        }, 1000);
    })();
</script>
@endsection