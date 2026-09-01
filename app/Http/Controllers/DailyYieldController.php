<?php

namespace App\Http\Controllers;

use App\Imports\DailyYieldImport;
use App\Models\DailyYieldUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DailyYieldController extends Controller
{
    public function index(Request $request): View
    {
        // Semua batch yang berstatus "versi terbaru" per periode -> untuk dropdown filter periode
        $periodeOptions = DailyYieldUpload::query()
            ->where('is_latest', true)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get(['id', 'bulan', 'tahun']);
 
        $selectedUploadId = $request->query('upload_id');
 
        $currentUpload = $selectedUploadId
            ? DailyYieldUpload::with(['details' => fn ($q) => $q->orderBy('plant')])->find($selectedUploadId)
            : DailyYieldUpload::with(['details' => fn ($q) => $q->orderBy('plant')])
                ->where('is_latest', true)
                ->orderByDesc('tahun')
                ->orderByDesc('bulan')
                ->first();
 
        $plants = $currentUpload?->details ?? collect();
 
        // Riwayat upload (semua versi, termasuk yang lama) untuk keperluan audit trail
        $riwayatUpload = DailyYieldUpload::with('uploader')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();
 
        return view('daily-yields.index', [
            'periodeOptions' => $periodeOptions,
            'currentUpload' => $currentUpload,
            'plants' => $plants,
            'riwayatUpload' => $riwayatUpload,
            'bulanNames' => DailyYieldUpload::BULAN_NAMES,
        ]);
    }
 
    /**
     * Proses upload Excel baru untuk suatu Periode (Bulan + Tahun).
     * Upload ulang periode yang sama TIDAK menimpa data lama -> disimpan sebagai
     * batch/versi baru (histori tetap ada di tabel daily_yield_uploads).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [], [
            'bulan' => 'Periode Bulan',
            'tahun' => 'Tahun',
            'file' => 'File Excel',
        ]);
 
        $import = new DailyYieldImport();
        Excel::import($import, $request->file('file'));
        $rows = $import->getRows();
 
        if (empty($rows)) {
            return back()
                ->withErrors(['file' => 'File Excel tidak berisi data plant yang valid. Pastikan format mengikuti template yang disediakan.'])
                ->withInput();
        }
 
        DB::transaction(function () use ($validated, $request, $rows) {
            // Tandai versi lama untuk periode yang sama sebagai bukan versi terbaru
            DailyYieldUpload::where('bulan', $validated['bulan'])
                ->where('tahun', $validated['tahun'])
                ->update(['is_latest' => false]);
 
            $path = $request->file('file')->store('daily-yield-uploads', 'local');
 
            $upload = DailyYieldUpload::create([
                'bulan' => $validated['bulan'],
                'tahun' => $validated['tahun'],
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $path,
                'uploaded_by' => $request->user()->id,
                'is_latest' => true,
            ]);
 
            foreach ($rows as $row) {
                $upload->details()->create($row);
            }
        });
 
        return redirect()
            ->route('daily-yields.index')
            ->with('success', 'Data Daily Monitoring Yield berhasil diupload (' . count($rows) . ' plant).');
    }
 
    /**
     * Unduh file template Excel kosong.
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $path = storage_path('app/templates/Template_Daily_Yield.xlsx');
 
        abort_unless(file_exists($path), 404, 'Template belum tersedia. Hubungi admin untuk menaruh file template di storage/app/templates/.');
 
        return response()->download($path, 'Template_Daily_Yield.xlsx');
    }
}