@extends('admin.layout')
@section('title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('styles')
<style>
    .small-box .inner h3 { font-size: 1.5rem; }
    .small-box .inner p { font-size: .82rem; margin-bottom: 0; }
    .small-box .icon i { font-size: 55px; }
    .growth-badge { font-size: .7rem; padding: 2px 6px; border-radius: 10px; }
    .stat-mini .info-box { min-height: 72px; }
    .stat-mini .info-box-icon { width: 60px; font-size: 1.3rem; line-height: 72px; }
    .stat-mini .info-box-content { padding: 10px 10px; }
    .stat-mini .info-box-text { font-size: .78rem; }
    .stat-mini .info-box-number { font-size: 1rem; font-weight: 700; }
    .chart-card .card-body { padding: 15px; }
    .top-earner-avatar { width:32px;height:32px;border-radius:50%;background:#1a3a5c;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700; flex-shrink:0; }
    .period-filter-bar { background:#fff;border-radius:8px;padding:.75rem 1rem;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:1rem; }
    .period-filter-bar select { border:1px solid #dee2e6;border-radius:6px;padding:.4rem .8rem;font-size:.88rem;font-weight:600;color:#1a3a5c; }
    .period-filter-bar select:focus { outline:none;border-color:#1a3a5c;box-shadow:0 0 0 2px rgba(26,58,92,.15); }
    .small-box .inner h3 small { font-size:.65em; font-weight:400; }
</style>
@endpush

@section('content')
@php
    $fmt  = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $fmtM = fn($n) => 'Rp ' . number_format($n/1000000, 2, '.', ',') . ' Jt';

    $growthKary = ($stats['karyawan_prev'] ?? 0) > 0
        ? round((($stats['total_karyawan'] - $stats['karyawan_prev']) / $stats['karyawan_prev']) * 100, 1)
        : 0;
    $growthGaji = ($stats['gaji_prev'] ?? 0) > 0
        ? round((($stats['total_gaji'] - $stats['gaji_prev']) / $stats['gaji_prev']) * 100, 1)
        : 0;
@endphp

{{-- ═══ FILTER PERIODE ═══ --}}
<div class="period-filter-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center">
        <i class="fas fa-filter text-primary mr-2"></i>
        <span class="font-weight-bold mr-3" style="font-size:.9rem">Filter Periode:</span>
        <form method="GET" action="{{ route('admin.dashboard') }}" id="periodFilterForm">
            <select name="period_id" class="form-control form-control-sm" data-autosubmit="true" style="min-width:260px">
                <option value="">-- Periode Terbaru --</option>
                @foreach($allPeriods as $ap)
                <option value="{{ $ap->id }}"
                    {{ (request('period_id') == $ap->id) ? 'selected' : '' }}>
                    {{ $ap->period_label }}
                    @if($ap->status === 'published') ✓ @else (Draft) @endif
                </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.salary.upload.form') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus mr-1"></i>Periode Baru
        </a>
        @if($totalPeriods > 0)
        <a href="{{ route('admin.salary.index') }}" class="btn btn-sm btn-outline-secondary ml-2">
            <i class="fas fa-list mr-1"></i>Semua Periode
        </a>
        @endif
    </div>
</div>

@if(!$latestPeriod)
{{-- EMPTY STATE --}}
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-database fa-4x text-muted mb-3 d-block"></i>
        <h5 class="text-muted">Belum ada data gaji</h5>
        <p class="text-muted mb-4">Upload data gaji pertama untuk melihat dashboard.</p>
        <a href="{{ route('admin.salary.upload.form') }}" class="btn btn-primary">
            <i class="fas fa-upload mr-2"></i>Upload Data Gaji Pertama
        </a>
    </div>
</div>
@else

{{-- ═══ HEADER INFO PERIODE AKTIF ═══ --}}
<div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background:rgba(26,58,92,.06);border-left:4px solid #1a3a5c">
    <div class="d-flex align-items-center">
        <i class="fas fa-calendar-check text-primary mr-2"></i>
        <div>
            <span class="text-muted small d-block" style="line-height:1.2">Menampilkan data periode:</span>
            <strong class="text-dark">{{ $latestPeriod->period_label }}</strong>
            <span class="ml-1">
                <small class="text-muted">
                    ({{ $latestPeriod->period_start->format('d M Y') }} – {{ $latestPeriod->period_end->format('d M Y') }})
                </small>
            </span>
        </div>
        <span class="badge {{ $latestPeriod->status === 'published' ? 'badge-success' : 'badge-warning' }} ml-2">
            {{ ucfirst($latestPeriod->status) }}
        </span>
    </div>
    <a href="{{ route('admin.salary.period', $latestPeriod->id) }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-eye mr-1"></i>Lihat Detail
    </a>
</div>

{{-- ═══ MAIN STAT CARDS ═══ --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ number_format($stats['total_karyawan']) }}</h3>
                <p>
                    Total Karyawan
                    @if($growthKary != 0)
                        <span class="growth-badge ml-1" style="background:rgba(255,255,255,.25)">
                            {{ $growthKary > 0 ? '+' : '' }}{{ $growthKary }}%
                        </span>
                    @endif
                </p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('admin.salary.period', $latestPeriod->id) }}" class="small-box-footer">
                Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($stats['total_gaji']/1000000, 1) }} <small>Juta</small></h3>
                <p>
                    Total Transfer
                    @if($growthGaji != 0)
                        <span class="growth-badge ml-1" style="background:rgba(255,255,255,.25)">
                            {{ $growthGaji > 0 ? '+' : '' }}{{ $growthGaji }}%
                        </span>
                    @endif
                </p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="{{ route('admin.salary.index') }}" class="small-box-footer">
                Semua Periode <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Rp {{ number_format($stats['avg_gaji']/1000000, 2) }} <small>Juta</small></h3>
                <p>Rata-rata Gaji / Karyawan</p>
            </div>
            <div class="icon"><i class="fas fa-chart-bar"></i></div>
            <a href="#" class="small-box-footer">Per karyawan</a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalPeriods }}</h3>
                <p>Total Periode</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <a href="{{ route('admin.salary.index') }}" class="small-box-footer">
                Lihat Semua <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- ═══ MINI INFO BOXES ═══ --}}
<div class="row stat-mini">
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-wallet"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Gaji Pokok</span>
                <span class="info-box-number">Rp {{ number_format($stats['total_gaji_pokok']/1000000, 1) }} Jt</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Lembur</span>
                <span class="info-box-number">Rp {{ number_format($stats['total_lembur']/1000000, 1) }} Jt</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-minus-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Potongan</span>
                <span class="info-box-number">Rp {{ number_format($stats['total_potongan']/1000000, 1) }} Jt</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Rata-rata Hadir</span>
                <span class="info-box-number">{{ $stats['avg_hadir'] }} Hari</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ CHARTS ROW ═══ --}}
<div class="row chart-card">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-chart-line text-primary mr-2"></i>Tren Penggajian (8 Periode Terakhir)
                </h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="90"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-university text-success mr-2"></i>Distribusi Bank
                </h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="bankChart" height="160"></canvas>
                <div class="mt-2">
                    @foreach($byBank->take(5) as $b)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted text-truncate" style="max-width:120px">{{ $b->nama_bank ?: 'N/A' }}</small>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-primary mr-1">{{ $b->jumlah }} org</span>
                            <small class="text-muted" style="font-size:.72rem">Rp {{ number_format($b->total/1000000, 1) }} Jt</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ TABLES ROW ═══ --}}
<div class="row">
    {{-- Department table --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-sitemap text-warning mr-2"></i>Penggajian per Departemen
                    <small class="text-muted font-weight-normal" style="font-size:.78rem">– {{ $latestPeriod->period_label }}</small>
                </h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Departemen</th>
                                <th class="text-center">Karyawan</th>
                                <th class="text-right">Gaji Pokok</th>
                                <th class="text-right">Total Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($byDept as $dept)
                            <tr>
                                <td>
                                    <span class="font-weight-500" style="font-size:.82rem">{{ $dept->department ?: 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $dept->jumlah }}</span>
                                </td>
                                <td class="text-right" style="font-size:.82rem">
                                    Rp {{ number_format($dept->total_pokok, 0, ',', '.') }}
                                </td>
                                <td class="text-right font-weight-bold" style="font-size:.82rem">
                                    Rp {{ number_format($dept->total, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                        @if($byDept->count() > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <td class="font-weight-bold">TOTAL</td>
                                <td class="text-center font-weight-bold">{{ $byDept->sum('jumlah') }}</td>
                                <td class="text-right font-weight-bold">Rp {{ number_format($byDept->sum('total_pokok'), 0, ',', '.') }}</td>
                                <td class="text-right font-weight-bold text-success">Rp {{ number_format($byDept->sum('total'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top earners + recent periods --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-trophy text-warning mr-2"></i>Top 5 Penerima Gaji
                    <small class="text-muted font-weight-normal" style="font-size:.78rem">– {{ $latestPeriod->period_label }}</small>
                </h5>
            </div>
            <div class="card-body p-0">
                @forelse($topEarners as $i => $te)
                <div class="d-flex align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="top-earner-avatar mr-3">{{ $i+1 }}</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="font-weight-bold text-truncate" style="font-size:.85rem">{{ $te->nama }}</div>
                        <small class="text-muted text-truncate d-block">{{ $te->department ?: '-' }}</small>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <div class="font-weight-bold text-success" style="font-size:.82rem">
                            Rp {{ number_format($te->total_ditransfer, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">Lembur: Rp {{ number_format($te->lembur, 0, ',', '.') }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 d-flex align-items-center justify-content-between">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-history text-info mr-2"></i>Riwayat Periode
                </h5>
                <a href="{{ route('admin.salary.index') }}" class="btn btn-xs btn-outline-primary">Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Periode</th>
                                <th class="text-center">Karyawan</th>
                                <th class="text-right">Transfer</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periods->take(6) as $p)
                            <tr class="{{ $latestPeriod->id === $p->id ? 'table-primary' : '' }}">
                                <td>
                                    <a href="{{ route('admin.salary.period', $p->id) }}"
                                       class="text-decoration-none font-weight-bold" style="font-size:.82rem">
                                        {{ $p->period_label }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary">{{ $p->records_count ?? 0 }}</span>
                                </td>
                                <td class="text-right" style="font-size:.8rem">
                                    Rp {{ number_format(($p->records_sum_total_ditransfer ?? 0)/1000000, 1) }} Jt
                                </td>
                                <td>
                                    <span class="badge {{ $p->status === 'published' ? 'badge-success' : 'badge-warning' }}">
                                        {{ $p->status === 'published' ? 'Pub' : 'Draft' }}
                                    </span>
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

@endif {{-- end $latestPeriod --}}

@push('scripts')
<script>
@if(isset($monthlyTrend) && $monthlyTrend->count() > 0)
const labels  = @json($monthlyTrend->pluck('period_label'));
const totals  = @json($monthlyTrend->pluck('records_sum_total_ditransfer'));
const counts  = @json($monthlyTrend->pluck('records_count'));
const lemburs = @json($monthlyTrend->pluck('records_sum_lembur'));

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Total Transfer (Juta Rp)',
            data: totals.map(v => ((v||0)/1000000).toFixed(2)),
            borderColor: '#1a3a5c',
            backgroundColor: 'rgba(26,58,92,.08)',
            fill: true, tension: .4, yAxisID: 'y',
            pointBackgroundColor: '#1a3a5c', pointRadius: 4,
        }, {
            label: 'Total Lembur (Juta Rp)',
            data: lemburs.map(v => ((v||0)/1000000).toFixed(2)),
            borderColor: '#f0a500',
            backgroundColor: 'rgba(240,165,0,.05)',
            fill: false, tension: .4, yAxisID: 'y',
            borderDash: [4,4], pointRadius: 3,
        }, {
            label: 'Jumlah Karyawan',
            data: counts,
            borderColor: '#28a745',
            borderDash: [6,3], tension: .4, yAxisID: 'y1',
            pointRadius: 3, fill: false,
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top', labels: { font: { size: 11 } } },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        if (ctx.datasetIndex < 2) {
                            return ctx.dataset.label + ': Rp ' + parseFloat(ctx.raw).toLocaleString('id-ID') + ' Jt';
                        }
                        return ctx.dataset.label + ': ' + ctx.raw + ' org';
                    }
                }
            }
        },
        scales: {
            y:  { type: 'linear', position: 'left',  title: { display: true, text: 'Juta Rp' }, grid: { color: '#f0f0f0' } },
            y1: { type: 'linear', position: 'right', title: { display: true, text: 'Karyawan' }, grid: { drawOnChartArea: false } }
        }
    }
});
@endif

@if(isset($byBank) && $byBank->count() > 0)
new Chart(document.getElementById('bankChart'), {
    type: 'doughnut',
    data: {
        labels: @json($byBank->pluck('nama_bank')),
        datasets: [{
            data: @json($byBank->pluck('jumlah')),
            backgroundColor: ['#1a3a5c','#2563a8','#16a34a','#f59e0b','#7c3aed','#dc2626','#0891b2','#db2777'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return ctx.label + ': ' + ctx.raw + ' karyawan';
                    }
                }
            }
        }
    }
});
@endif
</script>
@endpush
@endsection
