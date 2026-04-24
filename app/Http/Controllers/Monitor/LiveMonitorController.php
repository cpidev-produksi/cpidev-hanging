<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Models\MonitorControl;
use App\Models\PlanningLb;

class LiveMonitorController extends Controller
{
    protected function getMaxCapacity(string $location, int $lineNo): int
    {
        $custom = [
            // 'SH01' => [17 => 46],
            'SH02' => [30 => 19],
        ];

        return $custom[$location][$lineNo] ?? 50;
    }

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

        $shiftsToday = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', now('Asia/Jakarta')->toDateString())
            ->pluck('shift')
            ->filter()
            ->map(fn($s) => strtolower((string)$s))
            ->unique()
            ->values()
            ->all();

        // mapping: pagi->1, malam->3
        $hasShift1 = in_array('pagi', $shiftsToday, true);
        $hasShift3 = in_array('malam', $shiftsToday, true);

        $shiftDoneMessage = null;
        if ($hasShift1 && $hasShift3) $shiftDoneMessage = 'Shift 1 dan 3 selesai.';
        elseif ($hasShift1) $shiftDoneMessage = 'Shift 1 selesai.';
        elseif ($hasShift3) $shiftDoneMessage = 'Shift 3 selesai.';

        // Ambil total planning untuk hari ini
        $todayPlanning = PlanningLb::query()
            ->where('location', $location)
            ->whereDate('process_date', now('Asia/Jakarta')->toDateString())
            ->first();

        $totalPlanningAyam = $todayPlanning ? (int) $todayPlanning->total_plan_chicken : 0;
        $totalPlanningTruk = $todayPlanning ? (int) $todayPlanning->total_plan_truck : 0;

        $todayRegisteredTruckCount = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', now('Asia/Jakarta')->toDateString())
            ->count();

        $targetReached = ($totalPlanningTruk > 0) && ($todayRegisteredTruckCount >= $totalPlanningTruk);

        // Ambil semua MonitorControl hari ini
        $allTodayControls = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', now('Asia/Jakarta')->toDateString())
            ->with(['hangingForm.lines.sets'])
            ->get();

        // Total ayam diterima (shackle saja, TANPA dikurangi dead)
        $todayAyam = $allTodayControls->sum(function ($mc) {
            if (!$mc->hangingForm) return 0;

            $location = $mc->location ?? '';
            $totalShackle = 0;

            foreach ($mc->hangingForm->lines as $line) {
                $cap = $this->getMaxCapacity($location, (int) $line->line_no);

                foreach ($line->sets as $set) {
                    if ($set->empty_count === null) continue;
                    $totalShackle += ($cap - (int) $set->empty_count);
                }
            }

            return $totalShackle;
        });

        $todayTruckCount = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', now('Asia/Jakarta')->toDateString())
            ->whereHas('hangingForm', fn($q) => $q->whereIn('status', ['running', 'done']))
            ->whereHas('hangingForm.lines.sets', fn($q) => $q->whereNotNull('empty_count'))
            ->count();

        if (!$active || !$active->hangingForm) {
            return response()->json([
                'active' => false,
                'location' => $location,
                'today_total_ayam' => $todayAyam,
                'today_truck_count' => $todayTruckCount,
                'total_planning_ayam' => $totalPlanningAyam,
                'total_planning_truk' => $totalPlanningTruk,
                'no_process_reason' => ($totalPlanningTruk <= 0 && $totalPlanningAyam <= 0)
                    ? 'no_planning'
                    : (($targetReached && $shiftDoneMessage) ? 'target_reached' : 'no_running'),
                'shift_done_message' => ($targetReached ? $shiftDoneMessage : null),
                
                // Data kosong untuk bagian lain
                'report_code' => null,
                'total_ayam_running' => 0,
                'total_ekor' => 0,
                'truck_no' => null,
                'expedition_name' => null,
                'driver_name' => null,
                'driver_phone' => null,
                'size' => null,
                'farm_name' => null,
            ]);
        }

        $totalAyamShackle = 0;
        foreach ($active->hangingForm->lines as $line) {
            $cap = $this->getMaxCapacity($location, (int) $line->line_no);

            foreach ($line->sets as $set) {
                if ($set->empty_count === null) continue;
                $totalAyamShackle += ($cap - (int) $set->empty_count);
            }
        }

        $deadCount = (int) ($active->hangingForm->dead_count ?? 0);
        $totalAyamBersih = $totalAyamShackle;

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
            'today_total_ayam' => $todayAyam,
            'today_truck_count' => $todayTruckCount,
            
            // total planning
            'total_planning_ayam' => $totalPlanningAyam,
            'total_planning_truk' => $totalPlanningTruk,
        ]);
    }
}