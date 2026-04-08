@extends('admin.layout')
@section('title', 'Input Manual Gaji')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.salary.index') }}">Periode Gaji</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.salary.period', $period->id) }}">{{ $period->period_label }}</a></li>
    <li class="breadcrumb-item active">Input Manual</li>
@endsection

@section('content')
<div class="row">

{{-- ═══ FORM ═══ --}}
<div class="col-lg-8">

<div class="callout callout-info mb-3">
    <h6 class="font-weight-bold mb-1">
        <i class="fas fa-info-circle mr-2"></i>Input Manual untuk Periode: {{ $period->period_label }}
    </h6>
    <p class="mb-0 small text-muted">
        Tambah data gaji karyawan secara manual. BPJS Kesehatan (1%) dan BPJS TK (3%) dihitung otomatis
        dari gaji pokok. Kalkulasi ditampilkan di samping kanan secara real-time.
    </p>
</div>

<form action="{{ route('admin.salary.record.store', $period->id) }}" method="POST">
    @csrf

    {{-- INFO KARYAWAN --}}
    <div class="card card-primary card-outline mb-3">
        <div class="card-header py-2">
            <h6 class="card-title mb-0"><i class="fas fa-user mr-2"></i>Informasi Karyawan</h6>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">NIK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('nik') is-invalid @enderror"
                               name="nik" value="{{ old('nik') }}"
                               placeholder="PUS00001" required style="text-transform:uppercase"
                               oninput="this.value=this.value.toUpperCase()">
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Format: PUS + nomor</small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="font-weight-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('nama') is-invalid @enderror"
                               name="nama" value="{{ old('nama') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">NIK KTP</label>
                        <input type="text" class="form-control form-control-sm"
                               name="nik_ktp" value="{{ old('nik_ktp') }}" maxlength="20">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Departemen</label>
                        <input type="text" class="form-control form-control-sm"
                               name="department" value="{{ old('department') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Jabatan</label>
                        <input type="text" class="form-control form-control-sm"
                               name="jabatan" value="{{ old('jabatan') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Bank</label>
                        <input type="text" class="form-control form-control-sm"
                               name="nama_bank" value="{{ old('nama_bank') }}" placeholder="BNI / BRI / Mandiri">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">No. Rekening</label>
                        <input type="text" class="form-control form-control-sm"
                               name="no_rekening" value="{{ old('no_rekening') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Tanggal Lahir</label>
                        <input type="date" class="form-control form-control-sm"
                               name="tgl_lahir" value="{{ old('tgl_lahir') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">No. KPJ (BPJS TK)</label>
                        <input type="text" class="form-control form-control-sm"
                               name="no_kpj" value="{{ old('no_kpj') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">No. JKN (BPJS Kes)</label>
                        <input type="text" class="form-control form-control-sm"
                               name="no_jkn" value="{{ old('no_jkn') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PENDAPATAN --}}
    <div class="card card-success card-outline mb-3">
        <div class="card-header py-2">
            <h6 class="card-title mb-0"><i class="fas fa-wallet mr-2"></i>Pendapatan</h6>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold small">Gaji Pokok <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control @error('gaji_pokok') is-invalid @enderror"
                                   name="gaji_pokok" id="gajiPokok" value="{{ old('gaji_pokok', 0) }}"
                                   required min="0" oninput="recalc()">
                            @error('gaji_pokok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold small">Gaji Kurangi Potongan</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="gaji_kurangi_potongan"
                                   value="{{ old('gaji_kurangi_potongan', 0) }}" min="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Lembur <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="lembur" id="lembur"
                                   value="{{ old('lembur', 0) }}" required min="0" oninput="recalc()">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Rapel Gaji / Lembur</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="rapel_gaji_lembur" id="rapel"
                                   value="{{ old('rapel_gaji_lembur', 0) }}" min="0" oninput="recalc()">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Kompensasi PKWT</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="kompensasi_pkwt" id="kompensasi"
                                   value="{{ old('kompensasi_pkwt', 0) }}" min="0" oninput="recalc()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POTONGAN --}}
    <div class="card card-danger card-outline mb-3">
        <div class="card-header py-2">
            <h6 class="card-title mb-0"><i class="fas fa-minus-circle mr-2"></i>Potongan Tambahan</h6>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-light border mb-3 py-2">
                <small><i class="fas fa-info-circle mr-1"></i>
                BPJS Kesehatan 1% dan BPJS TK 3% dihitung otomatis dari gaji pokok.</small>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">PPh 21</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="pph21" id="pph21"
                                   value="{{ old('pph21', 0) }}" min="0" oninput="recalc()">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Pinjaman Pribadi</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="pinjaman_pribadi" id="pinjaman"
                                   value="{{ old('pinjaman_pribadi', 0) }}" min="0" oninput="recalc()">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Sumbangan</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="sumbangan" id="sumbangan"
                                   value="{{ old('sumbangan', 0) }}" min="0" oninput="recalc()">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Potongan Absen</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="potongan_absen"
                                   value="{{ old('potongan_absen', 0) }}" min="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Pembuatan Rekening</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="pembuatan_rekening"
                                   value="{{ old('pembuatan_rekening', 0) }}" min="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Meterai</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" class="form-control" name="meterai"
                                   value="{{ old('meterai', 0) }}" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KEHADIRAN --}}
    <div class="card card-info card-outline mb-3">
        <div class="card-header py-2">
            <h6 class="card-title mb-0"><i class="fas fa-calendar-check mr-2"></i>Kehadiran</h6>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach([['hadir','Hadir','success'],['cuti','Cuti','info'],['travel','Travel','primary'],['sakit','Sakit','warning'],['ijin','Ijin','secondary'],['alpa','Alpa','danger'],['off','Off','dark']] as [$field,$label,$color])
                <div class="col-4 col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold small text-{{ $color }}">{{ $label }}</label>
                        <input type="number" class="form-control form-control-sm text-center"
                               name="{{ $field }}" value="{{ old($field, 0) }}" min="0">
                    </div>
                </div>
                @endforeach
                <div class="col-4 col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold small">Masuk Kerja</label>
                        <input type="number" class="form-control form-control-sm text-center"
                               name="masuk_kerja_hari" value="{{ old('masuk_kerja_hari', 0) }}" min="0">
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold small">Lembur (Jam)</label>
                        <input type="number" class="form-control form-control-sm text-center"
                               name="lama_lembur_jam" value="{{ old('lama_lembur_jam', 0) }}" min="0" step="0.5">
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold small">OT (Jam)</label>
                        <input type="number" class="form-control form-control-sm text-center"
                               name="ot_jam" value="{{ old('ot_jam', 0) }}" min="0" step="0.5">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <button type="submit" class="btn btn-success btn-lg mr-2">
            <i class="fas fa-save mr-2"></i>Simpan Data
        </button>
        <a href="{{ route('admin.salary.period', $period->id) }}" class="btn btn-default btn-lg">
            <i class="fas fa-times mr-1"></i>Batal
        </a>
    </div>
</form>
</div>

{{-- ═══ KALKULASI SIDEBAR ═══ --}}
<div class="col-lg-4">
    <div class="card sticky-top" style="top:70px">
        <div class="card-header" style="background:#1a3a5c;color:#fff">
            <h5 class="card-title mb-0"><i class="fas fa-calculator mr-2"></i>Kalkulasi Real-time</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">Gaji Pokok</span>
                    <span id="calcGP" class="font-weight-bold">Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">Tambahan (Lembur+Rapel+Komp)</span>
                    <span id="calcExtra" class="text-success">+ Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2 bg-light">
                    <span class="font-weight-bold small">Total Pendapatan</span>
                    <span id="calcPendapatan" class="font-weight-bold">Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">BPJS Kesehatan (1% gapok)</span>
                    <span id="calcBpjsKes" class="text-danger small">- Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">BPJS TK (3% gapok)</span>
                    <span id="calcBpjsTk" class="text-danger small">- Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">PPh21 + Pinjaman + Lainnya</span>
                    <span id="calcLainnya" class="text-danger small">- Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2 bg-light">
                    <span class="font-weight-bold small">Total Potongan</span>
                    <span id="calcPotongan" class="font-weight-bold text-danger">Rp 0</span>
                </li>
                <li class="list-group-item py-3" style="background:#f0fff4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold text-success">GAJI BERSIH</span>
                        <span id="calcBersih" class="font-weight-bold text-success h5 mb-0">Rp 0</span>
                    </div>
                </li>
            </ul>
            <div class="p-3 border-top">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Lembur per Jam</small>
                    <small class="font-weight-bold" id="lemburPerJam">Rp 0</small>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Periode</small>
                    <small class="font-weight-bold">{{ $period->period_label }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
function fmt(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }
function recalc() {
    const gp   = +document.getElementById('gajiPokok').value  || 0;
    const lem  = +document.getElementById('lembur').value     || 0;
    const rap  = +document.getElementById('rapel').value      || 0;
    const komp = +document.getElementById('kompensasi').value || 0;
    const pph  = +document.getElementById('pph21').value      || 0;
    const pin  = +document.getElementById('pinjaman').value   || 0;
    const sum  = +document.getElementById('sumbangan').value  || 0;

    const totalPend = gp + lem + rap + komp;
    const bpjsKes   = Math.round(gp * 0.01);
    const bpjsTk    = Math.round(gp * 0.03);
    const totalPot  = bpjsKes + bpjsTk + pph + pin + sum;
    const bersih    = totalPend - totalPot;

    document.getElementById('calcGP').textContent         = fmt(gp);
    document.getElementById('calcExtra').textContent      = '+ ' + fmt(lem + rap + komp);
    document.getElementById('calcPendapatan').textContent = fmt(totalPend);
    document.getElementById('calcBpjsKes').textContent    = '- ' + fmt(bpjsKes);
    document.getElementById('calcBpjsTk').textContent     = '- ' + fmt(bpjsTk);
    document.getElementById('calcLainnya').textContent    = '- ' + fmt(pph + pin + sum);
    document.getElementById('calcPotongan').textContent   = fmt(totalPot);
    document.getElementById('calcBersih').textContent     = fmt(bersih);
    document.getElementById('lemburPerJam').textContent   = fmt(gp / 173.333333);
}
recalc();
</script>
@endpush
@endsection
