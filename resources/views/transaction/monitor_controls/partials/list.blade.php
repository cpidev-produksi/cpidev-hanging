@php
  $slug = auth()->user()?->role?->slug;
  $canEditDone = in_array($slug, ['supervisor','superadmin'], true);
@endphp

@if($rows->isEmpty())
  <div class="lst-empty">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.3"><rect x="1" y="3" width="15" height="13" rx="2"/>
      <path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
    </svg>
    <span>Tidak ada data</span>
  </div>
@else
  <div class="lst-list">
    @foreach($rows as $it)
      <div class="lst-row lst-row-{{ $it->status }}">

        {{-- Truck number + move controls --}}
        <div class="lst-truck-col">
          @if($it->status === 'draft' && $showMove && $it->truck_no)
            <div class="lst-move-group">
              <form method="POST" action="{{ route('monitor-controls.move', $it) }}" style="display:inline">
                @csrf
                <input type="hidden" name="direction" value="up">
                <button class="lst-move-btn" title="Naik">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="3"><polyline points="18 15 12 9 6 15"/></svg>
                </button>
              </form>
              <div class="lst-truck-num">#{{ $it->truck_no }}</div>
              <form method="POST" action="{{ route('monitor-controls.move', $it) }}" style="display:inline">
                @csrf
                <input type="hidden" name="direction" value="down">
                <button class="lst-move-btn" title="Turun">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
              </form>
            </div>
          @else
            <div class="lst-truck-num lst-truck-num-static">#{{ $it->truck_no ?? '–' }}</div>
          @endif
        </div>

        {{-- Main info --}}
        <div class="lst-info">
          <div class="lst-info-top">
            <code class="lst-code">{{ $it->report_code }}</code>
            <span class="lst-status lst-s-{{ $it->status }}">{{ $it->status }}</span>
            @if($it->size)
              <span class="lst-size-badge">{{ $it->size }}</span>
            @endif
          </div>
          <div class="lst-info-bottom">
            <span class="lst-meta">
              <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13" rx="2"/>
                <path d="M16 8h4l3 5v3h-7V8z"/>
              </svg>
              <code class="lst-plate">{{ $it->plateNumber?->plate_number ?? '–' }}</code>
            </span>
            <span class="lst-dot">·</span>
            <span class="lst-meta">{{ $it->expedition?->name ?? '–' }}</span>
            <span class="lst-dot">·</span>
            <span class="lst-meta">{{ $it->farm?->name ?? '–' }}</span>
          </div>
        </div>

        {{-- Actions --}}
        <div class="lst-actions">
          @if($it->status === 'draft' || $canEditDone)
            <a href="{{ route('monitor-controls.edit', $it) }}" class="lst-btn lst-btn-edit">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
              Edit
            </a>
          @endif

          @if($it->status === 'draft')
            <form method="POST" action="{{ route('monitor-controls.destroy', $it) }}" style="display:inline"
                  onsubmit="return confirm('Hapus data truk #{{ $it->truck_no }}?')">
              @csrf @method('DELETE')
              <button class="lst-btn lst-btn-del">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                  <path d="M9 6V4h6v2"/>
                </svg>
                Hapus
              </button>
            </form>
          @else
            <a href="{{ route('hanging.landing') }}" class="lst-btn lst-btn-hanging">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2.5"><polygon points="6 3 20 12 6 21 6 3"/>
              </svg>
              Hanging
            </a>
          @endif
        </div>

      </div>
    @endforeach
  </div>
@endif

<style>
/* ── LIST ── */
.lst-list  { display:flex; flex-direction:column; gap:7px; }
.lst-empty { display:flex; align-items:center; gap:8px; padding:16px 12px; color:#9CA3AF; font-size:.82rem; font-weight:700; }
.lst-empty svg { opacity:.35; flex-shrink:0; }

/* ── ROW ── */
.lst-row {
  display:flex; align-items:center; gap:10px;
  padding:11px 13px;
  border:1.5px solid #E8EBF2;
  border-radius:12px;
  background:#fff;
  transition:border-color .15s, box-shadow .15s, transform .15s;
}
.lst-row:hover {
  border-color:rgba(232,93,47,.28);
  box-shadow:0 3px 12px rgba(232,93,47,.07);
  transform:translateY(-1px);
}
/* running row: warm amber tint */
.lst-row-running {
  border-color:rgba(245,159,0,.35);
  background:rgba(245,159,0,.03);
}
.lst-row-running:hover { border-color:rgba(245,159,0,.5); }

/* ── TRUCK COL ── */
.lst-truck-col { flex-shrink:0; }
.lst-move-group { display:flex; flex-direction:column; align-items:center; gap:2px; }
.lst-move-btn {
  width:22px; height:22px;
  border:none; border-radius:6px;
  background:#F0F2F7; color:#6B7896;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer;
  transition:background .13s, color .13s;
}
.lst-move-btn:hover { background:#0D1117; color:#fff; }
.lst-truck-num {
  font-size:.78rem; font-weight:900;
  color:#E85D2F;
  background:rgba(232,93,47,.1);
  border:1px solid rgba(232,93,47,.2);
  padding:3px 9px; border-radius:7px;
  letter-spacing:.02em; text-align:center; min-width:34px;
}
.lst-truck-num-static { display:inline-flex; align-items:center; justify-content:center; }

/* ── INFO ── */
.lst-info { flex:1; min-width:0; }
.lst-info-top { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:5px; }
.lst-code {
  font-family:'Fira Code','Courier New',monospace;
  font-size:.76rem; font-weight:600;
  background:#F3F4F8; color:#4B5563;
  padding:2px 8px; border-radius:6px;
}
.lst-status {
  display:inline-flex; padding:2px 9px; border-radius:999px;
  font-size:.68rem; font-weight:900; letter-spacing:.04em;
  text-transform:lowercase; border:1px solid transparent;
}
.lst-s-draft   { background:#F3F4F8; color:#4B5563; border-color:rgba(75,85,99,.15); }
.lst-s-running { background:rgba(245,159,0,.12); color:#92400E; border-color:rgba(245,159,0,.3); }

/* ── SIZE BADGE ── */
.lst-size-badge {
  display:inline-flex; align-items:center; justify-content:center;
  padding:2px 9px; border-radius:999px;
  background:rgba(79,103,255,.1); color:#3730A3;
  font-size:.68rem; font-weight:900; letter-spacing:.04em;
  border:1px solid rgba(79,103,255,.2);
}

/* ── SUB ROW ── */
.lst-info-bottom { display:flex; align-items:center; gap:5px; flex-wrap:wrap; font-size:.76rem; color:#6B7896; font-weight:600; }
.lst-meta { display:inline-flex; align-items:center; gap:3px; }
.lst-plate { font-family:'Fira Code','Courier New',monospace; font-size:.74rem; color:#6B7896; }
.lst-dot   { color:#D1D5E0; }

/* ── ACTIONS ── */
.lst-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
.lst-btn {
  display:inline-flex; align-items:center; gap:5px;
  padding:7px 12px; border-radius:8px;
  font-size:.78rem; font-weight:700;
  text-decoration:none; cursor:pointer;
  transition:all .14s;
  border:1.5px solid var(--mc-border, #E2E5EE);
}
.lst-btn-edit { background:#fff; color:#0D1117; }
.lst-btn-edit:hover { background:#F0F2F7; border-color:#C5CAD8; }
.lst-btn-del  { background:#fff; color:#EF4444; border-color:rgba(239,68,68,.25); }
.lst-btn-del:hover { background:#EF4444; color:#fff; border-color:#EF4444; }
.lst-btn-hanging { background:#0D1117; color:#fff; border-color:#0D1117; }
.lst-btn-hanging:hover { background:#1E2330; border-color:#1E2330; }
</style>