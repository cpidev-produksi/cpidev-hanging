<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CutUpMdCheck;
use App\Models\CutUpMdReport;
use App\Models\CutUpMdVerification;
use App\Models\CutUpProduct;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CutUpMetalDetectorController extends Controller
{
    /* =========================================================
     |  INDEX — dashboard + daftar laporan
     * ========================================================= */
    public function index(Request $request)
    {
        $from     = $request->input('from', now()->startOfMonth()->toDateString());
        $to       = $request->input('to', now()->toDateString());
        $location = $request->input('location');
        $shift    = $request->input('shift');

        $base = CutUpMdReport::query()
            ->whereBetween('report_date', [$from, $to])
            ->when(in_array($location, ['SH01', 'SH02'], true), fn ($q) => $q->where('location', $location))
            ->when(in_array($shift, ['Pagi', 'Malam'], true), fn ($q) => $q->where('shift', $shift));

        $ids = (clone $base)->select('id');

        $notOk = CutUpMdVerification::whereIn('report_id', $ids)->where(function ($q) {
            foreach (CutUpMdVerification::RESULT_COLUMNS as $col) {
                $q->orWhere($col, 'NOT OK');
            }
        })->count();

        $stats = [
            'reports'       => (clone $base)->count(),
            'checks'        => CutUpMdCheck::whereIn('report_id', $ids)->count(),
            'unusable'      => CutUpMdCheck::whereIn('report_id', $ids)->where('conclusion', 'NOT_USABLE')->count(),
            'verifications' => CutUpMdVerification::whereIn('report_id', $ids)->count(),
            'not_ok'        => $notOk,
        ];

        $reports = (clone $base)
            ->withCount([
                'checks',
                'verifications',
                'checks as unusable_count' => fn ($q) => $q->where('conclusion', 'NOT_USABLE'),
            ])
            ->orderByDesc('report_date')
            ->orderBy('location')
            ->orderByDesc('shift')
            ->paginate(15)
            ->withQueryString();

        return view('report.cutup.metal-detector.index', compact('reports', 'stats', 'from', 'to', 'location', 'shift'));
    }

    /* =========================================================
     |  CREATE / EDIT
     * ========================================================= */
    public function create()
    {
        return view('report.cutup.metal-detector.form', [
            'report'   => null,
            'products' => $this->productList(),
            // Diameter spesimen biasanya sama antar laporan -> pakai nilai laporan terakhir sebagai default
            'defaults' => CutUpMdReport::latest('report_date')->latest('id')
                ->first(['fe_diameter', 'nonfe_diameter', 'sus316_diameter']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $report = DB::transaction(function () use ($data) {
            $report = CutUpMdReport::create($data['header'] + ['created_by' => Auth::id()]);
            $this->syncRows($report, $data);

            return $report;
        });

        return redirect()->route('cut-up.metal-detector.index')
            ->with('status', 'Laporan ' . $report->date_long . ' (' . $report->location . ', ' . $report->shift . ') berhasil disimpan.');
    }

    public function edit(CutUpMdReport $report)
    {
        $report->load(['checks', 'verifications']);

        return view('report.cutup.metal-detector.form', [
            'report'   => $report,
            'products' => $this->productList(),
            'defaults' => null,
        ]);
    }

    public function update(Request $request, CutUpMdReport $report)
    {
        $data = $this->validated($request, $report);

        DB::transaction(function () use ($report, $data) {
            $report->update($data['header'] + ['updated_by' => Auth::id()]);
            $report->checks()->delete();
            $report->verifications()->delete();
            $this->syncRows($report, $data);
        });

        return redirect()->route('cut-up.metal-detector.index')
            ->with('status', 'Laporan berhasil diperbarui.');
    }

    public function destroy(CutUpMdReport $report)
    {
        $report->delete(); // checks & verifications ikut terhapus (cascade)

        return redirect()->route('cut-up.metal-detector.index')
            ->with('status', 'Laporan berhasil dihapus.');
    }

    /* =========================================================
     |  EXPORT PDF
     * ========================================================= */
    public function pdf(CutUpMdReport $report)
    {
        $report->load(['checks', 'verifications']);

        $pdf = Pdf::loadView('report.cutup.metal-detector.pdf', compact('report'))
            ->setPaper('a4', 'portrait');

        $filename = sprintf(
            'Pemeriksaan_Metal_Detector_%s_%s_%s.pdf',
            $report->report_date->format('Y-m-d'),
            $report->location,
            $report->shift
        );

        return $pdf->stream($filename);
    }

    /* =========================================================
     |  HELPERS
     * ========================================================= */
    private function productList()
    {
        return CutUpProduct::orderBy('name')->get(['id', 'name']);
    }

    private function validated(Request $request, ?CutUpMdReport $report = null): array
    {
        $result = ['required', Rule::in(['OK', 'NOT OK'])];
        $time   = ['required', 'date_format:H:i'];
        $md     = ['required', Rule::in(['01', '02', '03'])];
        $dia    = ['required', 'numeric', 'gt:0', 'max:999.99'];

        $rules = [
            'report_date'     => ['required', 'date'],
            'location'        => ['required', Rule::in(['SH01', 'SH02'])],
            'shift'           => ['required', Rule::in(['Pagi', 'Malam'])],
            'start_time'      => ['nullable', 'date_format:H:i'],
            'break_time'      => ['nullable', 'date_format:H:i'],
            'end_time'        => ['nullable', 'date_format:H:i'],
            'fe_diameter'     => $dia,
            'nonfe_diameter'  => $dia,
            'sus316_diameter' => $dia,
            'made_by'         => ['nullable', 'string', 'max:100'],
            'approved_by'     => ['nullable', 'string', 'max:100'],
            'known_by'        => ['nullable', 'string', 'max:100'],

            'checks'                         => ['nullable', 'array'],
            'checks.*.metal_detector_no'     => $md,
            'checks.*.check_time'            => $time,
            'checks.*.fe_result'             => $result,
            'checks.*.nonfe_result'          => $result,
            'checks.*.sus316_result'         => $result,
            'checks.*.conclusion'            => ['required', Rule::in(['USABLE', 'NOT_USABLE'])],
            'checks.*.remark'                => ['nullable', 'string', 'max:255', 'required_if:checks.*.conclusion,NOT_USABLE'],

            'verifications'                      => ['nullable', 'array'],
            'verifications.*.check_time'         => $time,
            'verifications.*.metal_detector_no'  => $md,
            'verifications.*.product_id'         => ['required', 'integer', 'exists:cutup_products,id'],
            'verifications.*.product_code'       => ['required', 'string', 'size:10'],
        ];

        $attributes = [
            'report_date'     => 'Tanggal',
            'location'        => 'Lokasi',
            'shift'           => 'Shift',
            'fe_diameter'     => 'Diameter Fe',
            'nonfe_diameter'  => 'Diameter Non Fe',
            'sus316_diameter' => 'Diameter SUS 316',

            'checks.*.metal_detector_no' => 'No. Metal Detector (pemeriksaan ke-:position)',
            'checks.*.check_time'        => 'Pukul (pemeriksaan ke-:position)',
            'checks.*.fe_result'         => 'Fe (pemeriksaan ke-:position)',
            'checks.*.nonfe_result'      => 'Non Fe (pemeriksaan ke-:position)',
            'checks.*.sus316_result'     => 'SUS 316 (pemeriksaan ke-:position)',
            'checks.*.conclusion'        => 'Kesimpulan (pemeriksaan ke-:position)',
            'checks.*.remark'            => 'Keterangan (pemeriksaan ke-:position)',

            'verifications.*.check_time'        => 'Pukul (verifikasi ke-:position)',
            'verifications.*.metal_detector_no' => 'No. Metal Detector (verifikasi ke-:position)',
            'verifications.*.product_id'        => 'Nama Produk (verifikasi ke-:position)',
            'verifications.*.product_code'      => 'Kode Produk (verifikasi ke-:position)',
        ];

        foreach (CutUpMdVerification::RESULT_COLUMNS as $col) {
            [$sp, $pos] = explode('_', $col);
            $rules["verifications.*.{$col}"] = $result;
            $attributes["verifications.*.{$col}"] = sprintf(
                '%s - %s (verifikasi ke-:position)',
                ['fe' => 'Fe', 'nonfe' => 'Non Fe', 'sus316' => 'SUS 316'][$sp],
                ['d' => 'Depan', 't' => 'Tengah', 'b' => 'Belakang'][$pos]
            );
        }

        $messages = [
            'checks.*.remark.required_if' => 'Keterangan wajib diisi jika metal detector tidak dapat digunakan (pemeriksaan ke-:position).',
            'verifications.*.product_code.size' => 'Kode Produk harus tepat 10 karakter (verifikasi ke-:position).',
            '*.required' => ':attribute wajib diisi.',
            '*.in'       => ':attribute tidak valid.',
        ];

        $v = $request->validate($rules, $messages, $attributes);

        // 1 laporan per kombinasi tanggal + lokasi + shift
        $exists = CutUpMdReport::query()
            ->whereDate('report_date', $v['report_date'])
            ->where('location', $v['location'])
            ->where('shift', $v['shift'])
            ->when($report, fn ($q) => $q->where('id', '!=', $report->id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'report_date' => 'Laporan untuk tanggal, lokasi, dan shift tersebut sudah ada. Silakan edit laporan yang ada.',
            ]);
        }

        $header = collect($v)->only([
            'report_date', 'location', 'shift', 'start_time', 'break_time', 'end_time',
            'fe_diameter', 'nonfe_diameter', 'sus316_diameter',
            'made_by', 'approved_by', 'known_by',
        ])->all();

        return [
            'header'        => $header,
            'checks'        => array_values($v['checks'] ?? []),
            'verifications' => array_values($v['verifications'] ?? []),
        ];
    }

    private function syncRows(CutUpMdReport $report, array $data): void
    {
        $checks = collect($data['checks'])->sortBy('check_time')->values();
        foreach ($checks as $row) {
            $report->checks()->create($row);
        }

        $names = CutUpProduct::whereIn('id', collect($data['verifications'])->pluck('product_id'))
            ->pluck('name', 'id');

        $verifs = collect($data['verifications'])->sortBy('check_time')->values();
        foreach ($verifs as $row) {
            $row['product_name'] = $names[$row['product_id']] ?? '-';
            $row['product_code'] = strtoupper($row['product_code']);
            $report->verifications()->create($row);
        }
    }
}
