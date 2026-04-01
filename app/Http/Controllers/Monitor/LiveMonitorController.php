<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Models\MonitorControl;

class LiveMonitorController extends Controller
{
    public function show(string $location)
    {
        abort_unless(in_array($location, ['SH01','SH02']), 404);
        return view('monitor.show', ['location' => $location]);
    }

    public function data(string $location)
    {
        abort_unless(in_array($location, ['SH01','SH02']), 404);

        $active = MonitorControl::query()
            ->where('location', $location)
            ->where('status', 'running')
            ->with(['expedition','plateNumber','farm','hangingForm.lines.sets'])
            ->first();

        // Counter harian (semua status, berdasarkan process_date hari ini)
        $todayAyam = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', date('Y-m-d'))
            ->with(['hangingForm.lines.sets'])
            ->get()
            ->sum(function ($mc) {
                if (!$mc->hangingForm) return 0;
                $sets = $mc->hangingForm->lines->flatMap->sets;
                return (int) $sets->sum(function ($s) {
                    if ($s->empty_count === null) return 0;
                    return 50 - (int)$s->empty_count;
                });
            });

        $todayTruckCount = (int) MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', date('Y-m-d'))
            ->count();

        if (!$active || !$active->hangingForm) {
            return response()->json([
                'active' => false,
                'location' => $location,
                'today_total_ayam' => (int) $todayAyam,
                'today_truck_count' => $todayTruckCount,
            ]);
        }

        $sets = $active->hangingForm->lines->flatMap->sets;

        // $totalAyamRunning = (int) $sets->sum(function ($s) {
        //     if ($s->empty_count === null) return 0;
        //     return 50 - (int) $s->empty_count;
        // });

        $deadCount = (int) ($active->hangingForm->dead_count ?? 0);

        $totalAyamShackle = (int) $sets->sum(function ($s) {
            if ($s->empty_count === null) return 0;
            return 50 - (int) $s->empty_count;
        });

        $totalAyamBersih = max(0, $totalAyamShackle - $deadCount);

        return response()->json([
            'active' => true,
            'location' => $location,

            // header
            'report_code' => $active->report_code,

            // main
            'total_ayam_running' => $totalAyamBersih,
            'dead_count' => $deadCount,
            'total_ekor' => (int) ($active->total_chicken ?? 0),
            'truck_no' => (int) ($active->truck_no ?? 0),

            // sub main
            'expedition_name' => optional($active->expedition)->name,
            'driver_name' => optional($active->plateNumber)->driver_name,
            'driver_phone' => optional($active->plateNumber)->driver_phone,
            'size' => (string) ($active->size ?? ''),
            'farm_name' => optional($active->farm)->name,

            // footer counters (hari ini)
            'today_total_ayam' => (int) $todayAyam,
            'today_truck_count' => $todayTruckCount,
        ]);
    }
}