@extends('admin.layout')
@section('title', 'Preview Data Import')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.salary.index') }}">Periode Gaji</a></li>
    <li class="breadcrumb-item active">Preview Import</li>
@endsection

@push('styles')
<style>
.preview-banner {
    background: linear-gradient(135deg, #1a3a5c, #2563a8);
    border-radius: 10px;
    padding: 1.25rem 1.5rem;
    color: #fff;
    margin-bottom: 1.25rem;
}
.preview-banner .badge-draft {
    background: rgba(255,165,0,.25);
    color: #f0a500;
    border: 1px solid rgba(240,165,0,.4);
    font-size: .8rem;
    padding: 3px 10px;
    border-radius: 20px;
}
.stat-review { background: #fff; border-radius: 8px; padding: .85rem 1rem; }
.stat-review .val { font-size: 1.3rem; font-weight: 700; }
</style>
@endpush

@section('content')

{{-- ═══ REVIEW BANNER ═══ --}}
<div class="preview-banner">
    <div class="d-flex align-items-start justify-content-between flex-wrap">
        <div>
            <div class="d-flex align-items-center mb-1">
                <i class="fas fa-clipboard-check mr-2" style="font-size:1.2rem"></i>
                <h5 class="mb-0 font-weight-bold">Review Data Sebelum Publish</h5>
                <span class="badge-draft ml-2">DRAFT</span>
            </div>
            <p class="mb-0" style="color:rgba(255,255,255,.8);font-size:.88rem">
                Data berhasil diimport. Periksa kembali seluruh data di bawah sebelum dipublish ke karyawan.
                Setelah publish, karyawan dapat melihat slip gaji mereka.
            </p>
        </div>
        <div class="d-flex mt-3 mt-sm-0">
            <form action="{{ route('admin.salary.period.publish', $period->id) }}" method="POST" class="mr-2">
                @csrf
                <button type="submit" class="btn btn-success"
                        onclick="return confirm('Publish periode {{ $period->period_label }}?\n\nKaryawan akan dapat melihat slip gaji mereka.')">
                    <i class="fas fa-check-circle mr-1"></i>Publish Sekarang
                </button>
            </form>
            <a href="{{ route('admin.salary.period', $period->id) }}" class="btn btn-outline-light">
                <i class="fas fa-eye mr-1"></i>Simpan Draft
            </a>
        </div>
    </div>
</div>

{{-- ═══ PERIOD INFO ═══ --}}
<div class="row mb-3">
    <div class="col-md-3 col-6">
        <div class="stat-review shadow-sm">
            <div class="text-muted small">Total Karyawan</div>
            <div class="val text-primary">{{ number_format($stats['total']) }}</div>
            <small class="text-muted">Berhasil diimport</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-review shadow-sm">
            <div class="text-muted small">Total Transfer</div>
            <div class="val text-success" style="font-size:1rem">
                Rp {{ number_format($stats['total_gaji'], 0, ',', '.') }}
            </div>
            <small class="text-muted">Seluruh karyawan</small>
        </div>
    </div>
    <div class="col-md-3 col-6 mt-2 mt-md-0">
        <div class="stat-review shadow-sm">
            <div class="text-muted small">Total Potongan</div>
            <div class="val text-danger" style="font-size:1rem">
                Rp {{ number_format($stats['total_potongan'], 0, ',', '.') }}
            </div>
            <small class="text-muted">BPJS + PPh + lainnya</small>
        </div>
    </div>
    <div class="col-md-3 col-6 mt-2 mt-md-0">
        <div class="stat-review shadow-sm">
            <div class="text-muted small">Periode</div>
            <div class="font-weight-bold" style="font-size:.9rem">{{ $period->period_label }}</div>
            <small class="text-muted">
                {{ $period->period_start->format('d/m/Y') }} &ndash; {{ $period->period_end->format('d/m/Y') }}
            </small>
        </div>
    </div>
</div>

{{-- ═══ DATA TABLE ═══ --}}
<div class="card">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 font-weight-bold">
            <i class="fas fa-table text-primary mr-2"></i>
            Daftar {{ $stats['total'] }} Karyawan
        </h5>
        <div class="d-flex align-items-center">
            <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                   placeholder="Cari nama / NIK..." style="width:200px">
            <a href="{{ route('admin.salary.record.create', $period->id) }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i>Tambah Manual
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0" id="previewTable">
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
                        <td class="text-muted">{{ $i+1 }}</td>
                        <td><code style="font-size:.78rem">{{ $r->nik }}</code></td>
                        <td class="font-weight-500">{{ $r->nama }}</td>
                        <td class="text-truncate" style="max-width:130px;font-size:.82rem">{{ $r->department ?: '-' }}</td>
                        <td style="font-size:.82rem">{{ $r->nama_bank ?: '-' }}</td>
                        <td class="text-right" style="font-size:.82rem">{{ number_format($r->gaji_pokok,0,',','.') }}</td>
                        <td class="text-right" style="font-size:.82rem">{{ number_format($r->lembur,0,',','.') }}</td>
                        <td class="text-right text-danger" style="font-size:.82rem">{{ number_format($r->total_potongan,0,',','.') }}</td>
                        <td class="text-right font-weight-bold text-success" style="font-size:.82rem">
                            {{ number_format($r->total_ditransfer,0,',','.') }}
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
    <div class="card-footer bg-white d-flex align-items-center justify-content-between">
        <small class="text-muted">Total {{ $stats['total'] }} data karyawan berhasil diimport</small>
        <div class="d-flex">
            <form action="{{ route('admin.salary.period.publish', $period->id) }}" method="POST" class="mr-2">
                @csrf
                <button type="submit" class="btn btn-success"
                        onclick="return confirm('Publish periode {{ $period->period_label }}?')">
                    <i class="fas fa-check-circle mr-1"></i>Publish Sekarang
                </button>
            </form>
            <form action="{{ route('admin.salary.period.destroy', $period->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"
                        onclick="return confirm('Hapus periode ini beserta semua data karyawan? Tindakan ini tidak bisa dibatalkan!')">
                    <i class="fas fa-trash mr-1"></i>Batalkan & Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#previewTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
