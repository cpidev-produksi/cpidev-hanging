<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function index() {
        $farms = Farm::query()->orderBy('name')->paginate(20);
        return view('master.farms.index', compact('farms'));
    }

    public function create() {
        return view('master.farms.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:farms,name'],
        ], [
            'name.required' => 'Nama farm wajib diisi.',
            'name.unique' => 'Nama farm sudah ada.',
        ]);

        Farm::create($data);

        return redirect()->route('master.farms.index')->with('status','Farm dibuat.');
    }

    public function edit(Farm $farm) {
        return view('master.farms.edit', compact('farm'));
    }

    public function update(Request $request, Farm $farm) {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:farms,name,'.$farm->id],
        ], [
            'name.required' => 'Nama farm wajib diisi.',
            'name.unique' => 'Nama farm sudah ada.',
        ]);

        $farm->update($data);

        return redirect()->route('master.farms.index')->with('status','Farm diupdate.');
    }

    public function destroy(Farm $farm) {
        $farm->delete();
        return back()->with('status','Farm dihapus.');
    }
}