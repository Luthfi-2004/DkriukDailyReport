<form action="{{ route('admin.reports.update', $report->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Tanggal --}}
    <div class="form-group row">
        <label class="col-md-4 col-form-label font-weight-bold">Tanggal Laporan</label>
        <div class="col-md-8">
            <input class="form-control" type="date" name="report_date" value="{{ $report->report_date->format('Y-m-d') }}" required>
        </div>
    </div>

    <hr>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="text-muted font-size-12 text-uppercase font-weight-bold">Detail Item</div>
        <div class="font-size-11 text-muted font-italic">*Kosongkan jika 0</div>
    </div>

    <div style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
        @foreach($items as $item)
            @php
                // 1. Cari apakah item ini ada di laporan yg sedang diedit?
                $detail = $report->details->firstWhere('master_item_id', $item->id);
                
                // 2. Ambil angkanya. Jika tidak ada, anggap 0.
                $rawQty = $detail ? $detail->quantity : 0;

                // 3. LOGIKA TAMPILAN:
                // - ($rawQty + 0) trik biar 5.00 jadi 5, tapi 5.5 tetap 5.5
                // - Jika > 0, tampilkan angkanya. Jika 0, kosongkan stringnya ('').
                $displayQty = $rawQty > 0 ? ($rawQty + 0) : '';
            @endphp

            <div class="form-group row mb-2 align-items-center">
                <label class="col-7 col-form-label font-size-13 text-truncate" title="{{ $item->name }}">
                    <span class="font-weight-bold text-dark">{{ $item->name }}</span> 
                    <small class="text-muted">({{ $item->unit }})</small>
                </label>
                <div class="col-5">
                    <div class="input-group input-group-sm">
                        {{-- Placeholder dinamis: "Input Ayam..." --}}
                        <input type="number" step="0.01" min="0" 
                               name="items[{{ $item->id }}]" 
                               class="form-control text-right {{ $displayQty !== '' ? 'bg-white font-weight-bold border-primary' : 'bg-light' }}" 
                               value="{{ $displayQty }}"
                               placeholder="Input {{ $item->name }}..."
                               onfocus="this.select()"> {{-- UX: Pas diklik langsung keblok semua --}}
                               
                        <div class="input-group-append">
                            <span class="input-group-text font-size-11">{{ $item->unit }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-group mt-3 border-top pt-3">
        <label class="font-size-13 font-weight-bold">Catatan Revisi</label>
        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Tulis alasan revisi...">{{ $report->notes }}</textarea>
    </div>

    <div class="text-right mt-4">
        <button type="button" class="btn btn-secondary btn-sm mr-1" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
            <i class="ri-save-line mr-1"></i> Simpan Revisi
        </button>
    </div>
</form>