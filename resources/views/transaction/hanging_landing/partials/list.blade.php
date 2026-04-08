@php
  $runningRows = $list->filter(fn($x) => ($x->hangingForm?->status ?? '') === 'running')
                      ->sortBy('truck_no')
                      ->values();

  $draftRows = $list->filter(fn($x) => ($x->hangingForm?->status ?? '') === 'draft')
                    ->sortBy('truck_no')
                    ->values();

  $doneRows = $list->filter(fn($x) => ($x->hangingForm?->status ?? '') === 'done')
                   ->sortBy('truck_no')
                   ->values();

  $key = $location;
@endphp

<div class="lst-card">
  <div class="lst-head">
    <div class="lst-head-left">
      <div class="lst-loc-badge">{{ $location }}</div>
      <span class="lst-head-title">Daftar Truk</span>
    </div>
    <div class="lst-total-pill">
      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="2"/>
        <path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
      {{ $list->count() }} Truk
    </div>
  </div>

  <div class="lst-body">
    @if($runningRows->isEmpty() && $draftRows->isEmpty() && $doneRows->isEmpty())
      <div class="lst-empty">
        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/>
        </svg>
        <p>Belum ada kontrol monitor untuk <strong>{{ $location }}</strong>.</p>
      </div>
    @else
      {{-- RUNNING --}}
      <div class="lst-grid">
        @foreach($runningRows as $it)
          @php
            $hf       = $it->hangingForm;
            $hfStatus = $hf?->status;
          @endphp
          @include('transaction.hanging_landing.partials.list_row', [
            'it' => $it, 'hf' => $hf, 'hfStatus' => $hfStatus
          ])
        @endforeach
      </div>

      {{-- DRAFT --}}
      <div class="lst-grid" style="margin-top:8px">
        @foreach($draftRows as $it)
          @php
            $hf       = $it->hangingForm;
            $hfStatus = $hf?->status;
          @endphp
          @include('transaction.hanging_landing.partials.list_row', [
            'it' => $it, 'hf' => $hf, 'hfStatus' => $hfStatus
          ])
        @endforeach
      </div>

      {{-- DONE (DROPDOWN TOGGLE) --}}
      @include('transaction.hanging_landing.partials.done', [
        'rows' => $doneRows,
        'key'  => $key
      ])
    @endif
  </div>
</div>

<style>
/* ── EMPTY ── */
.lst-empty {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 48px 20px; gap: 10px;
  color: #9CA3AF;
}
.lst-empty svg { opacity: .3; }
.lst-empty p { margin: 0; font-size: .88rem; font-weight: 600; }

/* ── LIST CARD ── */
.lst-card {
  background: #fff;
  border: 1px solid #E2E5EE;
  border-radius: 14px;
  box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 6px 18px rgba(0,0,0,.04);
  overflow: hidden;
}

/* ── CARD HEAD ── */
.lst-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 18px;
  border-bottom: 1px solid #E2E5EE;
  background: #FAFBFD;
}
.lst-head-left { display: flex; align-items: center; gap: 10px; }
.lst-loc-badge {
  padding: 4px 12px;
  border-radius: 8px;
  background: rgba(232,93,47,.10);
  color: #E85D2F;
  font-size: .72rem; font-weight: 900; letter-spacing: .08em;
}
.lst-head-title { font-size: .88rem; font-weight: 800; color: #0D1117; }
.lst-total-pill {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 12px;
  border-radius: 8px;
  background: #F0F2F7;
  color: #6B7896;
  font-size: .75rem; font-weight: 700;
}

/* ── BODY ── */
.lst-body { padding: 12px 14px; }
.lst-grid { display: flex; flex-direction: column; gap: 8px; }

/* ── ROW ── */
.lst-row {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 16px;
  border: 1.5px solid #E8EBF2;
  border-radius: 12px;
  background: #fff;
  transition: border-color .15s, box-shadow .15s, transform .15s;
}
.lst-row:hover {
  border-color: rgba(232,93,47,.3);
  box-shadow: 0 4px 14px rgba(232,93,47,.08);
  transform: translateY(-1px);
}

/* ── TRUCK BADGE ── */
.lst-truck-badge {
  width: 52px; min-width: 52px; height: 52px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 12px;
  background: linear-gradient(135deg, #FFF0EB 0%, #FFE4D9 100%);
  border: 1.5px solid rgba(232,93,47,.2);
}
.lst-truck-no {
  font-size: .8rem; font-weight: 900; color: #E85D2F; letter-spacing: .02em;
}

/* ── INFO ── */
.lst-info { flex: 1; min-width: 0; }
.lst-info-top { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 6px; }
.lst-code {
  font-family: 'Fira Code', 'Courier New', monospace;
  font-size: .78rem; font-weight: 600;
  background: #F3F4F8; color: #4B5563;
  padding: 3px 10px; border-radius: 7px;
}
.lst-status {
  display: inline-flex;
  padding: 3px 10px; border-radius: 999px;
  font-size: .7rem; font-weight: 900;
  letter-spacing: .04em; text-transform: lowercase;
  border: 1px solid transparent;
}
.lst-status-draft   { background: #F3F4F8; color: #4B5563; border-color: rgba(75,85,99,.15); }
.lst-status-running { background: rgba(245,159,0,.12); color: #92400E; border-color: rgba(245,159,0,.3); }
.lst-status-done    { background: rgba(16,185,129,.12); color: #065F46; border-color: rgba(16,185,129,.25); }

.lst-info-bottom {
  display: flex; align-items: center; gap: 6px;
  flex-wrap: wrap;
  font-size: .78rem; color: #6B7896; font-weight: 600;
}
.lst-meta-item { display: flex; align-items: center; gap: 4px; }
.lst-dot { color: #C5CAD8; }
.lst-badge-shift {
  padding: 2px 8px; border-radius: 6px;
  background: #F0F2F7; color: #4B5563;
  font-size: .7rem; font-weight: 900; letter-spacing: .04em;
}

/* ── SIZE BADGE ── */
.lst-badge-size {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 8px;
  border-radius: 6px;
  background: rgba(232, 93, 47, 0.08);
  color: #E85D2F;
  font-size: .7rem;
  font-weight: 800;
  letter-spacing: .02em;
}

.lst-plate {
  font-family: 'Fira Code', 'Courier New', monospace;
  font-size: .76rem; color: #6B7896;
  background: #F3F4F8; padding: 2px 8px; border-radius: 6px;
}

/* ── ACTIONS ── */
.lst-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; flex-shrink: 0; }

.lst-btn-open {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px;
  border: 1.5px solid #E2E5EE;
  border-radius: 9px;
  background: #fff; color: #6B7896;
  font-size: .8rem; font-weight: 700;
  cursor: pointer; text-decoration: none;
  transition: all .15s;
}
.lst-btn-open:hover { border-color: #C5CAD8; color: #0D1117; background: #F5F7FA; }

.lst-btn-start {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px;
  border: none; border-radius: 9px;
  background: #F59F00; color: #fff;
  font-size: .8rem; font-weight: 700;
  cursor: pointer;
  transition: filter .15s, transform .15s;
  box-shadow: 0 2px 8px rgba(245,159,0,.3);
}
.lst-btn-start:hover { filter: brightness(.93); transform: translateY(-1px); }

.lst-btn-lanjut {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px;
  border: none; border-radius: 9px;
  background: #0D1117; color: #fff;
  font-size: .8rem; font-weight: 700;
  text-decoration: none;
  transition: background .15s, transform .15s;
}
.lst-btn-lanjut:hover { background: #1E2330; transform: translateY(-1px); }

.lst-done-pill {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 12px; border-radius: 999px;
  background: rgba(16,185,129,.1); color: #065F46;
  font-size: .74rem; font-weight: 900; letter-spacing: .04em;
  border: 1px solid rgba(16,185,129,.22);
}

.lst-no-form { color: #9CA3AF; font-size: .78rem; font-weight: 700; }

/* ── EMPTY ── */
.lst-empty {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 48px 20px; gap: 10px;
  color: #9CA3AF;
}
.lst-empty svg { opacity: .3; }
.lst-empty p { margin: 0; font-size: .88rem; font-weight: 600; }

/* ── RESPONSIVE ── */
@media (max-width: 720px) {
  .lst-row { flex-wrap: wrap; }
  .lst-actions { width: 100%; justify-content: flex-start; }
}
</style>