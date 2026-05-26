@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            {{ isset($reportEvis) ? 'Edit Report Evisceration' : 'Buat Report Evisceration Baru' }}
        </div>
        <a href="{{ route('product-evis.index') }}"
            class="topnav-link"
            style="font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
            </svg>
            Lihat daftar produk
        </a>
    </div>

    <div class="panel-body">
        <form method="POST" action="@if(isset($reportEvis)) {{ route('report-evis.update', ['report_evi' => $reportEvis->id]) }} @else {{ route('report-evis.store') }} @endif">
            @csrf
            @if(isset($reportEvis))
                @method('PUT')
            @endif

            {{-- Tanggal Report --}}
            <div style="margin-bottom: 20px;">
                <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 6px;">Tanggal Report *</label>
                <input type="date" name="report_date" 
                    value="{{ isset($reportEvis) ? $reportEvis->report_date->format('Y-m-d') : now()->format('Y-m-d') }}"
                    required 
                    style="width: 100%; max-width: 250px; padding: 10px; border: 1px solid var(--card-border); border-radius: 8px;">
                @error('report_date')
                    <small style="color: var(--error);">{{ $message }}</small>
                @enderror
            </div>

            {{-- Tabel Item Report dengan 10 kolom --}}
            <div style="overflow-x: auto; margin-bottom: 20px; border: 1px solid var(--card-border); border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="background: #f5f7fc; border-bottom: 2px solid var(--card-border);">
                            <th style="width: 200px; padding: 7px 10px; text-align: left; font-weight: 700; border-right: 1px solid var(--card-border);">Nama Produk</th>
                            @for($i = 1; $i <= 10; $i++)
                                <th colspan="2" style="text-align: center; padding: 7px 4px; font-weight: 700; border-right: 1px solid var(--card-border);">{{ $i }}</th>
                            @endfor
                            <th style="text-align: center; padding: 7px 6px; font-weight: 700; border-right: 1px solid var(--card-border);">Total Bag</th>
                            <th style="text-align: center; padding: 7px 6px; font-weight: 700; border-right: 1px solid var(--card-border);">Total Kg</th>
                            <th style="padding: 7px 6px;"></th>
                        </tr>
                        <tr style="background: #f9fafc; border-bottom: 1px solid var(--card-border);">
                            <th style="padding: 4px 10px; border-right: 1px solid var(--card-border);"></th>
                            @for($i = 1; $i <= 10; $i++)
                                <th style="text-align: center; padding: 4px 2px; font-size: 10px; font-weight: 600; color: var(--text-muted);">Bag</th>
                                <th style="text-align: center; padding: 4px 2px; font-size: 10px; font-weight: 600; color: var(--text-muted); border-right: 1px solid var(--card-border);">Kg</th>
                            @endfor
                            <th style="border-right: 1px solid var(--card-border);"></th>
                            <th style="border-right: 1px solid var(--card-border);"></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="itemsContainer">
                        @if(isset($reportEvis) && $reportEvis->items->count())
                            @foreach($reportEvis->items as $index => $item)
                                <tr class="item-row" data-row="{{ $index }}" style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 4px 6px; border-right: 1px solid var(--card-border);">
                                        <input type="hidden" name="items[{{ $index }}][product_evis_id]" 
                                            value="{{ $item->product_evis_id }}" required>
                                        <input type="text" class="product-input" placeholder="Cari produk..."
                                            value="{{ $item->product?->material_number }} - {{ $item->product?->name }}"
                                            data-index="{{ $index }}"
                                            list="productList{{ $index }}"
                                            style="width: 100%; padding: 4px 6px; border: 1px solid var(--card-border); border-radius: 4px;">
                                        <datalist id="productList{{ $index }}"></datalist>
                                    </td>
                                    @for($i = 1; $i <= 10; $i++)
                                        <td style="padding: 2px 2px;">
                                            <input type="number" step="0.01" min="0" 
                                                name="items[{{ $index }}][bag_{{ $i }}]"
                                                value="{{ $item->getAttribute("bag_$i") ?? '' }}"
                                                class="item-bag" data-index="{{ $index }}" data-col="{{ $i }}"
                                                style="width: 100%; padding: 3px 2px; border: none; border-bottom: 1px solid var(--card-border); text-align: center; font-size: 11px; background: transparent; outline: none;">
                                        </td>
                                        <td style="padding: 2px 2px; border-right: 1px solid var(--card-border);">
                                            <input type="number" step="0.01" min="0" 
                                                name="items[{{ $index }}][kg_{{ $i }}]"
                                                value="{{ $item->getAttribute("kg_$i") ?? '' }}"
                                                class="item-kg" data-index="{{ $index }}" data-col="{{ $i }}"
                                                style="width: 100%; padding: 3px 2px; border: none; border-bottom: 1px solid var(--card-border); text-align: center; font-size: 11px; background: transparent; outline: none;">
                                        </td>
                                    @endfor
                                    <td style="text-align: center; font-weight: 700; font-size: 12px; padding: 4px 6px; border-right: 1px solid var(--card-border);">
                                        <span class="total-bag">{{ $item->total_bag }}</span>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; font-size: 12px; padding: 4px 6px; border-right: 1px solid var(--card-border);">
                                        <span class="total-kg">{{ $item->total_kg }}</span>
                                    </td>
                                    <td style="padding: 4px 6px; text-align: center;">
                                        <button type="button" onclick="removeRow({{ $index }})"
                                            style="background: none; border: none; cursor: pointer; color: var(--error, #e53e3e); padding: 2px 4px; font-size: 16px; line-height: 1;"
                                            title="Hapus baris">&#x2715;</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="item-row" data-row="0" style="border-bottom: 1px solid var(--card-border);">
                                <td style="padding: 4px 6px; border-right: 1px solid var(--card-border);">
                                    <input type="hidden" name="items[0][product_evis_id]" value="" required>
                                    <input type="text" class="product-input" placeholder="Cari produk..."
                                        data-index="0"
                                        list="productList0"
                                        style="width: 100%; padding: 4px 6px; border: 1px solid var(--card-border); border-radius: 4px;">
                                    <datalist id="productList0"></datalist>
                                </td>
                                @for($i = 1; $i <= 10; $i++)
                                    <td style="padding: 2px 2px;">
                                        <input type="number" step="0.01" min="0" 
                                            name="items[0][bag_{{ $i }}]"
                                            class="item-bag" data-index="0" data-col="{{ $i }}"
                                            style="width: 100%; padding: 3px 2px; border: none; border-bottom: 1px solid var(--card-border); text-align: center; font-size: 11px; background: transparent; outline: none;">
                                    </td>
                                    <td style="padding: 2px 2px; border-right: 1px solid var(--card-border);">
                                        <input type="number" step="0.01" min="0" 
                                            name="items[0][kg_{{ $i }}]"
                                            class="item-kg" data-index="0" data-col="{{ $i }}"
                                            style="width: 100%; padding: 3px 2px; border: none; border-bottom: 1px solid var(--card-border); text-align: center; font-size: 11px; background: transparent; outline: none;">
                                    </td>
                                @endfor
                                <td style="text-align: center; font-weight: 700; font-size: 12px; padding: 4px 6px; border-right: 1px solid var(--card-border);">
                                    <span class="total-bag"></span>
                                </td>
                                <td style="text-align: center; font-weight: 700; font-size: 12px; padding: 4px 6px; border-right: 1px solid var(--card-border);">
                                    <span class="total-kg"></span>
                                </td>
                                <td style="padding: 4px 6px; text-align: center;">
                                    <button type="button" onclick="removeRow(0)"
                                        style="background: none; border: none; cursor: pointer; color: var(--error, #e53e3e); padding: 2px 4px; font-size: 16px; line-height: 1;"
                                        title="Hapus baris">&#x2715;</button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Grand Total --}}
            <div style="margin-bottom: 20px; padding: 12px; background: #f5f7fc; border-radius: 8px; border: 1px solid var(--card-border);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">GRAND TOTAL BAG</div>
                        <div style="font-size: 18px; font-weight: 700;" id="grandTotalBag">0.00</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">GRAND TOTAL KG</div>
                        <div style="font-size: 18px; font-weight: 700;" id="grandTotalKg">0.00</div>
                    </div>
                </div>
            </div>

            {{-- Tombol Tambah Row --}}
            <button type="button" onclick="addRow()" class="topnav-link" style="margin-bottom: 20px; background: var(--accent); color: white;">
                + Tambah Produk
            </button>

            {{-- Tombol Submit --}}
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="topnav-link" style="flex: 1; background: var(--accent); color: white;">
                    {{ isset($reportEvis) ? 'Update' : 'Simpan' }} Report
                </button>
                <a href="{{ route('report-evis.index') }}" class="topnav-link" style="flex: 1; text-align: center;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let rowCount = {{ isset($reportEvis) ? $reportEvis->items->count() : 1 }};
let productData = [];
let productMap = new Map();

// Load product list
fetch('/api/product-evis/list', {
    credentials: 'include'
})
    .then(r => r.json())
    .then(data => {
        productData = data;
        productMap = new Map(productData.map(p => [`${p.material_number} - ${p.name}`.trim(), String(p.id)]));
        initializeProductInputs();
    }).catch(error => {
        console.error('Error loading products:', error);
    });

function initializeProductInputs() {
    document.querySelectorAll('.product-input').forEach(input => {
        setupProductInput(input);
    });
}

function setupProductInput(input) {
    const index = input.dataset.index;
    const datalist = document.getElementById(`productList${index}`);
    const hiddenField = document.querySelector(`input[name="items[${index}][product_evis_id]"]`);

    input.addEventListener('input', function() {
        const filtered = productData.filter(p =>
            p.material_number.toLowerCase().includes(this.value.toLowerCase()) ||
            p.name.toLowerCase().includes(this.value.toLowerCase())
        );

        datalist.innerHTML = filtered.map(p =>
            `<option value="${p.material_number} - ${p.name}" data-id="${p.id}"></option>`
        ).join('');
    });

    input.addEventListener('change', function() {
        resolveProductSelection(this, datalist, hiddenField);
    });
    
    input.addEventListener('blur', function() {
        resolveProductSelection(this, datalist, hiddenField);
    });
    
    if (input.value && hiddenField.value) {
        // Already has hidden value, keep it
    }
}

function resolveProductSelection(input, datalist, hiddenField) {
  const key = input.value.trim();
  const id = productMap.get(key);

  if (id) {
    hiddenField.value = id;
    return;
  }

  hiddenField.value = '';
}

function addRow() {
    const container = document.getElementById('itemsContainer');
    const newIndex = rowCount++;

    let html = `
        <tr class="item-row" data-row="${newIndex}" style="border-bottom: 1px solid var(--card-border);">
            <td style="padding: 4px 6px; border-right: 1px solid var(--card-border);">
                <input type="hidden" name="items[${newIndex}][product_evis_id]" value="" required>
                <input type="text" class="product-input" placeholder="Cari produk..."
                    data-index="${newIndex}"
                    list="productList${newIndex}"
                    style="width: 100%; padding: 4px 6px; border: 1px solid var(--card-border); border-radius: 4px;">
                <datalist id="productList${newIndex}"></datalist>
            </td>
    `;

    for (let i = 1; i <= 10; i++) {
        html += `
            <td style="padding: 2px 2px;">
                <input type="number" step="0.01" min="0"
                    name="items[${newIndex}][bag_${i}]"
                    class="item-bag" data-index="${newIndex}" data-col="${i}"
                    style="width: 100%; padding: 3px 2px; border: none; border-bottom: 1px solid var(--card-border); text-align: center; font-size: 11px; background: transparent; outline: none;">
            </td>
            <td style="padding: 2px 2px; border-right: 1px solid var(--card-border);">
                <input type="number" step="0.01" min="0"
                    name="items[${newIndex}][kg_${i}]"
                    class="item-kg" data-index="${newIndex}" data-col="${i}"
                    style="width: 100%; padding: 3px 2px; border: none; border-bottom: 1px solid var(--card-border); text-align: center; font-size: 11px; background: transparent; outline: none;">
            </td>
        `;
    }

    html += `
            <td style="text-align: center; font-weight: 700; font-size: 12px; padding: 4px 6px; border-right: 1px solid var(--card-border);">
                <span class="total-bag"></span>
            </td>
            <td style="text-align: center; font-weight: 700; font-size: 12px; padding: 4px 6px; border-right: 1px solid var(--card-border);">
                <span class="total-kg"></span>
            </td>
            <td style="padding: 4px 6px; text-align: center;">
                <button type="button" onclick="removeRow(${newIndex})"
                    style="background: none; border: none; cursor: pointer; color: var(--error, #e53e3e); padding: 2px 4px; font-size: 16px; line-height: 1;"
                    title="Hapus baris">&#x2715;</button>
            </td>
        </tr>
    `;

    container.insertAdjacentHTML('beforeend', html);
    
    const newInput = container.querySelector(`input[data-index="${newIndex}"]`);
    setupProductInput(newInput);
    attachBagKgListeners(newIndex);
}

function removeRow(index) {
    document.querySelector(`tr[data-row="${index}"]`)?.remove();
    calculateGrandTotals();
}

function attachBagKgListeners(index) {
    document.querySelectorAll(`.item-bag[data-index="${index}"], .item-kg[data-index="${index}"]`).forEach(input => {
        input.addEventListener('change', () => {
            calculateRowTotals(index);
            calculateGrandTotals();
        });
        input.addEventListener('input', () => calculateGrandTotals());
    });
}

function calculateRowTotals(index) {
    const row = document.querySelector(`tr[data-row="${index}"]`);
    if (!row) return;

    let totalBag = 0, totalKg = 0;
    for (let i = 1; i <= 10; i++) {
        const bag = parseFloat(row.querySelector(`input[name="items[${index}][bag_${i}]"]`)?.value || 0);
        const kg = parseFloat(row.querySelector(`input[name="items[${index}][kg_${i}]"]`)?.value || 0);
        totalBag += bag;
        totalKg += kg;
    }

    row.querySelector('.total-bag').textContent = totalBag > 0 ? totalBag.toFixed(2) : '';
    row.querySelector('.total-kg').textContent = totalKg > 0 ? totalKg.toFixed(2) : '';
}

function calculateGrandTotals() {
    let grandBag = 0, grandKg = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const index = row.dataset.row;
        let rowBag = 0, rowKg = 0;
        
        for (let i = 1; i <= 10; i++) {
            const bag = parseFloat(row.querySelector(`input[name="items[${index}][bag_${i}]"]`)?.value || 0);
            const kg = parseFloat(row.querySelector(`input[name="items[${index}][kg_${i}]"]`)?.value || 0);
            rowBag += bag;
            rowKg += kg;
        }
        
        grandBag += rowBag;
        grandKg += rowKg;
    });
    
    document.getElementById('grandTotalBag').textContent = grandBag.toFixed(2);
    document.getElementById('grandTotalKg').textContent = grandKg.toFixed(2);
}

// Initialize listeners untuk existing rows
document.querySelectorAll('.item-row').forEach(row => {
    const index = row.dataset.row;
    attachBagKgListeners(index);
});

// Calculate on load
calculateGrandTotals();
</script>
@endsection