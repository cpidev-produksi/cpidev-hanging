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

        if (!$active || !$active->hangingForm) {
            return response()->json(['active' => false]);
        }

        $sets = $active->hangingForm->lines->flatMap->sets;

        $totalAyam = (int) $sets->sum(function ($s) {
            if ($s->empty_count === null) return 0;
            return 50 - (int) $s->empty_count;
        });

        return response()->json([
            'active' => true,
            'report_code' => $active->report_code,
            'expedition_name' => optional($active->expedition)->name,
            'plate_number' => optional($active->plateNumber)->plate_number,
            'driver_name' => optional($active->plateNumber)->driver_name,
            'driver_phone' => optional($active->plateNumber)->driver_phone,
            'size' => (float) $active->size,
            'farm_name' => optional($active->farm)->name,
            'total_ayam' => $totalAyam,
            'farm_fee_amount' => (float) $active->farm_fee_amount,
        ]);
    }
}