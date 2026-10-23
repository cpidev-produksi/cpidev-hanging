<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Efficiency Defeathering Report {{ $report->report_date->format('d-m-Y') }} {{ $report->location }}</title>
    <style>
        @page { margin: 12mm 10mm 14mm 10mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; font-size: 9px; color: #1a1f2e; line-height: 1.4; }

        .header { border-bottom: 3px solid #0d3b6e; padding-bottom: 8px; margin-bottom: 10px; }
        .header table { width: 100%; border-collapse: collapse; }
        .header h1 { font-size: 15px; color: #0d3b6e; letter-spacing: .03em; }
        .header .company { font-size: 9px; color: #4a6fa5; font-weight: bold; margin-top: 2px; }
        .stamp { text-align: right; vertical-align: middle; }
        .stamp span { display: inline-block; background: #0d3b6e; color: #fff; padding: 5px 12px; border-radius: 4px; font-size: 10px; font-weight: bold; }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.meta td { padding: 4px 8px; border: 1px solid #cfd6e4; }
        .lbl { font-size: 7.5px; font-weight: bold; text-transform: uppercase; color: #6b7896; letter-spacing: .04em; }
        .val { font-size: 10px; font-weight: bold; margin-top: 1px; }

        table.df-tbl { width: 100%; border-collapse: collapse; table-layout: auto; font-size: 6.6px; }
        table.df-tbl th, table.df-tbl td { border: 1px solid #cfd6e4; padding: 3px 2px; text-align: center; }
        table.df-tbl thead th { background: #eaf0f9; font-size: 6.2px; text-transform: uppercase; color: #0d3b6e; }
        table.df-tbl td.l, table.df-tbl th.l { text-align: left; padding-left: 4px; }
        table.df-tbl tr.sum-tot td { background: #fff4e5; font-weight: bold; }
        table.df-tbl tr.sum-avg td { background: #e8f7ee; font-weight: bold; }
        table.df-tbl td.sum-lbl { text-align: right; text-transform: uppercase; padding-right: 5px; }
        table.df-tbl tr { page-break-inside: avoid; }

        .sign-section { margin-top: 14px; page-break-inside: avoid; }
        table.sign { width: 100%; border-collapse: collapse; }
        table.sign td { width: 33.33%; border: 1px solid #cfd6e4; text-align: center; vertical-align: top; padding: 8px; }
        .sign-role { font-weight: bold; font-size: 9px; }
        .sign-sub { font-size: 8px; color: #6b7896; }
        .sign-area { height: 52px; padding-top: 10px; font-size: 8px; color: #047857; }
        .sign-name { min-height: 13px; font-weight: bold; font-size: 9.5px; border-top: 1px solid #333; padding-top: 3px; margin: 0 14px; }
        .sign-time { font-size: 8px; color: #6b7896; }

        .footer { margin-top: 10px; text-align: center; font-size: 8px; color: #8a94a8; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>EFFICIENCY DEFEATHERING REPORT</h1>
                    <div class="company">PT. Charoen Pokphand Indonesia &mdash; Slaughterhouse</div>
                </td>
                <td class="stamp"><span>{{ $report->report_date->format('d/m/Y') }}</span></td>
            </tr>
        </table>
    </div>

    <table class="meta">
        <tr>
            <td><div class="lbl">Tanggal Operasional</div><div class="val">{{ $report->report_date->format('d/m/Y') }}</div></td>
            <td><div class="lbl">Shift</div><div class="val">{{ ucfirst($report->shift) }}</div></td>
            <td><div class="lbl">Lokasi</div><div class="val">{{ $report->location }}</div></td>
            <td><div class="lbl">Jumlah Farm</div><div class="val">{{ $report->items->count() }}</div></td>
            <td><div class="lbl">Status</div><div class="val">{{ $report->status_label }}</div></td>
        </tr>
    </table>

    @include('report.defeat.partials.table', ['report' => $report, 'pdf' => true, 'rowActions' => false])

    <div style="text-align: right; font-size: 6px; margin-top: 4px;">SH 03/03</div>
    
    <div class="sign-section">
        <table class="sign">
            <tr>
                <td>
                    <div class="sign-role">Dibuat oleh</div>
                    <div class="sign-sub">(Checker)</div>
                    <div class="sign-area">&#10003; Dibuat secara digital<br><span class="sign-time">{{ $report->created_at?->format('d/m/Y H:i') }}</span></div>
                    <div class="sign-name">{{ $report->checker_name ?: $report->creator?->name }}</div>
                </td>
                <td>
                    <div class="sign-role">Diketahui oleh</div>
                    <div class="sign-sub">(Foreman/lady)</div>
                    <div class="sign-area">
                        @if($report->known_by)&#10003; Diketahui secara digital<br><span class="sign-time">{{ $report->known_at?->format('d/m/Y H:i') }}</span>@endif
                    </div>
                    <div class="sign-name">{{ $report->known_name ?: $report->knower?->name }}</div>
                </td>
                <td>
                    <div class="sign-role">Disetujui oleh</div>
                    <div class="sign-sub">(Supervisor)</div>
                    <div class="sign-area">
                        @if($report->approved_by)&#10003; Disetujui secara digital<br><span class="sign-time">{{ $report->approved_at?->format('d/m/Y H:i') }}</span>@endif
                    </div>
                    <div class="sign-name">{{ $report->approved_name ?: $report->approver?->name }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini dihasilkan oleh CPI Paperless System &mdash; Report ID: {{ $report->id }} | Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>