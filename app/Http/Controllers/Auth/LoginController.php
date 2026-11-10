<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses login pengguna
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Membatasi percobaan login
        $throttleKey = Str::lower($request->input('email'))
            . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()
                ->withErrors([
                    'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam '
                        . RateLimiter::availableIn($throttleKey)
                        . ' detik.',
                ])
                ->onlyInput('email');
        }

        // Proses autentikasi
        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors([
                    'email' => 'Email atau kata sandi yang kamu masukkan salah.',
                ])
                ->onlyInput('email');
        }

        // Login berhasil, reset percobaan
        RateLimiter::clear($throttleKey);

        // Regenerasi session untuk keamanan
        $request->session()->regenerate();

        // Ambil data pengguna
        $user = Auth::user();

        // Redirect berdasarkan role
        if ($user->role === 'pembeli') {
            return redirect()->route('pembeli.dashboard');
        }

        if ($user->role === 'penjual') {
            return redirect()->route('penjual.dashboard');
        }

        // Jika role tidak dikenali
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Role akun tidak dikenali. Silakan hubungi admin.',
            ]);
    }

    /**
     * Proses logout pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Kamu berhasil keluar dari akun.');
    }
}
