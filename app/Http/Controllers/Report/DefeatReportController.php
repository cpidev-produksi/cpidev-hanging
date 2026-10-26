<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DefeatReport;
use App\Models\DefeatReportItem as Item;
use App\Models\MonitorControl;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DefeatReportController extends Controller
{
    private const LOCATIONS = ['SH01', 'SH02'];
    private const SHIFTS = ['pagi', 'malam'];

    /**
     * Sumber "Waktu" otomatis = Form Hanging milik Kontrol Monitor farm tersebut.
     * Sesuaikan jika nama kolom/relasi di tabel hanging_forms berbeda.
     * Bila tidak ditemukan, form menampilkan input waktu manual sebagai cadangan.
     */
    private const HANGING_FK = 'monitor_control_id';
    private const HANGING_START_COLUMNS = ['unloading_time', 'started_at', 'start_at', 'start_time', 'time_start', 'hanging_start'];
    private const HANGING_END_COLUMNS = ['finish_time', 'finished_at', 'finish_at', 'ended_at', 'end_at', 'end_time', 'time_end', 'hanging_end'];

    private ?array $hangingCols = null;

    /* =====================================================================
     |  INDEX: dashboard summary and report list
     * ===================================================================== */
    public function index(Request $request)
    {
        $reportId = $request->query('report_id');
        $detailMode = is_scalar($reportId) && ctype_digit((string) $reportId);

        $filters = [
            'date'      => $request->query('date', ''),
            'location'  => in_array($request->query('location'), self::LOCATIONS, true) ? $request->query('location') : '',
            'shift'     => in_array($request->query('shift'), self::SHIFTS, true) ? $request->query('shift') : '',
            'farm_name' => is_string($request->query('farm_name')) ? trim($request->query('farm_name')) : '',
        ];

        $reports = DefeatReport::query()
            ->when($filters['date'], fn ($q, $date) => $q->whereDate('report_date', $date))
            ->when($detailMode, fn ($q) => $q->whereKey($reportId))
            ->when($filters['location'], fn ($q, $v) => $q->where('location', $v))
            ->when($filters['shift'], fn ($q, $v) => $q->where('shift', $v))
            ->when($filters['farm_name'], fn ($q, $farm) => $q->whereHas('items', fn ($items) => $items->where('farm_name', $farm)))
            ->with(['items', 'creator'])
            ->orderByDesc('report_date')
            ->orderBy('location')
            ->orderByDesc('shift') // pagi sebelum malam
            ->paginate(10)
            ->withQueryString();

        $items = Item::query()
            ->select('defeat_report_items.*')
            ->join('defeat_reports', 'defeat_reports.id', '=', 'defeat_report_items.defeat_report_id')
            ->when($filters['date'], fn ($q, $date) => $q->whereDate('defeat_reports.report_date', $date))
            ->when($filters['location'], fn ($q, $v) => $q->where('defeat_reports.location', $v))
            ->when($filters['shift'], fn ($q, $v) => $q->where('defeat_reports.shift', $v))
            ->when($filters['farm_name'], fn ($q, $farm) => $q->where('defeat_report_items.farm_name', $farm))
            ->with('report')
            ->get();

        $dash = $this->buildDashboard($items);
        $farmOptions = Item::query()
            ->whereNotNull('farm_name')
            ->where('farm_name', '!=', '')
            ->distinct()
            ->orderBy('farm_name')
            ->pluck('farm_name');

        $hour = (int) now()->format('H');

        return view('report.defeat.index', [
            'detailMode' => $detailMode,
            'filters' => $filters,
            'farmOptions' => $farmOptions,
            'reports' => $reports,
            'dash' => $dash,
            'newDefaults' => [
                'report_date' => now()->toDateString(),
                'shift' => ($hour >= 6 && $hour < 18) ? 'pagi' : 'malam',
                'location' => 'SH01',
            ],
        ]);
    }

    private function buildDashboard($items): array
    {
        $reports = $items->pluck('report')->unique('id');
        $mean = fn ($vals) => ($vals = collect($vals)->filter(fn ($v) => $v !== null))->count() ? $vals->avg() : null;

        // rata-rata tiap parameter dari seluruh farm pada filter (parameter time tidak dipakai di dashboard)
        $avg = [];
        foreach (Item::FIELDS as $key => $f) {
            $avg[$key] = $f['kind'] === 'time' ? null : $mean($items->pluck($key));
        }

        $kpi = [
            'reports'   => $reports->count(),
            'batches'   => $items->count(),
            'draft'     => $reports->where('status', 'draft')->count(),
            'known'     => $reports->where('status', 'known')->count(),
            'approved'  => $reports->where('status', 'approved')->count(),
            'avg_dta'   => $avg['avg_dta'],
            'live_bird' => $avg['speed_live_bird'],
            'evis_c'    => $avg['speed_evis_c'],
            'atp_total' => $items->sum(fn ($i) => $i->atp ?? 0),
        ];

        // tren harian
        $byDate = $items->groupBy(fn ($i) => $i->report->report_date->toDateString())->sortKeys();
        $trend = [
            'labels' => $byDate->keys()->map(fn ($d) => Carbon::parse($d)->format('d/m'))->values(),
        ];
        foreach (['head_pulling', 'leg_cutting', 'leg_pulling', 'after_plucker', 'after_sortir'] as $k) {
            $trend[$k] = $byDate->map(fn ($g) => ($v = $mean($g->pluck($k))) === null ? null : round($v, 2))->values();
        }

        // rekap per farm
        $byFarm = $items->groupBy('farm_name')->map(function ($g, $farm) use ($mean) {
            return [
                'farm'          => $farm,
                'batches'       => $g->count(),
                'avg_dta'       => $mean($g->pluck('avg_dta')),
                'head_pulling'  => $mean($g->pluck('head_pulling')),
                'leg_cutting'   => $mean($g->pluck('leg_cutting')),
                'leg_pulling'   => $mean($g->pluck('leg_pulling')),
                'after_plucker' => $mean($g->pluck('after_plucker')),
                'after_sortir'  => $mean($g->pluck('after_sortir')),
            ];
        })->sortByDesc('batches')->values();

        return compact('kpi', 'avg', 'trend', 'byFarm');
    }

    /* =====================================================================
     |  TABEL BARU (dokumen per tanggal operasional + shift + lokasi)
     * ===================================================================== */
    public function storeReport(Request $request)
    {
        $data = $request->validate([
            'report_date' => ['required', 'date'],
            'shift' => ['required', Rule::in(self::SHIFTS)],
            'location' => ['required', Rule::in(self::LOCATIONS)],
        ], [], [
            'report_date' => 'Tanggal operasional',
            'shift' => 'Shift',
            'location' => 'Lokasi',
        ]);

        $date = Carbon::parse($data['report_date'])->toDateString();

        $existing = DefeatReport::query()
            ->whereDate('report_date', $date)
            ->where('shift', $data['shift'])
            ->where('location', $data['location'])
            ->first();

        if ($existing) {
            return redirect($this->indexUrl($existing))
                ->with('status', 'Tabel untuk tanggal, shift, dan lokasi tersebut sudah ada. Gunakan tombol "Tambah Farm" pada tabel itu.');
        }

        $report = DefeatReport::create([
            'report_date' => $date,
            'shift' => $data['shift'],
            'location' => $data['location'],
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        return redirect($this->indexUrl($report))
            ->with('status', 'Tabel baru dibuat. Klik "Tambah Farm" untuk mengisi data.');
    }

    private function indexUrl(DefeatReport $report): string
    {
        $date = $report->report_date->toDateString();

        return route('defeathering.efficiency.index', ['date' => $date])
            . '#report-' . $report->id;
    }

    /* =====================================================================
     |  FORM FARM (tambah / ubah satu baris)
     * ===================================================================== */
    public function createItem(DefeatReport $defeatReport)
    {
        abort_unless($defeatReport->canManageItems(), 403, 'Tabel ini tidak dapat diubah.');

        return view('report.defeat.form', [
            'report' => $defeatReport,
            'item' => null,
            'farms' => $this->farmOptions($defeatReport, null),
        ]);
    }

    public function editItem(DefeatReport $defeatReport, Item $item)
    {
        abort_unless($defeatReport->canManageItems(), 403, 'Tabel ini tidak dapat diubah.');
        abort_unless($item->defeat_report_id === $defeatReport->id, 404);

        return view('report.defeat.form', [
            'report' => $defeatReport,
            'item' => $item,
            'farms' => $this->farmOptions($defeatReport, $item),
        ]);
    }

    /**
     * Pilihan farm = Kontrol Monitor berstatus Selesai pada tanggal/shift/lokasi tabel,
     * yang belum ada di tabel ini. Tiap pilihan membawa Size, Waktu hanging, dan riwayat
     * farm sebelumnya agar form terisi otomatis di sisi browser.
     */
    private function farmOptions(DefeatReport $report, ?Item $current): array
    {
        // Mode ubah: farm tidak bisa diganti, cukup tampilkan farm yang sedang diubah
        if ($current) {
            $mc = $current->monitor_control_id
                ? MonitorControl::with('farm')->find($current->monitor_control_id)
                : null;

            return [$mc
                ? $this->optionFromMonitor($mc, $report, $current)
                : [
                    'id' => $current->monitor_control_id ?? 0,
                    'label' => $current->farm_name,
                    'farm' => $current->farm_name,
                    'size_label' => $current->size_label,
                    'time_start' => null,
                    'time_end' => null,
                    'history' => null,
                ]];
        }

        $used = $report->items()->reorder()->pluck('monitor_control_id')->filter()->all();

        return MonitorControl::query()
            ->with('farm')
            ->where('status', 'done')
            ->whereDate('process_date', $report->report_date->toDateString())
            ->where('shift', $report->shift)
            ->where('location', $report->location)
            ->whereNotIn('id', $used)
            ->orderBy('truck_no')
            ->get()
            ->map(fn (MonitorControl $m) => $this->optionFromMonitor($m, $report, null))
            ->values()
            ->all();
    }

    private function optionFromMonitor(MonitorControl $m, DefeatReport $report, ?Item $exclude): array
    {
        $farm = $m->farm?->name ?? '-';
        $window = $this->hangingWindow($m);

        return [
            'id' => $m->id,
            'label' => '#' . ($m->truck_no ?? '–') . ' · ' . $farm . ($m->size ? ' · ' . $m->size : ''),
            'farm' => $farm,
            'size_label' => $m->size ? str_replace('-', ' - ', $m->size) : '–',
            'time_start' => $window['start'] ?? null,
            'time_end' => $window['end'] ?? null,
            'history' => $this->history($m, $report, $exclude),
        ];
    }

    /** Waktu hanging (mulai - selesai) dari Form Hanging farm tersebut. */
    private function hangingWindow(MonitorControl $m): ?array
    {
        try {
            $class = \App\Models\HangingForm::class;
            if (! class_exists($class)) {
                return null;
            }

            $model = new $class;
            $cols = $this->hangingCols ??= Schema::getColumnListing($model->getTable());

            $start = collect(self::HANGING_START_COLUMNS)->first(fn ($c) => in_array($c, $cols, true));
            $end = collect(self::HANGING_END_COLUMNS)->first(fn ($c) => in_array($c, $cols, true));

            if (! $start || ! $end || ! in_array(self::HANGING_FK, $cols, true)) {
                return null;
            }

            $form = $model->newQuery()->where(self::HANGING_FK, $m->id)->latest('id')->first();
            $from = $form?->getAttribute($start);
            $to = $form?->getAttribute($end);

            if (! $from || ! $to) {
                return null;
            }

            return [
                'start' => Carbon::parse($from)->format('H:i'),
                'end' => Carbon::parse($to)->format('H:i'),
            ];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /** Speed Line & Time dari data farm yang sama pada laporan sebelumnya (paling baru). */
    private function history(MonitorControl $m, DefeatReport $report, ?Item $exclude): ?array
    {
        $farmId = $m->farm?->getKey();
        $farmName = $m->farm?->name;

        if (! $farmId && ! $farmName) {
            return null;
        }

        $prev = Item::query()
            ->select('defeat_report_items.*')
            ->join('defeat_reports', 'defeat_reports.id', '=', 'defeat_report_items.defeat_report_id')
            ->where(function ($q) use ($farmId, $farmName) {
                if ($farmId) {
                    $q->where('defeat_report_items.farm_id', $farmId);
                }
                if ($farmName) {
                    $q->orWhere('defeat_report_items.farm_name', $farmName);
                }
            })
            ->whereDate('defeat_reports.report_date', '<=', $report->report_date->toDateString())
            ->when($exclude, fn ($q) => $q->where('defeat_report_items.id', '!=', $exclude->id))
            ->orderByDesc('defeat_reports.report_date')
            ->orderByDesc('defeat_report_items.id')
            ->with('report')
            ->first();

        if (! $prev) {
            return null;
        }

        $out = ['date' => $prev->report->report_date->format('d/m/Y')];
        foreach (Item::FIELDS as $key => $f) {
            if (! empty($f['hist'])) {
                $out[$key] = $prev->getAttribute($key);
            }
        }

        return $out;
    }

    /* =====================================================================
     |  SIMPAN / UBAH / HAPUS
     * ===================================================================== */
    public function storeItem(Request $request, DefeatReport $defeatReport)
    {
        abort_unless($defeatReport->canManageItems(), 403, 'Tabel ini tidak dapat diubah.');

        [$data, $mc, $window] = $this->validatedItem($request, $defeatReport, null);

        $item = $defeatReport->items()->create(
            $this->values($data) + [
                'monitor_control_id' => $mc->id,
                'farm_id' => $mc->farm?->getKey(),
                'farm_name' => $mc->farm?->name ?? '-',
                'size' => $mc->size ?: '-',
                'time_start' => $window['start'],
                'time_end' => $window['end'],
                'sort' => ((int) $defeatReport->items()->reorder()->max('sort')) + 1,
            ]
        );

        return redirect($this->indexUrl($defeatReport))
            ->with('status', 'Farm ' . $item->farm_name . ' ditambahkan ke tabel.');
    }

    public function updateItem(Request $request, DefeatReport $defeatReport, Item $item)
    {
        abort_unless($defeatReport->canManageItems(), 403, 'Tabel ini tidak dapat diubah.');
        abort_unless($item->defeat_report_id === $defeatReport->id, 404);

        [$data, , $window] = $this->validatedItem($request, $defeatReport, $item);

        $item->update($this->values($data) + [
            'time_start' => $window['start'],
            'time_end' => $window['end'],
        ]);

        return redirect($this->indexUrl($defeatReport))
            ->with('status', 'Data farm ' . $item->farm_name . ' diperbarui.');
    }

    public function destroyItem(DefeatReport $defeatReport, Item $item)
    {
        abort_unless($defeatReport->canManageItems(), 403, 'Tabel ini tidak dapat diubah.');
        abort_unless($item->defeat_report_id === $defeatReport->id, 404);

        $name = $item->farm_name;
        $item->delete();

        return redirect($this->indexUrl($defeatReport))
            ->with('status', 'Farm ' . $name . ' dihapus dari tabel.');
    }

    public function destroy(DefeatReport $defeatReport)
    {
        abort_unless($defeatReport->canBeDeleted(), 403, 'Laporan ini tidak dapat dihapus.');

        $defeatReport->delete();

        return redirect()->route('defeathering.efficiency.index')
            ->with('status', 'Tabel dihapus.');
    }

    /* =====================================================================
     |  SHOW / PENGESAHAN / PDF
     * ===================================================================== */
    public function show(DefeatReport $defeatReport)
    {
        $defeatReport->load(['items', 'creator', 'knower', 'approver']);

        return view('report.defeat.show', ['report' => $defeatReport]);
    }

    /** Diketahui oleh (Foreman/lady). */
    public function known(Request $request, DefeatReport $defeatReport)
    {
        abort_unless($defeatReport->canBeKnown(), 403, 'Anda tidak berwenang mengetahui laporan ini.');

        if (! $defeatReport->items()->exists()) {
            return back()->withErrors(['report' => 'Tabel masih kosong. Tambahkan minimal 1 farm sebelum diketahui.']);
        }

        $data = $request->validate([
            'known_name' => ['required', Rule::in(DefeatReport::KNOWN_NAMES)],
        ]);

        $defeatReport->update([
            'status' => 'known',
            'known_by' => Auth::id(),
            'known_name' => $data['known_name'],
            'known_at' => now(),
        ]);

        return back()->with('status', 'Laporan ditandai Diketahui.');
    }

    public function saveCheckerName(Request $request, DefeatReport $defeatReport)
    {
        abort_unless($defeatReport->canBeEdited(), 403, 'Nama Checker hanya dapat diubah selama laporan berstatus Draft.');

        $data = $request->validate([
            'checker_name' => ['required', 'string', 'max:255'],
        ], [], [
            'checker_name' => 'Nama Checker',
        ]);

        $defeatReport->update($data);

        return back()->with('status', 'Nama Checker disimpan.');
    }

    /** Disetujui oleh (Supervisor). */
    public function approve(Request $request, DefeatReport $defeatReport)
    {
        abort_unless($defeatReport->canBeApproved(), 403, 'Laporan belum diketahui Foreman/lady atau Anda tidak berwenang menyetujui.');

        $data = $request->validate([
            'approved_name' => ['required', Rule::in(DefeatReport::APPROVED_NAMES)],
        ]);

        $defeatReport->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_name' => $data['approved_name'],
            'approved_at' => now(),
        ]);

        return back()->with('status', 'Laporan disetujui.');
    }

    public function pdf(DefeatReport $defeatReport)
    {
        $defeatReport->load(['items', 'creator', 'knower', 'approver']);

        $pdf = Pdf::loadView('report.defeat.pdf', ['report' => $defeatReport])
            ->setPaper('a4', 'landscape');

        $name = sprintf(
            'Efficiency-Defeathering_%s_%s_%s.pdf',
            $defeatReport->location,
            $defeatReport->report_date->format('Y-m-d'),
            $defeatReport->shift
        );

        return $pdf->download($name);
    }

    /* =====================================================================
     |  VALIDASI & NORMALISASI
     * ===================================================================== */

    /** @return array{0: array, 1: ?MonitorControl, 2: array} [data tervalidasi, kontrol monitor, waktu hanging] */
    private function validatedItem(Request $request, DefeatReport $report, ?Item $item): array
    {
        $request->merge($this->normalize($request->all()));

        $rules = [
            'sampling_count' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'time_start' => ['nullable', 'date_format:H:i'],
            'time_end' => ['nullable', 'date_format:H:i'],
        ];
        $attributes = [
            'monitor_control_id' => 'Nama Farm',
            'sampling_count' => 'Jumlah Sampling',
            'time_start' => 'Waktu mulai hanging',
            'time_end' => 'Waktu selesai hanging',
        ];

        if (! $item) {
            $rules['monitor_control_id'] = [
                'required', 'integer',
                Rule::exists('monitor_controls', 'id'),
                Rule::unique('defeat_report_items', 'monitor_control_id')->where('defeat_report_id', $report->id),
            ];
        }

        foreach (Item::FIELDS as $key => $f) {
            if (in_array($key, ['head_pulling', 'leg_cutting', 'leg_pulling'], true)) {
                continue;
            }

            $r = [$f['req'] ? 'required' : 'nullable'];

            if ($f['kind'] === 'time') {
                $r[] = 'regex:/^\d{2}\.[0-5]\d$/';
            } else {
                $r[] = 'numeric';
                $r[] = 'min:0';
                if ($f['kind'] === 'int') {
                    $r[] = 'integer';
                }
                if ($f['max'] !== null) {
                    $r[] = 'max:' . $f['max'];
                }
            }

            $rules[$key] = $r;
            $name = $f['group'] === $f['label'] ? $f['label'] : $f['group'] . ' ' . $f['label'];
            $attributes[$key] = $f['kind'] === 'time' ? "$name (format mm.ss)" : $name;
        }

        foreach (['head_pulling', 'leg_cutting', 'leg_pulling'] as $key) {
            $countKey = $key . '_count';
            $rules[$countKey] = ['required', 'integer', 'min:0', 'max:4294967295', 'lte:sampling_count'];
            $attributes[$countKey] = match ($key) {
                'head_pulling' => 'Jumlah Head Pulling',
                'leg_cutting' => 'Jumlah Cutting',
                'leg_pulling' => 'Jumlah Pulling',
            };
        }

        $validator = Validator::make($request->all(), $rules, [
            'monitor_control_id.required' => 'Pilih Nama Farm.',
            'monitor_control_id.unique' => 'Farm tersebut sudah ada pada tabel ini.',
            'regex' => ':attribute harus berformat mm.ss (contoh 01.16).',
            'lte' => ':attribute tidak boleh melebihi Jumlah Sampling.',
        ], $attributes);

        $mc = null;
        $window = null;

        $validator->after(function ($v) use ($request, $report, $item, &$mc, &$window) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            if ($item) {
                $mc = $item->monitorControl;
            } else {
                // farm harus dari Kontrol Monitor berstatus Selesai pada tanggal/shift/lokasi tabel ini
                $mc = MonitorControl::with('farm')->find((int) $request->input('monitor_control_id'));

                $ok = $mc
                    && $mc->status === 'done'
                    && $mc->process_date?->toDateString() === $report->report_date->toDateString()
                    && $mc->shift === $report->shift
                    && $mc->location === $report->location;

                if (! $ok) {
                    $v->errors()->add('monitor_control_id', 'Farm harus berasal dari Kontrol Monitor berstatus Selesai pada tanggal, shift, dan lokasi tabel ini.');

                    return;
                }
            }

            // Waktu: utamakan data Form Hanging; jika tidak ada, pakai isian manual / data tersimpan
            $window = $mc ? $this->hangingWindow($mc) : null;

            if (! $window) {
                $start = $request->input('time_start') ?: $item?->time_start;
                $end = $request->input('time_end') ?: $item?->time_end;

                if (! $start || ! $end) {
                    $v->errors()->add('time_start', 'Waktu hanging belum tersedia pada Form Hanging. Isi waktu mulai dan selesai secara manual.');

                    return;
                }

                $window = ['start' => $start, 'end' => $end];
            }
        });

        return [$validator->validate(), $mc, $window];
    }

    /** Nilai parameter siap simpan. */
    private function values(array $data): array
    {
        $sampleCount = (int) $data['sampling_count'];
        $out = [
            'sampling_count' => $sampleCount,
        ];

        foreach (['head_pulling', 'leg_cutting', 'leg_pulling'] as $key) {
            $countKey = $key . '_count';
            $count = (int) $data[$countKey];
            $out[$countKey] = $count;
            $out[$key] = round($count / $sampleCount * 100, 2);
        }

        foreach (Item::FIELDS as $key => $f) {
            if (in_array($key, ['head_pulling', 'leg_cutting', 'leg_pulling'], true)) {
                continue;
            }

            $v = $data[$key] ?? null;
            $out[$key] = ($v === null || $v === '')
                ? null
                : match ($f['kind']) {
                    'int' => (int) $v,
                    'dec' => round((float) $v, 2),
                    default => $v,
                };
        }

        return $out;
    }

    /** Terima koma/titik desimal, buang sampah; nilai non-numerik dibiarkan supaya gagal validasi. */
    private function normalize(array $in): array
    {
        $out = [
            'monitor_control_id' => $in['monitor_control_id'] ?? null,
            'sampling_count' => $in['sampling_count'] ?? null,
            'time_start' => $this->hhmm($in['time_start'] ?? null),
            'time_end' => $this->hhmm($in['time_end'] ?? null),
        ];

        foreach (['head_pulling', 'leg_cutting', 'leg_pulling'] as $key) {
            $out[$key . '_count'] = $in[$key . '_count'] ?? null;
        }

        foreach (Item::FIELDS as $key => $f) {
            if (in_array($key, ['head_pulling', 'leg_cutting', 'leg_pulling'], true)) {
                continue;
            }

            $out[$key] = match ($f['kind']) {
                'int' => $this->digits($in[$key] ?? null),
                'time' => $this->mmss($in[$key] ?? null),
                default => $this->num($in[$key] ?? null),
            };
        }

        return $out;
    }

    private function num($v)
    {
        if ($v === null) {
            return null;
        }
        $v = trim((string) $v);

        return $v === '' ? null : str_replace(',', '.', $v);
    }

    private function digits($v)
    {
        if ($v === null) {
            return null;
        }
        $v = preg_replace('/[^0-9]/', '', (string) $v);

        return $v === '' ? null : $v;
    }

    private function hhmm($v)
    {
        if ($v === null || trim((string) $v) === '') {
            return null;
        }
        $v = str_replace('.', ':', trim((string) $v));

        if (preg_match('/^(\d{1,2}):(\d{2})/', $v, $m)) {
            return sprintf('%02d:%s', $m[1], $m[2]);
        }

        return $v;
    }

    private function mmss($v)
    {
        if ($v === null || trim((string) $v) === '') {
            return null;
        }
        $v = str_replace([',', ':'], '.', trim((string) $v));

        if (preg_match('/^(\d{1,2})\.(\d{2})$/', $v, $m)) {
            return sprintf('%02d.%s', $m[1], $m[2]);
        }

        return $v;
    }
}