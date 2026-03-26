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
            ->with(['truck.expedition','farm','hangingForm.lines.sets'])
            ->latest()
            ->first();

        if (!$active || !$active->hangingForm) {
            return response()->json(['active' => false]);
        }

        $sets = $active->hangingForm->lines->flatMap->sets;
        $totalAyam = (int) $sets->sum(fn ($s) => 50 - (int)$s->empty_count);

        return response()->json([
            'active' => true,
            'report_code' => $active->report_code,
            'truck_no' => $active->truck->no_truck,
            'expedition_name' => $active->truck->expedition->name,
            'driver_name' => $active->driver_name,
            'size' => (float)$active->size,
            'farm_name' => $active->farm->name,
            'total_ayam' => $totalAyam,
            'farm_fee_amount' => (float)$active->farm_fee_amount,
        ]);
    }
}
