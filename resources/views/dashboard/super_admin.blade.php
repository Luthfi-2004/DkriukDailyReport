@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
{{-- TITLE SECTION --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Control Panel System</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">SandLab</a></li>
                    <li class="breadcrumb-item active">Super Admin</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- BARIS 1: STATISTIK USER (KHUSUS SUPER ADMIN) --}}
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Total Pengguna</p>
                        <h4 class="mb-0">{{ $total_users }}</h4>
                    </div>
                    <div class="text-primary">
                        <i class="ri-group-line font-size-24"></i>
                    </div>
                </div>
            </div>
            <div class="card-body border-top py-3">
                <div class="text-truncate">
                    <span class="badge badge-soft-success font-size-11"><i class="mdi mdi-menu-up"></i> Active</span>
                    <span class="text-muted ml-2">Akun Terdaftar</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Staff Gudang</p>
                        <h4 class="mb-0">{{ $total_staff }}</h4>
                    </div>
                    <div class="text-primary">
                        <i class="ri-user-line font-size-24"></i>
                    </div>
                </div>
            </div>
            <div class="card-body border-top py-3">
                <div class="text-truncate">
                    <span class="text-muted">User Input Data</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK OPERASIONAL (DARI DATA ADMIN) --}}
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Laporan Hari Ini</p>
                        <h4 class="mb-0">{{ $reports_today }}</h4>
                    </div>
                    <div class="text-success">
                        <i class="ri-file-text-line font-size-24"></i>
                    </div>
                </div>
            </div>
            <div class="card-body border-top py-3">
                <div class="text-truncate">
                    <span class="text-muted">Perlu Dicek</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Est. Biaya Bulan Ini</p>
                        <h4 class="mb-0">Rp {{ number_format($cost_this_month / 1000000, 1, ',', '.') }} Jt</h4>
                    </div>
                    <div class="text-primary">
                        <i class="ri-money-dollar-circle-line font-size-24"></i>
                    </div>
                </div>
            </div>
            <div class="card-body border-top py-3">
                <div class="text-truncate">
                    <span class="text-muted">Total Rp {{ number_format($cost_this_month, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- END BARIS 1 --}}

{{-- BARIS 2: GRAFIK & NAVIGASI CEPAT --}}
<div class="row">
    {{-- GRAFIK PENGELUARAN --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Tren Pengeluaran Operasional (7 Hari)</h4>
                <div id="super-expense-chart" class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>

    {{-- SHORTCUT SYSTEM --}}
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Akses Cepat Sistem</h4>
                
                <div class="d-grid gap-3">
                    <a href="{{ route('manage-users.index') }}" class="btn btn-outline-primary btn-block waves-effect waves-light text-left p-3 mb-3">
                        <i class="ri-user-settings-line align-middle mr-2 font-size-18"></i> 
                        <strong>Kelola Pengguna</strong>
                        <p class="text-muted mb-0 mt-1 font-size-12">Tambah, Edit, Hapus akun Admin/Staff</p>
                    </a>

                    <a href="{{ route('master-items.index') }}" class="btn btn-outline-success btn-block waves-effect waves-light text-left p-3 mb-3">
                        <i class="ri-database-2-line align-middle mr-2 font-size-18"></i> 
                        <strong>Master Data Barang</strong>
                        <p class="text-muted mb-0 mt-1 font-size-12">Atur Harga & Satuan Barang</p>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-info btn-block waves-effect waves-light text-left p-3">
                        <i class="ri-pie-chart-2-line align-middle mr-2 font-size-18"></i> 
                        <strong>Rekapitulasi Laporan</strong>
                        <p class="text-muted mb-0 mt-1 font-size-12">Lihat semua data laporan masuk</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BARIS 3: TABEL LAPORAN TERBARU --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">5 Laporan Terakhir Masuk</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Staff</th>
                                <th>Total Item</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_reports as $report)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                                <td><h5 class="font-size-14 mb-0">{{ $report->user->name }}</h5></td>
                                <td>{{ $report->details->count() }} Item</td>
                                <td>
                                    <span class="badge badge-soft-success font-size-12">Submitted</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-primary btn-sm btn-rounded">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Load ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Logic Grafik sama persis dengan Admin
    var dates = @json($chart_dates);
    var values = @json($chart_values);

    var options = {
        series: [{ name: 'Pengeluaran (Rp)', data: values }],
        chart: { height: 320, type: 'area', toolbar: {show: false} },
        colors: ['#34c38f'], // Warna Hijau beda dikit biar fresh
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100]
            },
        },
        xaxis: { categories: dates },
        yaxis: {
            labels: {
                formatter: function (value) {
                    if(value >= 1000000) return (value/1000000).toFixed(1) + " Jt";
                    if(value >= 1000) return (value/1000).toFixed(0) + " Rb";
                    return value;
                }
            }
        },
        tooltip: {
            y: { formatter: function (val) { return "Rp " + new Intl.NumberFormat('id-ID').format(val); } }
        }
    };

    var chart = new ApexCharts(document.querySelector("#super-expense-chart"), options);
    chart.render();
</script>
@endpush

@endsection