<?php

namespace App\Http\Controllers;

use App\Models\Expedition;
use App\Models\Farm;
use App\Models\MonitorControl;
use App\Models\PlanningLb;
use App\Models\PlateNumber;
use App\Models\ShiftCompletion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

            $ayamReceived = $mcs->sum(fn($mc) => $this->calcAyamDiterimaFromHanging($mc));

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

            $runningTotalAyam = $running ? $this->calcAyamDiterimaFromHanging($running) : 0;

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
                'ayam_received' => (int)$ayamReceived,
                'plan_truck'    => $planTruck,
                'plan_chicken'  => $planChicken,
                'running'       => $running ? [
                    'shift_label' => $shiftLabel,
                    'truck_no'    => $running->truck_no,
                    'expedition'  => $running->expedition?->name,
                    'farm'        => $running->farm?->name,
                    'plate'       => $running->plateNumber?->plate_number,
                    'total_ayam'  => (int)$runningTotalAyam,
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
            $grand['ayam_received'] += (int)$ayamReceived;
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

            $ayamDay = $mcsDay->sum(fn($mc) => $this->calcAyamDiterimaFromHanging($mc));

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
        // Rekapan per Tanggal (sesuai filter)
        // =========================
        $rekap = [];
        $startTs = strtotime($from);
        $endTs   = strtotime($to);

        for ($ts = $startTs; $ts <= $endTs; $ts += 86400) {
            $d = date('Y-m-d', $ts);

            $countedTrucksSH01 = MonitorControl::query()
                ->whereDate('process_date', $d)
                ->where('location', 'SH01')
                ->whereHas('hangingForm.lines.sets', fn ($q) => $q->whereNotNull('empty_count'))
                ->with(['expedition', 'farm', 'plateNumber', 'hangingForm.lines.sets'])
                ->orderBy('truck_no')
                ->get();

            $countedTrucksSH02 = MonitorControl::query()
                ->whereDate('process_date', $d)
                ->where('location', 'SH02')
                ->whereHas('hangingForm.lines.sets', fn ($q) => $q->whereNotNull('empty_count'))
                ->with(['expedition', 'farm', 'plateNumber', 'hangingForm.lines.sets'])
                ->orderBy('truck_no')
                ->get();

            $countedTrucks = $countedTrucksSH01->merge($countedTrucksSH02);

            // Hitung ayam SH01 + jumlah truk SH01
            $ayamTotal = $countedTrucks->sum(fn($mc) => $this->calcAyamDiterimaFromHanging($mc));
            $ayamSH01  = $countedTrucks->where('location', 'SH01')->sum(fn($mc) => $this->calcAyamDiterimaFromHanging($mc));
            $ayamSH02  = $countedTrucks->where('location', 'SH02')->sum(fn($mc) => $this->calcAyamDiterimaFromHanging($mc));
            $trukSH01 = $countedTrucksSH01->count();
            $trukSH02 = $countedTrucksSH02->count();

            // Hitung total ayam
            $ayamTotal = $ayamSH01 + $ayamSH02;

            $rekap[] = [
                'date' => $d,
                'label_long' => \Carbon\Carbon::parse($d)->translatedFormat('d F Y'),
                'ayam_received' => (int) $ayamTotal,
                'ayam_received_sh01' => (int) $ayamSH01,
                'ayam_received_sh02' => (int) $ayamSH02,
                'truk_sh01' => (int) $trukSH01,
                'truk_sh02' => (int) $trukSH02,
                'truk_counted' => (int) $countedTrucks->count(),
                'display_sh01' => $trukSH01 > 0 
                    ? number_format($ayamSH01) . ' / ' . $trukSH01 
                    : '0 / 0',
                'display_sh02' => $trukSH02 > 0 
                    ? number_format($ayamSH02) . ' / ' . $trukSH02 
                    : '0 / 0',
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
        $mode = $request->get('mode', 'single');
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
        } else {
            $from = $from ?: $today;
            $to   = $to ?: $today;
        }

        if (strtotime($from) > strtotime($to)) {
            [$from, $to] = [$to, $from];
        }

        $diffDays = (int) floor((strtotime($to) - strtotime($from)) / 86400);
        if ($diffDays > 31) {
            $to = date('Y-m-d', strtotime($from . ' +31 days'));
        }

        $mcs = MonitorControl::query()
            ->whereBetween('process_date', [$from, $to])
            ->whereHas('hangingForm.lines.sets', fn ($q) => $q->whereNotNull('empty_count'))
            ->with(['farm', 'plateNumber', 'hangingForm']) // Pastikan hangingForm di-load
            ->orderBy('process_date')
            ->orderBy('location')
            ->orderBy('truck_no')
            ->get();

        $rows = [];
        $no = 1;

        foreach ($mcs as $mc) {
            $hangingForm = $mc->hangingForm;
            
            // Hitung ayam diterima dari hanging sets
            $ayamDiterima = $this->calcAyamDiterimaFromHanging($mc);

            // Total ekor dari monitor control
            $totalEkor = (int) ($mc->total_chicken ?? 0);

            // ✅ AMBIL DARI HangingForm
            $ayamMati  = (int) ($hangingForm->dead_count ?? 0);
            $ayamRetur = (int) ($hangingForm->retur_count ?? 0);
            $totalEkor = (int) ($mc->total_chicken ?? 0);
            $targetAyam = max(0, $totalEkor - $ayamMati - $ayamRetur);

            // ✅ JAM BONGKAR: prioritaskan unloading_time dari HangingForm, fallback ke truck_arrival_time
            $jamBongkar = null;
            if ($hangingForm && $hangingForm->unloading_time) {
                $jamBongkar = $hangingForm->unloading_time instanceof \DateTime 
                    ? $hangingForm->unloading_time->format('H:i') 
                    : date('H:i', strtotime($hangingForm->unloading_time));
            } elseif ($mc->truck_arrival_time) {
                $jamBongkar = $mc->truck_arrival_time instanceof \DateTime 
                    ? $mc->truck_arrival_time->format('H:i') 
                    : date('H:i', strtotime($mc->truck_arrival_time));
            }

            // ✅ JAM SELESAI: dari finish_time di HangingForm
            $jamSelesai = null;
            if ($hangingForm && $hangingForm->finish_time) {
                $jamSelesai = $hangingForm->finish_time instanceof \DateTime 
                    ? $hangingForm->finish_time->format('H:i') 
                    : date('H:i', strtotime($hangingForm->finish_time));
            }

            $rows[] = [
                'no'            => $no++,
                'no_polisi'     => $mc->plateNumber?->plate_number ?? null,
                'jam_bongkar'   => $jamBongkar,
                'jam_selesai'   => $jamSelesai,
                'nama_farm'     => $mc->farm?->name ?? null,
                'size'          => $mc->size ?? null,
                'total_ekor'    => $totalEkor,
                'ayam_mati'     => $ayamMati,
                'ayam_retur'    => $ayamRetur,
                'ayam_diterima' => $ayamDiterima,
                // untuk export lengkap (dipakai rekap_export_pdf)
                'tanggal'       => optional($mc->process_date)->format('Y-m-d'),
                'shift'         => strtoupper((string)($mc->shift ?? '')),
                'lokasi'        => $mc->location,
                'seal_no'       => $mc->seal_no,
                'expedition'    => $mc->expedition?->name,
                'farm'          => $mc->farm?->name,
                'retur_kg'      => (float) ($hangingForm->retur_total_kg ?? 0),

                // ini penting untuk export:
                'target_ayam'   => $targetAyam,                     // DTA
                'hasil_shackle' => $ayamDiterima,                   // Ayam diterima versi show
                'selisih'       => (int)($ayamDiterima - $targetAyam),

                // QC (biar sama dengan summary)
                'qc_keranjang'  => $hangingForm->basket_condition ?? '—',
                'qc_platform'   => $hangingForm->truck_platform_condition ?? '—',
                'qc_bulu'       => $hangingForm->feather_condition ?? '—',
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
            
            $mcsToday = MonitorControl::with(['hangingForm.lines.sets'])
                ->whereDate('process_date', $today)
                ->get();
            
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
            
            $totalPlanChicken = PlanningLb::whereDate('process_date', $today)->sum('total_plan_chicken');
            $totalPlanTruck = PlanningLb::whereDate('process_date', $today)->sum('total_plan_truck');
            
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
            'SH02' => [30 => 16],
        ];

        return $custom[$location][$lineNo] ?? 50;
    }

    protected function calcAyamDiterimaFromHanging(?\App\Models\MonitorControl $mc): int
    {
        if (!$mc || !$mc->relationLoaded('hangingForm')) {
            return 0;
        }
        if (!$mc->hangingForm) {
            return 0;
        }

        // pastikan lines.sets ada
        $mc->hangingForm->loadMissing('lines.sets');

        $location = (string)($mc->location ?? '');
        $totalAyamShackle = 0;

        foreach ($mc->hangingForm->lines as $line) {
            $cap = $this->getMaxCapacity($location, (int)$line->line_no);

            foreach ($line->sets as $set) {
                if ($set->empty_count === null) continue;

                $empty = (int) $set->empty_count;
                // safety: empty tidak boleh > cap
                if ($empty > $cap) $empty = $cap;
                if ($empty < 0) $empty = 0;

                $totalAyamShackle += ($cap - $empty);
            }
        }

        return (int) $totalAyamShackle;
    }

    private function normalizeRekapRange(Request $request, string $today): array
    {
        $mode = $request->get('mode', 'single');
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
        } else {
            $from = $from ?: $today;
            $to   = $to ?: $today;
        }

        if (strtotime($from) > strtotime($to)) {
            [$from, $to] = [$to, $from];
        }

        $diffDays = (int) floor((strtotime($to) - strtotime($from)) / 86400);
        if ($diffDays > 31) {
            $to = date('Y-m-d', strtotime($from . ' +31 days'));
        }

        return [$mode, $date, $from, $to];
    }

    private function buildMonitorSummaryRow(MonitorControl $mc): array
    {
        $form = $mc->hangingForm;

        // ===== hitungan seperti summary.blade =====
        $dead   = (int) ($form->dead_count ?? 0);
        $retur  = (int) ($form->retur_count ?? 0);
        $returKg = (float) ($form->retur_total_kg ?? 0);

        $totalEkor = (int) ($mc->total_chicken ?? 0);
        $targetAyam = max(0, $totalEkor - $dead - $retur);

        // hasil shackle: cap custom mengikuti getMaxCapacity()
        $hasilShackle = 0;
        $totalKosongCalc = 0;
        $normalSetCount = 0;

        $location = (string) ($mc->location ?? '');

        if ($form && $form->relationLoaded('lines')) {
            foreach ($form->lines as $line) {
                $cap = $this->getMaxCapacity($location, (int) ($line->line_no ?? 0));

                foreach ($line->sets as $set) {
                    if ($set->empty_count === null) continue;

                    $empty = (int) $set->empty_count;
                    $empty = min($empty, $cap);

                    $totalKosongCalc += $empty;
                    $hasilShackle += ($cap - $empty);

                    if ($cap === 50 && $empty === 0) {
                        $normalSetCount++;
                    }
                }
            }
        }

        $selisih = $hasilShackle - $targetAyam;

        $status = ($selisih === 0) ? 'MATCH' : (($selisih > 0) ? 'KELEBIHAN' : 'KEKURANGAN');

        // jam
        $jamDatang = null;
        if ($mc->truck_arrival_time) {
            $jamDatang = is_string($mc->truck_arrival_time)
                ? \Carbon\Carbon::parse($mc->truck_arrival_time)->format('H:i')
                : $mc->truck_arrival_time->format('H:i');
        }

        $jamBongkar = $form?->unloading_time ? $form->unloading_time->format('H:i') : null;
        $jamSelesai = $form?->finish_time ? $form->finish_time->format('H:i') : null;

        // QC
        $basket = $form->basket_condition ?? null;
        $platform = $form->truck_platform_condition ?? null;
        $feather = $form->feather_condition ?? null;

        return [
            'tanggal' => $mc->process_date?->format('Y-m-d'),
            'shift' => $mc->shift,
            'lokasi' => $mc->location,
            'report_code' => $mc->report_code,

            'truck_no' => $mc->truck_no,
            'no_polisi' => $mc->plateNumber?->plate_number,

            'farm' => $mc->farm?->name,
            'expedition' => $mc->expedition?->name,

            'size' => $mc->size,
            'seal_no' => $mc->seal_no,

            'jam_datang' => $jamDatang,
            'jam_bongkar' => $jamBongkar,
            'jam_selesai' => $jamSelesai,

            'total_ekor' => $totalEkor,
            'total_kilo' => $mc->total_kilo,
            'abw' => $mc->abw,

            'ayam_mati' => $dead,
            'ayam_retur' => $retur,
            'retur_kg' => $returKg,

            'target_ayam' => $targetAyam,
            'hasil_shackle' => $hasilShackle,
            'shackle_kosong' => $totalKosongCalc,
            'blok_penuh_50' => $normalSetCount,

            'selisih' => $selisih,
            'status' => $status,

            'qc_keranjang' => $basket,
            'qc_platform' => $platform,
            'qc_bulu' => $feather,
        ];
    }

    // public function rekapExportExcel(Request $request)
    // {
    //     $today = date('Y-m-d');
    //     [$mode, $date, $from, $to] = $this->normalizeRekapRange($request, $today);

    //     $mcs = MonitorControl::query()
    //         ->whereBetween('process_date', [$from, $to])
    //         // "yang sudah diinput" minimal punya hangingForm
    //         ->whereHas('hangingForm')
    //         ->with([
    //             'farm',
    //             'expedition',
    //             'plateNumber',
    //             'hangingForm.lines.sets',
    //         ])
    //         ->orderBy('process_date')
    //         ->orderBy('location')
    //         ->orderBy('truck_no')
    //         ->get();

    //     $filename = "monitor-summary-list-{$from}-{$to}.xls";

    //     $headers = [
    //         'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
    //         'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    //         'Cache-Control' => 'max-age=0',
    //     ];

    //     $callback = function () use ($mcs) {
    //         $out = fopen('php://output', 'w');
    //         fwrite($out, "\xEF\xBB\xBF"); // BOM

    //         $no = 1;

    //         $cols = [
    //             'No',
    //             'Tanggal', 'Shift', 'Lokasi', 'Report Code',
    //             'Truk #', 'No Polisi',
    //             'Ekspedisi', 'Farm',
    //             'Size', 'No Segel',
    //             'Jam Datang', 'Jam Bongkar', 'Jam Selesai',
    //             'Total Ekor', 'Total Kilo', 'ABW',
    //             'Ayam Mati', 'Ayam Retur', 'Retur Kg',
    //             'Target Ayam', 'Hasil Shackle', 'Shackle Kosong', 'Blok Penuh(50)',
    //             'Selisih', 'Status',
    //             'QC Keranjang', 'QC Platform', 'QC Bulu',
    //         ];

    //         // header row TSV
    //         fwrite($out, implode("\t", $cols) . "\r\n");

    //         $clean = function ($v) {
    //             $v = (string)($v ?? '');
    //             return str_replace(["\t", "\r", "\n"], ' ', $v);
    //         };

    //         foreach ($mcs as $mc) {
    //             $row = $this->buildMonitorSummaryRow($mc);

    //             $line = [
    //                 $no++,
    //                 $clean($row['tanggal']),
    //                 $clean($row['shift']),
    //                 $clean($row['lokasi']),
    //                 $clean($row['report_code']),
    //                 $clean($row['truck_no']),
    //                 $clean($row['no_polisi']),
    //                 $clean($row['expedition']),
    //                 $clean($row['farm']),
    //                 $clean($row['size']),
    //                 $clean($row['seal_no']),
    //                 $clean($row['jam_datang']),
    //                 $clean($row['jam_bongkar']),
    //                 $clean($row['jam_selesai']),
    //                 (int)$row['total_ekor'],
    //                 $clean($row['total_kilo']),
    //                 $clean($row['abw']),
    //                 (int)$row['ayam_mati'],
    //                 (int)$row['ayam_retur'],
    //                 $clean($row['retur_kg']),
    //                 (int)$row['target_ayam'],
    //                 (int)$row['hasil_shackle'],
    //                 (int)$row['shackle_kosong'],
    //                 (int)$row['blok_penuh_50'],
    //                 (int)$row['selisih'],
    //                 $clean($row['status']),
    //                 $clean($row['qc_keranjang']),
    //                 $clean($row['qc_platform']),
    //                 $clean($row['qc_bulu']),
    //             ];

    //             fwrite($out, implode("\t", $line) . "\r\n");
    //         }

    //         fclose($out);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    public function rekapExportPdf(Request $request)
    {
        $today = date('Y-m-d');
        [$mode, $date, $from, $to] = $this->normalizeRekapRange($request, $today);

        $mcs = MonitorControl::query()
            ->whereBetween('process_date', [$from, $to])
            ->whereHas('hangingForm')
            ->with([
                'farm',
                'expedition',
                'plateNumber',
                'hangingForm.lines.sets',
            ])
            ->orderBy('process_date')
            ->orderBy('location')
            ->orderBy('truck_no')
            ->get();

        $rows = [];
        $no = 1;
        foreach ($mcs as $mc) {
            $r = $this->buildMonitorSummaryRow($mc);
            $r['no'] = $no++;
            $rows[] = $r;
        }

        $filename = "Rekap-LB-{$from}-{$to}.pdf";

        $pdf = Pdf::loadView('dashboard.rekap_export_pdf', [
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}