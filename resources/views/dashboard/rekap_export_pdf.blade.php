<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap LB – PT. Charoen Pokphand Indonesia</title>
    <style>
        /* ── Page setup ─────────────────────────────────────────── */
        @page {
            size: A4 landscape;
            margin: 12mm 10mm 14mm 10mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1a1a2e;
            background: #f4f5f7;
            padding: 8px;
        }

        /* ── MAIN WRAPPER (menjorok ke dalam) ─────────────────── */
        .page-wrapper {
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 18px 22px 22px 22px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        /* ── KOP / LETTERHEAD ─────────────────────────────────── */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 2.5px solid #c62828;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .kop-logo {
            display: table-cell;
            width: 52px;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 44px;
            height: auto;
        }

        .kop-divider {
            display: table-cell;
            width: 16px;
            vertical-align: middle;
        }

        .kop-divider-line {
            width: 1.5px;
            height: 38px;
            background: #d1d5db;
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
            letter-spacing: 0.02em;
            line-height: 1.25;
        }

        .kop-sub {
            font-size: 8px;
            color: #6b7280;
            margin-top: 3px;
            letter-spacing: 0.02em;
        }

        .kop-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 7.5px;
            color: #6b7280;
            white-space: nowrap;
        }

        .kop-right .doc-label {
            font-size: 9px;
            font-weight: 700;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .kop-right .doc-meta {
            margin-top: 4px;
            color: #9ca3af;
        }

        /* ── TITLE BAND ───────────────────────────────────────── */
        .title-band {
            background: linear-gradient(135deg, #c62828 0%, #b71c1c 100%);
            color: #fff;
            padding: 8px 14px;
            margin-bottom: 14px;
            border-radius: 4px;
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
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .title-band-period {
            font-size: 8px;
            color: #ffcdd2;
            margin-top: 3px;
        }

        .title-band-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .badge {
            display: inline-block;
            background: rgba(255,255,255,0.20);
            border: 1px solid rgba(255,255,255,0.50);
            border-radius: 12px;
            padding: 3px 12px;
            font-size: 8px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.03em;
        }

        /* ── TABLE ────────────────────────────────────────────── */
        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        /* Header group row */
        thead .th-group {
            background: #374357;
            color: #d1d5db;
            font-size: 6.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-align: center;
            padding: 4px 4px;
            border: 1px solid #4a5568;
        }

        /* Header column row */
        thead .th-col {
            background: #1f2733;
            color: #fff;
            padding: 6px 4px;
            font-size: 6.8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #2d3748;
            word-break: break-word;
        }

        .group-end { border-right: 2px solid #c62828 !important; }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody tr:hover {
            background: #f3f4f6;
        }

        tbody td {
            padding: 5px 5px;
            font-size: 7.6px;
            color: #1a1a2e;
            border-right: 1px solid #e5e7eb;
            vertical-align: middle;
            word-break: break-word;
        }

        td.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
            color: #1a237e;
            white-space: nowrap;
        }

        td.nowrap { white-space: nowrap; }

        td.center { text-align: center; }

        .col-end { border-right: 2px solid #d1d5db !important; }

        /* Row number */
        td.no-col {
            text-align: center;
            color: #9ca3af;
            font-size: 7px;
            background: #f3f4f6;
            border-right: 2px solid #c62828;
        }

        td.selisih-neg { color: #c62828; font-weight: 700; }
        td.selisih-pos { color: #2e7d32; font-weight: 700; }
        td.selisih-zero { color: #6b7280; }

        td.na { color: #9ca3af; text-align: center; }

        td.qc {
            text-align: center;
            font-size: 7.5px;
        }

        /* ── EMPTY STATE ──────────────────────────────────────── */
        .empty-cell {
            text-align: center;
            padding: 28px;
            color: #9ca3af;
            font-style: italic;
            font-size: 9px;
        }

        /* ── FOOTER ───────────────────────────────────────────── */
        .footer {
            margin-top: 16px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 70%;
            font-size: 7px;
            color: #9ca3af;
            vertical-align: bottom;
            line-height: 1.6;
        }

        .footer-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: bottom;
        }

        .signature-block {
            display: inline-block;
            text-align: center;
            font-size: 7.5px;
            color: #4b5563;
        }

        .signature-block .sig-line {
            border-top: 1px solid #9ca3af;
            margin-top: 28px;
            padding-top: 4px;
            width: 110px;
        }

        .page-info {
            font-size: 6.5px;
            color: #d1d5db;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="page-wrapper">

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
            <div class="kop-sub">Food Division &ndash; Salatiga</div>
        </div>
        <div class="kop-right">
            <div class="doc-label">Laporan Rekapitulasi Live Bird</div>
            <div class="doc-meta">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ── TITLE BAND ───────────────────────────────────────── --}}
    <div class="title-band">
        <div class="title-band-inner">
            <div class="title-band-left">
                <div class="title-band-title">DAFTAR TRUK LENGKAP</div>
                <div class="title-band-period">Periode: {{ $from }} s/d {{ $to }}</div>
            </div>
            <div class="title-band-right">
                <span class="badge">{{ count($rows) }} Truk</span>
            </div>
        </div>
    </div>

    {{-- ── TABLE ────────────────────────────────────────────── --}}
    <div class="table-wrap">
        <table>
            <colgroup>
                {{-- Informasi truk --}}
                <col style="width:2.2%">
                <col style="width:4.8%">
                <col style="width:3%">
                <col style="width:4%">
                <col style="width:6.5%">
                <col style="width:6.5%">
                <col style="width:5.5%">
                <col style="width:3%">
                <col style="width:5%">
                <col style="width:4%">
                <col style="width:4%">
                {{-- Data ayam --}}
                <col style="width:5%">
                <col style="width:4%">
                <col style="width:4%">
                <col style="width:4.2%">
                <col style="width:5%">
                <col style="width:5%">
                <col style="width:4%">
                <col style="width:5%">
                {{-- QC --}}
                <col style="width:5.1%">
                <col style="width:5.1%">
                <col style="width:5.1%">
            </colgroup>
            <thead>
                {{-- Group row --}}
                <tr>
                    <th class="th-group group-end" colspan="11">INFORMASI TRUK</th>
                    <th class="th-group group-end" colspan="8">DATA AYAM</th>
                    <th class="th-group" colspan="3">QUALITY CONTROL</th>
                </tr>
                {{-- Column headers --}}
                <tr>
                    <th class="th-col">No</th>
                    <th class="th-col">Tgl</th>
                    <th class="th-col">Shift</th>
                    <th class="th-col">Loc</th>
                    <th class="th-col">No Polisi</th>
                    <th class="th-col">Ekspedisi</th>
                    <th class="th-col">Farm</th>
                    <th class="th-col">Size</th>
                    <th class="th-col">Segel</th>
                    <th class="th-col">Bongkar</th>
                    <th class="th-col group-end">Selesai</th>
                    <th class="th-col">Ekor Plan</th>
                    <th class="th-col">Mati</th>
                    <th class="th-col">Retur</th>
                    <th class="th-col">Ret Kg</th>
                    <th class="th-col">Selisih</th>
                    <th class="th-col">Diterima</th>
                    <th class="th-col">Jetson</th>
                    <th class="th-col group-end">Selisih<br>Jetson</th>
                    <th class="th-col">QC<br>Keranjang</th>
                    <th class="th-col">QC<br>Platform</th>
                    <th class="th-col">QC Bulu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    @php
                        $selisih = (int) str_replace([',', '.'], '', $r['selisih']);
                        $selisihClass = $selisih < 0 ? 'selisih-neg' : ($selisih > 0 ? 'selisih-pos' : 'selisih-zero');
                        $isSH02 = ($r['lokasi'] ?? null) === 'SH02';
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
                        <td class="center nowrap col-end">{{ $r['jam_selesai'] }}</td>

                        <td class="num">{{ number_format((int)$r['total_ekor']) }}</td>
                        <td class="num" style="color:{{ (int)$r['ayam_mati'] > 0 ? '#c62828' : '#2e7d32' }};">
                            {{ number_format((int)$r['ayam_mati']) }}
                        </td>
                        <td class="num">{{ number_format((int)$r['ayam_retur']) }}</td>
                        <td class="num">{{ number_format((float)$r['retur_kg'], 2) }}</td>
                        <td class="num {{ $selisihClass }}">
                            {{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih) }}
                        </td>
                        <td class="num">{{ number_format((int)($r['hasil_shackle'] ?? 0)) }}</td>
                        <td class="num">
                            @if(!$isSH02)
                                <span class="na">&mdash;</span>
                            @elseif(is_null($r['jetson_count'] ?? null))
                                <span class="na">n/a</span>
                            @else
                                {{ number_format($r['jetson_count']) }}
                            @endif
                        </td>
                        <td class="num col-end">
                            @if(!$isSH02 || is_null($r['jetson_selisih'] ?? null))
                                <span class="na">&mdash;</span>
                            @else
                                @php($jsel = $r['jetson_selisih'])
                                <span class="{{ $jsel < 0 ? 'selisih-neg' : ($jsel > 0 ? 'selisih-pos' : 'selisih-zero') }}">
                                    {{ $jsel > 0 ? '+' : '' }}{{ number_format($jsel) }}
                                </span>
                            @endif
                        </td>

                        <td class="qc">{{ $r['qc_keranjang'] }}</td>
                        <td class="qc">{{ $r['qc_platform'] }}</td>
                        <td class="qc">{{ $r['qc_bulu'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="22" class="empty-cell">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── FOOTER ───────────────────────────────────────────── --}}
    <div class="footer">
        <div class="footer-left">
            &copy; {{ now()->year }} PT. Charoen Pokphand Indonesia &ndash; Salatiga<br>
            Dokumen ini dicetak secara otomatis oleh sistem.
            <div class="page-info">Halaman {{ $page ?? '1' }} dari {{ $totalPages ?? '1' }}</div>
        </div>
        <div class="footer-right">
            <div class="signature-block">
                <div class="sig-line">Supervisor</div>
            </div>
        </div>
    </div>

</div>

</body>
</html>