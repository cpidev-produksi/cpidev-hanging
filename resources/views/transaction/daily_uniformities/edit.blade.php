@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

  .du-root { font-family:'Plus Jakarta Sans',sans-serif; background:#f8fafc; min-height:100vh; padding:32px; }
  .du-wrap { max-width:1180px; margin:0 auto; }

  .du-back { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#64748b; text-decoration:none; margin-bottom:16px; }
  .du-title { font-size:22px; font-weight:800; color:#0f172a; letter-spacing:-.2px; margin-bottom:4px; }
  .du-sub { font-size:13px; color:#64748b; margin-bottom:20px; }

  .du-alert { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:10px 16px; border-radius:12px; font-size:13px; font-weight:600; margin-bottom:16px; }
  .du-alert-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:10px 16px; border-radius:12px; font-size:13px; font-weight:600; margin-bottom:16px; }

  /* ===== Baris atas: 2 kartu sejajar ===== */
  .du-top-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; align-items:start; }
  @media (max-width:860px) { .du-top-grid { grid-template-columns:1fr; } }

  .du-card { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:24px; margin-bottom:20px; }
  .du-card-title { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#1a56db; margin-bottom:16px; }

  .du-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 20px; }
  .du-info-item .lbl { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; }
  .du-info-item .val { font-size:14px; font-weight:700; color:#0f172a; margin-top:2px; }

  .du-field { margin-bottom:0; }
  .du-field label { display:block; font-size:12px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
  .du-field input { border:1px solid #e2e8f0; border-radius:12px; padding:11px 14px; font-family:inherit; font-size:14px; color:#0f172a; }
  .du-field input:focus { outline:none; border-color:#1a56db; box-shadow:0 0 0 3px #dbeafe; }

  .du-inline-form { display:flex; gap:10px; align-items:flex-end; }
  .du-inline-form .du-field { flex:1; }
  .du-inline-form input { width:100%; }

  .du-btn { display:inline-flex; align-items:center; gap:6px; padding:11px 18px; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none; border:none; cursor:pointer; white-space:nowrap; }
  .du-btn-primary { background:#1a56db; color:#fff; }
  .du-btn-primary:hover { background:#1743ab; }
  .du-btn-ghost { background:#fff; color:#0f172a; border:1px solid #e2e8f0; }

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

  /* ===== Input Berat Sampling: kolom per 20 item ===== */
  .du-weight-legend { display:flex; gap:14px; flex-wrap:wrap; margin-bottom:14px; }
  .du-weight-legend span { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:700; color:#475569; }
  .du-weight-legend .dot { width:8px; height:8px; border-radius:50%; }
  .du-weight-legend .dot.below { background:#f59e0b; }
  .du-weight-legend .dot.in { background:#0d9488; }
  .du-weight-legend .dot.above { background:#6366f1; }

  .du-weight-grid-scroll { overflow-x:auto; padding-bottom:6px; }
  .du-weight-grid { display:flex; gap:14px; min-width:max-content; align-items:flex-start; }
  .du-weight-column {
    width:220px; flex:0 0 220px; display:flex; flex-direction:column; gap:8px;
  }

  .du-weight-row {
    display:grid; grid-template-columns:34px 1fr 34px; align-items:stretch;
    border:1px solid #e2e8f0; border-radius:10px; margin-bottom:8px; overflow:hidden; background:#fff;
  }
  .du-weight-row .cell { padding:8px 4px; font-size:13px; text-align:center; display:flex; align-items:center; justify-content:center; }
  .du-weight-row .seq { color:#94a3b8; font-weight:700; border-right:1px solid #eef2f7; }
  .du-weight-row .val { gap:6px; font-weight:800; color:#0f172a; border-right:1px solid #eef2f7; }
  .du-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
  .du-dot.below { background:#f59e0b; }
  .du-dot.in { background:#0d9488; }
  .du-dot.above { background:#6366f1; }
  .du-weight-row form { display:flex; align-items:center; justify-content:center; margin:0; }
  .du-del-btn { background:none; border:none; color:#dc2626; cursor:pointer; font-size:16px; font-weight:700; line-height:1; }

  .du-weight-row.add-row { grid-template-columns:1fr 34px; border-style:dashed; border-color:#93c5fd; background:#f8fafc; }
  .du-weight-row.add-row input { border:none; background:transparent; width:100%; padding:8px 10px; font-size:13px; text-align:center; font-family:inherit; }
  .du-weight-row.add-row input:focus { outline:none; }
  .du-weight-row.add-row button { background:#1a56db; color:#fff; border:none; cursor:pointer; font-weight:900; font-size:17px; }

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
    <div class="du-title">Edit Laporan Daily Uniformity</div>
    <div class="du-sub">{{ $daily->monitorControl->sppa_no ?? '-' }} · {{ $daily->monitorControl->report_code ?? '-' }}</div>

    @if (session('status'))
      <div class="du-alert">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="du-alert-err">{{ $errors->first() }}</div>
    @endif

    {{-- ============ BARIS ATAS: 2 KARTU SEJAJAR ============ --}}
    <div class="du-top-grid">

      <div class="du-card">
        <div class="du-card-title">Data Trial Sampling Berat Live Bird</div>
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
        </div>

        <form action="{{ route('daily-uniformities.update', $daily) }}" method="POST" style="margin-top:18px; padding-top:18px; border-top:1px solid #f1f5f9;">
          @csrf
          @method('PUT')
          <div class="du-inline-form">
            <div class="du-field">
              <label for="avg_rpa">Rata-rata RPA</label>
              <input type="number" step="0.01" min="0" id="avg_rpa" name="avg_rpa" value="{{ old('avg_rpa', $daily->avg_rpa) }}">
            </div>
            <div class="du-field">
              <label for="berat_rpa">Berat RPA</label>
              <input type="number" step="0.01" min="0" id="berat_rpa" name="berat_rpa" value="{{ old('berat_rpa', $daily->berat_rpa) }}">
            </div>
            <button type="submit" class="du-btn du-btn-ghost">Simpan</button>
          </div>
        </form>
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

    {{-- ============ INPUT BERAT SAMPLING: GRID MULTI-KOLOM ============ --}}
    <div class="du-card">
      <div class="du-card-title">Input Berat Sampling (kg)</div>

      @php $low = $summary['range_low']; $high = $summary['range_high']; @endphp
      @php
        $weightCount = $daily->weights->count();
        $weightChunkSize = max(1, (int) ceil($weightCount / 5));
        $weightColumns = $daily->weights->chunk($weightChunkSize);
      @endphp

      <div class="du-weight-legend">
        <span><span class="dot below"></span> Dibawah Range</span>
        <span><span class="dot in"></span> Didalam Range</span>
        <span><span class="dot above"></span> Diatas Range</span>
      </div>

      <div class="du-weight-grid-scroll">
        <div class="du-weight-grid">
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
                  <form action="{{ route('daily-uniformities.weights.destroy', [$daily, $w]) }}" method="POST" onsubmit="return confirm('Hapus data berat ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="du-del-btn" title="Hapus">&times;</button>
                  </form>
                </div>
              @endforeach

              @if ($loop->last)
                <form action="{{ route('daily-uniformities.weights.store', $daily) }}" method="POST" class="du-weight-row add-row" id="duWeightForm">
                  @csrf
                  <input type="number" step="0.001" min="0.001" max="99.999" id="weight_kg" name="weight_kg" placeholder="Tambah berat..." required>
                  <button type="submit" title="Tambah">+</button>
                </form>
              @endif
            </div>
          @empty
            <div class="du-weight-column">
              <div class="du-empty-w" style="margin:0; width:100%;">Belum ada data berat sampling.</div>
              <form action="{{ route('daily-uniformities.weights.store', $daily) }}" method="POST" class="du-weight-row add-row" id="duWeightForm">
                @csrf
                <input type="number" step="0.001" min="0.001" max="99.999" id="weight_kg" name="weight_kg" placeholder="Tambah berat..." required>
                <button type="submit" title="Tambah">+</button>
              </form>
            </div>
          @endforelse
        </div>
      </div>

      @if ($daily->weights->isEmpty())
        <div class="du-empty-w">Mulai input di kotak bertanda "+" di atas.</div>
      @endif
    </div>

    <a href="{{ route('daily-uniformities.show', $daily) }}" class="du-btn du-btn-ghost" style="width:100%; justify-content:center;">Lihat Laporan Final &rarr;</a>

  </div>
</div>

<button type="button" class="du-scrolltop" id="duScrollTop" title="Kembali ke atas">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="18 15 12 9 6 15"/>
  </svg>
</button>

<script>
  // Fokuskan kursor ke field input berat ayam setiap halaman dimuat
  // (termasuk setelah submit tambah/hapus berat yang me-reload halaman).
  document.addEventListener('DOMContentLoaded', function () {
    const weightInput = document.getElementById('weight_kg');
    if (weightInput) {
      weightInput.focus();
    }
  });

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