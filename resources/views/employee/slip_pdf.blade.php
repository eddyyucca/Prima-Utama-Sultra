<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
    .header { background: #1a3a5c; color: #fff; padding: 14px 18px; }
    .header-title { font-size: 14px; font-weight: bold; }
    .header-sub { font-size: 10px; opacity: .85; margin-top: 2px; }
    .header-right { float: right; text-align: right; font-size: 10px; }
    .clearfix::after { content:''; display:table; clear:both; }
    .confidential { background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.4); padding: 2px 8px; font-size: 9px; display: inline-block; margin-top: 4px; }
    .info-section { padding: 10px 18px; border-bottom: 1px solid #eee; }
    .info-grid { width: 100%; }
    .info-grid td { padding: 3px 6px; vertical-align: top; width: 25%; }
    .info-label { font-size: 9px; text-transform: uppercase; color: #888; }
    .info-value { font-weight: bold; font-size: 10px; }
    .body-section { padding: 8px 18px; }
    .columns { width: 100%; }
    .columns td { vertical-align: top; width: 50%; padding: 0 4px; }
    .section-title { background: #f0f4f8; border-left: 3px solid #1a3a5c; padding: 3px 8px; font-weight: bold; font-size: 10px; text-transform: uppercase; margin-bottom: 4px; }
    .section-title-green { border-left-color: #16a34a; }
    .row { display: table; width: 100%; padding: 3px 0; border-bottom: 1px dashed #f0f0f0; }
    .row-label { display: table-cell; color: #555; }
    .row-amount { display: table-cell; text-align: right; font-weight: 600; }
    .row-amount.green { color: #16a34a; }
    .row-amount.red { color: #dc2626; }
    .subtotal { background: #f8f8f8; padding: 4px 6px; }
    .total-bar { background: #1a3a5c; color: #fff; padding: 10px 18px; margin-top: 4px; }
    .total-label { font-size: 11px; }
    .total-amount { font-size: 16px; font-weight: bold; float: right; }
    .terbilang { padding: 8px 18px; font-style: italic; background: #f9f9f9; border-bottom: 1px solid #eee; font-size: 10px; }
    .attendance { padding: 8px 18px; background: #f9f9f9; }
    .att-item { display: inline-block; background: #e2e8f0; padding: 2px 7px; border-radius: 4px; margin: 1px; font-size: 9px; font-weight: bold; }
    .signatures { padding: 14px 18px; }
    .sig-col { display: inline-block; width: 45%; text-align: center; }
    .sig-line { border-top: 1px solid #ccc; margin-top: 40px; padding-top: 4px; font-size: 10px; }
</style>
</head>
<body>
<div class="header clearfix">
    <div class="header-right">
        <div class="confidential">PRIVATE &amp; CONFIDENTIAL</div>
        <div style="margin-top:4px">{{ $record->period->period_start->format('d/m/Y') }} s/d {{ $record->period->period_end->format('d/m/Y') }}</div>
    </div>
    <div class="header-title">PT PRIMA UTAMA SULTRA</div>
    <div class="header-sub">SLIP GAJI LENGKAP TENAGA KERJA &mdash; {{ $record->period->period_label }}</div>
</div>

<div class="info-section">
    <table class="info-grid">
        <tr>
            <td><div class="info-label">Nama</div><div class="info-value">{{ $record->nama }}</div></td>
            <td><div class="info-label">Jabatan</div><div class="info-value">{{ $record->jabatan ?: '-' }}</div></td>
            <td><div class="info-label">NIK</div><div class="info-value">{{ $record->nik }}</div></td>
            <td><div class="info-label">Departemen</div><div class="info-value">{{ $record->department ?: '-' }}</div></td>
        </tr>
        <tr>
            <td><div class="info-label">No. KPJ (BPJS TK)</div><div class="info-value">{{ $record->no_kpj ?: '-' }}</div></td>
            <td><div class="info-label">No. JKN (BPJS Kes)</div><div class="info-value">{{ $record->no_jkn ?: '-' }}</div></td>
            <td><div class="info-label">Bank / No. Rek.</div><div class="info-value">{{ $record->nama_bank }} / {{ $record->no_rekening }}</div></td>
            <td><div class="info-label">Gaji Pokok</div><div class="info-value">Rp {{ number_format($record->gaji_pokok, 0, ',', '.') }}</div></td>
        </tr>
    </table>
</div>

<div class="body-section">
<table class="columns">
<tr>
<td>
    <div class="section-title">A. Pendapatan</div>
    <div class="section-title section-title-green" style="font-size:9px">A.1 Pendapatan Tidak Langsung</div>
    <div class="row"><div class="row-label">BPJS Kesehatan 4% (Perusahaan)</div><div class="row-amount green">{{ number_format($record->gaji_pokok*0.04,0,',','.') }}</div></div>
    <div class="row"><div class="row-label">JHT 3,7%</div><div class="row-amount green">{{ number_format($record->jht_kary,0,',','.') }}</div></div>
    <div class="row"><div class="row-label">JKM 0,3%</div><div class="row-amount green">{{ number_format($record->jkm,0,',','.') }}</div></div>
    <div class="row"><div class="row-label">JKK 0,24%</div><div class="row-amount green">{{ number_format($record->jkk,0,',','.') }}</div></div>
    <div class="row"><div class="row-label">Pensiun 3%</div><div class="row-amount green">{{ number_format($record->pensiun_kary,0,',','.') }}</div></div>
    <div class="row subtotal"><div class="row-label"><b>Total Tdk Langsung</b></div><div class="row-amount green"><b>{{ number_format($record->tot_iuran_kary+($record->gaji_pokok*0.04),0,',','.') }}</b></div></div>

    <div class="section-title" style="margin-top:6px;font-size:9px">A.2 Pendapatan Langsung</div>
    <div class="row"><div class="row-label">Gaji Dibayar ({{ $record->masuk_kerja_hari }} Hari)</div><div class="row-amount">{{ number_format($record->gaji_kurangi_potongan,0,',','.') }}</div></div>
    @if($record->rapel_gaji_lembur > 0)<div class="row"><div class="row-label">Rapel Gaji/Lembur</div><div class="row-amount">{{ number_format($record->rapel_gaji_lembur,0,',','.') }}</div></div>@endif
    @if($record->kompensasi_pkwt > 0)<div class="row"><div class="row-label">Kompensasi PKWT</div><div class="row-amount">{{ number_format($record->kompensasi_pkwt,0,',','.') }}</div></div>@endif
    <div class="row"><div class="row-label">Lembur ({{ $record->lama_lembur_jam }} Jam)</div><div class="row-amount">{{ number_format($record->lembur,0,',','.') }}</div></div>
    <div class="row subtotal"><div class="row-label"><b>Total Pendapatan Langsung</b></div><div class="row-amount"><b>{{ number_format($record->total_pendapatan,0,',','.') }}</b></div></div>
</td>
<td>
    <div class="section-title">B. Potongan</div>
    <div class="row"><div class="row-label">BPJS Kesehatan 1%</div><div class="row-amount red">{{ number_format($record->bpjs_kesehatan_potongan,0,',','.') }}</div></div>
    <div class="row"><div class="row-label">BPJS TK 3%</div><div class="row-amount red">{{ number_format($record->bpjs_tk_potongan,0,',','.') }}</div></div>
    <div class="row"><div class="row-label">PPh 21</div><div class="row-amount red">{{ number_format($record->pph21,0,',','.') }}</div></div>
    @if($record->pinjaman_pribadi > 0)<div class="row"><div class="row-label">Pinjaman Pribadi</div><div class="row-amount red">{{ number_format($record->pinjaman_pribadi,0,',','.') }}</div></div>@endif
    @if($record->sumbangan > 0)<div class="row"><div class="row-label">Sumbangan</div><div class="row-amount red">{{ number_format($record->sumbangan,0,',','.') }}</div></div>@endif
    <div class="row subtotal"><div class="row-label"><b>Total Potongan</b></div><div class="row-amount red"><b>{{ number_format($record->total_potongan,0,',','.') }}</b></div></div>

    <div class="section-title" style="margin-top:8px">Kehadiran</div>
    <div style="padding:3px 0">
        <span class="att-item">Hadir: {{ $record->hadir }}</span>
        <span class="att-item">Cuti: {{ $record->cuti }}</span>
        <span class="att-item">Travel: {{ $record->travel }}</span>
        <span class="att-item">Sakit: {{ $record->sakit }}</span>
        <span class="att-item">Ijin: {{ $record->ijin }}</span>
        <span class="att-item">Alpa: {{ $record->alpa }}</span>
        <span class="att-item">Off: {{ $record->off }}</span>
    </div>
    <div class="row"><div class="row-label">Total HK</div><div class="row-amount">{{ $record->total_hk }} Hari</div></div>
    <div class="row"><div class="row-label">Lembur</div><div class="row-amount">{{ $record->lama_lembur_jam }} Jam</div></div>
    <div class="row"><div class="row-label">Lembur/Jam</div><div class="row-amount">Rp {{ number_format($lemburPerJam,0,',','.') }}</div></div>
</td>
</tr>
</table>
</div>

<div class="total-bar clearfix">
    <span class="total-label">C. TOTAL DITERIMA (A.2 - B)</span>
    <span class="total-amount">Rp {{ number_format($record->total_ditransfer,0,',','.') }}</span>
</div>

<div class="terbilang">{{ terbilang($record->total_ditransfer) }} Rupiah</div>

<div class="signatures">
    <div class="sig-col">
        <div style="font-size:10px">Kendari, {{ now()->isoFormat('D MMMM Y') }}</div>
        <div style="font-size:10px;color:#888">Disiapkan Oleh,</div>
        <div class="sig-line">Satya Ananda Prema<br>Admin HR PUS</div>
    </div>
    <div class="sig-col" style="margin-left:8%">
        <div style="font-size:10px">&nbsp;</div>
        <div style="font-size:10px;color:#888">Diterima Oleh,</div>
        <div class="sig-line">{{ $record->nama }}</div>
    </div>
</div>
</body>
</html>
