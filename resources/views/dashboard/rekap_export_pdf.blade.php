<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap LB – PT. Charoen Pokphand Indonesia</title>
    <style>
        /* ── Reset & Base ─────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1a1a2e;
            background: #ffffff;
            margin: 0;
            padding: 18px 22px 18px 22px;
        }

        /* ── KOP / LETTERHEAD ─────────────────────────────────── */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px solid #d32f2f;
            padding-bottom: 8px;
            margin-bottom: 4px;
        }

        .kop-logo {
            display: table-cell;
            width: 52px;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 46px;
            height: auto;
        }

        .kop-divider {
            display: table-cell;
            width: 2px;
            vertical-align: middle;
            padding: 0 8px;
        }

        .kop-divider-line {
            width: 1.5px;
            height: 40px;
            background: #d32f2f;
            margin: 0 auto;
        }

        .kop-text {
            display: table-cell;
            vertical-align: middle;
        }

        .kop-brand {
            font-size: 13px;
            font-weight: 700;
            color: #c62828;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .kop-sub {
            font-size: 8px;
            color: #555;
            margin-top: 2px;
            letter-spacing: 0.02em;
        }

        .kop-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 7.5px;
            color: #777;
        }

        .kop-right .doc-label {
            font-size: 8.5px;
            font-weight: 700;
            color: #c62828;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* ── DOCUMENT TITLE BAND ──────────────────────────────── */
        .title-band {
            background: #c62828;
            color: #fff;
            padding: 5px 8px;
            margin-bottom: 6px;
            border-radius: 2px;
        }

        .title-band-inner {
            display: table;
            width: 100%;
        }

        .title-band-left {
            display: table-cell;
            vertical-align: middle;
        }

        .title-band-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .title-band-period {
            font-size: 7.5px;
            opacity: 0.88;
            margin-top: 1px;
        }

        .title-band-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .badge {
            display: inline-block;
            background: rgba(255,255,255,0.22);
            border: 1px solid rgba(255,255,255,0.45);
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 7.5px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.04em;
        }

        /* ── TABLE ────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #1a1a2e;
            color: #fff;
        }

        thead th {
            padding: 5px 4px;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            text-align: center;
            border: 1px solid #2d2d4a;
            white-space: nowrap;
        }

        /* Sub-header group labels */
        .th-group {
            background: #2d3748;
            color: #a0aec0;
            font-size: 6.5px;
            letter-spacing: 0.1em;
            text-align: center;
            padding: 3px 4px;
        }

        tbody tr {
            border-bottom: 1px solid #e8eaf0;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fc;
        }

        tbody tr:hover {
            background: #fff3e0;
        }

        tbody td {
            padding: 4px 4px;
            font-size: 7.8px;
            color: #1a1a2e;
            border-right: 1px solid #e8eaf0;
            vertical-align: middle;
        }

        td.num {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
            color: #1a237e;
        }

        td.nowrap { white-space: nowrap; }

        td.center { text-align: center; }

        /* Row number */
        td.no-col {
            text-align: center;
            color: #999;
            font-size: 7px;
            background: #f0f1f5;
            border-right: 2px solid #d32f2f;
        }

        /* Highlight selisih negative */
        td.selisih-neg {
            color: #c62828;
            font-weight: 700;
        }

        td.selisih-pos {
            color: #2e7d32;
            font-weight: 700;
        }

        /* QC cells */
        td.qc {
            text-align: center;
            font-size: 7.5px;
        }

        /* ── EMPTY STATE ──────────────────────────────────────── */
        .empty-cell {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }

        /* ── FOOTER ───────────────────────────────────────────── */
        .footer {
            margin-top: 10px;
            border-top: 1px solid #e8eaf0;
            padding-top: 6px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            font-size: 7px;
            color: #9ca3af;
            vertical-align: bottom;
        }

        .footer-right {
            display: table-cell;
            text-align: right;
            vertical-align: bottom;
        }

        .signature-block {
            display: inline-block;
            text-align: center;
            font-size: 7px;
            color: #555;
            margin-left: 20px;
        }

        .signature-block .sig-line {
            border-top: 1px solid #555;
            margin-top: 28px;
            padding-top: 3px;
            width: 90px;
        }

        .accent-bar {
            height: 3px;
            background: linear-gradient(to right, #c62828, #ef9a9a);
            margin-bottom: 8px;
            border-radius: 1px;
        }
    </style>
</head>
<body>

    {{-- ── KOP SURAT ────────────────────────────────────────── --}}
    <div class="kop">
        <div class="kop-logo">
            <img src="{{ public_path('images/logo small.png') }}" alt="Logo CPI">
        </div>
        <div class="kop-divider">
            <div class="kop-divider-line"></div>
        </div>
        <div class="kop-text">
            <div class="kop-brand">PT. Charoen Pokphand Indonesia</div>
            <div class="kop-sub">Food Division – Salatiga</div>
        </div>
        <div class="kop-right">
            <div class="doc-label">Laporan Rekapitulasi Live Bird</div>
            <div style="margin-top:3px;">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ── ACCENT LINE ──────────────────────────────────────── --}}
    <div class="accent-bar"></div>

    {{-- ── TITLE BAND ───────────────────────────────────────── --}}
    <div class="title-band">
        <div class="title-band-inner">
            <div class="title-band-left">
                <div class="title-band-title">&#9654; DAFTAR TRUK LENGKAP</div>
                <div class="title-band-period">Periode: {{ $from }} &nbsp;s/d&nbsp; {{ $to }}</div>
            </div>
            <div class="title-band-right">
                <span class="badge">{{ count($rows) }} Truk</span>
            </div>
        </div>
    </div>

    {{-- ── TABLE ────────────────────────────────────────────── --}}
    <table>
        <thead>
            {{-- Group row --}}
            <tr>
                <th class="th-group" colspan="11" style="border-right:2px solid #4a5568;">INFORMASI TRUK</th>
                <th class="th-group" colspan="6" style="border-right:2px solid #4a5568;">DATA AYAM</th>
                <th class="th-group" colspan="3">QUALITY CONTROL</th>
            </tr>
            {{-- Column headers --}}
            <tr>
                <th style="width:18px;">No</th>
                <th>Tgl</th>
                <th>Shift</th>
                <th>Loc</th>
                <th>No Polisi</th>
                <th>Ekspedisi</th>
                <th>Farm</th>
                <th>Size</th>
                <th>Segel</th>
                <th>Bongkar</th>
                <th style="border-right:2px solid #4a5568;">Selesai</th>
                <th class="num">Ekor Plan</th>
                <th class="num">Mati</th>
                <th class="num">Retur</th>
                <th class="num">Ret Kg</th>
                <th class="num">Diterima</th>
                <th class="num" style="border-right:2px solid #4a5568;">Selisih</th>
                <th>QC Keranjang</th>
                <th>QC Platform</th>
                <th>QC Bulu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                @php
                    $selisih = (int) str_replace([',', '.'], '', $r['selisih']);
                @endphp
                <tr>
                    <td class="no-col">{{ $r['no'] }}</td>
                    <td class="nowrap">{{ $r['tanggal'] }}</td>
                    <td class="center">{{ $r['shift'] }}</td>
                    <td class="center nowrap">{{ $r['lokasi'] }}</td>
                    <td class="nowrap" style="font-weight:700;">{{ $r['no_polisi'] }}</td>
                    <td>{{ $r['expedition'] }}</td>
                    <td>{{ $r['farm'] }}</td>
                    <td class="center nowrap">{{ $r['size'] }}</td>
                    <td class="nowrap">{{ $r['seal_no'] }}</td>
                    <td class="center nowrap">{{ $r['jam_bongkar'] }}</td>
                    <td class="center nowrap" style="border-right:2px solid #e8eaf0;">{{ $r['jam_selesai'] }}</td>

                    <td class="num">{{ number_format((int)$r['total_ekor']) }}</td>
                    <td class="num" style="color:{{ (int)$r['ayam_mati'] > 0 ? '#c62828' : '#2e7d32' }};">
                        {{ number_format((int)$r['ayam_mati']) }}
                    </td>
                    <td class="num">{{ number_format((int)$r['ayam_retur']) }}</td>
                    <td class="num">{{ number_format((float)$r['retur_kg'], 2) }}</td>
                    <td class="num">{{ number_format((int)($r['hasil_shackle'] ?? 0)) }}</td>
                    <td class="num {{ $selisih < 0 ? 'selisih-neg' : ($selisih > 0 ? 'selisih-pos' : '') }}"
                        style="border-right:2px solid #e8eaf0;">
                        {{ $selisih > 0 ? '+' : '-' }}{{ number_format($selisih) }}
                    </td>

                    <td class="qc">{{ $r['qc_keranjang'] }}</td>
                    <td class="qc">{{ $r['qc_platform'] }}</td>
                    <td class="qc">{{ $r['qc_bulu'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="empty-cell">
                        &#9888; Tidak ada data.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── FOOTER ───────────────────────────────────────────── --}}
    <div class="footer">
        <div class="footer-left">
            &copy; {{ now()->year }} PT. Charoen Pokphand Indonesia – Salatiga &nbsp;|&nbsp;
            Dokumen ini dicetak secara otomatis oleh sistem.
        </div>
        <div class="footer-right">
            <div class="signature-block">
                <div class="sig-line">Supervisor</div>
            </div>
        </div>
    </div>

</body>
</html>