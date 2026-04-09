@php
  /**
   * @var \Illuminate\Support\Collection $listPagi
   * @var \Illuminate\Support\Collection $listMalam
   * @var string $location  (SH01 | SH02)
   * @var string $theme     (sh01 | sh02)
   */
  $themeVar   = $theme === 'sh01' ? '#E85D2F' : '#7C3AED';
  $themeXl    = $theme === 'sh01' ? 'rgba(232,93,47,.08)'  : 'rgba(124,58,237,.08)';
  $themeBd    = $theme === 'sh01' ? 'rgba(232,93,47,.22)'  : 'rgba(124,58,237,.22)';
  $themeLight = $theme === 'sh01' ? 'rgba(232,93,47,.03)'  : 'rgba(124,58,237,.03)';

  $sortByPriority = function($collection) {
    return $collection->sort(function($a, $b) {
      $statusA = $a->hangingForm?->status ?? null;
      $statusB = $b->hangingForm?->status ?? null;

      $isRunningA = $statusA === 'running';
      $isRunningB = $statusB === 'running';

      if ($isRunningA && !$isRunningB) return -1;
      if (!$isRunningA && $isRunningB) return 1;

      $isDraftA = is_null($a->hangingForm) || is_null($a->hangingForm->dead_count);
      $isDraftB = is_null($b->hangingForm) || is_null($b->hangingForm->dead_count);

      if ($isDraftA && !$isDraftB) return -1;
      if (!$isDraftA && $isDraftB) return 1;

      $isDoneA = $statusA === 'done';
      $isDoneB = $statusB === 'done';

      if ($isDoneA && !$isDoneB) return 1;
      if (!$isDoneA && $isDoneB) return -1;

      // fallback aman
      $truckA = is_numeric($a->truck_no ?? null) ? (int)$a->truck_no : 0;
      $truckB = is_numeric($b->truck_no ?? null) ? (int)$b->truck_no : 0;

      return $truckA <=> $truckB;
    })->values();
  };
@endphp

  $shifts = [
    ['list' => $sortByPriority($listPagi), 'label' => 'Shift Pagi', 'key' => 'pagi'],
    ['list' => $sortByPriority($listMalam), 'label' => 'Shift Malam', 'key' => 'malam'],
  ];
@endphp

<div class="rl-wrap" style="--t:{{ $themeVar }};--t-xl:{{ $themeXl }};--t-bd:{{ $themeBd }};--t-light:{{ $themeLight }}">

  @foreach($shifts as $shift)
    @php
      $sList   = $shift['list'];
      $sLabel  = $shift['label'];
      $sPagi   = $shift['key'] === 'pagi';
      $cntTotal   = $sList->count();
      $cntDone    = $sList->filter(fn($it) => $it->hangingForm?->status === 'done')->count();
      $cntBelum   = $sList->filter(fn($it) => is_null($it->hangingForm) || is_null($it->hangingForm->dead_count))->count();
      $cntRunning = $sList->filter(fn($it) => $it->hangingForm?->status === 'running')->count();
      $cntPartial = $cntTotal - $cntDone - $cntBelum - $cntRunning;

      // Pisahkan item berdasarkan status untuk toggle
      $activeItems = $sList->filter(fn($it) => $it->hangingForm?->status !== 'done');
      $doneItems   = $sList->filter(fn($it) => $it->hangingForm?->status === 'done');
      $hasDone     = $doneItems->count() > 0;
    @endphp

    <div class="rl-shift-card rl-shift-{{ $shift['key'] }}">

      {{-- shift header --}}
      <div class="rl-shift-head">
        <div class="rl-shift-label">
          @if($sPagi)
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/>
              <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
              <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
              <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
              <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
          @else
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
          @endif
          {{ $sLabel }}
        </div>
        <div class="rl-shift-stats">
          <span class="rl-stat rl-stat-total">{{ $cntTotal }} truk</span>
          @if($cntRunning > 0)
            <span class="rl-stat rl-stat-running">
              <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
              </svg>
              {{ $cntRunning }} proses
            </span>
          @endif
          @if($cntDone > 0)
            <span class="rl-stat rl-stat-done">
              <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
              {{ $cntDone }} selesai
            </span>
          @endif
          @if($cntBelum > 0)
            <span class="rl-stat rl-stat-warn">
              <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="3"><line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
              {{ $cntBelum }} belum isi
            </span>
          @endif
        </div>
      </div>

      {{-- progress bar --}}
      @if($cntTotal > 0)
        <div class="rl-prog-wrap">
          <div class="rl-prog-track">
            <div class="rl-prog-running" style="width:{{ ($cntRunning/$cntTotal)*100 }}%"></div>
            <div class="rl-prog-partial" style="width:{{ ($cntPartial/$cntTotal)*100 }}%"></div>
            <div class="rl-prog-done"    style="width:{{ ($cntDone/$cntTotal)*100 }}%"></div>
          </div>
          <span class="rl-prog-txt">{{ $cntDone }}/{{ $cntTotal }} selesai</span>
        </div>
      @endif

      {{-- ACTIVE ITEMS (RUNNING + DRAFT/PROSES) --}}
      <div class="rl-body rl-active-section">
        @if($activeItems->isEmpty())
          <div class="rl-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.3"><rect x="1" y="3" width="15" height="13" rx="2"/>
              <path d="M16 8h4l3 5v3h-7V8z"/>
              <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            @if($hasDone)
              Semua item sudah selesai ✨
            @else
              Tidak ada truk untuk {{ $sLabel }}.
            @endif
          </div>
        @else
          @foreach($activeItems as $it)
            @php
              $hf         = $it->hangingForm;
              $hfStatus   = $hf?->status ?? null;
              $dead       = $hf ? (int)($hf->dead_count ?? 0) : null;
              $returCount = (int)($hf?->retur_count ?? 0);
              $returKg    = (float)($hf?->retur_total_kg ?? 0);
              $isRunning  = $hfStatus === 'running';
              $isDone     = $hfStatus === 'done';
              $isBelum    = is_null($hf) || is_null($dead);
              $isPartial  = !$isDone && !$isBelum && !$isRunning;
            @endphp

            <div class="rl-row 
              @if($isRunning) rl-row-running 
              @elseif($isBelum) rl-row-belum 
              @elseif($isPartial) rl-row-partial 
              @endif">
              <div class="rl-stripe 
                @if($isRunning) rl-stripe-running 
                @elseif($isBelum) rl-stripe-belum 
                @elseif($isPartial) rl-stripe-accent 
                @endif"></div>

              {{-- truck + state --}}
              <div class="rl-truck">
                <div class="rl-truck-num">#{{ $it->truck_no ?? '–' }}</div>
                <div class="rl-state-badge 
                  @if($isRunning) rsb-running 
                  @elseif($isBelum) rsb-belum 
                  @elseif($isPartial) rsb-partial 
                  @endif">
                  @if($isRunning)
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                      <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Proses
                  @elseif($isBelum)
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                      <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Belum
                  @else
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                      <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Partial
                  @endif
                </div>
              </div>

              {{-- info --}}
              <div class="rl-info">
                <div class="rl-top">
                  <code class="rl-code">{{ $it->report_code }}</code>
                  <span class="rl-mc-pill rl-mc-{{ $it->status }}">{{ $it->status }}</span>
                  <span class="rl-hf-pill @if($hfStatus==='done') rhf-done @elseif($hfStatus==='running') rhf-run @else rhf-other @endif">
                    Hanging: {{ $hfStatus ?? '—' }}
                  </span>
                </div>
                <div class="rl-mid">
                  <span class="rl-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/>
                      <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    {{ $it->process_date?->format('d/m/Y') }}
                  </span>
                  <span class="rl-dot">·</span>
                  <span class="rl-shift-pill">{{ strtoupper($it->shift) }}</span>
                  <span class="rl-dot">·</span>
                  <span class="rl-meta-item">{{ $it->farm?->name ?? '–' }}</span>
                  <span class="rl-dot">·</span>
                  <span class="rl-meta-item">{{ $it->expedition?->name ?? '–' }}</span>
                  <span class="rl-dot">·</span>
                  <code class="rl-plate">{{ $it->plateNumber?->plate_number ?? '–' }}</code>
                </div>

                @if($isBelum)
                  <div class="rl-unread">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
                      <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Data retur &amp; mati belum diisi — klik <strong>Isi Sekarang</strong>
                  </div>
                @elseif(!$isRunning)
                  <div class="rl-chips">
                    <span class="rl-chip rl-chip-mati">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      Mati <strong>{{ $dead }}</strong>
                    </span>
                    <span class="rl-chip rl-chip-retur">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/></svg>
                      Retur <strong>{{ $returCount }}</strong> ekor
                    </span>
                    <span class="rl-chip rl-chip-kg">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2v20M18 2v20M2 12h20"/></svg>
                      <strong>{{ number_format($returKg, 2) }}</strong> Kg
                    </span>
                  </div>
                @endif
              </div>

              {{-- actions --}}
              <div class="rl-actions">
                <form method="POST" action="{{ route('retur-mati.open', $it) }}" style="display:inline">
                  @csrf
                  <button type="submit" class="rl-btn 
                    @if($isBelum) rl-btn-alert 
                    @elseif($isRunning) rl-btn-running 
                    @else rl-btn-accent @endif">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    @if($isBelum) Isi Sekarang @elseif($isRunning) Lanjutkan @else Detail @endif
                  </button>
                </form>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      {{-- TOGGLE DONE SECTION --}}
      @if($hasDone)
        <div class="rl-done-toggle-wrap">
          <button type="button" class="rl-done-toggle" data-toggle="done-{{ $shift['key'] }}-{{ $location }}">
            <svg class="rl-toggle-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
            <span class="rl-toggle-label">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              Selesai ({{ $doneItems->count() }})
            </span>
          </button>
          <div class="rl-done-body" id="done-{{ $shift['key'] }}-{{ $location }}" style="display: none;">
            @foreach($doneItems as $it)
              @php
                $hf         = $it->hangingForm;
                $dead       = $hf ? (int)($hf->dead_count ?? 0) : 0;
                $returCount = (int)($hf?->retur_count ?? 0);
                $returKg    = (float)($hf?->retur_total_kg ?? 0);
              @endphp

              <div class="rl-row rl-row-done">
                <div class="rl-stripe rl-stripe-done"></div>

                <div class="rl-truck">
                  <div class="rl-truck-num">#{{ $it->truck_no ?? '–' }}</div>
                  <div class="rl-state-badge rsb-done">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Done
                  </div>
                </div>

                <div class="rl-info">
                  <div class="rl-top">
                    <code class="rl-code">{{ $it->report_code }}</code>
                    <span class="rl-mc-pill rl-mc-done">{{ $it->status }}</span>
                    <span class="rl-hf-pill rhf-done">Hanging: done</span>
                  </div>
                  <div class="rl-mid">
                    <span class="rl-meta-item">
                      <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                      </svg>
                      {{ $it->process_date?->format('d/m/Y') }}
                    </span>
                    <span class="rl-dot">·</span>
                    <span class="rl-shift-pill">{{ strtoupper($it->shift) }}</span>
                    <span class="rl-dot">·</span>
                    <span class="rl-meta-item">{{ $it->farm?->name ?? '–' }}</span>
                    <span class="rl-dot">·</span>
                    <span class="rl-meta-item">{{ $it->expedition?->name ?? '–' }}</span>
                    <span class="rl-dot">·</span>
                    <code class="rl-plate">{{ $it->plateNumber?->plate_number ?? '–' }}</code>
                  </div>

                  <div class="rl-chips">
                    <span class="rl-chip rl-chip-mati">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      Mati <strong>{{ $dead }}</strong>
                    </span>
                    <span class="rl-chip rl-chip-retur">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/></svg>
                      Retur <strong>{{ $returCount }}</strong> ekor
                    </span>
                    <span class="rl-chip rl-chip-kg">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2v20M18 2v20M2 12h20"/></svg>
                      <strong>{{ number_format($returKg, 2) }}</strong> Kg
                    </span>
                  </div>
                </div>

                <div class="rl-actions">
                  <form method="POST" action="{{ route('retur-mati.open', $it) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="rl-btn rl-btn-ghost">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      Lihat Detail
                    </button>
                  </form>
                  <span class="rl-done-pill">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    DONE
                  </span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    @if(!$loop->last)<div style="height:14px"></div>@endif
  @endforeach
</div>

<style>
.rl-wrap { display:flex; flex-direction:column; }

/* SHIFT CARD */
.rl-shift-card { background:#fff; border:1px solid #E2E5EE; border-radius:14px; box-shadow:0 1px 4px rgba(0,0,0,.04),0 6px 18px rgba(0,0,0,.04); overflow:hidden; }
.rl-shift-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 18px; border-bottom:1px solid #E2E5EE; flex-wrap:wrap; }
.rl-shift-pagi  .rl-shift-head { background:rgba(245,159,0,.04); }
.rl-shift-malam .rl-shift-head { background:rgba(79,103,255,.04); }

.rl-shift-label { display:inline-flex; align-items:center; gap:8px; padding:5px 13px; border-radius:8px; font-size:.8rem; font-weight:800; letter-spacing:.03em; }
.rl-shift-pagi  .rl-shift-label { background:rgba(245,159,0,.14); color:#92400E; }
.rl-shift-malam .rl-shift-label { background:rgba(79,103,255,.12); color:#3730A3; }

.rl-shift-stats { display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
.rl-stat { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; font-size:.74rem; font-weight:700; border:1px solid transparent; }
.rl-stat-total   { background:#F0F2F7; color:#6B7896; }
.rl-stat-running { background:rgba(79,103,255,.12); color:#3730A3; border-color:rgba(79,103,255,.2); }
.rl-stat-done    { background:rgba(16,185,129,.10); color:#065F46; border-color:rgba(16,185,129,.2); }
.rl-stat-warn    { background:rgba(245,159,0,.12); color:#92400E; border-color:rgba(245,159,0,.25); }

/* PROGRESS */
.rl-prog-wrap { display:flex; align-items:center; gap:10px; padding:8px 18px; border-bottom:1px solid #E2E5EE; background:#FAFBFD; }
.rl-prog-track { flex:1; height:5px; border-radius:999px; background:#EEF0F4; overflow:hidden; display:flex; }
.rl-prog-running { height:100%; background:#7C3AED; transition:width .4s; }
.rl-prog-partial { height:100%; background:#F59F00; transition:width .4s; }
.rl-prog-done    { height:100%; background:#10B981; transition:width .4s; }
.rl-prog-txt { font-size:.72rem; font-weight:800; color:#6B7896; white-space:nowrap; }

/* ACTIVE BODY */
.rl-body { padding:10px 12px; display:flex; flex-direction:column; gap:7px; }
.rl-active-section { border-bottom:1px solid #E2E5EE; }

/* TOGGLE DONE SECTION */
.rl-done-toggle-wrap { border-top:1px solid #E2E5EE; background:#FAFBFD; }
.rl-done-toggle {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 12px 18px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-weight: 700;
  font-size: .8rem;
  color: #065F46;
  transition: background .15s;
  text-align: left;
}
.rl-done-toggle:hover { background: rgba(16,185,129,.08); }
.rl-toggle-icon { transition: transform .2s ease; }
.rl-done-toggle[aria-expanded="true"] .rl-toggle-icon { transform: rotate(180deg); }
.rl-toggle-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  background: rgba(16,185,129,.12);
  border-radius: 20px;
  color: #065F46;
}
.rl-done-body {
  padding: 8px 12px 12px 12px;
  display: flex;
  flex-direction: column;
  gap: 7px;
  border-top: 1px solid #E2E5EE;
  background: #FFFFFF;
}

/* ROW */
.rl-row { display:flex; align-items:center; gap:12px; padding:12px 14px 12px 12px; border:1.5px solid #E8EBF2; border-radius:12px; background:#fff; position:relative; overflow:hidden; transition:all .15s; }
.rl-row:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(0,0,0,.07); }
.rl-row-running { border-color:rgba(124,58,237,.4); background:rgba(124,58,237,.04); }
.rl-row-running:hover { border-color:rgba(124,58,237,.6); box-shadow:0 4px 14px rgba(124,58,237,.12); }
.rl-row-belum   { border-color:rgba(245,159,0,.4); background:rgba(255,251,235,.5); }
.rl-row-belum:hover { border-color:rgba(245,159,0,.6); box-shadow:0 4px 14px rgba(245,159,0,.12); }
.rl-row-done    { border-color:rgba(16,185,129,.25); background:rgba(240,253,249,.5); opacity:.85; }
.rl-row-partial { border-color:var(--t-bd); background:var(--t-light); }

/* stripe */
.rl-stripe { position:absolute; left:0; top:0; bottom:0; width:4px; }
.rl-stripe-running { background:#7C3AED; }
.rl-stripe-belum  { background:#F59F00; }
.rl-stripe-done   { background:#10B981; }
.rl-stripe-accent { background:var(--t,#E85D2F); }

/* truck */
.rl-truck { flex-shrink:0; display:flex; flex-direction:column; align-items:center; gap:5px; min-width:48px; }
.rl-truck-num { font-size:.78rem; font-weight:900; padding:3px 9px; border-radius:7px; text-align:center; white-space:nowrap; }
.rl-row-running .rl-truck-num { color:#3730A3; background:rgba(124,58,237,.12); border:1px solid rgba(124,58,237,.3); }
.rl-row-belum   .rl-truck-num { color:#92400E; background:rgba(245,159,0,.14); border:1px solid rgba(245,159,0,.3); }
.rl-row-done    .rl-truck-num { color:#065F46; background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.22); }
.rl-row-partial .rl-truck-num { color:var(--t); background:var(--t-xl); border:1px solid var(--t-bd); }

.rl-state-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:999px; font-size:.62rem; font-weight:900; letter-spacing:.03em; white-space:nowrap; }
.rsb-running { background:rgba(124,58,237,.12); color:#3730A3; }
.rsb-done    { background:rgba(16,185,129,.12); color:#065F46; }
.rsb-belum   { background:rgba(245,159,0,.14); color:#92400E; animation:rsb-pulse 2s ease infinite; }
.rsb-partial { background:var(--t-xl); color:var(--t); }
@keyframes rsb-pulse { 0%,100%{opacity:1} 50%{opacity:.6} }

/* info */
.rl-info { flex:1; min-width:0; }
.rl-top { display:flex; align-items:center; gap:7px; flex-wrap:wrap; margin-bottom:5px; }
.rl-code { font-family:'Fira Code','Courier New',monospace; font-size:.75rem; background:#F3F4F8; color:#4B5563; padding:2px 8px; border-radius:6px; }
.rl-mc-pill { display:inline-flex; padding:2px 9px; border-radius:999px; font-size:.68rem; font-weight:900; letter-spacing:.04em; text-transform:lowercase; border:1px solid transparent; }
.rl-mc-draft   { background:#F3F4F8; color:#4B5563; border-color:rgba(75,85,99,.15); }
.rl-mc-running { background:rgba(245,159,0,.12); color:#92400E; border-color:rgba(245,159,0,.3); }
.rl-mc-done    { background:rgba(16,185,129,.12); color:#065F46; border-color:rgba(16,185,129,.25); }
.rl-hf-pill { display:inline-flex; padding:2px 9px; border-radius:999px; font-size:.68rem; font-weight:900; letter-spacing:.03em; border:1px solid transparent; }
.rhf-done  { background:#0D1117; color:#fff; }
.rhf-run   { background:rgba(124,58,237,.12); color:#3730A3; border-color:rgba(124,58,237,.3); }
.rhf-other { background:#F3F4F8; color:#6B7896; }

.rl-mid { display:flex; align-items:center; gap:5px; flex-wrap:wrap; font-size:.76rem; color:#6B7896; font-weight:600; }
.rl-meta-item { display:inline-flex; align-items:center; gap:3px; }
.rl-dot   { color:#D1D5E0; }
.rl-shift-pill { padding:2px 8px; border-radius:6px; background:#F0F2F7; color:#4B5563; font-size:.7rem; font-weight:900; }
.rl-plate { font-family:'Fira Code','Courier New',monospace; font-size:.73rem; color:#6B7896; }

/* unread banner */
.rl-unread { display:inline-flex; align-items:center; gap:7px; margin-top:7px; padding:6px 12px; border-radius:8px; background:rgba(245,159,0,.09); border:1px dashed rgba(245,159,0,.45); color:#92400E; font-size:.76rem; font-weight:700; }

/* chips */
.rl-chips { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-top:7px; }
.rl-chip { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:8px; font-size:.75rem; font-weight:700; border:1px solid transparent; }
.rl-chip-mati   { background:rgba(239,68,68,.07); color:#991B1B; border-color:rgba(239,68,68,.18); }
.rl-chip-retur  { background:var(--t-xl); color:var(--t); border-color:var(--t-bd); }
.rl-chip-kg     { background:#F0F2F7; color:#4B5563; border-color:#E2E5EE; }

/* actions */
.rl-actions { display:flex; align-items:center; gap:7px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }
.rl-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:9px; font-size:.8rem; font-weight:700; cursor:pointer; transition:all .15s; }
.rl-btn-ghost  { border:1.5px solid #E2E5EE; background:#fff; color:#0D1117; }
.rl-btn-ghost:hover  { background:#F0F2F7; border-color:#C5CAD8; }
.rl-btn-running { border:1.5px solid rgba(124,58,237,.4); background:rgba(124,58,237,.1); color:#3730A3; }
.rl-btn-running:hover { background:rgba(124,58,237,.2); border-color:rgba(124,58,237,.6); }
.rl-btn-accent { border:1.5px solid var(--t-bd); background:var(--t-xl); color:var(--t); }
.rl-btn-accent:hover { filter:brightness(.94); }
.rl-btn-alert  { border:1.5px solid rgba(245,159,0,.4); background:rgba(245,159,0,.1); color:#92400E; animation:btn-pulse 2.2s ease infinite; }
.rl-btn-alert:hover  { background:rgba(245,159,0,.2); border-color:rgba(245,159,0,.6); }
@keyframes btn-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(245,159,0,.3)} 50%{box-shadow:0 0 0 5px rgba(245,159,0,0)} }

.rl-done-pill { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:999px; background:rgba(16,185,129,.1); color:#065F46; font-size:.72rem; font-weight:900; border:1px solid rgba(16,185,129,.22); }

/* empty */
.rl-empty { display:flex; align-items:center; gap:8px; padding:20px 12px; color:#9CA3AF; font-size:.82rem; font-weight:700; }
.rl-empty svg { opacity:.3; flex-shrink:0; }

@media (max-width:680px) {
  .rl-row { flex-wrap:wrap; }
  .rl-truck { flex-direction:row; min-width:unset; }
  .rl-actions { width:100%; justify-content:flex-start; }
}
</style>

<script>
(function() {
  // Inisialisasi semua toggle button
  const toggles = document.querySelectorAll('.rl-done-toggle');

  toggles.forEach(btn => {
    const targetId = btn.dataset.toggle;
    const target = document.getElementById(targetId);

    if (target) {
      // Set initial state (default collapsed)
      target.style.display = 'none';
      btn.setAttribute('aria-expanded', 'false');

      // Cek localStorage untuk remember state
      const savedState = localStorage.getItem(`toggle_${targetId}`);
      if (savedState === 'open') {
        target.style.display = 'flex';
        btn.setAttribute('aria-expanded', 'true');
      }

      btn.addEventListener('click', () => {
        const isOpen = btn.getAttribute('aria-expanded') === 'true';
        if (isOpen) {
          target.style.display = 'none';
          btn.setAttribute('aria-expanded', 'false');
          localStorage.setItem(`toggle_${targetId}`, 'closed');
        } else {
          target.style.display = 'flex';
          btn.setAttribute('aria-expanded', 'true');
          localStorage.setItem(`toggle_${targetId}`, 'open');
        }
      });
    }
  });
})();
</script>