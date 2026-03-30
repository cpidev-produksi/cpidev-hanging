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
    public function index()
    {
        $items = MonitorControl::query()
            ->with(['expedition', 'plateNumber', 'farm', 'hangingForm'])
            ->latest()
            ->paginate(20);

        $runningLocations = MonitorControl::query()
            ->where('status', 'running')
            ->pluck('location')
            ->unique()
            ->values()
            ->all();

        return view('transaction.monitor_controls.index', compact('items', 'runningLocations'));
    }

    public function create()
    {
        $expeditions = Expedition::query()
            ->with(['plateNumbers' => fn ($q) => $q->orderBy('plate_number')])
            ->orderBy('name')
            ->get();

        $farms = Farm::query()->orderBy('name')->get();
        $sizes = ['1.10-1.20', '1.20-1.40', '1.40-1.60', '1.60-1.80', '1.80-2.00', '2.00-2.20', '2.20-2.40', '2.40-2.60', '2.60-2.80', '2.80-3.00'];

        return view('transaction.monitor_controls.create', compact('expeditions', 'farms', 'sizes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location' => ['required', 'in:SH01,SH02'],
            'process_date' => ['required', 'date'],
            'shift' => ['required', 'in:pagi,malam'],
            'size' => ['required', 'numeric', 'min:1.2', 'max:1.5'],

            'expedition_id' => ['required', 'exists:expeditions,id'],
            'plate_number_id' => ['required', 'exists:plate_numbers,id'],

            'farm_id' => ['required', 'exists:farms,id'],
            'farm_fee_amount' => ['required', 'numeric', 'min:0'],
        ], [
            'expedition_id.required' => 'Ekspedisi wajib dipilih.',
            'plate_number_id.required' => 'No Polisi wajib dipilih.',
        ]);

        // plate number harus milik ekspedisi tsb
        $ok = PlateNumber::query()
            ->where('id', $data['plate_number_id'])
            ->where('expedition_id', $data['expedition_id'])
            ->exists();

        if (!$ok) {
            return back()
                ->withErrors(['plate_number_id' => 'No Polisi tidak sesuai dengan Ekspedisi yang dipilih.'])
                ->withInput();
        }

        $meta = $this->locationMeta($data['location']);
        $data['set_count'] = $meta['set_count'];
        $data['shackle_count'] = $meta['shackle_count'];
        $data['status'] = 'draft';
        $data['report_code'] = $this->generateReportCode($data['location'], $data['process_date'], $data['shift']);

        $monitor = MonitorControl::create($data);

        return redirect()->route('monitor-controls.index')->with('status', 'Kontrol monitor dibuat: ' . $monitor->report_code);
    }

    public function edit(MonitorControl $monitorControl)
    {
        if ($monitorControl->status !== 'draft') {
            return redirect()->route('monitor-controls.index')->with('status', 'Tidak bisa edit jika sudah running/done.');
        }

        $monitorControl->load(['expedition', 'plateNumber', 'farm']);

        $expeditions = Expedition::query()
            ->with(['plateNumbers' => fn ($q) => $q->orderBy('plate_number')])
            ->orderBy('name')
            ->get();

        $farms = Farm::query()->orderBy('name')->get();
        $sizes = [1.2, 1.3, 1.4, 1.5];

        return view('transaction.monitor_controls.edit', [
            'monitor' => $monitorControl,
            'expeditions' => $expeditions,
            'farms' => $farms,
            'sizes' => $sizes,
        ]);
    }

    public function update(Request $request, MonitorControl $monitorControl)
    {
        if ($monitorControl->status !== 'draft') {
            return back()->with('status', 'Tidak bisa edit jika sudah running/done.');
        }

        $data = $request->validate([
            'process_date' => ['required', 'date'],
            'shift' => ['required', 'in:pagi,malam'],
            'size' => ['required', 'numeric', 'min:1.2', 'max:1.5'],

            'expedition_id' => ['required', 'exists:expeditions,id'],
            'plate_number_id' => ['required', 'exists:plate_numbers,id'],

            'farm_id' => ['required', 'exists:farms,id'],
            'farm_fee_amount' => ['required', 'numeric', 'min:0'],
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

    public function destroy(MonitorControl $monitorControl)
    {
        if ($monitorControl->status !== 'draft') {
            return back()->with('status', 'Tidak bisa hapus jika sudah running/done.');
        }

        $monitorControl->delete();
        return back()->with('status', 'Kontrol monitor dihapus.');
    }

    public function start(MonitorControl $monitorControl)
    {
        if ($monitorControl->status !== 'draft') {
            return back()->with('status', 'Sudah berjalan / selesai.');
        }

        $existsRunning = MonitorControl::query()
            ->where('location', $monitorControl->location)
            ->where('status', 'running')
            ->exists();

        if ($existsRunning) {
            return back()->withErrors([
                'start' => "Tidak bisa mulai. Masih ada proses RUNNING di lokasi {$monitorControl->location}. Selesaikan dulu proses tersebut.",
            ]);
        }

        return DB::transaction(function () use ($monitorControl) {
            $monitorControl->update(['status' => 'running']);

            $form = HangingForm::create([
                'monitor_control_id' => $monitorControl->id,
                'status' => 'running',
                'unloading_time' => null,
                'finish_time' => null,
            ]);

            $shackleCount = (int) $monitorControl->shackle_count;
            $setCount = (int) $monitorControl->set_count;

            for ($i = 1; $i <= $shackleCount; $i++) {
                $line = HangingLine::create([
                    'hanging_form_id' => $form->id,
                    'line_no' => $i,
                    'shackle_label' => "{$i}-" . ($i + 1),
                    'rule_min' => ($i - 1) * 50,
                    'rule_max' => $i * 50,
                ]);

                for ($setNo = 1; $setNo <= $setCount; $setNo++) {
                    HangingLineSet::create([
                        'hanging_line_id' => $line->id,
                        'set_no' => $setNo,
                        'empty_count' => null,
                    ]);
                }
            }

            return redirect()->route('hanging-forms.show', $form)
                ->with('status', 'Proses dimulai. Silakan input Form Hanging.');
        });
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
}