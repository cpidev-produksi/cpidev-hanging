<?php

namespace App\Http\Controllers;

use App\Models\MonitorControl;
use App\Models\PlanningLb;

class MenuController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        $locations = ['SH01', 'SH02'];
        
        // Hitung total ayam diterima hari ini (sama seperti di dashboard)
        $mcsToday = MonitorControl::query()
            ->whereDate('process_date', $today)
            ->with(['hangingForm.lines.sets'])
            ->get();
        
        $totalAyamDiterima = $mcsToday->sum(function ($mc) {
            if (!$mc->hangingForm) return 0;
            $sets = $mc->hangingForm->lines->flatMap->sets;
            return (int) $sets->sum(function ($s) {
                if ($s->empty_count === null) return 0;
                return 50 - (int) $s->empty_count;
            });
        });
        
        // Hitung total truk terhitung hari ini
        $totalTrukTerhitung = MonitorControl::query()
            ->whereDate('process_date', $today)
            ->whereHas('hangingForm.lines.sets', function ($q) {
                $q->whereNotNull('empty_count');
            })
            ->count();
        
        // Hitung juga target planning untuk progress bar
        $totalPlanChicken = PlanningLb::whereDate('process_date', $today)
            ->sum('total_plan_chicken');
        
        $totalPlanTruck = PlanningLb::whereDate('process_date', $today)
            ->sum('total_plan_truck');
        
        // Hitung progress percentage
        $ayamProgress = $totalPlanChicken > 0 
            ? min(round(($totalAyamDiterima / $totalPlanChicken) * 100), 100) 
            : 0;
        
        $trukProgress = $totalPlanTruck > 0 
            ? min(round(($totalTrukTerhitung / $totalPlanTruck) * 100), 100) 
            : 0;
        
        return view('menu.index', [
            'totalAyamDiterima' => $totalAyamDiterima,
            'totalTrukTerhitung' => $totalTrukTerhitung,
            'ayamProgress' => $ayamProgress,
            'trukProgress' => $trukProgress,
            'planChicken' => $totalPlanChicken,
            'planTruck' => $totalPlanTruck,
        ]);
    }
}