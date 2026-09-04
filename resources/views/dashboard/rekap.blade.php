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

    .rk-modal { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(15, 22, 35, .5); }
    .rk-modal.is-open { display: flex; }
    .rk-modal-dialog { width: min(820px, 100%); max-height: min(760px, 92vh); overflow-y: auto; border-radius: var(--radius); background: var(--surface); box-shadow: 0 24px 70px rgba(15, 22, 35, .25); }
    .rk-modal-head { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; padding: 22px 24px; border-bottom: 1px solid var(--border); }
    .rk-modal-title { margin: 0; font-size: 20px; font-weight: 800; }
    .rk-modal-sub { margin-top: 5px; color: var(--muted); font-size: 12px; font-weight: 600; }
    .rk-modal-close { width: 34px; height: 34px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface2); color: var(--text); font-size: 20px; line-height: 1; cursor: pointer; }
    .rk-modal-body { padding: 22px 24px 26px; }
    .rk-detail-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
    .rk-detail-item { padding: 12px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface2); }
    .rk-detail-label { display: block; margin-bottom: 5px; color: var(--muted); font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .rk-detail-value { font-size: 14px; font-weight: 800; }
    .rk-modal-section { margin-top: 22px; }
    .rk-modal-section h3 { margin: 0 0 12px; font-size: 13px; }
    .rk-uniformity-empty { padding: 20px; border: 1px dashed var(--border); border-radius: 10px; color: var(--muted); text-align: center; font-size: 12px; font-weight: 600; }
    .rk-uniformity-bars { display: grid; gap: 9px; }
    .rk-uniformity-row { display: grid; grid-template-columns: 120px 1fr 110px; align-items: center; gap: 10px; font-size: 11px; font-weight: 700; }
    .rk-bar-track { height: 9px; overflow: hidden; border-radius: 10px; background: #edf1f7; }
    .rk-bar-fill { height: 100%; border-radius: inherit; }
    .rk-bar-below { background: var(--red); }
    .rk-bar-in { background: var(--green); }
    .rk-bar-above { background: var(--gold); }
    .rk-weight-list { display: flex; flex-wrap: wrap; gap: 7px; }
    .rk-weight { padding: 5px 8px; border-radius: 6px; background: var(--surface2); border: 1px solid var(--border); font-size: 11px; font-weight: 700; }
    @media (max-width: 640px) {
        .rk-detail-grid { grid-template-columns: repeat(2, 1fr); }
        .rk-modal-head, .rk-modal-body { padding-left: 16px; padding-right: 16px; }
        .rk-uniformity-row { grid-template-columns: 90px 1fr; }
        .rk-uniformity-row strong { grid-column: 2; text-align: right; }
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
            <div class="rk-modal-head">
                <div>
                    <h2 class="rk-modal-title" id="rekapModalTitle">Detail Truk</h2>
                    <div class="rk-modal-sub" id="rekapModalSub"></div>
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
    const modalTitle = document.getElementById('rekapModalTitle');
    const modalSub = document.getElementById('rekapModalSub');
    const modalClose = document.getElementById('rekapModalClose');

    const escapeHtml = (value) => String(value ?? '-').replace(/[&<>'"]/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    }[char]));
    const formatNumber = (value, decimals = 0) => value === null || value === undefined || value === ''
        ? '-'
        : Number(value).toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    const detail = (label, value) => `<div class="rk-detail-item"><span class="rk-detail-label">${escapeHtml(label)}</span><span class="rk-detail-value">${escapeHtml(value)}</span></div>`;

    function openModal(row) {
        const uniformity = row.uniformity;
        const summary = uniformity && uniformity.summary;
        modalTitle.textContent = `Detail Truk ${row.truck_no || row.no || ''}`;
        modalSub.textContent = `${row.nama_farm || '-'} · ${row.no_polisi || '-'} · ${row.tanggal || '-'}`;

        let content = `<div class="rk-detail-grid">
            ${detail('No. Polisi', row.no_polisi)}
            ${detail('No. Truk', row.truck_no)}
            ${detail('Jam Bongkar', row.jam_bongkar)}
            ${detail('Jam Selesai', row.jam_selesai)}
            ${detail('Nama Farm', row.nama_farm)}
            ${detail('Lokasi', row.lokasi)}
            ${detail('Size', row.size)}
            ${detail('Ekor Plan', formatNumber(row.total_ekor))}
            ${detail('Ayam Mati', formatNumber(row.ayam_mati))}
            ${detail('Ayam Retur', formatNumber(row.ayam_retur))}
            ${detail('Ayam Diterima', formatNumber(row.ayam_diterima))}
            ${detail('Selisih Target', formatNumber(row.selisih))}
        </div>`;

        if (!uniformity) {
            content += `<div class="rk-modal-section"><div class="rk-uniformity-empty">Belum ada laporan uniformity untuk truk ini.</div></div>`;
        } else {
            const categories = [
                ['Undersize', summary.below, 'rk-bar-below'],
                ['In Range', summary.in_range, 'rk-bar-in'],
                ['Oversize', summary.above, 'rk-bar-above']
            ];
            const bars = categories.map(([label, item, color]) => `<div class="rk-uniformity-row">
                <span>${label}</span><div class="rk-bar-track"><div class="rk-bar-fill ${color}" style="width:${Number(item.pct) || 0}%"></div></div>
                <strong>${formatNumber(item.count)} ekor · ${escapeHtml(item.pct)}%</strong>
            </div>`).join('');
            const weights = (uniformity.weights || []).map((weight) => `<span class="rk-weight">#${escapeHtml(weight.sequence)} · ${formatNumber(weight.weight_kg, 3)} kg</span>`).join('');
            content += `<div class="rk-modal-section"><h3>Ringkasan Uniformity</h3>
                <div class="rk-detail-grid">
                    ${detail('Jumlah Sampling', `${formatNumber(summary.count)} ekor`)}
                    ${detail('Total Berat', `${formatNumber(summary.total, 3)} kg`)}
                    ${detail('Berat Terkecil', formatNumber(summary.min, 3))}
                    ${detail('Berat Terbesar', formatNumber(summary.max, 3))}
                    ${detail('Rata-rata Berat', formatNumber(summary.avg, 3))}
                    ${detail('Range Size', `${summary.range_low ?? '-'} - ${summary.range_high ?? '-'}`)}
                    ${detail('Rata-rata RPA', uniformity.avg_rpa)}
                    ${detail('Berat RPA', uniformity.berat_rpa)}
                </div>
            </div><div class="rk-modal-section"><h3>Sebaran Terhadap Range</h3><div class="rk-uniformity-bars">${bars}</div></div>
            <div class="rk-modal-section"><h3>Berat Sampling</h3><div class="rk-weight-list">${weights || '<span class="rk-uniformity-empty">Belum ada data berat sampling.</span>'}</div></div>`;
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