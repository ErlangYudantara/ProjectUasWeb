<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun pembeli
     */
    public function register(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:100',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],

            'terms' => [
                'accepted',
            ],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',

            'terms.accepted' => 'Kamu harus menyetujui ketentuan layanan.',
        ]);

        // Membuat akun baru
        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'pembeli',
        ]);

        // Redirect ke halaman login
        return redirect()
            ->route('login')
            ->with('success', 'Akun berhasil dibuat! Silakan masuk ke akun kamu.');
    }
}
