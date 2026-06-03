<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Evis {{ $report->report_date->format('d-m-Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1a1f2e;
            line-height: 1.5;
            background: white;
        }

        .page {
            width: {{ ($orientation ?? 'portrait') === 'landscape' ? '267mm' : '190mm' }};
            min-height: {{ ($orientation ?? 'portrait') === 'landscape' ? '180mm' : '277mm' }};
            margin: 0 auto;
            padding: 10mm 10mm 12mm 10mm;
            background: white;
            page-break-after: auto;
        }

        .page:last-child { page-break-after: avoid; }

        .header {
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 3px solid #0d3b6e;
        }

        .header-inner { display: table; width: 100%; }
        .header-text { display: table-cell; vertical-align: middle; }
        .header-badge { display: table-cell; vertical-align: middle; text-align: right; white-space: nowrap; }

        .header h1 {
            font-size: 15px;
            font-weight: bold;
            color: #0d3b6e;
            letter-spacing: 0.03em;
            margin-bottom: 3px;
        }

        .header .company { font-size: 10px; color: #4a6fa5; font-weight: bold; }

        .header .report-date {
            display: inline-block;
            background: #0d3b6e;
            color: white;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border: 1px solid #c8d8e8;
            border-radius: 5px;
            overflow: hidden;
            background: white;
        }

        .info-row { display: table-row; }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 7px 11px;
            border-right: 1px solid #c8d8e8;
            border-bottom: 1px solid #e4edf5;
            font-weight: bold;
            background: #eaf1f8;
            color: #1a4a7a;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .info-value {
            display: table-cell;
            width: 70%;
            padding: 7px 11px;
            border-bottom: 1px solid #e4edf5;
            font-size: 10.5px;
            color: #1a1f2e;
        }

        .info-row:last-child .info-label,
        .info-row:last-child .info-value { border-bottom: none; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        thead {
            background: #0d3b6e;
            color: white;
        }

        th {
            padding: {{ ($orientation ?? 'portrait') === 'landscape' ? '6px 5px' : '9px 11px' }};
            text-align: left;
            font-weight: bold;
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '8px' : '9.5px' }};
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border: none;
        }

        td {
            padding: {{ ($orientation ?? 'portrait') === 'landscape' ? '5px 5px' : '7px 11px' }};
            border-bottom: 1px solid #e4edf5;
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '8.5px' : '10px' }};
        }

        tbody tr:nth-child(even) td { background: #f6f9fc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .total-row td {
            background: #1a4a7a !important;
            color: white !important;
            font-weight: bold;
            font-size: 10.5px;
            border-bottom: none;
            padding: 9px 11px;
        }

        .section-title {
            margin: 10px 0 6px;
            font-weight: bold;
            color: #0d3b6e;
            font-size: 10.5px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .signature-section { margin-top: 20px; page-break-inside: avoid; }

        .signature-section h3 {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #111;
            margin-bottom: 10px;
        }

        .signatures { display: table; width: 100%; }
        .signature-box { display: table-cell; width: 40%; text-align: center; vertical-align: top; padding: 10px 12px; background: #ffffff; }
        .signature-line { border-top: 1px solid #333; margin-top: 28px; padding-top: 5px; font-weight: normal; font-size: 9.5px; color: #333; }

        .qr-code { margin: 10px auto; text-align: center; }
        .qr-code img { width: 80px; height: 80px; border: 0; padding: 0; background: transparent; }
        .qr-label { font-size: 9px; color: #4a6fa5; margin-top: 6px; }

        .divider { display: table-cell; width: 4%; }

        .footer {
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #c8d8e8;
            display: table;
            width: 100%;
        }

        .footer p {
            display: table-cell;
            font-size: 8.5px;
            color: #9ab4cc;
            vertical-align: middle;
        }

        .footer .report-id {
            text-align: right;
            font-family: monospace;
            white-space: nowrap;
        }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @php
        $freshItems = $report->items->where('category', 'fresh')->values();
        $frozenItems = $report->items->where('category', 'frozen')->values();

        $fmt2 = fn($v) => number_format((float)($v ?? 0), 2);

        $calcUsedCols = function ($items) {
            $used = [];
            foreach ($items as $item) {
                for ($c = 1; $c <= 10; $c++) {
                    $b = $item->getAttribute("bag_$c");
                    $k = $item->getAttribute("kg_$c");
                    if (($b !== null && $b !== '' && (float)$b > 0) ||
                        ($k !== null && $k !== '' && (float)$k > 0)) {
                        $used[$c] = true;
                    }
                }
            }
            $cols = array_keys($used);
            sort($cols);
            return $cols;
        };

        $renderTable = function ($title, $items, $totalBag, $totalKg) use ($calcUsedCols, $fmt2) {
            $usedCols = $calcUsedCols($items);
            $hasDetail = count($usedCols) > 0;

            echo '<div class="section-title">'.e($title).'</div>';

            if ($hasDetail) {
                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                echo '<th style="width: 35%;">Nama Produk</th>';
                foreach ($usedCols as $c) {
                    echo '<th colspan="2" style="text-align: center; font-size: 8.5px;">'.e($c).'</th>';
                }
                echo '<th class="text-right" style="width: 10%;">Total Bag</th>';
                echo '<th class="text-right" style="width: 10%;">Total Kg</th>';
                echo '</tr>';

                echo '<tr style="background: #1a4a7a;">';
                echo '<th style="font-size: 8px; color: #c8d8e8;"></th>';
                foreach ($usedCols as $c) {
                    echo '<th style="text-align: center; font-size: 8px; color: #c8d8e8;">Bag</th>';
                    echo '<th style="text-align: center; font-size: 8px; color: #c8d8e8; border-right: 1px solid #2a5a8a;">Kg</th>';
                }
                echo '<th style="color: #c8d8e8; font-size: 8px;"></th>';
                echo '<th style="color: #c8d8e8; font-size: 8px;"></th>';
                echo '</tr>';
                echo '</thead>';

                echo '<tbody>';
                if (count($items)) {
                    foreach ($items as $item) {
                        echo '<tr>';
                        echo '<td style="font-size: 9.5px;">'.e($item->product?->name ?? '—').'</td>';

                        foreach ($usedCols as $c) {
                            $b = $item->getAttribute("bag_$c");
                            $k = $item->getAttribute("kg_$c");
                            $bStr = ($b !== null && $b !== '' && (float)$b > 0) ? number_format((float)$b, 2) : '';
                            $kStr = ($k !== null && $k !== '' && (float)$k > 0) ? number_format((float)$k, 2) : '';
                            echo '<td style="text-align: right; font-size: 9px;">'.e($bStr).'</td>';
                            echo '<td style="text-align: right; font-size: 9px; border-right: 1px solid #e4edf5;">'.e($kStr).'</td>';
                        }

                        echo '<td class="text-right" style="font-size: 9.5px;">'.$fmt2($item->total_bag).'</td>';
                        echo '<td class="text-right" style="font-size: 9.5px;">'.$fmt2($item->total_kg).'</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="'.(3 + count($usedCols) * 2).'" class="text-center" style="color:#999;">Tidak ada item</td></tr>';
                }

                echo '<tr class="total-row">';
                echo '<td style="font-size: 9.5px;">TOTAL '.e($title).'</td>';
                foreach ($usedCols as $c) { echo '<td></td><td></td>'; }
                echo '<td class="text-right">'.$fmt2($totalBag).'</td>';
                echo '<td class="text-right">'.$fmt2($totalKg).'</td>';
                echo '</tr>';

                echo '</tbody></table>';
            } else {
                // fallback simple table
                echo '<table>';
                echo '<thead><tr>';
                echo '<th style="width: 50%;">Nama Produk</th>';
                echo '<th class="text-right" style="width: 25%;">Total Bag</th>';
                echo '<th class="text-right" style="width: 25%;">Total Kg</th>';
                echo '</tr></thead>';

                echo '<tbody>';
                if (count($items)) {
                    foreach ($items as $item) {
                        echo '<tr>';
                        echo '<td>'.e($item->product?->name ?? '—').'</td>';
                        echo '<td class="text-right">'.$fmt2($item->total_bag).'</td>';
                        echo '<td class="text-right">'.$fmt2($item->total_kg).'</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3" class="text-center" style="color:#999;">Tidak ada item</td></tr>';
                }

                echo '<tr class="total-row">';
                echo '<td style="font-size: 11px;">TOTAL '.e($title).'</td>';
                echo '<td class="text-right">'.$fmt2($totalBag).'</td>';
                echo '<td class="text-right">'.$fmt2($totalKg).'</td>';
                echo '</tr>';

                echo '</tbody></table>';
            }
        };
    @endphp

    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <div class="header-inner">
                <div class="header-text">
                    <p class="company">PT. Charoen Pokphand Indonesia</p>
                    <h1>LAPORAN HARIAN EVISCERATION</h1>
                </div>
                <div class="header-badge">
                    <p class="report-date">Tanggal: <strong>{{ $report->report_date->format('d M Y') }}</strong></p>
                </div>
            </div>
        </div>

        <!-- INFO SECTION -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Dibuat Oleh</div>
                <div class="info-value">{{ $report->createdBy->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Dibuat</div>
                <div class="info-value">{{ $report->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Lokasi</div>
                <div class="info-value">{{ $report->location ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Shift</div>
                <div class="info-value">{{ $report->shift ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jumlah Truk</div>
                <div class="info-value">{{ (int)($report->truck_count ?? 0) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ayam Diterima (Ekor)</div>
                <div class="info-value">{{ (int)($report->received_chicken ?? 0) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Yield (%)</div>
                <div class="info-value">
                    {{ $report->yield_percent !== null ? number_format((float)$report->yield_percent, 2) : '—' }}
                </div>
            </div>

            @if($report->isApproved())
                <div class="info-row">
                    <div class="info-label">Disetujui Oleh</div>
                    <div class="info-value">{{ $report->approvedBy->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Approval</div>
                    <div class="info-value">{{ $report->approved_at->format('d/m/Y H:i') }}</div>
                </div>
            @endif
        </div>

        <!-- TABLES -->
        {!! $renderTable('FRESH', $freshItems, $report->fresh_total_bag, $report->fresh_total_kg) !!}
        {!! $renderTable('FROZEN', $frozenItems, $report->frozen_total_bag, $report->frozen_total_kg) !!}

        <!-- GRAND TOTAL -->
        <table>
            <tbody>
                <tr class="total-row">
                    <td style="font-size: 11px;">TOTAL KESELURUHAN</td>
                    <td class="text-right" style="width: 25%;">{{ number_format((float)($report->total_bag ?? 0), 2) }}</td>
                    <td class="text-right" style="width: 25%;">{{ number_format((float)($report->total_kg ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SIGNATURE & QR CODE SECTION -->
        <div class="signature-section">
            <h3>TANDA TANGAN</h3>

            <div class="signatures">
                <!-- Left: Dibuat Oleh -->
                <div class="signature-box">
                    <strong style="font-size: 10px;">Dibuat Oleh</strong>

                    <div class="qr-code">
                        @if(!$report->isDraft() && $qrCreatedBy)
                            <img src="{{ $qrCreatedBy }}" alt="QR Code - Created By">
                        @endif
                    </div>

                    <div class="qr-label">
                        <strong>{{ $report->createdBy->name }}</strong><br>
                        {{ $report->created_at->format('d/m/Y H:i') }}
                    </div>

                    <div class="signature-line">
                        ___________________
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Right: Disetujui Oleh -->
                <div class="signature-box">
                    <strong style="font-size: 10px;">Disetujui Oleh</strong>

                    <div class="qr-code">
                        @if($report->isApproved() && $qrApprovedBy)
                            <img src="{{ $qrApprovedBy }}" alt="QR Code - Approved By">
                        @endif
                    </div>

                    <div class="qr-label">
                        @php $sigPath = $report->approved_signature_path; @endphp
                        @if($report->isApproved() && $sigPath)
                            <div style="margin-top: 8px;">
                                <img
                                    src="{{ public_path('storage/'.$sigPath) }}"
                                    alt="Signature Approved"
                                    style="height: 45px; width: auto;"
                                >
                            </div>
                            <strong>{{ $report->approvedBy->name }}</strong><br>
                            {{ $report->approved_at->format('d/m/Y H:i') }}
                        @else
                            <strong>&nbsp;</strong><br>
                            &nbsp;
                        @endif
                    </div>

                    <div class="signature-line">
                        ___________________
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Dokumen ini dihasilkan secara otomatis oleh CPI Paperless System</p>
            <p class="report-id">Report ID: {{ $report->id }} | Generated: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>