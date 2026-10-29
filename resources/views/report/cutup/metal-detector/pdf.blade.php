@php
    $hm   = fn ($t) => $t ? substr($t, 0, 5) : '';
    $fmt  = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
    $dia  = [
        'fe'     => $report->fe_diameter !== null ? $fmt($report->fe_diameter) : '',
        'nonfe'  => $report->nonfe_diameter !== null ? $fmt($report->nonfe_diameter) : '',
        'sus316' => $report->sus316_diameter !== null ? $fmt($report->sus316_diameter) : '',
    ];

    $checks = $report->checks;
    $verifs = $report->verifications;

    // No. Metal Detector: tampil di header; jika lebih dari satu, tampil juga per baris
    $mdNos  = $checks->pluck('metal_detector_no')->merge($verifs->pluck('metal_detector_no'))->filter()->unique()->sort()->values();
    $multi  = $mdNos->count() > 1;

    $minCheckRows = 6;
    $minVerifRows = 28;
    $padChecks = max(0, $minCheckRows - $checks->count());
    $padVerifs = max(0, $minVerifRows - $verifs->count());

    $mark = fn ($v) => $v === 'OK' ? '&#10003;' : ($v === 'NOT OK' ? '&#10007;' : '');
    $logo = public_path('images/logo small.png');   // opsional: taruh logo di public/images/logo-cp.png
    $hasLogo = file_exists($logo);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Metal Detector</title>
    <style>
        @page { margin: 26px 34px 22px 34px; }
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 7.5px; color: #000; margin: 0; }

        table { border-collapse: collapse; width: 100%; }
        .grid td, .grid th { border: 1px solid #000; padding: 1.5px 2px; text-align: center; vertical-align: middle; height: 11px; font-weight: normal; }
        .grid th { font-size: 7.5px; }
        .grid td.l { text-align: left; padding-left: 4px; }
        .no-border td { border: none; padding: 0; }

        .logo-box { width: 34px; height: 34px; background: #e60012; color: #fff; text-align: center; font-weight: bold; font-size: 15px; line-height: 34px; }
        .company { font-size: 8.5px; line-height: 1.25; }
        .title { text-align: center; font-size: 11.5px; font-weight: bold; margin: 8px 0 7px; letter-spacing: .2px; }
        .info td { font-size: 8px; padding: 1.5px 0; }
        .sec { font-weight: bold; font-size: 8px; margin: 7px 0 0; }
        .sub { font-size: 8px; margin: 0 0 2px; }
        .bad { font-weight: bold; }
        .ck { font-size: 8px; }
        .code { font-size: 6.6px; letter-spacing: .3px; }
        .docno { text-align: right; font-size: 7px; margin: 3px 0 0; }
        .sign td { text-align: center; font-size: 8px; vertical-align: top; }
        .sign .space { height: 34px; }
        .sign .nm { font-weight: bold; text-decoration: underline; min-height: 10px; }
        .small { font-size: 6px; color: #333; }
    </style>
</head>
<body>

{{-- ============ KOP ============ --}}
<table class="no-border">
    <tr>
        <td style="width:42px;">
            @if($hasLogo)
                <img src="{{ $logo }}" style="width:34px; height:34px;">
            @else
                <div class="logo-box">CP</div>
            @endif
        </td>
        <td class="company">
            PT. Charoen Pokphand Indonesia<br>
            Food Division<br>
            Salatiga
        </td>
    </tr>
</table>

<div class="title">PEMERIKSAAN METAL DETECTOR</div>

{{-- ============ INFO ============ --}}
<table class="info no-border">
    <tr>
        <td style="width:62px;">Hari, Tanggal</td>
        <td style="width:210px;">: {{ $report->date_long }}</td>
        <td></td>
        <td style="width:62px;">Jam Mulai</td>
        <td style="width:70px;">: {{ $hm($report->start_time) }}</td>
    </tr>
    <tr>
        <td>Lokasi</td>
        <td>:
            <span style="font-size:9px;">{!! $report->location === 'SH01' ? '&#9745;' : '&#9744;' !!}</span> SH01
            &nbsp;&nbsp;
            <span style="font-size:9px;">{!! $report->location === 'SH02' ? '&#9745;' : '&#9744;' !!}</span> SH02
        </td>
        <td></td>
        <td>Jam Istirahat</td>
        <td>: {{ $hm($report->break_time) }}</td>
    </tr>
    <tr>
        <td>Shift</td>
        <td>: {{ $report->shift }}</td>
        <td></td>
        <td>Jam Pulang</td>
        <td>: {{ $hm($report->end_time) }}</td>
    </tr>
</table>

{{-- ============ PEMERIKSAAN SENSITIVITAS ============ --}}
<div class="sec">Pemeriksaan Sensitivitas</div>
<div class="sub">Metal Detector No. {{ $mdNos->isNotEmpty() ? $mdNos->implode(' / ') : '______' }}</div>

<table class="grid">
    <colgroup>
        <col style="width:11%"><col style="width:12%"><col style="width:12%"><col style="width:12%">
        <col style="width:31%"><col style="width:22%">
    </colgroup>
    <thead>
        <tr>
            <th rowspan="3">Pukul</th>
            <th colspan="3">Jenis Spesimen</th>
            <th rowspan="3">Kesimpulan</th>
            <th rowspan="3">Keterangan</th>
        </tr>
        <tr><th>Fe</th><th>Non Fe</th><th>SUS 316</th></tr>
        <tr>
            <th>&Oslash; : {{ $dia['fe'] }} mm</th>
            <th>&Oslash; : {{ $dia['nonfe'] }} mm</th>
            <th>&Oslash; : {{ $dia['sus316'] }} mm</th>
        </tr>
    </thead>
    <tbody>
        @foreach($checks as $c)
            <tr>
                <td>{{ $hm($c->check_time) }}@if($multi)<br><span class="small">MD {{ $c->metal_detector_no }}</span>@endif</td>
                <td class="{{ $c->fe_result === 'NOT OK' ? 'bad' : '' }}">{{ $c->fe_result }}</td>
                <td class="{{ $c->nonfe_result === 'NOT OK' ? 'bad' : '' }}">{{ $c->nonfe_result }}</td>
                <td class="{{ $c->sus316_result === 'NOT OK' ? 'bad' : '' }}">{{ $c->sus316_result }}</td>
                <td class="{{ $c->conclusion === 'NOT_USABLE' ? 'bad' : '' }}">{{ $c->conclusion === 'USABLE' ? 'Dapat Digunakan' : 'Tidak Dapat Digunakan' }}</td>
                <td class="l">{{ $c->remark }}</td>
            </tr>
        @endforeach
        @for($i = 0; $i < $padChecks; $i++)
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
        @endfor
    </tbody>
</table>

{{-- ============ VERIFIKASI SENSITIVITAS ============ --}}
<div class="sec" style="margin-top:10px;">Verifikasi Sensitivitas</div>
<div class="sub">Metal Detector No. {{ $mdNos->isNotEmpty() ? $mdNos->implode(' / ') : '______' }}</div>

<table class="grid">
    <colgroup>
        <col style="width:7%"><col style="width:33%">
        @for($i = 0; $i < 9; $i++)<col style="width:3.6%">@endfor
        <col style="width:27.6%">
    </colgroup>
    <thead>
        <tr>
            <th rowspan="4">Pukul</th>
            <th rowspan="4">Nama Produk</th>
            <th colspan="9">Jenis Spesimen</th>
            <th rowspan="4">Kode Produk</th>
        </tr>
        <tr><th colspan="3">Fe</th><th colspan="3">Non Fe</th><th colspan="3">SUS 316</th></tr>
        <tr>
            <th colspan="3">&Oslash; : {{ $dia['fe'] }} mm</th>
            <th colspan="3">&Oslash; : {{ $dia['nonfe'] }} mm</th>
            <th colspan="3">&Oslash; : {{ $dia['sus316'] }} mm</th>
        </tr>
        <tr>
            @for($i = 0; $i < 3; $i++)<th>D</th><th>T</th><th>B</th>@endfor
        </tr>
    </thead>
    <tbody>
        @foreach($verifs as $v)
            <tr>
                <td>{{ $hm($v->check_time) }}</td>
                <td class="l">{{ $v->product_name }}@if($multi && $v->metal_detector_no) <span class="small">(MD {{ $v->metal_detector_no }})</span>@endif</td>
                @foreach(\App\Models\CutUpMdVerification::RESULT_COLUMNS as $col)
                    <td class="ck {{ $v->{$col} === 'NOT OK' ? 'bad' : '' }}">{!! $mark($v->{$col}) !!}</td>
                @endforeach
                <td class="code">{{ $v->product_code }}</td>
            </tr>
        @endforeach
        @for($i = 0; $i < $padVerifs; $i++)
            <tr><td>&nbsp;</td><td></td>@for($j = 0; $j < 9; $j++)<td></td>@endfor<td></td></tr>
        @endfor
    </tbody>
</table>

<div class="docno">SH 30 / 03</div>

{{-- ============ TANDA TANGAN ============ --}}
<table class="sign no-border" style="margin-top:6px;">
    <tr>
        <td style="width:33%;">Dibuat oleh,</td>
        <td style="width:34%;">Disetujui oleh,</td>
        <td style="width:33%;">Diketahui oleh,</td>
    </tr>
    <tr class="space"><td class="space">&nbsp;</td><td class="space"></td><td class="space"></td></tr>
    <tr>
        <td><div class="nm">{{ $report->made_by ?: '' }}</div>( Operator MD )</td>
        <td><div class="nm">{{ $report->approved_by ?: '' }}</div>( SPV / Foreman/lady Produksi )</td>
        <td><div class="nm">{{ $report->known_by ?: '' }}</div>( Foreman/lady QC )</td>
    </tr>
</table>

</body>
</html>
