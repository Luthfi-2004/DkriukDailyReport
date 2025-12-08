@extends('layouts.app')

@section('title', 'Rekap Laporan Operasional')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Flash Message --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line mr-1 align-middle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Filter Data Laporan</h4>

                {{-- FORM FILTER --}}
                <form action="{{ route('admin.reports.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="user_id" class="form-control select2" style="width: 100%;">
                                    <option value="">-- Semua Staff --</option>
                                    @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Dari Tanggal">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="Sampai Tanggal">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="ri-filter-2-line mr-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover dt-responsive nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Staff</th>
                                <th>Jml Item</th>
                                <th>Total Estimasi</th>
                                <th>Status</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</strong></td>
                                <td>{{ $report->user->name }}</td>
                                <td>{{ $report->details->count() }} Item</td>
                                <td>
                                    @php 
                                        $total = $report->details->sum(function ($d) {
                                            return $d->quantity * $d->item->price; 
                                        }); 
                                    @endphp
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                
                                {{-- KOLOM STATUS --}}
                                <td>
                                    @if($report->status == 'pending')
                                        <div class="d-flex">
                                            {{-- Tombol ACC (Kasih margin kanan mr-2 biar gak nempel sama tolak) --}}
                                            <form action="{{ route('admin.reports.status', $report->id) }}" method="POST" class="mr-2">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-success" title="ACC Laporan" onclick="return confirm('Yakin ACC laporan ini?')">
                                                    <i class="ri-check-line"></i> ACC
                                                </button>
                                            </form>
                                
                                            {{-- Tombol TOLAK --}}
                                            <form action="{{ route('admin.reports.status', $report->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Tolak Laporan" onclick="return confirm('Tolak laporan ini?')">
                                                    <i class="ri-close-line"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($report->status == 'approved')
                                        <span class="badge badge-soft-success font-size-12">
                                            <i class="ri-checkbox-circle-line align-middle mr-1"></i> Disetujui
                                        </span>
                                    @elseif($report->status == 'rejected')
                                        <span class="badge badge-soft-danger font-size-12">
                                            <i class="ri-close-circle-line align-middle mr-1"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="badge badge-soft-warning font-size-12">Pending</span>
                                    @endif
                                </td>

                                {{-- KOLOM AKSI (EDIT/DELETE) --}}
                                <td>
                                    <div class="d-flex">
                                        {{-- Detail (Kasih mr-2) --}}
                                        <button type="button" class="btn btn-sm btn-info btn-detail mr-2"
                                            data-url="{{ route('admin.reports.show', $report->id) }}" title="Lihat Detail">
                                            <i class="ri-eye-line"></i>
                                        </button>

                                        {{-- Edit (Kasih mr-2) --}}
                                        <button type="button" class="btn btn-sm btn-warning btn-edit mr-2"
                                            data-url="{{ route('admin.reports.edit', $report->id) }}" title="Revisi Data">
                                            <i class="ri-pencil-line"></i>
                                        </button>

                                        {{-- Hapus --}}
                                        <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin hapus permanen?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Data tidak ditemukan.</td>
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

{{-- MODAL CONTAINER DETAIL --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rincian Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CONTAINER EDIT --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">Revisi Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <div class="text-center py-5"><div class="spinner-border text-warning" role="status"></div></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // 1. INISIALISASI SELECT2
        $('.select2').select2({
            placeholder: "-- Pilih Staff --",
            allowClear: true // Saya ubah true biar bisa di-reset filternya
        });

        // 2. HANDLER DETAIL
        $('.btn-detail').click(function () {
            let url = $(this).data('url');
            $('#detailModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            $('#detailModal').modal('show');
            $.get(url, function (data) { $('#detailModalBody').html(data); })
                .fail(function () { $('#detailModalBody').html('<div class="alert alert-danger">Gagal mengambil data.</div>'); });
        });

        // 3. HANDLER EDIT
        $('.btn-edit').click(function () {
            let url = $(this).data('url');
            $('#editModalBody').html('<div class="text-center py-5"><div class="spinner-border text-warning"></div></div>');
            $('#editModal').modal('show');
            $.get(url, function (data) { $('#editModalBody').html(data); })
                .fail(function () { $('#editModalBody').html('<div class="alert alert-danger">Gagal memuat form edit.</div>'); });
        });
    });
</script>
@endpush
@endsection