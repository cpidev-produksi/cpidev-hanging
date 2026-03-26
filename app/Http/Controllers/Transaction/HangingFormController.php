<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\HangingLineSet;
use Illuminate\Http\Request;

class HangingFormController extends Controller
{
    public function show(HangingForm $hangingForm)
    {
        $hangingForm->load([
            'monitorControl.truck.expedition',
            'monitorControl.farm',
            'lines.sets',
        ]);

        // totals
        $allSets = $hangingForm->lines->flatMap->sets;
        $totalKosong = (int) $allSets->sum('empty_count');
        $totalAyam = (int) $allSets->sum(fn ($s) => 50 - (int)$s->empty_count);

        return view('transaction.hanging_forms.show', [
            'form' => $hangingForm,
            'totalKosong' => $totalKosong,
            'totalAyam' => $totalAyam,
            'ayamMati' => 0,
        ]);
    }

    public function updateCell(Request $request, HangingLineSet $hangingLineSet)
    {
        $data = $request->validate([
            'empty_count' => ['required','integer','min:0','max:50'],
        ]);

        $hangingLineSet->update($data);

        // response ringan untuk ajax
        $ayam = 50 - (int) $hangingLineSet->empty_count;

        return response()->json([
            'ok' => true,
            'empty_count' => (int)$hangingLineSet->empty_count,
            'ayam' => $ayam,
        ]);
    }
}
