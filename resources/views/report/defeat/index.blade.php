@extends('layouts.app')

@section('content')
@php
    use App\Models\DefeatReportItem as DI;

    $badge = fn ($status) => match ($status) {
        'approved' => ['Disetujui', 'background:rgba(16,185,129,.10);color:#047857;border:1px solid rgba(16,185,129,.25);'],
        'known'    => ['Diketahui', 'background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;'],
        default    => ['Draft', 'background:#FEF9C3;color:#92400E;border:1px solid #FDE68A;'],
    };
    $f = fn ($v, $d = 1) => DI::fmt($v, $d);
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
</style>

<div class="panel df-wrap">
    <div class="panel-header">
        <div class="panel-title">Efficiency Defeathering Report</div>
        <button type="button" class="df-btn primary" onclick="document.getElementById('newReport').showModal()">+ Buat Laporan</button>
    </div>

    <div class="panel-body">
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
                <a href="{{ route('defeathering.efficiency.index') }}" class="df-btn">Reset</a>
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
            @if($reports->isEmpty())
                <div class="empty">Laporan tidak ditemukan.</div>
            @else
                <p><a class="df-btn" href="{{ route('defeathering.efficiency.index', ['date' => $filters['date'], 'location' => $filters['location'], 'shift' => $filters['shift'], 'farm_name' => $filters['farm_name']]) }}">Kembali ke Daftar</a></p>
                @foreach($reports as $report)
                    @php [$bl, $bs] = $badge($report->status); @endphp
                    <div class="rp-card" id="report-{{ $report->id }}">
                        <div class="rp-head">
                            <div class="rp-meta">
                                <div><div class="lbl">Tanggal Operasional</div><div class="val">{{ $report->report_date->format('d/m/Y') }}</div></div>
                                <div><div class="lbl">Shift</div><div class="val">{{ ucfirst($report->shift) }}</div></div>
                                <div><div class="lbl">Lokasi</div><div class="val">{{ $report->location }}</div></div>
                                <div><div class="lbl">Jumlah Farm</div><div class="val">{{ $report->items->count() }}</div></div>
                                <div><div class="lbl">Dibuat oleh</div><div class="val" style="font-weight:600;">{{ $report->checker_name ?: ($report->creator?->name ?? '–') }}</div></div>
                                <div><span style="display:inline-block;padding:3px 9px;border-radius:6px;font-size:11.5px;font-weight:600;{{ $bs }}">{{ $bl }}</span></div>
                            </div>
                            <div class="rp-actions">
                                @if($report->canManageItems())
                                    <a class="df-btn primary sm" href="{{ route('defeathering.efficiency.items.create', $report) }}">+ Tambah Farm</a>
                                @endif
                                <a class="df-btn sm" href="{{ route('defeathering.efficiency.show', $report) }}">Detail &amp; Pengesahan</a>
                                <a class="df-btn sm" href="{{ route('defeathering.efficiency.pdf', $report) }}">PDF</a>
                            </div>
                        </div>

                        @include('report.defeat.partials.table', ['report' => $report, 'rowActions' => true])
                    </div>
                @endforeach
            @endif
        @else
            {{-- ===================== DASHBOARD SUMMARY ===================== --}}
            <h2 class="sec-h">Dashboard Summary</h2>
            @php $k = $dash['kpi']; $a = $dash['avg']; @endphp
            @if($k['batches'] === 0)
                <div class="empty">Belum ada data pada filter ini.</div>
            @else
                <div class="kpi-grid">
                    <div class="kpi"><div class="lbl">Laporan</div><div class="val">{{ $k['reports'] }}</div>
                        <div class="sub">{{ $k['draft'] }} draft · {{ $k['known'] }} diketahui · {{ $k['approved'] }} disetujui</div></div>
                    <div class="kpi"><div class="lbl">Farm Tercatat</div><div class="val">{{ $k['batches'] }}</div><div class="sub">batch</div></div>
                    <div class="kpi"><div class="lbl">Head Pulling</div><div class="val">{{ $f($a['head_pulling'], 1) }}<small style="font-size:13px;"> %</small></div><div class="sub">rata-rata</div></div>
                    <div class="kpi"><div class="lbl">Leg Cutting</div><div class="val">{{ $f($a['leg_cutting'], 1) }}<small style="font-size:13px;"> %</small></div><div class="sub">Pulling {{ $f($a['leg_pulling'], 1) }} %</div></div>
                    <div class="kpi"><div class="lbl">After Plucker</div><div class="val">{{ $f($a['after_plucker'], 1) }}</div><div class="sub">Sortir karyawan {{ $f($a['after_sortir'], 1) }}</div></div>
                    <div class="kpi"><div class="lbl">AVG DTA</div><div class="val">{{ $f($k['avg_dta'], 2) }}<small style="font-size:13px;"> kg</small></div><div class="sub">rata-rata</div></div>
                    <div class="kpi"><div class="lbl">Speed Line</div><div class="val">{{ $k['live_bird'] !== null ? number_format($k['live_bird'], 0, ',', '') : '–' }}</div><div class="sub">Live Bird BPH · Evis C. {{ $k['evis_c'] !== null ? number_format($k['evis_c'], 0, ',', '') : '–' }}</div></div>
                    <div class="kpi"><div class="lbl">ATP</div><div class="val">{{ number_format($k['atp_total'], 0, ',', '') }}</div><div class="sub">total</div></div>
                </div>

                <div class="kpi-grid">
                    <div class="kpi"><div class="lbl">Stunner</div><div class="val" style="font-size:18px;">{{ $f($a['amp'], 2) }} A · {{ $f($a['volt'], 1) }} V</div><div class="sub">rata-rata Amp · Volt</div></div>
                    <div class="kpi"><div class="lbl">Scalder 1</div><div class="val" style="font-size:18px;">{{ $f($a['scalder1_temp'], 1) }} °C</div><div class="sub">rata-rata suhu</div></div>
                    <div class="kpi"><div class="lbl">Scalder 2</div><div class="val" style="font-size:18px;">{{ $f($a['scalder2_temp'], 1) }} °C</div><div class="sub">rata-rata suhu</div></div>
                    <div class="kpi"><div class="lbl">Scalder 3</div><div class="val" style="font-size:18px;">{{ $f($a['scalder3_temp'], 1) }} °C</div><div class="sub">rata-rata suhu</div></div>
                </div>

                <div class="chart-grid">
                    <div class="chart-box"><h4>Efisiensi per Tanggal (%)</h4><div class="cv"><canvas id="chEff"></canvas></div></div>
                    <div class="chart-box"><h4>After Plucker vs After Sortir Karyawan</h4><div class="cv"><canvas id="chDefeat"></canvas></div></div>
                </div>

                <div class="sec-h">Rekap per Farm</div>
                <div style="overflow-x:auto;">
                    <table class="df-rekap">
                        <thead>
                            <tr>
                                <th class="l">Nama Farm</th><th>Batch</th><th>AVG DTA</th>
                                <th>Head Pulling %</th><th>Leg Cutting %</th><th>Leg Pulling %</th>
                                <th>After Plucker</th><th>After Sortir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dash['byFarm'] as $r)
                                <tr>
                                    <td class="l">{{ $r['farm'] }}</td>
                                    <td>{{ $r['batches'] }}</td>
                                    <td>{{ $f($r['avg_dta'], 2) }}</td>
                                    <td>{{ $f($r['head_pulling'], 1) }}</td>
                                    <td>{{ $f($r['leg_cutting'], 1) }}</td>
                                    <td>{{ $f($r['leg_pulling'], 1) }}</td>
                                    <td>{{ $f($r['after_plucker'], 1) }}</td>
                                    <td>{{ $f($r['after_sortir'], 1) }}</td>
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
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    @php
                                        $rowUrl = route('defeathering.efficiency.index', [
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
                                        <td>
                                            <div class="report-actions">
                                                <a class="df-btn sm" href="{{ route('defeathering.efficiency.show', $report) }}#report-items">Show / Edit</a>
                                                <a class="df-btn sm" href="{{ route('defeathering.efficiency.show', $report) }}#approval">Pengesahan</a>
                                                <a class="df-btn sm" href="{{ route('defeathering.efficiency.pdf', $report) }}">PDF</a>
                                                @if($report->canBeDeleted())
                                                    <form method="POST" action="{{ route('defeathering.efficiency.destroy', $report) }}"
                                                          onsubmit="return confirm('Hapus laporan ini beserta seluruh datanya?')">
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
@endif

{{-- Dialog: buat tabel baru (dokumen per tanggal operasional + shift + lokasi) --}}
<dialog class="nr" id="newReport">
    <form method="POST" action="{{ route('defeathering.efficiency.store') }}" autocomplete="off">
        @csrf
        <div class="nr-body">
            <div class="nr-title">Buat Tabel Baru</div>
            <div class="nr-sub">Satu tabel untuk satu tanggal operasional, shift, dan lokasi. Farm ditambahkan dari tabel.</div>
            <div class="nr-grid">
                <div><label class="f-label" for="nr_date">Tanggal Operasional</label>
                    <input class="f-in" type="date" id="nr_date" name="report_date" value="{{ old('report_date', $newDefaults['report_date']) }}" required></div>
                <div><label class="f-label" for="nr_shift">Shift</label>
                    <select class="f-in" id="nr_shift" name="shift" required>
                        <option value="pagi" @selected(old('shift', $newDefaults['shift']) === 'pagi')>Pagi</option>
                        <option value="malam" @selected(old('shift', $newDefaults['shift']) === 'malam')>Malam</option>
                    </select></div>
                <div><label class="f-label" for="nr_loc">Lokasi</label>
                    <select class="f-in" id="nr_loc" name="location" required>
                        @foreach(['SH01','SH02'] as $l)<option value="{{ $l }}" @selected(old('location', $newDefaults['location']) === $l)>{{ $l }}</option>@endforeach
                    </select></div>
            </div>
            <div class="nr-foot">
                <button type="button" class="df-btn" onclick="document.getElementById('newReport').close()">Batal</button>
                <button type="submit" class="df-btn primary">Buat Tabel</button>
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

            new Chart(document.getElementById('chEff'), {
                type: 'line',
                data: { labels: T.labels, datasets: [
                    line('Head Pulling', T.head_pulling, '#e85d2f'),
                    line('Leg Cutting', T.leg_cutting, '#0284c7'),
                    line('Leg Pulling', T.leg_pulling, '#16a34a'),
                ] },
                options: { ...common, scales: { y: { suggestedMax: 100 } } },
            });

            new Chart(document.getElementById('chDefeat'), {
                type: 'bar',
                data: { labels: T.labels, datasets: [
                    { label: 'After Plucker', data: T.after_plucker, backgroundColor: '#7c3aed' },
                    { label: 'After Sortir Karyawan', data: T.after_sortir, backgroundColor: '#94a3b8' },
                ] },
                options: { ...common, scales: { y: { beginAtZero: true } } },
            });
        })();
    </script>
@endif
@endsection