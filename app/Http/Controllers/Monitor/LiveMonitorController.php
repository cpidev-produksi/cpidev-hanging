<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Models\MonitorControl;
use App\Models\PlanningLb;
use App\Models\ShiftCompletion;

class LiveMonitorController extends Controller
{
    protected function getMaxCapacity(string $location, int $lineNo): int
    {
        $custom = [
            // 'SH01' => [17 => 46],
            'SH02' => [30 => 16],
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

        $today = now('Asia/Jakarta')->toDateString();

        $shiftCompletions = ShiftCompletion::where('location', $location)
        ->whereDate('process_date', $today)
        ->get();

        $completedShifts = [];
        foreach ($shiftCompletions as $sc) {
            $completedShifts[] = $sc->shift;
        }

        $hasShift1Completed = in_array('pagi', $completedShifts);
        $hasShift3Completed = in_array('malam', $completedShifts);

        // Shift message berdasarkan completion
        $shiftDoneMessage = null;
        if ($hasShift1Completed && $hasShift3Completed) {
            $shiftDoneMessage = 'Shift 1 dan 3 selesai.';
        } elseif ($hasShift1Completed) {
            $shiftDoneMessage = 'Shift 1 selesai.';
        } elseif ($hasShift3Completed) {
            $shiftDoneMessage = 'Shift 3 selesai.';
        }

        // Ambil total planning untuk hari ini
        $todayPlanning = PlanningLb::query()
            ->where('location', $location)
            ->whereDate('process_date', $today)
            // ->whereDate('process_date', now('Asia/Jakarta')->toDateString())
            ->first();

        $totalPlanningAyam = $todayPlanning ? (int) $todayPlanning->total_plan_chicken : 0;
        $totalPlanningTruk = $todayPlanning ? (int) $todayPlanning->total_plan_truck : 0;

        $todayRegisteredTruckCount = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', $today)
            // ->whereDate('process_date', now('Asia/Jakarta')->toDateString())
            ->count();

        $targetReached = ($totalPlanningTruk > 0) && ($todayRegisteredTruckCount >= $totalPlanningTruk);

        // Ambil semua MonitorControl hari ini
        $allTodayControls = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', $today)
            //->whereDate('process_date', now('Asia/Jakarta')->toDateString())
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

        $hasAnyShiftCompleted = $hasShift1Completed || $hasShift3Completed;
        $isNoProcess = (!$active || !$active->hangingForm);
        $showShiftCompleteBanner = $isNoProcess && $hasAnyShiftCompleted && $shiftDoneMessage;

        $allHangingForms = $allTodayControls
            ->filter(fn($mc) => $mc->hangingForm !== null)
            ->map(fn($mc) => $mc->hangingForm);

        // Jam mulai = unloading_time terkecil; jam selesai = finish_time terbesar
        $unloadingTimes = $allHangingForms
            ->filter(fn($hf) => $hf->unloading_time !== null)
            ->map(fn($hf) => $hf->unloading_time instanceof \Carbon\Carbon
                ? $hf->unloading_time->format('H:i')
                : \Carbon\Carbon::parse($hf->unloading_time)->format('H:i'));

        $finishTimes = $allHangingForms
            ->filter(fn($hf) => $hf->finish_time !== null)
            ->map(fn($hf) => $hf->finish_time instanceof \Carbon\Carbon
                ? $hf->finish_time->format('H:i')
                : \Carbon\Carbon::parse($hf->finish_time)->format('H:i'));

        $shiftStartTime  = $unloadingTimes->isNotEmpty() ? $unloadingTimes->min() : null;
        $shiftFinishTime = $finishTimes->isNotEmpty()    ? $finishTimes->max()    : null;

        $totalDeadCount   = $allHangingForms->sum(fn($hf) => (int)   ($hf->dead_count     ?? 0));
        $totalReturCount  = $allHangingForms->sum(fn($hf) => (int)   ($hf->retur_count    ?? 0));
        $totalReturWeight = $allHangingForms->sum(fn($hf) => (float) ($hf->retur_total_kg ?? 0));

        if (!$active || !$active->hangingForm) {
            return response()->json([
                'active' => false,
                'location' => $location,
                'today_total_ayam' => $todayAyam,
                'today_truck_count' => $todayTruckCount,
                'total_planning_ayam' => $totalPlanningAyam,
                'total_planning_truk' => $totalPlanningTruk,
                'no_process_reason' => $showShiftCompleteBanner ? 'target_reached' : 'no_running',
                'shift_done_message' => $showShiftCompleteBanner ? $shiftDoneMessage : null,

                // Shift summary infographic
                'shift_start_time'   => $shiftStartTime,
                'shift_finish_time'  => $shiftFinishTime,
                'total_dead_count'   => $totalDeadCount,
                'total_retur_count'  => $totalReturCount,
                'total_retur_weight' => $totalReturWeight,
                'process_date'       => $today,

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