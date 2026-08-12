<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DailyUniformity;
use App\Models\DailyUniformityWeight;
use App\Models\MonitorControl;
use Barryvdh\DomPDF\Facade\Pdf;
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

        $items = DailyUniformity::query()
            ->with(['monitorControl.farm', 'monitorControl.expedition', 'monitorControl.plateNumber', 'weights'])
            ->whereDate('process_date', $date)
            ->orderBy('location')
            ->orderBy('shift')
            ->get()
            ->map(function (DailyUniformity $du) {
                $du->summary_data = $du->summary();
                return $du;
            });

        $aggregate = $this->buildAggregate($items);

        return view('transaction.daily_uniformities.index', [
            'items' => $items,
            'date' => $date,
            'aggregate' => $aggregate,
        ]);
    }

    public function create()
    {
        $monitorControls = MonitorControl::query()
            ->whereNotNull('sppa_no')
            ->where('sppa_no', '!=', '')
            ->whereDoesntHave('dailyUniformity')
            ->with(['farm', 'expedition', 'plateNumber'])
            ->orderByDesc('process_date')
            ->orderBy('truck_no')
            ->get();

        return view('transaction.daily_uniformities.create', compact('monitorControls'));
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

        $items = DailyUniformity::query()
            ->with(['monitorControl.farm', 'monitorControl.expedition', 'monitorControl.plateNumber', 'monitorControl.hangingForm.lines.sets', 'weights'])
            ->whereDate('process_date', $date)
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

    // ================= Weight entries (input berat satu per satu) =================

    public function storeWeight(Request $request, DailyUniformity $dailyUniformity)
    {
        $data = $request->validate([
            'weight_kg' => ['required', 'numeric', 'min:0.001', 'max:99.999'],
        ], [
            'weight_kg.required' => 'Berat ayam wajib diisi.',
            'weight_kg.numeric' => 'Berat ayam harus berupa angka.',
        ]);

        $nextSeq = ((int) $dailyUniformity->weights()->max('sequence')) + 1;

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

        return back()->with('status', 'Data berat dihapus.');
    }

    // ================= Helper =================
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