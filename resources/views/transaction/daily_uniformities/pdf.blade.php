<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Daily Uniformity - {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: "Helvetica", "Arial", sans-serif; font-size: 10.5px; color: #0f172a; margin: 0; padding: 22px; }

    .pdf-title { font-size: 16px; font-weight: bold; margin: 0 0 2px; }
    .pdf-sub { font-size: 10px; color: #64748b; margin: 0 0 16px; }

    /* ===== Ringkasan angka ===== */
    .pdf-sum-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .pdf-sum-table td { border: 1px solid #e2e8f0; padding: 7px 8px; text-align: center; }
    .pdf-sum-table .lbl { font-size: 8.5px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 3px; }
    .pdf-sum-table .val { font-size: 13px; font-weight: bold; }

    /* ===== Grafik 3 batang + axis ===== */
    .pdf-chart-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #1a56db; margin: 0 0 8px; }
    .pdf-chart-legend { font-size: 9px; color: #475569; margin-bottom: 8px; }
    .pdf-chart-legend span { margin-right: 14px; }
    .pdf-legend-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 4px; }
    .pdf-legend-dot.below { background: #f59e0b; }
    .pdf-legend-dot.in { background: #0d9488; }
    .pdf-legend-dot.above { background: #6366f1; }

    .pdf-chart-table { width: 100%; margin-bottom: 20px; }
    .pdf-axis-col { width: 32px; vertical-align: top; }
    .pdf-axis-labels { position: relative; height: 150px; font-size: 8px; color: #94a3b8; }
    .pdf-axis-labels div { position: absolute; right: 4px; }

    .pdf-bars-col { vertical-align: top; }
    .pdf-chart-plot { position: relative; height: 150px; border-left: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; }
    .pdf-gridline { position: absolute; left: 0; right: 0; border-top: 1px dashed #e2e8f0; }

    .pdf-bar-wrap { position: absolute; bottom: 0; width: 30%; text-align: center; }
    .pdf-bar-value { font-size: 9px; font-weight: bold; margin-bottom: 3px; }
    .pdf-bar { width: 40px; margin: 0 auto; border-radius: 3px 3px 0 0; }
    .pdf-bar.below { background: #f59e0b; }
    .pdf-bar.in { background: #0d9488; }
    .pdf-bar.above { background: #6366f1; }

    .pdf-xaxis-row { width: 100%; margin-top: 6px; }
    .pdf-xaxis-row td { width: 33.33%; text-align: center; font-size: 8.5px; color: #334155; font-weight: bold; padding-top: 4px; }
    .pdf-xaxis-row td span { display: block; font-weight: normal; color: #94a3b8; }

    .pdf-axis-title-y { font-size: 8px; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
    .pdf-axis-title-x { font-size: 8px; color: #64748b; text-transform: uppercase; text-align: center; margin-top: 4px; }

    /* ===== Tabel data (kolom disamakan dengan index) ===== */
    table.pdf-table { width: 100%; border-collapse: collapse; }
    .pdf-table th { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 6px; font-size: 9px; text-transform: uppercase; text-align: center; }
    .pdf-table td { border: 1px solid #e2e8f0; padding: 6px 6px; font-size: 9.5px; text-align: center; }
    .pdf-table td.left { text-align: left; }

    .pdf-tag { padding: 2px 6px; border-radius: 4px; font-weight: bold; }
    .pdf-tag-below { background: #fef3c7; color: #b45309; }
    .pdf-tag-in { background: #ccfbf1; color: #0d9488; }
    .pdf-tag-above { background: #e0e7ff; color: #4338ca; }

    /* ===== Tanda tangan ===== */
    .pdf-sign-table { width: 100%; margin-top: 46px; }
    .pdf-sign-cell { width: 50%; text-align: center; padding: 0 24px; vertical-align: top; }
    .pdf-sign-role { font-size: 9.5px; font-weight: bold; text-transform: uppercase; color: #334155; margin-bottom: 56px; }
    .pdf-sign-line { border-top: 1px solid #334155; margin: 0 20px; }
    .pdf-sign-name { font-size: 9px; color: #64748b; margin-top: 4px; }

    .pdf-footer { margin-top: 18px; font-size: 8.5px; color: #94a3b8; text-align: right; }
  </style>
</head>
<body>

  <div class="pdf-title">Laporan Daily Uniformity</div>
  <div class="pdf-sub">Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</div>

  {{-- ===== Ringkasan angka ===== --}}
  <table class="pdf-sum-table">
    <tr>
      <td>
        <span class="lbl">Jumlah Sampling</span>
        <span class="val">{{ $aggregate['count'] }} ekor</span>
      </td>
      <td>
        <span class="lbl">Undersize</span>
        <span class="val">{{ $aggregate['below']['pct'] }}% ({{ $aggregate['below']['count'] }})</span>
      </td>
      <td>
        <span class="lbl">In Range</span>
        <span class="val">{{ $aggregate['in_range']['pct'] }}% ({{ $aggregate['in_range']['count'] }})</span>
      </td>
      <td>
        <span class="lbl">Oversize</span>
        <span class="val">{{ $aggregate['above']['pct'] }}% ({{ $aggregate['above']['count'] }})</span>
      </td>
    </tr>
  </table>

  {{-- ===== Grafik 3 batang dengan indikator sumbu vertikal & horizontal ===== --}}
  <div class="pdf-chart-title">Grafik Sebaran Uniformity</div>
  <div class="pdf-chart-legend">
    <span><span class="pdf-legend-dot below"></span>Undersize (di bawah range)</span>
    <span><span class="pdf-legend-dot in"></span>In Range (sesuai uniformity)</span>
    <span><span class="pdf-legend-dot above"></span>Oversize (di atas range)</span>
  </div>

  @if ($aggregate['count'] > 0)
    @php
      $maxBarPx = 110; // tinggi bar maksimum (px) untuk 100%
      $belowPx = max((int) round($aggregate['below']['pct'] / 100 * $maxBarPx), 2);
      $inPx = max((int) round($aggregate['in_range']['pct'] / 100 * $maxBarPx), 2);
      $abovePx = max((int) round($aggregate['above']['pct'] / 100 * $maxBarPx), 2);
    @endphp
    <table class="pdf-chart-table">
      <tr>
        <td class="pdf-axis-col">
          <div class="pdf-axis-title-y">%</div>
          <div class="pdf-axis-labels">
            <div style="top:0px;">100</div>
            <div style="top:35px;">75</div>
            <div style="top:70px;">50</div>
            <div style="top:105px;">25</div>
            <div style="top:140px;">0</div>
          </div>
        </td>
        <td class="pdf-bars-col">
          <div class="pdf-chart-plot">
            <div class="pdf-gridline" style="bottom:0%;"></div>
            <div class="pdf-gridline" style="bottom:25%;"></div>
            <div class="pdf-gridline" style="bottom:50%;"></div>
            <div class="pdf-gridline" style="bottom:75%;"></div>
            <div class="pdf-gridline" style="bottom:100%;"></div>

            <div class="pdf-bar-wrap" style="left:2%;">
              <div class="pdf-bar-value">{{ $aggregate['below']['pct'] }}%</div>
              <div class="pdf-bar below" style="height:{{ $belowPx }}px;"></div>
            </div>
            <div class="pdf-bar-wrap" style="left:35%;">
              <div class="pdf-bar-value">{{ $aggregate['in_range']['pct'] }}%</div>
              <div class="pdf-bar in" style="height:{{ $inPx }}px;"></div>
            </div>
            <div class="pdf-bar-wrap" style="left:68%;">
              <div class="pdf-bar-value">{{ $aggregate['above']['pct'] }}%</div>
              <div class="pdf-bar above" style="height:{{ $abovePx }}px;"></div>
            </div>
          </div>
          <table class="pdf-xaxis-row">
            <tr>
              <td>Undersize<span>{{ $aggregate['below']['count'] }} ekor</span></td>
              <td>In Range<span>{{ $aggregate['in_range']['count'] }} ekor</span></td>
              <td>Oversize<span>{{ $aggregate['above']['count'] }} ekor</span></td>
            </tr>
          </table>
          <div class="pdf-axis-title-x">Kategori Uniformity</div>
        </td>
      </tr>
    </table>
  @else
    <p style="color:#94a3b8;">Belum ada data sampling untuk tanggal ini.</p>
  @endif

  {{-- ===== Tabel data  ===== --}}
  <table class="pdf-table">
    <thead>
      <tr>
        <th rowspan="2">No</th>
        <th rowspan="2">Tanggal</th>
        <th rowspan="2">Farm</th>
        <th rowspan="2">Size Ayam</th>
        <th rowspan="2">Ayam Diterima</th>
        <th rowspan="2">Jml Sample</th>
        <th rowspan="2">Berat RPA</th>
        <th colspan="3">Persentase Uniformity</th>
      </tr>
      <tr>
        <th>Undersize</th>
        <th>In Range</th>
        <th>Oversize</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $idx => $item)
        @php $s = $item->summary_data; @endphp
        <tr>
          <td>{{ $idx + 1 }}</td>
          <td>{{ optional($item->monitorControl->process_date)->format('d/m/Y') }}</td>
          <td class="left">{{ $item->monitorControl->farm->name ?? '-' }}</td>
          <td>{{ $item->monitorControl->size ?? '-' }}</td>
          <td>{{ number_format((int)($item->monitorControl->ayam_diterima ?? 0)) }}</td>
          <td>{{ $s['count'] }}</td>
          <td>{{ $item->berat_rpa ?? '-' }}</td>
          <td><span class="pdf-tag pdf-tag-below">{{ $s['below']['pct'] }}%</span></td>
          <td><span class="pdf-tag pdf-tag-in">{{ $s['in_range']['pct'] }}%</span></td>
          <td><span class="pdf-tag pdf-tag-above">{{ $s['above']['pct'] }}%</span></td>
        </tr>
      @empty
        <tr><td colspan="10">Belum ada data untuk tanggal ini.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- ===== Tanda tangan ===== --}}
  <table class="pdf-sign-table">
    <tr>
      <td class="pdf-sign-cell">
        <div class="pdf-sign-role">Diperiksa oleh</div>
        <div class="pdf-sign-line"></div>
      </td>
      <td class="pdf-sign-cell">
        <div class="pdf-sign-role">Disetujui oleh</div>
        <div class="pdf-sign-line"></div>
      </td>
    </tr>
  </table>

  <div class="pdf-footer">Sistem Monitoring Live Bird &mdash; Laporan Daily Uniformity</div>

</body>
</html>