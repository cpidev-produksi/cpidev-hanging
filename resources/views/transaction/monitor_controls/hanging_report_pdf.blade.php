<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan {{ $mc->report_code }}</title>
<style>
  @page { margin: 30pt 41pt 26pt 45pt; }
  * { box-sizing: border-box; }
  body {
    margin: 0; padding: 0;
    font-family: Helvetica, Arial, sans-serif;
    font-size: 10.7pt; color: #000; line-height: 1.15;
  }
  table { border-collapse: collapse; }
  .sym { font-family: 'DejaVu Sans', sans-serif; }
  .minus { font-family: 'DejaVu Sans', sans-serif; font-size: 8.5pt; }

  /* ---------- kop ---------- */
  .hdr { width: 100%; }
  .hdr td { padding: 0; vertical-align: top; }
  .hdr-logo { width: 29.7pt; padding-top: 7pt; }
  .hdr-txt { font-size: 6.6pt; line-height: 9.3pt; padding-top: 7pt; }
  .hdr-txt .b { font-weight: bold; }

  .title { text-align: center; font-weight: bold; font-size: 11.8pt; }

  /* ---------- info (Tanggal / Lokasi / Shift) ---------- */
  .info { width: 100%; }
  .info td { padding: 0; height: 18.3pt; vertical-align: middle; }
  .info .il { width: 81.4pt; }
  .info .ic { width: 9.2pt; }
  .cb {
    display: inline-block; width: 11pt; height: 11pt;
    border: 0.8pt solid #000; vertical-align: middle;
    text-align: center; line-height: 11pt; font-size: 11pt; color: #fff;
  }
  .cb.on { color: #000; }
  .cb-txt { margin-left: 2pt; }

  .sec { font-weight: bold; font-size: 10.7pt; }

  /* ---------- tabel label | nilai ---------- */
  .kv { width: 100%; }
  .kv td {
    border: 0.7pt solid #333; height: 17.6pt; padding: 0 5.5pt;
    vertical-align: middle; font-size: 10.7pt;
  }
  .kv td.l { background: #f2f2f2; padding-left: 11.5pt; }
  .box { border: 0.7pt solid #333; height: 17.6pt; padding: 0 8.3pt; vertical-align: middle; font-weight: bold; }

  .layout { width: 100%; }
  .layout > tbody > tr > td { padding: 0; vertical-align: top; }

  /* ---------- halaman 2 ---------- */
  .grid { width: 100%; }
  .grid tr.ruler td { border: 0; padding: 0; height: 0; font-size: 0; line-height: 0; }
  .grid td {
    border: 0.7pt solid #333; padding: 0 2pt; text-align: center;
    vertical-align: middle; font-size: 10pt; white-space: nowrap;
  }
  .grid td.left { text-align: left; padding-left: 11pt; }
  .grid td.left2 { text-align: left; padding-left: 22pt; }
  .grid td.zebra { background: #f5f5f5; }
  .grid td.gap { border: 0; background: #fff; padding: 0; }
  .grid td.hd { font-size: 11pt; height: 18pt; }
  .grid td.hd2 { font-size: 10pt; height: 17pt; }
  .grid td.tot { font-weight: bold; }
  .grid tr.spacer td { border: 0; height: 5pt; padding: 0; }

  .sum { border-collapse: collapse; }
  .sum td { border: 0.7pt solid #333; height: 14pt; text-align: center; font-size: 10pt; vertical-align: middle; padding: 0; }
  .sum td.l { background: #f2f2f2; width: 95pt; }
  .sum td.v { width: 95pt; font-weight: bold; }

  .jam { border-collapse: collapse; }
  .jam td { border: 0.7pt solid #333; height: 18.3pt; font-size: 10.7pt; vertical-align: middle; padding: 0; }
  .jam td.l { background: #f2f2f2; width: 68.5pt; padding-left: 11.5pt; }
  .jam td.v { width: 77pt; text-align: center; font-weight: bold; }

  .sign { width: 100%; font-size: 7pt; }
  .sign td { text-align: center; padding: 0; vertical-align: top; }
  .formcode { text-align: right; font-size: 7pt; }
</style>
</head>
<body>
@php
  $logoPath = public_path('images/cp-logo.png');
  $logo = is_file($logoPath)
      ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
      : null;
  $isSH01 = ($r['location'] === 'SH01');
  $isSH02 = ($r['location'] === 'SH02');
  $nCol   = (int) $r['set_count'];

  /* lebar kolom halaman 2 (pt) */
  $contentW = 509.3;
  $wNo = 34.6; $wBlok = 60.5; $wJml = 97.0; $gapW = 5.5;
  $groupW  = ($contentW - $wNo - $wBlok - $wJml - ($gapW * $nCol)) / max($nCol, 1);
  $subW    = $groupW / 2;
  $nRows   = max(count($r['rows']), 1);
  $rowH    = $nRows >= 30 ? 14.0 : min(24, round(14.0 * 30 / $nRows, 1));
@endphp

{{-- ====================== HALAMAN 1 ====================== --}}
<table class="hdr"><tr>
  <td class="hdr-logo">@if($logo)<img src="{{ $logo }}" style="width:27pt;height:auto">@endif</td>
  <td class="hdr-txt">
    <div class="b">PT. Charoen Pokphand Indonesia</div>
    <div>Food Division</div>
    <div>Slaughterhouse Department - Salatiga</div>
  </td>
</tr></table>

<div class="title" style="margin-top:31pt; margin-bottom:20pt;">FORM PERHITUNGAN AYAM</div>

<table class="info">
  <tr><td class="il">Tanggal</td><td class="ic">:</td><td>{{ $r['tanggal'] }}</td></tr>
  <tr><td class="il">Lokasi</td><td class="ic">:</td><td><span class="cb sym {{ $isSH01 ? 'on' : '' }}">&#10004;</span><span class="cb-txt">SH01</span><span class="cb sym {{ $isSH02 ? 'on' : '' }}" style="margin-left:16pt">&#10004;</span><span class="cb-txt">SH02</span></td></tr>
  <tr><td class="il">Shift</td><td class="ic">:</td><td>{{ $r['shift'] }}</td></tr>
</table>

<div class="sec" style="margin-top:12pt; margin-bottom:8pt;">DTA</div>

<table class="layout"><tr>
  <td style="width:326.9pt">
    <table class="kv">
      <tr><td class="l" style="width:68pt">Farm</td><td>{{ $r['farm'] }}</td></tr>
      <tr><td class="l">Ekspedisi</td><td>{{ $r['ekspedisi'] }}</td></tr>
      <tr><td class="l">No. Polisi</td><td>{{ $r['no_polisi'] }}</td></tr>
      <tr><td class="l">No. Segel</td><td>{{ $r['no_segel'] }}</td></tr>
      <tr><td class="l">Size Ayam</td><td>{{ $r['size'] }}</td></tr>
      <tr><td class="l">ABW</td><td>{{ $r['abw'] }}</td></tr>
      <tr><td class="l">Total Ekor</td><td>{{ $r['total_ekor'] }}</td></tr>
      <tr><td class="l">Total Kilo</td><td>{{ $r['total_kilo'] }}</td></tr>
    </table>
  </td>
  <td style="width:6.5pt"></td>
  <td style="width:176.2pt">
    <table class="kv">
      <tr><td class="l" style="width:88pt; padding-left:10.7pt; white-space:nowrap">No. SPPA</td><td>{{ $r['sppa_no'] }}</td></tr>
      <tr><td class="l" style="padding-left:10.7pt">Tgl. SPPA</td><td>{{ $r['sppa_date'] }}</td></tr>
      <tr><td class="l" style="padding-left:10.7pt">Order ID</td><td>{{ $r['order_id'] }}</td></tr>
      <tr><td class="l" style="padding-left:10.7pt">Tgl. Tangkap</td><td>{{ $r['catch_date'] }}</td></tr>
      <tr><td class="l" style="padding-left:10.7pt">Jam Truk Datang</td><td>{{ $r['arrival'] }}</td></tr>
    </table>
  </td>
</tr></table>

<table style="width:100%; margin-top:12pt; margin-bottom:6pt;"><tr>
  <td class="sec" style="padding:0">HANGING AYAM</td>
  <td style="padding:0; text-align:right; font-size:10.7pt;">No. Urut : #{{ $r['truck_no'] }}</td>
</tr></table>

<table class="kv">
  <tr><td class="l" style="width:238pt">Ekor Plan</td><td style="padding-left:8.3pt">{{ $r['ekor_plan'] }}</td></tr>
  <tr><td class="l">Blok Kondisional</td><td style="padding-left:8.3pt">
    @if(count($r['cond_groups']) === 0)
      -
    @else
      @foreach($r['cond_groups'] as $gi => $g)
        @if($gi > 0)<br>@endif
        {{ $g['count'] }} &times; {{ $g['cap'] }}@if($g['empty'] > 0) <span class="minus">&minus;</span> {{ $g['empty'] }}@endif = {{ number_format($g['ayam'], 0, ',', '.') }}
      @endforeach
    @endif
  </td></tr>
  <tr><td class="l">Blok Penuh</td><td style="padding-left:8.3pt">
    @if(count($r['full_groups']) === 0)
      -
    @else
      @foreach($r['full_groups'] as $g)
        {{ $g['count'] }} &times; {{ $g['cap'] }}@if($g['empty'] > 0) <span class="minus">&minus;</span> {{ $g['empty'] }}@endif = {{ number_format($g['ayam'], 0, ',', '.') }}
      @endforeach
    @endif
  </td></tr>
  <tr><td class="l">Shackle Kosong</td><td style="padding-left:8.3pt">{{ $r['shackle_kosong'] }}</td></tr>
  <tr><td class="l">Status</td><td style="padding-left:8.3pt">{{ $r['status'] }}</td></tr>
  <tr><td class="l">Selisih</td><td style="padding-left:8.3pt">{{ $r['selisih_text'] }}</td></tr>
</table>

<table style="width:100%; margin-top:8pt;"><tr>
  <td style="width:242pt; text-align:right; font-weight:bold; padding-right:12pt;">Jumlah Ayam Diterima</td>
  <td class="box">{{ $r['diterima'] }}</td>
</tr></table>

<div class="sec" style="margin-top:15.5pt; margin-bottom:6pt;">AYAM RETUR &amp; MATI</div>
<table class="kv">
  <tr><td class="l" style="width:238pt">Ayam Mati</td><td style="padding-left:8.3pt">{{ $r['dead'] }}</td></tr>
  <tr><td class="l">Ayam Retur</td><td style="padding-left:8.3pt">{{ $r['retur'] }}</td></tr>
  <tr><td class="l">Berat Retur (kg)</td><td style="padding-left:8.3pt">{{ $r['retur_kg'] }}</td></tr>
</table>

<div class="sec" style="margin-top:18pt; margin-bottom:6pt;">QC KONDISI</div>
<table class="kv">
  <tr><td class="l" style="width:238pt">Keranjang</td><td style="padding-left:8.3pt">{{ $r['qc_basket'] }}</td></tr>
  <tr><td class="l">Platform Truck</td><td style="padding-left:8.3pt">{{ $r['qc_platform'] }}</td></tr>
  <tr><td class="l">Bulu Ayam</td><td style="padding-left:8.3pt">{{ $r['qc_feather'] }}</td></tr>
</table>

{{-- ====================== HALAMAN 2 ====================== --}}
<div style="page-break-before: always;"></div>

<table class="hdr"><tr>
  <td class="hdr-logo">@if($logo)<img src="{{ $logo }}" style="width:27pt;height:auto">@endif</td>
  <td class="hdr-txt">
    <div class="b">PT. Charoen Pokphand Indonesia</div>
    <div>Food Division</div>
    <div>Slaughterhouse Department - Salatiga</div>
  </td>
</tr></table>

<div class="title" style="margin-top:20pt; margin-bottom:14pt;">FORM PERHITUNGAN HANGING AYAM</div>

<table class="layout"><tr>
  <td style="width:350pt">
    <table class="info">
      <tr><td class="il">Tanggal</td><td class="ic">:</td><td>{{ $r['tanggal'] }}</td></tr>
      <tr><td class="il">Lokasi</td><td class="ic">:</td><td><span class="cb sym {{ $isSH01 ? 'on' : '' }}">&#10004;</span><span class="cb-txt">SH01</span><span class="cb sym {{ $isSH02 ? 'on' : '' }}" style="margin-left:16pt">&#10004;</span><span class="cb-txt">SH02</span></td></tr>
      <tr><td class="il">Shift</td><td class="ic">:</td><td>{{ $r['shift'] }}</td></tr>
      <tr><td class="il">No. Urut</td><td class="ic">:</td><td>#{{ $r['truck_no'] }}</td></tr>
    </table>
  </td>
  <td style="width:159pt; vertical-align:top">
    <table class="jam">
      <tr><td class="l">Jam Bongkar</td><td class="v">{{ $r['unloading'] }}</td></tr>
      <tr><td class="l">Jam Selesai</td><td class="v">{{ $r['finish'] }}</td></tr>
    </table>
  </td>
</tr></table>

<table class="grid" style="margin-top:4.7pt">
  <tr class="ruler">
    <td style="width:{{ $wNo }}pt"></td><td style="width:{{ $wBlok }}pt"></td><td style="width:{{ $wJml }}pt"></td>
    @for($s = 1; $s <= $nCol; $s++)
      <td style="width:{{ $gapW }}pt"></td><td style="width:{{ $subW }}pt"></td><td style="width:{{ $subW }}pt"></td>
    @endfor
  </tr>
  <tr>
    <td class="hd" rowspan="2">No.</td>
    <td class="hd" rowspan="2">Blok</td>
    <td class="hd" rowspan="2">Jumlah Shackle</td>
    @for($s = 1; $s <= $nCol; $s++)
      <td class="gap" rowspan="2"></td>
      <td class="hd" colspan="2">Kolom {{ $s }}</td>
    @endfor
  </tr>
  <tr>
    @for($s = 1; $s <= $nCol; $s++)
      <td class="hd2">Kosong</td><td class="hd2">Ayam</td>
    @endfor
  </tr>
  @foreach($r['rows'] as $i => $row)
    @php $z = ($i % 2 === 1) ? 'zebra' : ''; @endphp
    <tr>
      <td class="{{ $z }}" style="height:{{ $rowH }}pt">{{ $row['no'] }}</td>
      <td class="left {{ $z }}">{{ $row['blok'] }}</td>
      <td class="left2 {{ $z }}">{{ $row['range'] }}</td>
      @for($s = 1; $s <= $nCol; $s++)
        <td class="gap"></td>
        @if($row['cells'][$s] === null)
          <td class="{{ $z }}"></td><td class="{{ $z }}"></td>
        @else
          <td class="{{ $z }}">{{ $row['cells'][$s]['empty'] }}</td><td class="{{ $z }}">{{ $row['cells'][$s]['ayam'] }}</td>
        @endif
      @endfor
    </tr>
  @endforeach
  <tr class="spacer"><td colspan="{{ 3 + 3 * $nCol }}"></td></tr>
  <tr>
    <td colspan="3" class="tot" style="text-align:right; padding-right:8pt; height:16pt;">Total</td>
    @for($s = 1; $s <= $nCol; $s++)
      <td class="gap"></td>
      @if($r['col_totals'][$s]['filled'] === 0)
        <td class="tot"></td><td class="tot"></td>
      @else
        <td class="tot">{{ $r['col_totals'][$s]['empty'] }}</td><td class="tot">{{ $r['col_totals'][$s]['ayam'] }}</td>
      @endif
    @endfor
  </tr>
</table>

<table class="layout" style="margin-top:6pt"><tr>
  <td>
    <table class="sum">
      <tr><td class="l">Jumlah Diterima</td><td class="v">{{ $r['total_ayam'] }}</td></tr>
      <tr><td class="l">Jumlah Kosong</td><td class="v">{{ $r['total_kosong'] }}</td></tr>
    </table>
  </td>
  <td class="formcode" style="vertical-align:top; padding-top:3pt;">{{ $r['form_code'] }}</td>
</tr></table>

@php $sg = $r['signatures'] ?? []; @endphp
<table class="sign" style="margin-top:14pt">
  <tr>
    <td style="width:170pt">Dibuat oleh,</td>
    <td style="width:170pt">Diketahui oleh,</td>
    <td style="width:169pt">Disetujui oleh,</td>
  </tr>
  <tr>
    <td style="height:52pt; vertical-align:middle">
      @if(!empty($sg['created']['qr']))<img src="{{ $sg['created']['qr'] }}" style="width:48pt;height:48pt">@endif
    </td>
    <td style="vertical-align:middle">
      @if(!empty($sg['known']['qr']))<img src="{{ $sg['known']['qr'] }}" style="width:48pt;height:48pt">@endif
    </td>
    <td style="vertical-align:middle">
      @if(!empty($sg['approved']['qr']))
        <img src="{{ $sg['approved']['qr'] }}" style="width:48pt;height:48pt">
      @elseif(!empty($r['sign_image']))
        {{-- tanda tangan supervisor lama (hasil menu Summary) tetap tampil jika belum di-Approve --}}
        <img src="{{ $r['sign_image'] }}" style="height:38pt;width:auto">
      @endif
    </td>
  </tr>
  <tr>
    <td>( {{ $sg['created']['name'] ?? 'Checker' }} )</td>
    <td>( {{ $sg['known']['name'] ?? 'Foreman/lady' }} )</td>
    <td>( {{ $sg['approved']['name'] ?? (!empty($r['sign_image']) && !empty($r['sign_name']) ? $r['sign_name'] : 'Supervisor') }} )</td>
  </tr>
</table>

</body>
</html>