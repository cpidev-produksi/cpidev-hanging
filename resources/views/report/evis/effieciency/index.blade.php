@extends('layouts.app')

@section('content')
@php
    $badge = fn ($status) => match ($status) {
        'approved' => ['Disetujui', 'approved'],
        'known'    => ['Diketahui', 'known'],
        default    => ['Draft', 'draft'],
    };
    $f = fn ($v, $d = 1) => $v === null || $v === '' ? '–' : number_format((float) $v, $d, ',', '');
@endphp

<style>
    .df-wrap { font-family:'Inter',sans-serif; }
    .df-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; border:1px solid var(--card-border); background:#fff; color:var(--text-main); cursor:pointer; text-decoration:none; }
    .df-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .df-btn.danger { color:#b91c1c; border-color:#fecaca; background:#fff5f5; }
    .df-btn.sm { padding:4px 9px; font-size:12px; }
    .f-label { display:block; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); margin-bottom:5px; }
    .f-in { padding:8px 12px; border:1px solid var(--card-border); border-radius:8px; font-family:'Inter',sans-serif; font-size:13px; background:#fff; color:var(--text-main); }

    .df-filter { display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:18px; }
    .alert-err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:10px 14px; font-size:13px; margin-bottom:14px; }
    .alert-err ul { margin:0; padding-left:18px; }

    .report-list-wrap { overflow-x:auto; }
    table.report-list { width:100%; border-collapse:collapse; font-size:13px; }
    table.report-list th, table.report-list td { padding:10px 12px; border-bottom:1px solid var(--card-border); text-align:left; white-space:nowrap; }
    table.report-list th { background:#f5f7fc; color:var(--text-muted); font-size:10.5px; text-transform:uppercase; letter-spacing:.04em; }
    table.report-list tbody tr[data-href] { cursor:pointer; }
    table.report-list tbody tr[data-href]:hover td { background:#f8fafc; }
    .report-actions { display:flex; gap:6px; flex-wrap:wrap; }
    .report-actions form { margin:0; }
    .note { font-size:12px; color:var(--text-muted); margin:10px 0 0; }

    dialog.nr { position:fixed; inset:50% auto auto 50%; transform:translate(-50%, -50%); margin:0; border:none; border-radius:14px; padding:0; width:min(440px, 92vw); max-height:90vh; overflow:auto; box-shadow:0 20px 50px rgba(15,23,42,.25); }
    dialog.nr::backdrop { background:rgba(15,23,42,.45); }
    .nr-body { padding:18px; }
    .nr-title { font-size:15px; font-weight:700; margin-bottom:4px; }
    .nr-sub { font-size:12px; color:var(--text-muted); margin-bottom:14px; }
    .nr-grid { display:grid; gap:12px; }
    .nr-grid .f-in { width:100%; }
    .nr-foot { display:flex; justify-content:flex-end; gap:8px; margin-top:16px; }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px; margin-bottom:18px; }
    .kpi { border:1px solid var(--card-border); border-radius:12px; padding:14px; background:#fff; }
    .kpi .lbl { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); }
    .kpi .val { font-size:24px; font-weight:800; margin-top:4px; }
    .kpi .sub { font-size:11.5px; color:var(--text-muted); margin-top:2px; }
    .chart-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; margin-bottom:18px; }
    @media (max-width:900px){ .chart-grid { grid-template-columns:1fr; } }
    .chart-box { border:1px solid var(--card-border); border-radius:12px; padding:14px; background:#fff; }
    .chart-box h4 { font-size:13px; margin-bottom:10px; }
    .chart-box .cv { position:relative; height:260px; }
    .sec-h { font-size:13px; font-weight:700; margin:6px 0 10px; }
    .empty { padding:40px 10px; text-align:center; color:var(--text-muted); }

    /* tabel rekap dashboard */
    table.df-rekap { width:max-content; min-width:100%; border-collapse:collapse; font-size:12.5px; }
    table.df-rekap th, table.df-rekap td { padding:8px 9px; border-bottom:1px solid var(--card-border); white-space:nowrap; text-align:center; }
    table.df-rekap thead th { background:#f5f7fc; font-size:10.5px; text-transform:uppercase; letter-spacing:.04em; color:var(--text-muted); }
    table.df-rekap td.l, table.df-rekap th.l { text-align:left; }

    /* ===== Tombol: transisi & fokus ===== */
    .df-btn { line-height:1.2; transition:background-color .15s, border-color .15s, color .15s; }
    .df-btn:hover { border-color:#cbd3e3; background:#f8fafc; }
    .df-btn.primary:hover { background:#d44f24; border-color:#d44f24; }
    .df-btn.danger:hover { background:#fee2e2; }
    .df-btn:focus-visible { outline:2px solid var(--accent); outline-offset:2px; }
    .df-btn svg { width:14px; height:14px; flex:none; }

    /* ===== Detail laporan ===== */
    .dt-back { display:inline-flex; align-items:center; gap:4px; margin-bottom:16px; font-size:13px; font-weight:600; color:var(--text-muted); text-decoration:none; }
    .dt-back svg { width:16px; height:16px; transition:transform .15s; }
    .dt-back:hover { color:var(--accent); }
    .dt-back:hover svg { transform:translateX(-3px); }
    .dt-back:focus-visible { outline:2px solid var(--accent); outline-offset:3px; border-radius:4px; }

    .dt-card.rp-card { --st:#d97706; position:relative; margin:0 0 22px; padding:0; background:#fff; border:1px solid var(--card-border); border-radius:14px; box-shadow:0 1px 3px rgba(15,23,42,.04), 0 10px 28px -14px rgba(15,23,42,.12); scroll-margin-top:90px; }
    .dt-card.is-known { --st:#0284c7; }
    .dt-card.is-approved { --st:#059669; }
    .dt-card::before { content:''; position:absolute; inset:0 auto 0 0; width:4px; background:var(--st); border-radius:14px 0 0 14px; }

    .dt-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px 24px; padding:22px 24px 20px 28px; }
    .dt-title-row { display:flex; align-items:center; flex-wrap:wrap; gap:6px 12px; }
    .dt-title { font-size:22px; font-weight:800; letter-spacing:-.01em; line-height:1.25; color:var(--text-main); }
    .dt-actions { display:flex; flex-wrap:wrap; gap:8px; }
    .dt-actions .df-btn { padding:8px 14px; }

    .dt-status { display:inline-flex; align-items:center; gap:6px; padding:3px 10px 3px 8px; border-radius:999px; border:1px solid; font-size:12px; font-weight:600; white-space:nowrap; }
    .dt-status::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .dt-status.draft { background:#FEF9C3; color:#92400E; border-color:#FDE68A; }
    .dt-status.known { background:#e0f2fe; color:#0369a1; border-color:#bae6fd; }
    .dt-status.approved { background:rgba(16,185,129,.10); color:#047857; border-color:rgba(16,185,129,.25); }

    .dt-facts { display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:14px 28px; padding:16px 24px 18px 28px; background:#fafbff; border-top:1px solid var(--card-border); }
    .dt-fact dt { margin-bottom:3px; font-size:12px; font-weight:500; color:var(--text-muted); }
    .dt-fact dd { display:flex; align-items:center; gap:6px; font-size:14.5px; font-weight:700; color:var(--text-main); }
    .dt-fact dd svg { width:15px; height:15px; flex:none; color:var(--text-muted); }

    .dt-section-head { padding:16px 24px 12px 28px; border-top:1px solid var(--card-border); }
    .dt-section-head h3 { font-size:14px; font-weight:700; }
    .dt-table-wrap { overflow-x:auto; padding:0 14px 14px 18px; }

    @media (max-width:640px) {
        .dt-head { padding:18px 16px 16px 20px; }
        .dt-title { font-size:19px; }
        .dt-actions { width:100%; }
        .dt-actions .df-btn { flex:1 1 auto; justify-content:center; }
        .dt-facts { grid-template-columns:repeat(2, minmax(0, 1fr)); padding:14px 16px 16px 20px; }
        .dt-section-head { padding:14px 16px 10px 20px; }
        .dt-table-wrap { padding:0 8px 12px 12px; }
    }
    @media (prefers-reduced-motion:reduce) {
        .df-btn, .dt-back svg { transition:none; }
        .dt-back:hover svg { transform:none; }
    }
</style>
<style>
    .alert-ok { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; border-radius:10px; padding:10px 14px; font-size:13px; margin-bottom:14px; }
</style>

<div class="panel df-wrap">
    <div class="panel-header">
        <div class="panel-title">Efficiency Evisceration Report</div>
        <button type="button" class="df-btn primary" onclick="document.getElementById('newReport').showModal()">+ Buat Laporan</button>
    </div>

    <div class="panel-body">
        @if(session('status'))
            <div class="alert-ok">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-err"><ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
        @endif

        @unless($detailMode)
            {{-- Filter --}}
            <form method="GET" class="df-filter" id="reportFilterForm">
                <div><label class="f-label">Tanggal</label><input class="f-in" type="date" name="date" value="{{ $filters['date'] }}"></div>
                <div><label class="f-label">Lokasi</label>
                    <select class="f-in" name="location">
                        <option value="">Semua</option>
                        @foreach(['SH01','SH02'] as $l)<option value="{{ $l }}" @selected($filters['location'] === $l)>{{ $l }}</option>@endforeach
                    </select></div>
                <div><label class="f-label">Shift</label>
                    <select class="f-in" name="shift">
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
            <h2 class="sec-h">Dashboard Summary</h2>
            @php $k = $dash['kpi']; @endphp
            @if($k['batches'] === 0)
                <div class="empty">Belum ada data pada filter ini.</div>
            @else
                <div class="kpi-grid">
                    <div class="kpi"><div class="lbl">Laporan</div><div class="val">{{ $k['reports'] }}</div>
                        <div class="sub">{{ $k['draft'] }} draft · {{ $k['known'] }} diketahui · {{ $k['approved'] }} disetujui</div></div>
                    <div class="kpi"><div class="lbl">Farm Tercatat</div><div class="val">{{ $k['batches'] }}</div><div class="sub">batch</div></div>
                    <div class="kpi"><div class="lbl">Vent Tidak Sobek</div><div class="val">{{ $f($k['vent'], 1) }}<small style="font-size:13px;"> %</small></div><div class="sub">rata-rata dari sampling</div></div>
                    <div class="kpi"><div class="lbl">Jeroan Tidak Terambil</div><div class="val">{{ $f($k['viscera'], 1) }}<small style="font-size:13px;"> %</small></div><div class="sub">rata-rata dari sampling</div></div>
                    <div class="kpi"><div class="lbl">Speed Line Evis</div><div class="val">{{ $f($k['speed_evis'], 0) }}</div><div class="sub">rata-rata BPH</div></div>
                    <div class="kpi"><div class="lbl">Jumlah Sampling</div><div class="val">{{ number_format($k['sampling_total'], 0, ',', '') }}</div><div class="sub">total ekor</div></div>
                </div>

                <div class="kpi-grid">
                    <div class="kpi"><div class="lbl">Temuan Grit</div><div class="val">{{ number_format($k['grit_total'], 0, ',', '') }}</div><div class="sub">total ekor</div></div>
                    <div class="kpi"><div class="lbl">Sprayer</div><div class="val" style="font-size:18px;">{{ $k['sprayer_on'] }} dari {{ $k['batches'] }} farm</div><div class="sub">beroperasi</div></div>
                    <div class="kpi"><div class="lbl">Debit Air Awal</div><div class="val" style="font-size:18px;">{{ $f($k['water_start'], 2) }}</div><div class="sub">rata-rata</div></div>
                    <div class="kpi"><div class="lbl">Debit Air Akhir</div><div class="val" style="font-size:18px;">{{ $f($k['water_end'], 2) }}</div><div class="sub">rata-rata</div></div>
                </div>

                <div class="chart-grid">
                    <div class="chart-box"><h4>Persentase per Tanggal (%)</h4><div class="cv"><canvas id="chPct"></canvas></div></div>
                    <div class="chart-box"><h4>Temuan Grit per Tanggal (ekor)</h4><div class="cv"><canvas id="chGrit"></canvas></div></div>
                </div>

                <div class="sec-h">Rekap per Farm</div>
                <div style="overflow-x:auto;">
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
                    <div class="empty">Belum ada laporan pada filter ini. Klik <strong>+ Buat Laporan</strong> untuk memulai.</div>
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
                                        <td>{{ $report->report_date->format('d/m/Y') }}</td>
                                        <td>{{ $report->location }}</td>
                                        <td>{{ ucfirst($report->shift) }}</td>
                                        <td>{{ $report->items_count }}</td>
                                        <td><span class="dt-status {{ $bc }}">{{ $bl }}</span></td>
                                        <td>
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
<dialog class="nr" id="newReport">
    <form method="POST" action="{{ route('evisceration.efficiency.store') }}" autocomplete="off">
        @csrf
        <div class="nr-body">
            <div class="nr-title">Buat Laporan Baru</div>
            <div class="nr-sub">Satu laporan untuk satu tanggal operasional, shift, dan lokasi. Farm ditambahkan dari detail laporan.</div>
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
            const line = (label, data, color) => ({ label, data, borderColor: color, backgroundColor: color, tension: .25, spanGaps: true, pointRadius: 3 });
            const common = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };

            new Chart(document.getElementById('chPct'), {
                type: 'line',
                data: { labels: T.labels, datasets: [
                    line('Vent Tidak Sobek', T.vent, '#e85d2f'),
                    line('Jeroan Tidak Terambil', T.viscera, '#0284c7'),
                ] },
                options: { ...common, scales: { y: { beginAtZero: true, suggestedMax: 100 } } },
            });

            new Chart(document.getElementById('chGrit'), {
                type: 'bar',
                data: { labels: T.labels, datasets: [
                    { label: 'Temuan Grit', data: T.grit, backgroundColor: '#7c3aed' },
                ] },
                options: { ...common, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
            });
        })();
    </script>
@endif
@endsection