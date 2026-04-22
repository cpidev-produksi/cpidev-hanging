<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\ReturMatiDailyExport;
use App\Exports\ReturMatiSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\MonitorControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReturMatiRecapController extends Controller
{
    private string $tz = 'Asia/Jakarta';

    private function resolvePeriod(Request $request): array
    {
        $mode = $request->query('mode', 'daily'); // daily|monthly|range

        $today = now($this->tz)->toDateString();

        $date  = $request->query('date', $today); // YYYY-MM-DD
        $month = $request->query('month');        // YYYY-MM
        $from  = $request->query('from');         // YYYY-MM-DD
        $to    = $request->query('to');           // YYYY-MM-DD

        if ($mode === 'monthly') {
            $m = $month
                ? Carbon::createFromFormat('Y-m', $month, $this->tz)
                : Carbon::now($this->tz);

            $start = $m->copy()->startOfMonth()->toDateString();
            $end   = $m->copy()->endOfMonth()->toDateString();
        } elseif ($mode === 'range') {
            $start = $from ?: $today;
            $end   = $to ?: $today;
            if ($start > $end) [$start, $end] = [$end, $start];
        } else { // daily
            $start = $date;
            $end   = $date;
        }

        return compact('mode','date','month','from','to','start','end');
    }

    public function index(Request $request)
    {
        $p = $this->resolvePeriod($request);

        $location = $request->query('location', 'ALL'); // ALL|SH01|SH02
        $shift    = $request->query('shift', 'ALL');    // ALL|pagi|malam
        $sort     = $request->query('sort', 'truck');   // truck|location|shift

        $q = MonitorControl::query()
            ->with(['plateNumber', 'hangingForm'])
            ->whereBetween('process_date', [$p['start'], $p['end']]);

        if ($location !== 'ALL') {
            $q->where('location', $location);
        }
        if ($shift !== 'ALL') {
            $q->where('shift', $shift);
        }

        // sorting: daily bisa pilih, non-daily cukup per tanggal
        $q->orderBy('process_date');

        if ($p['mode'] === 'daily') {
            if ($sort === 'location') {
                $q->orderBy('location')
                ->orderBy('shift')
                ->orderByRaw('CAST(truck_no as UNSIGNED)');
            } elseif ($sort === 'shift') {
                $q->orderBy('shift')
                ->orderBy('location')
                ->orderByRaw('CAST(truck_no as UNSIGNED)');
            } else { // truck
                $q->orderBy('location')
                ->orderBy('shift')
                ->orderByRaw('CAST(truck_no as UNSIGNED)');
            }
        } else {
            $q->orderBy('location')->orderBy('shift')->orderByRaw('CAST(truck_no as UNSIGNED)');
        }

        $rows = $q->get();

        // ====== DAILY DETAIL ======
        $dailyDetails = collect();
        $dailyTotals  = ['dead' => 0, 'retur' => 0, 'trucks' => 0];

        if ($p['mode'] === 'daily') {
            $dailyDetails = $rows->map(function ($mc) {
                return [
                    'plate_number' => $mc->plateNumber?->plate_number ?? '—',
                    'dead_count'   => (int)($mc->hangingForm?->dead_count ?? 0),
                    'retur_count'  => (int)($mc->hangingForm?->retur_count ?? 0),
                    'shift'        => strtoupper((string)$mc->shift),
                    'location'     => $mc->location,
                    'truck_no'     => $mc->truck_no, // masih dipakai untuk urutan saja (tidak ditampilkan)
                ];
            })->values();

            $dailyTotals = [
                'dead'   => $dailyDetails->sum('dead_count'),
                'retur'  => $dailyDetails->sum('retur_count'),
                'trucks' => $dailyDetails->count(),
            ];
        }

        // ====== MONTHLY/RANGE SUMMARY (PER HARI) ======
        $byDate = $rows->groupBy(fn($mc) => $mc->process_date?->toDateString() ?? '—')
            ->map(function ($items) {
                return [
                    'dead'   => $items->sum(fn($mc) => (int)($mc->hangingForm?->dead_count ?? 0)),
                    'retur'  => $items->sum(fn($mc) => (int)($mc->hangingForm?->retur_count ?? 0)),
                    'trucks' => $items->count(),
                ];
            })
            ->sortKeys()
            ->all();

        return view('transaction.retur_mati.rekap', [
            'p' => $p,
            'location' => $location,
            'shift' => $shift,
            'sort' => $sort,
            'dailyDetails' => $dailyDetails,
            'dailyTotals' => $dailyTotals,
            'byDate' => $byDate,
        ]);
    }

    public function export(Request $request)
    {
        $p = $this->resolvePeriod($request);

        if ($p['mode'] === 'daily') {
            $date = $p['start'];
            return Excel::download(
                new ReturMatiDailyExport($date),
                "rekap-retur-mati-{$date}.xlsx"
            );
        }

        return Excel::download(
            new ReturMatiSummaryExport($p['start'], $p['end']),
            "rekap-retur-mati-{$p['start']}-sampai-{$p['end']}.xlsx"
        );
    }
}