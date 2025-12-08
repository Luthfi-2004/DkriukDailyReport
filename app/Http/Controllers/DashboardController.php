<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\StatsService; // 1. Import Service

class DashboardController extends Controller
{
    protected StatsService $statsService; // 2. Siapkan wadah properti

    // 3. Dependency Injection via Constructor
    // Laravel otomatis membuatkan object StatsService dan memasukkannya ke sini
    public function __construct(StatsService $service)
    {
        $this->statsService = $service;
    }

    public function superIndex(): View
    {
        $viewData = $this->statsService->getSuperAdminStats();
        $viewData['user'] = Auth::user();
        return view('dashboard.super_admin', $viewData);
    }

    public function adminIndex(): View
    {
        // Panggil Service
        $stats = $this->statsService->getAdminStats();

        // Kirim data user + data stats
        return view('dashboard.admin', [
            'user' => Auth::user(),
            'stats' => $stats // Kirim array lengkap
        ]);
    }

    public function index(): View
    {
        $user = Auth::user();

        // 1. Cek apakah SUDAH ADA laporan hari ini (Oleh SIAPAPUN)
        // Jadi kalau si Budi udah lapor, si Siti tau "Oh, hari ini udah ada yang lapor".
        $hasReportedToday = \App\Models\DailyReport::whereDate('report_date', now())
            ->exists();

        // 2. Hitung total laporan bulan ini (GLOBAL SATU KANTOR)
        // HAPUS ->where('user_id', $user->id)
        $reportsThisMonth = \App\Models\DailyReport::whereMonth('report_date', now()->month)
            ->count();

        // 3. Ambil 5 laporan terakhir (DARI SIAPAPUN)
        // HAPUS ->where('user_id', $user->id)
        $recentReports = \App\Models\DailyReport::with('user') // Load nama pelapornya
            ->latest('report_date')
            ->take(5)
            ->get();

        return view('dashboard.user', compact('user', 'hasReportedToday', 'reportsThisMonth', 'recentReports'));
    }

    /**
     * OPSIONAL: Redirection Logic (Polisi Lalu Lintas)
     * Jika user mengakses url '/dashboard' (umum), lempar mereka ke tempat yang benar.
     * Gunakan method OOP dari Model User yang tadi kita buat!
     */
    public function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super.dashboard');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }
}