<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class AuthController
 * 
 * Menangani autentikasi: login dan logout.
 * Dilengkapi validasi Regex, try-catch, dan notifikasi error.
 */
class AuthController extends Controller
{
    /** Tampilkan halaman login */
    public function showLogin()
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return redirect()->route('mahasiswa.index');
        }
        return view('auth.login');
    }

    /**
     * Proses login dengan validasi Regex
     */
    public function login(Request $request)
    {
        // Validasi input dengan regex
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required'    => '❌ Email wajib diisi.',
            'email.regex'       => '❌ Format email tidak valid.',
            'password.required' => '❌ Password wajib diisi.',
            'password.min'      => '❌ Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'))
                ->with('error', $validator->errors()->first());
        }

        try {
            $credentials = $request->only('email', 'password');
            $remember    = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();

                // Catat log login berhasil
                ActivityLog::record('login', 'User berhasil login: ' . Auth::user()->name);

                return redirect()->intended(route('mahasiswa.index'))
                    ->with('success', '✅ Selamat datang, <strong>' . Auth::user()->name . '</strong>!');
            }

            // Login gagal: tambah throttle counter
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ Email atau password salah. Silakan coba lagi.');

        } catch (\Throwable $e) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ Terjadi kesalahan sistem. Hubungi administrator.');
        }
    }

    /** Logout dan hapus session */
    public function logout(Request $request)
    {
        $name = Auth::user()?->name ?? 'Unknown';
        ActivityLog::record('login', "User logout: {$name}");

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', '✅ Anda telah berhasil logout.');
    }
}
