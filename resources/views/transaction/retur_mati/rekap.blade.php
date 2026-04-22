@extends('layouts.app')

@section('content')
<div style="max-width:1100px;margin:0 auto;padding:28px 24px;">
  <h1 style="font-size:20px;font-weight:900;margin:0 0 6px;">Rekap Ayam Mati & Ayam Retur</h1>
  <div style="color:#6B7896;font-weight:700;margin-bottom:16px;">
    Periode: <strong>{{ $p['start'] }}</strong> s/d <strong>{{ $p['end'] }}</strong>
  </div>

  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px;">
    <div>
      <label style="display:block;font-size:12px;color:#6B7896;font-weight:800;">Mode</label>
      <select name="mode" style="padding:8px 10px;border:1px solid #E2E5EE;border-radius:8px;">
        <option value="daily" @selected($p['mode']==='daily')>Harian</option>
        <option value="monthly" @selected($p['mode']==='monthly')>Bulanan</option>
        <option value="range" @selected($p['mode']==='range')>Range</option>
      </select>
    </div>

    <div>
      <label style="display:block;font-size:12px;color:#6B7896;font-weight:800;">Tanggal (Harian)</label>
      <input type="date" name="date" value="{{ $p['date'] }}"
             style="padding:8px 10px;border:1px solid #E2E5EE;border-radius:8px;">
    </div>

    <div>
      <label style="display:block;font-size:12px;color:#6B7896;font-weight:800;">Bulan (Bulanan)</label>
      <input type="month" name="month" value="{{ $p['month'] }}"
             style="padding:8px 10px;border:1px solid #E2E5EE;border-radius:8px;">
    </div>

    <div>
      <label style="display:block;font-size:12px;color:#6B7896;font-weight:800;">Dari</label>
      <input type="date" name="from" value="{{ $p['from'] }}"
             style="padding:8px 10px;border:1px solid #E2E5EE;border-radius:8px;">
    </div>

    <div>
      <label style="display:block;font-size:12px;color:#6B7896;font-weight:800;">Sampai</label>
      <input type="date" name="to" value="{{ $p['to'] }}"
             style="padding:8px 10px;border:1px solid #E2E5EE;border-radius:8px;">
    </div>

    <button type="submit" style="padding:9px 14px;border:none;border-radius:8px;background:#0D1117;color:#fff;font-weight:900;">
      Tampilkan
    </button>

    @if($p['mode']==='daily')
      <a href="{{ route('retur-mati.rekap.export', ['mode'=>'daily','date'=>$p['date']]) }}"
         style="padding:9px 14px;border-radius:8px;border:1px solid #10B981;background:#ECFDF5;color:#065F46;font-weight:900;text-decoration:none;">
        Export Excel
      </a>
    @endif
  </form>

  @error('export')
    <div style="padding:10px 12px;border:1px solid #FCA5A5;background:#FEF2F2;color:#991B1B;border-radius:10px;font-weight:800;margin-bottom:12px;">
      {{ $message }}
    </div>
  @enderror

  @if($p['mode']==='daily')
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
      <div style="padding:10px 12px;border:1px solid #E2E5EE;border-radius:10px;background:#fff;font-weight:800;">
        Total Mati: <strong>{{ $dailyTotals['dead'] }}</strong>
      </div>
      <div style="padding:10px 12px;border:1px solid #E2E5EE;border-radius:10px;background:#fff;font-weight:800;">
        Total Retur: <strong>{{ $dailyTotals['retur'] }}</strong>
      </div>
    </div>

    <div style="background:#fff;border:1px solid #E2E5EE;border-radius:12px;overflow:hidden;">
      <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#FAFBFD;">
          <tr>
            <th style="text-align:left;padding:10px 12px;border-bottom:1px solid #E2E5EE;">Plat</th>
            <th style="text-align:right;padding:10px 12px;border-bottom:1px solid #E2E5EE;">Ayam Mati</th>
            <th style="text-align:right;padding:10px 12px;border-bottom:1px solid #E2E5EE;">Ayam Retur</th>
          </tr>
        </thead>
        <tbody>
          @foreach($dailyDetails as $r)
            <tr>
              <td style="padding:10px 12px;border-bottom:1px solid #F0F2F7;">
                <strong>{{ $r['plate_number'] }}</strong>
                <div style="color:#6B7896;font-size:12px;font-weight:700;">
                  {{ $r['location'] }} · {{ $r['shift'] }} · Truk #{{ $r['truck_no'] }} · {{ $r['report_code'] }}
                </div>
              </td>
              <td style="padding:10px 12px;text-align:right;border-bottom:1px solid #F0F2F7;">{{ $r['dead_count'] }}</td>
              <td style="padding:10px 12px;text-align:right;border-bottom:1px solid #F0F2F7;">{{ $r['retur_count'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  @else
    <div style="background:#fff;border:1px solid #E2E5EE;border-radius:12px;overflow:hidden;">
      <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#FAFBFD;">
          <tr>
            <th style="text-align:left;padding:10px 12px;border-bottom:1px solid #E2E5EE;">Tanggal</th>
            <th style="text-align:right;padding:10px 12px;border-bottom:1px solid #E2E5EE;">Truk</th>
            <th style="text-align:right;padding:10px 12px;border-bottom:1px solid #E2E5EE;">Mati</th>
            <th style="text-align:right;padding:10px 12px;border-bottom:1px solid #E2E5EE;">Retur</th>
          </tr>
        </thead>
        <tbody>
          @foreach($byDate as $d => $r)
            <tr>
              <td style="padding:10px 12px;border-bottom:1px solid #F0F2F7;"><strong>{{ $d }}</strong></td>
              <td style="padding:10px 12px;text-align:right;border-bottom:1px solid #F0F2F7;">{{ $r['trucks'] }}</td>
              <td style="padding:10px 12px;text-align:right;border-bottom:1px solid #F0F2F7;">{{ $r['dead'] }}</td>
              <td style="padding:10px 12px;text-align:right;border-bottom:1px solid #F0F2F7;">{{ $r['retur'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection