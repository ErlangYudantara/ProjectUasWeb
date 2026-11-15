@php
    $cur  = $menus->currentPage();
    $last = $menus->lastPage();
    $set  = [1, $last, $cur - 1, $cur, $cur + 1];
    if ($cur <= 2) { $set[] = 2; $set[] = 3; }
    $pages = collect($set)->filter(fn ($p) => $p >= 1 && $p <= $last)->unique()->sort()->values();
@endphp

<nav class="m-pager" aria-label="Halaman menu">
    @if ($menus->onFirstPage())
        <span class="m-pg off"><x-icon name="chevron-left" :size="14" /> Sebelumnya</span>
    @else
        <a class="m-pg" href="{{ $menus->previousPageUrl() }}"><x-icon name="chevron-left" :size="14" /> Sebelumnya</a>
    @endif

    @foreach ($pages as $i => $p)
        @if ($i > 0 && $p - $pages[$i - 1] > 1)
            <span class="m-dots">&hellip;</span>
        @endif
        @if ($p == $cur)
            <span class="m-pg num on" aria-current="page">{{ $p }}</span>
        @else
            <a class="m-pg num" href="{{ $menus->url($p) }}">{{ $p }}</a>
        @endif
    @endforeach

    @if ($menus->hasMorePages())
        <a class="m-pg" href="{{ $menus->nextPageUrl() }}">Selanjutnya <x-icon name="chevron-right" :size="14" /></a>
    @else
        <span class="m-pg off">Selanjutnya <x-icon name="chevron-right" :size="14" /></span>
    @endif
</nav>