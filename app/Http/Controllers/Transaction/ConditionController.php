<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\MonitorControl;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConditionController extends Controller
{
    public function landing(Request $request)
    {
        $date = $request->query('date');
        if ($date === null || $date === '') {
            $date = now()->toDateString();
        }

        $search = $request->query('search');
        $location = $request->query('location');
        $shift = $request->query('shift');
        $statusFilter = $request->query('status');
        $sort = $request->query('sort', 'truck_no');
        $direction = $request->query('direction', 'asc');

        $baseQuery = function () use ($date, $search, $location, $shift, $statusFilter, $sort, $direction) {
            $q = MonitorControl::query()
                ->with(['farm', 'expedition', 'plateNumber', 'hangingForm'])
                ->whereHas('hangingForm', function ($q) {
                }, '>=', 0);

            if ($date !== null && $date !== '') {
                $q->whereDate('process_date', $date);
            }

            if ($location && $location !== '') {
                $q->where('location', $location);
            }

            if ($shift && $shift !== '') {
                $q->where('shift', $shift);
            }

            if ($search && $search !== '') {
                $q->where(function ($query) use ($search) {
                    $query->where('report_code', 'like', "%{$search}%")
                        ->orWhereHas('plateNumber', function ($sq) use ($search) {
                            $sq->where('plate_number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('expedition', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($statusFilter === 'filled') {
                $q->whereHas('hangingForm', function ($query) {
                    $query->whereNotNull('basket_condition')
                        ->whereNotNull('truck_platform_condition')
                        ->whereNotNull('feather_condition');
                });
            } elseif ($statusFilter === 'empty') {
                $q->whereDoesntHave('hangingForm', function ($query) {
                    $query->whereNotNull('basket_condition')
                        ->whereNotNull('truck_platform_condition')
                        ->whereNotNull('feather_condition');
                })->orWhereDoesntHave('hangingForm');
            }

            $allowedSorts = ['truck_no', 'process_date', 'location', 'shift', 'report_code'];
            if (in_array($sort, $allowedSorts)) {
                $q->orderBy($sort, $direction);
            }

            $q->orderBy('process_date', 'desc')
              ->orderBy('location')
              ->orderByRaw("FIELD(shift,'pagi','malam')")
              ->orderBy('truck_no');

            return $q;
        };

        $perPage = $request->query('per_page', 15);

        $paginateShift = function (string $loc, string $sh, string $pageParam) use ($baseQuery, $perPage) {
            return $baseQuery()
                ->where('location', $loc)
                ->where('shift', $sh)
                ->paginate($perPage, ['*'], $pageParam)
                ->withQueryString();
        };

        $data = [
            'SH01' => [
                'pagi'  => $paginateShift('SH01', 'pagi',  'page_sh01_pagi'),
                'malam' => $paginateShift('SH01', 'malam', 'page_sh01_malam'),
            ],
            'SH02' => [
                'pagi'  => $paginateShift('SH02', 'pagi',  'page_sh02_pagi'),
                'malam' => $paginateShift('SH02', 'malam', 'page_sh02_malam'),
            ],
        ];

        return view('transaction.conditions.landing', [
            'data' => $data,
            'date' => $date,
            'search' => $search,
            'location' => $location,
            'shift' => $shift,
            'statusFilter' => $statusFilter,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
        ]);
    }

    public function open(MonitorControl $monitorControl)
    {
        return DB::transaction(function () use ($monitorControl) {
            $form = $monitorControl->hangingForm;

            if (!$form) {
                $form = HangingForm::create([
                    'monitor_control_id' => $monitorControl->id,
                    'status' => 'draft',
                    'unloading_time' => null,
                    'finish_time' => null,
                    'dead_count' => 0,
                    'retur_count' => 0,
                    'retur_total_kg' => 0,
                ]);
            }

            return redirect()->route('conditions.edit', $form);
        });
    }

    public function edit(HangingForm $hangingForm)
    {
        $hangingForm->load(['monitorControl.expedition', 'monitorControl.plateNumber', 'monitorControl.farm']);

        return view('transaction.conditions.edit', [
            'form' => $hangingForm,
        ]);
    }

    public function update(Request $request, HangingForm $hangingForm)
    {
        $slug = $request->user()?->role?->slug;

        if ($hangingForm->status === 'done' && !in_array($slug, ['supervisor','superadmin'], true)) {
            return back()->withErrors(['conditions' => 'Form sudah DONE dan tidak bisa diubah.']);
        }

        $data = $request->validate([
            'basket_condition' => ['required', 'in:sangat_basah,basah,kering'],
            'truck_platform_condition' => ['required', 'in:bak_berisi_air,bak_kering,benda_lain'],
            'feather_condition' => ['required', 'in:sangat_basah,medium_basah,basah,kering'],
        ]);

        $before = $hangingForm->only(['basket_condition','truck_platform_condition','feather_condition']);
        $hangingForm->update($data);

        $after = $hangingForm->only(['basket_condition','truck_platform_condition','feather_condition']);
        $changes = AuditLogger::diff($before, $after);

        $roleSlug = $request->user()?->role?->slug;
        $wasDone = ($hangingForm->status === 'done');

        if ($wasDone && $roleSlug === 'supervisor') {
            $meta = [
                'report_code' => $hangingForm->monitorControl?->report_code,
                'location' => $hangingForm->monitorControl?->location,
                'truck_no' => $hangingForm->monitorControl?->truck_no,
                'was_done' => true,
            ];

            AuditLogger::log('qc_kondisi', 'update', $hangingForm, $changes, $meta);
        }

        return redirect()
            ->route('conditions.landing')
            ->with('status', 'Kondisi tersimpan.');
    }
}