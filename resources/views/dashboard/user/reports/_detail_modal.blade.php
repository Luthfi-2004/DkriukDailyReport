{{-- HEADER STATUS --}}
<div class="alert {{ $dailyReport->status == 'approved' ? 'alert-success' : ($dailyReport->status == 'rejected' ? 'alert-danger' : 'alert-warning') }} d-flex justify-content-between align-items-center" role="alert">
    <div>
        <small class="d-block opacity-75">Tanggal Laporan</small>
        <h5 class="m-0 font-weight-bold">
            <i class="ri-calendar-event-line mr-1"></i> 
            {{ \Carbon\Carbon::parse($dailyReport->report_date)->translatedFormat('l, d F Y') }}
        </h5>
    </div>
    <div class="text-right">
        @if($dailyReport->status == 'pending')
            <span class="badge badge-light text-warning p-2 font-weight-bold">Menunggu ACC</span>
        @elseif($dailyReport->status == 'approved')
            <span class="badge badge-light text-success p-2 font-weight-bold">Disetujui</span>
        @else
            <span class="badge badge-light text-danger p-2 font-weight-bold">Ditolak</span>
        @endif
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <small class="text-muted d-block">Pelapor:</small>
        <span class="font-weight-bold font-size-14 text-dark">{{ $dailyReport->user->name }}</span>
    </div>
    <div class="col-md-6 text-right">
        <small class="text-muted d-block">Waktu Input:</small>
        <span class="font-size-13">{{ $dailyReport->created_at->format('d M Y, H:i') }} WIB</span>
    </div>
</div>

{{-- TABEL ITEM --}}
<div class="table-responsive border rounded">
    <table class="table table-sm table-striped mb-0">
        <thead class="bg-light">
            <tr>
                <th>Nama Barang</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Jumlah (Qty)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyReport->details as $detail)
            <tr>
                <td>{{ $detail->item->name }}</td>
                <td class="text-center small">
                    <span class="badge badge-soft-primary">{{ $detail->item->unit }}</span>
                </td>
                <td class="text-right font-weight-bold">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- CATATAN --}}
@if($dailyReport->notes)
<div class="alert alert-secondary mt-3 mb-0 py-2 font-size-13">
    <strong><i class="ri-sticky-note-line"></i> Catatan Tambahan:</strong><br>
    {{ $dailyReport->notes }}
</div>
@endif