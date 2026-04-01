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
        $date = $request->query('date'); // optional YYYY-MM-DD

        $q = MonitorControl::query()
            ->with([
                'farm',
                'expedition',
                'plateNumber',
                'hangingForm',          // to show summary
                'hangingForm.returItems' // optional, if you want detail count safe; we already store retur_count though
            ])
            ->orderBy('process_date', 'desc')
            ->orderBy('location')
            ->orderBy('truck_no');

        if ($date) {
            $q->whereDate('process_date', $date);
        }

        $items = $q->paginate(50);

        return view('transaction.retur_mati.landing', [
            'items' => $items,
            'date' => $date,
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

                    'dead_count' => 0,
                    'retur_count' => 0,
                    'retur_total_kg' => 0,
                ]);
            }

            return redirect()->route('retur-mati.edit', $form);
        });
    }
}
