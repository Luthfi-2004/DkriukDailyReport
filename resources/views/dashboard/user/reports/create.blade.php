@extends('layouts.app')

@section('title', 'Input Laporan Harian')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        
        {{-- Pesan Error/Sukses --}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Jika sudah lapor hari ini, kasih peringatan --}}
        @if($todayReport)
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="ri-alert-line mr-2 display-6"></i>
                <div>
                    <strong>Perhatian!</strong> Anda sudah mengirim laporan untuk hari ini. 
                    <br>Data inputan baru akan ditolak jika tanggalnya sama (Hari Ini).
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Formulir Laporan Operasional</h4>
                <p class="text-muted mb-4">Silakan isi jumlah pemakaian/stok sesuai kondisi lapangan. Kosongkan atau isi 0 jika tidak ada penggunaan.</p>

                <form action="{{ route('reports.store') }}" method="POST">
                    @csrf

                    {{-- Tanggal Laporan --}}
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">Tanggal Laporan</label>
                        <div class="col-md-9">
                            <input class="form-control" type="date" name="report_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="font-size-14 mb-3 text-uppercase"><i class="ri-list-check-2 mr-1"></i> Detail Item</h5>

                    {{-- LOOPING ITEM DINAMIS --}}
                    @forelse($items as $item)
                        <div class="form-group row d-flex align-items-center">
                            <label class="col-md-6 col-form-label font-weight-bold text-dark">
                                {{ $item->name }}
                                <span class="badge badge-soft-primary ml-1">{{ $item->unit }}</span>
                            </label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    {{-- Name Array: items[1], items[5], dst --}}
                                    <input type="number" step="0.01" min="0" 
                                           name="items[{{ $item->id }}]" 
                                           class="form-control bg-light border-light" 
                                           placeholder="0" required>
                                    
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ $item->unit }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center">
                            Belum ada Master Item yang aktif. Hubungi Super Admin.
                        </div>
                    @endforelse

                    <hr class="my-4">

                    {{-- Catatan Tambahan --}}
                    <div class="form-group">
                        <label>Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Stok tepung menipis, mohon restock."></textarea>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="reset" class="btn btn-secondary waves-effect mr-1">Reset</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light" onclick="return confirm('Pastikan data sudah benar. Laporan tidak bisa diedit setelah dikirim. Lanjutkan?');">
                            <i class="ri-send-plane-fill align-middle mr-1"></i> Kirim Laporan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection