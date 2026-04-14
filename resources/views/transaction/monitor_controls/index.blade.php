@extends('layouts.app')

@section('content')
<div class="mc-wrap">

  {{-- ── HEADER ── --}}
  <div class="mc-header">
    <div class="mc-header-left">
      <div class="mc-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="14" rx="2"/>
          <path d="M7 20h10M9 16v4M15 16v4"/>
        </svg>
      </div>
      <div>
        <h1 class="mc-title">Kontrol Monitor</h1>
        <p class="mc-sub">Kelola urutan truk dan input DTA</p>
      </div>
    </div>

    <div class="mc-header-right">
      <form method="GET" class="mc-filter-row">
        <div class="mc-input-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.2" class="mc-input-icon">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          <input type="date" name="date" value="{{ $date ?? '' }}" class="mc-input">
        </div>
        <button class="mc-btn-filter" type="submit">Filter</button>
        {{-- <a href="{{ route('monitor-controls.index') }}" class="mc-btn-reset">Reset</a> --}}
      </form>

      <a href="{{ route('monitor-controls.create') }}" class="mc-btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.8"><line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Buat Kontrol
      </a>
    </div>
  </div>

  @php
    $filterActive = fn($list) => $list->filter(fn($x) => $x->status !== 'done')->sortBy('truck_no')->values();
    $filterDone   = fn($list) => $list->filter(fn($x) => $x->status === 'done')->sortBy('truck_no')->values();
  @endphp

  {{-- ── LOCATION TABS ── --}}
  <div class="mc-tab-bar">
    @foreach($locations as $loc)
      @php
        $cntActive = ($stats[$loc]['pagi']['active'] ?? 0) + ($stats[$loc]['malam']['active'] ?? 0);
        $isFirst = $loop->first;
      @endphp
      <button type="button"
              class="mc-tab {{ $isFirst ? 'mc-tab-active' : '' }}"
              data-tab="tab-{{ strtolower($loc) }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2"/>
          <path d="M16 7V5a2 2 0 0 0-4 0v2"/>
        </svg>
        {{ $loc }}
        @if($cntActive > 0)
          <span class="mc-tab-badge">{{ $cntActive }}</span>
        @endif
      </button>
    @endforeach
    <div class="mc-tab-filler"></div>
  </div>

  {{-- ── LOCATION PANES ── --}}
  @foreach($locations as $loc)
    @php
      $pagi  = $data[$loc]['pagi'];
      $malam = $data[$loc]['malam'];

      $activePagi  = $filterActive($pagi->getCollection());
      $activeMalam = $filterActive($malam->getCollection());
      $donePagi    = $filterDone($pagi->getCollection());
      $doneMalam   = $filterDone($malam->getCollection());
    @endphp

    <div id="tab-{{ strtolower($loc) }}" class="mc-pane {{ $loop->first ? 'active' : '' }}">
      <div class="mc-two-col">

        {{-- ── SHIFT PAGI ── --}}
        <div class="mc-shift-card">
          <div class="mc-shift-head">
            <div class="mc-shift-label mc-pagi">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
              </svg>
              Shift Pagi
            </div>
            <div class="mc-shift-stats">
              <span class="mc-stat-active">{{ $stats[$loc]['pagi']['active'] ?? 0 }} aktif</span>
              @if(($stats[$loc]['pagi']['done'] ?? 0) > 0)
                <span class="mc-stat-sep">·</span>
                <span class="mc-stat-done">{{ $stats[$loc]['pagi']['done'] }} selesai</span>
              @endif
            </div>
          </div>
          <div class="mc-shift-body">
            @include('transaction.monitor_controls.partials.list', ['rows' => $activePagi, 'showMove' => true])
            @include('transaction.monitor_controls.partials.done', ['rows' => $donePagi, 'key' => $loc.'-pagi'])
            @include('transaction.monitor_controls.partials.pagination', ['list' => $pagi])
          </div>
        </div>

        {{-- ── SHIFT MALAM ── --}}
        <div class="mc-shift-card">
          <div class="mc-shift-head">
            <div class="mc-shift-label mc-malam">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
              </svg>
              Shift Malam
            </div>
            <div class="mc-shift-stats">
              <span class="mc-stat-active">{{ $stats[$loc]['malam']['active'] ?? 0 }} aktif</span>
              @if(($stats[$loc]['malam']['done'] ?? 0) > 0)
                <span class="mc-stat-sep">·</span>
                <span class="mc-stat-done">{{ $stats[$loc]['malam']['done'] }} selesai</span>
              @endif
            </div>
          </div>
          <div class="mc-shift-body">
            @include('transaction.monitor_controls.partials.list', ['rows' => $activeMalam, 'showMove' => true])
            @include('transaction.monitor_controls.partials.done', ['rows' => $doneMalam, 'key' => $loc.'-malam'])
            @include('transaction.monitor_controls.partials.pagination', ['list' => $malam])
          </div>
        </div>

      </div>
    </div>
  @endforeach

  {{-- ── SUMMARY ── --}}
  <div class="mc-summary-row" style="justify-content:flex-end; margin-top:30px">
    <div class="mc-pill mc-pill-active">
      <span class="mc-pill-dot" style="background:#E85D2F"></span>
      Aktif &amp; Proses
      <strong>{{ $summaryActive }}</strong>
    </div>
    <div class="mc-pill mc-pill-done">
      <span class="mc-pill-dot" style="background:#10B981"></span>
      Selesai
      <strong>{{ $summaryDone }}</strong>
    </div>
    <div class="mc-pill mc-pill-total">
      Total hari ini
      <strong>{{ $summaryActive + $summaryDone }}</strong>
    </div>
  </div>
</div>

<script>
(function(){
  const tabs  = document.querySelectorAll('.mc-tab');
  const panes = document.querySelectorAll('.mc-pane');
  function activate(id) {
    tabs.forEach(t  => t.classList.toggle('mc-tab-active', t.dataset.tab === id));
    panes.forEach(p => p.classList.toggle('active', p.id === id));
    try { localStorage.setItem('mcTab', id); } catch(e) {}
  }
  tabs.forEach(t => t.addEventListener('click', () => activate(t.dataset.tab)));
  try {
    const saved = localStorage.getItem('mcTab');
    if (saved && document.getElementById(saved)) activate(saved);
  } catch(e) {}
})();
</script>

<style>
:root {
  --mc-text:    #0D1117;
  --mc-muted:   #6B7896;
  --mc-border:  #E2E5EE;
  --mc-surface: #FFFFFF;
  --mc-accent:  #E85D2F;
  --mc-acc-hv:  #D04A1E;
  --mc-acc-xl:  rgba(232,93,47,.08);
  --mc-r:       14px;
  --mc-sh:      0 1px 4px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);
}

.mc-wrap { max-width: 1240px; margin: 0 auto; padding: 32px 24px; }

/* HEADER */
.mc-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
.mc-header-left { display:flex; align-items:center; gap:16px; }
.mc-header-right { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.mc-icon { width:52px; height:52px; flex-shrink:0; background:var(--mc-acc-xl); color:var(--mc-accent); border-radius:14px; display:flex; align-items:center; justify-content:center; }
.mc-title { font-size:1.45rem; font-weight:800; color:var(--mc-text); margin:0 0 3px; letter-spacing:-.01em; }
.mc-sub   { font-size:.8rem; color:var(--mc-muted); margin:0; }
.mc-btn-primary {
  display:inline-flex; align-items:center; gap:8px;
  padding:10px 20px; background:var(--mc-accent); color:#fff;
  border:none; border-radius:10px; font-size:.85rem; font-weight:700; text-decoration:none;
  box-shadow:0 2px 10px rgba(232,93,47,.28); transition:all .18s;
}
.mc-btn-primary:hover { background:var(--mc-acc-hv); transform:translateY(-1px); }

/* FILTER */
.mc-filter-row { display:flex; align-items:center; gap:10px; }
.mc-input-wrap {
  display:flex; align-items:center; gap:8px;
  padding:0 12px;
  border:1.5px solid var(--mc-border);
  border-radius:10px;
  background:#fff;
}
.mc-input-icon { color:var(--mc-muted); flex-shrink:0; }
.mc-input { border:none; outline:none; padding:10px 0; font-size:.85rem; color:var(--mc-text); background:transparent; }
.mc-btn-filter {
  display:inline-flex; align-items:center; gap:8px;
  padding:10px 18px; background:var(--mc-accent); color:#fff;
  border:none; border-radius:10px; font-size:.84rem; font-weight:700; cursor:pointer;
  box-shadow:0 2px 8px rgba(232,93,47,.28); transition:all .18s;
}
.mc-btn-filter:hover { background:var(--mc-acc-hv); transform:translateY(-1px); }
.mc-btn-reset {
  display:inline-flex; align-items:center; gap:7px;
  padding:10px 18px; background:#F3F4F6; color:#374151;
  border:1.5px solid #E5E7EB; border-radius:10px;
  font-size:.84rem; font-weight:700; cursor:pointer; text-decoration:none;
  transition:all .18s;
}
.mc-btn-reset:hover { background:#E5E7EB; }

/* SUMMARY */
.mc-summary-row { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; }
.mc-pill { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:10px; font-size:.82rem; font-weight:600; border:1.5px solid transparent; }
.mc-pill strong { font-size:.95rem; font-weight:900; }
.mc-pill-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.mc-pill-active { background:rgba(232,93,47,.08); border-color:rgba(232,93,47,.2); color:#92400E; }
.mc-pill-done   { background:rgba(16,185,129,.08); border-color:rgba(16,185,129,.2); color:#065F46; }
.mc-pill-total  { background:#F0F2F7; border-color:#E2E5EE; color:var(--mc-muted); }

/* TABS — underline style */
.mc-tab-bar {
  display: flex;
  align-items: flex-end;
  gap: 0;
  margin-bottom: 0;
  border-bottom: 1.5px solid var(--mc-border);
}
.mc-tab {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 11px 22px;
  border: 1.5px solid transparent;
  border-bottom: none;
  border-radius: 10px 10px 0 0;
  background: transparent;
  color: var(--mc-muted);
  font-size: .85rem; font-weight: 700; cursor: pointer;
  position: relative; bottom: -1.5px;
  transition: color .15s, background .15s, border-color .15s;
  white-space: nowrap;
}
.mc-tab:hover {
  color: var(--mc-text);
  background: #F5F7FA;
  border-color: var(--mc-border);
}
.mc-tab-active {
  background: var(--mc-surface);
  color: var(--mc-accent);
  border-color: var(--mc-border);
  border-bottom-color: var(--mc-surface);
}
.mc-tab-filler {
  flex: 1;
  border-bottom: 1.5px solid var(--mc-border);
  position: relative; bottom: -1.5px;
}
.mc-tab-badge {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 20px; height: 20px; padding: 0 4px;
  border-radius: 6px;
  font-size: .7rem; font-weight: 900;
}
.mc-tab-active .mc-tab-badge { background: rgba(232,93,47,.15); color: var(--mc-accent); }
.mc-tab:not(.mc-tab-active) .mc-tab-badge { background: #F0F2F7; color: var(--mc-muted); }

/* PANES */
.mc-pane { display:none; padding-top: 16px; }
.mc-pane.active { display:block; }

/* TWO COLUMN */
.mc-two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media (max-width:900px) { .mc-two-col { grid-template-columns:1fr; } }

/* SHIFT CARD */
.mc-shift-card { background:var(--mc-surface); border:1px solid var(--mc-border); border-radius:var(--mc-r); box-shadow:var(--mc-sh); overflow:hidden; }
.mc-shift-head { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--mc-border); background:#FAFBFD; }
.mc-shift-label { display:inline-flex; align-items:center; gap:7px; padding:5px 12px; border-radius:8px; font-size:.78rem; font-weight:800; letter-spacing:.04em; }
.mc-pagi  { background:rgba(245,159,0,.12); color:#92400E; }
.mc-malam { background:rgba(79,103,255,.10); color:#3730A3; }
.mc-shift-stats { display:flex; align-items:center; gap:5px; font-size:.78rem; font-weight:700; }
.mc-stat-active { color:var(--mc-accent); }
.mc-stat-sep    { color:#C5CAD8; }
.mc-stat-done   { color:#10B981; }
.mc-shift-body  { padding:12px 14px; }

/* PAGINATION (theme) */
.mc-pagination {
  margin-top:16px;
  padding:14px 18px;
  background:var(--mc-surface);
  border:1px solid var(--mc-border);
  border-radius:var(--mc-r);
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
}
.mc-pagination-info { font-size:12px; color:#9CA3AF; }
.mc-pagination-highlight { font-weight:600; color:#374151; }
.mc-pagination-nav { display:flex; align-items:center; gap:4px; }
.mc-page-btn {
  display:inline-flex; align-items:center; justify-content:center;
  min-width:32px; height:32px; padding:0 6px;
  font-size:12px; font-weight:500; color:#4B5563;
  background:#fff; border:1.5px solid #E5E7EB; border-radius:7px;
  text-decoration:none; transition:background .13s, color .13s, border-color .13s, box-shadow .13s;
  cursor:pointer; user-select:none;
}
.mc-page-btn:hover { background:#EEF2FF; color:#4338CA; border-color:#C7D2FE; }
.mc-page-btn--active {
  background:var(--mc-accent); color:#fff; border-color:var(--mc-accent);
  box-shadow:0 1px 6px rgba(232,93,47,.35); font-weight:600; cursor:default;
}
.mc-page-btn--active:hover { background:var(--mc-accent); color:#fff; border-color:var(--mc-accent); }
.mc-page-btn--disabled {
  background:#F9FAFB; color:#D1D5DB; border-color:#F3F4F6;
  cursor:not-allowed; pointer-events:none;
}
.mc-page-ellipsis {
  display:inline-flex; align-items:center; justify-content:center;
  width:28px; height:32px; font-size:13px; color:#9CA3AF; letter-spacing:.1em;
}
@media (max-width:680px) {
  .mc-pagination { flex-direction:column; align-items:center; text-align:center; }
}
</style>
@endsection