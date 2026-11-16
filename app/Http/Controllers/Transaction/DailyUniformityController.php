<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DailyUniformity;
use App\Models\DailyUniformityWeight;
use App\Models\MonitorControl;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DailyUniformityController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');
        if ($date === null || $date === '') {
            $date = now()->toDateString();
        }
        $shift = $request->query('shift', 'all');
        if (!in_array($shift, ['all', 'pagi', 'malam'], true)) {
            $shift = 'all';
        }

        $items = DailyUniformity::query()
            ->with(['monitorControl.farm', 'monitorControl.expedition', 'monitorControl.plateNumber', 'weights'])
            ->whereDate('process_date', $date)
            ->when($shift !== 'all', fn ($query) => $query->where('shift', $shift))
            ->orderBy('location')
            ->orderBy('shift')
            ->get()
            ->map(function (DailyUniformity $du) {
                $du->summary_data = $du->summary();
                return $du;
            });

        $aggregate = $this->buildAggregate($items);
        $dashboard = $this->buildDashboard(Carbon::parse($date)->year, $shift);

        return view('transaction.daily_uniformities.index', [
            'items' => $items,
            'date' => $date,
            'shift' => $shift,
            'aggregate' => $aggregate,
            'dashboard' => $dashboard,
        ]);
    }

    public function create(Request $request)
    {
        $processDate = $request->query('process_date')
            ?: $request->old('process_date')
            ?: now()->toDateString();
        $shift = $request->query('shift', $request->old('shift', 'all'));
        if (!in_array($shift, ['all', 'pagi', 'malam'], true)) {
            $shift = 'all';
        }

        $monitorControls = MonitorControl::query()
            ->whereNotNull('sppa_no')
            ->where('sppa_no', '!=', '')
            ->whereDate('process_date', $processDate)
            ->when($shift !== 'all', fn ($query) => $query->where('shift', $shift))
            ->whereDoesntHave('dailyUniformity')
            ->with(['farm', 'expedition', 'plateNumber'])
            ->orderBy('truck_no')
            ->orderByDesc('process_date')
            ->get();

        return view('transaction.daily_uniformities.create', compact('monitorControls', 'processDate', 'shift'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'monitor_control_id' => [
                'required',
                Rule::exists('monitor_controls', 'id'),
                Rule::unique('daily_uniformities', 'monitor_control_id'),
            ],
            'avg_rpa' => ['nullable', 'numeric', 'min:0'],
            'berat_rpa' => ['nullable', 'numeric', 'min:0'],
        ], [
            'monitor_control_id.required' => 'No. SPPA wajib dipilih.',
            'monitor_control_id.unique' => 'Truk/SPPA ini sudah memiliki laporan Daily Uniformity.',
        ]);

        $monitorControl = MonitorControl::findOrFail($data['monitor_control_id']);

        $daily = DailyUniformity::create([
            'monitor_control_id' => $monitorControl->id,
            'process_date' => $monitorControl->process_date,
            'shift' => $monitorControl->shift,
            'location' => $monitorControl->location,
            'avg_rpa' => $data['avg_rpa'] ?? null,
            'berat_rpa' => $data['berat_rpa'] ?? null,
        ]);

        return redirect()
            ->route('daily-uniformities.edit', $daily)
            ->with('status', 'Laporan Daily Uniformity dibuat. Silakan input berat sampling.');
    }

    public function show(DailyUniformity $dailyUniformity)
    {
        $dailyUniformity->load(['monitorControl.farm', 'monitorControl.expedition', 'monitorControl.plateNumber', 'monitorControl.hangingForm.lines.sets', 'weights']);

        return view('transaction.daily_uniformities.show', [
            'daily' => $dailyUniformity,
            'summary' => $dailyUniformity->summary(),
        ]);
    }

    public function edit(DailyUniformity $dailyUniformity)
    {
        $dailyUniformity->load(['monitorControl.farm', 'monitorControl.expedition', 'monitorControl.plateNumber', 'monitorControl.hangingForm.lines.sets', 'weights']);

        return view('transaction.daily_uniformities.edit', [
            'daily' => $dailyUniformity,
            'summary' => $dailyUniformity->summary(),
        ]);
    }

    public function update(Request $request, DailyUniformity $dailyUniformity)
    {
        $data = $request->validate([
            'avg_rpa' => ['nullable', 'numeric', 'min:0'],
            'berat_rpa' => ['nullable', 'numeric', 'min:0'],
        ]);

        $dailyUniformity->update($data);

        return back()->with('status', 'Rata-rata RPA berhasil diupdate.');
    }

    public function destroy(DailyUniformity $dailyUniformity)
    {
        $date = $dailyUniformity->process_date->toDateString();

        $dailyUniformity->delete(); // weights ikut terhapus (cascade)

        return redirect()
            ->route('daily-uniformities.index', ['date' => $date])
            ->with('status', 'Laporan Daily Uniformity dihapus.');
    }

    // ================= Export =================

    public function exportPdf(Request $request)
    {
        $date = $request->query('date');
        if ($date === null || $date === '') {
            $date = now()->toDateString();
        }
        $shift = $request->query('shift', 'all');
        if (!in_array($shift, ['all', 'pagi', 'malam'], true)) {
            $shift = 'all';
        }

        $items = DailyUniformity::query()
            ->with(['monitorControl.farm', 'monitorControl.expedition', 'monitorControl.plateNumber', 'monitorControl.hangingForm.lines.sets', 'weights'])
            ->whereDate('process_date', $date)
            ->when($shift !== 'all', fn ($query) => $query->where('shift', $shift))
            ->orderBy('location')
            ->orderBy('shift')
            ->get()
            ->map(function (DailyUniformity $du) {
                $du->summary_data = $du->summary();
                return $du;
            });

        $aggregate = $this->buildAggregate($items);

        $pdf = Pdf::loadView('transaction.daily_uniformities.pdf', [
            'items' => $items,
            'date' => $date,
            'aggregate' => $aggregate,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('daily-uniformity-' . $date . '.pdf');
    }

    public function exportSinglePdf(DailyUniformity $dailyUniformity)
    {
        $dailyUniformity->load(['monitorControl.farm', 'monitorControl.expedition', 'monitorControl.plateNumber', 'monitorControl.hangingForm.lines.sets', 'weights']);
        $dailyUniformity->summary_data = $dailyUniformity->summary();

        $items = collect([$dailyUniformity]);
        $pdf = Pdf::loadView('transaction.daily_uniformities.pdf', [
            'items' => $items,
            'date' => $dailyUniformity->process_date->toDateString(),
            'aggregate' => $this->buildAggregate($items),
            'singleReport' => true,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('daily-uniformity-' . $dailyUniformity->monitorControl->sppa_no . '.pdf');
    }

    // ================= Weight entries (input berat satu per satu) =================

    public function storeWeight(Request $request, DailyUniformity $dailyUniformity)
    {
        $data = $request->validate([
            'weight_kg' => ['required', 'numeric', 'min:0.001', 'max:99.999'],
        ], [
            'weight_kg.required' => 'Berat ayam wajib diisi.',
            'weight_kg.numeric' => 'Berat ayam harus berupa angka.',
        ]);

        $this->normalizeWeightSequences($dailyUniformity);
        $nextSeq = $dailyUniformity->weights()->count() + 1;

        $dailyUniformity->weights()->create([
            'sequence' => $nextSeq,
            'weight_kg' => round((float) $data['weight_kg'], 3),
        ]);

        return back()->with('status', 'Berat ayam ditambahkan.');
    }

    public function destroyWeight(DailyUniformity $dailyUniformity, DailyUniformityWeight $weight)
    {
        abort_unless($weight->daily_uniformity_id === $dailyUniformity->id, 404);

        $weight->delete();
        $this->normalizeWeightSequences($dailyUniformity);

        return back()->with('status', 'Data berat dihapus.');
    }

    private function normalizeWeightSequences(DailyUniformity $dailyUniformity): void
    {
        $dailyUniformity->weights()
            ->orderBy('sequence')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(function (DailyUniformityWeight $weight, int $index) {
                $sequence = $index + 1;
                if ($weight->sequence !== $sequence) {
                    $weight->update(['sequence' => $sequence]);
                }
            });
    }

    // ================= Helper =================
    private function buildDashboard(int $year, string $shift): array
    {
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $months = [];
        foreach ($monthLabels as $i => $label) {
            $months[$i + 1] = [
                'month' => $i + 1,
                'label' => $label,
                'reports' => 0,
                'count' => 0,
                'below' => 0,
                'in_range' => 0,
                'above' => 0,
                'sum' => 0.0,
                'min' => null,
                'max' => null,
            ];
        }

        $records = DailyUniformity::query()
            ->with(['monitorControl.farm', 'weights'])
            ->where('process_date', '>=', sprintf('%04d-01-01', $year))
            ->where('process_date', '<=', sprintf('%04d-12-31', $year + 1))
            ->when($shift !== 'all', fn ($query) => $query->where('shift', $shift))
            ->get();

        $total = ['reports' => 0, 'count' => 0, 'below' => 0, 'in_range' => 0, 'above' => 0, 'sum' => 0.0, 'sum_sq' => 0.0];
        $minRow = null;
        $maxRow = null;
        $buckets = [];

        foreach ($records as $du) {
            $s = $du->summary();
            $m = (int) $du->process_date->format('n');
            $mc = $du->monitorControl;
            $ctx = [
                'farm' => $mc->farm->name ?? '-',
                'sppa' => $mc->sppa_no ?? '-',
                'date' => $du->process_date->format('d/m/Y'),
            ];

            $months[$m]['reports']++;
            $months[$m]['count'] += $s['count'];
            $months[$m]['below'] += $s['below']['count'];
            $months[$m]['in_range'] += $s['in_range']['count'];
            $months[$m]['above'] += $s['above']['count'];
            $months[$m]['sum'] += (float) $s['total'];

            $total['reports']++;
            $total['count'] += $s['count'];
            $total['below'] += $s['below']['count'];
            $total['in_range'] += $s['in_range']['count'];
            $total['above'] += $s['above']['count'];
            $total['sum'] += (float) $s['total'];

            if ($s['min'] !== null) {
                $min = (float) $s['min'];
                if ($months[$m]['min'] === null || $min < $months[$m]['min']) {
                    $months[$m]['min'] = $min;
                }
                if ($minRow === null || $min < $minRow['value']) {
                    $minRow = ['value' => $min] + $ctx;
                }
            }

            if ($s['max'] !== null) {
                $max = (float) $s['max'];
                if ($months[$m]['max'] === null || $max > $months[$m]['max']) {
                    $months[$m]['max'] = $max;
                }
                if ($maxRow === null || $max > $maxRow['value']) {
                    $maxRow = ['value' => $max] + $ctx;
                }
            }
            
            foreach ($du->weights as $w) {
                $kg = (float) $w->weight_kg;
                $total['sum_sq'] += $kg * $kg;
                $key = (int) floor(round($kg * 10, 6));
                $buckets[$key] = ($buckets[$key] ?? 0) + 1;
            }
        }

        $pct = fn ($n, $d) => $d > 0 ? round(($n / $d) * 100, 1) : 0.0;

        $monthRows = array_values(array_map(function (array $r) use ($pct) {
            $r['below_pct'] = $pct($r['below'], $r['count']);
            $r['in_range_pct'] = $pct($r['in_range'], $r['count']);
            $r['above_pct'] = $pct($r['above'], $r['count']);
            $r['avg'] = $r['count'] > 0 ? $r['sum'] / $r['count'] : null;
            return $r;
        }, $months));

        $avg = $total['count'] > 0 ? $total['sum'] / $total['count'] : null;
        $cv = null;
        if ($avg !== null && $avg > 0) {
            $variance = max(0, ($total['sum_sq'] / $total['count']) - ($avg * $avg));
            $cv = round(sqrt($variance) / $avg * 100, 1);
        }

        $histogram = [];
        if ($buckets) {
            ksort($buckets);
            $firstKey = array_key_first($buckets);
            $lastKey = array_key_last($buckets);
            for ($k = $firstKey; $k <= $lastKey; $k++) {
                $histogram[] = ['from' => $k / 10, 'to' => ($k + 1) / 10, 'count' => $buckets[$k] ?? 0];
            }
        }

        return [
            'year' => $year,
            'shift' => $shift,
            'months' => $monthRows,
            'total' => [
                'reports' => $total['reports'],
                'count' => $total['count'],
                'below' => ['count' => $total['below'], 'pct' => $pct($total['below'], $total['count'])],
                'in_range' => ['count' => $total['in_range'], 'pct' => $pct($total['in_range'], $total['count'])],
                'above' => ['count' => $total['above'], 'pct' => $pct($total['above'], $total['count'])],
                'avg' => $avg,
                'cv' => $cv,
                'min_row' => $minRow,
                'max_row' => $maxRow,
            ],
            'histogram' => $histogram,
        ];
    }

    private function buildAggregate($items): array
    {
        $totalCount = 0;
        $totalBelow = 0;
        $totalIn = 0;
        $totalAbove = 0;

        foreach ($items as $it) {
            $s = $it->summary_data ?? $it->summary();
            $totalCount += $s['count'];
            $totalBelow += $s['below']['count'];
            $totalIn += $s['in_range']['count'];
            $totalAbove += $s['above']['count'];
        }

        $pct = fn ($n) => $totalCount > 0 ? round(($n / $totalCount) * 100, 1) : 0.0;

        return [
            'count' => $totalCount,
            'below' => ['count' => $totalBelow, 'pct' => $pct($totalBelow)],
            'in_range' => ['count' => $totalIn, 'pct' => $pct($totalIn)],
            'above' => ['count' => $totalAbove, 'pct' => $pct($totalAbove)],
        ];
    }
}