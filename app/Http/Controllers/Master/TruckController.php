<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Expedition;
use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    public function index() {
        $trucks = Truck::query()->with('expedition')->latest()->paginate(20);
        return view('master.trucks.index', compact('trucks'));
    }

    public function create() {
        $expeditions = Expedition::query()->orderBy('name')->get();
        return view('master.trucks.create', compact('expeditions'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'no_truck' => ['required','string','max:50'],
            'plate_number' => ['required','string','max:50','unique:trucks,plate_number'],
            'expedition_id' => ['required','exists:expeditions,id'],
        ], [
            'no_truck.required' => 'No truk wajib diisi.',
            'plate_number.required' => 'No polisi wajib diisi.',
            'plate_number.unique' => 'No polisi sudah terdaftar (harus unik).',
            'expedition_id.required' => 'Ekspedisi wajib dipilih.',
        ]);

        Truck::create($data);
        return redirect()->route('master.trucks.index')->with('status','Truk dibuat.');
    }

    public function edit(Truck $truck) {
        $expeditions = Expedition::query()->orderBy('name')->get();
        return view('master.trucks.edit', compact('truck','expeditions'));
    }

    public function update(Request $request, Truck $truck) {
        $data = $request->validate([
            'no_truck' => ['required','string','max:50'],
            'plate_number' => ['required','string','max:50','unique:trucks,plate_number,'.$truck->id],
            'expedition_id' => ['required','exists:expeditions,id'],
        ], [
            'no_truck.required' => 'No truk wajib diisi.',
            'plate_number.required' => 'No polisi wajib diisi.',
            'plate_number.unique' => 'No polisi sudah terdaftar (harus unik).',
            'expedition_id.required' => 'Ekspedisi wajib dipilih.',
        ]);

        $truck->update($data);
        return redirect()->route('master.trucks.index')->with('status','Truk diupdate.');
    }

    public function destroy(Truck $truck) {
        $truck->delete();
        return back()->with('status','Truk dihapus.');
    }
}
