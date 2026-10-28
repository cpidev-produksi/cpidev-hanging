@extends('layouts.app')

@section('content')
@php
    $badge = fn ($status) => match ($status) {
        'approved' => ['Disetujui', 'approved'],
        'known'    => ['Diketahui', 'known'],
        default    => ['Draft', 'draft'],
    };
    $f = fn ($v, $d = 1) => $v === null || $v === '' ? '–' : number_format((float) $v, $d, ',', '');
    $pct = fn ($v) => max(0, min(100, (float) ($v ?? 0)));
@endphp

<style>
    .ef, dialog.nr { --ink:var(--text-main,#0d1117); --mut:var(--text-muted,#6b7896); --line:var(--card-border,#e4e8f0); --acc:var(--accent,#e85d2f); --soft:#f6f8fc; --hover:#fafbfe; }
    .ef { max-width:1440px; margin:0 auto; font-family:'Inter',system-ui,sans-serif; color:var(--ink); }

    /* ===== Header halaman ===== */
    .ef-head { display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:14px 24px; margin-bottom:22px; }
    .ef-title { font-size:26px; font-weight:800; letter-spacing:-.025em; line-height:1.15; }
    .ef-desc { margin-top:6px; max-width:56ch; font-size:14px; line-height:1.5; color:var(--mut); }

    /* ===== Tombol & input ===== */
    .df-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:9px 15px; border-radius:10px; font:600 13px/1.2 'Inter',sans-serif; border:1px solid var(--line); background:#fff; color:var(--ink); cursor:pointer; text-decoration:none; transition:background-color .15s, border-color .15s, color .15s; }
    .df-btn:hover { background:var(--soft); border-color:#cfd6e4; }
    .df-btn.primary { background:var(--acc); border-color:var(--acc); color:#fff; box-shadow:0 1px 2px rgba(232,93,47,.3); }
    .df-btn.primary:hover { background:#d44f24; border-color:#d44f24; }
    .df-btn.danger { color:#b91c1c; border-color:#f3c6c6; }
    .df-btn.danger:hover { background:#fef2f2; border-color:#ecaaaa; }
    .df-btn.sm { padding:6px 10px; font-size:12px; border-radius:8px; }
    .df-btn:focus-visible { outline:2px solid var(--acc); outline-offset:2px; }
    .df-btn svg { width:15px; height:15px; flex:none; }

    .f-label { display:block; margin-bottom:6px; font-size:12px; font-weight:600; color:var(--mut); }
    .f-in { width:100%; min-height:40px; padding:9px 12px; border:1px solid var(--line); border-radius:10px; font:500 13.5px 'Inter',sans-serif; background:#fff; color:var(--ink); transition:border-color .15s, box-shadow .15s; }
    .f-in:focus { outline:none; border-color:var(--acc); box-shadow:0 0 0 3px rgba(232,93,47,.15); }

    /* ===== Filter ===== */
    .df-filter { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)) auto; gap:12px; align-items:end; padding:14px 16px; margin-bottom:26px; background:#fff; border:1px solid var(--line); border-radius:16px; }
    .df-filter .df-btn { min-height:40px; }
    @media (max-width:980px) { .df-filter { grid-template-columns:repeat(2, minmax(0,1fr)); } .df-filter .df-btn { grid-column:1 / -1; } }
    @media (max-width:480px) { .df-filter { grid-template-columns:1fr; } }

    .sec-h { display:flex; align-items:baseline; gap:10px; margin:26px 0 12px; font-size:16px; font-weight:700; letter-spacing:-.01em; }
    .sec-h.s { margin:20px 0 10px; font-size:13.5px; color:var(--mut); font-weight:600; }
    .sec-h .hint { font-size:12.5px; font-weight:500; color:var(--mut); }
    .ef-body > .df-filter + .sec-h { margin-top:0; }
    .note { margin:12px 0 0; font-size:12.5px; color:var(--mut); }
    .empty { padding:44px 16px; text-align:center; font-size:14px; color:var(--mut); background:#fff; border:1px dashed #cfd6e4; border-radius:16px; }

    /* ===== Status ===== */
    .dt-status, .chip { display:inline-flex; align-items:center; gap:6px; border-radius:999px; border:1px solid; font-weight:600; white-space:nowrap; }
    .dt-status { padding:3px 10px 3px 8px; font-size:12px; }
    .dt-status::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .chip { padding:2px 9px; font-size:11.5px; gap:4px; }
    .chip b { font-weight:800; }
    .dt-status.draft, .chip.draft { background:#FEF9C3; color:#92400E; border-color:#FDE68A; }
    .dt-status.known, .chip.known { background:#e0f2fe; color:#0369a1; border-color:#bae6fd; }
    .dt-status.approved, .chip.approved { background:rgba(16,185,129,.10); color:#047857; border-color:rgba(16,185,129,.25); }

    /* ===== KPI ===== */
    .kpi .lbl { font-size:12.5px; font-weight:600; color:var(--mut); }
    .kpi .val { margin-top:8px; font-size:34px; font-weight:800; letter-spacing:-.03em; line-height:1; font-variant-numeric:tabular-nums; }
    .kpi .val .u { margin-left:3px; font-size:15px; font-weight:600; letter-spacing:0; color:var(--mut); }
    .kpi .sub { margin-top:7px; font-size:12px; color:var(--mut); }
    .chips { display:flex; flex-wrap:wrap; gap:5px; margin-top:10px; }

    .kpi-hero { display:grid; grid-template-columns:repeat(auto-fit, minmax(230px,1fr)); gap:14px; margin-bottom:14px; }
    .kpi-hero .kpi { padding:18px 20px 18px; background:#fff; border:1px solid var(--line); border-radius:16px; }
    .kpi.has-meter .lbl { display:flex; align-items:center; gap:8px; }
    .kpi.has-meter .lbl::before { content:''; width:8px; height:8px; border-radius:50%; background:var(--mc, var(--acc)); }
    .meter { height:6px; margin-top:14px; border-radius:99px; background:#eef1f6; overflow:hidden; }
    .meter i { display:block; height:100%; border-radius:inherit; background:var(--mc, var(--acc)); }

    .stat-strip { display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); margin-bottom:14px; background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .stat-strip .kpi { padding:16px 18px; margin:0 -1px -1px 0; border-right:1px solid var(--line); border-bottom:1px solid var(--line); }
    .stat-strip .kpi .val { margin-top:6px; font-size:24px; }
    .stat-strip .kpi .val.sm { font-size:19px; }

    /* ===== Grafik ===== */
    .chart-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:14px; margin-top:26px; }
    .chart-box { padding:16px 18px 12px; background:#fff; border:1px solid var(--line); border-radius:16px; }
    .chart-box h4 { margin-bottom:12px; font-size:13.5px; font-weight:700; }
    .chart-box .cv { position:relative; height:270px; }
    @media (max-width:900px) { .chart-grid { grid-template-columns:1fr; } .chart-box .cv { height:230px; } }

    /* ===== Tabel rekap ===== */
    .tbl-card { background:#fff; border:1px solid var(--line); border-radius:16px; overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table.df-rekap { width:max-content; min-width:100%; border-collapse:separate; border-spacing:0; font-size:13px; font-variant-numeric:tabular-nums; }
    table.df-rekap th, table.df-rekap td { padding:11px 14px; border-bottom:1px solid var(--line); white-space:nowrap; text-align:right; }
    table.df-rekap thead th { background:var(--soft); font-size:12px; font-weight:600; color:var(--mut); }
    table.df-rekap td.l, table.df-rekap th.l { position:sticky; left:0; z-index:1; text-align:left; box-shadow:1px 0 0 var(--line); }
    table.df-rekap td.l { background:#fff; font-weight:600; }
    table.df-rekap thead th.l { background:var(--soft); }
    table.df-rekap tbody tr:last-child td { border-bottom:0; }
    table.df-rekap tbody tr:hover td, table.df-rekap tbody tr:hover td.l { background:var(--hover); }

    /* ===== Daftar laporan ===== */
    .report-list-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow-x:auto; }
    table.report-list { width:100%; border-collapse:collapse; font-size:13.5px; }
    table.report-list th, table.report-list td { padding:12px 16px; border-bottom:1px solid var(--line); text-align:left; white-space:nowrap; }
    table.report-list th { background:var(--soft); font-size:12px; font-weight:600; color:var(--mut); }
    table.report-list tbody tr:last-child td { border-bottom:0; }
    table.report-list tbody tr[data-href] { cursor:pointer; }
    table.report-list tbody tr[data-href]:hover td { background:var(--hover); }
    table.report-list tbody tr[data-href]:focus-visible { outline:2px solid var(--acc); outline-offset:-2px; }
    .report-actions { display:flex; gap:6px; flex-wrap:wrap; }
    .report-actions form { margin:0; }

    @media (max-width:720px) {
        table.report-list, table.report-list tbody, table.report-list tr, table.report-list td { display:block; width:100%; }
        table.report-list thead { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0 0 0 0); }
        table.report-list tbody tr { padding:12px 16px; border-bottom:1px solid var(--line); }
        table.report-list tbody tr:last-child { border-bottom:0; }
        table.report-list td { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:6px 0; border:0; white-space:normal; }
        table.report-list td::before { content:attr(data-label); font-size:12px; font-weight:600; color:var(--mut); }
        table.report-list td.act { display:block; padding-top:10px; }
        table.report-list td.act::before { content:none; }
        table.report-list tbody tr[data-href]:hover td { background:transparent; }
    }

    /* ===== Dialog buat laporan ===== */
    dialog.nr { position:fixed; inset:50% auto auto 50%; transform:translate(-50%, -50%); margin:0; border:none; border-radius:18px; padding:0; width:min(460px, 92vw); max-height:90vh; overflow:auto; font-family:'Inter',system-ui,sans-serif; color:var(--ink); box-shadow:0 24px 60px rgba(15,23,42,.28); }
    dialog.nr::backdrop { background:rgba(13,17,23,.5); backdrop-filter:blur(3px); }
    dialog.nr[open] { animation:nr-in .18s ease-out; }
    @keyframes nr-in { from { opacity:0; transform:translate(-50%, -48%) scale(.98); } }
    .nr-body { padding:20px; }
    .nr-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:18px; }
    .nr-title { font-size:17px; font-weight:700; letter-spacing:-.01em; }
    .nr-sub { margin-top:4px; font-size:13px; line-height:1.5; color:var(--mut); }
    .nr-x { flex:none; display:grid; place-items:center; width:32px; height:32px; margin:-4px -6px 0 0; border:0; border-radius:8px; background:transparent; color:var(--mut); cursor:pointer; }
    .nr-x:hover { background:var(--soft); color:var(--ink); }
    .nr-x:focus-visible { outline:2px solid var(--acc); outline-offset:1px; }
    .nr-x svg { width:16px; height:16px; }
    .nr-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .nr-grid > div:first-child { grid-column:1 / -1; }
    @media (max-width:420px) { .nr-grid { grid-template-columns:1fr; } }
    .nr-foot { display:flex; justify-content:flex-end; gap:8px; margin-top:22px; padding-top:16px; border-top:1px solid var(--line); }

    /* ===== Detail laporan ===== */
    .dt-back { display:inline-flex; align-items:center; gap:4px; margin-bottom:16px; font-size:13px; font-weight:600; color:var(--mut); text-decoration:none; }
    .dt-back svg { width:16px; height:16px; transition:transform .15s; }
    .dt-back:hover { color:var(--acc); }
    .dt-back:hover svg { transform:translateX(-3px); }
    .dt-back:focus-visible { outline:2px solid var(--acc); outline-offset:3px; border-radius:4px; }

    .dt-card.rp-card { --st:#d97706; position:relative; margin:0 0 22px; padding:0; background:#fff; border:1px solid var(--line); border-radius:16px; box-shadow:0 1px 2px rgba(15,23,42,.04), 0 12px 30px -16px rgba(15,23,42,.14); scroll-margin-top:90px; }
    .dt-card.is-known { --st:#0284c7; }
    .dt-card.is-approved { --st:#059669; }
    .dt-card::before { content:''; position:absolute; inset:0 auto 0 0; width:4px; background:var(--st); border-radius:16px 0 0 16px; }

    .dt-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px 24px; padding:22px 24px 20px 28px; }
    .dt-title-row { display:flex; align-items:center; flex-wrap:wrap; gap:6px 12px; }
    .dt-title { font-size:22px; font-weight:800; letter-spacing:-.02em; line-height:1.25; }
    .dt-actions { display:flex; flex-wrap:wrap; gap:8px; }

    .dt-facts { display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:14px 28px; padding:16px 24px 18px 28px; background:var(--soft); border-top:1px solid var(--line); }
    .dt-fact dt { margin-bottom:3px; font-size:12px; font-weight:500; color:var(--mut); }
    .dt-fact dd { display:flex; align-items:center; gap:6px; font-size:14.5px; font-weight:700; }
    .dt-fact dd svg { width:15px; height:15px; flex:none; color:var(--mut); }

    .dt-section-head { padding:16px 24px 12px 28px; border-top:1px solid var(--line); }
    .dt-section-head h3 { font-size:14px; font-weight:700; }
    .dt-table-wrap { overflow-x:auto; padding:0 14px 14px 18px; }

    @media (max-width:640px) {
        .ef-title { font-size:21px; }
        .ef-head .df-btn { width:100%; }
        .kpi .val { font-size:28px; }
        .dt-head { padding:18px 16px 16px 20px; }
        .dt-title { font-size:19px; }
        .dt-actions { width:100%; }
        .dt-actions .df-btn { flex:1 1 auto; }
        .dt-facts { grid-template-columns:repeat(2, minmax(0, 1fr)); padding:14px 16px 16px 20px; }
        .dt-section-head { padding:14px 16px 10px 20px; }
        .dt-table-wrap { padding:0 8px 12px 12px; }
    }
    @media (prefers-reduced-motion:reduce) {
        .df-btn, .dt-back svg, .f-in { transition:none; }
        .dt-back:hover svg { transform:none; }
        dialog.nr[open] { animation:none; }
    }
</style>

<div class="ef df-wrap">
    <header class="ef-head">
        <div>
            <h1 class="ef-title">Efficiency Evisceration Report</h1>
            <p class="ef-desc">Pantau vent, jeroan, dan temuan grit per farm, shift, dan lokasi.</p>
        </div>
        <button type="button" class="df-btn primary" onclick="document.getElementById('newReport').showModal()"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M10 4v12M4 10h12"/></svg> Buat laporan</button>
    </header>

    <div class="ef-body">

        @unless($detailMode)
            {{-- Filter --}}
            <form method="GET" class="df-filter" id="reportFilterForm">
                <div><label class="f-label" for="fDate">Tanggal</label><input class="f-in" type="date" id="fDate" name="date" value="{{ $filters['date'] }}"></div>
                <div><label class="f-label" for="fLoc">Lokasi</label>
                    <select class="f-in" id="fLoc" name="location">
                        <option value="">Semua</option>
                        @foreach(['SH01','SH02'] as $l)<option value="{{ $l }}" @selected($filters['location'] === $l)>{{ $l }}</option>@endforeach
                    </select></div>
                <div><label class="f-label" for="fShift">Shift</label>
                    <select class="f-in" id="fShift" name="shift">
                        <option value="">Semua</option>
                        <option value="pagi" @selected($filters['shift'] === 'pagi')>Pagi</option>
                        <option value="malam" @selected($filters['shift'] === 'malam')>Malam</option>
                    </select></div>
                <div>
                    <label class="f-label" for="farmFilter">Cari Nama Farm</label>
                    <input class="f-in" type="search" id="farmFilter" name="farm_name" list="farmOptions"
                           value="{{ $filters['farm_name'] }}" placeholder="Ketik atau pilih farm">
                    <datalist id="farmOptions">
                        @foreach($farmOptions as $farmName)
                            <option value="{{ $farmName }}">
                        @endforeach
                    </datalist>
                </div>
                <a href="{{ route('evisceration.efficiency.index') }}" class="df-btn">Reset</a>
            </form>
            <script>
                document.getElementById('reportFilterForm').addEventListener('change', event => {
                    if (event.target.matches('input[name="date"], input[name="farm_name"], select[name="location"], select[name="shift"]')) {
                        event.currentTarget.requestSubmit();
                    }
                });
            </script>
        @endunless

        @if($detailMode)
            {{-- ===================== DETAIL LAPORAN ===================== --}}
            <a class="dt-back" href="{{ route('evisceration.efficiency.index', ['date' => $filters['date'], 'location' => $filters['location'], 'shift' => $filters['shift'], 'farm_name' => $filters['farm_name']]) }}">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5l-5 5 5 5"/></svg>
                Kembali ke daftar laporan
            </a>

            @if($reports->isEmpty())
                <div class="empty">Laporan tidak ditemukan. Mungkin sudah dihapus. Kembali ke daftar untuk memilih laporan lain.</div>
            @else
                @foreach($reports as $report)
                    @php [$bl, $bc] = $badge($report->status); @endphp
                    <article class="dt-card rp-card is-{{ $bc }}" id="report-{{ $report->id }}">
                        <header class="dt-head">
                            <div class="dt-title-row">
                                <h2 class="dt-title">{{ $report->report_date->locale('id')->translatedFormat('l, d F Y') }}</h2>
                                <span class="dt-status {{ $bc }}">{{ $bl }}</span>
                            </div>
                            <div class="dt-actions">
                                @if($report->canManageItems())
                                    <a class="df-btn primary sm" href="{{ route('evisceration.efficiency.items.create', $report) }}">+ Tambah Farm</a>
                                @endif
                                <a class="df-btn sm" href="{{ route('evisceration.efficiency.show', $report) }}">Detail &amp; Pengesahan</a>
                                <a class="df-btn sm" href="{{ route('evisceration.efficiency.pdf', $report) }}">
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 3v9m0 0L6.5 8.5M10 12l3.5-3.5M4 16h12"/></svg>
                                    Unduh PDF
                                </a>
                            </div>
                        </header>

                        <dl class="dt-facts">
                            <div class="dt-fact"><dt>Lokasi</dt><dd>{{ $report->location }}</dd></div>
                            <div class="dt-fact"><dt>Shift</dt>
                                <dd>
                                    @if($report->shift === 'malam')
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16.5 11.6A6.6 6.6 0 0 1 8.4 3.5a6.6 6.6 0 1 0 8.1 8.1z"/></svg>
                                    @else
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="10" cy="10" r="3.2"/><path d="M10 2.5v1.8M10 15.7v1.8M2.5 10h1.8M15.7 10h1.8M4.7 4.7L6 6M14 14l1.3 1.3M15.3 4.7L14 6M6 14l-1.3 1.3"/></svg>
                                    @endif
                                    {{ ucfirst($report->shift) }}
                                </dd>
                            </div>
                            <div class="dt-fact"><dt>Jumlah farm</dt><dd>{{ $report->items->count() }}</dd></div>
                            <div class="dt-fact"><dt>Dibuat oleh</dt><dd>{{ $report->checker_name ?: ($report->creator?->name ?? '–') }}</dd></div>
                        </dl>

                        <div class="dt-section-head"><h3>Data per farm</h3></div>
                        <div class="dt-table-wrap">
                            @include('report.evis.effieciency.partials.table', ['report' => $report, 'actions' => true])
                        </div>
                    </article>
                @endforeach
            @endif
        @else
            {{-- ===================== DASHBOARD SUMMARY ===================== --}}
            <h2 class="sec-h">Ringkasan dashboard</h2>
            @php $k = $dash['kpi']; @endphp
            @if($k['batches'] === 0)
                <div class="empty">Belum ada data pada filter ini.</div>
            @else
                <div class="kpi-hero">
                    <div class="kpi has-meter" style="--mc:#e85d2f">
                        <div class="lbl">Vent tidak sobek</div>
                        <div class="val">{{ $f($k['vent'], 1) }}<span class="u">%</span></div>
                        <div class="sub">rata-rata dari sampling</div>
                        <div class="meter" role="img" aria-label="Vent tidak sobek {{ $f($k['vent'], 1) }} persen"><i style="width:{{ $pct($k['vent']) }}%"></i></div>
                    </div>
                    <div class="kpi has-meter" style="--mc:#0284c7">
                        <div class="lbl">Jeroan tidak terambil</div>
                        <div class="val">{{ $f($k['viscera'], 1) }}<span class="u">%</span></div>
                        <div class="sub">rata-rata dari sampling</div>
                        <div class="meter" role="img" aria-label="Jeroan tidak terambil {{ $f($k['viscera'], 1) }} persen"><i style="width:{{ $pct($k['viscera']) }}%"></i></div>
                    </div>
                </div>

                <div class="stat-strip">
                    <div class="kpi"><div class="lbl">Laporan</div><div class="val">{{ $k['reports'] }}</div>
                        <div class="chips">
                            <span class="chip draft"><b>{{ $k['draft'] }}</b> draft</span>
                            <span class="chip known"><b>{{ $k['known'] }}</b> diketahui</span>
                            <span class="chip approved"><b>{{ $k['approved'] }}</b> disetujui</span>
                        </div></div>
                    <div class="kpi"><div class="lbl">Farm tercatat</div><div class="val">{{ $k['batches'] }}</div><div class="sub">batch</div></div>
                    <div class="kpi"><div class="lbl">Speed line evis</div><div class="val">{{ $f($k['speed_evis'], 0) }}</div><div class="sub">rata-rata BPH</div></div>
                    <div class="kpi"><div class="lbl">Jumlah sampling</div><div class="val">{{ number_format($k['sampling_total'], 0, ',', '') }}</div><div class="sub">total ekor</div></div>
                    <div class="kpi"><div class="lbl">Temuan grit</div><div class="val">{{ number_format($k['grit_total'], 0, ',', '') }}</div><div class="sub">total ekor</div></div>
                </div>

                <div class="sec-h s">Sprayer dan debit air</div>
                <div class="stat-strip">
                    <div class="kpi"><div class="lbl">Sprayer</div><div class="val sm">{{ $k['sprayer_on'] }} dari {{ $k['batches'] }} farm</div><div class="sub">beroperasi</div></div>
                    <div class="kpi"><div class="lbl">Debit air awal</div><div class="val sm">{{ $f($k['water_start'], 2) }}</div><div class="sub">rata-rata</div></div>
                    <div class="kpi"><div class="lbl">Debit air akhir</div><div class="val sm">{{ $f($k['water_end'], 2) }}</div><div class="sub">rata-rata</div></div>
                </div>

                <div class="chart-grid">
                    <div class="chart-box"><h4>Persentase per Tanggal (%)</h4><div class="cv"><canvas id="chPct"></canvas></div></div>
                    <div class="chart-box"><h4>Temuan Grit per Tanggal (ekor)</h4><div class="cv"><canvas id="chGrit"></canvas></div></div>
                </div>

                <div class="sec-h">Rekap per Farm</div>
                <div class="tbl-card">
                    <table class="df-rekap">
                        <thead>
                            <tr>
                                <th class="l">Nama Farm</th><th>Batch</th><th>Speed Evis (BPH)</th><th>Jumlah Sampling</th>
                                <th>Vent Tidak Sobek %</th><th>Jeroan Tidak Terambil %</th><th>Temuan Grit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dash['byFarm'] as $r)
                                <tr>
                                    <td class="l">{{ $r['farm'] }}</td>
                                    <td>{{ $r['batches'] }}</td>
                                    <td>{{ $f($r['speed_evis'], 0) }}</td>
                                    <td>{{ number_format($r['sampling'], 0, ',', '') }}</td>
                                    <td>{{ $f($r['vent'], 1) }}</td>
                                    <td>{{ $f($r['viscera'], 1) }}</td>
                                    <td>{{ number_format($r['grit'], 0, ',', '') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="note">Rata-rata dihitung dari seluruh farm pada filter yang dipilih (setiap farm berbobot sama).</p>
            @endif
        @endif

        @unless($detailMode)
            <section class="report-list-section">
                <h2 class="sec-h">Daftar Laporan</h2>
                @if($reports->isEmpty())
                    <div class="empty">Belum ada laporan pada filter ini. Klik <strong>Buat laporan</strong> untuk memulai.</div>
                @else
                    <div class="report-list-wrap">
                        <table class="report-list">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Shift</th>
                                    <th>Jumlah Farm</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    @php
                                        [$bl, $bc] = $badge($report->status);
                                        $rowUrl = route('evisceration.efficiency.index', [
                                            'report_id' => $report->id,
                                            'date' => $report->report_date->toDateString(),
                                            'location' => $report->location,
                                            'shift' => $report->shift,
                                            'farm_name' => $filters['farm_name'],
                                        ]) . '#report-' . $report->id;
                                    @endphp
                                    <tr data-href="{{ $rowUrl }}" role="link" tabindex="0" aria-label="Buka laporan {{ $report->report_date->format('d/m/Y') }} {{ $report->location }} shift {{ ucfirst($report->shift) }}">
                                        <td data-label="Tanggal">{{ $report->report_date->format('d/m/Y') }}</td>
                                        <td data-label="Lokasi">{{ $report->location }}</td>
                                        <td data-label="Shift">{{ ucfirst($report->shift) }}</td>
                                        <td data-label="Jumlah farm">{{ $report->items_count }}</td>
                                        <td data-label="Status"><span class="dt-status {{ $bc }}">{{ $bl }}</span></td>
                                        <td class="act">
                                            <div class="report-actions">
                                                <a class="df-btn sm" href="{{ route('evisceration.efficiency.show', $report) }}#report-items">Show / Edit</a>
                                                <a class="df-btn sm" href="{{ route('evisceration.efficiency.show', $report) }}#approval">Pengesahan</a>
                                                <a class="df-btn sm" href="{{ route('evisceration.efficiency.pdf', $report) }}">PDF</a>
                                                @if($report->canBeDeleted())
                                                    <form method="POST" action="{{ route('evisceration.efficiency.destroy', $report) }}"
                                                          onsubmit="return confirm('Hapus laporan ini beserta seluruh farm?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="df-btn danger sm">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:14px;">{{ $reports->links() }}</div>
                @endif
            </section>
        @endunless
    </div>
</div>

@unless($detailMode)
    <script>
        document.querySelectorAll('tr[data-href]').forEach(row => {
            row.addEventListener('click', event => {
                if (event.target.closest('a, button, form')) return;
                window.location.href = row.dataset.href;
            });
            row.addEventListener('keydown', event => {
                if ((event.key === 'Enter' || event.key === ' ') && event.target === row) {
                    event.preventDefault();
                    window.location.href = row.dataset.href;
                }
            });
        });
    </script>
@endunless

{{-- Dialog: buat laporan baru (satu laporan per tanggal operasional + shift + lokasi) --}}
<dialog class="nr" id="newReport" onclick="if (event.target === this) this.close()">
    <form method="POST" action="{{ route('evisceration.efficiency.store') }}" autocomplete="off">
        @csrf
        <div class="nr-body">
            <div class="nr-head">
                <div>
                    <div class="nr-title">Buat Laporan Baru</div>
                    <div class="nr-sub">Satu laporan untuk satu tanggal operasional, shift, dan lokasi. Farm ditambahkan dari detail laporan.</div>
                </div>
                <button type="button" class="nr-x" aria-label="Tutup" onclick="document.getElementById('newReport').close()"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M5 5l10 10M15 5L5 15"/></svg></button>
            </div>
            <div class="nr-grid">
                <div><label class="f-label" for="nr_date">Tanggal Operasional</label>
                    <input class="f-in" type="date" id="nr_date" name="report_date" value="{{ old('report_date', $defaults['report_date']) }}" required></div>
                <div><label class="f-label" for="nr_shift">Shift</label>
                    <select class="f-in" id="nr_shift" name="shift" required>
                        <option value="pagi" @selected(old('shift', $defaults['shift']) === 'pagi')>Pagi</option>
                        <option value="malam" @selected(old('shift', $defaults['shift']) === 'malam')>Malam</option>
                    </select></div>
                <div><label class="f-label" for="nr_loc">Lokasi</label>
                    <select class="f-in" id="nr_loc" name="location" required>
                        @foreach(['SH01','SH02'] as $l)<option value="{{ $l }}" @selected(old('location', $defaults['location']) === $l)>{{ $l }}</option>@endforeach
                    </select></div>
            </div>
            <div class="nr-foot">
                <button type="button" class="df-btn" onclick="document.getElementById('newReport').close()">Batal</button>
                <button type="submit" class="df-btn primary">Buat Laporan</button>
            </div>
        </div>
    </form>
</dialog>
@if($errors->has('report_date') || $errors->has('shift') || $errors->has('location'))
    <script>document.getElementById('newReport').showModal();</script>
@endif

@if(! $detailMode && ($dash['kpi']['batches'] ?? 0) > 0)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        (function () {
            const T = @json($dash['trend']);
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#6b7896';
            Chart.defaults.borderColor = '#eef1f6';
            const line = (label, data, color) => ({ label, data, borderColor: color, backgroundColor: color, tension: .25, spanGaps: true, pointRadius: 3, pointHoverRadius: 5, borderWidth: 2.5 });
            const common = {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, boxHeight: 8, padding: 16 } },
                    tooltip: { backgroundColor: '#0d1117', padding: 10, cornerRadius: 8, boxPadding: 4 },
                },
                scales: { x: { grid: { display: false } } },
            };

            new Chart(document.getElementById('chPct'), {
                type: 'line',
                data: { labels: T.labels, datasets: [
                    line('Vent Tidak Sobek', T.vent, '#e85d2f'),
                    line('Jeroan Tidak Terambil', T.viscera, '#0284c7'),
                ] },
                options: { ...common, scales: { x: common.scales.x, y: { beginAtZero: true, suggestedMax: 100 } } },
            });

            new Chart(document.getElementById('chGrit'), {
                type: 'bar',
                data: { labels: T.labels, datasets: [
                    { label: 'Temuan Grit', data: T.grit, backgroundColor: '#7c3aed', borderRadius: 4, maxBarThickness: 28 },
                ] },
                options: { ...common, scales: { x: common.scales.x, y: { beginAtZero: true, ticks: { precision: 0 } } } },
            });
        })();
    </script>
@endif
@endsection