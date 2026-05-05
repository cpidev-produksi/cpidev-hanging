<?php

namespace App\Http\Controllers;

use App\Models\Expedition;
use App\Models\Farm;
use App\Models\MonitorControl;
use App\Models\PlanningLb;
use App\Models\PlateNumber;
use App\Models\ShiftCompletion;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = date('Y-m-d');
        $locations = ['SH01', 'SH02'];

        // =========================
        // Filter Rekapan (single / last7 / range)
        // =========================
        $mode = $request->get('rekap_mode', 'last7'); // single|last7|range
        $date = $request->get('rekap_date', $today);
        $from = $request->get('rekap_from', date('Y-m-d', strtotime('-6 days')));
        $to   = $request->get('rekap_to', $today);

        // normalize & safety
        $mode = in_array($mode, ['single', 'last7', 'range'], true) ? $mode : 'last7';

        if ($mode === 'single') {
            $from = $date ?: $today;
            $to   = $date ?: $today;
        } elseif ($mode === 'last7') {
            $from = date('Y-m-d', strtotime('-6 days'));
            $to   = $today;
        } else { // range
            $from = $from ?: $today;
            $to   = $to ?: $today;
        }

        // swap if inverted
        if (strtotime($from) > strtotime($to)) {
            [$from, $to] = [$to, $from];
        }

        // max 31 days
        $diffDays = (int) floor((strtotime($to) - strtotime($from)) / 86400);
        if ($diffDays > 31) {
            $to = date('Y-m-d', strtotime($from . ' +31 days'));
        }

        // Master counts
        $master = [
            'expeditions' => Expedition::count(),
            'farms'       => Farm::count(),
            'plates'      => class_exists(PlateNumber::class) ? PlateNumber::count() : null,
        ];

        // =========================
        // Existing: stats hari ini per lokasi + grand
        // =========================
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
                $locx = $running->location ?? '';

                foreach ($running->hangingForm->lines as $line) {
                    $cap = $this->getMaxCapacity($locx, (int) $line->line_no);

                    foreach ($line->sets as $set) {
                        if ($set->empty_count === null) continue;
                        $runningTotalAyam += ($cap - (int) $set->empty_count);
                    }
                }
            }

            $shiftLabel = null;
            if ($running) {
                $shiftRaw = strtolower((string) ($running->shift ?? ''));

                $shiftLabel = ($shiftRaw === 'pagi') ? 'Shift 1'
                    : (($shiftRaw === 'malam') ? 'Shift 3' : strtoupper((string) ($running->shift ?? '')));
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
                    'shift_label' => $shiftLabel,
                    'truck_no'    => $running->truck_no,
                    'expedition'  => $running->expedition?->name,
                    'farm'        => $running->farm?->name,
                    'plate'       => $running->plateNumber?->plate_number,
                    'total_ayam'  => $runningTotalAyam,
                ] : null,
            ];

            $shiftCompletions = ShiftCompletion::where('location', $loc)
                ->whereDate('process_date', $today)
                ->get();

            $completedShifts = [];
            foreach ($shiftCompletions as $sc) {
                $completedShifts[] = $sc->shift;
            }

            $statsByLoc[$loc]['completed_shifts'] = $completedShifts;
            $statsByLoc[$loc]['shift_completion_message'] = null;

            if (in_array('pagi', $completedShifts) && in_array('malam', $completedShifts)) {
                $statsByLoc[$loc]['shift_completion_message'] = 'Shift 1 dan 3 selesai.';
            } elseif (in_array('pagi', $completedShifts)) {
                $statsByLoc[$loc]['shift_completion_message'] = 'Shift 1 selesai.';
            } elseif (in_array('malam', $completedShifts)) {
                $statsByLoc[$loc]['shift_completion_message'] = 'Shift 3 selesai.';
            }

            $grand['truk_total']    += $trukTotal;
            $grand['truk_counted']  += $trukCounted;
            $grand['truk_queue']    += $trukQueue;
            $grand['truk_running']  += $trukRunning;
            $grand['truk_done']     += $trukDone;
            $grand['ayam_received'] += $ayamReceived;
            $grand['plan_truck']    += $planTruck;
            $grand['plan_chicken']  += $planChicken;
        }

        // =========================
        // Existing: chart 7 hari terakhir
        // =========================
        $chartDays = 7;
        $chartData = [];
        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $datex = date('Y-m-d', strtotime("-{$i} days"));

            $mcsDay = MonitorControl::query()
                ->whereDate('process_date', $datex)
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

            $planDay = PlanningLb::whereDate('process_date', $datex)
                ->sum('total_plan_chicken');

            $chartData[] = [
                'date'         => $datex,
                'label'        => date('d/m', strtotime($datex)),
                'ayam'         => (int) $ayamDay,
                'plan_chicken' => (int) $planDay,
                'truk_counted' => MonitorControl::whereDate('process_date', $datex)
                    ->whereHas('hangingForm.lines.sets', fn($q) => $q->whereNotNull('empty_count'))
                    ->count(),
                'plan_truck'   => (int) PlanningLb::whereDate('process_date', $datex)->sum('total_plan_truck'),
                'is_today'     => $datex === $today,
            ];
        }

        // =========================
        // NEW: Rekapan per Tanggal (sesuai filter)
        // - total ayam diterima (sum semua truk di tanggal tsb)
        // - jumlah truk terhitung
        // - daftar truk terhitung (detail MonitorControl + relasi)
        // =========================
        $rekap = [];
        $startTs = strtotime($from);
        $endTs   = strtotime($to);

        for ($ts = $startTs; $ts <= $endTs; $ts += 86400) {
            $d = date('Y-m-d', $ts);

            $countedTrucks = MonitorControl::query()
                ->whereDate('process_date', $d)
                ->whereHas('hangingForm.lines.sets', fn ($q) => $q->whereNotNull('empty_count'))
                ->with(['expedition', 'farm', 'plateNumber', 'hangingForm.lines.sets'])
                ->orderBy('location')
                ->orderBy('truck_no')
                ->get();

            $ayamTotal = $countedTrucks->sum(function ($mc) {
                if (!$mc->hangingForm) return 0;
                $sets = $mc->hangingForm->lines->flatMap->sets;
                return (int) $sets->sum(function ($s) {
                    if ($s->empty_count === null) return 0;
                    return 50 - (int) $s->empty_count;
                });
            });

            $rekap[] = [
                'date' => $d,
                'label_long' => \Carbon\Carbon::parse($d)->translatedFormat('d F Y'),
                'ayam_received' => (int) $ayamTotal,
                'truk_counted' => (int) $countedTrucks->count(),
                'trucks' => $countedTrucks,
            ];
        }

        return view('dashboard.index', [
            'today'      => $today,
            'master'     => $master,
            'statsByLoc' => $statsByLoc,
            'grand'      => $grand,
            'chartData'  => $chartData,

            // NEW
            'rekap'      => $rekap,
            'rekapFilter' => [
                'mode' => $mode,
                'date' => $date,
                'from' => $from,
                'to'   => $to,
            ],
        ]);
    }

    public function rekap(Request $request)
    {
        $today = date('Y-m-d');

        // mode single/last7/range
        $mode = $request->get('mode', 'single'); // default biar cocok klik dari dashboard
        $date = $request->get('date', $today);
        $from = $request->get('from', date('Y-m-d', strtotime('-6 days')));
        $to   = $request->get('to', $today);

        $mode = in_array($mode, ['single', 'last7', 'range'], true) ? $mode : 'single';

        if ($mode === 'single') {
            $from = $date ?: $today;
            $to   = $date ?: $today;
        } elseif ($mode === 'last7') {
            $from = date('Y-m-d', strtotime('-6 days'));
            $to   = $today;
        } else { // range
            $from = $from ?: $today;
            $to   = $to ?: $today;
        }

        if (strtotime($from) > strtotime($to)) {
            [$from, $to] = [$to, $from];
        }

        // max 31 days
        $diffDays = (int) floor((strtotime($to) - strtotime($from)) / 86400);
        if ($diffDays > 31) {
            $to = date('Y-m-d', strtotime($from . ' +31 days'));
        }

        $mcs = MonitorControl::query()
            ->whereBetween('process_date', [$from, $to])
            ->whereHas('hangingForm.lines.sets', fn ($q) => $q->whereNotNull('empty_count'))
            ->with(['farm', 'plateNumber', 'hangingForm.lines.sets'])
            ->orderBy('process_date')
            ->orderBy('location')
            ->orderBy('truck_no')
            ->get();

        // build rows
        $rows = [];
        $no = 1;

        foreach ($mcs as $mc) {
            // ayam diterima dari hanging sets
            $ayamDiterima = 0;
            if ($mc->hangingForm) {
                $sets = $mc->hangingForm->lines->flatMap->sets;
                $ayamDiterima = (int) $sets->sum(function ($s) {
                    if ($s->empty_count === null) return 0;
                    return 50 - (int) $s->empty_count;
                });
            }

            // total ekor (dari monitor control)
            $totalEkor = (int) ($mc->total_chicken ?? 0);

            // ayam mati & retur:
            // GANTI nama field jika di DB Anda beda
            $ayamMati  = (int) ($mc->dead_chicken ?? ($mc->ayam_mati ?? 0));
            $ayamRetur = (int) ($mc->return_chicken ?? ($mc->ayam_retur ?? 0));

            // jam bongkar & selesai (format H:i jika ada)
            $jamBongkar = $mc->truck_arrival_time ? $mc->truck_arrival_time->format('H:i') : null;

            // asumsi jam selesai dari supervisor_signed_at (ubah jika ada field lain)
            $jamSelesai = $mc->supervisor_signed_at ? $mc->supervisor_signed_at->format('H:i') : null;

            $rows[] = [
                'no'           => $no++,
                'no_polisi'    => $mc->plateNumber?->plate_number ?? null,
                'jam_bongkar'  => $jamBongkar,
                'jam_selesai'  => $jamSelesai,
                'nama_farm'    => $mc->farm?->name ?? null,
                'size'         => $mc->size ?? null,
                'total_ekor'   => $totalEkor,
                'ayam_mati'    => $ayamMati,
                'ayam_retur'   => $ayamRetur,
                'ayam_diterima'=> $ayamDiterima,
                // optional kalau mau debugging:
                'process_date' => optional($mc->process_date)->format('Y-m-d'),
                'truck_no'     => $mc->truck_no,
                'location'     => $mc->location,
            ];
        }

        return view('dashboard.rekap', [
            'today' => $today,
            'filter' => [
                'mode' => $mode,
                'date' => $date,
                'from' => $from,
                'to'   => $to,
            ],
            'rows' => $rows,
        ]);
    }

    public function todayStats()
    {
        try {
            $today = date('Y-m-d');
            
            // Ambil semua MonitorControl hari ini
            $mcsToday = MonitorControl::with(['hangingForm.lines.sets'])
                ->whereDate('process_date', $today)
                ->get();
            
            // Hitung total ayam diterima
            $totalAyamDiterima = 0;
            $totalTrukTerhitung = 0;
            
            foreach ($mcsToday as $mc) {
                $hasCounting = false;
                $ayamPerTruk = 0;
                
                if ($mc->hangingForm && $mc->hangingForm->lines) {
                    foreach ($mc->hangingForm->lines as $line) {
                        if ($line->sets && count($line->sets) > 0) {
                            foreach ($line->sets as $set) {
                                if ($set->empty_count !== null) {
                                    $hasCounting = true;
                                    // Asumsikan kapasitas 50 per cage
                                    $ayamPerTruk += (50 - (int)$set->empty_count);
                                }
                            }
                        }
                    }
                }
                
                if ($hasCounting) {
                    $totalTrukTerhitung++;
                    $totalAyamDiterima += $ayamPerTruk;
                }
            }
            
            // Hitung planning
            $totalPlanChicken = PlanningLb::whereDate('process_date', $today)
                ->sum('total_plan_chicken');
            
            $totalPlanTruck = PlanningLb::whereDate('process_date', $today)
                ->sum('total_plan_truck');
            
            return response()->json([
                'success' => true,
                'ayam_received' => $totalAyamDiterima,
                'truk_counted' => $totalTrukTerhitung,
                'plan_chicken' => (int)$totalPlanChicken,
                'plan_truck' => (int)$totalPlanTruck,
                'ayam_progress' => $totalPlanChicken > 0 
                    ? round(($totalAyamDiterima / $totalPlanChicken) * 100, 1) 
                    : 0,
                'truk_progress' => $totalPlanTruck > 0 
                    ? round(($totalTrukTerhitung / $totalPlanTruck) * 100, 1) 
                    : 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function getMaxCapacity(string $location, int $lineNo): int
    {
        $custom = [
            'SH02' => [30 => 13],
        ];

        return $custom[$location][$lineNo] ?? 50;
    }
}