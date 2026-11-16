@php $u = auth()->user(); @endphp
<details class="um">
    <summary class="um-btn" aria-label="Menu akun"><x-icon name="user" :size="18" /></summary>

    <div class="um-panel">
        <div class="um-head">
            <span class="um-ava"><x-icon name="user" :size="20" /></span>
            <div class="um-id">
                <strong>{{ $u->nama }}</strong>
                <small>{{ $u->email }}</small>
            </div>
        </div>

        <a href="{{ route('profil') }}" class="um-item">
            <x-icon name="user" :size="16" /> Profil Saya
        </a>

        @if (Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="um-item danger">
                    <x-icon name="log-out" :size="16" /> Keluar
                </button>
            </form>
        @endif
    </div>
</details>