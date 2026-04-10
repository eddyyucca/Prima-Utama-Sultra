<?php

namespace App\Http\Controllers;

use App\Models\SalaryPeriod;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalaryController extends Controller
{
    // ═══════════════════════════════════════════════════
    //  PERIOD LIST
    // ═══════════════════════════════════════════════════
    public function index()
    {
        $periods = SalaryPeriod::withCount('records')
            ->withSum('records', 'total_ditransfer')
            ->orderByDesc('period_start')
            ->paginate(10);

        return view('admin.salary.index', compact('periods'));
    }

    // ═══════════════════════════════════════════════════
    //  UPLOAD FORM + IMPORT
    // ═══════════════════════════════════════════════════
    public function showUploadForm()
    {
        return view('admin.salary.upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file'   => 'required|file|mimes:xlsx,xls|max:20480',
            'period_label' => 'required|string|max:50',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after:period_start',
            'working_days' => 'required|integer|min:1|max:31',
        ]);

        $file     = $request->file('excel_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/salary'), $filename);

        try {
            $spreadsheet = IOFactory::load(public_path('uploads/salary/' . $filename));

            // Try to find the DFT GJ sheet (with or without spaces)
            $sheet = null;
            foreach ($spreadsheet->getAllSheets() as $s) {
                if (str_contains(strtolower(trim($s->getTitle())), 'dft gj')) {
                    $sheet = $s;
                    break;
                }
            }
            // fallback: last sheet in file (old templates put DFT GJ at index 1)
            if (!$sheet) {
                $sheetCount = $spreadsheet->getSheetCount();
                $sheet = $spreadsheet->getSheet($sheetCount > 1 ? 1 : 0);
            }

            $period = SalaryPeriod::create([
                'period_label'  => $request->period_label,
                'period_start'  => $request->period_start,
                'period_end'    => $request->period_end,
                'working_days'  => $request->working_days,
                'uploaded_file' => $filename,
                'status'        => 'draft',
                'uploaded_by'   => Auth::id(),
            ]);

            $this->importRecords($sheet, $period->id);

            // Redirect to PREVIEW page instead of period detail
            return redirect()->route('admin.salary.period.preview', $period->id)
                ->with('success', "Berhasil import data gaji periode {$request->period_label}. Silakan review sebelum publish.");
        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }

    private function importRecords($sheet, $periodId): int
    {
        $rows         = $sheet->toArray(null, true, true, true);
        $imported     = 0;
        $dataStartRow = 7; // default for new compact template (header row 5, skip row 6, data row 7)

        // Auto-detect header row (looks for NIK + NAMA in row)
        foreach ($rows as $rowNum => $row) {
            $c = isset($row['C']) ? strtoupper(trim((string)$row['C'])) : '';
            $d = isset($row['D']) ? strtoupper(trim((string)$row['D'])) : '';
            if ($c === 'NIK' && $d === 'NAMA') {
                $dataStartRow = $rowNum + 2; // skip sub-header row
                break;
            }
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $rowNum => $row) {
                if ($rowNum < $dataStartRow) continue;

                $nik  = isset($row['C']) ? trim((string)$row['C']) : null;
                $nama = isset($row['D']) ? trim((string)$row['D']) : null;

                if (empty($nik) || empty($nama) || !preg_match('/^PUS/i', $nik)) continue;

                SalaryRecord::create([
                    'period_id'                => $periodId,
                    'nik'                      => strtoupper($nik),
                    'nama'                     => $nama,
                    'nik_ktp'                  => $row['E'] ?? null,
                    'department'               => $row['F'] ?? null,
                    'jabatan'                  => isset($row['G']) && !is_numeric($row['G']) ? $row['G'] : null,
                    'tanggal_masuk_kontrak'    => $this->parseDate($row['H'] ?? null),
                    'tanggal_akhir_kontrak'    => $this->parseDate($row['I'] ?? null),
                    'no_rekening'              => $row['J'] ?? null,
                    'nama_bank'                => $row['K'] ?? null,
                    'gaji_pokok'               => $this->toNum($row['L'] ?? 0),
                    'gaji_kurangi_potongan'    => $this->toNum($row['M'] ?? 0),
                    'rapel_gaji_lembur'        => $this->toNum($row['N'] ?? 0),
                    'kompensasi_pkwt'          => $this->toNum($row['O'] ?? 0),
                    'lembur'                   => $this->toNum($row['P'] ?? 0),
                    'total_pendapatan'         => $this->toNum($row['Q'] ?? 0),
                    'bpjs_kesehatan_potongan'  => $this->toNum($row['R'] ?? 0),
                    'bpjs_tk_potongan'         => $this->toNum($row['S'] ?? 0),
                    'pph21'                    => $this->toNum($row['T'] ?? 0),
                    'pembuatan_rekening'       => $this->toNum($row['V'] ?? 0),
                    'meterai'                  => $this->toNum($row['W'] ?? 0),
                    'pinjaman_pribadi'         => $this->toNum($row['X'] ?? 0),
                    'sumbangan'                => $this->toNum($row['Y'] ?? 0),
                    'total_potongan'           => $this->toNum($row['Z'] ?? 0),
                    'total_ditransfer'         => $this->toNum($row['AA'] ?? 0),
                    'tgl_lahir'                => $this->parseDate($row['AF'] ?? null),
                    'potongan_absen'           => $this->toNum($row['AC'] ?? 0),
                    'masuk_kerja_hari'         => (int)($row['AD'] ?? 0),
                    'lama_lembur_jam'          => $this->toNum($row['AE'] ?? 0),
                    'no_kpj'                   => $row['AG'] ?? null,
                    'no_jkn'                   => $row['AH'] ?? null,
                    'jht_kary'                 => $this->toNum($row['AI'] ?? 0),
                    'jkm'                      => $this->toNum($row['AJ'] ?? 0),
                    'jkk'                      => $this->toNum($row['AK'] ?? 0),
                    'pensiun_kary'             => $this->toNum($row['AL'] ?? 0),
                    'tot_iuran_kary'           => $this->toNum($row['AM'] ?? 0),
                    'hadir'                    => (int)($row['AO'] ?? 0),
                    'cuti'                     => (int)($row['AP'] ?? 0),
                    'travel'                   => (int)($row['AQ'] ?? 0),
                    'sakit'                    => (int)($row['AR'] ?? 0),
                    'ijin'                     => (int)($row['AS'] ?? 0),
                    'alpa'                     => (int)($row['AT'] ?? 0),
                    'off'                      => (int)($row['AU'] ?? 0),
                    'total_hk'                 => (int)($row['AV'] ?? 0),
                    'ot_jam'                   => $this->toNum($row['AW'] ?? 0),
                ]);
                $imported++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $imported;
    }

    // ═══════════════════════════════════════════════════
    //  PERIOD DETAIL
    // ═══════════════════════════════════════════════════
    public function showPeriod($id)
    {
        $period  = SalaryPeriod::findOrFail($id);
        $records = $period->records()->orderBy('nama')->paginate(25);
        $stats   = $this->periodStats($period);

        return view('admin.salary.period', compact('period', 'records', 'stats'));
    }

    // ═══════════════════════════════════════════════════
    //  PREVIEW (after upload, before publish)
    // ═══════════════════════════════════════════════════
    public function previewPeriod($id)
    {
        $period  = SalaryPeriod::findOrFail($id);
        $records = $period->records()->orderBy('nama')->get(); // all records, no pagination for review
        $stats   = $this->periodStats($period);

        return view('admin.salary.preview', compact('period', 'records', 'stats'));
    }

    private function periodStats(SalaryPeriod $period): array
    {
        return [
            'total'          => $period->records()->count(),
            'total_gaji'     => $period->records()->sum('total_ditransfer'),
            'total_potongan' => $period->records()->sum('total_potongan'),
        ];
    }

    // ═══════════════════════════════════════════════════
    //  MANUAL CREATE RECORD
    // ═══════════════════════════════════════════════════
    public function create($periodId)
    {
        $period = SalaryPeriod::findOrFail($periodId);
        return view('admin.salary.create', compact('period'));
    }

    public function store(Request $request, $periodId)
    {
        $period = SalaryPeriod::findOrFail($periodId);

        $validated = $request->validate([
            'nik'                   => 'required|string|max:20',
            'nama'                  => 'required|string|max:100',
            'nik_ktp'               => 'nullable|string|max:20',
            'department'            => 'nullable|string|max:100',
            'jabatan'               => 'nullable|string|max:100',
            'nama_bank'             => 'nullable|string|max:100',
            'no_rekening'           => 'nullable|string|max:50',
            'no_kpj'                => 'nullable|string|max:50',
            'no_jkn'                => 'nullable|string|max:50',
            'tgl_lahir'             => 'nullable|date',
            'gaji_pokok'            => 'required|numeric|min:0',
            'gaji_kurangi_potongan' => 'nullable|numeric|min:0',
            'lembur'                => 'required|numeric|min:0',
            'rapel_gaji_lembur'     => 'nullable|numeric|min:0',
            'kompensasi_pkwt'       => 'nullable|numeric|min:0',
            'pph21'                 => 'nullable|numeric|min:0',
            'pinjaman_pribadi'      => 'nullable|numeric|min:0',
            'sumbangan'             => 'nullable|numeric|min:0',
            'potongan_absen'        => 'nullable|numeric|min:0',
            'pembuatan_rekening'    => 'nullable|numeric|min:0',
            'meterai'               => 'nullable|numeric|min:0',
            'hadir'                 => 'nullable|integer|min:0',
            'cuti'                  => 'nullable|integer|min:0',
            'travel'                => 'nullable|integer|min:0',
            'sakit'                 => 'nullable|integer|min:0',
            'ijin'                  => 'nullable|integer|min:0',
            'alpa'                  => 'nullable|integer|min:0',
            'off'                   => 'nullable|integer|min:0',
            'masuk_kerja_hari'      => 'nullable|integer|min:0',
            'lama_lembur_jam'       => 'nullable|numeric|min:0',
            'ot_jam'                => 'nullable|numeric|min:0',
        ]);

        $gajiPokok   = (float)$request->gaji_pokok;
        $lembur      = (float)$request->lembur;
        $rapel       = (float)($request->rapel_gaji_lembur ?? 0);
        $kompensasi  = (float)($request->kompensasi_pkwt ?? 0);
        $pph21       = (float)($request->pph21 ?? 0);
        $pinjaman    = (float)($request->pinjaman_pribadi ?? 0);
        $sumbangan   = (float)($request->sumbangan ?? 0);
        $potAbsen    = (float)($request->potongan_absen ?? 0);
        $pemRek      = (float)($request->pembuatan_rekening ?? 0);
        $meterai     = (float)($request->meterai ?? 0);

        $totalPendapatan = $gajiPokok + $lembur + $rapel + $kompensasi;
        $bpjsKes         = round($gajiPokok * 0.01);
        $bpjsTk          = round($gajiPokok * 0.03);
        $totalPotongan   = $bpjsKes + $bpjsTk + $pph21 + $pinjaman + $sumbangan + $potAbsen + $pemRek + $meterai;
        $totalDitransfer = $totalPendapatan - $totalPotongan;

        $hadir  = (int)($request->hadir  ?? 0);
        $cuti   = (int)($request->cuti   ?? 0);
        $travel = (int)($request->travel ?? 0);
        $sakit  = (int)($request->sakit  ?? 0);
        $ijin   = (int)($request->ijin   ?? 0);
        $alpa   = (int)($request->alpa   ?? 0);
        $off    = (int)($request->off    ?? 0);

        SalaryRecord::create(array_merge($validated, [
            'period_id'               => $period->id,
            'nik'                     => strtoupper($request->nik),
            'total_pendapatan'        => $totalPendapatan,
            'bpjs_kesehatan_potongan' => $bpjsKes,
            'bpjs_tk_potongan'        => $bpjsTk,
            'pph21'                   => $pph21,
            'pinjaman_pribadi'        => $pinjaman,
            'sumbangan'               => $sumbangan,
            'potongan_absen'          => $potAbsen,
            'pembuatan_rekening'      => $pemRek,
            'meterai'                 => $meterai,
            'total_potongan'          => $totalPotongan,
            'total_ditransfer'        => $totalDitransfer,
            'total_hk'                => $hadir + $cuti + $travel + $sakit + $ijin + $alpa + $off,
        ]));

        return redirect()->route('admin.salary.period', $period->id)
            ->with('success', "Data gaji {$request->nama} berhasil ditambahkan.");
    }

    // ═══════════════════════════════════════════════════
    //  EDIT / UPDATE RECORD
    // ═══════════════════════════════════════════════════
    public function edit($id)
    {
        $record = SalaryRecord::with('period')->findOrFail($id);
        return view('admin.salary.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = SalaryRecord::findOrFail($id);

        $validated = $request->validate([
            'nama'                  => 'required|string|max:100',
            'department'            => 'nullable|string|max:100',
            'jabatan'               => 'nullable|string|max:100',
            'nama_bank'             => 'nullable|string|max:100',
            'no_rekening'           => 'nullable|string|max:50',
            'gaji_pokok'            => 'required|numeric|min:0',
            'gaji_kurangi_potongan' => 'nullable|numeric|min:0',
            'lembur'                => 'required|numeric|min:0',
            'rapel_gaji_lembur'     => 'nullable|numeric|min:0',
            'kompensasi_pkwt'       => 'nullable|numeric|min:0',
            'pph21'                 => 'nullable|numeric|min:0',
            'pinjaman_pribadi'      => 'nullable|numeric|min:0',
            'sumbangan'             => 'nullable|numeric|min:0',
            'potongan_absen'        => 'nullable|numeric|min:0',
            'hadir'                 => 'nullable|integer|min:0',
            'cuti'                  => 'nullable|integer|min:0',
            'sakit'                 => 'nullable|integer|min:0',
            'ijin'                  => 'nullable|integer|min:0',
            'alpa'                  => 'nullable|integer|min:0',
            'masuk_kerja_hari'      => 'nullable|integer|min:0',
            'lama_lembur_jam'       => 'nullable|numeric|min:0',
        ]);

        $gajiPokok  = (float)$request->gaji_pokok;
        $lembur     = (float)$request->lembur;
        $rapel      = (float)($request->rapel_gaji_lembur ?? 0);
        $kompensasi = (float)($request->kompensasi_pkwt ?? 0);
        $pph21      = (float)($request->pph21 ?? 0);
        $pinjaman   = (float)($request->pinjaman_pribadi ?? 0);
        $sumbangan  = (float)($request->sumbangan ?? 0);
        $potAbsen   = (float)($request->potongan_absen ?? 0);

        $totalPendapatan = $gajiPokok + $lembur + $rapel + $kompensasi;
        $bpjsKes         = round($gajiPokok * 0.01);
        $bpjsTk          = round($gajiPokok * 0.03);
        $totalPotongan   = $bpjsKes + $bpjsTk + $pph21 + $pinjaman + $sumbangan + $potAbsen;
        $totalDitransfer = $totalPendapatan - $totalPotongan;

        $record->update(array_merge($validated, [
            'total_pendapatan'        => $totalPendapatan,
            'bpjs_kesehatan_potongan' => $bpjsKes,
            'bpjs_tk_potongan'        => $bpjsTk,
            'pph21'                   => $pph21,
            'pinjaman_pribadi'        => $pinjaman,
            'sumbangan'               => $sumbangan,
            'potongan_absen'          => $potAbsen,
            'total_potongan'          => $totalPotongan,
            'total_ditransfer'        => $totalDitransfer,
        ]));

        return redirect()->route('admin.salary.period', $record->period_id)
            ->with('success', "Data gaji {$record->nama} berhasil diperbarui.");
    }

    // ═══════════════════════════════════════════════════
    //  DELETE RECORD / PERIOD
    // ═══════════════════════════════════════════════════
    public function destroy($id)
    {
        $record   = SalaryRecord::findOrFail($id);
        $periodId = $record->period_id;
        $nama     = $record->nama;
        $record->delete();

        return redirect()->route('admin.salary.period', $periodId)
            ->with('success', "Data gaji {$nama} berhasil dihapus.");
    }

    public function destroyPeriod($id)
    {
        $period = SalaryPeriod::findOrFail($id);
        $label  = $period->period_label;
        $period->delete();

        return redirect()->route('admin.salary.index')
            ->with('success', "Periode {$label} berhasil dihapus.");
    }

    // ═══════════════════════════════════════════════════
    //  PUBLISH
    // ═══════════════════════════════════════════════════
    public function publishPeriod($id)
    {
        $period = SalaryPeriod::findOrFail($id);
        $period->update(['status' => 'published']);

        return redirect()->route('admin.salary.period', $period->id)
            ->with('success', "Periode {$period->period_label} berhasil dipublish. Karyawan sekarang dapat mengakses slip gaji.");
    }

    // ═══════════════════════════════════════════════════
    //  EXPORT TEMPLATE (compact, no spacer rows)
    //  Layout:
    //   Row 1  – Company header
    //   Row 2  – Sheet sub-title
    //   Row 3  – Period placeholder
    //   Row 4  – Section colour-band labels
    //   Row 5  – Column headers  ← NIK in C, NAMA in D (auto-detected by importer)
    //   Row 6  – [skipped by importer: +2 from header row 5]
    //   Row 7+ – Data rows
    // ═══════════════════════════════════════════════════
    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('PUS Payroll System')
            ->setTitle('Template Gaji PUS');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DFT GJ');

        $lastCol = 'AW';
        $range   = "A1:{$lastCol}";

        // ── ROW 1: Company name ──────────────────────────
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'PT PRIMA UTAMA SULTRA');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a3a5c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ── ROW 2: Sheet title ───────────────────────────
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'DAFTAR GAJI KARYAWAN (DFT GJ)');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563a8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ── ROW 3: Period info ───────────────────────────
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', 'PERIODE: [ISI PERIODE]');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1a3a5c']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'dbeafe']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(14);

        // ── ROW 4: Section colour-band labels ───────────
        $sections4 = [
            'A4:B4'   => ['NO',                '374151', '9ca3af'],
            'C4:I4'   => ['IDENTITAS KARYAWAN', '1e3a5f', 'bfdbfe'],
            'J4:K4'   => ['BANK',               '065f46', 'a7f3d0'],
            'L4:Q4'   => ['PENDAPATAN',          '065f46', 'a7f3d0'],
            'R4:Z4'   => ['POTONGAN',            '7f1d1d', 'fecaca'],
            'AA4'     => ['TRANSFER',            '065f46', 'd1fae5'],
            'AB4:AH4' => ['INFO TAMBAHAN',       '4c1d95', 'ddd6fe'],
            'AI4:AN4' => ['BPJS PERUSAHAAN',     '0c4a6e', 'bae6fd'],
            'AO4:AW4' => ['KEHADIRAN',           '78350f', 'fed7aa'],
        ];
        foreach ($sections4 as $cellRange => [$label, $bg, $font]) {
            if (str_contains($cellRange, ':')) {
                $sheet->mergeCells($cellRange);
            }
            $firstCell = explode(':', $cellRange)[0];
            $sheet->setCellValue($firstCell, $label);
            $sheet->getStyle($cellRange)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => $font]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'ffffff']]],
            ]);
        }
        $sheet->getRowDimension(4)->setRowHeight(14);

        // ── ROW 5: Column headers (NIK in C, NAMA in D — auto-detected) ──
        $headers5 = [
            'A' => 'NO',    'B' => 'KODE',
            'C' => 'NIK',   'D' => 'NAMA',   'E' => 'NIK KTP',  'F' => 'DEPARTMENT',
            'G' => 'JABATAN','H' => 'TGL MASUK','I' => 'TGL AKHIR',
            'J' => 'NO REK', 'K' => 'BANK',
            'L' => 'GAJI POKOK','M' => 'GAJI KURANGI','N' => 'RAPEL',
            'O' => 'KOMP PKWT','P' => 'LEMBUR','Q' => 'TOTAL PEND',
            'R' => 'BPJS KES 1%','S' => 'BPJS TK 3%','T' => 'PPH21',
            'U' => 'KET',   'V' => 'PEM REK','W' => 'METERAI',
            'X' => 'PINJAMAN','Y' => 'SUMBANGAN','Z' => 'TOTAL POT',
            'AA' => 'TRANSFER',
            'AB' => 'KET2', 'AC' => 'POT ABSEN','AD' => 'MASUK HR',
            'AE' => 'LBR JAM','AF' => 'TGL LAHIR',
            'AG' => 'NO KPJ','AH' => 'NO JKN',
            'AI' => 'JHT 3.7%','AJ' => 'JKM 0.3%','AK' => 'JKK 0.24%',
            'AL' => 'PENSIUN 3%','AM' => 'TOT IURAN','AN' => 'KET3',
            'AO' => 'HADIR','AP' => 'CUTI','AQ' => 'TRAVEL',
            'AR' => 'SAKIT','AS' => 'IJIN','AT' => 'ALPA',
            'AU' => 'OFF',  'AV' => 'TOTAL HK','AW' => 'OT JAM',
        ];
        foreach ($headers5 as $col => $label) {
            $sheet->setCellValue("{$col}5", $label);
        }
        $sheet->getStyle("A5:{$lastCol}5")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '1e3a5f']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'c3d0e0']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(28);

        // ── ROW 6: skipped by importer (dataStartRow = 5+2 = 7) ─
        $sheet->getRowDimension(6)->setRowHeight(4);

        // ── ROW 7+: Sample data row ──────────────────────
        $sampleData = [
            'C' => 'PUS00001', 'D' => 'NAMA KARYAWAN CONTOH',
            'E' => '7401010101010001', 'F' => 'DEPARTEMEN',
            'G' => 'JABATAN', 'K' => 'BNI', 'J' => '1234567890',
            'L' => 5000000, 'M' => 5000000, 'P' => 500000,
            'Q' => 5500000,
            'R' => 50000,  // BPJS Kes 1%
            'S' => 150000, // BPJS TK 3%
            'T' => 0,      // PPH21
            'Z' => 200000, // Total Potongan
            'AA' => 5300000, // Total Transfer
            'AD' => 26, 'AE' => 2,
            'AO' => 26, 'AV' => 26,
        ];
        foreach ($sampleData as $col => $val) {
            $sheet->setCellValue("{$col}7", $val);
        }
        $sheet->getStyle("A7:{$lastCol}7")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8fafc']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']]],
        ]);
        $sheet->getStyle("L7:AA7")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension(7)->setRowHeight(16);

        // ── Row 8 onwards: empty data rows with borders ──
        for ($r = 8; $r <= 200; $r++) {
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']]],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        // ── Column widths ────────────────────────────────
        $widths = [
            'A'=>4,'B'=>7,'C'=>11,'D'=>24,'E'=>15,'F'=>17,'G'=>16,'H'=>11,'I'=>11,
            'J'=>14,'K'=>10,'L'=>13,'M'=>13,'N'=>11,'O'=>11,'P'=>11,'Q'=>13,
            'R'=>11,'S'=>11,'T'=>11,'U'=>8,'V'=>11,'W'=>9,'X'=>11,'Y'=>10,'Z'=>13,
            'AA'=>13,'AB'=>8,'AC'=>11,'AD'=>9,'AE'=>9,'AF'=>11,'AG'=>13,'AH'=>13,
            'AI'=>9,'AJ'=>9,'AK'=>10,'AL'=>10,'AM'=>11,'AN'=>7,
            'AO'=>7,'AP'=>7,'AQ'=>7,'AR'=>7,'AS'=>7,'AT'=>7,'AU'=>7,'AV'=>7,'AW'=>7,
        ];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Freeze: fix rows 1-5 + cols A-C, data starts at D7
        $sheet->freezePane('D6');

        // ── Write & stream ───────────────────────────────
        $writer   = new Xlsx($spreadsheet);
        $filename = 'template_gaji_pus.xlsx';
        $tmpPath  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        $writer->save($tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ])->deleteFileAfterSend(true);
    }

    // ═══════════════════════════════════════════════════
    //  SAMPLE DATA (10 karyawan siap upload)
    // ═══════════════════════════════════════════════════
    public function exportSample()
    {
        // 10 karyawan sample dengan perhitungan lengkap
        // Kolom sesuai layout template: C=NIK, D=NAMA, E=NIK KTP, F=DEPT, G=JABATAN,
        // H=TGL MASUK, I=TGL AKHIR, J=NO REK, K=BANK,
        // L=GAJI POKOK, M=GAJI KURANGI, N=RAPEL, O=KOMP, P=LEMBUR, Q=TOTAL PEND,
        // R=BPJS KES, S=BPJS TK, T=PPH21, U=KET, V=PEM REK, W=METERAI,
        // X=PINJAMAN, Y=SUMBANGAN, Z=TOTAL POT, AA=TRANSFER,
        // AB=KET2, AC=POT ABSEN, AD=MASUK HR, AE=LBR JAM, AF=TGL LAHIR,
        // AG=NO KPJ, AH=NO JKN,
        // AI=JHT, AJ=JKM, AK=JKK, AL=PENSIUN, AM=TOT IURAN, AN=KET3,
        // AO=HADIR, AP=CUTI, AQ=TRAVEL, AR=SAKIT, AS=IJIN, AT=ALPA, AU=OFF,
        // AV=TOTAL HK, AW=OT JAM
        $employees = [
            ['PUS00001','AHMAD FAUZAN','7471010101900001','Information Technology','Staff IT','2022-03-01','2026-02-28','1234500001','BNI',6500000,2,0,'2024-05-15','BPJSTK001','JKN001',3.7,0.3,0.24,3,26,2,0,0],
            ['PUS00002','SITI RAHAYU','7471015502880002','Human Resources','HR Officer','2021-06-15','2026-06-14','1234500002','BRI',5500000,0,0,'1988-02-15','BPJSTK002','JKN002',3.7,0.3,0.24,3,26,0,0,0],
            ['PUS00003','BUDI SANTOSO','7471010303850003','Finance','Akuntan','2020-01-10','2025-12-31','1234500003','Mandiri',7000000,3,500000,'1985-03-03','BPJSTK003','JKN003',3.7,0.3,0.24,3,25,1,0,0],
            ['PUS00004','DEWI LESTARI','7471014404920004','Operations','Operator','2023-04-01','2026-03-31','1234500004','BNI',4500000,1,0,'1992-04-04','BPJSTK004','JKN004',3.7,0.3,0.24,3,26,0,0,0],
            ['PUS00005','RUDI HARTONO','7471010505870005','Engineering','Teknisi Senior','2019-08-20','2025-08-19','1234500005','BSI',8000000,5,0,'1987-05-05','BPJSTK005','JKN005',3.7,0.3,0.24,3,24,2,0,0],
            ['PUS00006','NINA KUSUMA','7471016606930006','Marketing','Marketing Staff','2022-09-05','2026-09-04','1234500006','BRI',5800000,0,0,'1993-06-06','BPJSTK006','JKN006',3.7,0.3,0.24,3,26,0,0,0],
            ['PUS00007','HENDRA WIJAYA','7471010707860007','Production','Kepala Produksi','2018-02-14','2026-02-13','1234500007','Mandiri',9000000,8,0,'1986-07-07','BPJSTK007','JKN007',3.7,0.3,0.24,3,25,0,1,0],
            ['PUS00008','RATNA SARI','7471018808950008','Administration','Staff Admin','2024-01-02','2025-12-31','1234500008','BNI',4200000,0,0,'1995-08-08','BPJSTK008','JKN008',3.7,0.3,0.24,3,26,0,0,0],
            ['PUS00009','DONI PRASETYO','7471010909900009','Logistics','Driver','2021-11-01','2026-10-31','1234500009','BRI',4800000,2,0,'1990-09-09','BPJSTK009','JKN009',3.7,0.3,0.24,3,24,0,0,2],
            ['PUS00010','AISYAH PUTRI','7471011010940010','Quality Control','QC Inspector','2023-07-17','2026-07-16','1234500010','BSI',5600000,1,0,'1994-10-10','BPJSTK010','JKN010',3.7,0.3,0.24,3,26,0,0,0],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DFT GJ');
        $lastCol = 'AW';

        // ── Rows 1-3: header (sama dengan template) ──────
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'PT PRIMA UTAMA SULTRA');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a3a5c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'DAFTAR GAJI KARYAWAN (DFT GJ)');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563a8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', 'PERIODE: APRIL 2026');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1e3a5f']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'dbeafe']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(14);

        // ── Row 4: Section bands ──────────────────────────
        $sections = [
            'A4:B4'   => ['NO',              '374151','9ca3af'],
            'C4:I4'   => ['IDENTITAS',       '1e3a5f','bfdbfe'],
            'J4:K4'   => ['BANK',            '065f46','a7f3d0'],
            'L4:Q4'   => ['PENDAPATAN',      '065f46','a7f3d0'],
            'R4:Z4'   => ['POTONGAN',        '7f1d1d','fecaca'],
            'AA4'     => ['TRANSFER',        '065f46','d1fae5'],
            'AB4:AH4' => ['INFO TAMBAHAN',   '4c1d95','ddd6fe'],
            'AI4:AN4' => ['BPJS PERUSAHAAN', '0c4a6e','bae6fd'],
            'AO4:AW4' => ['KEHADIRAN',       '78350f','fed7aa'],
        ];
        foreach ($sections as $range => [$lbl, $bg, $fg]) {
            if (str_contains($range, ':')) $sheet->mergeCells($range);
            $cell = explode(':', $range)[0];
            $sheet->setCellValue($cell, $lbl);
            $sheet->getStyle($range)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => $fg]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'ffffff']]],
            ]);
        }
        $sheet->getRowDimension(4)->setRowHeight(14);

        // ── Row 5: Column headers ─────────────────────────
        $headers = [
            'A'=>'NO','B'=>'KODE','C'=>'NIK','D'=>'NAMA','E'=>'NIK KTP','F'=>'DEPARTMENT',
            'G'=>'JABATAN','H'=>'TGL MASUK','I'=>'TGL AKHIR','J'=>'NO REK','K'=>'BANK',
            'L'=>'GAJI POKOK','M'=>'GAJI KURANGI','N'=>'RAPEL','O'=>'KOMP PKWT',
            'P'=>'LEMBUR','Q'=>'TOTAL PEND','R'=>'BPJS KES 1%','S'=>'BPJS TK 3%',
            'T'=>'PPH21','U'=>'KET','V'=>'PEM REK','W'=>'METERAI','X'=>'PINJAMAN',
            'Y'=>'SUMBANGAN','Z'=>'TOTAL POT','AA'=>'TRANSFER',
            'AB'=>'KET2','AC'=>'POT ABSEN','AD'=>'MASUK HR','AE'=>'LBR JAM','AF'=>'TGL LAHIR',
            'AG'=>'NO KPJ','AH'=>'NO JKN','AI'=>'JHT 3.7%','AJ'=>'JKM 0.3%',
            'AK'=>'JKK 0.24%','AL'=>'PENSIUN 3%','AM'=>'TOT IURAN','AN'=>'KET3',
            'AO'=>'HADIR','AP'=>'CUTI','AQ'=>'TRAVEL','AR'=>'SAKIT','AS'=>'IJIN',
            'AT'=>'ALPA','AU'=>'OFF','AV'=>'TOTAL HK','AW'=>'OT JAM',
        ];
        foreach ($headers as $col => $lbl) {
            $sheet->setCellValue("{$col}5", $lbl);
        }
        $sheet->getStyle("A5:{$lastCol}5")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '1e3a5f']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'c3d0e0']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(28);

        // Row 6: spacer (dilewati importer: dataStartRow = 5+2 = 7)
        $sheet->getRowDimension(6)->setRowHeight(4);

        // ── Rows 7+: Data ─────────────────────────────────
        foreach ($employees as $idx => $e) {
            $row = 7 + $idx;
            [
                $nik, $nama, $nikKtp, $dept, $jabatan,
                $tglMasuk, $tglAkhir, $noRek, $bank,
                $gajiPokok, $lemburJam, $pinjaman, $tglLahir,
                $noKpj, $noJkn,
                $pctJht, $pctJkm, $pctJkk, $pctPensiun,
                $hadir, $cuti, $travel, $alpa,
            ] = $e;

            // Kalkulasi
            $lembur          = (int)($gajiPokok / 173 * $lemburJam);
            $masukHari       = $hadir;
            $gajiKurangi     = $gajiPokok; // full month
            $totalPendapatan = $gajiPokok + $lembur + $pinjaman; // pinjaman = kompensasi here
            $bpjsKes         = (int)round($gajiPokok * 0.01);
            $bpjsTk          = (int)round($gajiPokok * 0.03);
            $sumbangan       = 50000;
            $totalPotongan   = $bpjsKes + $bpjsTk + $sumbangan;
            $totalTransfer   = $totalPendapatan - $totalPotongan;
            $jht             = (int)round($gajiPokok * $pctJht / 100);
            $jkm             = (int)round($gajiPokok * $pctJkm / 100);
            $jkk             = (int)round($gajiPokok * $pctJkk / 100);
            $pensiun         = (int)round($gajiPokok * $pctPensiun / 100);
            $totIuran        = $jht + $jkm + $jkk + $pensiun;
            $totalHk         = $hadir + $cuti + $travel;
            $off             = 26 - $totalHk;

            $values = [
                'A' => $idx + 1,
                'C' => $nik,
                'D' => $nama,
                'E' => $nikKtp,
                'F' => $dept,
                'G' => $jabatan,
                'H' => $tglMasuk,
                'I' => $tglAkhir,
                'J' => $noRek,
                'K' => $bank,
                'L' => $gajiPokok,
                'M' => $gajiKurangi,
                'N' => 0,               // rapel
                'O' => 0,               // kompensasi pkwt
                'P' => $lembur,
                'Q' => $totalPendapatan,
                'R' => $bpjsKes,
                'S' => $bpjsTk,
                'T' => 0,               // pph21
                'V' => 0,               // pembuatan rekening
                'W' => 0,               // meterai
                'X' => 0,               // pinjaman
                'Y' => $sumbangan,
                'Z' => $totalPotongan,
                'AA' => $totalTransfer,
                'AC' => 0,              // potongan absen
                'AD' => $masukHari,
                'AE' => $lemburJam,
                'AF' => $tglLahir,
                'AG' => $noKpj,
                'AH' => $noJkn,
                'AI' => $jht,
                'AJ' => $jkm,
                'AK' => $jkk,
                'AL' => $pensiun,
                'AM' => $totIuran,
                'AO' => $hadir,
                'AP' => $cuti,
                'AQ' => $travel,
                'AR' => 0,
                'AS' => 0,
                'AT' => $alpa,
                'AU' => $off > 0 ? $off : 0,
                'AV' => $totalHk,
                'AW' => $lemburJam,
            ];

            foreach ($values as $col => $val) {
                $sheet->setCellValue("{$col}{$row}", $val);
            }

            // Format currency columns
            $sheet->getStyle("L{$row}:AA{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("AI{$row}:AM{$row}")->getNumberFormat()->setFormatCode('#,##0');

            // Alternating row colour
            $bgColor = ($idx % 2 === 0) ? 'ffffff' : 'f8fafc';
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']]],
                'font'    => ['size' => 9],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(16);
        }

        // ── Column widths ─────────────────────────────────
        $widths = [
            'A'=>4,'B'=>7,'C'=>11,'D'=>24,'E'=>15,'F'=>17,'G'=>16,'H'=>11,'I'=>11,
            'J'=>14,'K'=>10,'L'=>13,'M'=>13,'N'=>11,'O'=>11,'P'=>11,'Q'=>13,
            'R'=>11,'S'=>11,'T'=>11,'U'=>8,'V'=>11,'W'=>9,'X'=>11,'Y'=>10,'Z'=>13,
            'AA'=>13,'AB'=>8,'AC'=>11,'AD'=>9,'AE'=>9,'AF'=>11,'AG'=>13,'AH'=>13,
            'AI'=>9,'AJ'=>9,'AK'=>10,'AL'=>10,'AM'=>11,'AN'=>7,
            'AO'=>7,'AP'=>7,'AQ'=>7,'AR'=>7,'AS'=>7,'AT'=>7,'AU'=>7,'AV'=>7,'AW'=>7,
        ];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->freezePane('D6');

        // ── Stream ────────────────────────────────────────
        $writer  = new Xlsx($spreadsheet);
        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sample_gaji_pus.xlsx';
        $writer->save($tmpPath);

        return response()->download($tmpPath, 'sample_gaji_april_2026.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ═══════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════
    private function parseDate($val): ?string
    {
        if (empty($val)) return null;
        try {
            if (is_numeric($val)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$val);
                return $date->format('Y-m-d');
            }
            $ts = strtotime($val);
            return $ts ? date('Y-m-d', $ts) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function toNum($val): float
    {
        if (is_null($val) || $val === '' || $val === '-') return 0;
        return (float) preg_replace('/[^0-9.\-]/', '', (string)$val);
    }
}
