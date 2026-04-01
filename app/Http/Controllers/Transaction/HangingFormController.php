<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\HangingLineSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HangingFormController extends Controller
{
    public function show(HangingForm $hangingForm)
    {
        $hangingForm->load([
            'monitorControl.expedition',
            'monitorControl.plateNumber',
            'monitorControl.farm',
            'lines.sets',
        ]);

        $allSets = $hangingForm->lines->flatMap->sets;
        $deadCount = (int) ($hangingForm->dead_count ?? 0);

        $totalAyamShackle = (int) $allSets->sum(function ($s) {
            if ($s->empty_count === null) return 0;
            return 50 - (int)$s->empty_count;
        });

        $totalAyamBersih = max(0, $totalAyamShackle - $deadCount);

        $totalKosong = (int) $allSets->sum(fn ($s) => (int) ($s->empty_count ?? 0));

        return view('transaction.hanging_forms.show', [
            'form' => $hangingForm,
            'totalKosong' => $totalKosong,
            'totalAyam' => $totalAyamBersih,
            'ayamMati' => $deadCount,
        ]);
    }

    public function updateCell(Request $request, HangingLineSet $hangingLineSet)
    {
        $hangingLineSet->load('line.form');

        if (($hangingLineSet->line?->form?->status ?? null) === 'done') {
            return response()->json([
                'ok' => false,
                'message' => 'Form sudah DONE dan tidak bisa diubah.',
            ], 422);
        }

        $data = $request->validate([
            'empty_count' => ['nullable','integer','min:0','max:50'],
        ]);

        $hangingLineSet->update([
            'empty_count' => $data['empty_count'],
        ]);

        $ayam = ($hangingLineSet->empty_count === null)
            ? 0
            : 50 - (int) $hangingLineSet->empty_count;

        return response()->json([
            'ok' => true,
            'empty_count' => $hangingLineSet->empty_count,
            'ayam' => $ayam,
        ]);
    }

    public function finish(Request $request, HangingForm $hangingForm)
    {
        if ($hangingForm->status === 'done') {
            return back()->withErrors(['finish' => 'Form sudah DONE dan tidak bisa diselesaikan ulang.']);
        }

        $data = $request->validate([
            'unloading_time' => ['nullable', 'date_format:H:i'],
            'finish_time' => ['nullable', 'date_format:H:i'],
        ], [
            'unloading_time.date_format' => 'Format jam bongkar harus HH:MM.',
            'finish_time.date_format' => 'Format jam selesai harus HH:MM.',
        ]);

        return DB::transaction(function () use ($hangingForm, $data) {
            $hangingForm->update([
                'unloading_time' => $data['unloading_time'] ?? $hangingForm->unloading_time,
                'finish_time' => $data['finish_time'] ?? $hangingForm->finish_time,
                'status' => 'done',
            ]);

            $hangingForm->monitorControl()->update([
                'status' => 'done',
            ]);

            return redirect()->route('hanging.landing')->with('status', 'Proses selesai. Laporan tersimpan.');
        });
    }
}