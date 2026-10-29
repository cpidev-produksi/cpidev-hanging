@extends('layouts.app')

@section('content')
@php
    $f = fn ($v, $d = 1) => $v === null || $v === '' ? '–' : number_format((float) $v, $d, ',', '');
    $pct = fn ($v) => max(0, min(100, (float) ($v ?? 0)));

    // Persentase diturunkan dari ringkasan yang sudah ada di controller
    $usablePct = $stats['checks'] > 0 ? (($stats['checks'] - $stats['unusable']) / $stats['checks']) * 100 : null;
    $verifPct  = $stats['verifications'] > 0 ? (($stats['verifications'] - $stats['not_ok']) / $stats['verifications']) * 100 : null;

    $badge = function ($r) {
        if ($r->unusable_count > 0) return [$r->unusable_count . '× tidak dapat digunakan', 'bad'];
        if ($r->checks_count === 0) return ['Belum ada pemeriksaan', 'draft'];
        return ['Normal', 'approved'];
    };
@endphp

<style>
    .ef { --ink:var(--text-main,#0d1117); --mut:var(--text-muted,#6b7896); --line:var(--card-border,#e4e8f0); --acc:var(--accent,#e85d2f); --soft:#f6f8fc; --hover:#fafbfe; }
    .ef { max-width:1440px; margin:0 auto; font-family:'Inter',system-ui,sans-serif; color:var(--ink); }

    /* ===== Header halaman ===== */
    .ef-head { display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:14px 24px; margin-bottom:22px; }
    .ef-title { font-size:26px; font-weight:800; letter-spacing:-.025em; line-height:1.15; }

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
    .ef-body > .df-filter + .sec-h { margin-top:0; }
    .empty { padding:44px 16px; text-align:center; font-size:14px; color:var(--mut); background:#fff; border:1px dashed #cfd6e4; border-radius:16px; }

    /* ===== Status ===== */
    .dt-status { display:inline-flex; align-items:center; gap:6px; padding:3px 10px 3px 8px; border-radius:999px; border:1px solid; font-size:12px; font-weight:600; white-space:nowrap; }
    .dt-status::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .dt-status.draft { background:#FEF9C3; color:#92400E; border-color:#FDE68A; }
    .dt-status.approved { background:rgba(16,185,129,.10); color:#047857; border-color:rgba(16,185,129,.25); }
    .dt-status.bad { background:rgba(239,68,68,.10); color:#b91c1c; border-color:rgba(239,68,68,.28); }

    .chip-loc { display:inline-flex; align-items:center; padding:2px 9px; border-radius:999px; border:1px solid var(--line); background:var(--soft); font-size:11.5px; font-weight:700; }
    .chip-loc.night { background:rgba(232,93,47,.10); border-color:rgba(232,93,47,.25); color:var(--acc); }

    /* ===== KPI ===== */
    .kpi .lbl { font-size:12.5px; font-weight:600; color:var(--mut); }
    .kpi .val { margin-top:8px; font-size:34px; font-weight:800; letter-spacing:-.03em; line-height:1; font-variant-numeric:tabular-nums; }
    .kpi .val .u { margin-left:3px; font-size:15px; font-weight:600; letter-spacing:0; color:var(--mut); }
    .kpi .sub { margin-top:7px; font-size:12px; color:var(--mut); }
    .kpi.warn .val { color:#b91c1c; }

    .kpi-hero { display:grid; grid-template-columns:repeat(auto-fit, minmax(230px,1fr)); gap:14px; margin-bottom:14px; }
    .kpi-hero .kpi { padding:18px 20px 18px; background:#fff; border:1px solid var(--line); border-radius:16px; }
    .kpi.has-meter .lbl { display:flex; align-items:center; gap:8px; }
    .kpi.has-meter .lbl::before { content:''; width:8px; height:8px; border-radius:50%; background:var(--mc, var(--acc)); }
    .meter { height:6px; margin-top:14px; border-radius:99px; background:#eef1f6; overflow:hidden; }
    .meter i { display:block; height:100%; border-radius:inherit; background:var(--mc, var(--acc)); }

    .stat-strip { display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); margin-bottom:14px; background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
    .stat-strip .kpi { padding:16px 18px; margin:0 -1px -1px 0; border-right:1px solid var(--line); border-bottom:1px solid var(--line); }
    .stat-strip .kpi .val { margin-top:6px; font-size:24px; }

    /* ===== Daftar laporan ===== */
    .report-list-wrap { background:#fff; border:1px solid var(--line); border-radius:16px; overflow-x:auto; }
    table.report-list { width:100%; border-collapse:collapse; font-size:13.5px; font-variant-numeric:tabular-nums; }
    table.report-list th, table.report-list td { padding:12px 16px; border-bottom:1px solid var(--line); text-align:left; white-space:nowrap; }
    table.report-list th { background:var(--soft); font-size:12px; font-weight:600; color:var(--mut); }
    table.report-list th.c, table.report-list td.c { text-align:center; }
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
        table.report-list td { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:6px 0; border:0; white-space:normal; text-align:right; }
        table.report-list td::before { content:attr(data-label); font-size:12px; font-weight:600; color:var(--mut); text-align:left; }
        table.report-list td.act { display:block; padding-top:10px; text-align:left; }
        table.report-list td.act::before { content:none; }
        table.report-list tbody tr[data-href]:hover td { background:transparent; }
    }
    @media (max-width:640px) {
        .ef-title { font-size:21px; }
        .ef-head .df-btn { width:100%; }
        .kpi .val { font-size:28px; }
    }
    @media (prefers-reduced-motion:reduce) { .df-btn, .f-in { transition:none; } }
</style>

<div class="ef df-wrap">
    <header class="ef-head">
        <div>
            <h1 class="ef-title">Pemeriksaan Metal Detector</h1>
        </div>
        <a href="{{ route('cut-up.metal-detector.create') }}" class="df-btn primary"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M10 4v12M4 10h12"/></svg> Buat laporan</a>
    </header>

    <div class="ef-body">

        {{-- Filter --}}
        <form method="GET" action="{{ route('cut-up.metal-detector.index') }}" class="df-filter" id="reportFilterForm">
            <div><label class="f-label" for="fFrom">Dari tanggal</label><input class="f-in" type="date" id="fFrom" name="from" value="{{ $from }}"></div>
            <div><label class="f-label" for="fTo">Sampai tanggal</label><input class="f-in" type="date" id="fTo" name="to" value="{{ $to }}"></div>
            <div><label class="f-label" for="fLoc">Lokasi</label>
                <select class="f-in" id="fLoc" name="location">
                    <option value="">Semua</option>
                    @foreach(['SH01','SH02'] as $l)<option value="{{ $l }}" @selected($location === $l)>{{ $l }}</option>@endforeach
                </select></div>
            <div><label class="f-label" for="fShift">Shift</label>
                <select class="f-in" id="fShift" name="shift">
                    <option value="">Semua</option>
                    @foreach(['Pagi','Malam'] as $s)<option value="{{ $s }}" @selected($shift === $s)>{{ $s }}</option>@endforeach
                </select></div>
            <a href="{{ route('cut-up.metal-detector.index') }}" class="df-btn">Reset</a>
        </form>
        <script>
            document.getElementById('reportFilterForm').addEventListener('change', event => {
                if (event.target.matches('input[name="from"], input[name="to"], select[name="location"], select[name="shift"]')) {
                    event.currentTarget.requestSubmit();
                }
            });
        </script>

        {{-- ===================== DASHBOARD SUMMARY ===================== --}}
        <h2 class="sec-h">Ringkasan dashboard</h2>
        @if($stats['reports'] === 0)
            <div class="empty">Belum ada data pada filter ini.</div>
        @else
            <div class="kpi-hero">
                <div class="kpi has-meter" style="--mc:#10b981">
                    <div class="lbl">Pemeriksaan dapat digunakan</div>
                    <div class="val">{{ $f($usablePct, 1) }}<span class="u">%</span></div>
                    <div class="sub">dari {{ number_format($stats['checks']) }} pemeriksaan sensitivitas</div>
                    <div class="meter" role="img" aria-label="Pemeriksaan dapat digunakan {{ $f($usablePct, 1) }} persen"><i style="width:{{ $pct($usablePct) }}%"></i></div>
                </div>
                <div class="kpi has-meter" style="--mc:#0284c7">
                    <div class="lbl">Verifikasi tanpa NOT OK</div>
                    <div class="val">{{ $f($verifPct, 1) }}<span class="u">%</span></div>
                    <div class="sub">dari {{ number_format($stats['verifications']) }} verifikasi produk</div>
                    <div class="meter" role="img" aria-label="Verifikasi tanpa NOT OK {{ $f($verifPct, 1) }} persen"><i style="width:{{ $pct($verifPct) }}%"></i></div>
                </div>
            </div>

            <div class="stat-strip">
                <div class="kpi"><div class="lbl">Laporan</div><div class="val">{{ number_format($stats['reports']) }}</div><div class="sub">pada periode terpilih</div></div>
                <div class="kpi"><div class="lbl">Pemeriksaan Sensitivitas</div><div class="val">{{ number_format($stats['checks']) }}</div><div class="sub">kali pemeriksaan</div></div>
                <div class="kpi {{ $stats['unusable'] > 0 ? 'warn' : '' }}"><div class="lbl">Tidak Dapat Digunakan</div><div class="val">{{ number_format($stats['unusable']) }}</div><div class="sub">kejadian pemeriksaan</div></div>
                <div class="kpi"><div class="lbl">Verifikasi Produk</div><div class="val">{{ number_format($stats['verifications']) }}</div><div class="sub">baris verifikasi</div></div>
                <div class="kpi {{ $stats['not_ok'] > 0 ? 'warn' : '' }}"><div class="lbl">Verifikasi NOT OK</div><div class="val">{{ number_format($stats['not_ok']) }}</div><div class="sub">baris ada hasil NOT OK</div></div>
            </div>
        @endif

        {{-- ===================== DAFTAR LAPORAN ===================== --}}
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
                                <th>Jam Kerja</th>
                                <th class="c">Pemeriksaan</th>
                                <th class="c">Verifikasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $r)
                                @php
                                    [$bl, $bc] = $badge($r);
                                    $rowUrl = route('cut-up.metal-detector.edit', $r);
                                @endphp
                                <tr data-href="{{ $rowUrl }}" role="link" tabindex="0" aria-label="Buka laporan {{ $r->date_long }} {{ $r->location }} shift {{ $r->shift }}">
                                    <td data-label="Tanggal" style="font-weight:700;">{{ $r->date_long }}</td>
                                    <td data-label="Lokasi"><span class="chip-loc">{{ $r->location }}</span></td>
                                    <td data-label="Shift"><span class="chip-loc {{ $r->shift === 'Malam' ? 'night' : '' }}">{{ $r->shift }}</span></td>
                                    <td data-label="Jam kerja" style="color:var(--mut);">
                                        {{ $r->start_time ? substr($r->start_time, 0, 5) : '--:--' }}
                                        –
                                        {{ $r->end_time ? substr($r->end_time, 0, 5) : '--:--' }}
                                    </td>
                                    <td class="c" data-label="Pemeriksaan">{{ $r->checks_count }}</td>
                                    <td class="c" data-label="Verifikasi">{{ $r->verifications_count }}</td>
                                    <td data-label="Status"><span class="dt-status {{ $bc }}">{{ $bl }}</span></td>
                                    <td class="act">
                                        <div class="report-actions">
                                            <a class="df-btn sm" href="{{ $rowUrl }}">Edit</a>
                                            <a class="df-btn sm" href="{{ route('cut-up.metal-detector.pdf', $r) }}" target="_blank" rel="noopener">PDF</a>
                                            <form method="POST" action="{{ route('cut-up.metal-detector.destroy', $r) }}"
                                                  onsubmit="return confirm('Hapus laporan ini beserta seluruh data pemeriksaan dan verifikasi?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="df-btn danger sm">Hapus</button>
                                            </form>
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
    </div>
</div>

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
@endsection