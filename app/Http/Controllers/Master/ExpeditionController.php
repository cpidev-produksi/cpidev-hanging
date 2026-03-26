<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Expedition;
use Illuminate\Http\Request;

class ExpeditionController extends Controller
{
    public function index() {
        $expeditions = Expedition::query()->latest()->paginate(20);
        return view('master.expeditions.index', compact('expeditions'));
    }

    public function create() {
        return view('master.expeditions.create');
    }

    public function store(Request $request) {
        $data = $request->validate(['name' => ['required','string','max:150']]);
        Expedition::create($data);
        return redirect()->route('master.expeditions.index')->with('status','Ekspedisi dibuat.');
    }

    public function edit(Expedition $expedition) {
        return view('master.expeditions.edit', compact('expedition'));
    }

    public function update(Request $request, Expedition $expedition) {
        $data = $request->validate(['name' => ['required','string','max:150']]);
        $expedition->update($data);
        return redirect()->route('master.expeditions.index')->with('status','Ekspedisi diupdate.');
    }

    public function destroy(Expedition $expedition) {
        $expedition->delete();
        return back()->with('status','Ekspedisi dihapus.');
    }
}
