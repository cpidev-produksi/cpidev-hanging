<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitorControl;
use Illuminate\Http\Request;

class Esp32MonitorController extends Controller
{
    public function show(Request $request)
    {
        $location = 'SH02';
        $today = now()->toDateString();

        $format = function (?MonitorControl $mc) {
            if (!$mc) return null;
            return [
                'plate_number' => $mc->plateNumber->plate_number ?? null,
                'expedition'   => $mc->expedition->name ?? null,
                'size'         => $mc->size,
            ];
        };

        $running = MonitorControl::with(['expedition', 'plateNumber'])
            ->where('location', $location)
            ->whereDate('process_date', $today)
            ->where('status', 'running')
            ->orderBy('truck_no')
            ->first();

        $queueQuery = MonitorControl::with(['expedition', 'plateNumber'])
            ->where('location', $location)
            ->whereDate('process_date', $today)
            ->where('status', 'draft')
            ->orderBy('truck_no');

        if ($running) {
            $queueQuery->where('truck_no', '>', $running->truck_no);
        }

        $queue = $queueQuery->take(2)->get();

        return response()->json([
            'location' => $location,
            'date'     => $today,
            'running'  => $format($running),
            'queue'    => $queue->map($format)->values(),
        ]);
    }
}