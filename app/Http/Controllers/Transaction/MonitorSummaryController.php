<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MonitorControl;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MonitorSummaryController extends Controller
{
    private function assertComplete(MonitorControl $mc): void
    {
        $mc->load([
            'farm','expedition','plateNumber',
            'hangingForm.lines.sets',
        ]);

        abort_unless($mc->status === 'done', 403, 'Monitor control belum DONE.');

        $form = $mc->hangingForm;
        abort_unless($form && $form->status === 'done', 403, 'Hanging form belum DONE.');

        // QC kondisi wajib lengkap
        abort_unless(!empty($form->basket_condition), 403, 'QC kondisi (keranjang) belum diisi.');
        abort_unless(!empty($form->truck_platform_condition), 403, 'QC kondisi (platform truk) belum diisi.');
        abort_unless(!empty($form->feather_condition), 403, 'QC kondisi (bulu) belum diisi.');

        // Retur & mati: boleh 0 tapi field harus ada (kolom default 0)
        abort_unless($form->dead_count !== null, 403, 'Input ayam mati belum ada.');
        abort_unless($form->retur_count !== null && $form->retur_total_kg !== null, 403, 'Input ayam retur belum lengkap.');
    }

    public function show(Request $request, MonitorControl $monitorControl)
    {
        $this->assertComplete($monitorControl);

        $form = $monitorControl->hangingForm;
        $sets = $form->lines->flatMap->sets;

        $totalKosong = (int) $sets->sum(fn ($s) => (int) ($s->empty_count ?? 0));
        $totalAyamTerima = (int) $sets->sum(function ($s) {
            if ($s->empty_count === null) return 0;
            return 50 - (int)$s->empty_count;
        });

        return view('transaction.monitor_controls.summary', [
            'mc' => $monitorControl,
            'form' => $form,
            'totalKosong' => $totalKosong,
            'totalAyamTerima' => $totalAyamTerima,
            'createdBy' => $request->user()->name,
            //'createdBy' => $request->user()->name ?? $request->user()->email ?? $request->user()->username ?? '—',
        ]);
    }

    public function pdf(Request $request, MonitorControl $monitorControl)
    {
        $this->assertComplete($monitorControl);

        $form = $monitorControl->hangingForm;
        $sets = $form->lines->flatMap->sets;

        $totalKosong = (int) $sets->sum(fn ($s) => (int) ($s->empty_count ?? 0));
        $totalAyamTerima = (int) $sets->sum(function ($s) {
            if ($s->empty_count === null) return 0;
            return 50 - (int)$s->empty_count;
        });

        $createdBy = $request->user()->name ?? $request->user()->email ?? $request->user()->username ?? '—';

        $pdf = Pdf::loadView('transaction.monitor_controls.summary_pdf', [
            'mc' => $monitorControl,
            'form' => $form,
            'totalKosong' => $totalKosong,
            'totalAyamTerima' => $totalAyamTerima,
            'createdBy' => $createdBy,
        ])->setPaper('A4', 'portrait');

        $filename = 'rekap-' . $monitorControl->report_code . '.pdf';
        return $pdf->download($filename);
    }
}
