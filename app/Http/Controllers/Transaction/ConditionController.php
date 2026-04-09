<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\MonitorControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConditionController extends Controller
{
    public function landing(Request $request)
    {
        $date = $request->query('date'); // optional YYYY-MM-DD

        $q = MonitorControl::query()
            ->with(['farm','expedition','plateNumber','hangingForm'])
            ->orderBy('process_date', 'desc')
            ->orderBy('location')
            ->orderByRaw("FIELD(shift,'pagi','malam')") // supaya pagi dulu
            ->orderBy('truck_no');

        if ($date !== null && $date !== '') {
            $q->whereDate('process_date', $date);
        }

        $items = $q->paginate(200)->withQueryString(); // <-- naikkan limit

        return view('transaction.conditions.landing', [
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

                    // default rekap existing feature
                    'dead_count' => 0,
                    'retur_count' => 0,
                    'retur_total_kg' => 0,
                ]);
            }

            return redirect()->route('conditions.edit', $form);
        });
    }

    public function edit(HangingForm $hangingForm)
    {
        $hangingForm->load(['monitorControl.expedition','monitorControl.plateNumber','monitorControl.farm']);

        return view('transaction.conditions.edit', [
            'form' => $hangingForm,
        ]);
    }

    public function update(Request $request, HangingForm $hangingForm)
    {
        if ($hangingForm->status === 'done') {
            return back()->withErrors(['conditions' => 'Form sudah DONE dan tidak bisa diubah.']);
        }

        $data = $request->validate([
            'basket_condition' => ['required', 'in:sangat_basah,basah,kering'],
            'truck_platform_condition' => ['required', 'in:bak_berisi_air,bak_kering,benda_lain'],
            'feather_condition' => ['required', 'in:sangat_basah,medium_basah,basah,kering'],
        ]);

        $hangingForm->update($data);

        return redirect()
            ->route('conditions.landing')
            ->with('status', 'Kondisi tersimpan.');
    }
}
