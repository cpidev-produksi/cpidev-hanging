<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\HangingForm;
use App\Models\HangingReturItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ReturMatiController extends Controller
{
    public function edit(HangingForm $hangingForm)
    {
        $hangingForm->load([
            'monitorControl.expedition',
            'monitorControl.plateNumber',
            'monitorControl.farm',
            'returItems',
        ]);

        return view('transaction.retur_mati.edit', [
            'form' => $hangingForm,
        ]);
    }

    public function update(Request $request, HangingForm $hangingForm)
    {
        if ($hangingForm->status === 'done') {
            return back()->withErrors(['retur' => 'Form sudah DONE dan tidak bisa diubah.']);
        }

        $data = $request->validate([
            'dead_count' => ['required', 'integer', 'min:0'],

            'retur_weights' => ['nullable', 'array'],
            'retur_weights.*' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'retur_photos' => ['nullable', 'array'],
            'retur_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],

            'retur_photo_existing' => ['nullable', 'array'],
            'retur_photo_existing.*' => ['nullable', 'string'],

            'retur_photo_remove' => ['nullable', 'array'],
            'retur_photo_remove.*' => ['nullable', 'in:0,1'],
        ]);

        $weightsInput   = $request->input('retur_weights', []);
        $existingPhotos = $request->input('retur_photo_existing', []);
        $removeFlags    = $request->input('retur_photo_remove', []);
        $files          = $request->file('retur_photos', []);

        $oldPhotos = $hangingForm->returItems->pluck('photo_path')->filter()->values()->all();
        $usedOld   = [];

        $savePhoto = function ($file) {
            $image = Image::make($file)->resize(1280, null, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            $path = 'retur-photos/' . uniqid('retur_', true) . '.jpg';

            Storage::disk('public')->put($path, (string) $image->encode('jpg', 75));

            return $path;
        };

        return DB::transaction(function () use ($hangingForm, $data, $weightsInput, $existingPhotos, $removeFlags, $files, $oldPhotos, &$usedOld, $savePhoto) {
            // hapus semua item lama
            HangingReturItem::query()
                ->where('hanging_form_id', $hangingForm->id)
                ->delete();

            $rows = [];
            foreach ($weightsInput as $i => $w) {
                $w = (float) $w;
                if ($w <= 0) continue; // skip kosong

                $photoPath = null;
                $remove = ($removeFlags[$i] ?? '0') === '1';
                $existing = $existingPhotos[$i] ?? null;

                if (isset($files[$i]) && $files[$i]) {
                    // new upload -> replace
                    $photoPath = $savePhoto($files[$i]);
                    if ($existing) $usedOld[] = $existing; // mark to delete later
                } else {
                    if (!$remove && $existing) {
                        $photoPath = $existing;
                        $usedOld[] = $existing;
                    } else {
                        $photoPath = null;
                    }
                }

                $rows[] = [
                    'hanging_form_id' => $hangingForm->id,
                    'weight_kg' => round($w, 2),
                    'photo_path' => $photoPath,
                ];
            }

            foreach ($rows as $row) {
                HangingReturItem::create($row);
            }

            // delete old photos that are not reused
            $unused = array_diff($oldPhotos, $usedOld);
            foreach ($unused as $p) {
                Storage::disk('public')->delete($p);
            }

            $returCount = count($rows);
            $returTotalKg = array_sum(array_column($rows, 'weight_kg'));

            $hangingForm->update([
                'dead_count' => (int) $data['dead_count'],
                'retur_count' => $returCount,
                'retur_total_kg' => $returTotalKg,
            ]);

            return redirect()
                ->route('retur-mati.landing')
                ->with('status', 'Data Ayam Retur & Mati tersimpan.');
        });
    }
}
