@extends('layouts.app')

@section('title', 'Laporan Operasional')

@section('content')
    <div class="row">
        {{-- ============================================================== --}}
        {{-- BAGIAN 1: FORM INPUT HARIAN --}}
        {{-- ============================================================== --}}
        <div class="col-12 mb-4">
            @if($todayReport)
                {{-- TAMPILAN JIKA SUDAH LAPOR HARI INI --}}
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="ri-checkbox-circle-line display-5 mr-3"></i>
                    <div>
                        <h5 class="alert-heading font-size-16 font-weight-bold">Laporan Hari Ini Sudah Masuk!</h5>
                        <p class="mb-0">Terima kasih telah melakukan input data. Anda bisa melihat status laporan Anda di tabel
                            bawah.</p>
                    </div>
                </div>
            @else
                {{-- TAMPILAN FORMULIR (JIKA BELUM LAPOR) --}}
                <div class="card border-primary" style="border-top: 4px solid #556ee6;">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="my-0 text-primary"><i class="ri-edit-2-line mr-2"></i> Input Laporan Harian</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reports.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                {{-- Kolom Kiri: Tanggal & Info --}}
                                <div class="col-md-4 border-right">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tanggal Laporan</label>
                                        <input class="form-control" type="date" name="report_date" value="{{ date('Y-m-d') }}"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>Catatan Tambahan (Opsional)</label>
                                        <textarea name="notes" class="form-control" rows="4"
                                            placeholder="Contoh: Stok tepung menipis..."></textarea>
                                    </div>
                                </div>

                                {{-- Kolom Kanan: Daftar Barang --}}
                                <div class="col-md-8">
                                    <label class="font-weight-bold mb-3">Rincian Barang Keluar / Stok</label>

                                    <div class="row">
                                        @forelse($items as $item)
                                            <div class="col-md-6 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light font-weight-bold"
                                                            style="min-width: 100px;">
                                                            {{ $item->name }}
                                                        </span>
                                                    </div>
                                                    <input type="number" step="0.01" min="0" name="items[{{ $item->id }}]"
                                                        class="form-control" placeholder="0" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{ $item->unit }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center text-muted py-3">
                                                Belum ada Master Item yang aktif. Hubungi Super Admin.
                                            </div>
                                        @endforelse
                                    </div>

                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light px-4"
                                            onclick="return confirm('Pastikan data sudah benar. Kirim Laporan?');">
                                            <i class="ri-send-plane-fill align-middle mr-1"></i> KIRIM LAPORAN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================================== --}}
        {{-- BAGIAN 2: TABEL RIWAYAT --}}
        {{-- ============================================================== --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Riwayat Laporan Masuk</h4>

                    {{-- Flash Message --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-check-line mr-1 align-middle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-line mr-1 align-middle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pelapor</th>
                                    <th>Jumlah Item</th>
                                    <th>Status</th>
                                    <th>Waktu Submit</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td><strong>{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}</strong>
                                        </td>
                                        <td><span class="font-weight-bold">{{ $report->user->name }}</span></td>
                                        <td>{{ $report->details->count() }} Jenis</td>
                                        <td>
                                            @if($report->status == 'pending')
                                                <span class="badge badge-soft-warning font-size-12">Menunggu ACC</span>
                                            @elseif($report->status == 'approved')
                                                <span class="badge badge-soft-success font-size-12">Disetujui</span>
                                            @else
                                                <span class="badge badge-soft-danger font-size-12">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>{{ $report->created_at->format('H:i') }} WIB</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- TOMBOL DETAIL (Selalu Muncul) --}}
                                                <button type="button" class="btn btn-sm btn-info btn-detail mr-1"
                                                    data-url="{{ route('reports.show', $report->id) }}" title="Lihat Detail">
                                                    <i class="ri-eye-line"></i>
                                                </button>

                                                {{-- AREA EDIT & DELETE (Hanya jika PENDING & MILIK SENDIRI) --}}
                                                @if($report->status == 'pending' && $report->user_id == Auth::id())

                                                    {{-- Tombol Edit --}}
                                                    <button type="button" class="btn btn-sm btn-warning btn-user-edit mr-1"
                                                        data-url="{{ route('reports.edit', $report->id) }}" title="Edit Laporan">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>

                                                    {{-- Tombol Hapus --}}
                                                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Yakin ingin membatalkan & menghapus laporan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Laporan">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>

                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data laporan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $reports->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CONTAINER DETAIL (SAMA KAYAK SEBELUMNYA) --}}
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rincian Laporan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="userEditModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">Edit Laporan Saya</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="userEditModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.btn-detail').click(function () {
                    let url = $(this).data('url');
                    $('#detailModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
                    $('#detailModal').modal('show');
                    $.get(url, function (data) { $('#detailModalBody').html(data); })
                        .fail(function () { $('#detailModalBody').html('<div class="alert alert-danger">Gagal mengambil data.</div>'); });
                });
            });
            $('.btn-user-edit').click(function () {
                let url = $(this).data('url');
                $('#userEditModalBody').html('<div class="text-center py-5"><div class="spinner-border text-warning"></div></div>');
                $('#userEditModal').modal('show');

                $.get(url, function (data) {
                    $('#userEditModalBody').html(data);
                }).fail(function (xhr) {
                    // Jika error (misal status sudah berubah jadi ACC saat menu dibuka)
                    let msg = xhr.responseJSON ? xhr.responseJSON.error : 'Gagal memuat data.';
                    $('#userEditModalBody').html('<div class="alert alert-danger">' + msg + '</div>');
                });
            });
        </script>
    @endpush
@endsection