@extends('layouts.pembeli')

@section('title', 'Daftar Stan - Kantin UPNVJ')

@section('topnav')
    @include('layouts._topnav')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stan.css') }}">
@endpush

@section('content')

<section class="s-search">
    <form method="GET" action="{{ route('stan.index') }}" role="search" id="stan-search-form">
        <label class="s-input">
            <x-icon name="search" :size="20" />
            <input type="text" name="q" id="stan-search-input" value="{{ $q }}" placeholder="Cari nama stan..." autocomplete="off" aria-label="Cari stan">
        </label>
        <button type="submit" class="btn btn-primary s-btn"><x-icon name="search" :size="16" /> Cari Stan</button>
    </form>
</section>

{{-- Bagian ini diganti otomatis saat mengetik di kolom pencarian --}}
<div id="stan-results" aria-live="polite">
@if ($tenants->count())
    <section class="s-panel">
        <div class="s-grid">
            @foreach ($tenants as $t)
                @php
                    $foto  = $t->foto_stan_url;
                    $buka  = $t->jam_buka ?? null;
                    $tutup = $t->jam_tutup ?? null;
                    $rating = $t->menus_avg_rating;
                @endphp
                <article class="s-card">
                    <div class="s-photo">
                        <img src="{{ $foto }}" alt="{{ $t->nama_stan }}" loading="lazy">
                        <span class="s-open"><x-icon name="timer" :size="12" /> BUKA</span>
                        @if ($rating)
                            <span class="s-rate"><x-icon name="star" :size="12" /> {{ number_format($rating, 1) }}</span>
                        @endif
                    </div>
                    <div class="s-body">
                        <div class="s-title">
                            <h3>{{ $t->nama_stan }}</h3>
                            <small>{{ $t->menus_count }} Menu</small>
                        </div>
                        @if ($t->deskripsi ?? null)
                            <p>{{ $t->deskripsi }}</p>
                        @endif
                        @if ($buka && $tutup)
                            <div class="s-jam"><x-icon name="timer" :size="14" /> {{ substr($buka, 0, 5) }} - {{ substr($tutup, 0, 5) }} WIB</div>
                        @endif
                        <a href="{{ route('menu.index', ['stan' => $t->id]) }}" class="s-go">
                            <x-icon name="utensils" :size="16" /> Lihat Menu Stan
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@else
    <div class="s-empty">
        <h3>Stan tidak ditemukan</h3>
        <p>Coba kata kunci lain.</p>
        <a href="{{ route('stan.index') }}" class="btn btn-primary btn-sm" id="stan-reset">Lihat Semua Stan</a>
    </div>
@endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form    = document.getElementById('stan-search-form');
    var input   = document.getElementById('stan-search-input');
    var results = document.getElementById('stan-results');
    if (!form || !input || !results) return;

    var timer = null;
    var controller = null;

    function cari() {
        var q = input.value.trim();
        var url = form.action + (q ? '?q=' + encodeURIComponent(q) : '');

        // batalkan permintaan sebelumnya supaya hasil lama tidak menimpa yang baru
        if (controller) controller.abort();
        controller = new AbortController();

        results.style.opacity = '.5';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var baru = doc.getElementById('stan-results');
            if (baru) results.innerHTML = baru.innerHTML;
            results.style.opacity = '';
            history.replaceState(null, '', url);
        })
        .catch(function (err) {
            if (err.name === 'AbortError') return;
            results.style.opacity = '';
            form.submit(); // kalau gagal, pakai pencarian biasa
        });
    }

    // cari otomatis 300 ms setelah berhenti mengetik
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(cari, 300);
    });

    // tombol "Cari Stan" / Enter tidak memuat ulang halaman
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(timer);
        cari();
    });

    // tombol "Lihat Semua Stan" ikut tanpa refresh
    results.addEventListener('click', function (e) {
        var reset = e.target.closest('#stan-reset');
        if (!reset) return;
        e.preventDefault();
        input.value = '';
        cari();
    });
});
</script>
@endpush