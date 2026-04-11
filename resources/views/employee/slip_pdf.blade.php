<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
@page { margin: 8mm 10mm; size: A4 portrait; }
body { font-family: Arial, sans-serif; font-size: 9px; color: #222; line-height: 1.3; }

/* HEADER */
.header { background: #1a3a5c; color: #fff; padding: 8px 12px; }
.header-left { float: left; }
.header-right { float: right; text-align: right; }
.header-title { font-size: 12px; font-weight: bold; letter-spacing: .3px; }
.header-sub { font-size: 8px; opacity: .85; margin-top: 1px; }
.confidential { display: inline-block; border: 1px solid rgba(255,255,255,.5); padding: 1px 6px; font-size: 7.5px; border-radius: 2px; margin-bottom: 2px; }
.clearfix::after { content:''; display:table; clear:both; }

/* INFO SECTION */
.info-section { padding: 6px 12px; border-bottom: 1px solid #e0e0e0; background: #fafafa; }
.info-table { width: 100%; border-collapse: collapse; }
.info-table td { padding: 2px 4px; vertical-align: top; width: 25%; }
.lbl { font-size: 7.5px; color: #888; text-transform: uppercase; letter-spacing: .3px; }
.val { font-weight: bold; font-size: 8.5px; color: #111; }

/* BODY */
.body-section { padding: 6px 12px; }
.cols-table { width: 100%; border-collapse: collapse; }
.cols-table > tbody > tr > td { vertical-align: top; width: 50%; padding: 0 5px 0 0; }
.cols-table > tbody > tr > td:last-child { padding: 0 0 0 5px; border-left: 1px solid #eee; }

/* SECTION TITLES */
.sec-title {
    background: #eef2f7;
    border-left: 3px solid #1a3a5c;
    padding: 2px 6px;
    font-weight: bold;
    font-size: 7.5px;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 3px;
}
.sec-title.green { border-left-color: #16a34a; }
.sec-title.red   { border-left-color: #dc2626; }
.sec-title.grey  { border-left-color: #6b7280; }

/* ROWS */
.row-item { display: table; width: 100%; padding: 1.5px 2px; border-bottom: 1px dashed #f0f0f0; }
.row-label { display: table-cell; color: #444; font-size: 8px; }
.row-amount { display: table-cell; text-align: right; font-weight: 600; font-size: 8px; white-space: nowrap; }
.row-amount.g { color: #16a34a; }
.row-amount.r { color: #dc2626; }
.subtotal-row { background: #f5f5f5; display: table; width: 100%; padding: 2px 4px; margin-top: 1px; }
.subtotal-row .row-label,
.subtotal-row .row-amount { font-weight: bold; font-size: 8px; }

/* ATTENDANCE */
.att-section { padding: 4px 12px; background: #f9f9f9; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
.att-item { display: inline-block; background: #e2e8f0; padding: 1px 5px; border-radius: 3px; margin: 1px 1px 1px 0; font-size: 7.5px; font-weight: bold; }
.att-item.red  { background: #fee2e2; color: #dc2626; }
.att-item.blue { background: #dbeafe; color: #1d4ed8; }

/* TOTAL BAR */
.total-bar { background: #1a3a5c; color: #fff; padding: 7px 12px; }
.total-bar-label { font-size: 9px; font-weight: bold; float: left; margin-top: 2px; }
.total-bar-amount { font-size: 15px; font-weight: bold; float: right; }

/* TERBILANG */
.terbilang { padding: 4px 12px; font-style: italic; color: #444; font-size: 8px; background: #fffbf0; border-bottom: 1px solid #eee; }

/* SIGNATURES */
.sig-section { padding: 8px 12px 4px; }
.sig-table { width: 100%; border-collapse: collapse; }
.sig-table td { width: 33.33%; text-align: center; padding: 0 8px; vertical-align: bottom; }
.sig-line { border-top: 1px solid #999; margin-top: 30px; padding-top: 3px; font-size: 8px; font-weight: bold; }
.sig-sub { font-size: 7.5px; color: #888; margin-bottom: 2px; }
.sig-date { font-size: 7.5px; color: #666; margin-bottom: 1px; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header clearfix">
    <div class="header-right">
        <div class="confidential">PRIVATE &amp; CONFIDENTIAL</div>
        <div style="font-size:8px;margin-top:2px">
            {{ $record->period->period_start->format('d/m/Y') }} s/d {{ $record->period->period_end->format('d/m/Y') }}
        </div>
    </div>
    <div class="header-left">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" alt="PUS Logo"
                 style="height:32px;width:auto;filter:brightness(0) invert(1);margin-bottom:2px;display:block">
        @else
            <div class="header-title">PT PRIMA UTAMA SULTRA</div>
        @endif
        <div class="header-sub">SLIP GAJI &mdash; {{ strtoupper($record->period->period_label) }}</div>
    </div>
</div>

{{-- INFO KARYAWAN --}}
<div class="info-section">
    <table class="info-table">
        <tr>
            <td>
                <div class="lbl">Nama</div>
                <div class="val">{{ $record->nama }}</div>
            </td>
            <td>
                <div class="lbl">Jabatan</div>
                <div class="val">{{ $record->jabatan ?: '-' }}</div>
            </td>
            <td>
                <div class="lbl">NIK / NIK KTP</div>
                <div class="val">{{ $record->nik }} / {{ $record->nik_ktp ?: '-' }}</div>
            </td>
            <td>
                <div class="lbl">Departemen</div>
                <div class="val">{{ $record->department ?: '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="lbl">No. KPJ (BPJS TK)</div>
                <div class="val">{{ $record->no_kpj ?: '-' }}</div>
            </td>
            <td>
                <div class="lbl">No. JKN (BPJS Kes)</div>
                <div class="val">{{ $record->no_jkn ?: '-' }}</div>
            </td>
            <td>
                <div class="lbl">Bank / No. Rekening</div>
                <div class="val">{{ $record->nama_bank ?: '-' }} / {{ $record->no_rekening ?: '-' }}</div>
            </td>
            <td>
                <div class="lbl">Gaji Pokok</div>
                <div class="val">Rp {{ number_format($record->gaji_pokok, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- BODY: 2 KOLOM --}}
<div class="body-section">
<table class="cols-table">
<tr>
{{-- KOLOM KIRI: PENDAPATAN --}}
<td>
    <div class="sec-title">A. Pendapatan</div>

    <div class="sec-title green" style="margin-top:3px">A.1 Tidak Langsung (Tanggungan Perusahaan)</div>
    <div class="row-item">
        <div class="row-label">BPJS Kesehatan 4% (Perusahaan)</div>
        <div class="row-amount g">Rp {{ number_format($record->gaji_pokok * 0.04, 0, ',', '.') }}</div>
    </div>
    <div class="row-item">
        <div class="row-label">JHT 3,7%</div>
        <div class="row-amount g">Rp {{ number_format($record->jht_kary, 0, ',', '.') }}</div>
    </div>
    <div class="row-item">
        <div class="row-label">JKM 0,3%</div>
        <div class="row-amount g">Rp {{ number_format($record->jkm, 0, ',', '.') }}</div>
    </div>
    <div class="row-item">
        <div class="row-label">JKK 0,24%</div>
        <div class="row-amount g">Rp {{ number_format($record->jkk, 0, ',', '.') }}</div>
    </div>
    <div class="row-item">
        <div class="row-label">Pensiun 3%</div>
        <div class="row-amount g">Rp {{ number_format($record->pensiun_kary, 0, ',', '.') }}</div>
    </div>
    <div class="subtotal-row">
        <div class="row-label">Subtotal Tdk Langsung</div>
        <div class="row-amount g">Rp {{ number_format($record->tot_iuran_kary + ($record->gaji_pokok * 0.04), 0, ',', '.') }}</div>
    </div>

    <div class="sec-title" style="margin-top:4px">A.2 Langsung (Diterima Karyawan)</div>
    <div class="row-item">
        <div class="row-label">Gaji Dibayar ({{ $record->masuk_kerja_hari }} hari)</div>
        <div class="row-amount">Rp {{ number_format($record->gaji_kurangi_potongan, 0, ',', '.') }}</div>
    </div>
    @if($record->rapel_gaji_lembur > 0)
    <div class="row-item">
        <div class="row-label">Rapel Gaji / Lembur</div>
        <div class="row-amount">Rp {{ number_format($record->rapel_gaji_lembur, 0, ',', '.') }}</div>
    </div>
    @endif
    @if($record->kompensasi_pkwt > 0)
    <div class="row-item">
        <div class="row-label">Kompensasi PKWT</div>
        <div class="row-amount">Rp {{ number_format($record->kompensasi_pkwt, 0, ',', '.') }}</div>
    </div>
    @endif
    <div class="row-item">
        <div class="row-label">Lembur ({{ $record->lama_lembur_jam }} jam)</div>
        <div class="row-amount">Rp {{ number_format($record->lembur, 0, ',', '.') }}</div>
    </div>
    <div class="subtotal-row">
        <div class="row-label">Total Pendapatan Langsung</div>
        <div class="row-amount">Rp {{ number_format($record->total_pendapatan, 0, ',', '.') }}</div>
    </div>
</td>

{{-- KOLOM KANAN: POTONGAN + KEHADIRAN --}}
<td>
    <div class="sec-title red">B. Potongan</div>
    <div class="row-item">
        <div class="row-label">BPJS Kesehatan 1% (Karyawan)</div>
        <div class="row-amount r">Rp {{ number_format($record->bpjs_kesehatan_potongan, 0, ',', '.') }}</div>
    </div>
    <div class="row-item">
        <div class="row-label">BPJS TK 3% (Karyawan)</div>
        <div class="row-amount r">Rp {{ number_format($record->bpjs_tk_potongan, 0, ',', '.') }}</div>
    </div>
    <div class="row-item">
        <div class="row-label">PPh 21</div>
        <div class="row-amount r">Rp {{ number_format($record->pph21, 0, ',', '.') }}</div>
    </div>
    @if($record->pinjaman_pribadi > 0)
    <div class="row-item">
        <div class="row-label">Pinjaman Pribadi</div>
        <div class="row-amount r">Rp {{ number_format($record->pinjaman_pribadi, 0, ',', '.') }}</div>
    </div>
    @endif
    @if($record->sumbangan > 0)
    <div class="row-item">
        <div class="row-label">Sumbangan</div>
        <div class="row-amount r">Rp {{ number_format($record->sumbangan, 0, ',', '.') }}</div>
    </div>
    @endif
    @if($record->pembuatan_rekening > 0)
    <div class="row-item">
        <div class="row-label">Pembuatan Rekening</div>
        <div class="row-amount r">Rp {{ number_format($record->pembuatan_rekening, 0, ',', '.') }}</div>
    </div>
    @endif
    @if($record->meterai > 0)
    <div class="row-item">
        <div class="row-label">Meterai</div>
        <div class="row-amount r">Rp {{ number_format($record->meterai, 0, ',', '.') }}</div>
    </div>
    @endif
    <div class="subtotal-row">
        <div class="row-label">Total Potongan</div>
        <div class="row-amount r">Rp {{ number_format($record->total_potongan, 0, ',', '.') }}</div>
    </div>

    {{-- KEHADIRAN --}}
    <div class="sec-title grey" style="margin-top:5px">Kehadiran &amp; Lembur</div>
    <div style="margin-bottom:3px">
        <span class="att-item">Hadir: {{ $record->hadir }}</span>
        <span class="att-item blue">Cuti: {{ $record->cuti }}</span>
        <span class="att-item blue">Travel: {{ $record->travel }}</span>
        <span class="att-item">Sakit: {{ $record->sakit }}</span>
        <span class="att-item">Ijin: {{ $record->ijin }}</span>
        <span class="att-item red">Alpa: {{ $record->alpa }}</span>
        <span class="att-item">Off: {{ $record->off }}</span>
    </div>
    <div class="row-item">
        <div class="row-label">Total Hari Kerja</div>
        <div class="row-amount">{{ $record->total_hk }} hari</div>
    </div>
    <div class="row-item">
        <div class="row-label">Durasi Lembur</div>
        <div class="row-amount">{{ $record->lama_lembur_jam }} jam</div>
    </div>
    <div class="row-item">
        <div class="row-label">Tarif Lembur / Jam</div>
        <div class="row-amount">Rp {{ number_format($lemburPerJam, 0, ',', '.') }}</div>
    </div>
</td>
</tr>
</table>
</div>

{{-- TOTAL BAR --}}
<div class="total-bar clearfix">
    <span class="total-bar-label">C. TOTAL DITERIMA &nbsp;(A.2 &minus; B)</span>
    <span class="total-bar-amount">Rp {{ number_format($record->total_ditransfer, 0, ',', '.') }}</span>
</div>

{{-- TERBILANG --}}
<div class="terbilang clearfix">
    <span style="font-weight:bold;font-style:normal;color:#1a3a5c">Terbilang: </span>
    {{ ucfirst(terbilang($record->total_ditransfer)) }} Rupiah
</div>

{{-- TANDA TANGAN --}}
<div class="sig-section">
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-date">Kendari, {{ now()->isoFormat('D MMMM Y') }}</div>
                <div class="sig-sub">Disiapkan Oleh,</div>
                <div class="sig-line">
                    Satya Ananda Prema<br>
                    <span style="font-weight:normal;font-size:7.5px">Admin HR &ndash; PT Prima Utama Sultra</span>
                </div>
            </td>
            <td>
                <div class="sig-date">&nbsp;</div>
                <div class="sig-sub">Mengetahui,</div>
                <div class="sig-line">
                    HRD Manager<br>
                    <span style="font-weight:normal;font-size:7.5px">PT Prima Utama Sultra</span>
                </div>
            </td>
            <td>
                <div class="sig-date">&nbsp;</div>
                <div class="sig-sub">Diterima Oleh,</div>
                <div class="sig-line">
                    {{ $record->nama }}<br>
                    <span style="font-weight:normal;font-size:7.5px">{{ $record->jabatan ?: 'Karyawan' }}</span>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
