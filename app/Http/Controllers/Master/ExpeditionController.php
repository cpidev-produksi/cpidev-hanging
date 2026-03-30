<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Expedition;
use App\Models\PlateNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpeditionController extends Controller
{
    public function index()
    {
        $expeditions = Expedition::query()->orderBy('name')->paginate(20);
        return view('master.expeditions.index', compact('expeditions'));
    }

    public function create()
    {
        return view('master.expeditions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:expeditions,name'],

            // Bisa format lama (array of string) atau format baru (array of object)
            'plate_numbers' => ['nullable','array'],

            // format baru
            'plate_numbers.*.plate_number' => ['nullable','string','max:20'],
            'plate_numbers.*.driver_name' => ['nullable','string','max:150'],
            'plate_numbers.*.driver_phone' => ['nullable','string','max:30'],

            // format lama
            'plate_numbers.*' => ['nullable'],
        ], [
            'name.required' => 'Nama ekspedisi wajib diisi.',
            'name.unique' => 'Nama ekspedisi sudah ada.',
            'plate_numbers.*.plate_number.max' => 'Nomor plat maksimal 20 karakter.',
        ]);

        $rows = $this->normalizePlateNumbersPayload($data['plate_numbers'] ?? []);

        // validasi duplikat di input (case-insensitive)
        $normalizedPlates = $rows->pluck('plate_number_normalized');
        if ($normalizedPlates->count() !== $normalizedPlates->unique()->count()) {
            return back()
                ->withErrors(['plate_numbers' => 'Nomor plat tidak boleh duplikat.'])
                ->withInput();
        }

        // unique global: plate_number harus unik di table plate_numbers
        $exists = PlateNumber::query()
            ->whereIn('plate_number', $rows->pluck('plate_number'))
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['plate_numbers' => 'Ada nomor plat yang sudah terdaftar.'])
                ->withInput();
        }

        return DB::transaction(function () use ($data, $rows) {
            $expedition = Expedition::create(['name' => $data['name']]);

            foreach ($rows as $row) {
                $expedition->plateNumbers()->create([
                    'plate_number' => $row['plate_number'],
                    'driver_name' => $row['driver_name'],
                    'driver_phone' => $row['driver_phone'],
                ]);
            }

            return redirect()->route('master.expeditions.index')->with('status', 'Ekspedisi dibuat.');
        });
    }

    public function edit(Expedition $expedition)
    {
        $expedition->load('plateNumbers');
        return view('master.expeditions.edit', compact('expedition'));
    }

    public function update(Request $request, Expedition $expedition)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:expeditions,name,'.$expedition->id],

            'plate_numbers' => ['nullable','array'],

            // format baru
            'plate_numbers.*.plate_number' => ['nullable','string','max:20'],
            'plate_numbers.*.driver_name' => ['nullable','string','max:150'],
            'plate_numbers.*.driver_phone' => ['nullable','string','max:30'],

            // format lama
            'plate_numbers.*' => ['nullable'],
        ], [
            'name.required' => 'Nama ekspedisi wajib diisi.',
            'name.unique' => 'Nama ekspedisi sudah ada.',
            'plate_numbers.*.plate_number.max' => 'Nomor plat maksimal 20 karakter.',
        ]);

        $rows = $this->normalizePlateNumbersPayload($data['plate_numbers'] ?? []);

        // validasi duplikat di input
        $normalizedPlates = $rows->pluck('plate_number_normalized');
        if ($normalizedPlates->count() !== $normalizedPlates->unique()->count()) {
            return back()
                ->withErrors(['plate_numbers' => 'Nomor plat tidak boleh duplikat.'])
                ->withInput();
        }

        // Cek jika ada plate_number yang sudah dipakai ekspedisi lain
        $existsOther = PlateNumber::query()
            ->whereIn('plate_number', $rows->pluck('plate_number'))
            ->where('expedition_id', '!=', $expedition->id)
            ->exists();

        if ($existsOther) {
            return back()
                ->withErrors(['plate_numbers' => 'Ada nomor plat yang sudah dipakai ekspedisi lain.'])
                ->withInput();
        }

        return DB::transaction(function () use ($expedition, $data, $rows) {
            $expedition->update(['name' => $data['name']]);

            // simple & aman: reset semua plate numbers
            $expedition->plateNumbers()->delete();

            foreach ($rows as $row) {
                $expedition->plateNumbers()->create([
                    'plate_number' => $row['plate_number'],
                    'driver_name' => $row['driver_name'],
                    'driver_phone' => $row['driver_phone'],
                ]);
            }

            return redirect()->route('master.expeditions.index')->with('status', 'Ekspedisi diupdate.');
        });
    }

    public function destroy(Expedition $expedition)
    {
        $expedition->delete();
        return back()->with('status','Ekspedisi dihapus.');
    }

    /**
     * Normalize payload plate numbers menjadi collection of:
     * - plate_number
     * - plate_number_normalized (untuk cek duplikat)
     * - driver_name
     * - driver_phone
     */
    private function normalizePlateNumbersPayload(array $payload)
    {
        return collect($payload)
            ->map(function ($item) {
                // format lama: string
                if (is_string($item)) {
                    $plate = trim($item);
                    if ($plate === '') return null;

                    return [
                        'plate_number' => $plate,
                        'plate_number_normalized' => mb_strtoupper($plate),
                        'driver_name' => null,
                        'driver_phone' => null,
                    ];
                }

                // format baru: array {plate_number, driver_name, driver_phone}
                if (is_array($item)) {
                    $plate = isset($item['plate_number']) ? trim((string) $item['plate_number']) : '';
                    if ($plate === '') return null;

                    $driverName = isset($item['driver_name']) ? trim((string) $item['driver_name']) : null;
                    $driverPhone = isset($item['driver_phone']) ? trim((string) $item['driver_phone']) : null;

                    $driverName = ($driverName === '') ? null : $driverName;
                    $driverPhone = ($driverPhone === '') ? null : $driverPhone;

                    return [
                        'plate_number' => $plate,
                        'plate_number_normalized' => mb_strtoupper($plate),
                        'driver_name' => $driverName,
                        'driver_phone' => $driverPhone,
                    ];
                }

                return null;
            })
            ->filter()
            ->values();
    }
}