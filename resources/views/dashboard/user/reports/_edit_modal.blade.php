<form action="{{ route('reports.update', $dailyReport->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="alert alert-warning font-size-12">
        <i class="ri-alert-line mr-1"></i> Anda sedang mengedit laporan status <strong>PENDING</strong>.
    </div>

    {{-- Tanggal --}}
    <div class="form-group row">
        <label class="col-md-4 col-form-label font-weight-bold">Tanggal Laporan</label>
        <div class="col-md-8">
            <input class="form-control" type="date" name="report_date" value="{{ $dailyReport->report_date->format('Y-m-d') }}" required>
        </div>
    </div>

    <hr>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="text-muted font-size-12 text-uppercase font-weight-bold">Detail Item</div>
    </div>

    <div style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
        @foreach($items as $item)
            @php
                $detail = $dailyReport->details->firstWhere('master_item_id', $item->id);
                $rawQty = $detail ? $detail->quantity : 0;
                $displayQty = $rawQty > 0 ? ($rawQty + 0) : '';
            @endphp

            <div class="form-group row mb-2 align-items-center">
                <label class="col-7 col-form-label font-size-13 text-truncate">
                    <span class="font-weight-bold text-dark">{{ $item->name }}</span> 
                    <small class="text-muted">({{ $item->unit }})</small>
                </label>
                <div class="col-5">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" min="0" 
                               name="items[{{ $item->id }}]" 
                               class="form-control text-right {{ $displayQty !== '' ? 'bg-white border-primary font-weight-bold' : 'bg-light' }}" 
                               value="{{ $displayQty }}"
                               placeholder="0"
                               onfocus="this.select()">
                        <div class="input-group-append">
                            <span class="input-group-text font-size-11">{{ $item->unit }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-group mt-3">
        <label class="font-size-13 font-weight-bold">Catatan</label>
        <textarea name="notes" class="form-control form-control-sm" rows="2">{{ $dailyReport->notes }}</textarea>
    </div>

    <div class="text-right mt-3">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
            <i class="ri-save-line mr-1"></i> Simpan Perubahan
        </button>
    </div>
</form>