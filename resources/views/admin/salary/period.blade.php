@extends('admin.layout')
@section('title', 'Periode: ' . $period->period_label)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.salary.index') }}">Periode Gaji</a></li>
    <li class="breadcrumb-item active">{{ $period->period_label }}</li>
@endsection

@section('content')

{{-- ═══ ACTION BAR ═══ --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
    <div class="d-flex align-items-center">
        <span class="badge {{ $period->status === 'published' ? 'badge-success' : 'badge-warning' }} mr-2 px-3 py-2"
              style="font-size:.85rem">
            <i class="fas {{ $period->status === 'published' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
            {{ ucfirst($period->status) }}
        </span>
        <small class="text-muted">
            {{ $period->period_start->format('d M Y') }} &ndash; {{ $period->period_end->format('d M Y') }}
            &bull; {{ $period->working_days }} hari kerja
        </small>
    </div>
    <div class="d-flex mt-2 mt-sm-0">
        <a href="{{ route('admin.salary.record.create', $period->id) }}" class="btn btn-success btn-sm mr-2">
            <i class="fas fa-user-plus mr-1"></i>Tambah Manual
        </a>
        @if($period->status !== 'published')
            <form action="{{ route('admin.salary.period.publish', $period->id) }}" method="POST" class="mr-2">
                @csrf
                <button class="btn btn-primary btn-sm"
                        onclick="return confirm('Publish periode ini? Karyawan akan dapat melihat slip gaji.')">
                    <i class="fas fa-check mr-1"></i>Publish
                </button>
            </form>
        @endif
        <form action="{{ route('admin.salary.period.destroy', $period->id) }}" method="POST">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"
                    onclick="return confirm('Hapus periode \'{{ $period->period_label }}\'?\n\nSemua {{ $stats['total'] }} data karyawan akan terhapus permanen!')">
                <i class="fas fa-trash mr-1"></i>Hapus Periode
            </button>
        </form>
    </div>
</div>

{{-- ═══ STAT CARDS ═══ --}}
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Karyawan</span>
                <span class="info-box-number">{{ number_format($stats['total']) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Transfer</span>
                <span class="info-box-number" style="font-size:.95rem">
                    Rp {{ number_format($stats['total_gaji']/1000000, 1) }}M
                </span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-minus-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Potongan</span>
                <span class="info-box-number" style="font-size:.95rem">
                    Rp {{ number_format($stats['total_potongan']/1000000, 1) }}M
                </span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Rata-rata Transfer</span>
                <span class="info-box-number" style="font-size:.95rem">
                    Rp {{ $stats['total'] > 0 ? number_format($stats['total_gaji']/$stats['total']/1000000, 2) : '0' }}M
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ DATA TABLE ═══ --}}
<div class="card">
    <div class="card-header border-0 d-flex align-items-center justify-content-between flex-wrap">
        <h5 class="card-title mb-0 font-weight-bold">
            <i class="fas fa-list text-primary mr-2"></i>Data Karyawan – {{ $period->period_label }}
        </h5>
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <input type="text" class="form-control form-control-sm" id="searchInput"
                   placeholder="Cari nama / NIK / dept..." style="width:220px">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0" id="salaryTable">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Bank</th>
                        <th class="text-right">Gaji Pokok</th>
                        <th class="text-right">Lembur</th>
                        <th class="text-right">Potongan</th>
                        <th class="text-right">Transfer</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $i => $r)
                    <tr>
                        <td class="text-muted">{{ $records->firstItem() + $i }}</td>
                        <td><code style="font-size:.75rem;background:#f0f4f8;color:#1a3a5c;padding:1px 4px;border-radius:3px">{{ $r->nik }}</code></td>
                        <td class="font-weight-500">{{ $r->nama }}</td>
                        <td class="text-truncate" style="max-width:140px;font-size:.82rem">{{ $r->department ?: '-' }}</td>
                        <td style="font-size:.82rem">{{ $r->nama_bank ?: '-' }}</td>
                        <td class="text-right" style="font-size:.82rem">
                            {{ number_format($r->gaji_pokok, 0, ',', '.') }}
                        </td>
                        <td class="text-right" style="font-size:.82rem">
                            {{ number_format($r->lembur, 0, ',', '.') }}
                        </td>
                        <td class="text-right text-danger" style="font-size:.82rem">
                            {{ number_format($r->total_potongan, 0, ',', '.') }}
                        </td>
                        <td class="text-right font-weight-bold text-success" style="font-size:.82rem">
                            {{ number_format($r->total_ditransfer, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.salary.record.edit', $r->id) }}"
                                   class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.salary.record.destroy', $r->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Hapus"
                                            onclick="return confirm('Hapus data {{ addslashes($r->nama) }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $records->firstItem() }}–{{ $records->lastItem() }} dari {{ $records->total() }} karyawan
        </small>
        {{ $records->links() }}
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#salaryTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
