<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function index(Request $request) {
        $query = Farm::query();
 
        // Filter pencarian nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    
        // Filter huruf awal (alfabet)
        if ($request->filled('letter')) {
            $query->where('name', 'like', $request->letter . '%');
        }
    
        $farms = $query->orderBy('name')->paginate(15)->withQueryString();
    
        return view('master.farms.index', compact('farms'));
    }

    public function create() {
        return view('master.farms.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:farms,name'],
            'address' => ['required','string','max:255'],
            'city' => ['required','string','max:100'],
            'vendor_code' => ['required','string','max:50'], // boleh duplikat
            'area_category' => ['integer','in:1,2,3,4'],
            'distance' => ['string','max:100'],
        ], [
            'name.required' => 'Nama farm wajib diisi.',
            'name.unique' => 'Nama farm sudah ada.',
            'address.required' => 'Alamat farm wajib diisi.',
            'city.required' => 'Kota wajib diisi.',
            'vendor_code.required' => 'Vendor Code wajib diisi.',
            // 'area_category.required' => 'Kategori area wajib dipilih.',
            // 'area_category.in' => 'Kategori area harus 1, 2, 3, atau 4.',
            // 'distance.required' => 'Jarak wajib diisi.',
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
            'address' => ['required','string','max:255'],
            'city' => ['required','string','max:100'],
            'vendor_code' => ['required','string','max:50'],
            'area_category' => ['integer','in:1,2,3,4'],
            'distance' => ['string','max:100'],
        ], [
            'name.required' => 'Nama farm wajib diisi.',
            'name.unique' => 'Nama farm sudah ada.',
            'address.required' => 'Alamat farm wajib diisi.',
            'city.required' => 'Kota wajib diisi.',
            'vendor_code.required' => 'Vendor Code wajib diisi.',
            // 'area_category.required' => 'Kategori area wajib dipilih.',
            // 'area_category.in' => 'Kategori area harus 1, 2, 3, atau 4.',
            // 'distance.required' => 'Jarak wajib diisi.',
        ]);

        $farm->update($data);

        return redirect()->route('master.farms.index')->with('status','Farm diupdate.');
    }

    public function destroy(Farm $farm) {
        $farm->delete();
        return back()->with('status','Farm dihapus.');
    }
}