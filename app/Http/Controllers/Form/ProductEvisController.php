<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\ProductEvis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductEvisController extends Controller
{
    public function index()
    {
        $products = ProductEvis::orderBy('material_number', 'asc')->paginate(10);
        return view('report.evis.products.index', compact('products'));
    }

    public function create()
    {
        return view('report.evis.products.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_number' => 'required|unique:product_evis|regex:/^[a-zA-Z0-9]+$/',
            'name' => 'required|string|max:255',
        ]);

        ProductEvis::create($validated);

        return redirect()->route('product-evis.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(ProductEvis $productEvis)
    {
        return view('report.evis.products.form', compact('productEvis'));
    }

    public function update(Request $request, ProductEvis $productEvis)
    {
        $productId = $request->input('product_id') ?: $productEvis->id;
        $validated = $request->validate([
            'material_number' => [
                'required',
                'regex:/^[a-zA-Z0-9]+$/',
                Rule::unique('product_evis', 'material_number')->ignore($productId),
            ],
            'name' => 'required|string|max:255',
        ]);

        $productEvis->update($validated);

        return redirect()->route('product-evis.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(ProductEvis $productEvis)
    {
        $productEvis->delete();
        return redirect()->route('product-evis.index')
            ->with('status', 'Produk berhasil dihapus.');
    }

    public function apiList()
    {
        return response()->json(
            ProductEvis::orderBy('material_number', 'asc')->get(['id', 'material_number', 'name'])
        );
    }
}
