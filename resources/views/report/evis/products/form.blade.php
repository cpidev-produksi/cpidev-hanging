@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            {{ isset($productEvis) ? 'Edit Produk' : 'Tambah Produk' }}
        </div>
    </div>

    <div class="panel-body">
        <form method="POST" action="{{ isset($productEvis) ? route('product-evis.update', $productEvis->id) : route('product-evis.store') }}">
            @csrf
            @if(isset($productEvis))
                @method('PUT')
            @endif

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 6px;">Material Number *</label>
                <input type="text" name="material_number" 
                    value="{{ isset($productEvis) ? $productEvis->material_number : old('material_number') }}"
                    placeholder="Cth: MAT001"
                    required
                    style="width: 100%; padding: 10px; border: 1px solid var(--card-border); border-radius: 8px;">
                @error('material_number')
                    <small style="color: var(--error);">{{ $message }}</small>
                @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 6px;">Nama Produk *</label>
                <input type="text" name="name" 
                    value="{{ isset($productEvis) ? $productEvis->name : old('name') }}"
                    placeholder="Cth: Produk Frozen"
                    required
                    style="width: 100%; padding: 10px; border: 1px solid var(--card-border); border-radius: 8px;">
                @error('name')
                    <small style="color: var(--error);">{{ $message }}</small>
                @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 6px;">
                    Satuan (Kg per Bag) *
                </label>
                <input type="number" step="0.01" min="0" name="satuan"
                    value="{{ isset($productEvis) ? $productEvis->satuan : old('satuan') }}"
                    placeholder="Cth: 10"
                    required
                    style="width: 100%; padding: 10px; border: 1px solid var(--card-border); border-radius: 8px;">
                @error('satuan')
                    <small style="color: var(--error);">{{ $message }}</small>
                @enderror
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="topnav-link" style="flex: 1; background: var(--accent); color: white;">
                    {{ isset($productEvis) ? 'Perbarui' : 'Tambah' }} Produk
                </button>
                <a href="{{ route('product-evis.index') }}" class="topnav-link" style="flex: 1; text-align: center;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection