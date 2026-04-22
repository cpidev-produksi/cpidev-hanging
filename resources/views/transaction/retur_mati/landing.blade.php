@extends('layouts.app')

@section('content')
<div class="rm-wrap">

  {{-- ── HEADER ── --}}
  <div class="rm-header">
    <div class="rm-header-left">
      <div class="rm-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <div>
        <h1 class="rm-title">Ayam Retur &amp; Mati</h1>
        <p class="rm-sub">Input jumlah ayam mati &amp; berat retur per truk</p>
      </div>
    </div>

    <form method="GET" class="rm-filter">
      <div class="rm-input-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" class="rm-input-icon">
          <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
        <input type="date" name="date" value="{{ $date ?? '' }}" class="rm-date-input">
      </div>
      <button class="rm-btn-filter" type="submit">Filter</button>
      <a href="{{ route('retur-mati.landing') }}" class="rm-btn-reset">Reset</a>
      <a href="{{ route('retur-mati.rekap') }}" class="rm-btn-reset">Rekap</a>
    </form>
  </div>

  @php
    $cntBelum = function($list) {
      return $list->filter(fn($it) => is_null($it->hangingForm) || is_null($it->hangingForm->dead_count))->count();
    };
  @endphp

  {{-- ── TABS (SH01 / SH02) ── --}}
  <div class="rm-tab-bar">
    @foreach($locations as $loc)
      @php
        $pagi  = $data[$loc]['pagi'];
        $malam = $data[$loc]['malam'];
        $total = $pagi->total() + $malam->total();
        $totalBelum = $cntBelum($pagi->getCollection()) + $cntBelum($malam->getCollection());
      @endphp
      <button type="button" class="rm-tab rm-tab-{{ strtolower($loc) }} {{ $loop->first ? 'rm-tab-active' : '' }}"
              data-tab="rm-tab-{{ strtolower($loc) }}-pane">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2"/>
          <path d="M16 7V5a2 2 0 0 0-4 0v2"/>
        </svg>
        {{ $loc }}
        <span class="rm-tab-cnt">{{ $total }}</span>
        @if($totalBelum > 0)
          <span class="rm-tab-warn">{{ $totalBelum }} belum</span>
        @endif
      </button>
    @endforeach
    <div class="rm-tab-filler"></div>
  </div>

  {{-- ── PANES ── --}}
  @foreach($locations as $loc)
      @php
        $pagi  = $data[$loc]['pagi'];
        $malam = $data[$loc]['malam'];
      @endphp

      <div id="rm-tab-{{ strtolower($loc) }}-pane" class="rm-pane {{ $loop->first ? 'active' : '' }}">
        @include('transaction.retur_mati.partials.list', [
          'location' => $loc,
          'listPagi'  => $pagi->getCollection(),
          'listMalam' => $malam->getCollection(),
          'pagiPaginator' => $pagi,  // Kirim paginator untuk pagi
          'malamPaginator' => $malam, // Kirim paginator untuk malam
          'theme'    => strtolower($loc),
        ])
      </div>
  @endforeach
  </div>

<script>
(function () {
  const tabs  = document.querySelectorAll('.rm-tab');
  const panes = document.querySelectorAll('.rm-pane');

  function activate(tabId) {
    tabs.forEach(t  => t.classList.toggle('rm-tab-active', t.dataset.tab === tabId));
    panes.forEach(p => p.classList.toggle('active', p.id === tabId));
    try { localStorage.setItem('returMatiTab2', tabId); } catch (e) {}
  }

  tabs.forEach(t => t.addEventListener('click', () => activate(t.dataset.tab)));

  try {
    const saved = localStorage.getItem('returMatiTab2');
    if (saved && document.getElementById(saved)) activate(saved);
  } catch (e) {}
})();
</script>

<style>
/* ── TOKENS ── */
:root {
  --rm-text:    #0D1117;
  --rm-muted:   #6B7896;
  --rm-border:  #E2E5EE;
  --rm-surface: #FFFFFF;
  --rm-accent:  #E85D2F;
  --rm-acc-hv:  #D04A1E;
  --rm-acc-xl:  rgba(232,93,47,.08);
  --rm-warn:    #F59F00;
  --rm-r:       14px;
  --rm-sh:      0 1px 4px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);

  /* per-lokasi accent */
  --sh01: #E85D2F;
  --sh01-xl: rgba(232,93,47,.08);
  --sh01-bd: rgba(232,93,47,.25);
  --sh02: #7C3AED;
  --sh02-xl: rgba(124,58,237,.08);
  --sh02-bd: rgba(124,58,237,.25);
}

.rm-wrap { max-width: 1240px; margin: 0 auto; padding: 32px 24px; }

/* HEADER */
.rm-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:22px; }
.rm-header-left { display:flex; align-items:center; gap:16px; }
.rm-icon { width:52px; height:52px; flex-shrink:0; background:var(--rm-acc-xl); color:var(--rm-accent); border-radius:14px; display:flex; align-items:center; justify-content:center; }
.rm-title { font-size:1.45rem; font-weight:800; color:var(--rm-text); margin:0 0 3px; letter-spacing:-.01em; }
.rm-sub   { font-size:.8rem; color:var(--rm-muted); margin:0; }

/* FILTER */
.rm-filter { display:flex; align-items:center; gap:10px; }
.rm-input-wrap { display:flex; align-items:center; border:1.5px solid var(--rm-border); border-radius:10px; background:var(--rm-surface); overflow:hidden; transition:border-color .18s, box-shadow .18s; }
.rm-input-wrap:focus-within { border-color:var(--rm-accent); box-shadow:0 0 0 3px var(--rm-acc-xl); }
.rm-input-icon { width:40px; display:flex; align-items:center; justify-content:center; color:var(--rm-muted); flex-shrink:0; }
.rm-date-input { border:none; outline:none; background:transparent; padding:10px 12px 10px 0; font-size:.875rem; color:var(--rm-text); }
.rm-btn-filter { display:inline-flex; align-items:center; gap:7px; padding:10px 18px; background:var(--rm-accent); color:#fff; border:none; border-radius:10px; font-size:.84rem; font-weight:700; cursor:pointer; box-shadow:0 2px 8px rgba(232,93,47,.28); transition:all .18s; }
.rm-btn-filter:hover { background:var(--rm-acc-hv); transform:translateY(-1px); }

/* TABS */
.rm-tab-bar { display:flex; align-items:flex-end; gap:0; border-bottom:1.5px solid var(--rm-border); margin-bottom:0; }
.rm-tab {
  display:inline-flex; align-items:center; gap:8px;
  padding:11px 22px;
  border:1.5px solid transparent; border-bottom:none;
  border-radius:10px 10px 0 0;
  background:transparent; color:var(--rm-muted);
  font-size:.85rem; font-weight:700; cursor:pointer;
  position:relative; bottom:-1.5px;
  transition:color .15s, background .15s, border-color .15s;
  white-space:nowrap;
}
.rm-tab:hover { color:var(--rm-text); background:#F5F7FA; border-color:var(--rm-border); }

/* SH01 active */
.rm-tab-sh01.rm-tab-active { background:var(--rm-surface); color:var(--sh01); border-color:var(--rm-border); border-bottom-color:var(--rm-surface); }
/* SH02 active */
.rm-tab-sh02.rm-tab-active { background:var(--rm-surface); color:var(--sh02); border-color:var(--rm-border); border-bottom-color:var(--rm-surface); }

.rm-tab-filler { flex:1; border-bottom:1.5px solid var(--rm-border); position:relative; bottom:-1.5px; }
.rm-tab-cnt { display:inline-flex; align-items:center; justify-content:center; min-width:20px; height:20px; padding:0 4px; border-radius:6px; background:#F0F2F7; color:var(--rm-muted); font-size:.7rem; font-weight:900; }
.rm-tab-active .rm-tab-cnt { background:rgba(0,0,0,.06); }
.rm-tab-warn { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:6px; background:rgba(245,159,0,.15); color:#92400E; font-size:.68rem; font-weight:900; }

/* PANES */
.rm-pane { display:none; padding-top:16px; }
.rm-pane.active { display:block; }

/* ===== PAGINATION YANG RAPI ===== */
.rm-pagination {
  margin-top: 24px;
  padding: 14px 20px;
  background: var(--rm-surface);
  border: 1px solid var(--rm-border);
  border-radius: var(--rm-r);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.pagination-info {
  font-size: 12px;
  color: #9ca3af;
}

.pagination-info--highlight {
  font-weight: 600;
  color: #374151;
}

.pagination-nav {
  display: flex;
  align-items: center;
  gap: 4px;
}

.page-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 32px;
  height: 32px;
  padding: 0 6px;
  font-size: 12px;
  font-weight: 500;
  color: #4b5563;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 7px;
  text-decoration: none;
  transition: background .13s, color .13s, border-color .13s, box-shadow .13s;
  cursor: pointer;
  user-select: none;
}

.rm-btn-reset {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 10px 18px;
  background: #f3f4f6;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: .84rem;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  transition: all .18s;
}
.rm-btn-reset:hover {
  background: #e5e7eb;
}

.page-btn:hover {
  background: #eef2ff;
  color: #4338ca;
  border-color: #c7d2fe;
}

.page-btn--active {
  background: var(--rm-accent);
  color: #fff;
  border-color: var(--rm-accent);
  box-shadow: 0 1px 6px rgba(232,93,47,.35);
  font-weight: 600;
  cursor: default;
}

.page-btn--active:hover {
  background: var(--rm-accent);
  color: #fff;
  border-color: var(--rm-accent);
}

.page-btn--disabled {
  background: #f9fafb;
  color: #d1d5db;
  border-color: #f3f4f6;
  cursor: not-allowed;
  pointer-events: none;
}

.page-ellipsis {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 32px;
  font-size: 13px;
  color: #9ca3af;
  letter-spacing: .1em;
}

/* Responsive */
@media (max-width: 680px) {
  .rm-pagination {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
}
</style>
@endsection