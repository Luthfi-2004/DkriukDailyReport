<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user login? Kalau tidak, tendang ke login.
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Logika "Super Admin" (Dewa)
        // Jika user adalah Super Admin, dia boleh masuk ke MANA SAJA (Bypass).
        // Ini opsional, tapi praktik bagus biar Super Admin gak ribet.
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // 3. Cek Role yang diminta
        // Kita bisa kirim parameter role lebih dari satu, misal: role:admin,user
        foreach ($roles as $role) {
            // Kita pakai method hasRole() dari Model User yang tadi kita buat!
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // 4. Jika sampai sini berarti GAK PUNYA AKSES -> Tendang 403 (Forbidden)
        abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
    }
}