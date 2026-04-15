<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Expedition;
use App\Models\Farm;
use App\Models\HangingForm;
use App\Models\HangingLine;
use App\Models\HangingLineSet;
use App\Models\MonitorControl;
use App\Models\PlateNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitorControlController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');
        if ($date === null || $date === '') {
            $date = now()->toDateString(); // default: tanggal operasional hari ini
        }

        $perPage = $request->query('per_page', 20);
        $locations = ['SH01', 'SH02'];

        $baseQuery = function () use ($date) {
            return MonitorControl::query()
                ->with(['expedition', 'plateNumber', 'farm', 'hangingForm'])
                ->whereDate('process_date', $date);
        };

        $paginateShift = function (string $loc, string $sh, string $pageParam) use ($baseQuery, $perPage) {
            return $baseQuery()
                ->where('location', $loc)
                ->where('shift', $sh)
                ->orderBy('truck_no')
                ->paginate($perPage, ['*'], $pageParam)
                ->withQueryString();
        };

        $data = [];
        $stats = [];
        $summaryActive = 0;
        $summaryDone = 0;

        foreach ($locations as $loc) {
            foreach (['pagi', 'malam'] as $sh) {
                $data[$loc][$sh] = $paginateShift($loc, $sh, "page_{$loc}_{$sh}");

                $countBase = MonitorControl::query()
                    ->whereDate('process_date', $date)
                    ->where('location', $loc)
                    ->where('shift', $sh);

                $active = (clone $countBase)->where('status', '!=', 'done')->count();
                $done = (clone $countBase)->where('status', 'done')->count();

                $stats[$loc][$sh] = [
                    'active' => $active,
                    'done' => $done,
                    'total' => $active + $done,
                ];

                $summaryActive += $active;
                $summaryDone += $done;
            }
        }

        $runningLocations = MonitorControl::query()
            ->whereDate('process_date', $date)
            ->where('status', 'running')
            ->pluck('location')
            ->unique()
            ->values()
            ->all();

        return view('transaction.monitor_controls.index', [
            'data' => $data,
            'date' => $date,
            'locations' => $locations,
            'stats' => $stats,
            'summaryActive' => $summaryActive,
            'summaryDone' => $summaryDone,
            'runningLocations' => $runningLocations,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $expeditions = Expedition::query()
            ->with(['plateNumbers' => fn ($q) => $q->orderBy('plate_number')])
            ->orderBy('name')
            ->get();

        $farms = Farm::query()->orderBy('name')->get();

        $sizes = [
            '1.10-1.20','1.20-1.40','1.40-1.60','1.60-1.80','1.80-2.00',
            '2.00-2.20','2.20-2.40','2.40-2.60','2.60-2.80','2.80-3.00',
        ];

        return view('transaction.monitor_controls.create', compact('expeditions', 'farms', 'sizes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location' => ['required', 'in:SH01,SH02'],
            'process_date' => ['required', 'date'],
            'shift' => ['required', 'in:pagi,malam'],
            'size' => ['required', 'string', 'max:20'],

            'expedition_id' => ['required', 'exists:expeditions,id'],
            'plate_number_id' => ['required', 'exists:plate_numbers,id'],

            'farm_id' => ['required', 'exists:farms,id'],
            'seal_no' => ['nullable', 'string', 'max:50'],
            'truck_arrival_time' => ['nullable', 'date_format:H:i'],
            'catch_date' => ['nullable', 'date'],
            'total_chicken' => ['nullable', 'integer', 'min:0'],
            'total_kilo' => ['nullable', 'numeric', 'min:0'],
            'abw' => ['nullable', 'numeric', 'min:0'],
            'sppa_no' => ['nullable', 'string', 'max:50'],
            'order_id' => ['nullable', 'string', 'max:100'],
            'sppa_date' => ['nullable', 'date'],
        ], [
            'expedition_id.required' => 'Ekspedisi wajib dipilih.',
            'plate_number_id.required' => 'No Polisi wajib dipilih.',
            'truck_arrival_time.date_format' => 'Format jam truk datang harus HH:MM.',
        ]);

        $ok = PlateNumber::query()
            ->where('id', $data['plate_number_id'])
            ->where('expedition_id', $data['expedition_id'])
            ->exists();

        if (!$ok) {
            return back()
                ->withErrors(['plate_number_id' => 'No Polisi tidak sesuai dengan Ekspedisi yang dipilih.'])
                ->withInput();
        }

        return DB::transaction(function () use ($data) {
            // hitung truck_no berikutnya berdasarkan location + process_date
            $nextTruckNo = (int) MonitorControl::query()
                ->where('location', $data['location'])
                ->whereDate('process_date', $data['process_date'])
                ->max('truck_no');

            $data['truck_no'] = $nextTruckNo + 1;

            $meta = $this->locationMeta($data['location']);
            $data['set_count'] = $meta['set_count'];
            $data['shackle_count'] = $meta['shackle_count'];

            $data['status'] = 'draft';
            $data['report_code'] = $this->generateReportCode($data['location'], $data['process_date'], $data['shift']);

            $monitor = MonitorControl::create($data);

            return redirect()
                ->route('monitor-controls.index')
                ->with('status', 'Kontrol monitor dibuat: ' . $monitor->report_code);
        });
    }

    public function edit(Request $request, MonitorControl $monitorControl)
    {
        $slug = $request->user()?->role?->slug;

        if ($monitorControl->status !== 'draft' && !in_array($slug, ['supervisor','superadmin'], true)) {
            return redirect()->route('monitor-controls.index')
                ->with('status', 'Tidak bisa edit jika sudah running/done.');
        }

        $monitorControl->load(['expedition', 'plateNumber', 'farm']);

        $expeditions = Expedition::query()
            ->with(['plateNumbers' => fn ($q) => $q->orderBy('plate_number')])
            ->orderBy('name')
            ->get();

        $farms = Farm::query()->orderBy('name')->get();

        $sizes = [
            '1.10-1.20','1.20-1.40','1.40-1.60','1.60-1.80','1.80-2.00',
            '2.00-2.20','2.20-2.40','2.40-2.60','2.60-2.80','2.80-3.00',
        ];

        return view('transaction.monitor_controls.edit', [
            'monitor' => $monitorControl,
            'expeditions' => $expeditions,
            'farms' => $farms,
            'sizes' => $sizes,
        ]);
    }

    public function update(Request $request, MonitorControl $monitorControl)
    {
        $slug = $request->user()?->role?->slug;

        if ($monitorControl->status !== 'draft' && !in_array($slug, ['supervisor', 'spv', 'superadmin'], true)) {
            return back()->with('status', 'Tidak bisa edit jika sudah running/done.');
        }

        $data = $request->validate([
            'process_date' => ['required', 'date'],
            'shift' => ['required', 'in:pagi,malam'],
            'size' => ['required', 'string', 'max:20'],

            'expedition_id' => ['required', 'exists:expeditions,id'],
            'plate_number_id' => ['required', 'exists:plate_numbers,id'],

            'farm_id' => ['required', 'exists:farms,id'],

            'seal_no' => ['nullable', 'string', 'max:50'],
            'truck_arrival_time' => ['nullable', 'date_format:H:i'],
            'catch_date' => ['nullable', 'date'],
            'total_chicken' => ['nullable', 'integer', 'min:0'],
            'total_kilo' => ['nullable', 'numeric', 'min:0'],
            'abw' => ['nullable', 'numeric', 'min:0'],
            'sppa_no' => ['nullable', 'string', 'max:50'],
            'order_id' => ['nullable', 'string', 'max:100'],
            'sppa_date' => ['nullable', 'date'],
        ]);

        $ok = PlateNumber::query()
            ->where('id', $data['plate_number_id'])
            ->where('expedition_id', $data['expedition_id'])
            ->exists();

        if (!$ok) {
            return back()
                ->withErrors(['plate_number_id' => 'No Polisi tidak sesuai dengan Ekspedisi yang dipilih.'])
                ->withInput();
        }

        $monitorControl->update($data);

        return redirect()->route('monitor-controls.index')->with('status', 'Kontrol monitor diupdate.');
    }

    public function destroy(Request $request, MonitorControl $monitorControl)
    {
        $slug = $request->user()?->role?->slug;

        if ($monitorControl->status !== 'draft' && !in_array($slug, ['supervisor','superadmin'], true)) {
            return back()->with('status', 'Tidak bisa hapus jika sudah running/done.');
        }

        $monitorControl->delete();
        return back()->with('status', 'Kontrol monitor dihapus.');
    }

    private function locationMeta(string $location): array
    {
        return match ($location) {
            'SH01' => ['set_count' => 4, 'shackle_count' => 20],
            'SH02' => ['set_count' => 3, 'shackle_count' => 35],
            default => ['set_count' => 4, 'shackle_count' => 20],
        };
    }

    private function generateReportCode(string $location, string $processDate, string $shift): string
    {
        $date = date('Ymd', strtotime($processDate));
        $shiftCode = strtoupper($shift);
        $prefix = "{$location}-{$date}-{$shiftCode}-";

        $last = MonitorControl::query()
            ->where('report_code', 'like', $prefix . '%')
            ->orderByDesc('report_code')
            ->value('report_code');

        $next = 1;
        if ($last) {
            $lastNo = (int) substr($last, -4);
            $next = $lastNo + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function moveTruckNo(Request $request, MonitorControl $monitorControl)
    {
        if ($monitorControl->status !== 'draft') {
            return back()->withErrors(['move' => 'Hanya draft yang boleh diubah urutannya.']);
        }

        $data = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        return DB::transaction(function () use ($monitorControl, $data) {
            $location = $monitorControl->location;
            $date = $monitorControl->process_date->format('Y-m-d');

            $list = MonitorControl::query()
                ->where('location', $location)
                ->whereDate('process_date', $date)
                ->where('status', 'draft')
                ->orderBy('truck_no')
                ->lockForUpdate()
                ->get();

            // normalize jika ada null
            $i = 1;
            foreach ($list as $mc) {
                if (!$mc->truck_no) {
                    $mc->truck_no = $i;
                    $mc->save();
                }
                $i++;
            }

            $idx = $list->search(fn ($x) => $x->id === $monitorControl->id);
            if ($idx === false) return back();

            if ($data['direction'] === 'up' && $idx > 0) {
                $a = $list[$idx - 1];
                $b = $list[$idx];
            } elseif ($data['direction'] === 'down' && $idx < ($list->count() - 1)) {
                $a = $list[$idx];
                $b = $list[$idx + 1];
            } else {
                return back();
            }

            // swap truck_no
            $tmp = $a->truck_no;
            $a->truck_no = $b->truck_no;
            $b->truck_no = $tmp;
            $a->save();
            $b->save();

            return back()->with('status', 'Urutan truk berhasil diubah.');
        });
    }
}