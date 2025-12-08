@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
{{-- HEADER --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Halo, {{ $user->name }}!</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- STATUS HARIAN (MAIN CALL TO ACTION) --}}
<div class="row">
    <div class="col-lg-12">
        @if($hasReportedToday)
            {{-- TAMPILAN JIKA SUDAH LAPOR (HIJAU/TENANG) --}}
            <div class="card bg-success text-white border-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-2"><i class="ri-checkbox-circle-line mr-2"></i>Laporan Hari Ini Aman!</h3>
                            <p class="mb-0 text-white-50">Terima kasih telah melakukan input data operasional tepat waktu. Data Anda sudah tersimpan di sistem.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('reports.index') }}" class="btn btn-light waves-effect waves-light text-success font-weight-bold shadow-sm">
                                <i class="ri-file-list-3-line mr-1"></i> Lihat Riwayat Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- TAMPILAN JIKA BELUM LAPOR (MERAH/URGENT) --}}
            <div class="card bg-danger text-white border-danger shadow-lg">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-2"><i class="ri-alarm-warning-line mr-2"></i>Anda Belum Lapor Hari Ini</h3>
                            <p class="mb-0 text-white-50">Mohon segera lakukan input data operasional sebelum jam kerja berakhir.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('reports.create') }}" class="btn btn-light waves-effect waves-light text-danger font-weight-bold shadow-sm btn-lg">
                                <i class="ri-edit-2-line mr-1"></i> Input Laporan Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- STATISTIK & HISTORY --}}
<div class="row">
    {{-- KARTU STATISTIK KECIL --}}
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body">
                        <p class="text-muted font-weight-medium">Total Laporan (Bulan Ini)</p>
                        <h4 class="mb-0">{{ $reportsThisMonth }} <small class="text-muted font-size-14">Submit</small></h4>
                    </div>
                    <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                        <span class="avatar-title rounded-circle bg-primary">
                            <i class="ri-calendar-check-line font-size-24"></i>
                        </span>
                    </div>
                </div>
                <div class="pt-2 mt-3 border-top">
                    <p class="text-muted mb-0">Terus pertahankan konsistensi!</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Informasi Akun</h4>
                <div class="text-center">
                    <div class="mb-3">
                        <img src="{{ asset('assets/images/users/avatar-2.jpg') }}" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                    </div>
                    <p class="text-muted font-size-13 mb-1">Login sebagai:</p>
                    <h5 class="font-size-16 mb-1">{{ $user->name }}</h5>
                    <span class="badge badge-soft-secondary mb-3">{{ $user->email }}</span>
                    
                    <div class="mt-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-block">
                                <i class="ri-shut-down-line mr-1"></i> Logout Sistem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT TERAKHIR --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Riwayat Aktivitas Terakhir</h4>
                    <a href="{{ route('reports.index') }}" class="text-primary font-size-13">Lihat Semua <i class="mdi mdi-arrow-right"></i></a>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Waktu Input</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReports as $report)
                            <tr>
                                <td>
                                    <h5 class="font-size-14 mb-0 font-weight-bold">
                                        {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
                                    </h5>
                                </td>
                                <td>
                                    <span class="badge badge-soft-success font-size-12">
                                        <i class="ri-checkbox-circle-line align-middle mr-1"></i> Submitted
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ $report->created_at->format('H:i') }} WIB
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('reports.show', $report->id) }}" class="btn btn-outline-primary btn-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ri-folder-open-line display-4 d-block mb-3"></i>
                                        <p>Belum ada aktivitas laporan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection