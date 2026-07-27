<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    /* ── BASE ── */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10.5px;
      color: #111827;
      background: #fff;
      margin: 0;
      padding: 18px 22px 18px 22px;
    }

    /* ══ KOP ══ */
    .kop {
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
      padding: 0;
      display: table;
      width: 100%;
    }
    .kop-inner { display: table-row; }
    .kop-logo-cell {
      display: table-cell; vertical-align: middle;
      width: 76px; padding: 16px 0 16px 0;
    }
    .kop-logo-cell img {
      width: 48px; height: 48px; object-fit: contain;
      border-radius: 10px;
      background: rgba(255,255,255,.2);
      padding: 4px; display: block;
    }
    .kop-text-cell {
      display: table-cell; vertical-align: middle;
      padding: 16px 0 16px 12px;
    }
    .kop-company  { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }
    .kop-tagline  { font-size: 8px; color: rgba(255,255,255,.7); letter-spacing: .1em; text-transform: uppercase; margin-top: 2px; }
    .kop-right-cell {
      display: table-cell; vertical-align: middle;
      text-align: right; padding: 16px 0 16px 0;
    }
    .kop-doc-eyebrow { font-size: 7.5px; font-weight: 700; color: rgba(255,255,255,.65); letter-spacing: .15em; text-transform: uppercase; margin-bottom: 2px; }
    .kop-doc-title   { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -.01em; line-height: 1; }

    /* accent bar */
    .kop-accent {
      height: 3px;
      background: linear-gradient(90deg, #34d399, #6ee7b7, #a7f3d0);
    }

    /* ══ META STRIP ══ */
    .meta-strip {
      background: #f0fdf4;
      border-bottom: 1px solid #bbf7d0;
      padding: 8px 0;
      display: table; width: 100%;
    }
    .meta-item { display: table-cell; padding-right: 22px; vertical-align: middle; }
    .meta-label { font-size: 7.5px; font-weight: 700; color: #6b7280; letter-spacing: .1em; text-transform: uppercase; }
    .meta-value { font-size: 10.5px; font-weight: 700; color: #065f46; margin-top: 1px; }
    .meta-badge {
      display: inline-block;
      background: #059669; color: #fff;
      padding: 2px 8px; border-radius: 4px;
      font-size: 9.5px; font-weight: 700;
    }

    /* ══ CONTENT ══ */
    .content { padding: 16px 0 10px; }

    /* ══ SECTION TITLE ══ */
    .sec-title {
      font-size: 8.5px; font-weight: 700; letter-spacing: .12em;
      text-transform: uppercase;
      padding: 4px 9px;
      margin-bottom: 7px; margin-top: 14px;
      display: table; width: 100%;
      border-radius: 0 4px 4px 0;
    }
    .sec-title:first-child { margin-top: 0; }
    .sec-title-teal   { color: #0f766e; background: #f0fdfa; border-left: 3px solid #0d9488; }
    .sec-title-slate  { color: #334155; background: #f8fafc; border-left: 3px solid #475569; }

    /* ══ TABLES ══ */
    table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    th, td { border: 1px solid #e8ecf1; padding: 6px 10px; vertical-align: top; font-size: 10px; }
    th { background: #f8fafc; color: #374151; font-weight: 700; width: 40%; border-right-color: #e2e8f0; }
    td { color: #111827; background: #fff; }
    tr:nth-child(even) td { background: #fafbfc; }
    .td-num { font-weight: 700; color: #065f46; }

    /* ══ TWO-COL ══ */
    .two-col { display: table; width: 100%; border-collapse: collapse; }
    .col-l   { display: table-cell; width: 50%; padding-right: 8px; vertical-align: top; }
    .col-r   { display: table-cell; width: 50%; padding-left: 8px; vertical-align: top; }

    /* ════════════════════════════════════════
       ★ HIGHLIGHT 1 — Ringkasan Perhitungan
    ════════════════════════════════════════ */
    .hl-calc {
      border: 1.5px solid #6ee7b7;
      border-radius: 8px; overflow: hidden;
      margin-bottom: 12px;
    }
    .hl-calc-head {
      background: linear-gradient(90deg, #059669, #0d9488);
      padding: 6px 12px; display: table; width: 100%;
    }
    .hl-calc-head-label {
      display: table-cell;
      font-size: 8.5px; font-weight: 700;
      letter-spacing: .1em; text-transform: uppercase;
      color: #fff;
    }
    .hl-calc-head-sub {
      display: table-cell; text-align: right;
      font-size: 7.5px; color: rgba(255,255,255,.6);
    }

    .hl-row { display: table; width: 100%; border-bottom: 1px solid #d1fae5; }
    .hl-row:last-child { border-bottom: none; }
    .hl-row-total   { background: #ecfdf5; }
    .hl-row-selisih { background: #fffbeb; }

    .hl-key {
      display: table-cell; padding: 6px 12px;
      width: 56%; font-size: 10px; font-weight: 500;
      color: #374151; border-right: 1px solid #d1fae5;
      vertical-align: middle;
    }
    .hl-val {
      display: table-cell; padding: 6px 12px;
      font-size: 10px; font-weight: 700; color: #065f46;
      text-align: right; vertical-align: middle;
    }
    .hl-val-hero   { font-size: 13px; font-weight: 700; color: #047857; }
    .hl-val-warn   { color: #92400e; }

    .hl-badge-match {
      display: inline-block;
      background: #dcfce7; border: 1px solid #86efac;
      color: #166534; border-radius: 20px;
      padding: 2px 9px; font-size: 8.5px; font-weight: 700;
    }
    .hl-badge-diff {
      display: inline-block;
      background: #fef9c3; border: 1px solid #fde047;
      color: #854d0e; border-radius: 20px;
      padding: 2px 9px; font-size: 8.5px; font-weight: 700;
    }

    /* ════════════════════════════════════════
       ★ HIGHLIGHT 2 — Ayam Retur & Mati
    ════════════════════════════════════════ */
    .hl-retur {
      border: 1.5px solid #fecdd3;
      border-radius: 8px; overflow: hidden;
      margin-bottom: 12px;
    }
    .hl-retur-head {
      background: linear-gradient(90deg, #be123c, #e11d48);
      padding: 6px 12px;
      font-size: 8.5px; font-weight: 700;
      letter-spacing: .1em; text-transform: uppercase;
      color: #fff;
    }
    .hl-retur-body { display: table; width: 100%; border-collapse: collapse; }
    .hl-retur-cell {
      display: table-cell; width: 33.33%;
      padding: 12px 8px; text-align: center;
      border-right: 1px solid #fecdd3;
      vertical-align: middle;
    }
    .hl-retur-cell:last-child { border-right: none; }
    .hl-retur-val-dead  { font-size: 16px; font-weight: 700; color: #be123c; line-height: 1; }
    .hl-retur-val-ret   { font-size: 16px; font-weight: 700; color: #92400e; line-height: 1; }
    .hl-retur-val-kg    { font-size: 16px; font-weight: 700; color: #1e40af; line-height: 1; }
    .hl-retur-lbl {
      font-size: 7px; font-weight: 700; color: #9ca3af;
      text-transform: uppercase; letter-spacing: .07em; margin-top: 4px;
    }

    /* ════════════════════════════════════════
       ★ HIGHLIGHT 3 — QC Kondisi
    ════════════════════════════════════════ */
    .hl-qc {
      border: 1.5px solid #ddd6fe;
      border-radius: 8px; overflow: hidden;
      margin-bottom: 12px;
    }
    .hl-qc-head {
      background: linear-gradient(90deg, #5b21b6, #7c3aed);
      padding: 6px 12px;
      font-size: 8.5px; font-weight: 700;
      letter-spacing: .1em; text-transform: uppercase;
      color: #fff;
    }
    .hl-qc-legend {
      display: table; width: 100%;
      padding: 6px 10px;
      border-bottom: 1px solid #ede9fe;
      background: #faf5ff;
    }
    .hl-qc-legend-cell { display: table-cell; padding-right: 8px; vertical-align: middle; }
    .hl-qc-legend-item {
      display: inline-block; border-radius: 4px; border: 1px solid;
      padding: 1px 6px; font-size: 7px; font-weight: 700;
    }
    .qli-green  { background:#dcfce7; border-color:#86efac; color:#166534; }
    .qli-yellow { background:#fef9c3; border-color:#fde047; color:#854d0e; }
    .qli-orange { background:#ffedd5; border-color:#fdba74; color:#9a3412; }
    .qli-red    { background:#fee2e2; border-color:#fca5a5; color:#991b1b; }

    .qc-table  { width: 100%; border-collapse: collapse; padding: 8px; }
    .qc-table td { border: none; padding: 0 5px 0 0; vertical-align: top; width: 33.33%; }
    .qc-table td:last-child { padding-right: 0; }

    .qc-cell {
      border-radius: 7px; border: 1.5px solid;
      padding: 9px 8px 8px; text-align: center;
    }
    .qc-cell-label {
      font-size: 7px; font-weight: 700; color: #6b7280;
      text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px;
    }
    .qc-bar { display: table; margin: 0 auto 6px; border-collapse: separate; border-spacing: 2px 0; }
    .qc-bar-seg { display: table-cell; width: 13px; height: 7px; border-radius: 3px; }
    .qc-bar-seg.active { height: 15px; border-radius: 4px; vertical-align: bottom; }
    .qc-bar-seg.past   { opacity: .45; }
    .qc-bar-seg.future { opacity: .18; }
    .seg-g { background: #22c55e; }
    .seg-y { background: #eab308; }
    .seg-o { background: #f97316; }
    .seg-r { background: #ef4444; }
    .qc-dot-row { text-align: center; margin-bottom: 4px; }
    .qc-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; }
    .qc-cell-val { font-size: 9.5px; font-weight: 700; }

    .qc-green  { background: #dcfce7; border-color: #86efac; }
    .qc-green  .qc-dot { background: #22c55e; }
    .qc-green  .qc-cell-val { color: #166534; }
    .qc-yellow { background: #fef9c3; border-color: #fde047; }
    .qc-yellow .qc-dot { background: #eab308; }
    .qc-yellow .qc-cell-val { color: #854d0e; }
    .qc-orange { background: #ffedd5; border-color: #fdba74; }
    .qc-orange .qc-dot { background: #f97316; }
    .qc-orange .qc-cell-val { color: #9a3412; }
    .qc-red    { background: #fee2e2; border-color: #fca5a5; }
    .qc-red    .qc-dot { background: #ef4444; }
    .qc-red    .qc-cell-val { color: #991b1b; }
    .qc-neutral{ background: #f3f4f6; border-color: #d1d5db; }
    .qc-neutral .qc-dot { background: #9ca3af; }
    .qc-neutral .qc-cell-val { color: #6b7280; }

    /* ══ SIGNATURE ══ */
    .sig-area {
      margin-top: 16px; border-top: 1.5px solid #d1fae5;
      padding-top: 12px; display: table; width: 100%;
    }
    .sig-cell { display: table-cell; width: 50%; vertical-align: top; padding-right: 14px; }
    .sig-cell:last-child { padding-right: 0; padding-left: 14px; }
    .sig-label { font-size: 8px; font-weight: 700; color: #6b7280; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 5px; }
    .sig-line  { font-size: 10px; font-weight: 700; color: #374151; }

    /* ══ FOOTER ══ */
    .footer {
      margin-top: 14px;
      background: #f8fafc;
      border-top: 1.5px solid #e2e8f0;
      padding: 8px 22px; display: table; width: 100%;
    }
    .footer-left  { display: table-cell; vertical-align: middle; font-size: 8px; color: #94a3b8; }
    .footer-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 8px; color: #94a3b8; }

    /* ══ WRAPPER QC BODY ══ */
    .hl-qc-inner { padding: 8px 8px 4px; }
  </style>
</head>
<body>

@php
$qcColorClass = function(?string $val): string {
    return match($val) {
        'kering', 'bak_kering'         => 'qc-green',
        'basah', 'medium_basah'        => 'qc-yellow',
        'bak_berisi_air', 'benda_lain' => 'qc-orange',
        'sangat_basah'                 => 'qc-red',
        default                        => 'qc-neutral',
    };
};
$qcLabel = function(?string $val): string {
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
$qcLevel = function(?string $val): int {
    return match($val) {
        'kering', 'bak_kering'         => 1,
        'basah', 'medium_basah'        => 2,
        'bak_berisi_air', 'benda_lain' => 3,
        'sangat_basah'                 => 4,
        default                        => 0,
    };
};
$segColors = ['seg-g', 'seg-y', 'seg-o', 'seg-r'];

$customCaps = ['SH02' => [30 => 16]];
$location = $mc->location ?? '';
$totalKosongCalc = 0;
$totalAyamCap = 0;

// Breakdown per kapasitas (cap), bukan per "penuh/tidak penuh", supaya
// blok cap-50 yang terisi sebagian tidak pernah hilang dari rincian.
$capGroups = [];

foreach ($form->lines as $line) {
    $cap = $customCaps[$location][$line->line_no] ?? 50;
    foreach ($line->sets as $set) {
        if ($set->empty_count === null) continue;
        $empty = (int) $set->empty_count;
        $empty = min($empty, $cap);
        $totalKosongCalc += $empty;
        $totalAyamCap += ($cap - $empty);

        if (!isset($capGroups[$cap])) {
            $capGroups[$cap] = ['count' => 0, 'empty' => 0, 'ayam' => 0];
        }
        $capGroups[$cap]['count']++;
        $capGroups[$cap]['empty'] += $empty;
        $capGroups[$cap]['ayam']  += ($cap - $empty);
    }
}
ksort($capGroups);

$dead   = (int)($form->dead_count ?? 0);
$retur  = (int)($form->retur_count ?? 0);
$totalEkorMC = (int)($mc->total_chicken ?? 0);
$targetAyam = max(0, $totalEkorMC - $dead - $retur);
$hasilShackle = $totalAyamCap;
$selisih = $totalAyamCap - $targetAyam;
$isMatch = ($selisih === 0);
$isExcess = $selisih > 0;
$isDeficit = $selisih < 0;
@endphp

  {{-- KOP --}}
  <div class="kop">
    <div class="kop-inner">
      <div class="kop-logo-cell">
        <img src="{{ public_path('images/logo small.png') }}" alt="Logo">
      </div>
      <div class="kop-text-cell">
        <div class="kop-company">PT. Charoen Pokphand Indonesia</div>
        <div class="kop-tagline">Food Division · SlaughterHouse Department</div>
      </div>
      <div class="kop-right-cell">
        <div class="kop-doc-eyebrow">Dokumen Resmi</div>
        <div class="kop-doc-title">Data Summary</div>
      </div>
    </div>
  </div>
  <div class="kop-accent"></div>

  {{-- META STRIP --}}
  <div class="meta-strip">
    <div class="meta-item">
      <div class="meta-label">Kode Report</div>
      <div class="meta-value"><span class="meta-badge">{{ $mc->report_code }}</span></div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Lokasi</div>
      <div class="meta-value">{{ $mc->location }}</div>
    </div>
    <div class="meta-item">
      <div class="meta-label">No. Truk</div>
      <div class="meta-value">#{{ $mc->truck_no }}</div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Tanggal Cetak</div>
      <div class="meta-value">{{ now()->format('d/m/Y H:i') }}</div>
    </div>
  </div>

  <div class="content">

    {{-- Kontrol Monitor --}}
    <div class="sec-title sec-title-teal">Kontrol Monitor</div>
    <div class="two-col">
      <div class="col-l">
        <table>
          <tr><th>Tanggal</th><td>{{ $mc->process_date?->format('d/m/Y') }}</td></tr>
          <tr><th>Shift</th><td>{{ strtoupper($mc->shift) }}</td></tr>
          <tr><th>Size</th><td>{{ $mc->size }}</td></tr>
          <tr><th>Farm</th><td>{{ $mc->farm?->name ?? '—' }}</td></tr>
          <tr><th>Ekspedisi</th><td>{{ $mc->expedition?->name ?? '—' }}</td></tr>
        </table>
      </div>
      <div class="col-r">
        <table>
          <tr><th>No Polisi</th><td>{{ $mc->plateNumber?->plate_number ?? '—' }}</td></tr>
          <tr><th>Total Ekor</th><td class="td-num">{{ number_format((int)($mc->total_chicken ?? 0)) }}</td></tr>
          <tr><th>Total Kilo (Kg)</th><td class="td-num">{{ number_format((float)($mc->total_kilo ?? 0), 2) }}</td></tr>
          <tr><th>ABW</th><td class="td-num">{{ number_format((float)($mc->abw ?? 0), 2) }}</td></tr>
          <tr><th>No SPPA</th><td>{{ $mc->sppa_no ?? '—' }}</td></tr>
          <tr><th>Tanggal SPPA</th><td>{{ $mc->sppa_date?->format('d/m/Y') ?? '—' }}</td></tr>
        </table>
      </div>
    </div>

    {{-- Hanging & Perhitungan --}}
    <div class="two-col" style="margin-top:2px">
      <div class="col-l">
        <div class="sec-title sec-title-slate">Hanging</div>
        <table>
          <tr><th>Jam Bongkar</th><td>{{ $form->unloading_time?->format('H:i') ?? '—' }}</td></tr>
          <tr><th>Jam Selesai</th><td>{{ $form->finish_time?->format('H:i') ?? '—' }}</td></tr>
          <tr><th>Shackle Kosong</th><td class="td-num">{{ $totalKosong }}</td></tr>
          <tr><th>Ayam Diterima</th><td class="td-num">{{ $totalAyamCap }}</td></tr>
        </table>
      </div>
      <div class="col-r">
        {{-- ★ HIGHLIGHT 1 --}}
        <div class="sec-title sec-title-teal">Ringkasan Perhitungan Ayam</div>
        <div class="hl-calc">
          <div class="hl-calc-head">
            <span class="hl-calc-head-label">Verifikasi Jumlah</span>
            <span class="hl-calc-head-sub">Otomatis</span>
          </div>
          <div class="hl-row">
            <span class="hl-key">Total Ekor</span>
            <span class="hl-val">{{ number_format($totalEkorMC) }}</span>
          </div>
          @foreach($capGroups as $cap => $g)
          <div class="hl-row">
            <span class="hl-key">{{ $cap === 50 ? 'Blok Cap 50' : 'Blok Kondisional (Cap '.$cap.')' }} ({{ $g['count'] }} blok{{ $g['empty'] > 0 ? ', kosong '.$g['empty'] : ', penuh' }})</span>
            <span class="hl-val">{{ $g['count'] }} × {{ $cap }} @if($g['empty'] > 0) − {{ $g['empty'] }} @endif = {{ number_format($g['ayam']) }}</span>
          </div>
          @endforeach
          <div class="hl-row">
            <span class="hl-key">Shackle Kosong</span>
            <span class="hl-val" style="color:#6b7280">{{ number_format($totalKosongCalc) }}</span>
          </div>
          <div class="hl-row hl-row-total">
            <span class="hl-key"><b>Ayam Diterima</b></span>
            <span class="hl-val hl-val-hero">{{ number_format($totalAyamCap) }}</span>
          </div>
          <div class="hl-row">
            <span class="hl-key">Status</span>
            <span class="hl-val">
              @if($isMatch)
                <span class="hl-badge-match">✓ MATCH</span>
              @else
                <span class="hl-badge-diff">SELISIH {{ $selisih > 0 ? '(KELEBIHAN)' : '(KEKURANGAN)' }}</span>
              @endif
            </span>
          </div>
          @if(!$isMatch)
          <div class="hl-row hl-row-selisih">
            <span class="hl-key">Selisih</span>
            <span class="hl-val hl-val-warn">{{ number_format($selisih) }}</span>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ★ HIGHLIGHT 2: Retur & Mati --}}
    <div class="hl-retur">
      <div class="hl-retur-head">Ayam Retur &amp; Mati</div>
      <div class="hl-retur-body">
        <div class="hl-retur-cell">
          <div class="hl-retur-val-dead">{{ (int)($form->dead_count ?? 0) }}</div>
          <div class="hl-retur-lbl">Ayam Mati</div>
        </div>
        <div class="hl-retur-cell">
          <div class="hl-retur-val-ret">{{ (int)($form->retur_count ?? 0) }}</div>
          <div class="hl-retur-lbl">Ayam Retur</div>
        </div>
        <div class="hl-retur-cell">
          <div class="hl-retur-val-kg">{{ number_format((float)($form->retur_total_kg ?? 0), 2) }}</div>
          <div class="hl-retur-lbl">Berat Retur (Kg)</div>
        </div>
      </div>
    </div>

    {{-- ★ HIGHLIGHT 3: QC Kondisi --}}
    <div class="hl-qc">
      <div class="hl-qc-head">QC Kondisi</div>
      {{-- <div class="hl-qc-legend">
        <div class="hl-qc-legend-cell"><span class="hl-qc-legend-item qli-green">● Baik (Kering)</span></div>
        <div class="hl-qc-legend-cell"><span class="hl-qc-legend-item qli-yellow">● Perlu Perhatian (Basah)</span></div>
        <div class="hl-qc-legend-cell"><span class="hl-qc-legend-item qli-orange">● Kurang Baik</span></div>
        <div class="hl-qc-legend-cell"><span class="hl-qc-legend-item qli-red">● Buruk (Sangat Basah)</span></div> --}}
      </div>
      <div class="hl-qc-inner">
        <table class="qc-table">
          <tr>
            @foreach([
              ['label' => 'Keranjang',      'val' => $form->basket_condition],
              ['label' => 'Platform Truck', 'val' => $form->truck_platform_condition],
              ['label' => 'Bulu Ayam',      'val' => $form->feather_condition],
            ] as $qcItem)
              @php $lv = $qcLevel($qcItem['val']); @endphp
              <td>
                <div class="qc-cell {{ $qcColorClass($qcItem['val']) }}">
                  <div class="qc-cell-label">{{ $qcItem['label'] }}</div>
                  <table class="qc-bar">
                    <tr>
                      @for($i = 1; $i <= 4; $i++)
                        @php
                          if ($lv === $i)   $segState = 'active';
                          elseif ($lv > $i) $segState = 'past';
                          else              $segState = 'future';
                        @endphp
                        <td class="qc-bar-seg {{ $segColors[$i-1] }} {{ $segState }}"></td>
                      @endfor
                    </tr>
                  </table>
                  <div class="qc-dot-row"><span class="qc-dot"></span></div>
                  <div class="qc-cell-val">{{ $qcLabel($qcItem['val']) }}</div>
                </div>
              </td>
            @endforeach
          </tr>
        </table>
      </div>
    </div>

    {{-- Tanda Tangan --}}
    <div class="sig-area">
      <div class="sig-cell">
        <div class="sig-label">Dibuat Oleh</div>
        <div class="sig-line">{{ $createdBy }}</div>
      </div>
      <div class="sig-cell">
        <div class="sig-label">Mengetahui / Supervisor</div>
        @if(!empty($mc->supervisor_signature))
          <div style="margin-bottom:6px">
            <img src="{{ $mc->supervisor_signature }}" style="width:200px;height:auto;border:1px solid #e2e8f0;padding:5px;border-radius:7px">
          </div>
          <div class="sig-line">{{ $mc->supervisor_signed_name ?? '' }}</div>
        @else
          <div class="sig-line">&nbsp;</div>
        @endif
      </div>
    </div>

  </div>{{-- /content --}}

  {{-- FOOTER --}}
  <div class="footer">
    <div class="footer-left">Paperless System · SlaughterHouse Department</div>
    <div class="footer-right">{{ $mc->report_code }} · {{ now()->format('d/m/Y H:i') }}</div>
  </div>

</body>
</html>