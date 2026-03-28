<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Expedition;
use App\Models\PlateNumber;
use Illuminate\Http\Request;

class ExpeditionController extends Controller
{
    public function index() {
        $expeditions = Expedition::query()->orderBy('name')->paginate(20);
        return view('master.expeditions.index', compact('expeditions'));
    }

    public function create() {
        return view('master.expeditions.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:expeditions,name'],
            'plate_numbers' => ['nullable','array'],
            'plate_numbers.*' => ['nullable','string','max:20','distinct'],
        ], [
            'name.required' => 'Nama ekspedisi wajib diisi.',
            'name.unique' => 'Nama ekspedisi sudah ada.',
            'plate_numbers.*.max' => 'Nomor plat maksimal 20 karakter.',
            'plate_numbers.*.distinct' => 'Nomor plat tidak boleh duplikat.',
        ]);

        $plateNumbers = collect($data['plate_numbers'] ?? [])
            ->filter(fn ($v) => $v !== null && trim($v) !== '')
            ->map(fn ($v) => trim($v))
            ->values();

        // unique global (plate_number harus unik di table plate_numbers)
        $exists = PlateNumber::query()
            ->whereIn('plate_number', $plateNumbers)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['plate_numbers' => 'Ada nomor plat yang sudah terdaftar.'])
                ->withInput();
        }

        $expedition = Expedition::create(['name' => $data['name']]);

        foreach ($plateNumbers as $plateNumber) {
            $expedition->plateNumbers()->create(['plate_number' => $plateNumber]);
        }

        return redirect()->route('master.expeditions.index')->with('status','Ekspedisi dibuat.');
    }

    public function edit(Expedition $expedition) {
        $expedition->load('plateNumbers');
        return view('master.expeditions.edit', compact('expedition'));
    }

    public function update(Request $request, Expedition $expedition) {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:expeditions,name,'.$expedition->id],
            'plate_numbers' => ['nullable','array'],
            'plate_numbers.*' => ['nullable','string','max:20','distinct'],
        ], [
            'name.required' => 'Nama ekspedisi wajib diisi.',
            'name.unique' => 'Nama ekspedisi sudah ada.',
            'plate_numbers.*.max' => 'Nomor plat maksimal 20 karakter.',
            'plate_numbers.*.distinct' => 'Nomor plat tidak boleh duplikat.',
        ]);

        $plateNumbers = collect($data['plate_numbers'] ?? [])
            ->filter(fn ($v) => $v !== null && trim($v) !== '')
            ->map(fn ($v) => trim($v))
            ->values();

        // Cek jika ada plate_number yang sudah dipakai ekspedisi lain
        $existsOther = PlateNumber::query()
            ->whereIn('plate_number', $plateNumbers)
            ->where('expedition_id', '!=', $expedition->id)
            ->exists();

        if ($existsOther) {
            return back()
                ->withErrors(['plate_numbers' => 'Ada nomor plat yang sudah dipakai ekspedisi lain.'])
                ->withInput();
        }

        $expedition->update(['name' => $data['name']]);

        // reset plate numbers (simple & aman)
        $expedition->plateNumbers()->delete();

        foreach ($plateNumbers as $plateNumber) {
            $expedition->plateNumbers()->create(['plate_number' => $plateNumber]);
        }

        return redirect()->route('master.expeditions.index')->with('status','Ekspedisi diupdate.');
    }

    public function destroy(Expedition $expedition) {
        $expedition->delete();
        return back()->with('status','Ekspedisi dihapus.');
    }
}