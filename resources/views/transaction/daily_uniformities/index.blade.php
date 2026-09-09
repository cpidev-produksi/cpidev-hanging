@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

  .du-root { font-family:'Plus Jakarta Sans',sans-serif; background:#f8fafc; min-height:100vh; padding:32px; }
  .du-wrap { max-width:1180px; margin:0 auto; }

  .du-head { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
  .du-title { font-size:22px; font-weight:800; color:#0f172a; letter-spacing:-.2px; }
  .du-sub { font-size:13px; color:#64748b; margin-top:2px; }
  .du-head-actions { display:flex; gap:10px; flex-wrap:wrap; }

  .du-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none; border:none; cursor:pointer; }
  .du-btn-primary { background:#1a56db; color:#fff; }
  .du-btn-primary:hover { background:#1743ab; color:#fff; }
  .du-btn-ghost { background:#fff; color:#0f172a; border:1px solid #e2e8f0; }
  .du-btn-danger { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
  .du-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }

  .du-filter { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .du-filter label { font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; }
  .du-filter input[type=date] { border:1px solid #e2e8f0; border-radius:10px; padding:8px 12px; font-family:inherit; font-size:13px; color:#0f172a; }
  .du-shift-tabs { display:flex; gap:6px; margin-bottom:20px; }
  .du-shift-tab { padding:9px 16px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; color:#475569; font-size:13px; font-weight:700; text-decoration:none; }
  .du-shift-tab.active { background:#1a56db; border-color:#1a56db; color:#fff; }

  .du-alert { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:10px 16px; border-radius:12px; font-size:13px; font-weight:600; margin-bottom:16px; }

  .du-card { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:24px; margin-bottom:20px; }
  .du-card.table-card { padding:0; overflow:hidden; }
  .du-card-title { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#1a56db; margin-bottom:4px; }
  .du-card-title small { display:block; font-size:11px; font-weight:600; text-transform:none; letter-spacing:0; color:#94a3b8; margin-top:2px; }

  /* ===== Chart 3 batang + axis ===== */
  .du-chart-legend { display:flex; gap:16px; flex-wrap:wrap; margin-bottom:14px; }
  .du-chart-legend span { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:700; color:#475569; }
  .du-chart-legend .dot { width:9px; height:9px; border-radius:50%; }
  .du-chart-legend .dot.below { background:#f59e0b; }
  .du-chart-legend .dot.in { background:#0d9488; }
  .du-chart-legend .dot.above { background:#6366f1; }

  .du-chart-axis-wrap { display:flex; gap:10px; }
  .du-chart-yaxis { display:flex; flex-direction:column; justify-content:space-between; height:210px; padding:2px 0 24px; font-size:10.5px; font-weight:700; color:#94a3b8; text-align:right; width:38px; flex-shrink:0; }
  .du-chart-ytitle { writing-mode:vertical-rl; transform:rotate(180deg); font-size:10.5px; font-weight:800; color:#64748b; text-align:center; letter-spacing:.04em; text-transform:uppercase; padding-bottom:24px; flex-shrink:0; }
  .du-chart-plot { position:relative; flex:1; height:210px; margin-bottom:24px; border-left:1.5px solid #cbd5e1; border-bottom:1.5px solid #cbd5e1; }
  .du-chart-gridline { position:absolute; left:0; right:0; border-top:1px dashed #e2e8f0; }
  .du-chart-bars { position:absolute; inset:0; display:flex; align-items:flex-end; justify-content:space-around; padding:0 16px; }
  .du-chart-xtitle { text-align:center; font-size:10.5px; font-weight:800; color:#64748b; letter-spacing:.04em; text-transform:uppercase; margin-top:6px; }

  .du-chart-col { display:flex; flex-direction:column; align-items:center; }
  .du-chart-value { font-size:14px; font-weight:800; color:#0f172a; margin-bottom:6px; }
  .du-chart-bar { width:60px; border-radius:10px 10px 4px 4px; }
  .du-chart-bar.below { background:linear-gradient(180deg,#fbbf24,#f59e0b); }
  .du-chart-bar.in { background:linear-gradient(180deg,#2dd4bf,#0d9488); }
  .du-chart-bar.above { background:linear-gradient(180deg,#818cf8,#6366f1); }
  .du-chart-label { margin-top:10px; font-size:12px; font-weight:800; color:#334155; }
  .du-chart-count { font-size:11px; color:#94a3b8; margin-top:2px; }

  /* ===== Table ===== */
  table.du-table { width:100%; border-collapse:collapse; }
  .du-table th { text-align:left; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:12px 14px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
  .du-table th.center { text-align:center; }
  .du-table td { padding:13px 14px; border-bottom:1px solid #f1f5f9; font-size:13px; color:#0f172a; vertical-align:middle; }
  .du-table td.center { text-align:center; }
  .du-table tr:last-child td { border-bottom:none; }
  .du-table .du-toggle-row { cursor:pointer; }
  .du-table .du-toggle-row:hover td { background:#f8fafc; }
  .du-toggle-btn { width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; border:0; border-radius:8px; background:#eff6ff; color:#1a56db; cursor:pointer; }
  .du-toggle-btn svg { width:16px; height:16px; transition:transform .2s ease; }
  .du-toggle-row.open .du-toggle-btn svg { transform:rotate(180deg); }
  .du-detail-row { display:none; }
  .du-detail-row.open { display:table-row; }
  .du-detail-row td { padding:0 14px 14px; background:#f8fafc; }
  .du-detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .du-detail-panel { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px; }
  .du-detail-title { font-size:11px; font-weight:800; color:#1a56db; text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px; }
  .du-detail-info { display:grid; grid-template-columns:1fr 1fr; gap:8px 16px; }
  .du-detail-item .lbl { display:block; font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; }
  .du-detail-item .val { display:block; margin-top:2px; font-size:12px; font-weight:700; color:#0f172a; }
  .du-detail-summary { display:grid; grid-template-columns:repeat(2, 1fr); gap:8px; }
  .du-detail-summary .du-sum-box { padding:10px; }
  .du-detail-summary .du-sum-box .val { font-size:15px; }
  @media (max-width:760px) { .du-detail-grid { grid-template-columns:1fr; } }

  .du-pct-cell { display:inline-flex; padding:3px 10px; border-radius:100px; font-size:12px; font-weight:800; }
  .du-pct-cell.below { background:#fef3c7; color:#b45309; }
  .du-pct-cell.in { background:#ccfbf1; color:#0d9488; }
  .du-pct-cell.above { background:#e0e7ff; color:#4338ca; }

  .du-empty { padding:48px 16px; text-align:center; color:#94a3b8; font-size:13px; }

  .du-actions { display:flex; gap:6px; justify-content:center; }
  .du-icon-btn { width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; border-radius:9px; border:1px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; }
  .du-icon-btn.danger { color:#dc2626; border-color:#fecaca; background:#fef2f2; }
  .du-icon-btn svg { width:15px; height:15px; fill:none; stroke:currentColor; stroke-width:2; }

  /* ===== Scroll to top ===== */
  .du-scrolltop {
    position:fixed; right:24px; bottom:24px; width:46px; height:46px; border-radius:50%;
    background:#1a56db; color:#fff; border:none; padding:0;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; box-shadow:0 10px 24px rgba(26,86,219,.35); z-index:60;
    opacity:0; visibility:hidden; transform:translateY(10px);
    transition:opacity .2s ease, transform .2s ease, visibility .2s ease;
  }
  .du-scrolltop.show { opacity:1; visibility:visible; transform:translateY(0); }
  .du-scrolltop svg { width:20px; height:20px; stroke:#fff; fill:none; stroke-width:2.5; display:block; }
</style>

<div class="du-root">
  <div class="du-wrap">

    <div class="du-head">
      <div>
        <div class="du-title">Laporan Daily Uniformity</div>
        <div class="du-sub">Trial sampling berat live bird per truk (No. SPPA)</div>
      </div>
      <div class="du-head-actions">
        <a href="{{ route('daily-uniformities.export-pdf', ['date' => $date, 'shift' => $shift]) }}" class="du-btn du-btn-ghost" target="_blank">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
          Export PDF
        </a>
        <a href="{{ route('daily-uniformities.create') }}" class="du-btn du-btn-primary">
          + Buat Laporan Baru
        </a>
      </div>
    </div>

    @if (session('status'))
      <div class="du-alert">{{ session('status') }}</div>
    @endif

    <form method="GET" class="du-filter">
      <label for="date">Tanggal</label>
      <input type="date" id="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
    </form>

    <div class="du-shift-tabs" role="tablist" aria-label="Filter shift">
      <a href="{{ route('daily-uniformities.index', ['date' => $date, 'shift' => 'all']) }}" class="du-shift-tab {{ $shift === 'all' ? 'active' : '' }}" role="tab" aria-selected="{{ $shift === 'all' ? 'true' : 'false' }}">Semua</a>
      <a href="{{ route('daily-uniformities.index', ['date' => $date, 'shift' => 'pagi']) }}" class="du-shift-tab {{ $shift === 'pagi' ? 'active' : '' }}" role="tab" aria-selected="{{ $shift === 'pagi' ? 'true' : 'false' }}">Pagi</a>
      <a href="{{ route('daily-uniformities.index', ['date' => $date, 'shift' => 'malam']) }}" class="du-shift-tab {{ $shift === 'malam' ? 'active' : '' }}" role="tab" aria-selected="{{ $shift === 'malam' ? 'true' : 'false' }}">Malam</a>
    </div>

    <div class="du-card table-card">
      <table class="du-table">
        <thead>
          <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Tanggal</th>
            <th rowspan="2">Farm</th>
            <th rowspan="2">Uniformity</th>
            <th rowspan="2">Jumlah Sample</th>
            <th colspan="3" class="center">Persentase Uniformity</th>
            <th rowspan="2" class="center">Aksi</th>
          </tr>
          <tr>
            <th class="center">Undersize</th>
            <th class="center">In Range</th>
            <th class="center">Oversize</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $idx => $item)
            @php $s = $item->summary_data; @endphp
            <tr class="du-toggle-row" data-detail-id="du-detail-{{ $item->id }}" tabindex="0" aria-expanded="false">
              <td>
                <button type="button" class="du-toggle-btn" aria-label="Tampilkan detail" tabindex="-1">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                {{ $idx + 1 }}
              </td>
              <td>{{ optional($item->monitorControl->process_date)->format('d/m/Y') }}</td>
              <td>{{ $item->monitorControl->farm->name ?? '-' }}</td>
              <td>{{ $item->monitorControl->size ?? '-' }}</td>
              <td>{{ $s['count'] }} ekor</td>
              <td class="center"><span class="du-pct-cell below">{{ $s['below']['pct'] }}%</span></td>
              <td class="center"><span class="du-pct-cell in">{{ $s['in_range']['pct'] }}%</span></td>
              <td class="center"><span class="du-pct-cell above">{{ $s['above']['pct'] }}%</span></td>
              <td>
                <div class="du-actions">
                  <a href="{{ route('daily-uniformities.show', $item) }}" class="du-icon-btn" title="Lihat">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </a>
                  <a href="{{ route('daily-uniformities.export-single-pdf', $item) }}" class="du-icon-btn" title="Export PDF" target="_blank">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
                  </a>
                  <a href="{{ route('daily-uniformities.edit', $item) }}" class="du-icon-btn" title="Edit">
                    <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                  </a>
                  <form action="{{ route('daily-uniformities.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus laporan ini beserta seluruh data berat sampling?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="du-icon-btn danger" title="Hapus">
                      <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <tr id="du-detail-{{ $item->id }}" class="du-detail-row">
              <td colspan="9">
                <div class="du-detail-grid">
                  <div class="du-detail-panel">
                    <div class="du-detail-title">Data Trial Sampling Berat Live Bird</div>
                    <div class="du-detail-info">
                      <div class="du-detail-item"><span class="lbl">Tanggal</span><span class="val">{{ optional($item->monitorControl->process_date)->format('d/m/Y') }}</span></div>
                      <div class="du-detail-item"><span class="lbl">Shift</span><span class="val">{{ ucfirst($item->shift) }}</span></div>
                      <div class="du-detail-item"><span class="lbl">No. SPPA</span><span class="val">{{ $item->monitorControl->sppa_no ?? '-' }}</span></div>
                      <div class="du-detail-item"><span class="lbl">Nama Farm</span><span class="val">{{ $item->monitorControl->farm->name ?? '-' }}</span></div>
                      <div class="du-detail-item"><span class="lbl">No. Polisi</span><span class="val">{{ $item->monitorControl->plateNumber->plate_number ?? '-' }}</span></div>
                      <div class="du-detail-item"><span class="lbl">Ekspedisi</span><span class="val">{{ $item->monitorControl->expedition->name ?? '-' }}</span></div>
                      <div class="du-detail-item"><span class="lbl">Sopir</span><span class="val">{{ $item->driverName() ?? '-' }}</span></div>
                      <div class="du-detail-item"><span class="lbl">ABW</span><span class="val">{{ $item->monitorControl->abw ?? '-' }}</span></div>
                      <div class="du-detail-item"><span class="lbl">Jumlah Ayam Diterima</span><span class="val">{{ number_format((int)($item->monitorControl->ayam_diterima ?? 0)) }} ekor</span></div>
                      <div class="du-detail-item"><span class="lbl">Uniformity (Size)</span><span class="val">{{ $item->monitorControl->size ?? '-' }}</span></div>
                    </div>
                  </div>
                  <div class="du-detail-panel">
                    <div class="du-detail-title">Ringkasan Sampling</div>
                    <div class="du-detail-summary">
                      <div class="du-sum-box"><div class="lbl">Jumlah Sampling</div><div class="val">{{ $s['count'] }} ekor</div></div>
                      <div class="du-sum-box"><div class="lbl">Total Berat</div><div class="val">{{ number_format($s['total'], 3) }} kg</div></div>
                      <div class="du-sum-box"><div class="lbl">Berat Terkecil</div><div class="val">{{ $s['min'] !== null ? number_format($s['min'], 3) : '-' }}</div></div>
                      <div class="du-sum-box"><div class="lbl">Berat Terbesar</div><div class="val">{{ $s['max'] !== null ? number_format($s['max'], 3) : '-' }}</div></div>
                      <div class="du-sum-box"><div class="lbl">Rata-rata Berat</div><div class="val">{{ $s['avg'] !== null ? number_format($s['avg'], 3) . ' kg' : '-' }}</div></div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9">
                <div class="du-empty">Belum ada laporan Daily Uniformity untuk tanggal ini.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="du-card">
        <div class="du-card-title">
          Grafik Sebaran Uniformity
          {{-- <small>{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }} &middot; {{ $aggregate['count'] }} ekor sampling</small> --}}
          <small>{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</small>
        </div>

        @if ($aggregate['count'] > 0)
          <div class="du-chart-legend">
            <span><span class="dot below"></span> Undersize (&lt; range)</span>
            <span><span class="dot in"></span> In Range (sesuai uniformity)</span>
            <span><span class="dot above"></span> Oversize (&gt; range)</span>
          </div>

          <div class="du-chart-axis-wrap">
            <div class="du-chart-ytitle">Persentase (%)</div>
            <div class="du-chart-yaxis">
              <span>100%</span>
              <span>75%</span>
              <span>50%</span>
              <span>25%</span>
              <span>0%</span>
            </div>
            <div style="flex:1;">
              <div class="du-chart-plot">
                <div class="du-chart-gridline" style="bottom:0%;"></div>
                <div class="du-chart-gridline" style="bottom:25%;"></div>
                <div class="du-chart-gridline" style="bottom:50%;"></div>
                <div class="du-chart-gridline" style="bottom:75%;"></div>
                <div class="du-chart-gridline" style="bottom:100%;"></div>

                <div class="du-chart-bars">
                  <div class="du-chart-col">
                    <div class="du-chart-value">{{ $aggregate['below']['pct'] }}%</div>
                    <div class="du-chart-bar below" style="height:{{ max((int) round($aggregate['below']['pct'] * 1.4), 4) }}px;"></div>
                    <div class="du-chart-label">Undersize</div>
                    <div class="du-chart-count">{{ $aggregate['below']['count'] }} ekor</div>
                  </div>
                  <div class="du-chart-col">
                    <div class="du-chart-value">{{ $aggregate['in_range']['pct'] }}%</div>
                    <div class="du-chart-bar in" style="height:{{ max((int) round($aggregate['in_range']['pct'] * 1.4), 4) }}px;"></div>
                    <div class="du-chart-label">In Range</div>
                    <div class="du-chart-count">{{ $aggregate['in_range']['count'] }} ekor</div>
                  </div>
                  <div class="du-chart-col">
                    <div class="du-chart-value">{{ $aggregate['above']['pct'] }}%</div>
                    <div class="du-chart-bar above" style="height:{{ max((int) round($aggregate['above']['pct'] * 1.4), 4) }}px;"></div>
                    <div class="du-chart-label">Oversize</div>
                    <div class="du-chart-count">{{ $aggregate['above']['count'] }} ekor</div>
                  </div>
                </div>
              </div>
              <div class="du-chart-xtitle">Kategori Uniformity</div>
            </div>
          </div>
        @else
          <div class="du-empty">Belum ada data sampling untuk tanggal ini.</div>
        @endif
      </div>
    </div>
</div>

<button type="button" class="du-scrolltop" id="duScrollTop" title="Kembali ke atas">
  <svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<script>
  (function () {
    const btn = document.getElementById('duScrollTop');
    if (!btn) return;
    window.addEventListener('scroll', function () {
      if (window.scrollY > 280) {
        btn.classList.add('show');
      } else {
        btn.classList.remove('show');
      }
    });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  })();
</script>
<script>
  document.querySelectorAll('.du-toggle-row').forEach(function (row) {
    function toggleDetail() {
      const detail = document.getElementById(row.dataset.detailId);
      const isOpen = row.classList.toggle('open');
      detail.classList.toggle('open', isOpen);
      row.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    row.addEventListener('click', function (event) {
      if (event.target.closest('a, button, form')) return;
      toggleDetail();
    });
    row.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        toggleDetail();
      }
    });
    row.querySelector('.du-toggle-btn').addEventListener('click', function (event) {
      event.stopPropagation();
      toggleDetail();
    });
  });
</script>
@endsection