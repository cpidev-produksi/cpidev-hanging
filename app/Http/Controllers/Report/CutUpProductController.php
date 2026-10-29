<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CutUpProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CutUpProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $products = CutUpProduct::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('report.cutup.product.index', [
            'products' => $products,
            'search'   => $search,
            'total'    => CutUpProduct::count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('cutup_products', 'name')],
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.unique'   => 'Nama produk sudah ada di daftar.',
            'name.max'      => 'Nama produk maksimal 150 karakter.',
        ]);

        CutUpProduct::create(['name' => trim($data['name'])]);

        return redirect()->route('cut-up.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, CutUpProduct $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('cutup_products', 'name')->ignore($product->id)],
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.unique'   => 'Nama produk sudah ada di daftar.',
            'name.max'      => 'Nama produk maksimal 150 karakter.',
        ]);

        $product->update(['name' => trim($data['name'])]);

        return redirect()->route('cut-up.products.index', $request->only('q', 'page'))
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, CutUpProduct $product)
    {
        // Laporan lama tetap aman: nama produk sudah disimpan sebagai snapshot di tabel verifikasi.
        $product->delete();

        return redirect()->route('cut-up.products.index', $request->only('q', 'page'))
            ->with('status', 'Produk berhasil dihapus.');
    }
}
