<?php

namespace App\Http\Controllers;

use App\Models\SalaryPeriod;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;

class EmployeePortalController extends Controller
{
    public function index()
    {
        $periods = SalaryPeriod::where('status', 'published')
            ->orderByDesc('period_start')
            ->get();
        return view('employee.portal', compact('periods'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'period_id' => 'required|exists:salary_periods,id',
        ]);

        $period = SalaryPeriod::where('id', $request->period_id)
            ->where('status', 'published')
            ->firstOrFail();

        $record = SalaryRecord::where('period_id', $period->id)
            ->where('nik', strtoupper(trim($request->nik)))
            ->first();

        if (!$record) {
            return back()->withErrors(['nik' => 'NIK tidak ditemukan pada periode ini.'])->withInput();
        }

        // Verify tanggal lahir
        if ($record->tgl_lahir) {
            $inputDate = date('Y-m-d', strtotime($request->tanggal_lahir));
            $recordDate = $record->tgl_lahir->format('Y-m-d');
            if ($inputDate !== $recordDate) {
                return back()->withErrors(['tanggal_lahir' => 'Tanggal lahir tidak sesuai.'])->withInput();
            }
        }

        return redirect()->route('employee.slip', $record->id);
    }

    public function showSlip($id)
    {
        $record = SalaryRecord::with('period')->findOrFail($id);

        if ($record->period->status !== 'published') {
            abort(403, 'Slip gaji belum tersedia.');
        }

        $lemburPerJam = $record->gaji_pokok > 0 ? $record->gaji_pokok / 173.333333 : 0;

        return view('employee.slip', compact('record', 'lemburPerJam'));
    }

    public function downloadSlip($id)
    {
        $record = SalaryRecord::with('period')->findOrFail($id);

        if ($record->period->status !== 'published') {
            abort(403);
        }

        $lemburPerJam = $record->gaji_pokok > 0 ? $record->gaji_pokok / 173.333333 : 0;

        $logoPath = public_path('images/logo_pus.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('employee.slip_pdf', compact('record', 'lemburPerJam', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("slip_gaji_{$record->nik}_{$record->period->period_label}.pdf");
    }
}
