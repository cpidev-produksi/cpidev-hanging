<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\MonitorControl;
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
    private function isBlank($value): bool
    {
        return $value === null || $value === '';
    }

    private function normalizeNumeric($value): ?float
    {
        if ($this->isBlank($value)) return null;
        if (!is_numeric($value)) return null;
        return (float) $value;
    }

    public function apiStats(Request $request)
    {
        $data = $request->validate([
            'report_date' => ['required', 'date'],
            'location' => ['required', 'in:SH01,SH02'], // sesuaikan jika lokasi Anda lebih banyak
            'shift' => ['required', 'in:pagi,malam'],
        ]);

        $q = MonitorControl::query()
            ->whereDate('process_date', $data['report_date'])
            ->where('location', $data['location'])
            ->where('shift', $data['shift']);

        return response()->json([
            'truck_count' => (int) $q->count(),
            'received_chicken' => (int) ($q->sum('total_chicken') ?? 0),
        ]);
    }

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
        $validated = $request->validate([
            'report_date' => 'required|date',
            'location' => 'required|in:SH01,SH02',
            'shift' => 'required|in:pagi,malam',
            'truck_count' => 'required|integer|min:0',
            'received_chicken' => 'required|integer|min:0',
            'yield_percent' => 'nullable|numeric|min:0|max:100',

            'fresh_items' => 'required|array',
            'fresh_items.*.product_evis_id' => 'required|exists:product_evis,id',
            'fresh_items.*.bag_*' => 'nullable|numeric|min:0',
            'fresh_items.*.kg_*' => 'nullable|numeric|min:0',

            'frozen_items' => 'required|array',
            'frozen_items.*.product_evis_id' => 'required|exists:product_evis,id',
            'frozen_items.*.bag_*' => 'nullable|numeric|min:0',
            'frozen_items.*.kg_*' => 'nullable|numeric|min:0',
        ]);

        $now = Carbon::now('Asia/Jakarta');
        $user ??= Auth::user();
        $report = ReportEvis::create([
            'report_date' => $validated['report_date'],
            'location' => $validated['location'],
            'shift' => $validated['shift'],
            'truck_count' => $validated['truck_count'],
            'received_chicken' => $validated['received_chicken'],
            'yield_percent' => $validated['yield_percent'] ?? null,
            'created_by' => $user?->id,
            'status' => 'draft',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $satuanCache = [];

        $freshTotals = $this->saveCategoryItems($report, $validated['fresh_items'], 'fresh', $satuanCache);
        $frozenTotals = $this->saveCategoryItems($report, $validated['frozen_items'], 'frozen', $satuanCache);

        $report->update([
            'fresh_total_bag' => $freshTotals['bag'],
            'fresh_total_kg' => $freshTotals['kg'],
            'frozen_total_bag' => $frozenTotals['bag'],
            'frozen_total_kg' => $frozenTotals['kg'],
        ]);

        // Simpan items
        // foreach ($validated['fresh_items'] as $item) {
        //     $productId = (string) $item['product_evis_id'];

        //     if (!array_key_exists($productId, $satuanCache)) {
        //         $satuanCache[$productId] = (float) (ProductEvis::query()->whereKey($productId)->value('satuan') ?? 0);
        //     }
        //     $satuan = (float) $satuanCache[$productId];

        //     $reportItem = new ReportEvisItem([
        //         'product_evis_id' => $item['product_evis_id'],
        //     ]);

        //     // Set bag & kg untuk 10 kolom
        //     for ($i = 1; $i <= 10; $i++) {
        //         $bag = $this->normalizeNumeric($item["bag_$i"] ?? null);
        //         $kg  = $this->normalizeNumeric($item["kg_$i"] ?? null);

        //         // default bag null -> 0 (mengikuti behavior existing store sebelumnya)
        //         $bagToSave = $bag ?? 0;

        //         // kalau kg kosong, auto hitung dari bag*satuan
        //         if ($kg === null) {
        //             $kgToSave = ($satuan > 0) ? ($bagToSave * $satuan) : 0;
        //         } else {
        //             // kalau user isi kg, hormati input user
        //             $kgToSave = $kg;
        //         }

        //         $reportItem->setAttribute("bag_$i", $bagToSave);
        //         $reportItem->setAttribute("kg_$i", $kgToSave);
        //     }

        //     $reportItem->calculateTotals();
        //     $report->items()->save($reportItem);
        // }

        $this->updateReportTotals($report);

        return redirect()->route('report-evis.index')
            ->with('status', 'Report berhasil dibuat.');
    }

    public function edit(ReportEvis $reportEvis)
    {
        if (!$reportEvis->canBeEdited()) abort(403, 'Unauthorized');

        $reportEvis->load('items.product');

        // pisahkan items by category (data lama default fresh)
        $freshItems = $reportEvis->items->where('category', 'fresh')->values();
        $frozenItems = $reportEvis->items->where('category', 'frozen')->values();

        $products = ProductEvis::orderBy('name', 'asc')->get();

        return view('report.evis.reports.form', [
            'reportEvis' => $reportEvis,
            'products' => $products,
            'freshItems' => $freshItems,
            'frozenItems' => $frozenItems,
        ]);
    }

    public function update(Request $request, ReportEvis $reportEvis)
    {
        if (!$reportEvis->canBeEdited()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'report_date' => 'required|date',
            'location' => 'required|in:SH01,SH02',
            'shift' => 'required|in:pagi,malam',
            'truck_count' => 'required|integer|min:0',
            'received_chicken' => 'required|integer|min:0',
            'yield_percent' => 'nullable|numeric|min:0|max:100',

            'fresh_items' => 'required|array',
            'fresh_items.*.product_evis_id' => 'required|exists:product_evis,id',
            'fresh_items.*.bag_*' => 'nullable|numeric|min:0',
            'fresh_items.*.kg_*' => 'nullable|numeric|min:0',

            'frozen_items' => 'required|array',
            'frozen_items.*.product_evis_id' => 'required|exists:product_evis,id',
            'frozen_items.*.bag_*' => 'nullable|numeric|min:0',
            'frozen_items.*.kg_*' => 'nullable|numeric|min:0',
        ]);
 
        $reportEvis->update([
            'report_date' => $validated['report_date'],
            'location' => $validated['location'],
            'shift' => $validated['shift'],
            'truck_count' => $validated['truck_count'],
            'received_chicken' => $validated['received_chicken'],
            'yield_percent' => $validated['yield_percent'] ?? null,
        ]);

        // Hapus items lama
        $reportEvis->items()->delete();

        $satuanCache = [];

        $freshTotals = $this->saveCategoryItems($reportEvis, $validated['fresh_items'], 'fresh', $satuanCache);
        $frozenTotals = $this->saveCategoryItems($reportEvis, $validated['frozen_items'], 'frozen', $satuanCache);

        $reportEvis->update([
            'fresh_total_bag' => $freshTotals['bag'],
            'fresh_total_kg' => $freshTotals['kg'],
            'frozen_total_bag' => $frozenTotals['bag'],
            'frozen_total_kg' => $frozenTotals['kg'],
        ]);

        $this->updateReportTotals($reportEvis);

        return redirect()->route('report-evis.index')
            ->with('status', 'Report berhasil diperbarui.');
    }

    private function saveCategoryItems(ReportEvis $report, array $items, string $category, array &$satuanCache): array
    {
        $totalBag = 0.0;
        $totalKg = 0.0;

        foreach ($items as $item) {
            $productId = (string) $item['product_evis_id'];

            if (!array_key_exists($productId, $satuanCache)) {
                $satuanCache[$productId] = (float) (ProductEvis::query()->whereKey($productId)->value('satuan') ?? 0);
            }
            $satuan = (float) $satuanCache[$productId];

            $reportItem = new ReportEvisItem([
                'product_evis_id' => $item['product_evis_id'],
                'category' => $category,
            ]);

            for ($i = 1; $i <= 10; $i++) {
                $bag = $this->normalizeNumeric($item["bag_$i"] ?? null);
                $kg  = $this->normalizeNumeric($item["kg_$i"] ?? null);

                // store: default bag null -> 0
                $bagToSave = $bag ?? 0;

                if ($kg === null) {
                    $kgToSave = ($satuan > 0) ? ($bagToSave * $satuan) : 0;
                } else {
                    $kgToSave = $kg;
                }

                $reportItem->setAttribute("bag_$i", $bagToSave);
                $reportItem->setAttribute("kg_$i", $kgToSave);
            }

            $reportItem->calculateTotals();
            $report->items()->save($reportItem);

            $totalBag += (float)($reportItem->total_bag ?? 0);
            $totalKg  += (float)($reportItem->total_kg ?? 0);
        }

        return ['bag' => $totalBag, 'kg' => $totalKg];
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
            /** @var \SimpleSoftwareIO\QrCode\Generator $qrCode */
            $qrCode = QrCode::format('svg');
            $svg = $qrCode
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
        $freshBag = (float) ($report->fresh_total_bag ?? 0);
        $freshKg  = (float) ($report->fresh_total_kg ?? 0);
        $frozenBag = (float) ($report->frozen_total_bag ?? 0);
        $frozenKg  = (float) ($report->frozen_total_kg ?? 0);

        $report->update([
            'total_bag' => $freshBag + $frozenBag,
            'total_kg' => $freshKg + $frozenKg,
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