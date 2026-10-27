<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DefeatReportItem;
use App\Models\EviscerationEfficiencyItem as Item;
use App\Models\EviscerationEfficiencyReport as Report;
use App\Models\MonitorControl;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EviscerationEfficiencyController extends Controller
{
    private const SHIFTS = ['pagi', 'malam'];

    private const LOCATIONS = ['SH01', 'SH02'];

    public function index(Request $request)
    {
        $reportId = $request->query('report_id');
        $detailMode = is_scalar($reportId) && ctype_digit((string) $reportId);

        $filters = [
            'date' => is_string($request->query('date')) ? $request->query('date') : '',
            'location' => in_array($request->query('location'), self::LOCATIONS, true) ? $request->query('location') : '',
            'shift' => in_array($request->query('shift'), self::SHIFTS, true) ? $request->query('shift') : '',
            'farm_name' => is_string($request->query('farm_name')) ? trim($request->query('farm_name')) : '',
        ];

        $reports = Report::query()
            ->when($filters['date'], fn ($query, $date) => $query->whereDate('report_date', $date))
            ->when($detailMode, fn ($query) => $query->whereKey($reportId))
            ->when($filters['location'], fn ($query, $value) => $query->where('location', $value))
            ->when($filters['shift'], fn ($query, $value) => $query->where('shift', $value))
            ->when($filters['farm_name'], fn ($query, $farm) => $query->whereHas('items', fn ($items) => $items->where('farm_name', $farm)))
            ->withCount('items')
            ->with('creator')
            ->when($detailMode, fn ($query) => $query->with('items'))
            ->orderByDesc('report_date')
            ->orderBy('location')
            ->orderByDesc('shift')
            ->paginate(10)
            ->withQueryString();

        // data dashboard: seluruh farm pada filter yang dipilih
        $items = Item::query()
            ->select('evisceration_efficiency_items.*')
            ->join('evisceration_efficiency_reports', 'evisceration_efficiency_reports.id', '=', 'evisceration_efficiency_items.evisceration_efficiency_report_id')
            ->when($filters['date'], fn ($query, $date) => $query->whereDate('evisceration_efficiency_reports.report_date', $date))
            ->when($filters['location'], fn ($query, $value) => $query->where('evisceration_efficiency_reports.location', $value))
            ->when($filters['shift'], fn ($query, $value) => $query->where('evisceration_efficiency_reports.shift', $value))
            ->when($filters['farm_name'], fn ($query, $farm) => $query->where('evisceration_efficiency_items.farm_name', $farm))
            ->with('report')
            ->get();

        $farmOptions = Item::query()
            ->whereNotNull('farm_name')
            ->where('farm_name', '!=', '')
            ->distinct()
            ->orderBy('farm_name')
            ->pluck('farm_name');

        return view('report.evis.effieciency.index', [
            'detailMode' => $detailMode,
            'reports' => $reports,
            'filters' => $filters,
            'farmOptions' => $farmOptions,
            'dash' => $this->buildDashboard($items),
            'defaults' => [
                'report_date' => now()->toDateString(),
                'shift' => now()->hour >= 6 && now()->hour < 18 ? 'pagi' : 'malam',
                'location' => 'SH01',
            ],
        ]);
    }

    private function buildDashboard($items): array
    {
        $reports = $items->pluck('report')->unique('id');
        $mean = fn ($values) => ($values = collect($values)->filter(fn ($value) => $value !== null))->count() ? $values->avg() : null;
        $vent = fn ($item) => $item->vent_percent;
        $viscera = fn ($item) => $item->viscera_percent;

        $kpi = [
            'reports' => $reports->count(),
            'batches' => $items->count(),
            'draft' => $reports->where('status', 'draft')->count(),
            'known' => $reports->where('status', 'known')->count(),
            'approved' => $reports->where('status', 'approved')->count(),
            'sampling_total' => (int) $items->sum('sampling_count'),
            'speed_evis' => $mean($items->pluck('speed_evis')),
            'vent' => $mean($items->map($vent)),
            'viscera' => $mean($items->map($viscera)),
            'grit_total' => (int) $items->sum('grit_findings'),
            'sprayer_on' => $items->where('sprayer_status', 'operating')->count(),
            'water_start' => $mean($items->pluck('water_flow_start')),
            'water_end' => $mean($items->pluck('water_flow_end')),
        ];

        // tren harian
        $byDate = $items->groupBy(fn ($item) => $item->report->report_date->toDateString())->sortKeys();
        $round = fn ($value) => $value === null ? null : round($value, 2);
        $trend = [
            'labels' => $byDate->keys()->map(fn ($date) => Carbon::parse($date)->format('d/m'))->values(),
            'vent' => $byDate->map(fn ($group) => $round($mean($group->map($vent))))->values(),
            'viscera' => $byDate->map(fn ($group) => $round($mean($group->map($viscera))))->values(),
            'grit' => $byDate->map(fn ($group) => (int) $group->sum('grit_findings'))->values(),
        ];

        // rekap per farm
        $byFarm = $items->groupBy('farm_name')->map(fn ($group, $farm) => [
            'farm' => $farm,
            'batches' => $group->count(),
            'speed_evis' => $mean($group->pluck('speed_evis')),
            'sampling' => (int) $group->sum('sampling_count'),
            'vent' => $mean($group->map($vent)),
            'viscera' => $mean($group->map($viscera)),
            'grit' => (int) $group->sum('grit_findings'),
        ])->sortByDesc('batches')->values();

        return compact('kpi', 'trend', 'byFarm');
    }

    public function storeReport(Request $request)
    {
        $data = $request->validate([
            'report_date' => ['required', 'date'],
            'shift' => ['required', Rule::in(self::SHIFTS)],
            'location' => ['required', Rule::in(self::LOCATIONS)],
        ]);

        $report = Report::firstOrCreate([
            'report_date' => Carbon::parse($data['report_date'])->toDateString(),
            'shift' => $data['shift'],
            'location' => $data['location'],
        ], [
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        $message = $report->wasRecentlyCreated
            ? 'Laporan baru dibuat. Gunakan Tambah Farm untuk mengisi data.'
            : 'Laporan untuk tanggal, shift, dan lokasi tersebut sudah ada.';

        return redirect()->route('evisceration.efficiency.show', $report)->with('status', $message);
    }

    public function show(Report $report)
    {
        $report->load(['items', 'creator', 'knower', 'approver']);

        return view('report.evis.effieciency.show', compact('report'));
    }

    public function createItem(Report $report)
    {
        abort_unless($report->canManageItems(), 403, 'Laporan ini tidak dapat diubah.');

        return view('report.evis.effieciency.form', [
            'report' => $report,
            'item' => null,
            'farms' => $this->farmOptions($report, null),
        ]);
    }

    public function editItem(Report $report, Item $item)
    {
        abort_unless($report->canManageItems(), 403, 'Laporan ini tidak dapat diubah.');
        abort_unless($item->evisceration_efficiency_report_id === $report->id, 404);

        return view('report.evis.effieciency.form', [
            'report' => $report,
            'item' => $item,
            'farms' => $this->farmOptions($report, $item),
        ]);
    }

    private function farmOptions(Report $report, ?Item $current): array
    {
        if ($current) {
            $monitor = $current->monitor_control_id
                ? MonitorControl::with('farm')->find($current->monitor_control_id)
                : null;

            return [[
                'id' => $current->monitor_control_id ?? 0,
                'label' => $monitor
                    ? '#'.($monitor->truck_no ?? '-').' · '.($monitor->farm?->name ?? $current->farm_name).($monitor->size ? ' · '.$monitor->size : '')
                    : $current->farm_name,
                'farm' => $current->farm_name,
                'size' => $current->size,
                'time_start' => $current->time_start,
                'time_end' => $current->time_end,
                'defeat' => null, // mode ubah: nilai tersimpan tidak ditimpa
            ]];
        }

        $used = $report->items()->pluck('monitor_control_id')->filter()->all();

        $monitors = MonitorControl::query()
            ->with('farm')
            ->where('status', 'done')
            ->whereDate('process_date', $report->report_date->toDateString())
            ->where('shift', $report->shift)
            ->where('location', $report->location)
            ->whereNotIn('id', $used)
            ->orderBy('truck_no')
            ->get();

        $defeat = $this->defeatData($monitors->pluck('id')->all());

        return $monitors
            ->map(function (MonitorControl $monitor) use ($defeat) {
                $window = $this->hangingWindow($monitor);

                return [
                    'id' => $monitor->id,
                    'label' => '#'.($monitor->truck_no ?? '-').' · '.($monitor->farm?->name ?? '-').($monitor->size ? ' · '.$monitor->size : ''),
                    'farm' => $monitor->farm?->name ?? '-',
                    'size' => $monitor->size ?? '-',
                    'time_start' => $window['start'] ?? null,
                    'time_end' => $window['end'] ?? null,
                    'defeat' => $defeat[$monitor->id] ?? null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Speed Line Evis (BPH) dan Jumlah Sampling dari Efficiency Defeathering Report farm yang sama
     * (dicocokkan lewat Kontrol Monitor). Bila ada lebih dari satu, dipakai yang paling baru.
     * Nilai ini hanya mengisi form otomatis dan tetap bisa diubah.
     *
     * @param  array<int>  $monitorIds
     * @return array<int, array{speed_evis: ?int, sampling_count: ?int, date: ?string}>
     */
    private function defeatData(array $monitorIds): array
    {
        if ($monitorIds === []) {
            return [];
        }

        return DefeatReportItem::query()
            ->with('report')
            ->whereIn('monitor_control_id', $monitorIds)
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (DefeatReportItem $row) => [
                $row->monitor_control_id => [
                    'speed_evis' => $row->speed_evis_c,
                    'sampling_count' => $row->sampling_count,
                    'date' => $row->report?->report_date?->format('d/m/Y'),
                ],
            ])
            ->all();
    }

    private function hangingWindow(MonitorControl $monitor): ?array
    {
        $hanging = $monitor->hangingForm;
        if (! $hanging?->unloading_time || ! $hanging?->finish_time) {
            return null;
        }

        return [
            'start' => Carbon::parse($hanging->unloading_time)->format('H:i'),
            'end' => Carbon::parse($hanging->finish_time)->format('H:i'),
        ];
    }

    public function storeItem(Request $request, Report $report)
    {
        abort_unless($report->canManageItems(), 403, 'Laporan ini tidak dapat diubah.');

        [$data, $monitor, $time] = $this->validatedItem($request, $report);
        $item = $report->items()->create($data + [
            'monitor_control_id' => $monitor->id,
            'farm_id' => $monitor->farm?->getKey(),
            'farm_name' => $monitor->farm?->name ?? '-',
            'size' => $monitor->size ?: '-',
            'time_start' => $time['start'],
            'time_end' => $time['end'],
            'sort' => (int) $report->items()->max('sort') + 1,
        ]);

        return redirect()->route('evisceration.efficiency.show', $report)
            ->with('status', 'Farm '.$item->farm_name.' ditambahkan.');
    }

    public function updateItem(Request $request, Report $report, Item $item)
    {
        abort_unless($report->canManageItems(), 403, 'Laporan ini tidak dapat diubah.');
        abort_unless($item->evisceration_efficiency_report_id === $report->id, 404);

        [$data, , $time] = $this->validatedItem($request, $report, $item);
        $item->update($data + ['time_start' => $time['start'], 'time_end' => $time['end']]);

        return redirect()->route('evisceration.efficiency.show', $report)
            ->with('status', 'Data farm '.$item->farm_name.' diperbarui.');
    }

    private function validatedItem(Request $request, Report $report, ?Item $item = null): array
    {
        $request->merge([
            'water_flow_start' => $this->normalizeDecimal($request->input('water_flow_start')),
            'water_flow_end' => $this->normalizeDecimal($request->input('water_flow_end')),
            'time_start' => $this->normalizeTime($request->input('time_start')),
            'time_end' => $this->normalizeTime($request->input('time_end')),
        ]);

        $rules = [
            'sampling_count' => ['required', 'integer', 'min:1', 'max:999999'],
            'speed_evis' => ['required', 'integer', 'min:0', 'max:99999'],
            'vent_not_torn' => ['required', 'integer', 'min:0', 'max:999999'],
            'viscera_not_removed' => ['required', 'integer', 'min:0', 'max:999999'],
            'sprayer_status' => ['required', Rule::in(['operating', 'not_operating'])],
            'grit_findings' => ['required', 'integer', 'min:0', 'max:999999'],
            'water_flow_start' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'water_flow_end' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'time_start' => ['nullable', 'date_format:H:i'],
            'time_end' => ['nullable', 'date_format:H:i'],
        ];
        if (! $item) {
            $rules['monitor_control_id'] = [
                'required',
                'integer',
                Rule::exists('monitor_controls', 'id'),
                Rule::unique('evisceration_efficiency_items', 'monitor_control_id')
                    ->where('evisceration_efficiency_report_id', $report->id),
            ];
        }

        $data = $request->validate($rules, [
            'monitor_control_id.required' => 'Pilih Nama Farm.',
            'monitor_control_id.unique' => 'Farm tersebut sudah ada pada laporan ini.',
        ]);

        if ($data['vent_not_torn'] > $data['sampling_count']) {
            throw ValidationException::withMessages([
                'vent_not_torn' => 'Vent Tidak Sobek tidak boleh melebihi Jumlah Sampling.',
            ]);
        }
        if ($data['viscera_not_removed'] > $data['sampling_count']) {
            throw ValidationException::withMessages([
                'viscera_not_removed' => 'Jeroan Tidak Terambil tidak boleh melebihi Jumlah Sampling.',
            ]);
        }

        $monitor = $item?->monitorControl;
        if (! $item) {
            $monitor = MonitorControl::with('farm')->findOrFail($data['monitor_control_id']);
            abort_unless(
                $monitor->status === 'done'
                    && $monitor->process_date?->toDateString() === $report->report_date->toDateString()
                    && $monitor->shift === $report->shift
                    && $monitor->location === $report->location,
                422,
                'Farm harus berasal dari Kontrol Monitor selesai pada tanggal, shift, dan lokasi laporan.'
            );
        }

        $time = $monitor ? $this->hangingWindow($monitor) : null;
        $time ??= [
            'start' => $data['time_start'] ?? $item?->time_start,
            'end' => $data['time_end'] ?? $item?->time_end,
        ];
        if (! $time['start'] || ! $time['end']) {
            throw ValidationException::withMessages([
                'time_start' => 'Waktu hanging belum tersedia. Isi waktu mulai dan selesai secara manual.',
            ]);
        }

        unset($data['monitor_control_id'], $data['time_start'], $data['time_end']);
        $data['speed_evis'] = $data['speed_evis'] ?? null;

        return [$data, $monitor, $time];
    }

    private function normalizeDecimal(mixed $value): mixed
    {
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : str_replace(',', '.', $value);
    }

    private function normalizeTime(mixed $value): mixed
    {
        if (! is_string($value)) {
            return null;
        }

        if (trim($value) === '') {
            return null;
        }

        $value = str_replace('.', ':', trim($value));

        return preg_match('/^(\d{1,2}):(\d{2})$/', $value, $matches)
            ? sprintf('%02d:%s', $matches[1], $matches[2])
            : $value;
    }

    public function destroyItem(Report $report, Item $item)
    {
        abort_unless($report->canManageItems(), 403, 'Laporan ini tidak dapat diubah.');
        abort_unless($item->evisceration_efficiency_report_id === $report->id, 404);

        $item->delete();

        return redirect()->route('evisceration.efficiency.show', $report)->with('status', 'Farm dihapus dari laporan.');
    }

    public function destroy(Report $report)
    {
        abort_unless($report->canBeDeleted(), 403, 'Laporan ini tidak dapat dihapus.');

        $report->delete();

        return redirect()->route('evisceration.efficiency.index')->with('status', 'Laporan dihapus.');
    }

    public function saveCheckerName(Request $request, Report $report)
    {
        abort_unless($report->canBeEdited(), 403, 'Nama Checker hanya dapat diubah selama laporan berstatus Draft.');
        $data = $request->validate(['checker_name' => ['required', 'string', 'max:255']]);
        $report->update($data);

        return back()->with('status', 'Nama Checker disimpan.');
    }

    public function known(Request $request, Report $report)
    {
        abort_unless($report->canBeKnown(), 403, 'Anda tidak berwenang mengetahui laporan ini.');
        if (! $report->items()->exists()) {
            return back()->withErrors(['report' => 'Tambahkan minimal satu farm sebelum laporan diketahui.']);
        }

        $data = $request->validate(['known_name' => ['required', Rule::in(Report::KNOWN_NAMES)]]);
        $report->update([
            'status' => 'known',
            'known_by' => Auth::id(),
            'known_name' => $data['known_name'],
            'known_at' => now(),
        ]);

        return back()->with('status', 'Laporan ditandai Diketahui.');
    }

    public function approve(Request $request, Report $report)
    {
        abort_unless($report->canBeApproved(), 403, 'Laporan belum diketahui atau Anda tidak berwenang menyetujui.');
        $data = $request->validate(['approved_name' => ['required', Rule::in(Report::APPROVED_NAMES)]]);
        $report->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_name' => $data['approved_name'],
            'approved_at' => now(),
        ]);

        return back()->with('status', 'Laporan disetujui.');
    }

    public function pdf(Report $report)
    {
        $report->load(['items', 'creator', 'knower', 'approver']);
        $pdf = Pdf::loadView('report.evis.effieciency.pdf', compact('report'))->setPaper('a4', 'landscape');
        $name = sprintf('Efficiency-Evisceration_%s_%s_%s.pdf', $report->location, $report->report_date->format('Y-m-d'), $report->shift);

        return $pdf->download($name);
    }
}