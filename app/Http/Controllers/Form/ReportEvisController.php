<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\ProductEvis;
use App\Models\ReportEvis;
use App\Models\ReportEvisItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReportEvisController extends Controller
{
    public function index(Request $request)
    {
        $query = ReportEvis::with('createdBy', 'approvedBy');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('report_date', $request->date);
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(10);

        return view('report.evis.reports.index', compact('reports'));
    }

    public function create()
    {
        $products = ProductEvis::orderBy('name', 'asc')->get();
        return view('report.evis.reports.form', compact('products'));
    }

    public function store(Request $request)
    {
        Log::info('Store Request:', $request->all());
        $validated = $request->validate([
            'report_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_evis_id' => 'required|exists:product_evis,id',
            'items.*.bag_1' => 'nullable|numeric|min:0',
            'items.*.kg_1' => 'nullable|numeric|min:0',
            'items.*.bag_*' => 'nullable|numeric|min:0',
            'items.*.kg_*' => 'nullable|numeric|min:0',
        ]);

        Log::info('Validated Data:', $validated);

        $now = Carbon::now('Asia/Jakarta');
        $user ??= Auth::user();
        $report = ReportEvis::create([
            'report_date' => $validated['report_date'],
            'created_by' => $user?->id,
            'status' => 'draft',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Simpan items
        foreach ($validated['items'] as $item) {
            $reportItem = new ReportEvisItem([
                'product_evis_id' => $item['product_evis_id'],
            ]);

            // Set bag & kg untuk 10 kolom
            for ($i = 1; $i <= 10; $i++) {
                $reportItem->setAttribute("bag_$i", $item["bag_$i"] ?? 0);
                $reportItem->setAttribute("kg_$i", $item["kg_$i"] ?? 0);
            }

            $reportItem->calculateTotals();
            $report->items()->save($reportItem);
        }

        $this->updateReportTotals($report);

        return redirect()->route('report-evis.index')
            ->with('status', 'Report berhasil dibuat.');
    }

    public function edit(ReportEvis $reportEvis)
    {
        if (!$reportEvis->canBeEdited()) {
            abort(403, 'Unauthorized');
        }

        $reportEvis = $reportEvis->load('items.product');
        $products = ProductEvis::orderBy('name', 'asc')->get();
        return view('report.evis.reports.form', compact('reportEvis', 'products'));
    }

    public function update(Request $request, ReportEvis $reportEvis)
    {
        if (!$reportEvis->canBeEdited()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'report_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_evis_id' => 'required|exists:product_evis,id',
            'items.*.bag_*' => 'nullable|numeric|min:0',
            'items.*.kg_*' => 'nullable|numeric|min:0',
        ]);
 
        $reportEvis->update(['report_date' => $validated['report_date']]);

        // Hapus items lama
        $reportEvis->items()->delete();

        // Simpan items baru
        foreach ($validated['items'] as $item) {
            $reportItem = new ReportEvisItem([
                'product_evis_id' => $item['product_evis_id'],
            ]);

            for ($i = 1; $i <= 10; $i++) {
                $bag = $item["bag_$i"] ?? null;
                $kg  = $item["kg_$i"] ?? null;

                if ($bag === '') $bag = null;
                if ($kg === '') $kg = null;

                $reportItem->setAttribute("bag_$i", $bag);
                $reportItem->setAttribute("kg_$i", $kg);
            }

            $reportItem->calculateTotals();
            $reportEvis->items()->save($reportItem);
        }

        $this->updateReportTotals($reportEvis);

        return redirect()->route('report-evis.index')
            ->with('status', 'Report berhasil diperbarui.');
    }

    public function show(ReportEvis $reportEvis)
    {
        $report = $reportEvis->load('items.product', 'createdBy', 'approvedBy');
        return view('report.evis.reports.show', compact('report'));
    }

    public function approve(ReportEvis $reportEvis)
    {
        if (!$reportEvis->canBeApproved()) {
            abort(403, 'Unauthorized');
        }

        $now = Carbon::now('Asia/Jakarta');
        $user ??= Auth::user();
        $reportEvis->update([
            'status' => 'approved',
            'approved_by' => $user?->id,
            'approved_at' => $now,
            'approved_signature_path' => $user?->signature_path,
        ]);

        return redirect()->route('report-evis.index')
            ->with('status', 'Report berhasil disetujui.');
    }

    public function exportPdf(ReportEvis $reportEvis)
    {
        try {
            Log::info('Export PDF called for report ID: ' . $reportEvis->id);

            $report = $reportEvis->load('items.product', 'createdBy', 'approvedBy');

            // Validate report has items
            if ($report->items->isEmpty()) {
                Log::warning('Report has no items: ' . $reportEvis->id);
                return redirect()->back()
                    ->with('error', 'Report tidak memiliki item. Tidak bisa export PDF.');
            }

            // Generate QR Codes
            $createdPdfUrl = route('report-evis.pdf', $report, true);
            $qrCreatedBy = $this->generateQrCodeSvgDataUri($createdPdfUrl);

            $qrApprovedBy = null;
            if ($report->isApproved()) {
                $approvedText =
                    "Approved by:\n" .
                    ($report->approvedBy->name ?? '-') . "\n" .
                    optional($report->approved_at)->translatedFormat('d F Y') . "\n" .
                    optional($report->approved_at)->format('H:i');

                $qrApprovedBy = $this->generateQrCodeSvgDataUri($approvedText);
            }

            // Hitung kolom terisi tertinggi untuk menentukan orientasi
            $maxUsedCol = 0;
            foreach ($report->items as $item) {
                for ($c = 1; $c <= 10; $c++) {
                    $b = $item->getAttribute("bag_$c");
                    $k = $item->getAttribute("kg_$c");
                    if (($b !== null && $b !== '' && (float)$b > 0) ||
                        ($k !== null && $k !== '' && (float)$k > 0)) {
                        $maxUsedCol = max($maxUsedCol, $c);
                    }
                }
            }
            // Lebih dari 4 kolom → landscape agar tabel tidak terpotong
            $orientation = $maxUsedCol > 4 ? 'landscape' : 'portrait';

            $pdf = Pdf::loadView('report.evis.reports.pdf', [
                'report'       => $report,
                'qrCreatedBy'  => $qrCreatedBy,
                'qrApprovedBy' => $qrApprovedBy,
                'orientation'  => $orientation,
            ]);

            // Kustomisasi PDF settings
            $pdf->setPaper('A4', $orientation);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('isPhpEnabled', true);

            return $pdf->download("Report-Evis-{$report->report_date->format('Y-m-d')}.pdf");

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if (config('app.debug')) {
                abort(500, $e->getMessage());
            }
            
            return redirect()->back()->with('error', 'Gagal membuat PDF.');
        }
    }

    private function generateQrCodeSvgDataUri(string $text): string
    {
        try {
            $svg = QrCode::format('svg')
                ->size(150)
                ->margin(1)
                ->errorCorrection('H')
                ->encoding('UTF-8')
                ->generate($text);

            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
            return '';
        }
    }

    private function updateReportTotals(ReportEvis $report)
    {
        $totalBag = $report->items()->sum('total_bag');
        $totalKg = $report->items()->sum('total_kg');

        $report->update([
            'total_bag' => $totalBag,
            'total_kg' => $totalKg,
        ]);
    }

    public function destroy(ReportEvis $reportEvis)
    {
        if (!$reportEvis->canBeEdited()) {
            abort(403, 'Unauthorized');
        }

        $reportEvis->delete();

        return redirect()->route('report-evis.index')
            ->with('status', 'Report berhasil dihapus.');
    }
}