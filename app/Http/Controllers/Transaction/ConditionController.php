<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\MonitorControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConditionController extends Controller
{
    public function landing(Request $request)
    {
        $date = $request->query('date');
        
        // Ambil parameter filter & sort
        $search = $request->query('search');
        $location = $request->query('location');
        $shift = $request->query('shift');
        $statusFilter = $request->query('status'); // 'filled' or 'empty'
        $sort = $request->query('sort', 'truck_no');
        $direction = $request->query('direction', 'asc');

        $baseQuery = function () use ($date, $search, $location, $shift, $statusFilter, $sort, $direction) {
            $q = MonitorControl::query()
                ->with(['farm', 'expedition', 'plateNumber', 'hangingForm'])
                ->whereHas('hangingForm', function ($q) {
                    // Pastikan ada hanging form
                }, '>=', 0);

            // Filter date
            if ($date !== null && $date !== '') {
                $q->whereDate('process_date', $date);
            }

            // Filter location
            if ($location && $location !== '') {
                $q->where('location', $location);
            }

            // Filter shift
            if ($shift && $shift !== '') {
                $q->where('shift', $shift);
            }

            // Search by report_code or plate_number
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

            // Filter by status (filled / empty)
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

            // Sorting
            $allowedSorts = ['truck_no', 'process_date', 'location', 'shift', 'report_code'];
            if (in_array($sort, $allowedSorts)) {
                $q->orderBy($sort, $direction);
            }

            // Default secondary sort
            $q->orderBy('process_date', 'desc')
              ->orderBy('location')
              ->orderByRaw("FIELD(shift,'pagi','malam')")
              ->orderBy('truck_no');

            return $q;
        };

        $perPage = $request->query('per_page', 15);

        $paginateShift = function (string $loc, string $sh, string $pageParam) use ($baseQuery, $perPage, $date, $search, $location, $shift, $statusFilter, $sort, $direction) {
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
        if ($hangingForm->status === 'done') {
            return back()->withErrors(['conditions' => 'Form sudah DONE dan tidak bisa diubah.']);
        }

        $data = $request->validate([
            'basket_condition' => ['required', 'in:sangat_basah,basah,kering'],
            'truck_platform_condition' => ['required', 'in:bak_berisi_air,bak_kering,benda_lain'],
            'feather_condition' => ['required', 'in:sangat_basah,medium_basah,basah,kering'],
        ]);

        $hangingForm->update($data);

        return redirect()
            ->route('conditions.landing')
            ->with('status', 'Kondisi tersimpan.');
    }
}