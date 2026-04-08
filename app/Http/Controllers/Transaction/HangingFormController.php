<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\HangingLineSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HangingFormController extends Controller
{
    protected function getMaxCapacity(string $location, int $lineNo): int
    {
        $custom = [
            'SH01' => [17 => 46],
            'SH02' => [30 => 19],
        ];

        return $custom[$location][$lineNo] ?? 50;
    }

    public function show(HangingForm $hangingForm)
    {
        $hangingForm->load([
            'monitorControl.expedition',
            'monitorControl.plateNumber',
            'monitorControl.farm',
            'lines.sets',
        ]);

        $location   = $hangingForm->monitorControl?->location ?? '';
        $deadCount  = (int) ($hangingForm->dead_count ?? 0);
        $returCount = (int) ($hangingForm->retur_count ?? 0);

        $totalKosong = 0;
        $totalAyamShackle = 0;
        $fullBlockCount = 0;

        foreach ($hangingForm->lines as $line) {
            $cap = $this->getMaxCapacity($location, (int) $line->line_no);

            foreach ($line->sets as $set) {
                if ($set->empty_count === null) {
                    continue;
                }
                $empty = (int) $set->empty_count;

                $totalKosong += $empty;
                $totalAyamShackle += ($cap - $empty);

                if ($cap === 50 && $empty === 0) {
                    $fullBlockCount++;
                }
            }
        }

        $totalChickenMC = (int) ($hangingForm->monitorControl?->total_chicken ?? 0);
        $totalAyamBersih = max(0, $totalChickenMC - $deadCount - $returCount);
        $selisihAyam    = $totalChickenMC - $totalAyamBersih;

        return view('transaction.hanging_forms.show', [
            'form'         => $hangingForm,
            'totalKosong'  => $totalKosong,
            'totalAyam'    => $totalAyamBersih,
            'ayamMati'     => $deadCount,
            'ayamRetur'    => $returCount,
            'selisihAyam'  => $selisihAyam,
            'totalChicken' => $totalChickenMC,
            'fullBlockCount' => $fullBlockCount,
        ]);
    }

    public function updateCell(Request $request, HangingLineSet $hangingLineSet)
    {
        $hangingLineSet->load('line.form.monitorControl');

        if (($hangingLineSet->line?->form?->status ?? null) === 'done') {
            return response()->json([
                'ok' => false,
                'message' => 'Form sudah DONE dan tidak bisa diubah.',
            ], 422);
        }

        $location = $hangingLineSet->line?->form?->monitorControl?->location ?? '';
        $lineNo   = (int) ($hangingLineSet->line?->line_no ?? 0);
        $maxCap   = $this->getMaxCapacity($location, $lineNo);

        $data = $request->validate([
            'empty_count' => ['nullable','integer','min:0','max:' . $maxCap],
        ]);

        $hangingLineSet->update([
            'empty_count' => $data['empty_count'],
        ]);

        $ayam = ($hangingLineSet->empty_count === null)
            ? 0
            : $maxCap - (int) $hangingLineSet->empty_count;

        return response()->json([
            'ok' => true,
            'empty_count' => $hangingLineSet->empty_count,
            'ayam' => $ayam,
            'max' => $maxCap,
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