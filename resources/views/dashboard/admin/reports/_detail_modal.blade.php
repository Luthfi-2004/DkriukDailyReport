<div class="alert alert-primary d-flex justify-content-between align-items-center" role="alert">
    <div>
        <small class="d-block text-primary-50">Tanggal Laporan Operasional</small>
        {{-- INI YANG PENTING: Tanggal Laporan (report_date) --}}
        <h5 class="m-0 font-weight-bold text-primary">
            <i class="ri-calendar-event-line mr-1"></i> 
            {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y') }}
        </h5>
    </div>
    <div class="text-right">
        <span class="badge badge-light text-primary p-2">Status: Submitted</span>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <small class="text-muted d-block">Staff Pelapor:</small>
        <span class="font-weight-bold font-size-14 text-dark">{{ $report->user->name }}</span>
        <div class="text-muted font-size-12">{{ $report->user->email }}</div>
    </div>
    <div class="col-md-6 text-right">
        {{-- INI SEKUNDER: Waktu Input ke System (created_at) --}}
        <small class="text-muted d-block">Waktu Input System:</small>
        <span class="font-size-13">{{ $report->created_at->format('d M Y, H:i') }} WIB</span>
    </div>
</div>

<div class="table-responsive border rounded">
    <table class="table table-sm table-striped mb-0">
        <thead class="bg-light">
            <tr>
                <th>Barang</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($report->details as $detail)
            @php 
                $subtotal = $detail->quantity * $detail->item->price; 
                $grandTotal += $subtotal;
            @endphp
            <tr>
                <td>{{ $detail->item->name }}</td>
                <td class="text-center small">{{ $detail->item->unit }}</td>
                <td class="text-right font-weight-bold">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                <td class="text-right text-muted">Rp {{ number_format($detail->item->price, 0, ',', '.') }}</td>
                <td class="text-right font-weight-bold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-light">
            <tr>
                <td colspan="4" class="text-right font-weight-bold">TOTAL ESTIMASI</td>
                <td class="text-right font-weight-bold text-primary">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

@if($report->notes)
<div class="alert alert-warning mt-3 mb-0 py-2 font-size-13">
    <strong><i class="ri-sticky-note-line"></i> Catatan Staff:</strong> {{ $report->notes }}
</div>
@endif