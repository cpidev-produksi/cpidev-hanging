<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\MonitorControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturMatiLandingController extends Controller
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
                ->with([
                    'farm',
                    'expedition',
                    'plateNumber',
                    'hangingForm',
                    'hangingForm.returItems',
                ])
                ->whereDate('process_date', $date);
        };

        $paginateShift = function (string $loc, string $sh, string $pageParam) use ($baseQuery, $perPage) {
            return $baseQuery()
                ->where('location', $loc)
                ->where('shift', $sh)
                ->orderByRaw("FIELD(shift,'pagi','malam')")
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

        return view('transaction.retur_mati.landing', [
            'data' => $data,
            'date' => $date,
            'locations' => $locations,
            'perPage' => $perPage,
        ]);
    }

    public function open(MonitorControl $monitorControl)
    {
        return DB::transaction(function () use ($monitorControl) {
            $form = $monitorControl->hangingForm;

            if (!$form) {
                $form = HangingForm::create([
                    'monitor_control_id' => $monitorControl->id,
                    'status' => 'draft',
                    'unloading_time' => null,
                    'finish_time' => null,

                    // 'dead_count' => 0,
                    // 'retur_count' => 0,
                    // 'retur_total_kg' => 0,
                ]);
            }

            return redirect()->route('retur-mati.edit', $form);
        });
    }
}
