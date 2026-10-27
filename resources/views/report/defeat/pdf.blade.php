<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Efficiency Defeathering Report {{ $report->report_date->format('d-m-Y') }} {{ $report->location }}</title>
    <style>
        @page { margin: 12mm 10mm 14mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        h1 { text-align: center; margin: 0 0 12px; font-size: 18px; }
        .meta { margin-bottom: 10px; }

        /* Tabel data (partial) - hitam putih, gaya sama dengan baking-report */
        table.df-tbl { width: 100%; border-collapse: collapse; font-size: 6px; }
        table.df-tbl th, table.df-tbl td { border: 1px solid #222; padding: 3px 2px; text-align: center; color: #000; }
        table.df-tbl th { background: #e5e5e5 !important; }
        table.df-tbl td.l, table.df-tbl th.l { text-align: left; }
        table.df-tbl tr.sum-tot td, table.df-tbl tr.sum-avg td { background: #fff !important; font-weight: bold; }
        table.df-tbl td.sum-lbl { text-align: right; text-transform: uppercase; }
        table.df-tbl tr { page-break-inside: avoid; }

        .signatures { margin-top: 28px; width: 100%; table-layout: fixed; border-collapse: collapse; page-break-inside: avoid; }
        .signatures td { border: 0; text-align: center; padding: 0; }
        .signatures .signature-cell { height: 55px; vertical-align: bottom; font-size: 9px; padding-bottom: 4px; }

        .footer { margin-top: 14px; text-align: center; font-size: 8px; }
    </style>
</head>
<body>
    <!-- KOP SURAT: logo + identitas perusahaan, rata kiri (satu-satunya bagian berwarna) -->
    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 8px; border-bottom: 3px solid #0d3b6e;">
        <tr>
            <td style="width: 90px; vertical-align: top; border: none; padding-bottom: 6px;">
                <img src="{{ public_path('images/logo small.png') }}" style="width: 75px; height: auto;">
            </td>
            <td style="vertical-align: top; border: none; text-align: left; padding-bottom: 6px;">
                <p style="margin: 0; font-size: 13px; font-weight: bold; color: #0d3b6e;">PT. Charoen Pokphand Indonesia</p>
                <p style="margin: 0; font-size: 12px; color: #4a6fa5;">Slaughterhouse</p>
                <p style="margin: 0; font-size: 11px; color: #4a6fa5;">Salatiga - Indonesia</p>
            </td>
        </tr>
    </table>

    <h1>EFFICIENCY DEFEATHERING REPORT</h1>
    <div class="meta">
        Tanggal Operasional: {{ $report->report_date->format('d/m/Y') }} &nbsp;&nbsp;
        Shift: {{ ucfirst($report->shift) }} &nbsp;&nbsp;
        Lokasi: {{ $report->location }} &nbsp;&nbsp;
        Jumlah Farm: {{ $report->items->count() }} &nbsp;&nbsp;
        Status: {{ $report->status_label }}
    </div>

    @include('report.defeat.partials.table', ['report' => $report, 'pdf' => true, 'rowActions' => false])

    <div style="text-align: right; font-size: 11px; margin-top: 4px;">SH 03/03</div>

    <table class="signatures">
        <tr>
            <td>Dibuat oleh,</td>
            <td>Diketahui oleh,</td>
            <td>Disetujui oleh,</td>
        </tr>
        <tr>
            <td class="signature-cell">
                &#10003; Dibuat secara digital<br>{{ $report->created_at?->format('d/m/Y H:i') }}
            </td>
            <td class="signature-cell">
                @if($report->known_by)&#10003; Diketahui secara digital<br>{{ $report->known_at?->format('d/m/Y H:i') }}@endif
            </td>
            <td class="signature-cell">
                @if($report->approved_by)&#10003; Disetujui secara digital<br>{{ $report->approved_at?->format('d/m/Y H:i') }}@endif
            </td>
        </tr>
        <tr>
            <td>
                {{ $report->checker_name ?: $report->creator?->name }}<br>
                ( Checker )
            </td>
            <td>
                {{ $report->known_name ?: $report->knower?->name }}<br>
                ( Foreman/Lady )
            </td>
            <td>
                {{ $report->approved_name ?: $report->approver?->name }}<br>
                ( Supervisor )
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan oleh CPI Paperless System &mdash; Report ID: {{ $report->id }} | Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>