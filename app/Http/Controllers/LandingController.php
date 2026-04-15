<?php

namespace App\Http\Controllers;

use App\Models\Expedition;
use App\Models\Farm;
use App\Models\MonitorControl;
use App\Models\PlateNumber;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        $locations = ['SH01', 'SH02'];

        // Master counts (sama seperti di Dashboard)
        $master = [
            'expeditions' => Expedition::count(),
            'farms' => Farm::count(),
            'plates' => class_exists(PlateNumber::class) ? PlateNumber::count() : null,
        ];

        // Grand totals (sama seperti di Dashboard)
        $grand = [
            'truk_total' => 0,
            'truk_counted' => 0,
            'truk_queue' => 0,
            'truk_running' => 0,
            'truk_done' => 0,
            'ayam_received' => 0,
        ];

        foreach ($locations as $loc) {
            $base = MonitorControl::query()
                ->where('location', $loc)
                ->whereDate('process_date', $today);

            $trukTotal = (clone $base)->count();
            $trukRunning = (clone $base)->where('status', 'running')->count();
            $trukDone = (clone $base)->where('status', 'done')->count();

            $trukCounted = (clone $base)
                ->whereHas('hangingForm.lines.sets', fn($q) => $q->whereNotNull('empty_count'))
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
                    return 50 - (int)$s->empty_count;
                });
            });

            // Grand total akumulasi
            $grand['truk_total'] += $trukTotal;
            $grand['truk_counted'] += $trukCounted;
            $grand['truk_queue'] += $trukQueue;
            $grand['truk_running'] += $trukRunning;
            $grand['truk_done'] += $trukDone;
            $grand['ayam_received'] += $ayamReceived;
        }

        return view('landing', compact('master', 'grand'));
    }
}