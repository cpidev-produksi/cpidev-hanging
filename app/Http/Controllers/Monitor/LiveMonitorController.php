<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Models\MonitorControl;
use App\Models\PlanningLb;
use App\Models\ShiftCompletion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiveMonitorController extends Controller
{
    protected function getMaxCapacity(string $location, int $lineNo): int
    {
        $custom = [
            // 'SH01' => [17 => 46],
            'SH02' => [30 => 17],
        ];

        return $custom[$location][$lineNo] ?? 50;
    }

    protected function getJetsonStats(string $location): array
    {
        $empty = [
            'today_ayam' => null,
            'current_batch_count' => null,
            'today_total_count' => null,
            'today_total_batches' => null,
            'is_fresh' => false,
        ];

        if ($location !== 'SH02') {
            return $empty;
        }

        $cacheKey = "jetson-stats-{$location}";
        $lastValueKey = "jetson-stats-{$location}-last";

        // Cache duration: 5 detik (increased from 2s)
        return Cache::remember($cacheKey, 5, function () use ($location, $empty, $lastValueKey) {
            $base = config('services.jetson_counter.url');

            try {
                $startTime = microtime(true);

                // ─── API Call 1: Summary ───
                $summary = Http::timeout(5)  // ← Increased from 2s to 5s
                    ->get("{$base}/api/summary")
                    ->throw()
                    ->json();

                $durationMs = (microtime(true) - $startTime) * 1000;
                Log::info("Jetson API /summary success", [
                    'duration_ms' => round($durationMs, 2),
                    'location' => $location,
                    'has_today_key' => isset($summary['today']),
                    'today_keys' => isset($summary['today']) ? array_keys($summary['today']) : [],
                ]);

                // Validate structure
                if (!is_array($summary) || !isset($summary['today']) || !is_array($summary['today'])) {
                    Log::error('Invalid Jetson summary structure', [
                        'location' => $location,
                        'response_type' => gettype($summary),
                        'response' => is_array($summary) ? $summary : (string)$summary,
                    ]);
                    // Use fallback
                    $lastValue = Cache::get($lastValueKey);
                    if ($lastValue) {
                        $lastValue['is_fresh'] = false;
                        Log::warning("Using fallback cache for summary", ['location' => $location]);
                        return $lastValue;
                    }
                    return $empty;
                }

                $closedToday = (int) data_get($summary, 'today.total_count', 0);
                $todayBatches = (int) data_get($summary, 'today.total_batches', 0);

                // ─── API Call 2: Current Batch ───
                $current = Http::timeout(5)  // ← Increased from 2s to 5s
                    ->get("{$base}/api/current-batch")
                    ->throw()
                    ->json();

                $durationMs = (microtime(true) - $startTime) * 1000;
                Log::info("Jetson API /current-batch success", [
                    'duration_ms' => round($durationMs, 2),
                    'location' => $location,
                    'success_value' => $current['success'] ?? null,
                    'has_count' => isset($current['count']),
                    'count_value' => $current['count'] ?? null,
                ]);

                // Validate structure & extract live count
                $liveCount = 0;
                if (is_array($current) && isset($current['success']) && $current['success'] === true) {
                    if (isset($current['count'])) {
                        $liveCount = (int) $current['count'];
                    }
                } else {
                    Log::warning('Jetson batch not in running/success state', [
                        'location' => $location,
                        'success' => $current['success'] ?? 'MISSING',
                        'has_count' => isset($current['count']),
                    ]);
                }

                // Build result
                $result = [
                    'today_ayam' => $closedToday + $liveCount,
                    'current_batch_count' => $liveCount,
                    'today_total_count' => $closedToday,
                    'today_total_batches' => $todayBatches,
                    'is_fresh' => true,  // ← Flag bahwa data fresh dari API
                ];

                // Simpan untuk fallback jika ada error berikutnya
                Cache::put($lastValueKey, $result, now()->addHours(1));

                Log::info("Jetson stats fetched successfully", [
                    'location' => $location,
                    'today_ayam' => $result['today_ayam'],
                    'current_batch' => $liveCount,
                    'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]);

                return $result;

            } catch (\Illuminate\Http\Client\RequestException $e) {
                $durationMs = (microtime(true) - $startTime) * 1000;
                Log::warning("Jetson API request failed", [
                    'location' => $location,
                    'exception' => $e::class,
                    'status_code' => $e->response?->status(),
                    'message' => $e->getMessage(),
                    'duration_ms' => round($durationMs, 2),
                ]);

                // Fallback to last known value
                $lastValue = Cache::get($lastValueKey);
                if ($lastValue) {
                    $lastValue['is_fresh'] = false;  // ← Flag bahwa data cached
                    Log::notice("Using fallback cache (request exception)", [
                        'location' => $location,
                        'last_known_ayam' => $lastValue['today_ayam'],
                    ]);
                    return $lastValue;
                }

                return $empty;

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $durationMs = (microtime(true) - $startTime) * 1000;
                Log::warning("Jetson API connection failed", [
                    'location' => $location,
                    'message' => $e->getMessage(),
                    'duration_ms' => round($durationMs, 2),
                ]);

                // Fallback to last known value
                $lastValue = Cache::get($lastValueKey);
                if ($lastValue) {
                    $lastValue['is_fresh'] = false;
                    Log::notice("Using fallback cache (connection exception)", ['location' => $location]);
                    return $lastValue;
                }

                return $empty;

            } catch (\Throwable $e) {
                $durationMs = (microtime(true) - $startTime) * 1000;
                Log::error("Jetson API unexpected error", [
                    'location' => $location,
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'duration_ms' => round($durationMs, 2),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Fallback to last known value
                $lastValue = Cache::get($lastValueKey);
                if ($lastValue) {
                    $lastValue['is_fresh'] = false;
                    Log::notice("Using fallback cache (unexpected error)", ['location' => $location]);
                    return $lastValue;
                }

                return $empty;
            }
        });
    }

    public function show(string $location)
    {
        abort_unless(in_array($location, ['SH01','SH02']), 404);
        return view('monitor.show', ['location' => $location]);
    }

    public function data(string $location)
    {
        abort_unless(in_array($location, ['SH01','SH02']), 404);
        $jetsonStats = $this->getJetsonStats($location);
        $jetsonAyam = $jetsonStats['today_ayam'];

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
            ->first();

        $totalPlanningAyam = $todayPlanning ? (int) $todayPlanning->total_plan_chicken : 0;
        $totalPlanningTruk = $todayPlanning ? (int) $todayPlanning->total_plan_truck : 0;

        $todayRegisteredTruckCount = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', $today)
            ->count();

        $targetReached = ($totalPlanningTruk > 0) && ($todayRegisteredTruckCount >= $totalPlanningTruk);

        // Ambil semua MonitorControl hari ini
        $allTodayControls = MonitorControl::query()
            ->where('location', $location)
            ->whereDate('process_date', $today)
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
                'total_ayam_jetson' => $jetsonAyam,
                'jetson_current_batch_count' => $jetsonStats['current_batch_count'],
                'jetson_today_total_count' => $jetsonStats['today_total_count'],
                'jetson_today_total_batches' => $jetsonStats['today_total_batches'],
                'jetson_is_fresh' => $jetsonStats['is_fresh'],  // ← Add flag
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
            'total_ayam_jetson' => $jetsonAyam,
            'jetson_current_batch_count' => $jetsonStats['current_batch_count'],
            'jetson_today_total_count' => $jetsonStats['today_total_count'],
            'jetson_today_total_batches' => $jetsonStats['today_total_batches'],
            'jetson_is_fresh' => $jetsonStats['is_fresh'],  // ← Add flag
            
            // total planning
            'total_planning_ayam' => $totalPlanningAyam,
            'total_planning_truk' => $totalPlanningTruk,
        ]);
    }
}