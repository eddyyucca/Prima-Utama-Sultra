<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $record->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .slip-wrapper { max-width: 780px; margin: 0 auto; padding: 1.5rem; }
        .slip-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .slip-header { background: linear-gradient(135deg, #1a3a5c, #2563a8); color: #fff; padding: 2rem; }
        .slip-header .company { font-size: 1.1rem; font-weight: 700; }
        .slip-header .subtitle { opacity: .8; font-size: .85rem; }
        .confidential { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3); border-radius: 6px; padding: .3rem .8rem; font-size: .75rem; display: inline-block; }
        .info-section { padding: 1.5rem; border-bottom: 1px solid #f0f2f5; }
        .info-label { font-size: .78rem; text-transform: uppercase; letter-spacing: .5px; color: #6c757d; margin-bottom: 2px; }
        .info-value { font-weight: 600; }
        .salary-section { padding: 1.5rem; }
        .salary-row { display: flex; justify-content: space-between; align-items: center; padding: .5rem 0; border-bottom: 1px dashed #f0f2f5; }
        .salary-row:last-child { border-bottom: none; }
        .salary-label { color: #495057; }
        .salary-amount { font-weight: 600; }
        .section-header { background: #f8f9fa; padding: .5rem 1rem; font-weight: 700; font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; color: #495057; border-left: 4px solid #1a3a5c; margin: 0 1.5rem .5rem; border-radius: 0 4px 4px 0; }
        .total-row { background: #1a3a5c; color: #fff; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .terbilang { background: #f8f9fa; padding: 1rem 1.5rem; font-style: italic; font-size: .9rem; border-bottom: 1px solid #f0f2f5; }
        .attendance-section { padding: 1rem 1.5rem; background: #f8f9fa; }
        .att-badge { display: inline-block; padding: .3rem .8rem; border-radius: 6px; font-size: .8rem; font-weight: 600; margin: .2rem; }
        .signature-section { padding: 1.5rem; display: flex; justify-content: space-between; }
        .sig-box { text-align: center; width: 45%; }
        .sig-line { border-top: 1px solid #dee2e6; margin-top: 4rem; padding-top: .5rem; font-size: .85rem; }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .slip-wrapper { padding: 0; max-width: 100%; }
            .slip-card { box-shadow: none; border-radius: 0; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#1a3a5c;padding:.75rem 1.5rem;color:#fff;display:flex;justify-content:space-between;align-items:center">
    <div>
        <a href="{{ route('employee.portal') }}" class="btn btn-sm btn-outline-light me-2">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
        <span class="opacity-75">Slip Gaji - {{ $record->period->period_label }}</span>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-sm btn-warning me-2">
            <i class="fas fa-print me-1"></i>Cetak
        </button>
        <a href="{{ route('employee.slip.download', $record->id) }}" class="btn btn-sm btn-success">
            <i class="fas fa-download me-1"></i>Download PDF
        </a>
    </div>
</div>

<div class="slip-wrapper mt-3">
<div class="slip-card">

    {{-- Header --}}
    <div class="slip-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="company">PT PRIMA UTAMA SULTRA</div>
                <div class="subtitle">SLIP GAJI LENGKAP TENAGA KERJA</div>
                <div class="subtitle mt-1">{{ $record->period->period_label }}</div>
            </div>
            <div class="text-end">
                <div class="confidential">PRIVATE & CONFIDENTIAL</div>
                <div class="subtitle mt-2">{{ $record->period->period_start->format('d/m/Y') }} s/d {{ $record->period->period_end->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Info Karyawan --}}
    <div class="info-section">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="info-label">Nama</div>
                <div class="info-value">{{ $record->nama }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-label">Jabatan</div>
                <div class="info-value">{{ $record->jabatan ?: '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-label">NIK</div>
                <div class="info-value">{{ $record->nik }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-label">Departemen</div>
                <div class="info-value" style="font-size:.85rem">{{ $record->department ?: '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-label">No. BPJS TK (KPJ)</div>
                <div class="info-value" style="font-size:.85rem">{{ $record->no_kpj ?: '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-label">No. BPJS Kes (JKN)</div>
                <div class="info-value" style="font-size:.85rem">{{ $record->no_jkn ?: '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-label">Periode PKWT</div>
                <div class="info-value" style="font-size:.82rem">
                    {{ $record->tanggal_masuk_kontrak ? $record->tanggal_masuk_kontrak->format('d/m/Y') : '-' }}
                    s/d
                    {{ $record->tanggal_akhir_kontrak ? $record->tanggal_akhir_kontrak->format('d/m/Y') : '-' }}
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-label">Bank / No. Rekening</div>
                <div class="info-value" style="font-size:.82rem">{{ $record->nama_bank }} / {{ $record->no_rekening }}</div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="info-label">Gaji dikirim via</div>
                <div class="info-value">{{ $record->nama_bank ?: '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Periode Gaji</div>
                <div class="info-value">{{ $record->period->period_start->format('d/m/Y') }} s/d {{ $record->period->period_end->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Gaji Pokok</div>
                <div class="info-value">Rp {{ number_format($record->gaji_pokok, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Rincian --}}
    <div class="salary-section">
        <div class="row">
            <div class="col-md-6">
                {{-- Pendapatan Tidak Langsung --}}
                <div class="section-header mb-2">A. Pendapatan</div>
                <div style="padding: 0 .5rem">
                    <div class="section-header" style="font-size:.75rem;border-left-color:#16a34a">A.1 Pendapatan Tidak Langsung</div>
                    <div class="salary-row">
                        <span class="salary-label">BPJS Kesehatan 4% (Perusahaan)</span>
                        <span class="salary-amount text-success">{{ number_format($record->gaji_pokok * 0.04, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">JHT 3,7% (Perusahaan)</span>
                        <span class="salary-amount text-success">{{ number_format($record->jht_kary, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">JKM 0,3% (Perusahaan)</span>
                        <span class="salary-amount text-success">{{ number_format($record->jkm, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">JKK 0,24% (Perusahaan)</span>
                        <span class="salary-amount text-success">{{ number_format($record->jkk, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">Pensiun 3% (Perusahaan)</span>
                        <span class="salary-amount text-success">{{ number_format($record->pensiun_kary, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row" style="background:#f0f9f0;margin:0 -.5rem;padding:.5rem">
                        <span class="salary-label fw-bold">Total Pendapatan Tidak Langsung</span>
                        <span class="salary-amount text-success fw-bold">
                            Rp {{ number_format($record->tot_iuran_kary + ($record->gaji_pokok * 0.04), 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="section-header mt-3 mb-2" style="font-size:.75rem;border-left-color:#2563a8">A.2 Pendapatan Langsung</div>
                    <div class="salary-row">
                        <span class="salary-label">Gaji Dibayar ({{ $record->masuk_kerja_hari }} Hari)</span>
                        <span class="salary-amount">Rp {{ number_format($record->gaji_kurangi_potongan, 0, ',', '.') }}</span>
                    </div>
                    @if($record->rapel_gaji_lembur > 0)
                    <div class="salary-row">
                        <span class="salary-label">Rapel Gaji/Lembur</span>
                        <span class="salary-amount">Rp {{ number_format($record->rapel_gaji_lembur, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($record->kompensasi_pkwt > 0)
                    <div class="salary-row">
                        <span class="salary-label">Kompensasi PKWT</span>
                        <span class="salary-amount">Rp {{ number_format($record->kompensasi_pkwt, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="salary-row">
                        <span class="salary-label">Lembur ({{ $record->lama_lembur_jam }} Jam)</span>
                        <span class="salary-amount">Rp {{ number_format($record->lembur, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row" style="background:#e8f4fd;margin:0 -.5rem;padding:.5rem">
                        <span class="salary-label fw-bold">Total Pendapatan Langsung</span>
                        <span class="salary-amount fw-bold">Rp {{ number_format($record->total_pendapatan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                {{-- Potongan --}}
                <div class="section-header mb-2">B. Potongan</div>
                <div style="padding: 0 .5rem">
                    <div class="salary-row">
                        <span class="salary-label">BPJS Kesehatan 1% (Karyawan)</span>
                        <span class="salary-amount text-danger">Rp {{ number_format($record->bpjs_kesehatan_potongan, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">BPJS TK 3% (Karyawan)</span>
                        <span class="salary-amount text-danger">Rp {{ number_format($record->bpjs_tk_potongan, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">PPh 21</span>
                        <span class="salary-amount text-danger">Rp {{ number_format($record->pph21, 0, ',', '.') }}</span>
                    </div>
                    @if($record->pembuatan_rekening > 0)
                    <div class="salary-row">
                        <span class="salary-label">Pembuatan Rekening</span>
                        <span class="salary-amount text-danger">Rp {{ number_format($record->pembuatan_rekening, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($record->meterai > 0)
                    <div class="salary-row">
                        <span class="salary-label">Meterai</span>
                        <span class="salary-amount text-danger">Rp {{ number_format($record->meterai, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($record->pinjaman_pribadi > 0)
                    <div class="salary-row">
                        <span class="salary-label">Pinjaman Pribadi</span>
                        <span class="salary-amount text-danger">Rp {{ number_format($record->pinjaman_pribadi, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($record->sumbangan > 0)
                    <div class="salary-row">
                        <span class="salary-label">Sumbangan</span>
                        <span class="salary-amount text-danger">Rp {{ number_format($record->sumbangan, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="salary-row" style="background:#fef0f0;margin:0 -.5rem;padding:.5rem">
                        <span class="salary-label fw-bold">Total Potongan</span>
                        <span class="salary-amount text-danger fw-bold">Rp {{ number_format($record->total_potongan, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Kehadiran --}}
                <div class="section-header mt-3 mb-2">Kehadiran</div>
                <div style="padding: 0 .5rem">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="att-badge bg-success text-white">Hadir: {{ $record->hadir }}</span>
                        <span class="att-badge bg-primary text-white">Cuti: {{ $record->cuti }}</span>
                        <span class="att-badge bg-info text-white">Travel: {{ $record->travel }}</span>
                        <span class="att-badge bg-warning text-dark">Sakit: {{ $record->sakit }}</span>
                        <span class="att-badge bg-secondary text-white">Ijin: {{ $record->ijin }}</span>
                        <span class="att-badge bg-danger text-white">Alpa: {{ $record->alpa }}</span>
                        <span class="att-badge bg-dark text-white">Off: {{ $record->off }}</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">Total HK</span>
                        <span>{{ $record->total_hk }} Hari</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">Lama Lembur</span>
                        <span>{{ $record->lama_lembur_jam }} Jam</span>
                    </div>
                    <div class="salary-row">
                        <span class="salary-label">Lembur per Jam</span>
                        <span>Rp {{ number_format($lemburPerJam, 0, ',', '.') }}</span>
                    </div>
                    @if($record->potongan_absen > 0)
                    <div class="salary-row">
                        <span class="salary-label text-danger">Potongan Absen</span>
                        <span class="text-danger">Rp {{ number_format($record->potongan_absen, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Total Diterima --}}
    <div class="total-row">
        <div>
            <div style="font-size:.8rem;opacity:.8">C. TOTAL DITERIMA (A.2 - B)</div>
            <div style="font-size:.85rem;opacity:.7">Transfer ke {{ $record->nama_bank }}</div>
        </div>
        <div class="text-end">
            <div style="font-size:1.6rem;font-weight:800">Rp {{ number_format($record->total_ditransfer, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Terbilang --}}
    <div class="terbilang">
        <i class="fas fa-quote-left me-2 text-muted"></i>
        {{ terbilang($record->total_ditransfer) }} Rupiah
        <i class="fas fa-quote-right ms-2 text-muted"></i>
    </div>

    {{-- Signature --}}
    <div class="signature-section">
        <div class="sig-box">
            <div style="font-size:.85rem">Kendari, {{ now()->isoFormat('D MMMM Y') }}</div>
            <div style="font-size:.85rem" class="text-muted">Disiapkan Oleh,</div>
            <div class="sig-line">Admin HR PUS</div>
        </div>
        <div class="sig-box">
            <div style="font-size:.85rem">&nbsp;</div>
            <div style="font-size:.85rem" class="text-muted">Diterima Oleh,</div>
            <div class="sig-line">{{ $record->nama }}</div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
