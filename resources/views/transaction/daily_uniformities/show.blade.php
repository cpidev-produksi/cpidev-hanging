@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

  .du-root { font-family:'Plus Jakarta Sans',sans-serif; background:#f8fafc; min-height:100vh; padding:32px; }
  .du-wrap { max-width:1180px; margin:0 auto; }

  .du-back { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#64748b; text-decoration:none; margin-bottom:16px; }
  .du-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
  .du-title { font-size:22px; font-weight:800; color:#0f172a; letter-spacing:-.2px; margin-bottom:4px; }
  .du-sub { font-size:13px; color:#64748b; }

  .du-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none; border:none; cursor:pointer; }
  .du-btn-primary { background:#1a56db; color:#fff; }
  .du-btn-ghost { background:#fff; color:#0f172a; border:1px solid #e2e8f0; }
  .du-btn-danger { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
  .du-actions-row { display:flex; gap:8px; }

  /* ===== Baris atas: 2 kartu sejajar ===== */
  .du-top-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; align-items:start; }
  @media (max-width:860px) { .du-top-grid { grid-template-columns:1fr; } }

  .du-card { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:24px; margin-bottom:20px; }
  .du-card-title { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#1a56db; margin-bottom:16px; }

  .du-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 20px; }
  .du-info-item .lbl { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; }
  .du-info-item .val { font-size:14px; font-weight:700; color:#0f172a; margin-top:2px; }

  .du-sum-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:18px; }
  .du-sum-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:14px; }
  .du-sum-box .lbl { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; }
  .du-sum-box .val { font-size:19px; font-weight:900; color:#0f172a; margin-top:4px; }

  .du-range-row { margin-bottom:12px; }
  .du-range-row .row-top { display:flex; justify-content:space-between; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px; }
  .du-bar-track { height:8px; border-radius:6px; background:#e2e8f0; overflow:hidden; }
  .du-bar-fill { height:100%; border-radius:6px; }
  .du-bar-below { background:#f59e0b; }
  .du-bar-in { background:#0d9488; }
  .du-bar-above { background:#6366f1; }

  /* ===== Data berat: grid multi-kolom (read-only) ===== */
  .du-weight-legend { display:flex; gap:14px; flex-wrap:wrap; margin-bottom:14px; }
  .du-weight-legend span { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:700; color:#475569; }
  .du-weight-legend .dot { width:8px; height:8px; border-radius:50%; }
  .du-weight-legend .dot.below { background:#f59e0b; }
  .du-weight-legend .dot.in { background:#0d9488; }
  .du-weight-legend .dot.above { background:#6366f1; }

  .du-weight-grid-scroll { overflow-x:auto; padding-bottom:6px; }
  .du-weight-grid {
    display:grid;
    gap:14px;
    align-items:start;
  }

  .du-weight-column {
    display:flex;
    flex-direction:column;
    gap:8px;
  }

  .du-weight-row {
    display:grid; grid-template-columns:34px 1fr; align-items:stretch;
    border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; background:#fff;
  }
  .du-weight-row .cell { padding:8px 4px; font-size:13px; text-align:center; display:flex; align-items:center; justify-content:center; }
  .du-weight-row .seq { color:#94a3b8; font-weight:700; border-right:1px solid #eef2f7; }
  .du-weight-row .val { gap:6px; font-weight:800; color:#0f172a; }
  .du-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
  .du-dot.below { background:#f59e0b; }
  .du-dot.in { background:#0d9488; }
  .du-dot.above { background:#6366f1; }

  .du-empty-w { padding:24px; text-align:center; color:#94a3b8; font-size:13px; }

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

    <a href="{{ route('daily-uniformities.index') }}" class="du-back">&larr; Kembali ke Daftar</a>

    <div class="du-head">
      <div>
        <div class="du-title">Laporan Daily Uniformity</div>
        <div class="du-sub">{{ $daily->monitorControl->sppa_no ?? '-' }} · {{ $daily->monitorControl->report_code ?? '-' }} · {{ optional($daily->monitorControl->process_date)->format('d/m/Y') }}</div>
      </div>
      <div class="du-actions-row">
        <a href="{{ route('daily-uniformities.edit', $daily) }}" class="du-btn du-btn-ghost">Edit</a>
        <form action="{{ route('daily-uniformities.destroy', $daily) }}" method="POST" onsubmit="return confirm('Hapus laporan ini beserta seluruh data berat sampling?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="du-btn du-btn-danger">Hapus</button>
        </form>
      </div>
    </div>

    {{-- ============ BARIS ATAS: 2 KARTU SEJAJAR ============ --}}
    <div class="du-top-grid">

      <div class="du-card">
        <div class="du-card-title">Data Truk</div>
        <div class="du-info-grid">
          <div class="du-info-item"><div class="lbl">Tanggal</div><div class="val">{{ optional($daily->monitorControl->process_date)->format('d/m/Y') }}</div></div>
          <div class="du-info-item"><div class="lbl">Shift</div><div class="val">{{ ucfirst($daily->shift) }}</div></div>
          <div class="du-info-item"><div class="lbl">No. SPPA</div><div class="val">{{ $daily->monitorControl->sppa_no ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">Nama Farm</div><div class="val">{{ $daily->monitorControl->farm->name ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">No. Polisi</div><div class="val">{{ $daily->monitorControl->plateNumber->plate_number ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">Ekspedisi</div><div class="val">{{ $daily->monitorControl->expedition->name ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">Sopir</div><div class="val">{{ $daily->driverName() ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">ABW</div><div class="val">{{ $daily->monitorControl->abw ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">Jumlah Ayam Diterima</div><div class="val">{{ number_format((int)($daily->monitorControl->ayam_diterima ?? 0)) }} ekor</div></div>
          <div class="du-info-item"><div class="lbl">Uniformity (Size)</div><div class="val">{{ $daily->monitorControl->size ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">Rata-rata RPA</div><div class="val">{{ $daily->avg_rpa ?? '-' }}</div></div>
          <div class="du-info-item"><div class="lbl">Berat RPA</div><div class="val">{{ $daily->berat_rpa ?? '-' }}</div></div>
        </div>
      </div>

      <div class="du-card">
        <div class="du-card-title">Ringkasan Sampling</div>
        <div class="du-sum-grid">
          <div class="du-sum-box"><div class="lbl">Jumlah Sampling</div><div class="val">{{ $summary['count'] }} ekor</div></div>
          <div class="du-sum-box"><div class="lbl">Total Berat</div><div class="val">{{ number_format($summary['total'], 3) }} kg</div></div>
          <div class="du-sum-box"><div class="lbl">Berat Terkecil</div><div class="val">{{ $summary['min'] !== null ? number_format($summary['min'], 3) : '-' }}</div></div>
          <div class="du-sum-box"><div class="lbl">Berat Terbesar</div><div class="val">{{ $summary['max'] !== null ? number_format($summary['max'], 3) : '-' }}</div></div>
        </div>
        <div class="du-sum-box" style="margin-bottom:18px;">
          <div class="lbl">Rata-rata Berat</div>
          <div class="val">{{ $summary['avg'] !== null ? number_format($summary['avg'], 3) . ' kg' : '-' }}</div>
        </div>

        <div class="du-card-title" style="margin-top:4px;">
          Sebaran Terhadap Range Uniformity
          @if($summary['range_low'] !== null)
            <span style="color:#94a3b8; font-weight:600; text-transform:none;">({{ $summary['range_low'] }} - {{ $summary['range_high'] }})</span>
          @endif
        </div>

        <div class="du-range-row">
          <div class="row-top"><span>Dibawah Range</span><span>{{ $summary['below']['count'] }} ekor · {{ $summary['below']['pct'] }}%</span></div>
          <div class="du-bar-track"><div class="du-bar-fill du-bar-below" style="width:{{ $summary['below']['pct'] }}%;"></div></div>
        </div>
        <div class="du-range-row">
          <div class="row-top"><span>Didalam Range</span><span>{{ $summary['in_range']['count'] }} ekor · {{ $summary['in_range']['pct'] }}%</span></div>
          <div class="du-bar-track"><div class="du-bar-fill du-bar-in" style="width:{{ $summary['in_range']['pct'] }}%;"></div></div>
        </div>
        <div class="du-range-row">
          <div class="row-top"><span>Diatas Range</span><span>{{ $summary['above']['count'] }} ekor · {{ $summary['above']['pct'] }}%</span></div>
          <div class="du-bar-track"><div class="du-bar-fill du-bar-above" style="width:{{ $summary['above']['pct'] }}%;"></div></div>
        </div>
      </div>

    </div>

    {{-- ============ DATA BERAT SAMPLING: GRID MULTI-KOLOM (READ-ONLY) ============ --}}
    <div class="du-card">
      <div class="du-card-title">Data Berat Sampling ({{ $daily->weights->count() }} ekor)</div>

      @php $low = $summary['range_low']; $high = $summary['range_high']; @endphp
      @php
        $weightCount = $daily->weights->count();
        $weightChunkSize = max(1, (int) ceil($weightCount / 5));
        $weightColumns = $daily->weights->chunk($weightChunkSize);
      @endphp

      @if ($daily->weights->isNotEmpty())
        <div class="du-weight-legend">
          <span><span class="dot below"></span> Dibawah Range</span>
          <span><span class="dot in"></span> Didalam Range</span>
          <span><span class="dot above"></span> Diatas Range</span>
        </div>

        <div class="du-weight-grid-scroll">
          <div class="du-weight-grid" style="grid-template-columns: repeat({{ max(1, min($weightColumns->count(), 5)) }}, minmax(170px, 1fr));">
            @forelse ($weightColumns as $column)
              <div class="du-weight-column">
                @foreach ($column as $w)
                  @php
                    $wv = (float) $w->weight_kg;
                    if ($low !== null && $wv < $low) { $cat = 'below'; }
                    elseif ($high !== null && $wv > $high) { $cat = 'above'; }
                    else { $cat = 'in'; }
                  @endphp
                  <div class="du-weight-row">
                    <div class="cell seq">{{ $w->sequence }}</div>
                    <div class="cell val"><span class="du-dot {{ $cat }}"></span>{{ number_format($wv, 3) }}</div>
                  </div>
                @endforeach
              </div>
            @endforeach
          </div>
        </div>
      @else
        <div class="du-empty-w">Belum ada data berat sampling.</div>
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
@endsection