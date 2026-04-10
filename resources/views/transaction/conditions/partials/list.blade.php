@php /** @var \Illuminate\Pagination\LengthAwarePaginator $list */ @endphp

@php
  $col = $list->getCollection();

  $sorted = $col->sort(function($a, $b) {
    $doneA = $a->hangingForm && $a->hangingForm->status === 'done';
    $doneB = $b->hangingForm && $b->hangingForm->status === 'done';

    if ($doneA && !$doneB) return 1;
    if (!$doneA && $doneB) return -1;

    $truckA = is_numeric($a->truck_no ?? null) ? (int)$a->truck_no : 0;
    $truckB = is_numeric($b->truck_no ?? null) ? (int)$b->truck_no : 0;
    return $truckA <=> $truckB;
  })->values();

  $activeRows = $sorted->filter(fn($it) => !($it->hangingForm && $it->hangingForm->status === 'done'));
  $doneRows   = $sorted->filter(fn($it) =>  ($it->hangingForm && $it->hangingForm->status === 'done'));

  $domId = 'done-'.strtolower($location).'-'.uniqid();
@endphp

<div class="kl-card">
  <div class="kl-card-head">
    <div class="kl-card-title">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13" rx="2"/>
        <path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
      Daftar Truk — <span class="kl-loc-badge">{{ $location }}</span>
    </div>
    <div class="kl-card-meta">
      <span class="kl-stat">
        {{ $sorted->filter(fn($it) => $it->hangingForm && $it->hangingForm->basket_condition && $it->hangingForm->truck_platform_condition && $it->hangingForm->feather_condition)->count() }}
        <span style="color:#9CA3AF;font-weight:600">/ {{ $sorted->count() }} terisi</span>
      </span>
    </div>
  </div>

  <div class="kl-body">
    @if($sorted->isEmpty())
      <div class="kl-empty">Belum ada data untuk {{ $location }}.</div>
    @else
      <div class="kl-list">
        @foreach($activeRows as $it)
          @include('transaction.conditions.partials.row', ['it' => $it])
        @endforeach
      </div>

      {{-- DONE TOGGLE (Improved) --}}
      @if($doneRows->isNotEmpty())
        <div class="kl-done-section">
          <button type="button"
                  class="kl-done-toggle"
                  id="toggle-btn-{{ $domId }}"
                  aria-expanded="false"
                  aria-controls="{{ $domId }}"
                  onclick="kdToggle('{{ $domId }}')">
            <div class="kl-done-toggle-left">
              <div class="kl-done-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <span class="kl-done-label">Selesai</span>
              <span class="kl-done-count">{{ $doneRows->count() }}</span>
            </div>
            <div class="kl-done-chevron" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </button>

          <div id="{{ $domId }}" class="kl-done-body" aria-hidden="true">
            <div class="kl-list kl-done-list">
              @foreach($doneRows as $it)
                @include('transaction.conditions.partials.row', ['it' => $it])
              @endforeach
            </div>
          </div>
        </div>
      @endif
    @endif
  </div>
</div>

<script>
function kdToggle(id) {
  const body   = document.getElementById(id);
  const btn    = document.getElementById('toggle-btn-' + id);
  const isOpen = body.classList.contains('kl-done-open');

  body.classList.toggle('kl-done-open', !isOpen);
  btn.setAttribute('aria-expanded', !isOpen);
  body.setAttribute('aria-hidden', isOpen);
}
</script>

<style>
/* ── CARD ── */
.kl-btn-edit {
  background:#F59F00;
  color:#fff;
  border-color:#F59F00;
}
.kl-btn-edit:hover { background:#D97706; border-color:#D97706; }

.kl-card {
  background:#fff;
  border:1px solid #E2E5EE;
  border-radius:14px;
  box-shadow:0 1px 4px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);
  overflow:hidden;
}
.kl-card-head {
  display:flex; align-items:center; justify-content:space-between;
  padding:13px 16px;
  border-bottom:1px solid #E2E5EE;
  background:#FAFBFD;
}
.kl-card-title {
  display:flex; align-items:center; gap:8px;
  font-size:.88rem; font-weight:800; color:#0D1117;
}
.kl-loc-badge {
  padding:3px 10px; border-radius:7px;
  background:rgba(79,103,255,.1); color:#3730A3;
  font-size:.78rem; font-weight:900;
  border:1px solid rgba(79,103,255,.2);
}
.kl-card-meta { font-size:.82rem; font-weight:800; color:#0D1117; }
.kl-stat { display:flex; align-items:center; gap:4px; }

/* ── BODY & EMPTY ── */
.kl-body  { padding:10px 12px; }
.kl-list  { display:flex; flex-direction:column; gap:7px; }
.kl-empty {
  display:flex; align-items:center; gap:8px;
  padding:16px 12px; color:#9CA3AF;
  font-size:.82rem; font-weight:700;
}

/* ── ROW ── */
.kl-row {
  display:flex; align-items:center; gap:10px;
  padding:11px 13px;
  border:1.5px solid #E8EBF2;
  border-radius:12px;
  background:#fff;
  transition:border-color .15s, box-shadow .15s, transform .15s;
}
.kl-row:hover {
  border-color:rgba(79,103,255,.28);
  box-shadow:0 3px 12px rgba(79,103,255,.07);
  transform:translateY(-1px);
}
.kl-row-done {
  border-color:rgba(16,185,129,.2);
  background:rgba(16,185,129,.025);
}
.kl-row-done:hover { border-color:rgba(16,185,129,.4); box-shadow:0 3px 12px rgba(16,185,129,.07); }

/* ── TRUCK NUM ── */
.kl-truck-col { flex-shrink:0; }
.kl-truck-num {
  font-size:.78rem; font-weight:900; color:#4F67FF;
  background:rgba(79,103,255,.1);
  border:1px solid rgba(79,103,255,.2);
  padding:3px 9px; border-radius:7px;
  letter-spacing:.02em; text-align:center; min-width:34px;
}

/* ── INFO ── */
.kl-info { flex:1; min-width:0; }
.kl-info-top { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:5px; }
.kl-code {
  font-family:'Fira Code','Courier New',monospace;
  font-size:.76rem; font-weight:600;
  background:#F3F4F8; color:#4B5563;
  padding:2px 8px; border-radius:6px;
}
.kl-pill {
  display:inline-flex; align-items:center;
  padding:2px 9px; border-radius:999px;
  font-size:.68rem; font-weight:900; letter-spacing:.04em;
  border:1px solid transparent;
}
.kl-pill-ok   { background:rgba(16,185,129,.1); color:#065F46; border-color:rgba(16,185,129,.2); }
.kl-pill-warn { background:rgba(245,159,0,.12); color:#92400E; border-color:rgba(245,159,0,.3); }
.kl-done-badge {
  display:inline-flex; align-items:center; gap:4px;
  padding:2px 9px; border-radius:999px;
  background:rgba(16,185,129,.12); color:#065F46;
  font-size:.68rem; font-weight:900;
  border:1px solid rgba(16,185,129,.2);
}

/* ── SUB ROW ── */
.kl-info-bottom { display:flex; align-items:center; gap:5px; flex-wrap:wrap; font-size:.76rem; color:#6B7896; font-weight:600; }
.kl-meta { display:inline-flex; align-items:center; gap:3px; }
.kl-plate { font-family:'Fira Code','Courier New',monospace; font-size:.74rem; color:#6B7896; }
.kl-dot { color:#D1D5E0; }

/* ── ACTIONS ── */
.kl-actions { display:flex; align-items:center; flex-shrink:0; }
.kl-btn {
  display:inline-flex; align-items:center; gap:5px;
  padding:8px 14px; border-radius:9px;
  font-size:.78rem; font-weight:700;
  border:1.5px solid transparent;
  cursor:pointer; transition:all .14s;
  text-decoration:none;
}
.kl-btn-input { background:#0D1117; color:#fff; border-color:#0D1117; }
.kl-btn-input:hover { background:#1E2330; border-color:#1E2330; }
.kl-btn-view { background:#fff; color:#6B7896; border-color:#E2E5EE; }
.kl-btn-view:hover { background:#F0F2F7; border-color:#C5CAD8; }

/* ── DONE SECTION ── */
.kl-done-section { margin-top:10px; }

.kl-done-toggle {
  width:100%;
  display:flex; align-items:center; justify-content:space-between;
  padding:10px 14px;
  border-radius:12px;
  border:1.5px dashed rgba(16,185,129,.35);
  background:rgba(16,185,129,.04);
  cursor:pointer;
  transition:background .18s, border-color .18s;
  user-select:none;
  appearance:none; -webkit-appearance:none;
  font-family:inherit;
}
.kl-done-toggle:hover {
  background:rgba(16,185,129,.08);
  border-color:rgba(16,185,129,.55);
}
.kl-done-toggle[aria-expanded="true"] {
  background:rgba(16,185,129,.07);
  border-style:solid;
  border-color:rgba(16,185,129,.4);
  border-radius:12px 12px 0 0;
}

.kl-done-toggle-left { display:flex; align-items:center; gap:10px; }

.kl-done-icon {
  width:28px; height:28px; border-radius:8px;
  background:rgba(16,185,129,.12);
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
  color:#059669;
}

.kl-done-label {
  font-size:.82rem; font-weight:700; color:#065F46;
}

.kl-done-count {
  display:inline-flex; align-items:center; justify-content:center;
  min-width:22px; height:20px; padding:0 7px;
  background:rgba(16,185,129,.15); color:#059669;
  border-radius:10px; font-size:.72rem; font-weight:900;
}

.kl-done-chevron {
  width:26px; height:26px; border-radius:7px;
  background:rgba(16,185,129,.1);
  display:flex; align-items:center; justify-content:center;
  transition:transform .25s ease, background .18s;
  color:#059669; flex-shrink:0;
}
.kl-done-toggle[aria-expanded="true"] .kl-done-chevron {
  transform:rotate(180deg);
  background:rgba(16,185,129,.2);
}

/* ── DONE BODY (animated) ── */
.kl-done-body {
  overflow:hidden;
  max-height:0;
  opacity:0;
  transition:max-height .32s ease, opacity .25s ease, padding .2s ease;
  padding:0 0;
  border:1.5px solid transparent;
  border-top:none;
  border-radius:0 0 12px 12px;
}
.kl-done-body.kl-done-open {
  max-height:2000px;
  opacity:1;
  padding:10px 0 0 0;
  border-color:rgba(16,185,129,.3);
  background:rgba(16,185,129,.025);
}

.kl-done-list { padding:8px 10px 10px; }
</style>