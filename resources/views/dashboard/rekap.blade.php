@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;0,14..32,900&display=swap');

    :root {
        --bg: #f0f4fa;
        --surface: #ffffff;
        --surface2: #f7f9fd;
        --border: #e2e8f4;
        --text: #0f1623;
        --muted: #8494b2;
        --accent: #2563eb;
        --accent2: #7c3aed;
        --green: #059669;
        --red: #dc2626;
        --gold: #d97706;
        --radius: 16px;
        --radius-sm: 10px;
    }

    *, *::before, *::after { box-sizing: border-box; }

    .rk-wrap {
        max-width: 1360px;
        margin: 0 auto;
        padding: 32px 24px 56px;
        font-family: 'Inter', sans-serif;
        background: var(--bg);
        min-height: 100vh;
        color: var(--text);
    }

    /* ── Header ── */
    .rk-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 32px;
    }

    .rk-title {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .rk-eyebrow {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--accent);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .rk-eyebrow::before {
        content: '';
        display: inline-block;
        width: 18px;
        height: 2px;
        background: var(--accent);
        border-radius: 2px;
    }

    .rk-h1 {
        margin: 0;
        font-size: 34px;
        font-weight: 900;
        letter-spacing: -.5px;
        color: var(--text);
        line-height: 1.1;
    }

    .rk-period {
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
        margin-top: 2px;
    }

    .rk-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--surface2);
        color: var(--text);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all .18s ease;
        white-space: nowrap;
    }

    .rk-back-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: rgba(79,140,255,.07);
    }

    /* ── Summary Cards ── */
    .rk-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .rk-card {
        border-radius: var(--radius);
        padding: 24px 26px;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .rk-card::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    .rk-card-truk {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #bfdbfe;
    }

    .rk-card-truk::before {
        background: radial-gradient(ellipse at top right, rgba(37,99,235,.08) 0%, transparent 65%);
    }

    .rk-card-ayam {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        border-color: #ddd6fe;
    }

    .rk-card-ayam::before {
        background: radial-gradient(ellipse at top right, rgba(124,58,237,.08) 0%, transparent 65%);
    }

    .rk-card-mati {
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        border-color: #fecdd3;
    }

    .rk-card-mati::before {
        background: radial-gradient(ellipse at top right, rgba(220,38,38,.08) 0%, transparent 65%);
    }

    .rk-card-retur {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border-color: #fde68a;
    }

    .rk-card-retur::before {
        background: radial-gradient(ellipse at top right, rgba(217,119,6,.08) 0%, transparent 65%);
    }

    .rk-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        position: relative;
        z-index: 1;
    }

    .icon-truk { background: rgba(37,99,235,.12); }
    .icon-ayam { background: rgba(124,58,237,.12); }
    .icon-mati { background: rgba(220,38,38,.12); }
    .icon-retur { background: rgba(217,119,6,.12); }

    .rk-card-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--muted);
        position: relative;
        z-index: 1;
    }

    .rk-card-value {
        font-family: 'Inter', sans-serif;
        font-size: 42px;
        font-weight: 700;
        letter-spacing: -.5px;
        line-height: 1;
        position: relative;
        z-index: 1;
    }

    .val-truk { color: #1d4ed8; }
    .val-ayam { color: #6d28d9; }
    .val-mati { color: #b91c1c; }
    .val-retur { color: #b45309; }

    .rk-card-sub {
        font-size: 11px;
        font-weight: 700;
        color: var(--muted);
        position: relative;
        z-index: 1;
    }

    .rk-card-deco {
        position: absolute;
        right: 20px;
        bottom: 14px;
        font-family: 'Inter', sans-serif;
        font-size: 60px;
        font-weight: 700;
        opacity: .06;
        pointer-events: none;
        line-height: 1;
        user-select: none;
    }

    /* ── Filter Panel ── */
    .rk-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .rk-panel-head {
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        background: var(--surface2);
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
    }

    .rk-filter {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .rk-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .rk-field label {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .rk-field select,
    .rk-field input[type="date"] {
        height: 36px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        padding: 0 12px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
        background: var(--bg);
        font-family: 'Inter', sans-serif;
        transition: border-color .15s;
        min-width: 140px;
    }

    .rk-field select:focus,
    .rk-field input[type="date"]:focus {
        border-color: var(--accent);
    }

    .rk-btn {
        height: 36px;
        border-radius: var(--radius-sm);
        border: none;
        background: linear-gradient(90deg, var(--accent), #818cf8);
        color: #fff;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 800;
        padding: 0 18px;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        letter-spacing: .04em;
    }

    .rk-btn:hover { opacity: .88; transform: translateY(-1px); }
    .rk-btn:active { transform: translateY(0); }

    /* ── Table ── */
    .rk-panel-body { padding: 20px 22px 24px; }

    .rk-table-wrap {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: var(--surface);
    }

    thead tr {
        background: var(--surface2);
    }

    th {
        text-align: left;
        padding: 13px 12px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    th.num { text-align: right; }

    td {
        padding: 12px 12px;
        border-bottom: 1px solid #f0f4fb;
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        vertical-align: middle;
    }

    tr:last-child td { border-bottom: none; }

    tbody tr {
        transition: background .12s;
        cursor: pointer;
    }

    tbody tr:hover { background: #f0f6ff; }
    tbody tr:focus { outline: 2px solid var(--accent); outline-offset: -2px; }

    td.num { text-align: right; }

    .rk-no {
        font-family: 'Inter', sans-serif;
        font-size: 10px;
        font-weight: 700;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 2px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    .rk-polisi {
        font-family: 'Inter', sans-serif;
        font-size: 11px;
        font-weight: 700;
        background: var(--surface2);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 3px 9px;
        border-radius: 7px;
        display: inline-block;
        letter-spacing: .04em;
    }

    .rk-time {
        font-family: 'Inter', sans-serif;
        font-size: 11px;
        color: var(--muted);
        font-weight: 400;
    }

    .rk-size {
        display: inline-flex;
        align-items: center;
        padding: 2px 9px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        background: #fef9c3;
        border: 1px solid #fde68a;
        color: #92400e;
        font-family: 'Inter', sans-serif;
    }

    .rk-num {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 700;
    }

    .num-mati { color: var(--red); }
    .num-retur { color: var(--gold); }

    .num-diterima {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #5b21b6;
        background: #f5f3ff;
        padding: 3px 10px;
        border-radius: 8px;
        border: 1px solid #ddd6fe;
        display: inline-block;
    }

    .rk-empty {
        padding: 48px 20px;
        text-align: center;
        color: var(--muted);
        font-size: 13px;
        font-weight: 600;
    }

    .rk-empty-icon { font-size: 32px; margin-bottom: 10px; }

    .rk-modal { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(15, 22, 35, .6); backdrop-filter: blur(2px); }
    .rk-modal.is-open { display: flex; }
    .rk-modal-dialog { width: min(560px, 100%); max-height: min(720px, 90vh); overflow-y: auto; overflow-x: hidden; border-radius: var(--radius); background: var(--surface); box-shadow: 0 24px 70px rgba(15, 22, 35, .28); position: relative; }
    .rk-modal-accent { height: 4px; background: linear-gradient(90deg, var(--accent), var(--accent2)); }

    /* ── Header modal ── */
    .rk-modal-head { display: flex; justify-content: space-between; gap: 14px; align-items: flex-start; padding: 20px 22px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2; }
    .rk-modal-head-main { display: flex; gap: 13px; align-items: flex-start; min-width: 0; }
    .rk-modal-icon { flex: none; width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe; display: flex; align-items: center; justify-content: center; font-size: 21px; }
    .rk-modal-eyebrow { font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--accent); margin-bottom: 3px; }
    .rk-modal-title { margin: 0; font-size: 21px; font-weight: 900; letter-spacing: -.4px; color: var(--text); line-height: 1.15; }
    .rk-modal-sub { margin-top: 9px; display: flex; flex-wrap: wrap; gap: 6px; }
    .rk-modal-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 7px; background: var(--surface2); border: 1px solid var(--border); font-size: 11px; font-weight: 700; color: var(--text); white-space: nowrap; }
    .rk-modal-close { flex: none; width: 30px; height: 30px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface2); color: var(--text); font-size: 18px; line-height: 1; cursor: pointer; transition: all .15s ease; }
    .rk-modal-close:hover { border-color: var(--accent); color: var(--accent); }
    .rk-modal-body { padding: 20px 22px 24px; }

    .rk-modal-section { margin-top: 22px; }
    .rk-modal-section:first-child { margin-top: 0; }
    .rk-modal-section h3 { margin: 0 0 11px; font-size: 11px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); display: flex; align-items: center; }
    .rk-count-badge { margin-left: 7px; padding: 1px 8px; border-radius: 20px; background: var(--surface2); border: 1px solid var(--border); color: var(--muted); font-size: 9px; font-weight: 700; text-transform: none; letter-spacing: 0; }

    /* Strip angka ringkas */
    .rk-stat-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .rk-stat { padding: 12px 6px; border-radius: var(--radius-sm); background: var(--surface2); text-align: center; }
    .rk-stat-num { display: block; font-size: 17px; font-weight: 800; color: var(--text); line-height: 1.2; }
    .rk-stat-label { display: block; margin-top: 3px; font-size: 9px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .rk-stat-pct { display: block; margin-top: 2px; font-size: 9.5px; font-weight: 700; color: var(--muted); }
    .rk-stat.plan .rk-stat-num { color: var(--accent); }
    .rk-stat.mati .rk-stat-num { color: var(--red); }
    .rk-stat.retur .rk-stat-num { color: var(--gold); }
    .rk-stat.diterima .rk-stat-num { color: var(--accent2); }

    /* Grid info (waktu, lokasi, size, ringkasan uniformity) */
    .rk-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .rk-info-tile { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); min-width: 0; }
    .rk-info-tile .tile-ico { flex: none; font-size: 17px; }
    .rk-info-tile .tile-label { display: block; font-size: 9px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); margin-bottom: 2px; }
    .rk-info-tile .tile-value { display: block; font-size: 12.5px; font-weight: 700; color: var(--text); overflow-wrap: anywhere; }

    .rk-uniformity-empty { padding: 20px; border: 1px dashed var(--border); border-radius: 10px; color: var(--muted); text-align: center; font-size: 12px; font-weight: 600; }
    .rk-empty-icon-sm { font-size: 26px; margin-bottom: 8px; }

    /* Sebaran (stacked bar + legend) */
    .rk-stacked-bar { display: flex; height: 14px; border-radius: 8px; overflow: hidden; background: #edf1f7; margin-bottom: 12px; }
    .rk-stacked-bar .seg { height: 100%; }
    .rk-stacked-bar .seg-below { background: var(--red); }
    .rk-stacked-bar .seg-in { background: var(--green); }
    .rk-stacked-bar .seg-above { background: var(--gold); }
    .rk-uniformity-legend { display: flex; flex-direction: column; gap: 8px; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: var(--text); }
    .legend-item strong { margin-left: auto; font-size: 11.5px; font-weight: 700; color: var(--muted); }
    .legend-item .dot { width: 9px; height: 9px; border-radius: 50%; flex: none; }
    .dot-below { background: var(--red); }
    .dot-in { background: var(--green); }
    .dot-above { background: var(--gold); }

    /* Berat sampling */
    .rk-weight-box { max-height: 150px; overflow-y: auto; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface2); padding: 10px; }
    .rk-weight-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(84px, 1fr)); gap: 6px; }
    .rk-weight { display: flex; flex-direction: column; align-items: center; gap: 2px; padding: 7px 4px; border-radius: 8px; background: var(--surface); border: 1px solid var(--border); font-size: 10.5px; font-weight: 700; color: var(--text); }
    .rk-weight .seq { font-size: 9px; font-weight: 700; color: var(--muted); }

    @media (max-width: 640px) {
        .rk-modal-head, .rk-modal-body { padding-left: 16px; padding-right: 16px; }
    }

    @media (max-width: 420px) {
        .rk-stat-strip { grid-template-columns: repeat(2, 1fr); }
        .rk-info-grid { grid-template-columns: 1fr; }
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

@php
    $f = $filter ?? ['mode'=>'single','date'=>$today,'from'=>$today,'to'=>$today];
    $totalTruk  = is_countable($rows ?? []) ? count($rows) : 0;
    $totalAyam  = array_sum(array_column($rows ?? [], 'ayam_diterima'));
    $totalMati  = array_sum(array_column($rows ?? [], 'ayam_mati'));
    $totalRetur = array_sum(array_column($rows ?? [], 'ayam_retur'));
@endphp

<div class="rk-wrap">

    {{-- Header --}}
    <div class="rk-header">
        <div class="rk-title">
            <span class="rk-eyebrow">Laporan</span>
            <h1 class="rk-h1">Detail Operasional</h1>
            <div class="rk-period">
                @if(($f['mode'] ?? 'single') === 'single')
                    {{ \Carbon\Carbon::parse($f['from'])->translatedFormat('d F Y') }}
                @elseif(($f['mode'] ?? 'single') === 'range')
                    {{ \Carbon\Carbon::parse($f['from'])->translatedFormat('d F Y') }} — {{ \Carbon\Carbon::parse($f['to'])->translatedFormat('d F Y') }}
                @else
                    7 Hari Terakhir
                @endif
            </div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a class="rk-back-btn" href="{{ route('dashboard') }}">← Kembali ke Dashboard</a>
            {{-- <a class="rk-back-btn"
            href="{{ route('dashboard.rekap.export.excel', request()->only(['mode','date','from','to'])) }}">
                Export Excel
            </a> --}}
            <a class="rk-back-btn"
            href="{{ route('dashboard.rekap.export.pdf', request()->only(['mode','date','from','to'])) }}">
                Export PDF
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="rk-summary">
        {{-- Card: Total Truk --}}
        <div class="rk-card rk-card-truk">
            <div class="rk-card-icon icon-truk">🚛</div>
            <div class="rk-card-label">Total Truk</div>
            <div class="rk-card-value val-truk">{{ number_format($totalTruk) }}</div>
            <div class="rk-card-sub">
                @if(($f['mode'] ?? 'single') === 'single')
                    Tanggal {{ \Carbon\Carbon::parse($f['from'])->translatedFormat('d F Y') }}
                @elseif(($f['mode'] ?? 'single') === 'range')
                    {{ \Carbon\Carbon::parse($f['from'])->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($f['to'])->translatedFormat('d M Y') }}
                @else
                    7 hari terakhir
                @endif
            </div>
            <div class="rk-card-deco">{{ number_format($totalTruk) }}</div>
        </div>

        {{-- Card: Total Ayam Diterima --}}
        <div class="rk-card rk-card-ayam">
            <div class="rk-card-icon icon-ayam">🐔</div>
            <div class="rk-card-label">Total Ayam Diterima</div>
            <div class="rk-card-value val-ayam">{{ number_format($totalAyam) }}</div>
            <div class="rk-card-sub">ekor</div>
            <div class="rk-card-deco">{{ number_format($totalAyam) }}</div>
        </div>

        {{-- Card: Jumlah Ayam Mati --}}
        <div class="rk-card rk-card-mati">
            <div class="rk-card-icon icon-mati">💀</div>
            <div class="rk-card-label">Jumlah Ayam Mati</div>
            <div class="rk-card-value val-mati">{{ number_format($totalMati) }}</div>
            <div class="rk-card-sub">ekor</div>
            <div class="rk-card-deco">{{ number_format($totalMati) }}</div>
        </div>

        {{-- Card: Jumlah Ayam Retur --}}
        <div class="rk-card rk-card-retur">
            <div class="rk-card-icon icon-retur">↩️</div>
            <div class="rk-card-label">Jumlah Ayam Retur</div>
            <div class="rk-card-value val-retur">{{ number_format($totalRetur) }}</div>
            <div class="rk-card-sub">ekor</div>
            <div class="rk-card-deco">{{ number_format($totalRetur) }}</div>
        </div>
    </div>

    {{-- Filter + Table Panel --}}
    <div class="rk-panel">
        <div class="rk-panel-head">
            <form method="GET" class="rk-filter">
                <div class="rk-field">
                    <label>Mode</label>
                    <select name="mode" id="mode">
                        <option value="single" {{ ($f['mode'] ?? '')==='single' ? 'selected' : '' }}>Pilih Tanggal</option>
                        <option value="last7"  {{ ($f['mode'] ?? '')==='last7'  ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="range"  {{ ($f['mode'] ?? '')==='range'  ? 'selected' : '' }}>Range Tanggal</option>
                    </select>
                </div>

                <div class="rk-field" id="field_single">
                    <label>Tanggal</label>
                    <input type="date" name="date" value="{{ $f['date'] ?? $today }}">
                </div>

                <div class="rk-field" id="field_from">
                    <label>Dari</label>
                    <input type="date" name="from" value="{{ $f['from'] ?? $today }}">
                </div>

                <div class="rk-field" id="field_to">
                    <label>Sampai</label>
                    <input type="date" name="to" value="{{ $f['to'] ?? $today }}">
                </div>

                <button class="rk-btn" type="submit">Terapkan Filter</button>
            </form>
        </div>

        <div class="rk-panel-body">
            <div class="rk-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px">No</th>
                            <th>No Polisi</th>
                            <th>No Truk</th>
                            <th>Jam Bongkar</th>
                            <th>Jam Selesai</th>
                            <th>Nama Farm</th>
                            <th>Size</th>
                            <th class="num">Ekor Plan</th>
                            <th class="num">Ayam Mati</th>
                            <th class="num">Ayam Retur</th>
                            <th class="num">Ayam Diterima</th>
                            {{-- <th class="num">Batch Jetson</th>
                            <th class="num">Selisih Jetson</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($rows ?? []) as $rowIndex => $r)
                            <tr class="rk-data-row" data-row-index="{{ $rowIndex }}" tabindex="0" role="button" aria-label="Lihat detail truk {{ $r['truck_no'] ?? $r['no'] }}">
                                <td><span class="rk-no">{{ $r['no'] }}</span></td>
                                <td><span class="rk-polisi">{{ $r['no_polisi'] ?? '—' }}</span></td>
                                <td><span class="rk-num">{{ $r['truck_no'] ?? '—' }}</span></td>
                                <td><span class="rk-time">{{ $r['jam_bongkar'] ?? '—' }}</span></td>
                                <td><span class="rk-time">{{ $r['jam_selesai'] ?? '—' }}</span></td>
                                <td style="color:var(--text)">{{ $r['nama_farm'] ?? '—' }}</td>
                                <td><span class="rk-size">{{ $r['size'] ?? '—' }}</span></td>
                                <td class="num"><span class="rk-num">{{ number_format((int)($r['total_ekor'] ?? 0)) }}</span></td>
                                <td class="num"><span class="rk-num num-mati">{{ number_format((int)($r['ayam_mati'] ?? 0)) }}</span></td>
                                <td class="num"><span class="rk-num num-retur">{{ number_format((int)($r['ayam_retur'] ?? 0)) }}</span></td>
                                <td class="num"><span class="num-diterima">{{ number_format((int)($r['ayam_diterima'] ?? 0)) }}</span></td>
                                {{-- <td class="num">
                                    @if(($r['lokasi'] ?? null) !== 'SH02')
                                        <span class="rk-num" style="color:var(--muted)">—</span>
                                    @elseif(is_null($r['jetson_batch_number'] ?? null))
                                        <span class="rk-num" style="color:var(--muted)" title="Batch Jetson tidak ditemukan / tidak tersedia">n/a</span>
                                    @else
                                        <span class="rk-num" title="Jumlah dari batch ini: {{ number_format($r['jetson_count']) }}">#{{ $r['jetson_batch_number'] }} ({{ number_format($r['jetson_count']) }})</span>
                                    @endif
                                </td>
                                <td class="num">
                                    @if(($r['lokasi'] ?? null) !== 'SH02' || is_null($r['jetson_selisih'] ?? null))
                                        <span class="rk-num" style="color:var(--muted)">—</span>
                                    @else
                                        @php($sel = $r['jetson_selisih'])
                                        <span class="rk-num" style="{{ $sel === 0 ? 'color:var(--green)' : ($sel > 0 ? 'color:var(--gold)' : 'color:var(--red)') }}">
                                            {{ $sel > 0 ? '+' : '' }}{{ number_format($sel) }}
                                        </span>
                                    @endif
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13">
                                    <div class="rk-empty">
                                        <div class="rk-empty-icon">📭</div>
                                        Tidak ada data.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rk-modal" id="rekapModal" role="dialog" aria-modal="true" aria-labelledby="rekapModalTitle">
        <div class="rk-modal-dialog">
            <div class="rk-modal-accent"></div>
            <div class="rk-modal-head">
                <div class="rk-modal-head-main">
                    <div class="rk-modal-icon">🚛</div>
                    <div style="min-width:0">
                        <div class="rk-modal-eyebrow" id="rekapModalEyebrow">Detail Truk</div>
                        <h2 class="rk-modal-title" id="rekapModalTitle">—</h2>
                        <div class="rk-modal-sub" id="rekapModalSub"></div>
                    </div>
                </div>
                <button class="rk-modal-close" type="button" id="rekapModalClose" aria-label="Tutup modal">&times;</button>
            </div>
            <div class="rk-modal-body" id="rekapModalBody"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = @json(array_values($rows ?? []));
    const modal = document.getElementById('rekapModal');
    const modalBody = document.getElementById('rekapModalBody');
    const modalEyebrow = document.getElementById('rekapModalEyebrow');
    const modalTitle = document.getElementById('rekapModalTitle');
    const modalSub = document.getElementById('rekapModalSub');
    const modalClose = document.getElementById('rekapModalClose');

    const escapeHtml = (value) => String(value ?? '-').replace(/[&<>'"]/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    }[char]));
    const formatNumber = (value, decimals = 0) => value === null || value === undefined || value === ''
        ? '-'
        : Number(value).toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });

    function openModal(row) {
        const uniformity = row.uniformity;
        const summary = uniformity && uniformity.summary;

        const plan = Number(row.total_ekor) || 0;
        const diterima = Number(row.ayam_diterima) || 0;
        const mati = Number(row.ayam_mati) || 0;
        const retur = Number(row.ayam_retur) || 0;
        const pctOf = (val) => plan > 0 ? `${((val / plan) * 100).toFixed(1)}%` : '-';

        modalEyebrow.textContent = `Detail Truk · #${row.truck_no || row.no || '-'}`;
        modalTitle.textContent = row.no_polisi || '—';
        modalSub.innerHTML = `
            <span class="rk-modal-chip">🏭 ${escapeHtml(row.nama_farm)}</span>
            <span class="rk-modal-chip">📅 ${escapeHtml(row.tanggal)}</span>
            ${row.size ? `<span class="rk-modal-chip">📦 Size ${escapeHtml(row.size)}</span>` : ''}
        `;

        let content = `
            <div class="rk-modal-section">
                <h3>🧮 Ringkasan Muatan</h3>
                <div class="rk-stat-strip">
                    <div class="rk-stat plan">
                        <span class="rk-stat-num">${formatNumber(plan)}</span>
                        <span class="rk-stat-label">Plan</span>
                    </div>
                    <div class="rk-stat diterima">
                        <span class="rk-stat-num">${formatNumber(diterima)}</span>
                        <span class="rk-stat-label">Diterima</span>
                        <span class="rk-stat-pct">${pctOf(diterima)}</span>
                    </div>
                    <div class="rk-stat mati">
                        <span class="rk-stat-num">${formatNumber(mati)}</span>
                        <span class="rk-stat-label">Mati</span>
                        <span class="rk-stat-pct">${pctOf(mati)}</span>
                    </div>
                    <div class="rk-stat retur">
                        <span class="rk-stat-num">${formatNumber(retur)}</span>
                        <span class="rk-stat-label">Retur</span>
                        <span class="rk-stat-pct">${pctOf(retur)}</span>
                    </div>
                </div>
            </div>
            <div class="rk-modal-section">
                <h3>🕓 Informasi Perjalanan</h3>
                <div class="rk-info-grid">
                    <div class="rk-info-tile"><span class="tile-ico">⏱️</span><div><span class="tile-label">Jam Bongkar</span><strong class="tile-value">${escapeHtml(row.jam_bongkar)}</strong></div></div>
                    <div class="rk-info-tile"><span class="tile-ico">✅</span><div><span class="tile-label">Jam Selesai</span><strong class="tile-value">${escapeHtml(row.jam_selesai)}</strong></div></div>
                    <div class="rk-info-tile"><span class="tile-ico">📍</span><div><span class="tile-label">Lokasi</span><strong class="tile-value">${escapeHtml(row.lokasi)}</strong></div></div>
                    <div class="rk-info-tile"><span class="tile-ico">📦</span><div><span class="tile-label">Size</span><strong class="tile-value">${escapeHtml(row.size)}</strong></div></div>
                </div>
            </div>
        `;

        if (!uniformity) {
            content += `
                <div class="rk-modal-section">
                    <h3>🔬 Uniformity</h3>
                    <div class="rk-uniformity-empty">
                        <div class="rk-empty-icon-sm">📊</div>
                        Belum ada laporan uniformity untuk truk ini.
                    </div>
                </div>
            `;
        } else {
            const below = Number(summary.below.pct) || 0;
            const inRange = Number(summary.in_range.pct) || 0;
            const above = Number(summary.above.pct) || 0;
            const weightsArr = uniformity.weights || [];
            const weights = weightsArr.map((weight) => `
                <div class="rk-weight">
                    <span class="seq">#${escapeHtml(weight.sequence)}</span>
                    <span>${formatNumber(weight.weight_kg, 3)} kg</span>
                </div>
            `).join('');

            content += `
                <div class="rk-modal-section">
                    <h3>🔬 Ringkasan Uniformity</h3>
                    <div class="rk-info-grid">
                        <div class="rk-info-tile"><span class="tile-ico">🧪</span><div><span class="tile-label">Jumlah Sampling</span><strong class="tile-value">${formatNumber(summary.count)} ekor</strong></div></div>
                        <div class="rk-info-tile"><span class="tile-ico">⚖️</span><div><span class="tile-label">Rata-rata Berat</span><strong class="tile-value">${formatNumber(summary.avg, 3)} kg</strong></div></div>
                        <div class="rk-info-tile"><span class="tile-ico">📏</span><div><span class="tile-label">Range Size</span><strong class="tile-value">${summary.range_low ?? '-'} - ${summary.range_high ?? '-'}</strong></div></div>
                        <div class="rk-info-tile"><span class="tile-ico">🎯</span><div><span class="tile-label">Rata-rata RPA</span><strong class="tile-value">${formatNumber(uniformity.avg_rpa)}</strong></div></div>
                    </div>
                </div>
                <div class="rk-modal-section">
                    <h3>📊 Sebaran Terhadap Range</h3>
                    <div class="rk-stacked-bar">
                        <div class="seg seg-below" style="width:${below}%" title="Undersize ${below}%"></div>
                        <div class="seg seg-in" style="width:${inRange}%" title="In Range ${inRange}%"></div>
                        <div class="seg seg-above" style="width:${above}%" title="Oversize ${above}%"></div>
                    </div>
                    <div class="rk-uniformity-legend">
                        <div class="legend-item"><span class="dot dot-below"></span>Undersize<strong>${formatNumber(summary.below.count)} · ${escapeHtml(summary.below.pct)}%</strong></div>
                        <div class="legend-item"><span class="dot dot-in"></span>In Range<strong>${formatNumber(summary.in_range.count)} · ${escapeHtml(summary.in_range.pct)}%</strong></div>
                        <div class="legend-item"><span class="dot dot-above"></span>Oversize<strong>${formatNumber(summary.above.count)} · ${escapeHtml(summary.above.pct)}%</strong></div>
                    </div>
                </div>
                <div class="rk-modal-section">
                    <h3>⚖️ Berat Sampling <span class="rk-count-badge">${weightsArr.length} sampel</span></h3>
                    <div class="rk-weight-box">
                        <div class="rk-weight-list">${weights || '<span style="color:var(--muted);font-size:12px;">Belum ada data.</span>'}</div>
                    </div>
                </div>
            `;
        }

        modalBody.innerHTML = content;
        modal.classList.add('is-open');
        modalClose.focus();
    }

    function closeModal() {
        modal.classList.remove('is-open');
    }

    document.querySelectorAll('.rk-data-row').forEach((row) => {
        const show = () => openModal(rows[Number(row.dataset.rowIndex)]);
        row.addEventListener('click', show);
        row.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                show();
            }
        });
    });
    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });

    const modeSel    = document.getElementById('mode');
    const fSingle    = document.getElementById('field_single');
    const fFrom      = document.getElementById('field_from');
    const fTo        = document.getElementById('field_to');

    function applyMode() {
        const m = modeSel ? modeSel.value : 'single';
        if (!fSingle || !fFrom || !fTo) return;
        fSingle.style.display = m === 'single' ? '' : 'none';
        fFrom.style.display   = m === 'range'  ? '' : 'none';
        fTo.style.display     = m === 'range'  ? '' : 'none';
    }

    if (modeSel) {
        modeSel.addEventListener('change', applyMode);
        applyMode();
    }
});
</script>
@endsection
