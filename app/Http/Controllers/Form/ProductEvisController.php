<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\ProductEvis;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductEvisController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $products = ProductEvis::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('material_number', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();
        return view('report.evis.products.index', compact('products', 'q'));
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
            'satuan' => 'required|numeric|min:0',
        ]);

        ProductEvis::create($validated);

        return $this->redirectToIndex($request)->with('status', 'Produk berhasil ditambahkan.');
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
            'satuan' => 'required|numeric|min:0',
        ]);

        $productEvis->update($validated);

        return $this->redirectToIndex($request)->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(ProductEvis $productEvis, Request $request)
    {
        $productEvis->delete();
        return $this->redirectToIndex($request)->with('status', 'Produk berhasil dihapus.');
    }

    public function apiList()
    {
        return response()->json(
            ProductEvis::orderBy('material_number', 'asc')->get(['id', 'material_number', 'name', 'satuan'])
        );
    }

    private function redirectToIndex(Request $request)
    {
        $page = $request->input('redirect_page', $request->query('page', 1));
        $q = $request->input('redirect_q', $request->query('q', ''));

        return redirect()->route('product-evis.index', array_filter([
            'page' => $page,
            'q' => $q,
        ], fn($v) => $v !== null && $v !== ''));
    }
}
