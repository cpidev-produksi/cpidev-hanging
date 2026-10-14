<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Imports\PlanningLbImport;
use App\Models\PlanningLb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PlanningLbController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date') ?: now()->toDateString();

        $sh01 = PlanningLb::where('location', 'SH01')
            ->when($date, fn($q) => $q->whereDate('process_date', $date))
            ->orderBy('process_date', 'desc')
            ->paginate(10, ['*'], 'sh01_page');

        $sh02 = PlanningLb::where('location', 'SH02')
            ->when($date, fn($q) => $q->whereDate('process_date', $date))
            ->orderBy('process_date', 'desc')
            ->paginate(10, ['*'], 'sh02_page');

        return view('planning_lb.index', compact('sh01', 'sh02', 'date'));
    }

    public function create()
    {
        return view('planning_lb.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'location' => ['required', 'in:SH01,SH02'],
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            $planning = (new PlanningLbImport())->read($validated['file']);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withErrors(['file' => $exception->getMessage()])
                ->withInput();
        }

        PlanningLb::create([
            'location' => $validated['location'],
            ...$planning,
        ]);

        return redirect()
            ->route('planning-lb.index', ['date' => $planning['process_date']])
            ->with('status', 'Planning LB berhasil diupload.');
    }

    public function show(PlanningLb $planningLb)
    {
        return view('planning_lb.show', [
            'item' => $planningLb,
        ]);
    }

    public function edit(PlanningLb $planningLb)
    {
        return view('planning_lb.edit', [
            'item' => $planningLb,
        ]);
    }

    public function update(Request $request, PlanningLb $planningLb)
    {
        $data = $request->validate([
            'location' => ['required', 'in:SH01,SH02'],
            'process_date' => ['required', 'date'],
            'total_plan_chicken' => ['required', 'integer', 'min:0'],
            'total_plan_truck' => ['required', 'integer', 'min:0'],
        ]);

        $planningLb->update($data);

        return redirect()
            ->route('planning-lb.index', ['date' => $data['process_date']])
            ->with('status', 'Planning LB diupdate.');
    }

    public function destroy(PlanningLb $planningLb)
    {
        $planningLb->delete();

        return back()->with('status', 'Planning LB dihapus.');
    }
}
