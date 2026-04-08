@extends('admin.layout')
@section('title', 'Edit Data Gaji')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.salary.index') }}">Periode Gaji</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.salary.period', $record->period_id) }}">{{ $record->period->period_label }}</a></li>
    <li class="breadcrumb-item active">Edit: {{ $record->nama }}</li>
@endsection

@section('content')
<div class="row">

{{-- ═══ FORM CARD ═══ --}}
<div class="col-lg-8">
<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 class="card-title mb-0 font-weight-bold">
            <i class="fas fa-edit mr-2"></i>Edit Data Gaji – {{ $record->nama }}
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.salary.record.update', $record->id) }}" method="POST">
            @csrf @method('PUT')

            {{-- INFO KARYAWAN --}}
            <div class="card card-secondary card-outline mb-3">
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
                                <label class="font-weight-bold small">NIK</label>
                                <input type="text" class="form-control form-control-sm bg-light"
                                       value="{{ $record->nik }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="font-weight-bold small">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('nama') is-invalid @enderror"
                                       name="nama" value="{{ old('nama', $record->nama) }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Departemen</label>
                                <input type="text" class="form-control form-control-sm"
                                       name="department" value="{{ old('department', $record->department) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Jabatan</label>
                                <input type="text" class="form-control form-control-sm"
                                       name="jabatan" value="{{ old('jabatan', $record->jabatan) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Bank</label>
                                <input type="text" class="form-control form-control-sm"
                                       name="nama_bank" value="{{ old('nama_bank', $record->nama_bank) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">No. Rekening</label>
                                <input type="text" class="form-control form-control-sm"
                                       name="no_rekening" value="{{ old('no_rekening', $record->no_rekening) }}">
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
                                    <input type="number" class="form-control" name="gaji_pokok" id="gajiPokok"
                                           value="{{ old('gaji_pokok', $record->gaji_pokok) }}" required min="0" oninput="recalc()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Gaji Kurangi Potongan</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="gaji_kurangi_potongan"
                                           value="{{ old('gaji_kurangi_potongan', $record->gaji_kurangi_potongan) }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Lembur <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="lembur" id="lembur"
                                           value="{{ old('lembur', $record->lembur) }}" required min="0" oninput="recalc()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Rapel Gaji / Lembur</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="rapel_gaji_lembur" id="rapel"
                                           value="{{ old('rapel_gaji_lembur', $record->rapel_gaji_lembur) }}" min="0" oninput="recalc()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Kompensasi PKWT</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="kompensasi_pkwt" id="kompensasi"
                                           value="{{ old('kompensasi_pkwt', $record->kompensasi_pkwt) }}" min="0" oninput="recalc()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- POTONGAN --}}
            <div class="card card-danger card-outline mb-3">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0"><i class="fas fa-minus-circle mr-2"></i>Potongan</h6>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">PPh 21</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="pph21" id="pph21"
                                           value="{{ old('pph21', $record->pph21) }}" min="0" oninput="recalc()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Pinjaman Pribadi</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="pinjaman_pribadi" id="pinjaman"
                                           value="{{ old('pinjaman_pribadi', $record->pinjaman_pribadi) }}" min="0" oninput="recalc()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Sumbangan</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="sumbangan" id="sumbangan"
                                           value="{{ old('sumbangan', $record->sumbangan) }}" min="0" oninput="recalc()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Potongan Absen</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" class="form-control" name="potongan_absen"
                                           value="{{ old('potongan_absen', $record->potongan_absen) }}" min="0">
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
                                       name="{{ $field }}" value="{{ old($field, $record->$field) }}" min="0">
                            </div>
                        </div>
                        @endforeach
                        <div class="col-4 col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold small">Masuk Kerja</label>
                                <input type="number" class="form-control form-control-sm text-center"
                                       name="masuk_kerja_hari" value="{{ old('masuk_kerja_hari', $record->masuk_kerja_hari) }}" min="0">
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold small">Lembur (Jam)</label>
                                <input type="number" class="form-control form-control-sm text-center"
                                       name="lama_lembur_jam" value="{{ old('lama_lembur_jam', $record->lama_lembur_jam) }}" min="0" step="0.5">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.salary.period', $record->period_id) }}" class="btn btn-default">
                    <i class="fas fa-times mr-1"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
</div>

{{-- ═══ KALKULASI SIDEBAR ═══ --}}
<div class="col-lg-4">
    <div class="card card-widget widget-user-2 sticky-top" style="top:70px">
        <div class="card-header" style="background:#1a3a5c;color:#fff">
            <h5 class="card-title mb-0"><i class="fas fa-calculator mr-2"></i>Kalkulasi Otomatis</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">Gaji Pokok</span>
                    <span id="calcGP" class="font-weight-bold">Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">Lembur + Rapel + Komp.</span>
                    <span id="calcExtra" class="text-success">+ Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2 bg-light">
                    <span class="font-weight-bold small">Total Pendapatan</span>
                    <span id="calcPendapatan" class="font-weight-bold">Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">BPJS Kesehatan (1%)</span>
                    <span id="calcBpjsKes" class="text-danger">- Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">BPJS TK (3%)</span>
                    <span id="calcBpjsTk" class="text-danger">- Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2">
                    <span class="text-muted small">PPh21 + Pinjaman + Lainnya</span>
                    <span id="calcLainnya" class="text-danger">- Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-2 bg-light">
                    <span class="font-weight-bold small">Total Potongan</span>
                    <span id="calcPotongan" class="font-weight-bold text-danger">Rp 0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between py-3" style="background:#f0fff4">
                    <span class="font-weight-bold text-success">GAJI BERSIH</span>
                    <span id="calcBersih" class="font-weight-bold text-success h5 mb-0">Rp 0</span>
                </li>
            </ul>
            <div class="p-3 border-top">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Lembur per Jam</small>
                    <small class="font-weight-bold" id="lemburPerJam">Rp 0</small>
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
    const gp    = +document.getElementById('gajiPokok').value  || 0;
    const lem   = +document.getElementById('lembur').value     || 0;
    const rap   = +document.getElementById('rapel').value      || 0;
    const komp  = +document.getElementById('kompensasi').value || 0;
    const pph   = +document.getElementById('pph21').value      || 0;
    const pin   = +document.getElementById('pinjaman').value   || 0;
    const sum   = +document.getElementById('sumbangan').value  || 0;

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
