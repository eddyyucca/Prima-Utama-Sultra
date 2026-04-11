<?php

namespace App\Http\Controllers;

use App\Models\SalaryPeriod;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $allPeriods   = SalaryPeriod::orderByDesc('period_start')->get(['id', 'period_label', 'status']);
        $totalPeriods = $allPeriods->count();

        // Pilih periode berdasarkan filter, default ke periode terbaru
        $selectedId   = $request->get('period_id');
        $latestPeriod = $selectedId
            ? SalaryPeriod::find($selectedId)
            : SalaryPeriod::orderByDesc('period_start')->first();

        // Periode sebelumnya (untuk perbandingan pertumbuhan)
        $prevPeriod = $latestPeriod
            ? SalaryPeriod::where('period_start', '<', $latestPeriod->period_start)
                ->orderByDesc('period_start')->first()
            : null;

        $stats      = [];
        $byDept     = collect();
        $byBank     = collect();
        $topEarners = collect();

        if ($latestPeriod) {
            $q = $latestPeriod->records();
            $stats = [
                'total_karyawan'   => $q->count(),
                'total_gaji'       => $q->sum('total_ditransfer'),
                'total_gaji_pokok' => $q->sum('gaji_pokok'),
                'total_lembur'     => $q->sum('lembur'),
                'total_potongan'   => $q->sum('total_potongan'),
                'avg_gaji'         => $q->avg('total_ditransfer') ?? 0,
                'avg_hadir'        => round($q->avg('hadir') ?? 0, 1),
                'total_pph'        => $q->sum('pph21'),
            ];

            $stats['karyawan_prev'] = $prevPeriod ? $prevPeriod->records()->count() : 0;
            $stats['gaji_prev']     = $prevPeriod ? $prevPeriod->records()->sum('total_ditransfer') : 0;

            $byDept = $latestPeriod->records()
                ->selectRaw('department, COUNT(*) as jumlah, SUM(total_ditransfer) as total, SUM(gaji_pokok) as total_pokok, SUM(lembur) as total_lembur')
                ->groupBy('department')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $byBank = $latestPeriod->records()
                ->selectRaw('nama_bank, COUNT(*) as jumlah, SUM(total_ditransfer) as total')
                ->groupBy('nama_bank')
                ->orderByDesc('jumlah')
                ->get();

            $topEarners = $latestPeriod->records()
                ->orderByDesc('total_ditransfer')
                ->limit(5)
                ->get(['nama', 'department', 'jabatan', 'gaji_pokok', 'lembur', 'total_ditransfer']);
        }

        $periods = SalaryPeriod::withCount('records')
            ->withSum('records', 'total_ditransfer')
            ->orderByDesc('period_start')
            ->limit(12)
            ->get();

        $monthlyTrend = SalaryPeriod::withCount('records')
            ->withSum('records', 'total_ditransfer')
            ->withSum('records', 'lembur')
            ->withSum('records', 'total_potongan')
            ->orderByDesc('period_start')
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        return view('admin.dashboard.index', compact(
            'latestPeriod', 'prevPeriod', 'totalPeriods',
            'stats', 'byDept', 'byBank', 'topEarners',
            'periods', 'monthlyTrend', 'allPeriods'
        ));
    }
}
