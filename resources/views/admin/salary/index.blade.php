@extends('admin.layout')
@section('title', 'Daftar Periode Gaji')
@section('breadcrumb')
    <li class="breadcrumb-item active">Periode Gaji</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">
        <i class="fas fa-info-circle mr-1"></i>
        Total <strong>{{ $periods->total() }}</strong> periode gaji tersimpan
    </div>
    <a href="{{ route('admin.salary.upload.form') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i>Upload Periode Baru
    </a>
</div>

<div class="card">
    <div class="card-header border-0">
        <h5 class="card-title mb-0 font-weight-bold">
            <i class="fas fa-calendar-alt text-primary mr-2"></i>Semua Periode Gaji
        </h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Periode</th>
                        <th>Rentang Tanggal</th>
                        <th class="text-center">Hari Kerja</th>
                        <th class="text-center">Karyawan</th>
                        <th class="text-right">Total Transfer</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $p)
                    <tr>
                        <td>
                            <a href="{{ route('admin.salary.period', $p->id) }}"
                               class="font-weight-bold text-dark text-decoration-none">
                                {{ $p->period_label }}
                            </a>
                            @if($p->uploaded_file)
                                <br><small class="text-muted"><i class="fas fa-file-excel mr-1"></i>Excel tersedia</small>
                            @endif
                        </td>
                        <td>
                            <div style="font-size:.85rem">
                                <i class="far fa-calendar mr-1 text-muted"></i>
                                {{ $p->period_start->format('d M Y') }}
                            </div>
                            <div style="font-size:.85rem">
                                <i class="far fa-calendar-check mr-1 text-muted"></i>
                                {{ $p->period_end->format('d M Y') }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary">{{ $p->working_days }} hari</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-primary" style="font-size:.82rem">
                                {{ number_format($p->records_count) }}
                            </span>
                        </td>
                        <td class="text-right font-weight-bold" style="font-size:.88rem">
                            Rp {{ number_format($p->records_sum_total_ditransfer ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($p->status === 'published')
                                <span class="badge badge-success px-3">
                                    <i class="fas fa-check mr-1"></i>Published
                                </span>
                            @else
                                <span class="badge badge-warning px-3">
                                    <i class="fas fa-pencil-alt mr-1"></i>Draft
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.salary.period', $p->id) }}"
                                   class="btn btn-outline-primary" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($p->status !== 'published')
                                    <form action="{{ route('admin.salary.period.publish', $p->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-success" title="Publish"
                                                onclick="return confirm('Publish periode {{ $p->period_label }}? Karyawan akan dapat melihat slip gaji.')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.salary.record.create', $p->id) }}"
                                   class="btn btn-outline-info" title="Tambah Manual">
                                    <i class="fas fa-user-plus"></i>
                                </a>
                                <form action="{{ route('admin.salary.period.destroy', $p->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Hapus Periode"
                                            onclick="return confirm('Hapus periode \'{{ $p->period_label }}\'?\n\nSEMUA data gaji ({{ $p->records_count }} karyawan) akan terhapus permanen!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x d-block mb-2"></i>
                            Belum ada data periode gaji.
                            <a href="{{ route('admin.salary.upload.form') }}" class="d-block mt-2">
                                Upload sekarang
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($periods->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $periods->firstItem() }}–{{ $periods->lastItem() }} dari {{ $periods->total() }} periode
        </small>
        {{ $periods->links() }}
    </div>
    @endif
</div>
@endsection
