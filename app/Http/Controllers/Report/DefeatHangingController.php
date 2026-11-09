<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\MonitorControl;
use App\Support\HangingApproval;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class DefeatHangingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $date = $filters['date'] ?? null;
        $month = $filters['month'] ?? null;
        $location = $filters['location'] ?? null;
        $shift = $filters['shift'] ?? null;

        $forms = $this->filteredDoneForms($filters)
            ->with(['monitorControl.farm'])
            ->orderByDesc(
                MonitorControl::query()
                    ->select('process_date')
                    ->whereColumn('monitor_controls.id', 'hanging_forms.monitor_control_id')
            )
            ->orderByDesc('id')
            ->get();

        $groups = $forms
            ->groupBy(fn ($form) => $form->monitorControl->process_date?->format('Y-m') ?? 'unknown')
            ->map(function ($monthForms, $monthKey) {
                $dates = $monthForms
                    ->groupBy(fn ($form) => $form->monitorControl->process_date?->format('Y-m-d') ?? 'unknown')
                    ->map(function ($dateForms, $dateKey) {
                        return [
                            'key' => $dateKey,
                            'label' => $dateKey === 'unknown' ? 'Tanggal tidak tersedia' : Carbon::parse($dateKey)->translatedFormat('l, d F Y'),
                            'forms' => $dateForms,
                            ...$this->signOffSummary($dateForms),
                        ];
                    })
                    ->values();

                return [
                    'key' => $monthKey,
                    'label' => $monthKey === 'unknown' ? 'Bulan tidak tersedia' : Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('F Y'),
                    'dates' => $dates,
                    ...$this->signOffSummary($monthForms),
                ];
            })
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $groups = new LengthAwarePaginator(
            $groups->forPage($currentPage, $perPage)->values(),
            $groups->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('report.defeat.hanging.index', [
            'groups'   => $groups,
            'summary'  => $this->signOffSummary($forms),
            'date'     => $date,
            'month'    => $month,
            'location' => $location,
            'shift'    => $shift,
        ]);
    }

    private function signOffSummary($forms): array
    {
        $total = $forms->count();
        $reviewed = $forms->filter(fn ($form) => $form->known_at !== null)->count();
        $approved = $forms->filter(fn ($form) => $form->approved_at !== null)->count();
        $qcReviewPending = $forms->filter(fn ($form) => $form->known_at === null && $form->hasCompleteQc())->count();
        $qcApprovePending = $forms->filter(fn ($form) => $form->approved_at === null && $form->hasCompleteQc())->count();

        return [
            'total' => $total,
            'reviewed' => $reviewed,
            'approved' => $approved,
            'qcIncomplete' => $forms->filter(fn ($form) => !$form->hasCompleteQc())->count(),
            'qcReviewPending' => $qcReviewPending,
            'qcApprovePending' => $qcApprovePending,
            'allReviewed' => $total > 0 && $reviewed === $total,
            'allApproved' => $total > 0 && $approved === $total,
        ];
    }

    /** Bulk review for all pending forms in the selected day or month. */
    public function bulkReview(Request $request)
    {
        return $this->bulkSignOff($request, 'review');
    }

    /** Bulk approval for all pending forms in the selected day or month. */
    public function bulkApprove(Request $request)
    {
        return $this->bulkSignOff($request, 'approve');
    }

    private function bulkSignOff(Request $request, string $action)
    {
        $user = $request->user();
        $role = $action === 'review' ? 'foreman' : 'supervisor';
        abort_unless($user?->role?->slug === $role, 403);

        $filters = $this->validatedFilters($request, true);

        $timestamp = now();
        $pendingColumn = $action === 'review' ? 'known_at' : 'approved_at';
        $nameColumn = $action === 'review' ? 'known_name' : 'approved_name';
        $userColumn = $action === 'review' ? 'known_user_id' : 'approved_user_id';
        $pendingForms = $this->filteredDoneForms($filters)->whereNull($pendingColumn);
        $pendingCount = (clone $pendingForms)->count();
        $count = (clone $pendingForms)
            ->qcComplete()
            ->update([
                $nameColumn => $user->name,
                $userColumn => $user->id,
                $pendingColumn => $timestamp,
            ]);

        $label = $action === 'review' ? 'direview' : 'disetujui';
        $message = $count > 0
            ? "{$count} form berhasil {$label} oleh {$user->name}."
            : "Tidak ada form yang perlu {$label} pada periode yang dipilih.";
        $skipped = $pendingCount - $count;
        if ($skipped > 0) {
            $message .= " {$skipped} form dilewati karena QC belum lengkap.";
        }

        return back()->with('status', $message);
    }

    private function filteredDoneForms(array $filters): Builder
    {
        return HangingForm::query()
            ->where('status', 'done')
            ->whereHas('monitorControl', function ($query) use ($filters) {
                if (!empty($filters['date'])) {
                    $query->whereDate('process_date', $filters['date']);
                } elseif (!empty($filters['month'])) {
                    [$year, $month] = explode('-', $filters['month']);
                    $query->whereYear('process_date', $year)->whereMonth('process_date', $month);
                }

                if (!empty($filters['location'])) {
                    $query->where('location', $filters['location']);
                }
                if (!empty($filters['shift'])) {
                    $query->where('shift', $filters['shift']);
                }
            });
    }

    private function validatedFilters(Request $request, bool $requirePeriod = false): array
    {
        $validator = Validator::make($request->all(), [
            'date' => ['nullable', 'date_format:Y-m-d'],
            'month' => ['nullable', 'date_format:Y-m'],
            'location' => ['nullable', 'in:SH01,SH02'],
            'shift' => ['nullable', 'in:pagi,malam'],
        ]);

        $validator->after(function ($validator) use ($request, $requirePeriod) {
            if ($request->filled('date') && $request->filled('month')) {
                $validator->errors()->add('month', 'Pilih tanggal atau bulan, bukan keduanya.');
            }

            if ($requirePeriod && !$request->filled('date') && !$request->filled('month')) {
                $validator->errors()->add('date', 'Pilih tanggal atau bulan untuk aksi massal.');
            }
        });

        return $validator->validate();
    }

    /** Diketahui oleh - hanya Foreman. */
    public function review(Request $request, HangingForm $hangingForm)
    {
        $user = $request->user();
        abort_unless($user?->role?->slug === 'foreman', 403, 'Hanya Foreman yang boleh melakukan Review.');
        abort_unless($hangingForm->status === 'done', 422, 'Form hanging belum DONE.');
        abort_unless($hangingForm->hasCompleteQc(), 422, 'QC kondisi belum lengkap. Review tidak dapat dilakukan.');

        if ($hangingForm->known_at) {
            return back()->withErrors(['review' => 'Form ini sudah direview oleh ' . $hangingForm->known_name . '.']);
        }

        $hangingForm->update([
            'known_name'    => $user->name,
            'known_user_id' => $user->id,
            'known_at'      => now(),
        ]);

        return back()->with('status', 'Review berhasil. Diketahui oleh: ' . $user->name . '.');
    }

    /** Disetujui oleh - hanya Supervisor. */
    public function approve(Request $request, HangingForm $hangingForm)
    {
        $user = $request->user();
        abort_unless($user?->role?->slug === 'supervisor', 403, 'Hanya Supervisor yang boleh melakukan Approve.');
        abort_unless($hangingForm->status === 'done', 422, 'Form hanging belum DONE.');
        abort_unless($hangingForm->hasCompleteQc(), 422, 'QC kondisi belum lengkap. Approve tidak dapat dilakukan.');

        if ($hangingForm->approved_at) {
            return back()->withErrors(['approve' => 'Form ini sudah disetujui oleh ' . $hangingForm->approved_name . '.']);
        }

        $hangingForm->update([
            'approved_name'    => $user->name,
            'approved_user_id' => $user->id,
            'approved_at'      => now(),
        ]);

        return back()->with('status', 'Approve berhasil. Disetujui oleh: ' . $user->name . '.');
    }

    /** Export PDF dari daftar (login). */
    public function pdf(HangingForm $hangingForm)
    {
        [$pdf, $mc] = $this->render($hangingForm);

        return $pdf->download('Laporan-' . $mc->report_code . '.pdf');
    }

    /** Tujuan QR "Dibuat oleh": PDF yang sama, dibuka langsung (URL bertanda tangan, tanpa login). */
    public function publicPdf(HangingForm $hangingForm)
    {
        [$pdf, $mc] = $this->render($hangingForm);

        return $pdf->stream('Laporan-' . $mc->report_code . '.pdf');
    }

    private function render(HangingForm $hangingForm): array
    {
        abort_unless($hangingForm->status === 'done', 404);

        $mc = $hangingForm->monitorControl;
        abort_unless($mc, 404);

        return [HangingApproval::pdf($mc), $mc];
    }
}