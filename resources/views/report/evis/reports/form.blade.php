@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');

    :root {
        --fresh-primary: #16a34a;
        --fresh-light: #f0fdf4;
        --fresh-mid: #bbf7d0;
        --fresh-border: #86efac;
        --fresh-dark: #14532d;
        --fresh-header-bg: #dcfce7;

        --frozen-primary: #0284c7;
        --frozen-light: #f0f9ff;
        --frozen-mid: #bae6fd;
        --frozen-border: #7dd3fc;
        --frozen-dark: #0c4a6e;
        --frozen-header-bg: #dbeafe;

        --surface: #ffffff;
        --surface-2: #f8fafc;
        --border: #e2e8f0;
        --text: #0f172a;
        --text-muted: #64748b;
        --error: #ef4444;
        --grand-bg: #1e293b;
        --radius: 12px;
        --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06);
    }

    .evis-wrap {
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        max-width: 100%;
    }

    /* ── Panel ── */
    .evis-panel {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .evis-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        background: var(--surface-2);
    }

    .evis-panel-title {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: var(--text);
    }

    .evis-back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface);
        transition: all .15s;
    }
    .evis-back-link:hover { color: var(--text); border-color: #94a3b8; background: var(--surface-2); }

    .evis-panel-body { padding: 20px; }

    /* ── Header Grid ── */
    .header-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
        padding: 16px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
    }

    .field-group label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .field-group input,
    .field-group select {
        font-family: 'DM Sans', sans-serif;
        width: 100%;
        padding: 9px 11px;
        font-size: 13px;
        font-weight: 500;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        background: var(--surface);
        color: var(--text);
        transition: border-color .15s;
        box-sizing: border-box;
    }
    .field-group input:focus,
    .field-group select:focus {
        outline: none;
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148,163,184,.15);
    }
    .field-group .hint {
        font-size: 10.5px;
        color: var(--text-muted);
        margin-top: 4px;
        line-height: 1.4;
    }
    .field-error { font-size: 11px; color: var(--error); margin-top: 3px; }

    /* ── Section Headers ── */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 10px 10px 0 0;
        border: 1px solid;
        border-bottom: none;
        margin-bottom: 0;
    }

    .section-header.fresh-header {
        background: var(--fresh-header-bg);
        border-color: var(--fresh-border);
    }
    .section-header.frozen-header {
        background: var(--frozen-header-bg);
        border-color: var(--frozen-border);
    }

    .section-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .section-label.fresh { color: var(--fresh-dark); }
    .section-label.frozen { color: var(--frozen-dark); }

    .section-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px; height: 22px;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 800;
        font-family: 'DM Mono', monospace;
    }
    .fresh .section-badge { background: var(--fresh-primary); color: #fff; }
    .frozen .section-badge { background: var(--frozen-primary); color: #fff; }

    /* ── Add Button ── */
    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 7px;
        border: none;
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }

    .btn-add-fresh {
        background: var(--fresh-primary);
        color: #fff;
    }
    .btn-add-fresh:hover {
        background: #15803d;
        box-shadow: 0 2px 8px rgba(22,163,74,.35);
        transform: translateY(-1px);
    }
    .btn-add-frozen {
        background: var(--frozen-primary);
        color: #fff;
    }
    .btn-add-frozen:hover {
        background: #0369a1;
        box-shadow: 0 2px 8px rgba(2,132,199,.35);
        transform: translateY(-1px);
    }

    .btn-add svg { flex-shrink: 0; }

    /* ── Table Wrapper ── */
    .table-wrap {
        overflow-x: auto;
        border: 1px solid;
        border-radius: 0 0 10px 10px;
        margin-bottom: 12px;
    }
    .table-wrap.fresh-wrap { border-color: var(--fresh-border); }
    .table-wrap.frozen-wrap { border-color: var(--frozen-border); }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11.5px;
        font-family: 'DM Sans', sans-serif;
    }

    /* thead row 1 */
    .data-table thead tr:first-child th {
        padding: 7px 6px;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: .02em;
        border-bottom: 1px solid;
    }

    .fresh-table thead tr:first-child {
        background: var(--fresh-light);
    }
    .fresh-table thead tr:first-child th {
        color: var(--fresh-dark);
        border-color: var(--fresh-border);
        border-right: 1px solid var(--fresh-border);
    }
    .frozen-table thead tr:first-child {
        background: var(--frozen-light);
    }
    .frozen-table thead tr:first-child th {
        color: var(--frozen-dark);
        border-color: var(--frozen-border);
        border-right: 1px solid var(--frozen-border);
    }

    /* thead row 2 sub-labels */
    .data-table thead tr:last-child th {
        padding: 4px 2px;
        font-size: 9.5px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .fresh-table thead tr:last-child {
        background: #f0fdf4;
        border-bottom: 2px solid var(--fresh-border);
    }
    .fresh-table thead tr:last-child th { color: #4ade80; border-right: 1px solid var(--fresh-border); }
    .frozen-table thead tr:last-child {
        background: #e0f2fe;
        border-bottom: 2px solid var(--frozen-border);
    }
    .frozen-table thead tr:last-child th { color: #38bdf8; border-right: 1px solid var(--frozen-border); }

    /* Transfer group header */
    .th-transfer {
        text-align: center;
        white-space: nowrap;
        font-size: 10px !important;
    }

    /* tbody rows */
    .item-row { transition: background .1s; }
    .item-row:hover { background: #fafafa; }
    .fresh-row:hover { background: #f7fef9; }
    .frozen-row:hover { background: #f0f9ff; }

    .td-product {
        padding: 5px 8px;
        min-width: 180px;
    }
    .fresh-row .td-product { border-right: 1px solid var(--fresh-border); }
    .frozen-row .td-product { border-right: 1px solid var(--frozen-border); }

    .product-input {
        font-family: 'DM Sans', sans-serif;
        width: 100%;
        padding: 5px 7px;
        font-size: 11.5px;
        font-weight: 500;
        border-radius: 6px;
        transition: border-color .15s;
        box-sizing: border-box;
        background: var(--surface);
        color: var(--text);
    }
    .fresh-row .product-input {
        border: 1.5px solid var(--fresh-border);
    }
    .fresh-row .product-input:focus {
        outline: none;
        border-color: var(--fresh-primary);
        box-shadow: 0 0 0 3px rgba(22,163,74,.12);
    }
    .frozen-row .product-input {
        border: 1.5px solid var(--frozen-border);
    }
    .frozen-row .product-input:focus {
        outline: none;
        border-color: var(--frozen-primary);
        box-shadow: 0 0 0 3px rgba(2,132,199,.12);
    }

    .td-num { padding: 3px 2px; text-align: center; }
    .fresh-row .td-num-right { border-right: 1px solid var(--fresh-border); }
    .frozen-row .td-num-right { border-right: 1px solid var(--frozen-border); }

    .num-input {
        font-family: 'DM Mono', monospace;
        width: 100%;
        padding: 4px 2px;
        border: none;
        text-align: center;
        font-size: 11px;
        background: transparent;
        outline: none;
        color: var(--text);
        min-width: 38px;
    }
    .fresh-row .num-input { border-bottom: 1.5px solid var(--fresh-mid); }
    .frozen-row .num-input { border-bottom: 1.5px solid var(--frozen-mid); }
    .fresh-row .num-input:focus { border-bottom-color: var(--fresh-primary); background: var(--fresh-light); }
    .frozen-row .num-input:focus { border-bottom-color: var(--frozen-primary); background: var(--frozen-light); }

    .td-total {
        text-align: center;
        font-family: 'DM Mono', monospace;
        font-weight: 700;
        font-size: 11.5px;
        padding: 4px 6px;
        white-space: nowrap;
    }
    .fresh-row .td-total { color: var(--fresh-dark); border-right: 1px solid var(--fresh-border); }
    .frozen-row .td-total { color: var(--frozen-dark); border-right: 1px solid var(--frozen-border); }

    .td-action { padding: 4px 6px; text-align: center; width: 30px; }
    .btn-remove {
        background: none;
        border: none;
        cursor: pointer;
        color: #fca5a5;
        padding: 3px 5px;
        border-radius: 5px;
        font-size: 13px;
        line-height: 1;
        transition: all .15s;
    }
    .btn-remove:hover { background: #fef2f2; color: var(--error); }

    /* ── Totals bar ── */
    .totals-bar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid;
    }

    .totals-bar.fresh-totals {
        background: var(--fresh-light);
        border-color: var(--fresh-border);
    }
    .totals-bar.frozen-totals {
        background: var(--frozen-light);
        border-color: var(--frozen-border);
    }

    .total-item { flex: 1; min-width: 120px; }
    .total-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: 3px;
    }
    .fresh-totals .total-label { color: var(--fresh-primary); }
    .frozen-totals .total-label { color: var(--frozen-primary); }

    .total-value {
        font-family: 'DM Mono', monospace;
        font-size: 18px;
        font-weight: 700;
    }
    .fresh-totals .total-value { color: var(--fresh-dark); }
    .frozen-totals .total-value { color: var(--frozen-dark); }

    /* ── Grand Total ── */
    .grand-total {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1px;
        background: var(--grand-bg);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid var(--grand-bg);
    }
    .grand-item {
        padding: 16px 18px;
        background: #1e293b;
    }
    .grand-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .grand-value {
        font-family: 'DM Mono', monospace;
        font-size: 22px;
        font-weight: 700;
        color: #f1f5f9;
    }

    /* ── Submit Area ── */
    .form-actions {
        display: flex;
        gap: 10px;
    }

    .btn-submit {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all .15s;
        background: #1e293b;
        color: #fff;
    }
    .btn-submit:hover { background: #0f172a; box-shadow: var(--shadow-md); transform: translateY(-1px); }

    .btn-cancel {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        padding: 12px 20px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        background: var(--surface);
        color: var(--text-muted);
        text-decoration: none;
        transition: all .15s;
        cursor: pointer;
    }
    .btn-cancel:hover { border-color: #94a3b8; color: var(--text); background: var(--surface-2); }

    /* ── Section spacer ── */
    .section-group { margin-bottom: 28px; }

    /* Row zebra subtle */
    .fresh-row:nth-child(even) { background: #fafffe; }
    .frozen-row:nth-child(even) { background: #f8fbff; }

    /* Animate new rows */
    .item-row.row-enter {
        animation: rowIn .2s ease forwards;
    }
    @keyframes rowIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="evis-wrap">
<div class="evis-panel">

    {{-- Header --}}
    <div class="evis-panel-header">
        <div class="evis-panel-title">
            {{ isset($reportEvis) ? 'Edit Report Evisceration' : 'Buat Report Evisceration Baru' }}
        </div>
        <a href="{{ route('product-evis.index') }}" class="evis-back-link">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
            </svg>
            Daftar Produk
        </a>
    </div>

    <div class="evis-panel-body">
    <form method="POST" action="@if(isset($reportEvis)) {{ route('report-evis.update', ['report_evi' => $reportEvis->id]) }} @else {{ route('report-evis.store') }} @endif">
        @csrf
        @if(isset($reportEvis)) @method('PUT') @endif

        {{-- ── Header Inputs ── --}}
        <div class="header-grid">
            <div class="field-group">
                <label>Tanggal Report *</label>
                <input type="date" id="reportDate" name="report_date"
                    value="{{ isset($reportEvis) ? $reportEvis->report_date->format('Y-m-d') : now()->format('Y-m-d') }}"
                    required>
                @error('report_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label>Lokasi *</label>
                <select id="locationSelect" name="location" required>
                    @php $locVal = old('location', $reportEvis->location ?? 'SH01'); @endphp
                    <option value="SH01" {{ $locVal === 'SH01' ? 'selected' : '' }}>SH01</option>
                    <option value="SH02" {{ $locVal === 'SH02' ? 'selected' : '' }}>SH02</option>
                </select>
                @error('location')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label>Shift *</label>
                <select id="shiftSelect" name="shift" required>
                    @php $shiftVal = old('shift', $reportEvis->shift ?? 'pagi'); @endphp
                    <option value="pagi" {{ $shiftVal === 'pagi' ? 'selected' : '' }}>Pagi</option>
                    <option value="malam" {{ $shiftVal === 'malam' ? 'selected' : '' }}>Malam</option>
                </select>
                @error('shift')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label>Jumlah Truk</label>
                <input type="number" min="0" id="truckCount" name="truck_count"
                    value="{{ old('truck_count', $reportEvis->truck_count ?? 0) }}">
                <div class="hint">Autofill, bisa diedit.</div>
                @error('truck_count')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label>Ayam Diterima (Ekor)</label>
                <input type="number" min="0" id="receivedChicken" name="received_chicken"
                    value="{{ old('received_chicken', $reportEvis->received_chicken ?? 0) }}">
                <div class="hint">Autofill, bisa diedit.</div>
                @error('received_chicken')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label>Yield (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="yield_percent"
                    value="{{ old('yield_percent', $reportEvis->yield_percent ?? '') }}"
                    placeholder="Cth: 72.50">
                @error('yield_percent')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label>Netto Weight (kg) *</label>
                <input type="number" step="0.01" min="0" id="nettoWeight" name="netto_weight"
                    value="{{ old('netto_weight', $reportEvis->netto_weight ?? '') }}"
                    placeholder="Contoh: 5000.00"
                    required>
                <div class="hint">Total netto weight dalam kilogram</div>
                @error('netto_weight')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        @php
            $freshItems  = $freshItems  ?? collect();
            $frozenItems = $frozenItems ?? collect();
        @endphp

        {{-- ══════════════════════ FRESH TABLE ══════════════════════ --}}
        <div class="section-group">
            <div class="section-header fresh-header">
                <div class="section-label fresh">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7z"/>
                        <circle cx="12" cy="9" r="2.5"/>
                    </svg>
                    Fresh
                    <span class="section-badge" id="freshRowCount">1</span>
                </div>
                <button type="button" onclick="addRow('fresh')" class="btn-add btn-add-fresh">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Fresh
                </button>
            </div>

            <div class="table-wrap fresh-wrap">
                <table class="data-table fresh-table">
                    <thead>
                        <tr>
                            <th style="width:180px; text-align:left; padding-left:10px;">Nama Produk</th>
                            @for($i = 1; $i <= 10; $i++)
                                <th colspan="2" class="th-transfer">No. Transfer {{ $i }}</th>
                            @endfor
                            <th style="text-align:center;">Total Bag</th>
                            <th style="text-align:center;">Total Kg</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th></th>
                            @for($i = 1; $i <= 10; $i++)
                                <th>Bag</th>
                                <th>Kg</th>
                            @endfor
                            <th></th><th></th><th></th>
                        </tr>
                    </thead>
                    <tbody id="freshItemsContainer">
                        @if(isset($reportEvis))
                            @php $rows = $freshItems->count() ? $freshItems : collect([null]); @endphp
                            @foreach($rows as $idx => $item)
                                <tr class="item-row fresh-row" data-scope="fresh" data-row="{{ $idx }}" data-satuan="0">
                                    <td class="td-product">
                                        <input type="hidden" name="fresh_items[{{ $idx }}][product_evis_id]" value="{{ $item?->product_evis_id ?? '' }}" required>
                                        <input type="text" class="product-input" placeholder="Cari produk…"
                                            value="{{ $item?->product?->material_number }} - {{ $item?->product?->name }}"
                                            data-scope="fresh" data-index="{{ $idx }}"
                                            list="productListFresh{{ $idx }}">
                                        <datalist id="productListFresh{{ $idx }}"></datalist>
                                    </td>
                                    @for($i = 1; $i <= 10; $i++)
                                        <td class="td-num">
                                            <input type="number" step="0.01" min="0"
                                                name="fresh_items[{{ $idx }}][bag_{{ $i }}]"
                                                value="{{ $item?->getAttribute("bag_$i") ?? '' }}"
                                                class="num-input item-bag" data-scope="fresh" data-index="{{ $idx }}" data-col="{{ $i }}">
                                        </td>
                                        <td class="td-num td-num-right">
                                            <input type="number" step="0.01" min="0"
                                                name="fresh_items[{{ $idx }}][kg_{{ $i }}]"
                                                value="{{ $item?->getAttribute("kg_$i") ?? '' }}"
                                                class="num-input item-kg" data-scope="fresh" data-index="{{ $idx }}" data-col="{{ $i }}">
                                        </td>
                                    @endfor
                                    <td class="td-total">
                                        <span class="total-bag">{{ $item?->total_bag }}</span>
                                    </td>
                                    <td class="td-total">
                                        <span class="total-kg">{{ $item?->total_kg }}</span>
                                    </td>
                                    <td class="td-action">
                                        <button type="button" onclick="removeRow('fresh', {{ $idx }})" class="btn-remove" title="Hapus">&#x2715;</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="item-row fresh-row" data-scope="fresh" data-row="0" data-satuan="0">
                                <td class="td-product">
                                    <input type="hidden" name="fresh_items[0][product_evis_id]" value="" required>
                                    <input type="text" class="product-input" placeholder="Cari produk…"
                                        data-scope="fresh" data-index="0" list="productListFresh0">
                                    <datalist id="productListFresh0"></datalist>
                                </td>
                                @for($i = 1; $i <= 10; $i++)
                                    <td class="td-num">
                                        <input type="number" step="0.01" min="0"
                                            name="fresh_items[0][bag_{{ $i }}]"
                                            class="num-input item-bag" data-scope="fresh" data-index="0" data-col="{{ $i }}">
                                    </td>
                                    <td class="td-num td-num-right">
                                        <input type="number" step="0.01" min="0"
                                            name="fresh_items[0][kg_{{ $i }}]"
                                            class="num-input item-kg" data-scope="fresh" data-index="0" data-col="{{ $i }}">
                                    </td>
                                @endfor
                                <td class="td-total"><span class="total-bag"></span></td>
                                <td class="td-total"><span class="total-kg"></span></td>
                                <td class="td-action">
                                    <button type="button" onclick="removeRow('fresh', 0)" class="btn-remove" title="Hapus">&#x2715;</button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Fresh Totals --}}
            <div class="totals-bar fresh-totals">
                <div class="total-item">
                    <div class="total-label">Total Fresh Bag</div>
                    <div class="total-value" id="freshTotalBag">0.00</div>
                </div>
                <div class="total-item">
                    <div class="total-label">Total Fresh Kg</div>
                    <div class="total-value" id="freshTotalKg">0.00</div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════ FROZEN TABLE ══════════════════════ --}}
        <div class="section-group">
            <div class="section-header frozen-header">
                <div class="section-label frozen">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/><line x1="19.07" y1="4.93" x2="4.93" y2="19.07"/>
                        <circle cx="12" cy="12" r="2.5"/>
                    </svg>
                    Frozen
                    <span class="section-badge" id="frozenRowCount">1</span>
                </div>
                <button type="button" onclick="addRow('frozen')" class="btn-add btn-add-frozen">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Frozen
                </button>
            </div>

            <div class="table-wrap frozen-wrap">
                <table class="data-table frozen-table">
                    <thead>
                        <tr>
                            <th style="width:180px; text-align:left; padding-left:10px;">Nama Produk</th>
                            @for($i = 1; $i <= 10; $i++)
                                <th colspan="2" class="th-transfer">No. Transfer {{ $i }}</th>
                            @endfor
                            <th style="text-align:center;">Total Bag</th>
                            <th style="text-align:center;">Total Kg</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th></th>
                            @for($i = 1; $i <= 10; $i++)
                                <th>Bag</th>
                                <th>Kg</th>
                            @endfor
                            <th></th><th></th><th></th>
                        </tr>
                    </thead>
                    <tbody id="frozenItemsContainer">
                        @if(isset($reportEvis))
                            @php $rows = $frozenItems->count() ? $frozenItems : collect([null]); @endphp
                            @foreach($rows as $idx => $item)
                                <tr class="item-row frozen-row" data-scope="frozen" data-row="{{ $idx }}" data-satuan="0">
                                    <td class="td-product">
                                        <input type="hidden" name="frozen_items[{{ $idx }}][product_evis_id]" value="{{ $item?->product_evis_id ?? '' }}" required>
                                        <input type="text" class="product-input" placeholder="Cari produk…"
                                            value="{{ $item?->product?->material_number }} - {{ $item?->product?->name }}"
                                            data-scope="frozen" data-index="{{ $idx }}"
                                            list="productListFrozen{{ $idx }}">
                                        <datalist id="productListFrozen{{ $idx }}"></datalist>
                                    </td>
                                    @for($i = 1; $i <= 10; $i++)
                                        <td class="td-num">
                                            <input type="number" step="0.01" min="0"
                                                name="frozen_items[{{ $idx }}][bag_{{ $i }}]"
                                                value="{{ $item?->getAttribute("bag_$i") ?? '' }}"
                                                class="num-input item-bag" data-scope="frozen" data-index="{{ $idx }}" data-col="{{ $i }}">
                                        </td>
                                        <td class="td-num td-num-right">
                                            <input type="number" step="0.01" min="0"
                                                name="frozen_items[{{ $idx }}][kg_{{ $i }}]"
                                                value="{{ $item?->getAttribute("kg_$i") ?? '' }}"
                                                class="num-input item-kg" data-scope="frozen" data-index="{{ $idx }}" data-col="{{ $i }}">
                                        </td>
                                    @endfor
                                    <td class="td-total"><span class="total-bag">{{ $item?->total_bag }}</span></td>
                                    <td class="td-total"><span class="total-kg">{{ $item?->total_kg }}</span></td>
                                    <td class="td-action">
                                        <button type="button" onclick="removeRow('frozen', {{ $idx }})" class="btn-remove" title="Hapus">&#x2715;</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="item-row frozen-row" data-scope="frozen" data-row="0" data-satuan="0">
                                <td class="td-product">
                                    <input type="hidden" name="frozen_items[0][product_evis_id]" value="" required>
                                    <input type="text" class="product-input" placeholder="Cari produk…"
                                        data-scope="frozen" data-index="0" list="productListFrozen0">
                                    <datalist id="productListFrozen0"></datalist>
                                </td>
                                @for($i = 1; $i <= 10; $i++)
                                    <td class="td-num">
                                        <input type="number" step="0.01" min="0"
                                            name="frozen_items[0][bag_{{ $i }}]"
                                            class="num-input item-bag" data-scope="frozen" data-index="0" data-col="{{ $i }}">
                                    </td>
                                    <td class="td-num td-num-right">
                                        <input type="number" step="0.01" min="0"
                                            name="frozen_items[0][kg_{{ $i }}]"
                                            class="num-input item-kg" data-scope="frozen" data-index="0" data-col="{{ $i }}">
                                    </td>
                                @endfor
                                <td class="td-total"><span class="total-bag"></span></td>
                                <td class="td-total"><span class="total-kg"></span></td>
                                <td class="td-action">
                                    <button type="button" onclick="removeRow('frozen', 0)" class="btn-remove" title="Hapus">&#x2715;</button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Frozen Totals --}}
            <div class="totals-bar frozen-totals">
                <div class="total-item">
                    <div class="total-label">Total Frozen Bag</div>
                    <div class="total-value" id="frozenTotalBag">0.00</div>
                </div>
                <div class="total-item">
                    <div class="total-label">Total Frozen Kg</div>
                    <div class="total-value" id="frozenTotalKg">0.00</div>
                </div>
            </div>
        </div>

        {{-- Grand Total --}}
        <div class="grand-total">
            <div class="grand-item">
                <div class="grand-label">Grand Total Bag</div>
                <div class="grand-value" id="grandTotalBag">0.00</div>
            </div>
            <div class="grand-item">
                <div class="grand-label">Grand Total Kg</div>
                <div class="grand-value" id="grandTotalKg">0.00</div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                {{ isset($reportEvis) ? 'Update' : 'Simpan' }} Report
            </button>
            <a href="{{ route('report-evis.index') }}" class="btn-cancel">Batal</a>
        </div>

    </form>
    </div>
</div>
</div>

<script>
let productData = [];
let productMap  = new Map();
let productById = new Map();

let rowCount = {
    fresh:  document.querySelectorAll('#freshItemsContainer tr.item-row').length  || 1,
    frozen: document.querySelectorAll('#frozenItemsContainer tr.item-row').length || 1,
};

let statsTouched = { truck: false, received: false };
document.getElementById('truckCount')?.addEventListener('input', () => statsTouched.truck = true);
document.getElementById('receivedChicken')?.addEventListener('input', () => statsTouched.received = true);

// Update row-count badges
function updateBadge(scope) {
    const count = document.querySelectorAll(`#${scope}ItemsContainer tr.item-row`).length;
    const badge = document.getElementById(`${scope}RowCount`);
    if (badge) badge.textContent = count;
}
updateBadge('fresh');
updateBadge('frozen');

// Load product list
fetch('{{ url("/api/product-evis/list") }}', {
    credentials: 'include',
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
})
.then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
.then(data => {
    productData = data;
    productMap  = new Map(data.map(p => [`${p.material_number} - ${p.name}`.trim(), String(p.id)]));
    productById = new Map(data.map(p => [String(p.id), p]));

    document.querySelectorAll('.product-input').forEach(input => setupProductInput(input));
    document.querySelectorAll('tr.item-row').forEach(row => {
        const scope = row.dataset.scope;
        const index = row.dataset.row;
        const hid = row.querySelector(`input[name="${scope}_items[${index}][product_evis_id]"]`);
        if (hid?.value) setRowSatuanFromProductId(scope, index, hid.value);
        attachBagKgListeners(scope, index);
    });
    calculateAllTotals();
})
.catch(err => console.error('Error loading products:', err));

function setupProductInput(input) {
    const scope    = input.dataset.scope;
    const index    = input.dataset.index;
    const capScope = scope === 'fresh' ? 'Fresh' : 'Frozen';
    const datalist = document.getElementById(`productList${capScope}${index}`);
    const hidden   = document.querySelector(`input[name="${scope}_items[${index}][product_evis_id]"]`);

    input.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        const filtered = productData.filter(p =>
            p.material_number.toLowerCase().includes(q) || p.name.toLowerCase().includes(q)
        );
        datalist.innerHTML = filtered.map(p =>
            `<option value="${p.material_number} - ${p.name}" data-id="${p.id}"></option>`
        ).join('');
    });

    const onResolve = () => {
        resolveProductSelection(input, hidden);
        setRowSatuanFromProductId(scope, index, hidden.value);
        document.querySelectorAll(`.item-bag[data-scope="${scope}"][data-index="${index}"]`).forEach(b => {
            if (b.value) autoFillKgForBagInput(b);
        });
    };
    input.addEventListener('change', onResolve);
    input.addEventListener('blur',   onResolve);
}

function resolveProductSelection(input, hiddenField) {
    const key = input.value.trim();
    hiddenField.value = productMap.get(key) ?? '';
}

function setRowSatuanFromProductId(scope, index, productId) {
    const row = document.querySelector(`tr.item-row[data-scope="${scope}"][data-row="${index}"]`);
    if (!row) return;
    const p = productById.get(String(productId));
    const satuan = p ? parseFloat(p.satuan || 0) : 0;
    row.dataset.satuan = isFinite(satuan) ? String(satuan) : '0';
}

function getRowSatuan(scope, index) {
    const row = document.querySelector(`tr.item-row[data-scope="${scope}"][data-row="${index}"]`);
    const s = parseFloat(row?.dataset?.satuan || 0);
    return isFinite(s) ? s : 0;
}

function autoFillKgForBagInput(bagInput) {
    const { scope, index, col } = bagInput.dataset;
    const satuan = getRowSatuan(scope, index);
    if (!satuan || satuan <= 0) return;
    const bagVal = parseFloat(bagInput.value || 0);
    if (!isFinite(bagVal)) return;
    const kgInput = document.querySelector(`input[name="${scope}_items[${index}][kg_${col}]"]`);
    if (!kgInput) return;
    kgInput.value = bagVal === 0 ? '' : (bagVal * satuan).toFixed(2);
    calculateAllTotals();
}

function addRow(scope) {
    const container = document.getElementById(scope === 'fresh' ? 'freshItemsContainer' : 'frozenItemsContainer');
    const newIndex  = rowCount[scope]++;
    const capScope  = scope === 'fresh' ? 'Fresh' : 'Frozen';
    const listId    = `productList${capScope}${newIndex}`;
    const rowClass  = `${scope}-row`;

    let html = `
        <tr class="item-row ${rowClass} row-enter" data-scope="${scope}" data-row="${newIndex}" data-satuan="0">
            <td class="td-product">
                <input type="hidden" name="${scope}_items[${newIndex}][product_evis_id]" value="" required>
                <input type="text" class="product-input" placeholder="Cari produk…"
                    data-scope="${scope}" data-index="${newIndex}" list="${listId}">
                <datalist id="${listId}"></datalist>
            </td>`;

    for (let i = 1; i <= 10; i++) {
        html += `
            <td class="td-num">
                <input type="number" step="0.01" min="0"
                    name="${scope}_items[${newIndex}][bag_${i}]"
                    class="num-input item-bag" data-scope="${scope}" data-index="${newIndex}" data-col="${i}">
            </td>
            <td class="td-num td-num-right">
                <input type="number" step="0.01" min="0"
                    name="${scope}_items[${newIndex}][kg_${i}]"
                    class="num-input item-kg" data-scope="${scope}" data-index="${newIndex}" data-col="${i}">
            </td>`;
    }

    html += `
            <td class="td-total"><span class="total-bag"></span></td>
            <td class="td-total"><span class="total-kg"></span></td>
            <td class="td-action">
                <button type="button" onclick="removeRow('${scope}', ${newIndex})" class="btn-remove" title="Hapus">&#x2715;</button>
            </td>
        </tr>`;

    container.insertAdjacentHTML('beforeend', html);

    const newInput = container.querySelector(`input.product-input[data-scope="${scope}"][data-index="${newIndex}"]`);
    setupProductInput(newInput);
    attachBagKgListeners(scope, newIndex);
    updateBadge(scope);

    // scroll product input into view
    newInput?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    newInput?.focus();
}

function removeRow(scope, index) {
    document.querySelector(`tr.item-row[data-scope="${scope}"][data-row="${index}"]`)?.remove();
    updateBadge(scope);
    calculateAllTotals();
}

function attachBagKgListeners(scope, index) {
    document.querySelectorAll(`.item-bag[data-scope="${scope}"][data-index="${index}"]`).forEach(input => {
        input.addEventListener('input',  () => autoFillKgForBagInput(input));
        input.addEventListener('change', () => autoFillKgForBagInput(input));
    });
    document.querySelectorAll(`.item-kg[data-scope="${scope}"][data-index="${index}"]`).forEach(input => {
        input.addEventListener('input',  calculateAllTotals);
        input.addEventListener('change', calculateAllTotals);
    });
}

function calculateScopeTotals(scope) {
    let bag = 0, kg = 0;
    document.querySelectorAll(`tr.item-row[data-scope="${scope}"]`).forEach(row => {
        const idx = row.dataset.row;
        let rowBag = 0, rowKg = 0;
        for (let i = 1; i <= 10; i++) {
            rowBag += parseFloat(row.querySelector(`input[name="${scope}_items[${idx}][bag_${i}]"]`)?.value || 0);
            rowKg  += parseFloat(row.querySelector(`input[name="${scope}_items[${idx}][kg_${i}]"]`)?.value  || 0);
        }
        row.querySelector('.total-bag').textContent = rowBag > 0 ? rowBag.toFixed(2) : '';
        row.querySelector('.total-kg').textContent  = rowKg  > 0 ? rowKg.toFixed(2)  : '';
        bag += rowBag; kg += rowKg;
    });
    return { bag, kg };
}

function calculateAllTotals() {
    const fresh  = calculateScopeTotals('fresh');
    const frozen = calculateScopeTotals('frozen');
    document.getElementById('freshTotalBag').textContent  = fresh.bag.toFixed(2);
    document.getElementById('freshTotalKg').textContent   = fresh.kg.toFixed(2);
    document.getElementById('frozenTotalBag').textContent = frozen.bag.toFixed(2);
    document.getElementById('frozenTotalKg').textContent  = frozen.kg.toFixed(2);
    document.getElementById('grandTotalBag').textContent  = (fresh.bag + frozen.bag).toFixed(2);
    document.getElementById('grandTotalKg').textContent   = (fresh.kg  + frozen.kg).toFixed(2);
}

// Auto stats refresh
async function refreshStatsIfPossible() {
    const date     = document.getElementById('reportDate')?.value;
    const location = document.getElementById('locationSelect')?.value;
    const shift    = document.getElementById('shiftSelect')?.value;
    if (!date || !location || !shift) return;
    try {
        const url = new URL('{{ url("/api/report-evis/stats") }}', window.location.origin);
        url.searchParams.set('report_date', date);
        url.searchParams.set('location',    location);
        url.searchParams.set('shift',       shift);
        const r    = await fetch(url.toString(), {
            credentials: 'include',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        const data = await r.json();
        if (!statsTouched.truck)    document.getElementById('truckCount').value       = data.truck_count    ?? 0;
        if (!statsTouched.received) document.getElementById('receivedChicken').value  = data.received_chicken ?? 0;
    } catch(e) { console.warn('Stats refresh failed:', e); }
}

document.getElementById('reportDate')?.addEventListener('change',    refreshStatsIfPossible);
document.getElementById('locationSelect')?.addEventListener('change', refreshStatsIfPossible);
document.getElementById('shiftSelect')?.addEventListener('change',    refreshStatsIfPossible);
refreshStatsIfPossible();
calculateAllTotals();
</script>
@endsection