@extends('layouts.app')

@section('title', 'Dashboard Manajer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Operational Overview</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">{{ date('d F Y') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- KARTU STATISTIK --}}
<div class="row">
    {{-- Kartu Laporan Hari Ini --}}
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="float-right mt-2">
                    <div class="avatar-sm rounded-circle bg-soft-primary border-soft-primary text-primary">
                        <i class="ri-file-text-line font-size-20 avatar-title"></i>
                    </div>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span data-plugin="counterup">{{ $stats['reports_today'] }}</span></h4>
                    <p class="text-muted mb-0">Laporan Masuk Hari Ini</p>
                </div>
                <p class="text-muted mt-3 mb-0">
                    @if($stats['reports_today'] > 0)
                        <span class="text-success mr-1"><i class="mdi mdi-arrow-up-bold mr-1"></i>Active</span>
                        Data masuk
                    @else
                        <span class="text-danger mr-1"><i class="mdi mdi-alert-circle-outline mr-1"></i>Empty</span>
                        Belum ada laporan
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Kartu Estimasi Biaya Bulan Ini --}}
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="float-right mt-2">
                    <div class="avatar-sm rounded-circle bg-soft-success border-soft-success text-success">
                        <i class="ri-money-dollar-circle-line font-size-20 avatar-title"></i>
                    </div>
                </div>
                <div>
                    {{-- Format Rupiah --}}
                    <h4 class="mb-1 mt-1">Rp {{ number_format($stats['cost_this_month'], 0, ',', '.') }}</h4>
                    <p class="text-muted mb-0">Est. Pengeluaran (Bulan Ini)</p>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <span class="text-success mr-1"><i class="mdi mdi-calendar mr-1"></i>{{ date('F Y') }}</span>
                    Akumulasi item keluar
                </p>
            </div>
        </div>
    </div>

    {{-- Kartu Info --}}
    <div class="col-md-6 col-xl-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="float-right mt-2">
                    <i class="ri-user-star-line display-4 text-white-50"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1 text-white">{{ $user->name }}</h4>
                    <p class="text-white-50 mb-0">Admin Operasional</p>
                </div>
                <p class="text-white-50 mt-3 mb-0">
                    Pantau terus stok dan laporan harian untuk menjaga efisiensi.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- BARIS GRAFIK & TABEL --}}
<div class="row">
    {{-- GRAFIK --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Tren Pengeluaran (7 Hari Terakhir)</h4>
                {{-- Container Chart --}}
                <div id="expense-chart" class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>

    {{-- TABEL LAPORAN TERBARU --}}
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Laporan Terbaru</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">User</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_reports'] as $report)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d M') }}</td>
                                <td>
                                    <h5 class="font-size-14 mb-0">{{ $report->user->name }}</h5>
                                </td>
                                <td>
                                    <span class="badge badge-soft-success">Submitted</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-sm">Lihat Semua Laporan</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Load ApexCharts dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Data dari Controller (Blade to JS)
    var dates = @json($stats['chart_dates']);
    var values = @json($stats['chart_values']);

    var options = {
        series: [{
            name: 'Pengeluaran (Rp)',
            data: values
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false }
        },
        colors: ['#556ee6'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            },
        },
        xaxis: {
            categories: dates,
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    // Format ribuan sederhana (k = ribu, jt = juta)
                    if(value >= 1000000) return (value/1000000).toFixed(1) + " Jt";
                    if(value >= 1000) return (value/1000).toFixed(0) + " Rb";
                    return value;
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#expense-chart"), options);
    chart.render();
</script>
@endpush

@endsection