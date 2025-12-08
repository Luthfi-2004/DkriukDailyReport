<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses data dari Form Login
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Login (Laravel otomatis hash password input dan cocokin dengan DB)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Security: Cegah Session Fixation

            // 3. Redirect user ke dashboard yang sesuai rolenya
            // Kita pakai method redirectBasedOnRole yang sudah ada di DashboardController
            return redirect()->route('auth.check_role');
        }

        // 4. Kalau Gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}