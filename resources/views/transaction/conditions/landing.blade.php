@extends('layouts.app')

@section('content')
<div class="kd-wrap">

  {{-- ── HEADER ── --}}
  <div class="kd-header">
    <div class="kd-header-left">
      <div class="kd-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 11l3 3L22 4"/>
          <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
      </div>
      <div>
        <h1 class="kd-title">Kondisi Keranjang & Bulu Ayam</h1>
        <p class="kd-sub">Isi kondisi keranjang, platform truk, dan bulu ayam (per truk)</p>
      </div>
    </div>

    <form method="GET" class="kd-filter-row">
      <div class="kd-input-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2" class="kd-input-icon">
          <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <input type="date" name="date" value="{{ $date ?? '' }}" class="kd-input">
      </div>
      <button class="kd-btn-primary" type="submit">Filter</button>
    </form>
  </div>

  @php
    $col      = $items->getCollection();
    $grouped  = $col->groupBy(fn($x) => $x->location)
                    ->map(fn($lg) => $lg->groupBy(fn($x) => $x->shift));
    $locations = ['SH01', 'SH02'];
    $countFilled = fn($list) => $list->filter(fn($it) =>
      $it->hangingForm && $it->hangingForm->basket_condition &&
      $it->hangingForm->truck_platform_condition && $it->hangingForm->feather_condition
    )->count();
  @endphp

  {{-- ── TABS ── --}}
  <div class="kd-tab-bar">
    @foreach($locations as $loc)
      @php
        $lg     = $grouped->get($loc, collect());
        $total  = $lg->flatten()->count();
        $filled = $countFilled($lg->flatten());
        $isFirst = $loop->first;
      @endphp
      <button type="button"
              class="kd-tab {{ $isFirst ? 'kd-tab-active' : '' }}"
              data-tab="tab-{{ strtolower($loc) }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2"/>
          <path d="M16 7V5a2 2 0 0 0-4 0v2"/>
        </svg>
        {{ $loc }}
        @if($total > 0)
          <span class="kd-tab-badge">{{ $filled }}/{{ $total }}</span>
        @endif
      </button>
    @endforeach
    <div class="kd-tab-filler"></div>
  </div>

  {{-- ── PANES ── --}}
  @foreach($locations as $loc)
    @php
      $lg    = $grouped->get($loc, collect());
      $pagi  = $lg->get('pagi',  collect())->sortBy('truck_no')->values();
      $malam = $lg->get('malam', collect())->sortBy('truck_no')->values();
    @endphp

    <div id="tab-{{ strtolower($loc) }}" class="kd-pane {{ $loop->first ? 'active' : '' }}">
      <div class="kd-two-col">

        {{-- SHIFT PAGI --}}
        <div class="kd-shift-card">
          <div class="kd-shift-head">
            <div class="kd-shift-label kd-pagi">
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
            <div class="kd-shift-stats">
              <span class="kd-stat-filled">{{ $countFilled($pagi) }} terisi</span>
              <span class="kd-stat-sep">·</span>
              <span class="kd-stat-total">{{ $pagi->count() }} truk</span>
            </div>
          </div>
          <div class="kd-shift-body">
            @include('transaction.conditions.partials.list', ['location' => $loc, 'list' => $pagi])
          </div>
        </div>

        {{-- SHIFT MALAM --}}
        <div class="kd-shift-card">
          <div class="kd-shift-head">
            <div class="kd-shift-label kd-malam">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
              </svg>
              Shift Malam
            </div>
            <div class="kd-shift-stats">
              <span class="kd-stat-filled">{{ $countFilled($malam) }} terisi</span>
              <span class="kd-stat-sep">·</span>
              <span class="kd-stat-total">{{ $malam->count() }} truk</span>
            </div>
          </div>
          <div class="kd-shift-body">
            @include('transaction.conditions.partials.list', ['location' => $loc, 'list' => $malam])
          </div>
        </div>

      </div>
    </div>
  @endforeach

  @if($items->hasPages())
    <div class="kd-pagination">{{ $items->withQueryString()->links() }}</div>
  @endif
</div>

<script>
(function () {
  const buttons = document.querySelectorAll('.kd-tab');
  const panes   = document.querySelectorAll('.kd-pane');
  function activate(tabId) {
    buttons.forEach(b => b.classList.toggle('kd-tab-active', b.dataset.tab === tabId));
    panes.forEach(p => p.classList.toggle('active', p.id === tabId));
    try { localStorage.setItem('conditionsTab', tabId); } catch (e) {}
  }
  buttons.forEach(btn => btn.addEventListener('click', () => activate(btn.dataset.tab)));
  try {
    const last = localStorage.getItem('conditionsTab');
    if (last && document.getElementById(last)) activate(last);
  } catch (e) {}
})();
</script>

<style>
:root {
  --kd-text:    #0D1117;
  --kd-muted:   #6B7896;
  --kd-border:  #E2E5EE;
  --kd-surface: #FFFFFF;
  --kd-accent:  #4F67FF;
  --kd-acc-hv:  #3A50E0;
  --kd-acc-xl:  rgba(79,103,255,.08);
  --kd-r:       14px;
  --kd-sh:      0 1px 4px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);
}

.kd-wrap { max-width: 1240px; margin: 0 auto; padding: 32px 24px; }

/* ── HEADER ── */
.kd-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
.kd-header-left { display:flex; align-items:center; gap:16px; }
.kd-icon {
  width:52px; height:52px; flex-shrink:0;
  background:var(--kd-acc-xl); color:var(--kd-accent);
  border-radius:14px; display:flex; align-items:center; justify-content:center;
}
.kd-title { font-size:1.45rem; font-weight:800; color:var(--kd-text); margin:0 0 3px; letter-spacing:-.01em; }
.kd-sub   { font-size:.8rem; color:var(--kd-muted); margin:0; }

/* ── FILTER ── */
.kd-filter-row { display:flex; align-items:center; gap:10px; }
.kd-input-wrap {
  display:flex; align-items:center; gap:8px;
  padding:0 12px;
  border:1.5px solid var(--kd-border);
  border-radius:10px;
  background:#fff;
}
.kd-input-icon { color:var(--kd-muted); flex-shrink:0; }
.kd-input { border:none; outline:none; padding:10px 0; font-size:.85rem; color:var(--kd-text); background:transparent; }
.kd-btn-primary {
  display:inline-flex; align-items:center; gap:8px;
  padding:10px 20px; background:var(--kd-accent); color:#fff;
  border:none; border-radius:10px; font-size:.85rem; font-weight:700; cursor:pointer;
  box-shadow:0 2px 10px rgba(79,103,255,.28); transition:all .18s;
}
.kd-btn-primary:hover { background:var(--kd-acc-hv); transform:translateY(-1px); }

/* ── TABS ── */
.kd-tab-bar {
  display:flex; align-items:flex-end; gap:0;
  margin-bottom:0;
  border-bottom:1.5px solid var(--kd-border);
}
.kd-tab {
  display:inline-flex; align-items:center; gap:8px;
  padding:11px 22px;
  border:1.5px solid transparent; border-bottom:none;
  border-radius:10px 10px 0 0;
  background:transparent;
  color:var(--kd-muted);
  font-size:.85rem; font-weight:700; cursor:pointer;
  position:relative; bottom:-1.5px;
  transition:color .15s, background .15s, border-color .15s;
  white-space:nowrap;
}
.kd-tab:hover { color:var(--kd-text); background:#F5F7FA; border-color:var(--kd-border); }
.kd-tab-active {
  background:var(--kd-surface); color:var(--kd-accent);
  border-color:var(--kd-border); border-bottom-color:var(--kd-surface);
}
.kd-tab-filler { flex:1; border-bottom:1.5px solid var(--kd-border); position:relative; bottom:-1.5px; }
.kd-tab-badge {
  display:inline-flex; align-items:center; justify-content:center;
  min-width:36px; height:20px; padding:0 6px;
  border-radius:6px; font-size:.7rem; font-weight:900;
}
.kd-tab-active .kd-tab-badge { background:rgba(79,103,255,.15); color:var(--kd-accent); }
.kd-tab:not(.kd-tab-active) .kd-tab-badge { background:#F0F2F7; color:var(--kd-muted); }

/* ── PANES ── */
.kd-pane { display:none; padding-top:16px; }
.kd-pane.active { display:block; }

/* ── TWO COLUMN ── */
.kd-two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media (max-width:900px) { .kd-two-col { grid-template-columns:1fr; } }

/* ── SHIFT CARD ── */
.kd-shift-card {
  background:var(--kd-surface);
  border:1px solid var(--kd-border);
  border-radius:var(--kd-r);
  box-shadow:var(--kd-sh);
  overflow:hidden;
}
.kd-shift-head {
  display:flex; align-items:center; justify-content:space-between;
  padding:12px 16px;
  border-bottom:1px solid var(--kd-border);
  background:#FAFBFD;
}
.kd-shift-label {
  display:inline-flex; align-items:center; gap:7px;
  padding:5px 12px; border-radius:8px;
  font-size:.78rem; font-weight:800; letter-spacing:.04em;
}
.kd-pagi  { background:rgba(245,159,0,.12); color:#92400E; }
.kd-malam { background:rgba(79,103,255,.10); color:#3730A3; }
.kd-shift-stats { display:flex; align-items:center; gap:5px; font-size:.78rem; font-weight:700; }
.kd-stat-filled { color:#10B981; }
.kd-stat-sep    { color:#C5CAD8; }
.kd-stat-total  { color:var(--kd-muted); }
.kd-shift-body  { padding:12px 14px; }

/* ── PAGINATION ── */
.kd-pagination { margin-top:16px; padding:14px 18px; background:var(--kd-surface); border:1px solid var(--kd-border); border-radius:var(--kd-r); }
</style>
@endsection