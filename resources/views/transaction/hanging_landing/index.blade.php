@extends('layouts.app')

@section('content')
<div class="lp-wrap">

  {{-- ── PAGE HEADER ── --}}
  <div class="lp-header">
    <div class="lp-header-left">
      <div class="lp-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/>
          <path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>
        </svg>
      </div>
      <div>
        <h1 class="lp-title">Form Hanging Ayam</h1>
        <p class="lp-sub">Pilih berdasarkan urutan truk</p>
      </div>
    </div>

    <form method="GET" class="lp-filter">
      <div class="lp-input-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" class="lp-input-icon">
          <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
        <input type="date" name="date" value="{{ $date ?? '' }}" class="lp-date-input">
      </div>
      <button class="lp-btn-filter" type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"><line x1="4" y1="6" x2="20" y2="6"/>
          <line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/>
        </svg>
        Filter
      </button>
    </form>
  </div>

{{-- ── STATUS BADGES ── --}}
@php
  $runningSH01 = in_array('SH01', $runningLocations ?? [], true);
  $runningSH02 = in_array('SH02', $runningLocations ?? [], true);
  $collection  = $items->getCollection();
  $byLocation  = $collection->groupBy('location');

  $mkSplit = function($locItems) {
    $byShift = $locItems->groupBy('shift');
    return [
      'pagi'  => $byShift->get('pagi',  collect())->sortBy('truck_no')->values(),
      'malam' => $byShift->get('malam', collect())->sortBy('truck_no')->values(),
    ];
  };

  $sh01 = $mkSplit($byLocation->get('SH01', collect()));
  $sh02 = $mkSplit($byLocation->get('SH02', collect()));

  $sh01Total = $sh01['pagi']->count() + $sh01['malam']->count();
  $sh02Total = $sh02['pagi']->count() + $sh02['malam']->count();
@endphp

  {{-- ── TABS ── --}}
  <div class="lp-tab-bar">
    <button type="button" class="lp-tab active" data-tab="tab-sh01">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2"/>
        <path d="M16 7V5a2 2 0 0 0-4 0v2"/></svg>
      SH01
      <span class="lp-tab-count">{{ $sh01Total }}</span>
    </button>
    <button type="button" class="lp-tab" data-tab="tab-sh02">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2"/>
        <path d="M16 7V5a2 2 0 0 0-4 0v2"/></svg>
      SH02
      <span class="lp-tab-count">{{ $sh02Total }}</span>
    </button>
    <div class="lp-tab-filler"></div>
  </div>

  {{-- ── TAB PANES ── --}}
  @php
    $currentHour = now('Asia/Jakarta')->hour;
    $isPagiFirst = $currentHour >= 6 && $currentHour < 18; // pagi: 06:00–17:59 WIB
  @endphp

  @foreach(['sh01' => $sh01, 'sh02' => $sh02] as $tabKey => $shiftData)
    @php $loc = strtoupper($tabKey); @endphp
    <div id="tab-{{ $tabKey }}" class="lp-pane {{ $loop->first ? 'active' : '' }}">
      <div class="lp-shift-stack">

        @php
          $firstShift  = $isPagiFirst ? 'pagi'  : 'malam';
          $secondShift = $isPagiFirst ? 'malam' : 'pagi';
        @endphp

        @foreach([$firstShift, $secondShift] as $shift)
          @php $isFirst = $loop->first; @endphp
          <div class="lp-shift-block">
            <div class="lp-shift-head {{ $shift === 'pagi' ? 'lp-sh-pagi' : 'lp-sh-malam' }}">
              <div class="lp-shift-label">
                @if($shift === 'pagi')
                  <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                  </svg>
                  Shift Pagi
                @else
                  <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                  </svg>
                  Shift Malam
                @endif
              </div>
              {{-- @if($isFirst)
                <span class="lp-shift-now-badge {{ $shift === 'malam' ? 'lp-shift-now-malam' : '' }}">
                  <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="currentColor"
                       stroke="none"><circle cx="12" cy="12" r="12"/></svg>
                  Sekarang
                </span>
              @endif --}}
              <span class="lp-shift-count">{{ $shiftData[$shift]->count() }} truk</span>
            </div>
            @include('transaction.hanging_landing.partials.list', [
              'location' => $loc,
              'list'     => $shiftData[$shift],
            ])
          </div>
        @endforeach

      </div>
    </div>
  @endforeach

  @if($items->hasPages())
    <div class="lp-pagination">{{ $items->links() }}</div>
  @endif
</div>

<script>
(function () {
  const tabs  = document.querySelectorAll('.lp-tab');
  const panes = document.querySelectorAll('.lp-pane');
  function activate(id) {
    tabs.forEach(t  => t.classList.toggle('active', t.dataset.tab === id));
    panes.forEach(p => p.classList.toggle('active', p.id === id));
    try { localStorage.setItem('hangingTab', id); } catch(e) {}
  }
  tabs.forEach(t => t.addEventListener('click', () => activate(t.dataset.tab)));
  try {
    const saved = localStorage.getItem('hangingTab');
    if (saved && document.getElementById(saved)) activate(saved);
  } catch(e) {}
})();
</script>

<style>
/* ── TOKENS ── */
:root {
  --lp-bg:        #F0F2F7;
  --lp-surface:   #FFFFFF;
  --lp-border:    #E2E5EE;
  --lp-text:      #0D1117;
  --lp-muted:     #6B7896;
  --lp-accent:    #E85D2F;
  --lp-accent-hv: #D04A1E;
  --lp-accent-xl: rgba(232,93,47,.08);
  --lp-run:       #F59F00;
  --lp-run-xl:    rgba(245,159,0,.10);
  --lp-ready:     #10B981;
  --lp-ready-xl:  rgba(16,185,129,.10);
  --lp-r:         12px;
  --lp-sh:        0 1px 3px rgba(0,0,0,.05), 0 6px 18px rgba(0,0,0,.04);
}

/* ── LAYOUT ── */
.lp-wrap { max-width: 1240px; margin: 0 auto; padding: 32px 24px; }

/* ── HEADER ── */
.lp-header {
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
}
.lp-header-left { display: flex; align-items: center; gap: 16px; }
.lp-icon {
  width: 50px; height: 50px;
  background: var(--lp-accent-xl);
  color: var(--lp-accent);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.lp-title { font-size: 1.45rem; font-weight: 800; color: var(--lp-text); margin: 0 0 3px; letter-spacing: -.01em; }
.lp-sub   { font-size: .8rem; color: var(--lp-muted); margin: 0; }

/* ── FILTER ── */
.lp-filter { display: flex; align-items: center; gap: 10px; }
.lp-input-wrap {
  display: flex; align-items: center;
  border: 1.5px solid var(--lp-border);
  border-radius: 10px;
  background: var(--lp-surface);
  overflow: hidden;
  transition: border-color .18s, box-shadow .18s;
}
.lp-input-wrap:focus-within {
  border-color: var(--lp-accent);
  box-shadow: 0 0 0 3px rgba(232,93,47,.12);
}
.lp-input-icon { width: 40px; display: flex; align-items: center; justify-content: center; color: var(--lp-muted); flex-shrink: 0; }
.lp-date-input {
  border: none; outline: none; background: transparent;
  padding: 10px 12px 10px 0;
  font-size: .875rem; color: var(--lp-text);
}
.lp-btn-filter {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 10px 18px;
  background: var(--lp-accent); color: #fff;
  border: none; border-radius: 10px;
  font-size: .84rem; font-weight: 700;
  cursor: pointer;
  transition: background .18s, transform .15s, box-shadow .18s;
  box-shadow: 0 2px 8px rgba(232,93,47,.28);
}
.lp-btn-filter:hover { background: var(--lp-accent-hv); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(232,93,47,.35); }
.lp-btn-filter:active { transform: translateY(0); }

/* ── STATUS CARDS ── */
.lp-status-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.lp-status-card {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 18px;
  border-radius: 10px;
  border: 1.5px solid transparent;
  font-weight: 700;
  font-size: .82rem;
}
.lp-status-card.is-running {
  background: var(--lp-run-xl);
  border-color: rgba(245,159,0,.25);
  color: #92400E;
}
.lp-status-card.is-ready {
  background: var(--lp-ready-xl);
  border-color: rgba(16,185,129,.2);
  color: #065F46;
}
.lp-status-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
}
.is-running .lp-status-dot { background: var(--lp-run); box-shadow: 0 0 0 3px rgba(245,159,0,.3); animation: pulse-run 1.5s ease infinite; }
.is-ready   .lp-status-dot { background: var(--lp-ready); }
@keyframes pulse-run {
  0%,100% { box-shadow: 0 0 0 0 rgba(245,159,0,.4); }
  50%      { box-shadow: 0 0 0 5px rgba(245,159,0,0); }
}
.lp-status-loc   { font-weight: 900; letter-spacing: .04em; }
.lp-status-label { opacity: .75; font-size: .74rem; letter-spacing: .08em; }

/* ── TABS ── */
.lp-tab-bar {
  display: flex; align-items: flex-end; gap: 0;
  margin-bottom: 0;
  border-bottom: 1.5px solid var(--lp-border);
}
.lp-tab {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 11px 22px;
  border: 1.5px solid transparent; border-bottom: none;
  border-radius: 10px 10px 0 0;
  background: transparent;
  color: var(--lp-muted);
  font-size: .85rem; font-weight: 700; cursor: pointer;
  position: relative; bottom: -1.5px;
  transition: color .15s, background .15s, border-color .15s;
  white-space: nowrap;
}
.lp-tab:hover { color: var(--lp-text); background: #F5F7FA; border-color: var(--lp-border); }
.lp-tab.active {
  background: var(--lp-surface); color: var(--lp-accent);
  border-color: var(--lp-border); border-bottom-color: var(--lp-surface);
}
.lp-tab-filler { flex: 1; border-bottom: 1.5px solid var(--lp-border); position: relative; bottom: -1.5px; }
.lp-tab-count {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 36px; height: 20px; padding: 0 6px;
  border-radius: 6px; font-size: .7rem; font-weight: 900;
}
.lp-tab.active .lp-tab-count { background: rgba(232,93,47,.15); color: var(--lp-accent); }
.lp-tab:not(.active) .lp-tab-count { background: #F0F2F7; color: var(--lp-muted); }

/* ── PANES ── */
.lp-pane { display: none; padding-top: 16px; }
.lp-pane.active { display: block; }

/* ── SHIFT STACK ── */
.lp-shift-stack { display: flex; flex-direction: column; gap: 16px; }

/* ── SHIFT BLOCK ── */
.lp-shift-block {
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid #E2E5EE;
  box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 6px 18px rgba(0,0,0,.04);
}
.lp-shift-head {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 16px;
  border-bottom: 1px solid rgba(0,0,0,.06);
}
.lp-sh-pagi  { background: rgba(245,159,0,.08); }
.lp-sh-malam { background: rgba(79,103,255,.07); }

.lp-shift-label {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 4px 12px; border-radius: 8px;
  font-size: .78rem; font-weight: 800; letter-spacing: .04em;
}
.lp-sh-pagi  .lp-shift-label { background: rgba(245,159,0,.15); color: #92400E; }
.lp-sh-malam .lp-shift-label { background: rgba(79,103,255,.12); color: #3730A3; }

.lp-shift-now-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 10px; border-radius: 999px;
  background: rgba(245,159,0,.18); color: #92400E;
  font-size: .7rem; font-weight: 900; letter-spacing: .04em;
  border: 1px solid rgba(245,159,0,.3);
}
.lp-shift-now-badge svg { animation: pulse-now 1.5s ease infinite; }
@keyframes pulse-now {
  0%,100% { opacity: 1; } 50% { opacity: .3; }
}
.lp-shift-now-malam {
  background: rgba(79,103,255,.12); color: #3730A3;
  border-color: rgba(79,103,255,.25);
}

.lp-shift-count {
  margin-left: auto;
  font-size: .75rem; font-weight: 700; color: #9CA3AF;
}

/* ── PAGINATION ── */
.lp-pagination {
  margin-top: 16px;
  padding: 14px 18px;
  background: var(--lp-surface);
  border: 1px solid var(--lp-border);
  border-radius: var(--lp-r);
}
</style>
@endsection