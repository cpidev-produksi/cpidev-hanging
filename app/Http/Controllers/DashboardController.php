<?php

namespace App\Http\Controllers;

use App\Models\Expedition;
use App\Models\Farm;
use App\Models\MonitorControl;
use App\Models\PlateNumber;
use App\Models\PlanningLb;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = date('Y-m-d');
        $locations = ['SH01', 'SH02'];

        // Master counts
        $master = [
            'expeditions' => Expedition::count(),
            'farms'       => Farm::count(),
            'plates'      => class_exists(PlateNumber::class) ? PlateNumber::count() : null,
        ];

        $statsByLoc = [];
        $grand = [
            'truk_total'      => 0,
            'truk_counted'    => 0,
            'truk_queue'      => 0,
            'truk_running'    => 0,
            'truk_done'       => 0,
            'ayam_received'   => 0,
            'plan_truck'      => 0,
            'plan_chicken'    => 0,
        ];

        foreach ($locations as $loc) {
            $base = MonitorControl::query()
                ->where('location', $loc)
                ->whereDate('process_date', $today);

            $trukTotal   = (clone $base)->count();
            $trukRunning = (clone $base)->where('status', 'running')->count();
            $trukDone    = (clone $base)->where('status', 'done')->count();

            $trukCounted = (clone $base)
                ->whereHas('hangingForm.lines.sets', fn ($q) => $q->whereNotNull('empty_count'))
                ->count();

            $trukQueue = (clone $base)
                ->whereNotIn('status', ['running', 'done'])
                ->count();

            $mcs = (clone $base)
                ->with(['hangingForm.lines.sets'])
                ->get();

            $ayamReceived = $mcs->sum(function ($mc) {
                if (!$mc->hangingForm) return 0;
                $sets = $mc->hangingForm->lines->flatMap->sets;
                return (int) $sets->sum(function ($s) {
                    if ($s->empty_count === null) return 0;
                    return 50 - (int) $s->empty_count;
                });
            });

            // Planning LB untuk lokasi & tanggal ini
            $planning = PlanningLb::where('location', $loc)
                ->whereDate('process_date', $today)
                ->first();

            $planTruck   = $planning?->total_plan_truck   ?? 0;
            $planChicken = $planning?->total_plan_chicken ?? 0;

            $running = MonitorControl::query()
                ->where('location', $loc)
                ->where('status', 'running')
                ->with(['expedition', 'farm', 'plateNumber', 'hangingForm.lines.sets'])
                ->first();

            $runningTotalAyam = 0;
            if ($running && $running->hangingForm) {
                $sets = $running->hangingForm->lines->flatMap->sets;
                $runningTotalAyam = (int) $sets->sum(function ($s) {
                    if ($s->empty_count === null) return 0;
                    return 50 - (int) $s->empty_count;
                });
            }

            $statsByLoc[$loc] = [
                'truk_total'    => $trukTotal,
                'truk_counted'  => $trukCounted,
                'truk_queue'    => $trukQueue,
                'truk_running'  => $trukRunning,
                'truk_done'     => $trukDone,
                'ayam_received' => $ayamReceived,
                'plan_truck'    => $planTruck,
                'plan_chicken'  => $planChicken,
                'running'       => $running ? [
                    'report_code' => $running->report_code,
                    'truck_no'    => $running->truck_no,
                    'expedition'  => $running->expedition?->name,
                    'farm'        => $running->farm?->name,
                    'plate'       => $running->plateNumber?->plate_number,
                    'total_ayam'  => $runningTotalAyam,
                ] : null,
            ];

            // Grand total
            $grand['truk_total']    += $trukTotal;
            $grand['truk_counted']  += $trukCounted;
            $grand['truk_queue']    += $trukQueue;
            $grand['truk_running']  += $trukRunning;
            $grand['truk_done']     += $trukDone;
            $grand['ayam_received'] += $ayamReceived;
            $grand['plan_truck']    += $planTruck;
            $grand['plan_chicken']  += $planChicken;
        }

        // ── Chart: Ayam diterima vs planning 7 hari terakhir ──
        $chartDays = 7;
        $chartData = [];
        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));

            // Ayam diterima (sum dari semua lokasi)
            $mcsDay = MonitorControl::query()
                ->whereDate('process_date', $date)
                ->with(['hangingForm.lines.sets'])
                ->get();

            $ayamDay = $mcsDay->sum(function ($mc) {
                if (!$mc->hangingForm) return 0;
                $sets = $mc->hangingForm->lines->flatMap->sets;
                return (int) $sets->sum(function ($s) {
                    if ($s->empty_count === null) return 0;
                    return 50 - (int) $s->empty_count;
                });
            });

            // Planning ayam (sum semua lokasi di tanggal itu)
            $planDay = PlanningLb::whereDate('process_date', $date)
                ->sum('total_plan_chicken');

            $chartData[] = [
                'date'         => $date,
                'label'        => date('d/m', strtotime($date)),
                'ayam'         => (int) $ayamDay,
                'plan_chicken' => (int) $planDay,
                'truk_counted' => MonitorControl::whereDate('process_date', $date)  // tambah ini
                                    ->whereHas('hangingForm.lines.sets', fn($q) => $q->whereNotNull('empty_count'))
                                    ->count(),
                'plan_truck'   => (int) PlanningLb::whereDate('process_date', $date)->sum('total_plan_truck'),
                'is_today'     => $date === $today,
            ];
        }

        return view('dashboard.index', [
            'today'      => $today,
            'master'     => $master,
            'statsByLoc' => $statsByLoc,
            'grand'      => $grand,
            'chartData'  => $chartData,
        ]);
    }
}