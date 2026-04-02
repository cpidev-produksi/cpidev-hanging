<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    /* ── BASE ── */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 11px;
      color: #1a1a2e;
      background: #fff;
      padding: 0;
    }

    /* ── KOP / HEADER ── */
    .kop {
      background: linear-gradient(135deg, #064e3b 0%, #065f46 60%, #047857 100%);
      padding: 22px 28px 18px;
      display: table;
      width: 100%;
    }
    .kop-inner {
      display: table-row;
    }
    .kop-logo-cell {
      display: table-cell;
      vertical-align: middle;
      width: 72px;
      padding-right: 16px;
    }
    .kop-logo-cell img {
      width: 60px;
      height: 60px;
      object-fit: contain;
      border-radius: 10px;
      background: rgba(255,255,255,.12);
      padding: 4px;
      display: block;
    }
    .kop-text-cell {
      display: table-cell;
      vertical-align: middle;
    }
    .kop-company {
      font-size: 17px;
      font-weight: 700;
      color: #000000;
      letter-spacing: .03em;
      line-height: 1.15;
    }
    .kop-tagline {
      font-size: 9.5px;
      color: rgba(68, 68, 68, 0.65);
      letter-spacing: .06em;
      text-transform: uppercase;
      margin-top: 2px;
    }
    .kop-right-cell {
      display: table-cell;
      vertical-align: middle;
      text-align: right;
    }
    .kop-doc-label {
      font-size: 8.5px;
      font-weight: 700;
      color: rgba(255,255,255,.55);
      letter-spacing: .12em;
      text-transform: uppercase;
    }
    .kop-doc-title {
      font-size: 19px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: .01em;
      line-height: 1.1;
      margin-top: 2px;
    }

    /* accent bar under kop */
    .kop-accent {
      height: 4px;
      background: linear-gradient(90deg, #10b981, #6ee7b7, #10b981);
    }

    /* ── META STRIP ── */
    .meta-strip {
      background: #f0fdf7;
      border-bottom: 1px solid #d1fae5;
      padding: 10px 28px;
      display: table;
      width: 100%;
    }
    .meta-item {
      display: table-cell;
      padding-right: 24px;
      vertical-align: middle;
    }
    .meta-label {
      font-size: 8px;
      font-weight: 700;
      color: #6b7280;
      letter-spacing: .1em;
      text-transform: uppercase;
    }
    .meta-value {
      font-size: 11px;
      font-weight: 700;
      color: #064e3b;
      margin-top: 1px;
    }
    .meta-badge {
      display: inline-block;
      background: #064e3b;
      color: #fff;
      padding: 2px 9px;
      border-radius: 4px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .03em;
    }

    /* ── BODY CONTENT ── */
    .content {
      padding: 18px 28px 10px;
    }

    /* ── SECTION TITLE ── */
    .section-title {
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: #059669;
      border-left: 3px solid #059669;
      padding-left: 7px;
      margin-bottom: 7px;
      margin-top: 14px;
    }
    .section-title:first-child { margin-top: 0; }

    /* ── TABLES ── */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 4px;
      border-radius: 7px;
      overflow: hidden;
    }
    th, td {
      border: 1px solid #e5e7eb;
      padding: 6px 10px;
      vertical-align: top;
      font-size: 10.5px;
    }
    th {
      background: #f9fafb;
      color: #374151;
      font-weight: 700;
      width: 38%;
      border-right-color: #d1fae5;
    }
    td {
      color: #111827;
      background: #fff;
    }
    tr:nth-child(even) td { background: #fafffe; }

    /* number cells */
    .td-num {
      font-weight: 700;
      color: #065f46;
    }

    /* QC badge cells */
    .qc-ok {
      display: inline-block;
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #6ee7b7;
      border-radius: 4px;
      padding: 1px 8px;
      font-weight: 700;
      font-size: 10px;
    }

    /* ── 2-COLUMN GRID ── */
    .two-col {
      display: table;
      width: 100%;
      gap: 0;
    }
    .col-l {
      display: table-cell;
      width: 50%;
      padding-right: 8px;
      vertical-align: top;
    }
    .col-r {
      display: table-cell;
      width: 50%;
      padding-left: 8px;
      vertical-align: top;
    }

    /* ── SIGNATURE ── */
    .sig-area {
      margin-top: 18px;
      border-top: 2px solid #d1fae5;
      padding-top: 12px;
      display: table;
      width: 100%;
    }
    .sig-cell {
      display: table-cell;
      width: 50%;
      vertical-align: top;
      padding-right: 16px;
    }
    .sig-cell:last-child { padding-right: 0; padding-left: 16px; }
    .sig-label {
      font-size: 8.5px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: #6b7280;
      margin-bottom: 36px;
    }
    .sig-line {
      border-top: 1px solid #9ca3af;
      padding-top: 4px;
      font-size: 10px;
      font-weight: 700;
      color: #374151;
    }

    /* ── FOOTER ── */
    .footer {
      margin-top: 14px;
      background: #064e3b;
      padding: 8px 28px;
      display: table;
      width: 100%;
    }
    .footer-left {
      display: table-cell;
      vertical-align: middle;
      font-size: 8.5px;
      color: rgba(255,255,255,.5);
    }
    .footer-right {
      display: table-cell;
      vertical-align: middle;
      text-align: right;
      font-size: 8.5px;
      color: rgba(255,255,255,.5);
    }
  </style>
</head>
<body>

  {{-- ══ KOP SURAT ══ --}}
  <div class="kop">
    <div class="kop-inner">
      <div class="kop-logo-cell">
        <img src="{{ public_path('images/logo small.png') }}" alt="Logo">
      </div>
      <div class="kop-text-cell">
        <div class="kop-company">PT. Charoen Pokphand Indonesia</div>
        <div class="kop-tagline">Food Division</div>
      </div>
      <div class="kop-right-cell">
        <div class="kop-doc-label">Dokumen</div>
        <div class="kop-doc-title">Data Summary</div>
      </div>
    </div>
  </div>
  <div class="kop-accent"></div>

  {{-- ══ META STRIP ══ --}}
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

  {{-- ══ CONTENT ══ --}}
  <div class="content">

    {{-- Kontrol Monitor ──────────────────────────────── --}}
    <div class="section-title">Kontrol Monitor</div>
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
          <tr><th>Total Kilo</th><td class="td-num">{{ number_format((float)($mc->total_kilo ?? 0), 2) }} Kg</td></tr>
          <tr><th>ABW</th><td class="td-num">{{ number_format((float)($mc->abw ?? 0), 2) }}</td></tr>
        </table>
      </div>
    </div>

    {{-- Hanging & Retur ──────────────────────────────── --}}
    <div class="two-col">
      <div class="col-l">
        <div class="section-title">Hanging</div>
        <table>
          <tr><th>Jam Bongkar</th><td>{{ $form->unloading_time?->format('H:i') ?? '—' }}</td></tr>
          <tr><th>Jam Selesai</th><td>{{ $form->finish_time?->format('H:i') ?? '—' }}</td></tr>
          <tr><th>Shackle Kosong</th><td class="td-num">{{ $totalKosong }}</td></tr>
          <tr><th>Ayam Diterima</th><td class="td-num">{{ $totalAyamTerima }}</td></tr>
        </table>
      </div>
      <div class="col-r">
        <div class="section-title">Ayam Retur &amp; Mati</div>
        <table>
          <tr><th>Ayam Mati</th><td class="td-num">{{ (int)($form->dead_count ?? 0) }}</td></tr>
          <tr><th>Ayam Retur</th><td class="td-num">{{ (int)($form->retur_count ?? 0) }}</td></tr>
          <tr><th>Total Berat Retur</th><td class="td-num">{{ number_format((float)($form->retur_total_kg ?? 0), 2) }} Kg</td></tr>
        </table>
      </div>
    </div>

    {{-- QC Kondisi ────────────────────────────────────── --}}
    <div class="section-title">QC Kondisi</div>
    <table>
      <tr>
        <th>Keranjang</th>
        <td><span class="qc-ok">{{ $form->basket_condition }}</span></td>
        <th>Platform Truck</th>
        <td><span class="qc-ok">{{ $form->truck_platform_condition }}</span></td>
        <th>Bulu Ayam</th>
        <td><span class="qc-ok">{{ $form->feather_condition }}</span></td>
      </tr>
    </table>

    {{-- Tanda Tangan ──────────────────────────────────── --}}
    <div class="sig-area">
      <div class="sig-cell">
        <div class="sig-label">Dibuat Oleh</div>
        <div class="sig-line">{{ $createdBy }}</div>
      </div>
      <div class="sig-cell">
        <div class="sig-label">Mengetahui / Supervisor</div>
        <div class="sig-line">&nbsp;</div>
      </div>
    </div>

  </div>{{-- /content --}}

  {{-- ══ FOOTER ══ --}}
  <div class="footer">
    <div class="footer-left">&middot; Paperless System SlaughterHouse Department &middot;</div>
    <div class="footer-right">{{ $mc->report_code }} &middot; {{ now()->format('d/m/Y H:i') }}</div>
  </div>

</body>
</html>