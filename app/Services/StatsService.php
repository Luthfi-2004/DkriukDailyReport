<?php

namespace App\Services;

use App\Models\User;
// use App\Models\Transaction; // Nanti kalau sudah ada tabel transaksi

class StatsService
{
    /**
     * Mengambil data Super Admin + Data Operasional Admin.
     * Jadi Super Admin bisa lihat SEMUANYA.
     */
    public function getSuperAdminStats(): array
    {
        // 1. Ambil Statistik Sistem (Khusus Super Admin)
        $systemStats = [
            'total_users'         => User::count(),
            'total_admin'         => User::where('role', User::ROLE_ADMIN)->count(),
            'total_staff'         => User::where('role', User::ROLE_USER)->count(),
            'server_status'       => 'Online', 
        ];

        // 2. Ambil Statistik Operasional (Punya Admin)
        // Kita REUSE logic yang sudah capek-capek kita buat di bawah
        $operationalStats = $this->getAdminStats();

        // 3. Gabungkan keduanya
        // Hasilnya: array berisi total_users, charts, reports, cost, dll.
        return array_merge($systemStats, $operationalStats);
    }
    /**
     * Hitung statistik untuk Admin Operasional.
     * Fokus: Laporan Harian & Pengeluaran.
     */
    public function getAdminStats(): array
    {
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('m');
        $thisYear = now()->format('Y');

        // 1. Total Laporan Masuk Hari Ini
        $reportsToday = \App\Models\DailyReport::whereDate('report_date', $today)
                        ->where('status', 'submitted')
                        ->count();

        // 2. Hitung Estimasi Biaya Bulan Ini (Quantity * Price)
        // Kita butuh join karena harga ada di master_items, quantity ada di details
        $costThisMonth = \App\Models\DailyReportDetail::query()
            ->join('daily_reports', 'daily_report_details.daily_report_id', '=', 'daily_reports.id')
            ->join('master_items', 'daily_report_details.master_item_id', '=', 'master_items.id')
            ->whereMonth('daily_reports.report_date', $thisMonth)
            ->whereYear('daily_reports.report_date', $thisYear)
            ->sum(\Illuminate\Support\Facades\DB::raw('daily_report_details.quantity * master_items.price'));

        // 3. Ambil 5 Laporan Terbaru (Buat tabel mini)
        $recentReports = \App\Models\DailyReport::with('user')
            ->latest('report_date')
            ->take(5)
            ->get();

        // 4. Data Grafik (7 Hari Terakhir) - Opsional tapi Keren
        // Kita hitung total pengeluaran per hari selama 7 hari ke belakang
        $chartData = $this->getWeeklyExpenseChart();

        return [
            'reports_today'   => $reportsToday,
            'cost_this_month' => $costThisMonth,
            'recent_reports'  => $recentReports,
            'chart_dates'     => $chartData['dates'],
            'chart_values'    => $chartData['values'],
        ];
    }

    /**
     * Helper Private untuk data grafik
     */
    private function getWeeklyExpenseChart(): array
    {
        $dates = [];
        $values = [];

        // Loop 7 hari ke belakang
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates[] = $date->format('d M'); // Label: "08 Dec"

            // Hitung cost per tanggal tersebut
            $val = \App\Models\DailyReportDetail::query()
                ->join('daily_reports', 'daily_report_details.daily_report_id', '=', 'daily_reports.id')
                ->join('master_items', 'daily_report_details.master_item_id', '=', 'master_items.id')
                ->whereDate('daily_reports.report_date', $date->format('Y-m-d'))
                ->sum(\Illuminate\Support\Facades\DB::raw('daily_report_details.quantity * master_items.price'));
            
            $values[] = $val;
        }

        return ['dates' => $dates, 'values' => $values];
    }
}