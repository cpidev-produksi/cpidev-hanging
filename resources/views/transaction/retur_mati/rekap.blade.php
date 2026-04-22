@extends('layouts.app')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600;700&display=swap');

:root {
  --rk-bg:       #F4F6FB;
  --rk-surface:  #FFFFFF;
  --rk-card:     #F9FAFB;
  --rk-border:   #E4E8F0;
  --rk-border2:  #CDD3DF;
  --rk-text:     #0D1117;
  --rk-muted:    #6B7896;
  --rk-accent:   #E85D2F;
  --rk-accent2:  #C94A1C;
  --rk-green:    #059669;
  --rk-green-xl: rgba(5,150,105,.08);
  --rk-acc-xl:   rgba(232,93,47,.08);
  --rk-r:        12px;
}

* { box-sizing: border-box; }

.rk-page {
  min-height: 100vh;
  background: var(--rk-bg);
  font-family: 'DM Sans', sans-serif;
  color: var(--rk-text);
  padding: 36px 28px 64px;
}

/* ─── HEADER ─── */
.rk-header {
  max-width: 1160px;
  margin: 0 auto 32px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.rk-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: .78rem;
  font-weight: 600;
  color: var(--rk-muted);
  text-decoration: none;
  margin-bottom: 10px;
  letter-spacing: .04em;
  transition: color .18s;
}
.rk-back:hover { color: var(--rk-text); }
.rk-back svg { width: 14px; height: 14px; }

.rk-headline {
  font-family: 'Syne', sans-serif;
  font-size: 1.85rem;
  font-weight: 800;
  color: var(--rk-text);
  margin: 0 0 6px;
  letter-spacing: -.02em;
  line-height: 1.1;
}

.rk-period {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: .78rem;
  font-weight: 600;
  color: var(--rk-muted);
  background: #fff;
  border: 1px solid var(--rk-border);
  border-radius: 20px;
  padding: 5px 14px;
  letter-spacing: .02em;
  box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.rk-period span { color: var(--rk-text); font-weight: 700; }
.rk-period-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--rk-accent); flex-shrink: 0; }

/* ─── FILTER CARD ─── */
.rk-filter-card {
  max-width: 1160px;
  margin: 0 auto 28px;
  background: var(--rk-surface);
  border: 1px solid var(--rk-border);
  border-radius: 16px;
  padding: 20px 24px;
  display: flex;
  align-items: flex-end;
  gap: 14px;
  flex-wrap: wrap;
  box-shadow: 0 1px 6px rgba(0,0,0,.04);
}

.rk-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.rk-label {
  font-size: .67rem;
  font-weight: 700;
  color: var(--rk-muted);
  letter-spacing: .08em;
  text-transform: uppercase;
}

.rk-select,
.rk-input {
  padding: 10px 14px;
  background: #fff;
  border: 1.5px solid var(--rk-border2);
  border-radius: var(--rk-r);
  color: var(--rk-text);
  font-family: 'DM Sans', sans-serif;
  font-size: .84rem;
  font-weight: 500;
  outline: none;
  transition: border-color .18s, box-shadow .18s;
  -webkit-appearance: none;
  appearance: none;
}
.rk-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236B7896' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 34px; background-color: #fff; }
.rk-select:focus, .rk-input:focus { border-color: var(--rk-accent); box-shadow: 0 0 0 3px var(--rk-acc-xl); }
.rk-input[type="date"]::-webkit-calendar-picker-indicator { filter: none; opacity: .5; cursor: pointer; }

.rk-filter-actions { display: flex; align-items: flex-end; gap: 10px; margin-left: auto; }

.rk-btn-primary {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px;
  background: var(--rk-accent);
  color: #fff;
  border: none; border-radius: var(--rk-r);
  font-family: 'DM Sans', sans-serif;
  font-size: .84rem; font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(232,93,47,.30);
  transition: all .18s;
  white-space: nowrap;
}
.rk-btn-primary:hover { background: #D04A1E; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(232,93,47,.40); }

.rk-btn-export {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px;
  background: var(--rk-green-xl);
  color: var(--rk-green);
  border: 1.5px solid rgba(16,185,129,.25); border-radius: var(--rk-r);
  font-family: 'DM Sans', sans-serif;
  font-size: .84rem; font-weight: 700;
  text-decoration: none;
  transition: all .18s;
  white-space: nowrap;
}
.rk-btn-export:hover { background: rgba(16,185,129,.20); border-color: var(--rk-green); transform: translateY(-1px); }

/* ─── ERROR ─── */
.rk-error {
  max-width: 1160px; margin: 0 auto 16px;
  padding: 12px 16px;
  background: #FEF2F2;
  border: 1px solid #FECACA;
  color: #991B1B;
  border-radius: var(--rk-r);
  font-size: .84rem; font-weight: 600;
}

/* ─── STAT PILLS ─── */
.rk-stats {
  max-width: 1160px;
  margin: 0 auto 20px;
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.rk-stat {
  flex: 1;
  min-width: 160px;
  background: var(--rk-surface);
  border: 1px solid var(--rk-border);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: border-color .18s, box-shadow .18s;
  box-shadow: 0 1px 6px rgba(0,0,0,.04);
}
.rk-stat:hover { border-color: var(--rk-accent); box-shadow: 0 4px 16px rgba(232,93,47,.08); }

.rk-stat-icon {
  width: 42px; height: 42px; flex-shrink: 0;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
}
.rk-stat-icon--dead  { background: rgba(232,93,47,.10); color: var(--rk-accent); }
.rk-stat-icon--retur { background: rgba(99,102,241,.10); color: #6366F1; }

.rk-stat-val  { font-family: 'Syne', sans-serif; font-size: 1.6rem; font-weight: 800; color: var(--rk-text); line-height: 1; }
.rk-stat-lbl  { font-size: .72rem; font-weight: 600; color: var(--rk-muted); letter-spacing: .04em; text-transform: uppercase; margin-top: 4px; }

/* ─── TABLE WRAPPER ─── */
.rk-table-wrap {
  max-width: 1160px;
  margin: 0 auto;
  background: var(--rk-surface);
  border: 1px solid var(--rk-border);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 1px 6px rgba(0,0,0,.04);
}

.rk-table-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px 14px;
  border-bottom: 1px solid var(--rk-border);
}
.rk-table-title {
  font-family: 'Syne', sans-serif;
  font-size: .9rem;
  font-weight: 700;
  color: var(--rk-text);
  letter-spacing: -.01em;
}
.rk-table-sub { font-size: .72rem; color: var(--rk-muted); margin-top: 2px; }

.rk-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: .7rem; font-weight: 700;
  letter-spacing: .04em;
  text-transform: uppercase;
}
.rk-badge--daily   { background: rgba(232,93,47,.08); color: #C94A1C; border: 1px solid rgba(232,93,47,.2); }
.rk-badge--monthly { background: rgba(99,102,241,.08); color: #4F46E5; border: 1px solid rgba(99,102,241,.2); }
.rk-badge--range   { background: rgba(5,150,105,.08); color: #065F46; border: 1px solid rgba(5,150,105,.2); }

table.rk-table { width: 100%; border-collapse: collapse; }

table.rk-table thead tr th {
  padding: 11px 20px;
  font-size: .68rem;
  font-weight: 700;
  color: var(--rk-muted);
  text-transform: uppercase;
  letter-spacing: .06em;
  background: #F8F9FC;
  border-bottom: 1px solid var(--rk-border);
}
table.rk-table thead tr th:first-child { text-align: left; }
table.rk-table thead tr th:not(:first-child) { text-align: right; }

table.rk-table tbody tr {
  transition: background .12s;
  animation: rk-row-in .35s ease both;
}
table.rk-table tbody tr:hover { background: #FDF8F6; }

@keyframes rk-row-in {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: translateY(0); }
}

table.rk-table tbody tr td {
  padding: 13px 20px;
  border-bottom: 1px solid var(--rk-border);
  font-size: .84rem;
  color: var(--rk-text);
}
table.rk-table tbody tr:last-child td { border-bottom: none; }
table.rk-table tbody tr td:not(:first-child) { text-align: right; }

/* Number pills */
.rk-num { font-family: 'Syne', sans-serif; font-weight: 700; }
.rk-num--dead  { color: #C94A1C; }
.rk-num--retur { color: #4F46E5; }
.rk-num--truck { color: #059669; font-size: .8rem; }

/* Daily detail row */
.rk-plate { font-weight: 700; color: var(--rk-text); font-size: .88rem; }
.rk-meta  {
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
  margin-top: 4px;
}
.rk-chip {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 2px 9px;
  border-radius: 5px;
  font-size: .65rem;
  font-weight: 700;
  letter-spacing: .03em;
  background: #F1F3F8;
  color: var(--rk-muted);
  border: 1px solid var(--rk-border);
}

/* Date row (summary mode) */
.rk-date-row td:first-child { font-weight: 700; color: var(--rk-text); }

/* Empty state */
.rk-empty {
  padding: 56px 24px;
  text-align: center;
  color: var(--rk-muted);
  font-size: .88rem;
}
.rk-empty-icon { font-size: 2.4rem; margin-bottom: 12px; opacity: .3; }

/* ─── Stagger rows ─── */
table.rk-table tbody tr:nth-child(1)  { animation-delay: .04s; }
table.rk-table tbody tr:nth-child(2)  { animation-delay: .07s; }
table.rk-table tbody tr:nth-child(3)  { animation-delay: .10s; }
table.rk-table tbody tr:nth-child(4)  { animation-delay: .13s; }
table.rk-table tbody tr:nth-child(5)  { animation-delay: .16s; }
table.rk-table tbody tr:nth-child(6)  { animation-delay: .19s; }
table.rk-table tbody tr:nth-child(7)  { animation-delay: .22s; }
table.rk-table tbody tr:nth-child(8)  { animation-delay: .25s; }
table.rk-table tbody tr:nth-child(n+9){ animation-delay: .28s; }

@media (max-width: 680px) {
  .rk-page { padding: 20px 16px 48px; }
  .rk-headline { font-size: 1.4rem; }
  .rk-filter-card { padding: 16px; }
  .rk-filter-actions { margin-left: 0; }
}
</style>

<div class="rk-page">

  {{-- ── HEADER ── --}}
  <div class="rk-header">
    <div>
      <a href="{{ route('retur-mati.landing') }}" class="rk-back">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Kembali
      </a>
      <h1 class="rk-headline">Rekap Ayam&nbsp;Mati<br>&amp; Ayam Retur</h1>
    </div>

    {{-- <div class="rk-period">
      <span class="rk-period-dot"></span>
      Periode&nbsp;
      <span>{{ $p['start'] }}</span>
      &mdash;
      <span>{{ $p['end'] }}</span>
    </div> --}}
  </div>

  {{-- ── FILTER ── --}}
  <form method="GET" id="rekapFilter" class="rk-filter-card">

    <div class="rk-field">
      <label class="rk-label">Tampilan</label>
      <select name="mode" id="modeSelect" class="rk-select" style="min-width:148px;">
        <option value="daily"   @selected($p['mode']==='daily')>📅&nbsp; Harian</option>
        <option value="monthly" @selected($p['mode']==='monthly')>🗓&nbsp; Bulanan</option>
        <option value="range"   @selected($p['mode']==='range')>📆&nbsp; Range Tanggal</option>
      </select>
    </div>

    <div class="rk-field" data-mode="daily">
      <label class="rk-label">Tanggal</label>
      <input type="date" name="date" value="{{ $p['date'] }}" class="rk-input">
    </div>

    <div class="rk-field" data-mode="monthly">
      <label class="rk-label">Bulan</label>
      <input type="month" name="month" value="{{ $p['month'] }}" class="rk-input">
    </div>

    <div class="rk-field" data-mode="range">
      <label class="rk-label">Dari</label>
      <input type="date" name="from" value="{{ $p['from'] }}" class="rk-input">
    </div>

    <div class="rk-field" data-mode="range">
      <label class="rk-label">Sampai</label>
      <input type="date" name="to" value="{{ $p['to'] }}" class="rk-input">
    </div>

    <div class="rk-field">
      <label class="rk-label">Lokasi</label>
      <select name="location" class="rk-select" style="min-width:120px;">
        <option value="ALL" @selected(($location ?? 'ALL')==='ALL')>Semua</option>
        <option value="SH01" @selected(($location ?? 'ALL')==='SH01')>SH01</option>
        <option value="SH02" @selected(($location ?? 'ALL')==='SH02')>SH02</option>
      </select>
    </div>

    <div class="rk-field">
      <label class="rk-label">Shift</label>
      <select name="shift" class="rk-select" style="min-width:120px;">
        <option value="ALL" @selected(($shift ?? 'ALL')==='ALL')>Semua</option>
        <option value="pagi" @selected(($shift ?? 'ALL')==='pagi')>Pagi</option>
        <option value="malam" @selected(($shift ?? 'ALL')==='malam')>Malam</option>
      </select>
    </div>

    <div class="rk-filter-actions">
      <button type="submit" class="rk-btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        Tampilkan
      </button>

      <a id="btnExport"
         href="{{ route('retur-mati.rekap.export', request()->query()) }}"
         class="rk-btn-export">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export Excel
      </a>
    </div>
  </form>

  {{-- ── ERROR ── --}}
  @error('export')
    <div class="rk-error">⚠ {{ $message }}</div>
  @enderror

  {{-- ── DAILY MODE ── --}}
  @if($p['mode']==='daily')

    <div class="rk-stats">
      <div class="rk-stat">
        <div class="rk-stat-icon rk-stat-icon--dead">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a8 8 0 0 0-8 8c0 5.4 7.1 11.5 7.4 11.8a1 1 0 0 0 1.2 0C13 21.5 20 15.4 20 10a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div>
          <div class="rk-stat-val">{{ $dailyTotals['dead'] }}</div>
          <div class="rk-stat-lbl">Ayam Mati</div>
        </div>
      </div>
      <div class="rk-stat">
        <div class="rk-stat-icon rk-stat-icon--retur">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.62"/></svg>
        </div>
        <div>
          <div class="rk-stat-val">{{ $dailyTotals['retur'] }}</div>
          <div class="rk-stat-lbl">Ayam Retur</div>
        </div>
      </div>
      <div class="rk-stat">
        <div class="rk-stat-icon" style="background: rgba(5,150,105,.10); color:#059669;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/>
            <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
          </svg>
        </div>
        <div>
          <div class="rk-stat-val">{{ $dailyTotals['trucks'] }}</div>
          <div class="rk-stat-lbl">Total Truk</div>
        </div>
      </div>
    </div>

    <div class="rk-table-wrap">
      <div class="rk-table-head">
        <div>
          <div class="rk-table-title">Detail Harian</div>
          <div class="rk-table-sub">Rincian per truk</div>
        </div>
        <span class="rk-badge rk-badge--daily">Harian</span>
      </div>

      <table class="rk-table">
        <thead>
          <tr>
            <th>Plat / Info Truk</th>
            <th>Ayam Mati</th>
            <th>Ayam Retur</th>
          </tr>
        </thead>
        <tbody>
          @forelse($dailyDetails as $r)
            <tr>
              <td>
                <div class="rk-plate">{{ $r['plate_number'] }}</div>
                <div class="rk-meta">
                  <span class="rk-chip">📍 {{ $r['location'] }}</span>
                  <span class="rk-chip">🔁 {{ $r['shift'] }}</span>
                </div>
              </td>
              <td><span class="rk-num rk-num--dead">{{ $r['dead_count'] }}</span></td>
              <td><span class="rk-num rk-num--retur">{{ $r['retur_count'] }}</span></td>
            </tr>
          @empty
            <tr><td colspan="3">
              <div class="rk-empty">
                <div class="rk-empty-icon">🐔</div>
                Tidak ada data untuk tanggal ini.
              </div>
            </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

  {{-- ── MONTHLY / RANGE MODE ── --}}
  @else

    <div class="rk-table-wrap">
      <div class="rk-table-head">
        <div>
          <div class="rk-table-title">Rekap {{ $p['mode'] === 'monthly' ? 'Bulanan' : 'Range Tanggal' }}</div>
          <div class="rk-table-sub">Dikelompokkan per tanggal</div>
        </div>
        <span class="rk-badge rk-badge--{{ $p['mode'] }}">
          {{ $p['mode'] === 'monthly' ? 'Bulanan' : 'Range' }}
        </span>
      </div>

      <table class="rk-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Truk</th>
            <th>Mati</th>
            <th>Retur</th>
          </tr>
        </thead>
        <tbody>
          @forelse($byDate as $d => $r)
            <tr class="rk-date-row">
              <td>{{ $d }}</td>
              <td><span class="rk-num rk-num--truck">{{ $r['trucks'] }}</span></td>
              <td><span class="rk-num rk-num--dead">{{ $r['dead'] }}</span></td>
              <td><span class="rk-num rk-num--retur">{{ $r['retur'] }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4">
              <div class="rk-empty">
                <div class="rk-empty-icon">📭</div>
                Tidak ada data pada periode ini.
              </div>
            </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

  @endif

</div>

<script>
(function(){
  const modeSelect = document.getElementById('modeSelect');
  const groups     = document.querySelectorAll('[data-mode]');
  const exportBtn  = document.getElementById('btnExport');

  function applyMode() {
    const mode = modeSelect.value;

    groups.forEach(g => {
      const show = g.getAttribute('data-mode') === mode;
      g.style.display = show ? '' : 'none';
      g.querySelectorAll('input,select').forEach(inp => inp.disabled = !show);
    });

    const form = document.getElementById('rekapFilter');
    const fd   = new FormData(form);
    fd.set('mode', mode);

    const params = new URLSearchParams();
    for (const [k, v] of fd.entries()) {
      if (v !== '' && v != null) params.append(k, v);
    }
    exportBtn.href = "{{ route('retur-mati.rekap.export') }}" + "?" + params.toString();
  }

  modeSelect.addEventListener('change', applyMode);
  document.addEventListener('DOMContentLoaded', applyMode);
  applyMode();
})();
</script>

@endsection