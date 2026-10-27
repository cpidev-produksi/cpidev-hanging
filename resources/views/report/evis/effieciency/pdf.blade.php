<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Efficiency Evisceration Report {{ $report->report_date->format('d-m-Y') }} {{ $report->location }}</title>
    <style>
        @page { margin: 12mm 10mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color:#111; }
        h1 { margin:12px 0; text-align:center; font-size:16px; }
        .meta { margin-bottom:12px; }
        .ev-data-table { width:100%; border-collapse:collapse; font-size:7px; }
        .ev-data-table th,.ev-data-table td { border:1px solid #222; padding:4px 2px; text-align:center; }
        .ev-data-table th { background:#e5e5e5; }
        .ev-data-table tfoot td { font-weight:bold; background:#fff; }
        .ev-data-table tfoot td.sum-lbl { text-align:right; text-transform:uppercase; }
        .ev-data-table tr { page-break-inside:avoid; }
        .signatures { width:100%; margin-top:30px; table-layout:fixed; text-align:center; }
        .signatures td { padding:5px; }
        .sign-space { height:55px; vertical-align:bottom; }
    </style>
</head>
<body>
    <table style="width:100%;border-bottom:2px solid #0d3b6e;margin-bottom:8px"><tr>
        <td style="width:85px"><img src="{{ public_path('images/logo small.png') }}" style="width:70px"></td>
        <td><strong>PT. Charoen Pokphand Indonesia</strong><br>Slaughterhouse<br>Salatiga - Indonesia</td>
    </tr></table>
    <h1>EFFICIENCY EVISCERATION REPORT</h1>
    <div class="meta">Tanggal Operasional: {{ $report->report_date->format('d/m/Y') }} &nbsp; Lokasi: {{ $report->location }} &nbsp; Shift: {{ ucfirst($report->shift) }}</div>
    @include('report.evis.effieciency.partials.table', ['report' => $report, 'actions' => false, 'pdf' => true])
    <div style="text-align: right; font-size: 8px; margin-top: 4px;">SH 04/02</div>
    <table class="signatures">
        <tr><td>Dibuat oleh (Checker)</td><td>Diketahui Oleh</td><td>Disetujui Oleh</td></tr>
        <tr class="sign-space"><td>{{ $report->created_at?->format('d/m/Y H:i') }}</td><td>{{ $report->known_at?->format('d/m/Y H:i') }}</td><td>{{ $report->approved_at?->format('d/m/Y H:i') }}</td></tr>
        <tr><td>{{ $report->checker_name ?: $report->creator?->name }}</td><td>{{ $report->known_name ?: $report->knower?->name }}</td><td>{{ $report->approved_name ?: $report->approver?->name }}</td></tr>
    </table>
    <p style="text-align:center;font-size:7px">CPI Paperless System · Report ID: {{ $report->id }} · Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>