@extends('admin.layout')
@section('title', 'Upload Data Gaji')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.salary.index') }}">Periode Gaji</a></li>
    <li class="breadcrumb-item active">Upload Excel</li>
@endsection

@push('styles')
<style>
.upload-zone {
    border: 2px dashed #c3cdd8;
    border-radius: 10px;
    background: #f8fafc;
    cursor: pointer;
    transition: all .25s;
    padding: 2.5rem 1.5rem;
    text-align: center;
}
.upload-zone:hover, .upload-zone.dragging {
    border-color: #1a3a5c;
    background: #eef3f8;
}
.upload-zone.has-file {
    border-color: #28a745;
    background: #f0fff4;
}
.upload-zone .upload-icon { font-size: 2.5rem; color: #9aafbf; transition: color .2s; }
.upload-zone.has-file .upload-icon { color: #28a745; }
.step-circle {
    width: 28px; height: 28px; border-radius: 50%;
    background: #1a3a5c; color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 700; flex-shrink: 0;
}
.col-mapping-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 4px; }
@media(min-width:768px) { .col-mapping-grid { grid-template-columns: repeat(3,1fr); } }
.col-item { background: #f8f9fa; border-radius: 5px; padding: 5px 8px; font-size: .78rem; }
.col-item code { background: #1a3a5c; color: #f0a500; padding: 1px 5px; border-radius: 3px; font-size: .75rem; }
</style>
@endpush

@section('content')
<div class="row">
<div class="col-lg-8">

{{-- UPLOAD CARD --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-upload mr-2"></i>Upload File Excel Gaji
        </h5>
    </div>
    <div class="card-body">

        {{-- STEPS --}}
        <div class="d-flex align-items-start mb-4">
            <div class="step-circle mr-3 mt-1">1</div>
            <div>
                <div class="font-weight-bold" style="font-size:.9rem">Download & Isi Template</div>
                <div class="text-muted small">
                    Gunakan template resmi.
                    <a href="{{ route('admin.salary.template') }}" class="text-primary font-weight-bold">
                        <i class="fas fa-download mr-1"></i>Download Template Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-start mb-4">
            <div class="step-circle mr-3 mt-1">2</div>
            <div>
                <div class="font-weight-bold" style="font-size:.9rem">Isi Informasi Periode</div>
                <div class="text-muted small">Lengkapi nama periode, tanggal, dan hari kerja.</div>
            </div>
        </div>
        <div class="d-flex align-items-start mb-4">
            <div class="step-circle mr-3 mt-1">3</div>
            <div>
                <div class="font-weight-bold" style="font-size:.9rem">Upload & Review</div>
                <div class="text-muted small">Sistem akan import data dan menampilkan preview sebelum dipublish.</div>
            </div>
        </div>

        <hr class="my-3">

        <form action="{{ route('admin.salary.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Label Periode <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('period_label') is-invalid @enderror"
                               name="period_label" value="{{ old('period_label') }}"
                               placeholder="Contoh: Maret 2026" required>
                        @error('period_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nama yang akan ditampilkan kepada karyawan</small>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('period_start') is-invalid @enderror"
                               name="period_start" value="{{ old('period_start') }}" required>
                        @error('period_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal Akhir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('period_end') is-invalid @enderror"
                               name="period_end" value="{{ old('period_end') }}" required>
                        @error('period_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold">Hari Kerja</label>
                        <input type="number" class="form-control" name="working_days"
                               value="{{ old('working_days', 26) }}" min="1" max="31">
                    </div>
                </div>
            </div>

            {{-- FILE DROP ZONE --}}
            <div class="form-group">
                <label class="font-weight-bold">File Excel <span class="text-danger">*</span></label>
                <div class="upload-zone" id="dropzone" onclick="document.getElementById('excel_file').click()">
                    <i class="fas fa-file-excel upload-icon d-block mb-2"></i>
                    <div class="font-weight-bold mb-1" id="dropTitle">Drag & drop atau klik untuk pilih file</div>
                    <div class="text-muted small mb-2" id="dropSub">Format: .xlsx atau .xls &bull; Maks. 20 MB</div>
                    <div id="fileInfo" class="d-none">
                        <span class="badge badge-success px-3 py-2" id="fileName" style="font-size:.85rem"></span>
                        <div class="text-success small mt-1">File siap diupload</div>
                    </div>
                    <input type="file" name="excel_file" id="excel_file"
                           accept=".xlsx,.xls" class="d-none" required>
                </div>
                @error('excel_file')
                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex align-items-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5 mr-3" id="submitBtn">
                    <i class="fas fa-upload mr-2"></i>Upload &amp; Import
                </button>
                <a href="{{ route('admin.salary.index') }}" class="btn btn-default btn-lg">
                    <i class="fas fa-times mr-1"></i>Batal
                </a>
                <div id="loadingIndicator" class="d-none ml-3">
                    <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                    <span class="text-muted">Sedang memproses file...</span>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
<div class="col-lg-4">

{{-- INFO CARD --}}
<div class="card card-info card-outline">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-info-circle mr-2"></i>Panduan Kolom Excel</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Sheet <strong>"DFT GJ"</strong> harus ada. Data karyawan diawali NIK <code>PUS</code>.
        </p>
        <div class="col-mapping-grid">
            <div class="col-item"><code>C</code> NIK</div>
            <div class="col-item"><code>D</code> Nama</div>
            <div class="col-item"><code>E</code> NIK KTP</div>
            <div class="col-item"><code>F</code> Departemen</div>
            <div class="col-item"><code>J</code> No. Rekening</div>
            <div class="col-item"><code>K</code> Bank</div>
            <div class="col-item"><code>L</code> Gaji Pokok</div>
            <div class="col-item"><code>M</code> Gaji Kurangi</div>
            <div class="col-item"><code>N</code> Rapel</div>
            <div class="col-item"><code>O</code> Kompensasi</div>
            <div class="col-item"><code>P</code> Lembur</div>
            <div class="col-item"><code>Q</code> Total Pend.</div>
            <div class="col-item"><code>R</code> BPJS Kes.</div>
            <div class="col-item"><code>S</code> BPJS TK</div>
            <div class="col-item"><code>T</code> PPh 21</div>
            <div class="col-item"><code>X</code> Pinjaman</div>
            <div class="col-item"><code>Y</code> Sumbangan</div>
            <div class="col-item"><code>Z</code> Total Pot.</div>
            <div class="col-item"><code>AA</code> Transfer</div>
            <div class="col-item"><code>AO</code> Hadir</div>
        </div>
    </div>
    <div class="card-footer bg-transparent">
        <a href="{{ route('admin.salary.template') }}" class="btn btn-success btn-block">
            <i class="fas fa-download mr-2"></i>Download Template Resmi
        </a>
    </div>
</div>

<div class="callout callout-warning">
    <h6><i class="fas fa-exclamation-triangle mr-1"></i>Perhatian</h6>
    <ul class="mb-0 small pl-3">
        <li>Data yang sudah diimport awalnya berstatus <strong>Draft</strong></li>
        <li>Karyawan belum bisa melihat slip sampai Anda <strong>Publish</strong></li>
        <li>Review data terlebih dahulu sebelum publish</li>
        <li>NIK harus berawalan <strong>PUS</strong></li>
    </ul>
</div>

</div>
</div>

@push('scripts')
<script>
const fileInput = document.getElementById('excel_file');
const dropzone  = document.getElementById('dropzone');
const fileInfo  = document.getElementById('fileInfo');
const fileName  = document.getElementById('fileName');
const dropTitle = document.getElementById('dropTitle');
const dropSub   = document.getElementById('dropSub');

function setFile(name) {
    dropzone.classList.add('has-file');
    fileInfo.classList.remove('d-none');
    fileName.textContent = '✓ ' + name;
    dropTitle.textContent = 'File terpilih';
    dropSub.classList.add('d-none');
}

fileInput.addEventListener('change', function() {
    if (this.files[0]) setFile(this.files[0].name);
});

dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragging'); });
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragging'));
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('dragging');
    const f = e.dataTransfer.files[0];
    if (f && (f.name.endsWith('.xlsx') || f.name.endsWith('.xls'))) {
        fileInput.files = e.dataTransfer.files;
        setFile(f.name);
    } else {
        alert('Hanya file .xlsx atau .xls yang diterima');
    }
});

document.getElementById('uploadForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>Memproses...';
    document.getElementById('loadingIndicator').classList.remove('d-none');
});
</script>
@endpush
@endsection
