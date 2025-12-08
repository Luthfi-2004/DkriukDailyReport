<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    protected DailyReportService $reportService;

    public function __construct(DailyReportService $service)
    {
        $this->reportService = $service;
    }

    // // Tampilkan Form Input
    // public function create()
    // {
    //     // Ambil barang-barang yang AKTIF saja
    //     $items = MasterItem::active()->get();

    //     // Cek apakah user sudah lapor hari ini? (Opsional, buat UX biar gak lapor 2x)
    //     $todayReport = DailyReport::where('user_id', Auth::id())
    //         ->whereDate('report_date', now())
    //         ->first();

    //     return view('dashboard.user.reports.create', compact('items', 'todayReport'));
    // }

    // Proses Simpan
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'report_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array', // Harus berupa array
            'items.*' => 'nullable|numeric|min:0', // Tiap item harus angka
        ]);

        // 2. Cek Duplikat Laporan (Security Layer)
        if ($this->reportService->hasReported($request->report_date, Auth::id())) {
            return back()->with('error', 'Anda sudah membuat laporan untuk tanggal tersebut. Silakan cek riwayat.');
        }

        // 3. Panggil Service
        try {
            $this->reportService->storeReport($request->all());

            return redirect()->route('reports.index')
                ->with('success', 'Laporan harian berhasil disimpan. Terima kasih!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    public function index()
    {
        // 1. DATA UNTUK TABEL RIWAYAT (GLOBAL)
        $reports = DailyReport::with(['details.item', 'user'])
                    ->latest('report_date')
                    ->paginate(10);

        // 2. DATA UNTUK FORM INPUT (MASTER ITEM)
        $items = MasterItem::active()->get();

        // 3. CEK STATUS HARI INI (Untuk UX Form)
        // Cek apakah USER YANG LOGIN sudah lapor hari ini?
        $todayReport = DailyReport::where('user_id', Auth::id())
                        ->whereDate('report_date', now())
                        ->first();

        return view('dashboard.user.reports.index', compact('reports', 'items', 'todayReport'));
    }

    public function show(DailyReport $dailyReport)
    {
        // Load relasi user dan item
        $dailyReport->load(['details.item', 'user']);

        // [BARU] Cek apakah request dari AJAX (Modal)?
        if (request()->ajax()) {
            return view('dashboard.user.reports._detail_modal', compact('dailyReport'))->render();
        }

        // Fallback jika dibuka lewat URL biasa
        return view('dashboard.user.reports.show', compact('dailyReport'));
    }
    // Tampilkan Modal Edit (Khusus User)
    public function edit(DailyReport $dailyReport)
    {
        // 1. Cek Kepemilikan
        if ($dailyReport->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        // 2. Cek Status (Hanya Pending yang boleh diedit)
        if ($dailyReport->status !== 'pending') {
            return response()->json(['error' => 'Laporan yang sudah diproses (ACC/Tolak) tidak dapat diedit.'], 403);
        }

        // 3. Ambil Master Item untuk Form
        $items = MasterItem::active()->get();

        // 4. Return Partial View untuk Modal
        return view('dashboard.user.reports._edit_modal', compact('dailyReport', 'items'))->render();
    }

    // Proses Update Laporan User
    public function update(Request $request, DailyReport $dailyReport)
    {
        // Validasi Keamanan Berlapis
        if ($dailyReport->user_id !== Auth::id()) abort(403);
        if ($dailyReport->status !== 'pending') return back()->with('error', 'Gagal update: Status laporan sudah final.');

        $request->validate([
            'report_date' => 'required|date',
            'items'       => 'required|array',
        ]);

        // Gunakan Service yang sudah kita buat sebelumnya (Re-use logic)
        // Kita panggil service updateReport yang sama dengan Admin
        $this->reportService->updateReport($dailyReport, $request->all());

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil diperbarui.');
    }
    // Hapus Laporan (Khusus User)
    public function destroy(DailyReport $dailyReport)
    {
        // 1. Cek Kepemilikan (Wajib Punya Sendiri)
        if ($dailyReport->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus data ini.');
        }

        // 2. Cek Status (Hanya Pending yang boleh dihapus)
        if ($dailyReport->status !== 'pending') {
            return back()->with('error', 'Gagal menghapus: Laporan sudah diproses (ACC/Tolak) oleh Admin.');
        }

        // 3. Eksekusi Hapus (Pakai Service yang sama dengan Admin)
        $this->reportService->deleteReport($dailyReport);

        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}