@extends('layouts.app')

@section('content')

@php
$condColor = function(?string $val): string {
    return match($val) {
        'kering', 'bak_kering'              => 'green',
        'basah', 'medium_basah'             => 'yellow',
        'bak_berisi_air', 'benda_lain'      => 'orange',
        'sangat_basah'                      => 'red',
        default                             => 'neutral',
    };
};
$condLabel = function(?string $val): string {
    return match($val) {
        'sangat_basah'   => 'Sangat Basah',
        'medium_basah'   => 'Medium Basah',
        'basah'          => 'Basah',
        'kering'         => 'Kering',
        'bak_berisi_air' => 'Bak berisi air',
        'bak_kering'     => 'Bak kering',
        'benda_lain'     => 'Benda lain-lain',
        default          => $val ?? '—',
    };
};

$customCaps = ['SH01' => [17 => 46], 'SH02' => [30 => 19]];
$location = $mc->location ?? '';
$normalSetCount = 0;
$customSetCounts = [19 => 0, 46 => 0];
$totalKosongCalc = 0;
$totalAyamCap = 0;

foreach ($form->lines as $line) {
    $cap = $customCaps[$location][$line->line_no] ?? 50;
    foreach ($line->sets as $set) {
        if ($set->empty_count === null) continue;
        $empty = (int) $set->empty_count;
        $totalKosongCalc += $empty;
        $totalAyamCap += ($cap - $empty);
        if ($cap === 50) { $normalSetCount++; }
        else { $customSetCounts[$cap] = ($customSetCounts[$cap] ?? 0) + 1; }
    }
}

$normalEkor  = $normalSetCount * 50;
$customEkor  = 0;
foreach ($customSetCounts as $cap => $count) { $customEkor += ($count * $cap); }

$dead   = (int)($form->dead_count ?? 0);
$retur  = (int)($form->retur_count ?? 0);
$totalAyamTerimaCalc = max(0, $totalAyamCap - $dead - $retur);
$totalEkorMC = (int)($mc->total_chicken ?? 0);
$selisih = $totalEkorMC - $totalAyamTerimaCalc;
$isMatch = ($selisih === 0);

$condLevel = function(?string $val): int {
    return match($val) {
        'kering', 'bak_kering'         => 1,
        'basah', 'medium_basah'        => 2,
        'bak_berisi_air', 'benda_lain' => 3,
        'sangat_basah'                 => 4,
        default                        => 0,
    };
};
$bc = $form->basket_condition;
$tc = $form->truck_platform_condition;
$fc = $form->feather_condition;
@endphp

<div class="sm-page">

  {{-- ══ HEADER ══ --}}
  <div class="sm-header">
    <div class="sm-header-left">
      <div class="sm-header-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="9" y1="13" x2="15" y2="13"/>
          <line x1="9" y1="17" x2="13" y2="17"/>
        </svg>
      </div>
      <div>
        <p class="sm-eyebrow">Monitor Control</p>
        <h1 class="sm-title">Data Summary</h1>
      </div>
    </div>

    <div class="sm-header-tags">
      <span class="sm-tag sm-tag-code">{{ $mc->report_code }}</span>
      <span class="sm-tag sm-tag-loc">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 21s7-4.5 7-10a7 7 0 0 0-14 0c0 5.5 7 10 7 10z"/><circle cx="12" cy="11" r="2"/></svg>
        {{ $mc->location }}
      </span>
      <span class="sm-tag sm-tag-truck">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        Truk #{{ $mc->truck_no }}
      </span>
    </div>

    <div class="sm-header-actions">
      <a class="sm-btn-export" target="_blank" href="{{ route('monitor-controls.summary.pdf', $mc) }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export PDF
      </a>
      <a class="sm-btn-back" href="{{ route('monitor-controls.index') }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
      </a>
    </div>
  </div>

  {{-- ══ TWO-COLUMN GRID ══ --}}
  {{-- Kedua kolom menggunakan display:flex flex-direction:column
       sehingga card di dalamnya bisa stretch seimbang --}}
  <div class="sm-grid">

    {{-- ── KOLOM KIRI ── --}}
    <div class="sm-col">

      {{-- Card: DTA --}}
      <div class="sm-card sm-card-stretch">
        <div class="sm-card-head sm-head-teal">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 8h8"/><path d="M8 12h5"/></svg>
          DTA
        </div>

        <div class="sm-kv-group">
          <div class="sm-kv">
            <span class="sm-kv-key">Tanggal</span>
            <span class="sm-kv-val">{{ $mc->process_date?->format('d/m/Y') ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Shift</span>
            <span class="sm-kv-val"><span class="sm-chip sm-chip-blue">{{ strtoupper($mc->shift) }}</span></span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Size</span>
            <span class="sm-kv-val"><span class="sm-chip sm-chip-violet">{{ $mc->size }}</span></span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Farm</span>
            <span class="sm-kv-val">{{ $mc->farm?->name ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Ekspedisi</span>
            <span class="sm-kv-val">{{ $mc->expedition?->name ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">No Polisi</span>
            <span class="sm-kv-val"><span class="sm-chip sm-chip-mono">{{ $mc->plateNumber?->plate_number ?? '—' }}</span></span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">No Segel</span>
            <span class="sm-kv-val">{{ $mc->seal_no ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Jam Truk Datang</span>
            <span class="sm-kv-val">
              @if($mc->truck_arrival_time)
                {{ is_string($mc->truck_arrival_time) ? \Carbon\Carbon::parse($mc->truck_arrival_time)->format('H:i') : $mc->truck_arrival_time->format('H:i') }}
              @else —
              @endif
            </span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Tgl Tangkap</span>
            <span class="sm-kv-val">{{ $mc->catch_date?->format('d/m/Y') ?? '—' }}</span>
          </div>
        </div>

        <div class="sm-rule"></div>

        {{-- Stats trio --}}
        <div class="sm-stats-trio">
          <div class="sm-stat">
            <div class="sm-stat-num">{{ number_format((int)($mc->total_chicken ?? 0)) }}</div>
            <div class="sm-stat-lbl">Total Ekor</div>
          </div>
          <div class="sm-stat">
            <div class="sm-stat-num">{{ number_format((float)($mc->total_kilo ?? 0), 2) }}</div>
            <div class="sm-stat-lbl">Total Kilo (Kg)</div>
          </div>
          <div class="sm-stat">
            <div class="sm-stat-num">{{ number_format((float)($mc->abw ?? 0), 2) }}</div>
            <div class="sm-stat-lbl">ABW</div>
          </div>
        </div>

        <div class="sm-rule"></div>

        <div class="sm-kv-group">
          <div class="sm-kv">
            <span class="sm-kv-key">No SPPA</span>
            <span class="sm-kv-val">{{ $mc->sppa_no ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Order ID</span>
            <span class="sm-kv-val">{{ $mc->order_id ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Tanggal SPPA</span>
            <span class="sm-kv-val">{{ $mc->sppa_date?->format('d/m/Y') ?? '—' }}</span>
          </div>
        </div>
      </div>

    </div>{{-- /kol kiri --}}

    {{-- ── KOLOM KANAN ── --}}
    <div class="sm-col">

      {{-- ★ HIGHLIGHT 1: Ringkasan Perhitungan Ayam --}}
      <div class="sm-card sm-card-highlight sm-card-calc">
        <div class="sm-card-head sm-head-emerald">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 3v18h18"/><path d="M7 14l3-3 4 4 6-7"/></svg>
          Ringkasan Perhitungan Ayam
        </div>

        <div class="sm-kv-group">
          <div class="sm-kv">
            <span class="sm-kv-key">Total Ekor</span>
            <span class="sm-kv-val sm-val-bold">{{ number_format($totalEkorMC) }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Blok Terisi Penuh</span>
            <span class="sm-kv-val sm-val-mono">{{ $normalSetCount }} × 50 = {{ number_format($normalEkor) }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Kondisional Blok</span>
            <span class="sm-kv-val sm-val-mono">
              @if(($customSetCounts[19] ?? 0) > 0){{ $customSetCounts[19] }}×19 @endif
              @if(($customSetCounts[46] ?? 0) > 0){{ ($customSetCounts[19] ?? 0) > 0 ? '+ ' : '' }}{{ $customSetCounts[46] }}×46 @endif
              = {{ number_format($customEkor) }}
            </span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Shackle Kosong</span>
            <span class="sm-kv-val sm-val-muted">{{ number_format($totalKosongCalc) }}</span>
          </div>
        </div>

        {{-- Total hero row --}}
        <div class="sm-total-row">
          <span class="sm-total-label">Jumlah Ayam Diterima</span>
          <span class="sm-total-val">{{ number_format($totalAyamTerimaCalc) }}</span>
        </div>

        <div class="sm-kv-group" style="margin-top:10px">
          <div class="sm-kv">
            <span class="sm-kv-key">Status</span>
            <span class="sm-kv-val">
              <span class="sm-status {{ $isMatch ? 'sm-status-match' : 'sm-status-diff' }}">
                @if($isMatch)
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  MATCH
                @else
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  SELISIH {{ $selisih > 0 ? '(KELEBIHAN)' : '(KEKURANGAN)' }}
                @endif
              </span>
            </span>
          </div>
          @if(!$isMatch)
          <div class="sm-kv">
            <span class="sm-kv-key">Selisih</span>
            <span class="sm-kv-val sm-val-warn">{{ number_format($selisih) }}</span>
          </div>
          @endif
        </div>
      </div>

      {{-- Card: Hanging --}}
      <div class="sm-card">
        <div class="sm-card-head sm-head-slate">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="2" x2="12" y2="6"/><path d="M12 6a6 6 0 0 1 6 6v6H6v-6a6 6 0 0 1 6-6z"/></svg>
          Hanging
        </div>
        <div class="sm-kv-group">
          <div class="sm-kv">
            <span class="sm-kv-key">Jam Bongkar</span>
            <span class="sm-kv-val">{{ $form->unloading_time?->format('H:i') ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Jam Selesai</span>
            <span class="sm-kv-val">{{ $form->finish_time?->format('H:i') ?? '—' }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Total Shackle Kosong</span>
            <span class="sm-kv-val sm-val-green">{{ $totalKosong }}</span>
          </div>
          <div class="sm-kv">
            <span class="sm-kv-key">Jumlah Ayam Diterima</span>
            <span class="sm-kv-val sm-val-green">{{ $totalAyamTerima }}</span>
          </div>
        </div>
      </div>

      {{-- ★ HIGHLIGHT 2: Ayam Retur & Mati --}}
      <div class="sm-card sm-card-highlight sm-card-retur">
        <div class="sm-card-head sm-head-rose">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.8"/></svg>
          Ayam Retur &amp; Mati
        </div>
        <div class="sm-retur-trio">
          <div class="sm-retur-item sm-retur-dead">
            <div class="sm-retur-num">{{ (int)($form->dead_count ?? 0) }}</div>
            <div class="sm-retur-lbl">Ayam Mati</div>
          </div>
          <div class="sm-retur-item sm-retur-ret">
            <div class="sm-retur-num">{{ (int)($form->retur_count ?? 0) }}</div>
            <div class="sm-retur-lbl">Ayam Retur</div>
          </div>
          <div class="sm-retur-item sm-retur-kg">
            <div class="sm-retur-num">{{ number_format((float)($form->retur_total_kg ?? 0), 2) }}</div>
            <div class="sm-retur-lbl">Berat Retur (Kg)</div>
          </div>
        </div>
      </div>

      {{-- ★ HIGHLIGHT 3: QC Kondisi --}}
      @php
        $segments = [
          ['color' => '#22c55e', 'lv' => 1],
          ['color' => '#eab308', 'lv' => 2],
          ['color' => '#f97316', 'lv' => 3],
          ['color' => '#ef4444', 'lv' => 4],
        ];
        $qcItems = [
          ['label' => 'Keranjang',      'val' => $bc],
          ['label' => 'Platform Truck', 'val' => $tc],
          ['label' => 'Bulu Ayam',      'val' => $fc],
        ];
        $qcValColor = [
          'green'   => '#15803d',
          'yellow'  => '#a16207',
          'orange'  => '#c2410c',
          'red'     => '#b91c1c',
          'neutral' => '#6b7280',
        ];
        $qcBorder = [
          'green'   => '#bbf7d0',
          'yellow'  => '#fef08a',
          'orange'  => '#fed7aa',
          'red'     => '#fecaca',
          'neutral' => '#e5e7eb',
        ];
        $qcBg = [
          'green'   => 'linear-gradient(145deg, #f0fdf4, #dcfce7)',
          'yellow'  => 'linear-gradient(145deg, #fefce8, #fef9c3)',
          'orange'  => 'linear-gradient(145deg, #fff7ed, #ffedd5)',
          'red'     => 'linear-gradient(145deg, #fff1f2, #fee2e2)',
          'neutral' => 'linear-gradient(145deg, #f9fafb, #f3f4f6)',
        ];
      @endphp

      <div class="sm-card sm-card-highlight sm-card-qc">
        <div class="sm-card-head sm-head-violet">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
          QC Kondisi
        </div>

        <div class="sm-qc-grid">
          @foreach($qcItems as $qc)
            @php
              $color  = $condColor($qc['val']);
              $lv     = $condLevel($qc['val']);
              $lbl    = $condLabel($qc['val']);
            @endphp
            <div class="sm-qc-card"
                 style="background:{{ $qcBg[$color] }};border-color:{{ $qcBorder[$color] }}">
              <div class="sm-qc-name">{{ $qc['label'] }}</div>

              {{-- Segmented gauge --}}
              <div class="sm-qc-gauge">
                @foreach($segments as $seg)
                  @php
                    $isActive = $lv === $seg['lv'];
                    $isPast   = $lv > $seg['lv'] && $lv > 0;
                  @endphp
                  <div class="sm-gauge-seg {{ $isActive ? 'is-active' : ($isPast ? 'is-past' : 'is-dim') }}"
                       style="background:{{ $seg['color'] }};
                              {{ $isActive ? 'box-shadow:0 2px 8px '.$seg['color'].'55;' : '' }}"></div>
                @endforeach
              </div>

              <div class="sm-qc-val" style="color:{{ $qcValColor[$color] }}">
                {{ $lbl }}
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>{{-- /kol kanan --}}
  </div>{{-- /grid --}}
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

/* ══════════════════════════════════════
   TOKENS
══════════════════════════════════════ */
:root {
  --sm-bg:        #f4f6f9;
  --sm-surface:   #ffffff;
  --sm-border:    #e8ecf1;
  --sm-border2:   #d1d9e0;
  --sm-text:      #111827;
  --sm-text2:     #4b5563;
  --sm-text3:     #9ca3af;
  --sm-radius:    14px;
  --sm-radius-sm: 9px;
  --sm-font:      'Plus Jakarta Sans', system-ui, sans-serif;

  /* accent palette */
  --sm-teal:      #0d9488;
  --sm-emerald:   #059669;
  --sm-rose:      #e11d48;
  --sm-violet:    #7c3aed;
  --sm-slate:     #475569;
  --sm-amber:     #d97706;
}

/* ══ PAGE ══ */
.sm-page {
  font-family: var(--sm-font);
  max-width: 1140px;
  margin: 0 auto;
  padding: 24px 20px 40px;
  background: var(--sm-bg);
  min-height: 100vh;
  color: var(--sm-text);
}

/* ══ HEADER ══ */
.sm-header {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
  background: var(--sm-surface);
  border: 1px solid var(--sm-border);
  border-radius: 16px;
  padding: 16px 20px;
  margin-bottom: 20px;
  box-shadow: 0 1px 4px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.04);
}

.sm-header-left {
  display: flex; align-items: center; gap: 12px; flex: 1; min-width: 200px;
}

.sm-header-icon {
  width: 44px; height: 44px; border-radius: 11px; flex-shrink: 0;
  background: linear-gradient(135deg, #0d9488, #059669);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  box-shadow: 0 4px 12px rgba(5,150,105,.25);
}

.sm-eyebrow {
  font-size: .7rem; font-weight: 700; letter-spacing: .1em;
  text-transform: uppercase; color: var(--sm-teal);
  margin: 0 0 2px;
}
.sm-title {
  font-size: 1.35rem; font-weight: 800; margin: 0;
  color: var(--sm-text); letter-spacing: -.01em;
}

.sm-header-tags {
  display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.sm-tag {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 6px;
  font-size: .72rem; font-weight: 700;
  border: 1px solid;
}
.sm-tag-code {
  background: #f1f5f9; border-color: #cbd5e1;
  color: #334155; font-variant-numeric: tabular-nums;
}
.sm-tag-loc  { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.sm-tag-truck{ background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }

.sm-header-actions { display: flex; gap: 8px; }

.sm-btn-export {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 18px; border-radius: 9px;
  font-size: .8rem; font-weight: 700;
  background: linear-gradient(135deg, #0d9488, #059669);
  color: #fff; text-decoration: none;
  box-shadow: 0 2px 10px rgba(5,150,105,.3);
  transition: transform .15s, box-shadow .15s, filter .15s;
  font-family: var(--sm-font);
}
.sm-btn-export:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 18px rgba(5,150,105,.4);
  filter: brightness(1.05);
}
.sm-btn-back {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 16px; border-radius: 9px;
  font-size: .8rem; font-weight: 700;
  background: #fff; color: var(--sm-text2);
  border: 1.5px solid var(--sm-border2);
  text-decoration: none;
  transition: border-color .15s, color .15s, background .15s;
  font-family: var(--sm-font);
}
.sm-btn-back:hover {
  border-color: #94a3b8; color: var(--sm-text); background: #f8fafc;
}

/* ══ GRID — seimbang kiri kanan ══ */
.sm-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  align-items: start;        /* kolom mulai dari atas */
}
@media (max-width: 860px) { .sm-grid { grid-template-columns: 1fr; } }

.sm-col {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* card stretch: kolom kiri satu card penuh mengisi tinggi kanan */
.sm-card-stretch {
  flex: 1;                   /* tumbuh mengisi sisa ruang di kolom */
}

/* ══ CARD ══ */
.sm-card {
  background: var(--sm-surface);
  border: 1px solid var(--sm-border);
  border-radius: var(--sm-radius);
  padding: 0;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.04);
  transition: box-shadow .2s;
}
.sm-card:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.06);
}

/* HIGHLIGHT cards */
.sm-card-calc  { border-top: 3px solid #059669; }
.sm-card-retur { border-top: 3px solid #e11d48; }
.sm-card-qc    { border-top: 3px solid #7c3aed; }

/* ══ CARD HEAD ══ */
.sm-card-head {
  display: flex; align-items: center; gap: 8px;
  padding: 12px 16px 10px;
  font-size: .73rem; font-weight: 800;
  letter-spacing: .08em; text-transform: uppercase;
  border-bottom: 1px solid var(--sm-border);
}

/* head color variants */
.sm-head-teal    { color: var(--sm-teal);    background: linear-gradient(90deg, #f0fdfa, #fff); }
.sm-head-emerald { color: var(--sm-emerald); background: linear-gradient(90deg, #f0fdf4, #fff); }
.sm-head-rose    { color: var(--sm-rose);    background: linear-gradient(90deg, #fff1f2, #fff); }
.sm-head-violet  { color: var(--sm-violet);  background: linear-gradient(90deg, #faf5ff, #fff); }
.sm-head-slate   { color: var(--sm-slate);   background: linear-gradient(90deg, #f8fafc, #fff); }

/* ══ KV ROWS ══ */
.sm-kv-group {
  padding: 4px 16px 8px;
  display: flex; flex-direction: column;
}
.sm-kv {
  display: flex; justify-content: space-between; align-items: center;
  gap: 16px; padding: 7px 0;
  border-bottom: 1px solid #f1f5f9;
  font-size: .83rem;
}
.sm-kv:last-child { border-bottom: none; }

.sm-kv-key { color: var(--sm-text2); font-weight: 500; flex-shrink: 0; }
.sm-kv-val { color: var(--sm-text); font-weight: 700; text-align: right; }

/* value modifiers */
.sm-val-bold  { font-size: .9rem; font-weight: 800; }
.sm-val-mono  { font-variant-numeric: tabular-nums; color: var(--sm-teal); }
.sm-val-muted { color: var(--sm-text3); }
.sm-val-green { color: var(--sm-emerald); font-weight: 800; }
.sm-val-warn  { color: var(--sm-amber); font-weight: 800; }

/* ══ CHIPS ══ */
.sm-chip {
  display: inline-flex; align-items: center;
  padding: 2px 9px; border-radius: 5px;
  font-size: .73rem; font-weight: 800; border: 1px solid;
}
.sm-chip-blue   { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
.sm-chip-violet { background: #faf5ff; border-color: #ddd6fe; color: #6d28d9; }
.sm-chip-mono   { background: #f8fafc; border-color: #cbd5e1; color: #334155; font-variant-numeric: tabular-nums; }

/* ══ RULE ══ */
.sm-rule { height: 1px; background: var(--sm-border); margin: 0 16px; }

/* ══ STATS TRIO ══ */
.sm-stats-trio {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 8px; padding: 10px 16px;
}
.sm-stat {
  background: linear-gradient(145deg, #f0fdf4, #dcfce7);
  border: 1px solid #bbf7d0;
  border-radius: var(--sm-radius-sm);
  padding: 10px 8px; text-align: center;
}
.sm-stat-num {
  font-size: 1.05rem; font-weight: 800;
  color: #065f46; letter-spacing: -.01em;
  font-variant-numeric: tabular-nums;
}
.sm-stat-lbl {
  font-size: .62rem; font-weight: 700; color: var(--sm-text3);
  margin-top: 2px; text-transform: uppercase; letter-spacing: .05em;
}

/* ══ TOTAL HERO ROW ══ */
.sm-total-row {
  display: flex; justify-content: space-between; align-items: center;
  margin: 8px 16px 0;
  padding: 12px 14px;
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  border: 1.5px solid #6ee7b7;
  border-radius: var(--sm-radius-sm);
}
.sm-total-label {
  font-size: .82rem; font-weight: 700; color: #065f46;
}
.sm-total-val {
  font-size: 1.4rem; font-weight: 800;
  color: #047857; letter-spacing: -.02em;
  font-variant-numeric: tabular-nums;
}

/* ══ STATUS BADGE ══ */
.sm-status {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 12px; border-radius: 20px;
  font-size: .75rem; font-weight: 800; border: 1.5px solid;
}
.sm-status-match {
  background: #f0fdf4; border-color: #86efac; color: #166534;
}
.sm-status-diff {
  background: #fffbeb; border-color: #fcd34d; color: #92400e;
}

/* ══ RETUR TRIO ══ */
.sm-retur-trio {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 10px; padding: 12px 16px;
}
.sm-retur-item {
  border-radius: var(--sm-radius-sm);
  border: 1.5px solid;
  padding: 12px 10px; text-align: center;
}
.sm-retur-dead {
  background: linear-gradient(145deg, #fff1f2, #ffe4e6);
  border-color: #fecdd3;
}
.sm-retur-dead .sm-retur-num { color: #be123c; }

.sm-retur-ret {
  background: linear-gradient(145deg, #fffbeb, #fef3c7);
  border-color: #fde68a;
}
.sm-retur-ret .sm-retur-num { color: #92400e; }

.sm-retur-kg {
  background: linear-gradient(145deg, #eff6ff, #dbeafe);
  border-color: #bfdbfe;
}
.sm-retur-kg .sm-retur-num { color: #1e40af; }

.sm-retur-num {
  font-size: 1.5rem; font-weight: 800;
  letter-spacing: -.02em; font-variant-numeric: tabular-nums;
  line-height: 1;
}
.sm-retur-lbl {
  font-size: .64rem; font-weight: 700; color: var(--sm-text3);
  margin-top: 5px; text-transform: uppercase; letter-spacing: .06em;
}

/* ══ QC GRID ══ */
.sm-qc-grid {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 10px; padding: 12px 16px;
}
.sm-qc-card {
  border: 1.5px solid;
  border-radius: var(--sm-radius-sm);
  padding: 12px 8px 10px;
  text-align: center;
  transition: transform .15s, box-shadow .15s;
}
.sm-qc-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(0,0,0,.08);
}

.sm-qc-name {
  font-size: .64rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: .08em;
  color: var(--sm-text3); margin-bottom: 10px;
}

/* Segmented gauge */
.sm-qc-gauge {
  display: flex; align-items: flex-end; justify-content: center;
  gap: 4px; height: 26px; margin-bottom: 9px;
}
.sm-gauge-seg {
  width: 16px; border-radius: 4px; flex-shrink: 0;
  transition: height .25s cubic-bezier(.34,1.56,.64,1), opacity .2s;
}
.sm-gauge-seg.is-active { height: 22px; border-radius: 5px; opacity: 1; }
.sm-gauge-seg.is-past   { height: 13px; opacity: .4; }
.sm-gauge-seg.is-dim    { height: 7px;  opacity: .2; }

.sm-qc-val {
  font-size: .8rem; font-weight: 800; line-height: 1.2;
}
</style>
@endsection