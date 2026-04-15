<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\HangingLine;
use App\Models\HangingLineSet;
use App\Models\MonitorControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HangingLandingController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');
        if ($date === null || $date === '') {
            $date = now('Asia/Jakarta')->toDateString(); // default: tanggal operasional hari ini
        }

        $perPage = $request->query('per_page', 20);
        $locations = ['SH01', 'SH02'];

        $baseQuery = function () use ($date) {
            return MonitorControl::query()
                ->with(['farm', 'expedition', 'plateNumber', 'hangingForm'])
                ->whereDate('process_date', $date);
        };

        $paginateShift = function (string $loc, string $sh, string $pageParam) use ($baseQuery, $perPage) {
            return $baseQuery()
                ->where('location', $loc)
                ->where('shift', $sh)
                ->orderByRaw("CASE WHEN status = 'done' THEN 1 ELSE 0 END")
                ->orderBy('truck_no')
                ->paginate($perPage, ['*'], $pageParam)
                ->withQueryString();
        };

        $data = [];
        foreach ($locations as $loc) {
            foreach (['pagi', 'malam'] as $sh) {
                $data[$loc][$sh] = $paginateShift($loc, $sh, "page_{$loc}_{$sh}");
            }
        }

        $runningLocations = HangingForm::query()
            ->where('status', 'running')
            ->whereHas('monitorControl', function ($mq) use ($date) {
                $mq->whereIn('location', ['SH01', 'SH02'])
                   ->whereDate('process_date', $date);
            })
            ->with('monitorControl:id,location')
            ->get()
            ->pluck('monitorControl.location')
            ->unique()
            ->values()
            ->all();

        return view('transaction.hanging_landing.index', [
            'data' => $data,
            'date' => $date,
            'locations' => $locations,
            'runningLocations' => $runningLocations,
            'perPage' => $perPage,
        ]);
    }

    public function open(MonitorControl $monitorControl)
    {
        $location = $monitorControl->location;

        // LOCK hanya untuk lokasi yg sama
        $runningSameLocation = HangingForm::query()
            ->where('status', 'running')
            ->whereHas('monitorControl', function ($q) use ($location) {
                $q->where('location', $location);
            })
            ->where('monitor_control_id', '!=', $monitorControl->id)
            ->exists();

        if ($runningSameLocation) {
            return back()->withErrors([
                'hanging' => "Tidak bisa membuka form lain karena masih ada proses RUNNING di lokasi {$location}.",
            ]);
        }

        return DB::transaction(function () use ($monitorControl) {
            $form = $monitorControl->hangingForm;

            if (!$form) {
                $form = HangingForm::create([
                    'monitor_control_id' => $monitorControl->id,
                    'status' => 'draft',
                    'unloading_time' => null,
                    'finish_time' => null,
                ]);
            }

            return redirect()->route('hanging-forms.show', $form);
        });
    }

    public function start(HangingForm $hangingForm, Request $request)
    {
        $hangingForm->load('monitorControl');
        $slug = $request->user()?->role?->slug;

        if ($hangingForm->status === 'done' && !in_array($slug, ['supervisor','superadmin'], true)) {
            return back()->withErrors(['start' => 'Form sudah DONE dan tidak bisa diubah.']);
        }

        $location = $hangingForm->monitorControl->location;

        // LOCK hanya untuk lokasi yg sama
        $runningSameLocation = HangingForm::query()
            ->where('status', 'running')
            ->whereHas('monitorControl', function ($q) use ($location) {
                $q->where('location', $location);
            })
            ->where('id', '!=', $hangingForm->id)
            ->exists();

        if ($runningSameLocation) {
            return back()->withErrors([
                'start' => "Tidak bisa mulai. Masih ada proses RUNNING di lokasi {$location}. Selesaikan dulu.",
            ]);
        }

        return DB::transaction(function () use ($hangingForm) {
            $monitor = $hangingForm->monitorControl;

            $monitor->update(['status' => 'running']);
            $hangingForm->update(['status' => 'running']);

            $existsLines = HangingLine::query()->where('hanging_form_id', $hangingForm->id)->exists();
            if ($existsLines) {
                return redirect()->route('hanging-forms.show', $hangingForm)->with('status', 'Proses dilanjutkan.');
            }

            $shackleCount = (int) $monitor->shackle_count;
            $setCount = (int) $monitor->set_count;

            for ($i = 1; $i <= $shackleCount; $i++) {
                $line = HangingLine::create([
                    'hanging_form_id' => $hangingForm->id,
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

            return redirect()
                ->route('hanging-forms.show', $hangingForm)
                ->with('status', 'Proses dimulai. Silakan input Form Hanging.');
        });
    }
}