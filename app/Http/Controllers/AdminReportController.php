<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MasterItem;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    protected DailyReportService $reportService;

    public function __construct(DailyReportService $service)
    {
        $this->reportService = $service;
    }

    // Halaman List & Filter
    public function index(Request $request)
    {
        // Ambil data laporan terfilter
        $reports = $this->reportService->getFilteredReports([
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Ambil daftar User (selain Super Admin) untuk dropdown filter
        $users = User::where('role', '!=', User::ROLE_SUPER_ADMIN)->get();

        return view('dashboard.admin.reports.index', compact('reports', 'users'));
    }

    // Tampilkan Detail (Support Modal)
    public function show($id)
    {
        $report = $this->reportService->getReportById($id);

        if (request()->ajax()) {
            // Jika AJAX, kembalikan hanya potongan HTML isinya saja
            return view('dashboard.admin.reports._detail_modal', compact('report'))->render();
        }

        return view('dashboard.admin.reports.show', compact('report'));
    }

    // Tampilkan Form Edit (Support Modal)
    public function edit($id)
    {
        $report = \App\Models\DailyReport::with('details')->findOrFail($id);
        $items = \App\Models\MasterItem::active()->get();

        if (request()->ajax()) {
            return view('dashboard.admin.reports._edit_modal', compact('report', 'items'))->render();
        }

        return view('dashboard.admin.reports.edit', compact('report', 'items'));
    }

    // [BARU] Proses Update
    public function update(Request $request, $id)
    {
        $report = DailyReport::findOrFail($id);

        $request->validate([
            'report_date' => 'required|date',
            'items' => 'required|array',
        ]);

        $this->reportService->updateReport($report, $request->all());

        return redirect()->route('admin.reports.index')->with('success', 'Laporan berhasil direvisi.');
    }

    // [BARU] Proses Hapus
    public function destroy($id)
    {
        $report = DailyReport::findOrFail($id);
        $this->reportService->deleteReport($report);

        return redirect()->route('admin.reports.index')->with('success', 'Laporan berhasil dihapus permanen.');
    }

    public function updateStatus(Request $request, $id)
{
    $report = DailyReport::findOrFail($id);
    $newStatus = $request->status; // 'approved' atau 'rejected'

    $this->reportService->changeStatus($report, $newStatus);

    // Kirim pesan beda tergantung status
    $msg = $newStatus == 'approved' ? 'Laporan disetujui (ACC).' : 'Laporan ditolak.';
    return back()->with('success', $msg);
}

}