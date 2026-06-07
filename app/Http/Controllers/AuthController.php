<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * POST /login — Proses login dengan validasi Regex.
     */
    public function login(Request $request)
    {
        try {
            $email    = $request->input('email', '');
            $password = $request->input('password', '');

            // Validasi format email dengan Regex
            if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
                return back()
                    ->withErrors(['email' => 'Format email tidak valid.'])
                    ->withInput(['email' => $email]);
            }

            if (empty($password)) {
                return back()
                    ->withErrors(['password' => 'Password wajib diisi.'])
                    ->withInput(['email' => $email]);
            }

            $credentials = $request->only('email', 'password');
            $remember    = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
            }

            return back()
                ->withErrors(['email' => 'Email atau password salah. Periksa kembali kredensial Anda.'])
                ->withInput(['email' => $email]);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Login gagal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'Anda berhasil logout.');
    }
}
