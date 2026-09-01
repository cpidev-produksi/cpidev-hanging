<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
      color: #000;
      background: #fff;
      padding: 16px 20px;
      line-height: 1.4;
    }

    /* --- KOP --- */
    .kop {
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
      margin-bottom: 10px;
      display: table;
      width: 100%;
    }
    .kop-cell {
      display: table-cell;
      vertical-align: middle;
    }
    .kop-company {
      font-size: 13px;
      font-weight: 700;
    }
    .kop-tagline {
      font-size: 8px;
      color: #555;
      margin-top: 2px;
    }
    .kop-right {
      text-align: right;
    }
    .kop-doc-title {
      font-size: 16px;
      font-weight: 700;
    }
    .kop-doc-eyebrow {
      font-size: 7px;
      color: #777;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-bottom: 2px;
    }

    /* --- META STRIP --- */
    .meta-strip {
      border-bottom: 1px solid #ccc;
      padding: 6px 0;
      margin-bottom: 12px;
      display: table;
      width: 100%;
    }
    .meta-item {
      display: table-cell;
      padding-right: 18px;
      vertical-align: top;
    }
    .meta-label {
      font-size: 7px;
      font-weight: 700;
      color: #777;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }
    .meta-value {
      font-size: 10px;
      font-weight: 700;
    }

    /* --- SECTION --- */
    .sec-title {
      font-size: 8px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      border-bottom: 1px solid #000;
      padding-bottom: 3px;
      margin: 14px 0 6px 0;
    }
    .sec-title:first-child {
      margin-top: 0;
    }

    /* --- TABLES --- */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 6px;
    }
    th, td {
      border: 1px solid #bbb;
      padding: 5px 8px;
      vertical-align: top;
      font-size: 9.5px;
      text-align: left;
    }
    th {
      background: #f5f5f5;
      font-weight: 700;
      width: 40%;
    }
    td {
      font-weight: 400;
    }
    .num {
      font-weight: 700;
      text-align: right;
    }

    /* --- TWO COLUMN --- */
    .two-col {
      display: table;
      width: 100%;
    }
    .col-l, .col-r {
      display: table-cell;
      width: 50%;
      vertical-align: top;
    }
    .col-l { padding-right: 6px; }
    .col-r { padding-left: 6px; }

    /* --- HIGHLIGHT BOX (RINGKASAN) --- */
    .box {
      border: 1px solid #000;
      margin-bottom: 10px;
    }
    .box-head {
      border-bottom: 1px solid #000;
      padding: 5px 10px;
      font-size: 8px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      background: #f5f5f5;
    }
    .box-row {
      display: table;
      width: 100%;
      border-bottom: 1px solid #ddd;
    }
    .box-row:last-child {
      border-bottom: none;
    }
    .box-row.total {
      background: #f5f5f5;
      font-weight: 700;
    }
    .box-key {
      display: table-cell;
      width: 60%;
      padding: 5px 10px;
      font-size: 9.5px;
      vertical-align: middle;
      border-right: 1px solid #ddd;
    }
    .box-val {
      display: table-cell;
      padding: 5px 10px;
      font-size: 9.5px;
      text-align: right;
      vertical-align: middle;
      font-weight: 700;
    }
    .box-val.hero {
      font-size: 12px;
    }
    .box-val.warn {
      color: #000;
    }
    .badge {
      font-size: 8px;
      font-weight: 700;
      border: 1px solid #000;
      padding: 1px 6px;
    }

    /* --- RETUR & MATI --- */
    .retur-box {
      border: 1px solid #000;
      margin-bottom: 10px;
    }
    .retur-head {
      border-bottom: 1px solid #000;
      padding: 5px 10px;
      font-size: 8px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      background: #f5f5f5;
    }
    .retur-body {
      display: table;
      width: 100%;
    }
    .retur-cell {
      display: table-cell;
      width: 33.33%;
      text-align: center;
      padding: 10px 5px;
      border-right: 1px solid #ddd;
    }
    .retur-cell:last-child {
      border-right: none;
    }
    .retur-val {
      font-size: 15px;
      font-weight: 700;
    }
    .retur-lbl {
      font-size: 7px;
      font-weight: 700;
      color: #555;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-top: 3px;
    }

    /* --- QC --- */
    .qc-box {
      border: 1px solid #000;
      margin-bottom: 10px;
    }
    .qc-head {
      border-bottom: 1px solid #000;
      padding: 5px 10px;
      font-size: 8px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      background: #f5f5f5;
    }
    .qc-body {
      padding: 8px;
    }
    .qc-table td {
      border: none;
      padding: 0 4px;
      width: 33.33%;
      vertical-align: top;
    }
    .qc-cell {
      border: 1px solid #999;
      padding: 8px;
      text-align: center;
    }
    .qc-label {
      font-size: 7px;
      font-weight: 700;
      color: #555;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 4px;
    }
    .qc-val {
      font-size: 10px;
      font-weight: 700;
    }

    /* --- SIGNATURE --- */
    .sig-area {
      margin-top: 16px;
      border-top: 1px solid #000;
      padding-top: 10px;
      display: table;
      width: 100%;
    }
    .sig-cell {
      display: table-cell;
      width: 50%;
      vertical-align: top;
      padding-right: 12px;
    }
    .sig-cell:last-child {
      padding-right: 0;
      padding-left: 12px;
    }
    .sig-label {
      font-size: 7px;
      font-weight: 700;
      color: #555;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 4px;
    }
    .sig-line {
      font-size: 10px;
      font-weight: 700;
    }

    /* --- FOOTER --- */
    .footer {
      margin-top: 14px;
      border-top: 1px solid #ccc;
      padding-top: 6px;
      display: table;
      width: 100%;
      font-size: 7px;
      color: #777;
    }
    .footer-left {
      display: table-cell;
    }
    .footer-right {
      display: table-cell;
      text-align: right;
    }
  </style>
</head>
<body>

@php
$customCaps = ['SH02' => [30 => 17]];
$location = $mc->location ?? '';
$totalKosongCalc = 0;
$totalAyamCap = 0;

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
@endphp

  {{-- KOP --}}
  <div class="kop">
    <div class="kop-cell">
      <div class="kop-company">PT. Charoen Pokphand Indonesia</div>
      <div class="kop-tagline">Food Division · SlaughterHouse Department</div>
    </div>
    <div class="kop-cell kop-right">
      <div class="kop-doc-eyebrow">Dokumen Resmi</div>
      <div class="kop-doc-title">Data Summary</div>
    </div>
  </div>

  {{-- META STRIP --}}
  <div class="meta-strip">
    <div class="meta-item">
      <div class="meta-label">Kode Report</div>
      <div class="meta-value">{{ $mc->report_code }}</div>
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
    <div class="sec-title">Kontrol Monitor</div>
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
          <tr><th>Total Ekor</th><td class="num">{{ number_format((int)($mc->total_chicken ?? 0)) }}</td></tr>
          <tr><th>Total Kilo (Kg)</th><td class="num">{{ number_format((float)($mc->total_kilo ?? 0), 2) }}</td></tr>
          <tr><th>ABW</th><td class="num">{{ number_format((float)($mc->abw ?? 0), 2) }}</td></tr>
          <tr><th>No SPPA</th><td>{{ $mc->sppa_no ?? '—' }}</td></tr>
          <tr><th>Tanggal SPPA</th><td>{{ $mc->sppa_date?->format('d/m/Y') ?? '—' }}</td></tr>
        </table>
      </div>
    </div>

    {{-- Hanging & Perhitungan --}}
    <div class="two-col" style="margin-top:4px">
      <div class="col-l">
        <div class="sec-title">Hanging</div>
        <table>
          <tr><th>Jam Bongkar</th><td>{{ $form->unloading_time?->format('H:i') ?? '—' }}</td></tr>
          <tr><th>Jam Selesai</th><td>{{ $form->finish_time?->format('H:i') ?? '—' }}</td></tr>
          <tr><th>Shackle Kosong</th><td class="num">{{ number_format($totalKosongCalc) }}</td></tr>
          <tr><th>Ayam Diterima</th><td class="num">{{ number_format($totalAyamCap) }}</td></tr>
        </table>
      </div>
      <div class="col-r">
        <div class="sec-title">Ringkasan Perhitungan Ayam</div>
        <div class="box">
          <div class="box-head">Verifikasi Jumlah</div>
          <div class="box-row">
            <span class="box-key">Total Ekor</span>
            <span class="box-val">{{ number_format($totalEkorMC) }}</span>
          </div>
          @foreach($capGroups as $cap => $g)
          <div class="box-row">
            <span class="box-key">
              {{ $cap === 50 ? 'Blok Cap 50' : 'Blok Kondisional (Cap '.$cap.')' }}
              ({{ $g['count'] }} blok{{ $g['empty'] > 0 ? ', kosong '.$g['empty'] : ', penuh' }})
            </span>
            <span class="box-val">
              {{ $g['count'] }} × {{ $cap }} @if($g['empty'] > 0) − {{ $g['empty'] }} @endif = {{ number_format($g['ayam']) }}
            </span>
          </div>
          @endforeach
          <div class="box-row">
            <span class="box-key">Shackle Kosong</span>
            <span class="box-val">{{ number_format($totalKosongCalc) }}</span>
          </div>
          <div class="box-row total">
            <span class="box-key">Ayam Diterima</span>
            <span class="box-val hero">{{ number_format($totalAyamCap) }}</span>
          </div>
          <div class="box-row">
            <span class="box-key">Status</span>
            <span class="box-val">
              @if($isMatch)
                <span class="badge">MATCH</span>
              @else
                <span class="badge">SELISIH {{ $selisih > 0 ? '(KELEBIHAN)' : '(KEKURANGAN)' }}</span>
              @endif
            </span>
          </div>
          @if(!$isMatch)
          <div class="box-row total">
            <span class="box-key">Selisih</span>
            <span class="box-val warn">{{ number_format($selisih) }}</span>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Retur & Mati --}}
    <div class="retur-box">
      <div class="retur-head">Ayam Retur &amp; Mati</div>
      <div class="retur-body">
        <div class="retur-cell">
          <div class="retur-val">{{ (int)($form->dead_count ?? 0) }}</div>
          <div class="retur-lbl">Ayam Mati</div>
        </div>
        <div class="retur-cell">
          <div class="retur-val">{{ (int)($form->retur_count ?? 0) }}</div>
          <div class="retur-lbl">Ayam Retur</div>
        </div>
        <div class="retur-cell">
          <div class="retur-val">{{ number_format((float)($form->retur_total_kg ?? 0), 2) }}</div>
          <div class="retur-lbl">Berat Retur (Kg)</div>
        </div>
      </div>
    </div>

    {{-- QC Kondisi --}}
    <div class="qc-box">
      <div class="qc-head">QC Kondisi</div>
      <div class="qc-body">
        <table class="qc-table">
          <tr>
            @foreach([
              ['label' => 'Keranjang',      'val' => $form->basket_condition],
              ['label' => 'Platform Truck', 'val' => $form->truck_platform_condition],
              ['label' => 'Bulu Ayam',      'val' => $form->feather_condition],
            ] as $qcItem)
              <td>
                <div class="qc-cell">
                  <div class="qc-label">{{ $qcItem['label'] }}</div>
                  <div class="qc-val">{{ $qcLabel($qcItem['val']) }}</div>
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
            <img src="{{ $mc->supervisor_signature }}" style="width:180px;height:auto;border:1px solid #ccc;padding:4px;">
          </div>
          <div class="sig-line">{{ $mc->supervisor_signed_name ?? '' }}</div>
        @else
          <div class="sig-line">&nbsp;</div>
        @endif
      </div>
    </div>

  </div>

  {{-- FOOTER --}}
  <div class="footer">
    <div class="footer-left">Paperless System · SlaughterHouse Department</div>
    <div class="footer-right">{{ $mc->report_code }} · {{ now()->format('d/m/Y H:i') }}</div>
  </div>

</body>
</html>