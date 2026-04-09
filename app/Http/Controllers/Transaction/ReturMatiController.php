<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\HangingReturItem;
use App\Models\MonitorControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturMatiController extends Controller
    {
        public function index(Request $request)
        {
            $date = $request->query('date') ?? now()->toDateString();

            $q = MonitorControl::query()
                ->with([
                    'farm',
                    'expedition',
                    'plateNumber',
                    'hangingForm',
                    'hangingForm.returItems'
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
        
    public function edit(HangingForm $hangingForm)
    {
        $hangingForm->load([
            'monitorControl.expedition',
            'monitorControl.plateNumber',
            'monitorControl.farm',
            'returItems',
        ]);

        return view('transaction.retur_mati.edit', [
            'form' => $hangingForm,
        ]);
    }

    public function update(Request $request, HangingForm $hangingForm)
    {
        // Jika DONE: tidak boleh diubah
        if ($hangingForm->status === 'done') {
            return back()->withErrors(['retur' => 'Form sudah DONE dan tidak bisa diubah.']);
        }

        $data = $request->validate([
            'dead_count' => ['required', 'integer', 'min:0'],

            // array repeater weights
            'retur_weights' => ['nullable', 'array'],
            'retur_weights.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // bersihkan input: ambil yang > 0
        $weights = collect($data['retur_weights'] ?? [])
            ->filter(fn ($v) => $v !== null && $v !== '' && (float)$v > 0)
            ->map(fn ($v) => round((float)$v, 2))
            ->values();

        return DB::transaction(function () use ($hangingForm, $data, $weights) {
            // replace all items (cara cepat + simpel)
            HangingReturItem::query()
                ->where('hanging_form_id', $hangingForm->id)
                ->delete();

            foreach ($weights as $w) {
                HangingReturItem::create([
                    'hanging_form_id' => $hangingForm->id,
                    'weight_kg' => $w,
                ]);
            }

            $returCount = (int) $weights->count();
            $returTotalKg = (float) $weights->sum();

            $hangingForm->update([
                'dead_count' => (int) $data['dead_count'],
                'retur_count' => $returCount,
                'retur_total_kg' => $returTotalKg,
            ]);

            return redirect()
                ->route('retur-mati.landing')
                ->with('status', 'Data Ayam Retur & Mati tersimpan.');
        });
    }
}
