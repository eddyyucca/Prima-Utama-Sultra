<?php

namespace App\Http\Controllers;

use App\Models\SalaryPeriod;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalaryController extends Controller
{
    public function index()
    {
        $periods = SalaryPeriod::withCount('records')
            ->withSum('records', 'total_ditransfer')
            ->orderByDesc('period_start')
            ->paginate(10);
        return view('admin.salary.index', compact('periods'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
            'period_label' => 'required|string|max:50',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'working_days' => 'required|integer|min:1|max:31',
        ]);

        $file = $request->file('excel_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/salary'), $filename);

        try {
            $spreadsheet = IOFactory::load(public_path('uploads/salary/' . $filename));
            $sheet = $spreadsheet->getSheetByName('    DFT GJ     ') 
                ?? $spreadsheet->getSheet(1);

            $period = SalaryPeriod::create([
                'period_label' => $request->period_label,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'working_days' => $request->working_days,
                'uploaded_file' => $filename,
                'status' => 'draft',
                'uploaded_by' => Auth::id(),
            ]);

            $this->importRecords($sheet, $period->id);

            return redirect()->route('admin.salary.period', $period->id)
                ->with('success', "Berhasil import data gaji periode {$request->period_label}");
        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }

    private function importRecords($sheet, $periodId)
    {
        $rows = $sheet->toArray(null, true, true, true);
        $imported = 0;

        // Find header row (row with "NIK" in column C)
        $dataStartRow = 9;
        foreach ($rows as $rowNum => $row) {
            if (isset($row['C']) && trim($row['C']) === 'NIK' && isset($row['D']) && trim($row['D']) === 'NAMA') {
                $dataStartRow = $rowNum + 2;
                break;
            }
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $rowNum => $row) {
                if ($rowNum < $dataStartRow) continue;

                $nik = isset($row['C']) ? trim($row['C']) : null;
                $nama = isset($row['D']) ? trim($row['D']) : null;

                if (empty($nik) || empty($nama) || !preg_match('/^PUS/', $nik)) continue;

                // Parse dates
                $tglMasuk = $this->parseDate($row['G'] ?? null);
                $tglAkhir = $this->parseDate($row['H'] ?? null);
                $tglLahir = $this->parseDate($row['AF'] ?? null);

                SalaryRecord::create([
                    'period_id' => $periodId,
                    'nik' => $nik,
                    'nama' => $nama,
                    'nik_ktp' => $row['E'] ?? null,
                    'department' => $row['F'] ?? null,
                    'jabatan' => $row['G'] ? (is_numeric($row['G']) ? null : $row['G']) : null,
                    'tanggal_masuk_kontrak' => $tglMasuk,
                    'tanggal_akhir_kontrak' => $tglAkhir,
                    'no_rekening' => $row['J'] ?? null,
                    'nama_bank' => $row['K'] ?? null,
                    'gaji_pokok' => $this->toNum($row['L'] ?? 0),
                    'gaji_kurangi_potongan' => $this->toNum($row['M'] ?? 0),
                    'rapel_gaji_lembur' => $this->toNum($row['N'] ?? 0),
                    'kompensasi_pkwt' => $this->toNum($row['O'] ?? 0),
                    'lembur' => $this->toNum($row['P'] ?? 0),
                    'total_pendapatan' => $this->toNum($row['Q'] ?? 0),
                    'bpjs_kesehatan_potongan' => $this->toNum($row['R'] ?? 0),
                    'bpjs_tk_potongan' => $this->toNum($row['S'] ?? 0),
                    'pph21' => $this->toNum($row['T'] ?? 0),
                    'pembuatan_rekening' => $this->toNum($row['V'] ?? 0),
                    'meterai' => $this->toNum($row['W'] ?? 0),
                    'pinjaman_pribadi' => $this->toNum($row['X'] ?? 0),
                    'sumbangan' => $this->toNum($row['Y'] ?? 0),
                    'total_potongan' => $this->toNum($row['Z'] ?? 0),
                    'total_ditransfer' => $this->toNum($row['AA'] ?? 0),
                    'tgl_lahir' => $tglLahir,
                    'potongan_absen' => $this->toNum($row['AC'] ?? 0),
                    'masuk_kerja_hari' => (int)($row['AD'] ?? 0),
                    'lama_lembur_jam' => $this->toNum($row['AE'] ?? 0),
                    'no_kpj' => $row['AG'] ?? null,
                    'no_jkn' => $row['AH'] ?? null,
                    'jht_kary' => $this->toNum($row['AI'] ?? 0),
                    'jkm' => $this->toNum($row['AJ'] ?? 0),
                    'jkk' => $this->toNum($row['AK'] ?? 0),
                    'pensiun_kary' => $this->toNum($row['AL'] ?? 0),
                    'tot_iuran_kary' => $this->toNum($row['AM'] ?? 0),
                    'hadir' => (int)($row['AO'] ?? 0),
                    'cuti' => (int)($row['AP'] ?? 0),
                    'travel' => (int)($row['AQ'] ?? 0),
                    'sakit' => (int)($row['AR'] ?? 0),
                    'ijin' => (int)($row['AS'] ?? 0),
                    'alpa' => (int)($row['AT'] ?? 0),
                    'off' => (int)($row['AU'] ?? 0),
                    'total_hk' => (int)($row['AV'] ?? 0),
                    'ot_jam' => $this->toNum($row['AW'] ?? 0),
                ]);
                $imported++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function parseDate($val): ?string
    {
        if (empty($val)) return null;
        try {
            if (is_numeric($val)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                return $date->format('Y-m-d');
            }
            return date('Y-m-d', strtotime($val));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function toNum($val): float
    {
        if (is_null($val) || $val === '' || $val === '-') return 0;
        return (float) preg_replace('/[^0-9.\-]/', '', $val);
    }

    public function showPeriod($id)
    {
        $period = SalaryPeriod::findOrFail($id);
        $records = $period->records()->orderBy('nama')->paginate(25);
        $stats = [
            'total' => $period->records()->count(),
            'total_gaji' => $period->records()->sum('total_ditransfer'),
            'total_potongan' => $period->records()->sum('total_potongan'),
        ];
        return view('admin.salary.period', compact('period', 'records', 'stats'));
    }

    public function edit($id)
    {
        $record = SalaryRecord::with('period')->findOrFail($id);
        return view('admin.salary.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = SalaryRecord::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'nama_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:50',
            'gaji_pokok' => 'required|numeric|min:0',
            'lembur' => 'required|numeric|min:0',
            'rapel_gaji_lembur' => 'nullable|numeric|min:0',
            'kompensasi_pkwt' => 'nullable|numeric|min:0',
            'pph21' => 'nullable|numeric|min:0',
            'pinjaman_pribadi' => 'nullable|numeric|min:0',
            'sumbangan' => 'nullable|numeric|min:0',
            'hadir' => 'nullable|integer|min:0',
            'cuti' => 'nullable|integer|min:0',
            'sakit' => 'nullable|integer|min:0',
            'ijin' => 'nullable|integer|min:0',
            'alpa' => 'nullable|integer|min:0',
        ]);

        // Recalculate totals
        $gajiPokok = $request->gaji_pokok;
        $lembur = $request->lembur;
        $rapel = $request->rapel_gaji_lembur ?? 0;
        $kompensasi = $request->kompensasi_pkwt ?? 0;
        $totalPendapatan = $gajiPokok + $lembur + $rapel + $kompensasi;

        $bpjsKes = round($gajiPokok * 0.01);
        $bpjsTk = round($gajiPokok * 0.03);
        $pph21 = $request->pph21 ?? 0;
        $pinjaman = $request->pinjaman_pribadi ?? 0;
        $sumbangan = $request->sumbangan ?? 0;
        $totalPotongan = $bpjsKes + $bpjsTk + $pph21 + $pinjaman + $sumbangan;
        $totalDitransfer = $gajiPokok + $lembur + $rapel + $kompensasi - $totalPotongan;

        $record->update(array_merge($validated, [
            'total_pendapatan' => $totalPendapatan,
            'bpjs_kesehatan_potongan' => $bpjsKes,
            'bpjs_tk_potongan' => $bpjsTk,
            'pph21' => $pph21,
            'pinjaman_pribadi' => $pinjaman,
            'sumbangan' => $sumbangan,
            'total_potongan' => $totalPotongan,
            'total_ditransfer' => $totalDitransfer,
        ]));

        return redirect()->route('admin.salary.period', $record->period_id)
            ->with('success', 'Data gaji berhasil diperbarui');
    }

    public function destroy($id)
    {
        $record = SalaryRecord::findOrFail($id);
        $periodId = $record->period_id;
        $record->delete();
        return redirect()->route('admin.salary.period', $periodId)
            ->with('success', 'Data berhasil dihapus');
    }

    public function destroyPeriod($id)
    {
        $period = SalaryPeriod::findOrFail($id);
        $period->delete();
        return redirect()->route('admin.salary.index')
            ->with('success', 'Periode gaji berhasil dihapus');
    }

    public function publishPeriod($id)
    {
        $period = SalaryPeriod::findOrFail($id);
        $period->update(['status' => 'published']);
        return back()->with('success', 'Periode berhasil dipublikasikan. Karyawan sekarang bisa mengakses slip gaji.');
    }

    public function exportTemplate()
    {
        return response()->download(public_path('template/template_gaji_pus.xlsx'), 'template_gaji_pus.xlsx');
    }

    public function showUploadForm()
    {
        return view('admin.salary.upload');
    }
}
