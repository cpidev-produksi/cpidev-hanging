@php /** @var \Illuminate\Support\Collection $list */ @endphp

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
        {{ $list->filter(fn($it) => $it->hangingForm && $it->hangingForm->basket_condition && $it->hangingForm->truck_platform_condition && $it->hangingForm->feather_condition)->count() }}
        <span style="color:#9CA3AF;font-weight:600">/ {{ $list->count() }} terisi</span>
      </span>
    </div>
  </div>

  <div class="kl-body">
    @if($list->isEmpty())
      <div class="kl-empty">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.3"><rect x="1" y="3" width="15" height="13" rx="2"/>
          <path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
        <span>Belum ada data untuk {{ $location }}.</span>
      </div>
    @else
      <div class="kl-list">
        @foreach($list as $it)
          @php
            $hf     = $it->hangingForm;
            $filled = $hf && $hf->basket_condition && $hf->truck_platform_condition && $hf->feather_condition;
            $isDone = $hf && $hf->status === 'done';
          @endphp

          <div class="kl-row {{ $isDone ? 'kl-row-done' : '' }}">

            {{-- Truck number --}}
            <div class="kl-truck-col">
              <div class="kl-truck-num">#{{ $it->truck_no ?? '–' }}</div>
            </div>

            {{-- Main info --}}
            <div class="kl-info">
              <div class="kl-info-top">
                <code class="kl-code">{{ $it->report_code }}</code>
                <span class="kl-pill {{ $filled ? 'kl-pill-ok' : 'kl-pill-warn' }}">
                  {{ $filled ? 'Sudah Terisi' : 'Belum Diisi' }}
                </span>
                @if($isDone)
                  <span class="kl-done-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    DONE
                  </span>
                @endif
              </div>
              <div class="kl-info-bottom">
                <span class="kl-meta">
                  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                  {{ $it->process_date?->format('d/m/Y') ?? '–' }}
                </span>
                <span class="kl-dot">·</span>
                <span class="kl-meta">{{ $it->expedition?->name ?? '–' }}</span>
                <span class="kl-dot">·</span>
                <code class="kl-plate">{{ $it->plateNumber?->plate_number ?? '–' }}</code>
                <span class="kl-dot">·</span>
                <span class="kl-meta">{{ $it->farm?->name ?? '–' }}</span>
              </div>
            </div>

            {{-- Action --}}
            <div class="kl-actions">
              <form method="POST" action="{{ route('conditions.open', $it) }}" style="display:inline">
                @csrf
                <button type="submit" class="kl-btn {{ $isDone ? 'kl-btn-view' : 'kl-btn-input' }}">
                  @if($isDone)
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Lihat
                  @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Input Kondisi
                  @endif
                </button>
              </form>
            </div>

          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

<style>
/* ── CARD ── */
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
.kl-empty svg { opacity:.35; flex-shrink:0; }

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
}
.kl-btn-input { background:#0D1117; color:#fff; border-color:#0D1117; }
.kl-btn-input:hover { background:#1E2330; border-color:#1E2330; }
.kl-btn-view { background:#fff; color:#6B7896; border-color:#E2E5EE; }
.kl-btn-view:hover { background:#F0F2F7; border-color:#C5CAD8; }
</style>