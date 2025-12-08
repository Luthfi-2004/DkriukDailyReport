<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\DailyReportDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DailyReportService
{
    /**
     * Menyimpan laporan harian beserta detail itemnya.
     */
    public function storeReport(array $data): DailyReport
    {
        // Gunakan DB Transaction agar data atomik (semua masuk atau tidak sama sekali)
        return DB::transaction(function () use ($data) {
            
            // 1. Buat Header Laporan
            $report = DailyReport::create([
                'user_id'     => Auth::id(),
                'report_date' => $data['report_date'],
                'notes'       => $data['notes'] ?? null,
                'status' => DailyReport::STATUS_PENDING, // Langsung submit
            ]);

            // 2. Loop inputan items dan simpan ke detail
            // Struktur $data['items'] adalah: [ID_ITEM => JUMLAH, ID_ITEM_LAIN => JUMLAH]
            foreach ($data['items'] as $itemId => $quantity) {
                // Hanya simpan jika quantity diisi (lebih dari 0)
                // Kalau user kosongin atau isi 0, kita anggap tidak ada pemakaian.
                // (Tapi kalau mau simpan 0 juga boleh, tergantung kebijakan).
                // Di sini kita simpan semua biar datanya lengkap.
                
                // Pastikan quantity bersih (kalau ada format rupiah tadi)
                $qtyClean = str_replace(['.', ','], '', $quantity);

                DailyReportDetail::create([
                    'daily_report_id' => $report->id,
                    'master_item_id'  => $itemId,
                    'quantity'        => (float) $qtyClean,
                ]);
            }

            return $report;
        });
    }

    /**
     * Update Laporan (Hapus detail lama, ganti baru).
     */
    public function updateReport(DailyReport $report, array $data): void
    {
        DB::transaction(function () use ($report, $data) {
            // 1. Update Header
            $report->update([
                'report_date' => $data['report_date'],
                'notes'       => $data['notes'] ?? null,
            ]);

            // 2. Hapus Detail Lama (Reset)
            $report->details()->delete();

            // 3. Masukkan Detail Baru (Looping sama kayak create)
            foreach ($data['items'] as $itemId => $quantity) {
                // Bersihkan format rupiah
                $qtyClean = str_replace(['.', ','], '', $quantity);

                // Hanya simpan jika lebih dari 0 (opsional, sesuaikan kebutuhan)
                // Di sini kita simpan 0 pun gak apa-apa buat tracking
                DailyReportDetail::create([
                    'daily_report_id' => $report->id,
                    'master_item_id'  => $itemId,
                    'quantity'        => (float) $qtyClean,
                ]);
            }
        });
    }

    /**
     * Hapus Laporan Permanen.
     */
    public function deleteReport(DailyReport $report): void
    {
        // Karena di migration kita set onDelete('cascade'), 
        // detailnya otomatis kehapus. Aman.
        $report->delete();
    }
    /**
     * Cek apakah user sudah lapor hari ini/tanggal tersebut?
     */
    public function hasReported(string $date, int $userId): bool
    {
        return DailyReport::where('user_id', $userId)
                          ->whereDate('report_date', $date)
                          ->exists();
    }
    /**
     * Ambil semua laporan dengan filter (Untuk Admin).
     */
    public function getFilteredReports(array $filters)
    {
        $query = DailyReport::with(['user', 'details']) // Eager load biar cepat
                    ->latest('report_date');

        // 1. Filter User (Jika Admin memilih user tertentu)
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // 2. Filter Tanggal Mulai
        if (!empty($filters['start_date'])) {
            $query->whereDate('report_date', '>=', $filters['start_date']);
        }

        // 3. Filter Tanggal Akhir
        if (!empty($filters['end_date'])) {
            $query->whereDate('report_date', '<=', $filters['end_date']);
        }

        return $query->paginate(15)->withQueryString(); // Pakai pagination + keep filter di URL
    }

    /**
     * Ambil detail laporan khusus Admin (Tanpa cek ID user).
     */
    public function getReportById(int $id): DailyReport
    {
        return DailyReport::with(['user', 'details.item'])->findOrFail($id);
    }
    public function changeStatus(DailyReport $report, string $status): void
{
    // Validasi input status biar gak aneh-aneh
    if (!in_array($status, ['approved', 'rejected', 'pending'])) {
        throw new \Exception("Status tidak valid.");
    }

    $report->update(['status' => $status]);
}
}