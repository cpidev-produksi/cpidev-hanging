@extends('layouts.app')

@section('content')
<div class="sh-wrap">

  {{-- ── BREADCRUMB ── --}}
  <nav class="sh-breadcrumb">
    <a href="{{ route('hanging.landing') }}" class="sh-bc-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/>
        <path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>
      </svg>
      Form Hanging Ayam
    </a>
    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/>
    </svg>
    <span>Detail</span>
  </nav>

  @php
  $slug = auth()->user()?->role?->slug;
  $canEditDone = in_array($slug, ['supervisor','superadmin'], true);
  $isLocked = ($form->status === 'done') && !$canEditDone;

    $mc       = $form->monitorControl;
    $isDone   = $form->status === 'done';
    $isDraft  = $form->status === 'draft';
    $setCount = (int) $mc->set_count;

    $customCaps = [
      // 'SH01' => [17 => 46],
      'SH02' => [30 => 13],
    ];
    $location = $mc->location ?? '';
  @endphp

  {{-- ── PAGE HEADER ── --}}
  <div class="sh-header">
    <div class="sh-header-left">
      <div class="sh-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/>
          <path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>
        </svg>
      </div>
      <div>
        <div class="sh-title-row">
          <h1 class="sh-title">Form Hanging Ayam</h1>
          <span class="sh-status sh-status-{{ $form->status }}">{{ strtoupper($form->status) }}</span>
        </div>
        <p class="sh-subtitle">
          <span class="sh-chip">{{ $mc->report_code }}</span>
          <span class="sh-sep">·</span>
          <span>{{ $mc->location }}</span>
          <span class="sh-sep">·</span>
          <span>Truk <strong>#{{ $mc->truck_no }}</strong></span>
        </p>
      </div>
    </div>

    <div class="sh-header-actions">
      <a href="{{ route('hanging.landing') }}" class="sh-btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/>
          <polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
      </a>
      @if($isDraft)
        <form method="POST" action="{{ route('hanging.start', $form) }}" style="display:inline">
          @csrf
          <button type="submit" class="sh-btn-start"
                  onclick="return confirm('Mulai proses hanging untuk truk ini?')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.2"><polygon points="6 3 20 12 6 21 6 3"/>
            </svg>
            Mulai
          </button>
        </form>
      @endif
    </div>
  </div>

  {{-- ── TOP SECTION: finish + info + summary ── --}}
  <div class="sh-top-grid">

    {{-- Finish Panel --}}
    <div class="sh-card sh-finish-panel">
      <div class="sh-card-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/>
          <path d="M12 6v6l4 2"/>
        </svg>
        Penyelesaian Proses
      </div>

      @if($isDone || $isLocked)
        <div class="sh-done-banner">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
            <polyline points="9 12 12 15 16 9"/>
          </svg>
          Form sudah <strong>DONE</strong> dan tidak dapat diubah lagi.
        </div>
      @endif

      <form method="POST" action="{{ route('hanging-forms.finish', $form) }}" class="sh-finish-form">
        @csrf
        <div class="sh-finish-grid">
          <div class="sh-form-group">
            <label class="sh-label" for="unloading_time">Jam Bongkar</label>
            <div class="sh-input-wrap @error('unloading_time') sh-has-error @enderror">
              <span class="sh-input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/>
                  <path d="M12 7v6l3 2"/>
                </svg>
              </span>
              <input type="time" id="unloading_time" name="unloading_time"
                     value="{{ old('unloading_time', $form->unloading_time?->format('H:i')) }}"
                     class="sh-input" @disabled($isLocked)>
            </div>
            @error('unloading_time')<p class="sh-error">{{ $message }}</p>@enderror
          </div>

          <div class="sh-form-group">
            <label class="sh-label" for="finish_time">Jam Selesai</label>
            <div class="sh-input-wrap @error('finish_time') sh-has-error @enderror">
              <span class="sh-input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/>
                  <path d="M12 7v6l3 2"/>
                </svg>
              </span>
              <input type="time" id="finish_time" name="finish_time"
                     value="{{ old('finish_time', $form->finish_time?->format('H:i')) }}"
                     class="sh-input" @disabled($isLocked)>
            </div>
            @error('finish_time')<p class="sh-error">{{ $message }}</p>@enderror
          </div>
        </div>

        <div class="sh-finish-footer">
          <button type="submit" class="sh-btn-finish" @disabled($isLocked)
                  onclick="return confirm('Selesaikan proses ini? Status akan menjadi DONE.')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/>
            </svg>
            Selesai
          </button>
        </div>
      </form>
    </div>

    {{-- Info Card --}}
    <div class="sh-card">
      <div class="sh-card-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Informasi Proses
      </div>
      <div class="sh-kv-grid">
        <div class="sh-kv"><div class="sh-k">Lokasi</div><div class="sh-v">{{ $mc->location }}</div></div>
        <div class="sh-kv"><div class="sh-k">Tanggal</div><div class="sh-v">{{ $mc->process_date?->format('d/m/Y') }}</div></div>
        <div class="sh-kv"><div class="sh-k">Shift</div><div class="sh-v sh-upper">{{ $mc->shift }}</div></div>
        <div class="sh-kv-divider"></div>
        <div class="sh-kv"><div class="sh-k">No Urut Truk</div><div class="sh-v">#{{ $mc->truck_no }}</div></div>
        <div class="sh-kv"><div class="sh-k">Ekspedisi</div><div class="sh-v">{{ $mc->expedition?->name ?? '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">No Polisi</div><div class="sh-v sh-mono">{{ $mc->plateNumber?->plate_number ?? '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">Farm</div><div class="sh-v">{{ $mc->farm?->name ?? '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">Size</div><div class="sh-v">{{ $mc->size ?? '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">No Segel</div><div class="sh-v sh-mono">{{ $mc->seal_no ?? '—' }}</div></div>
        <div class="sh-kv-divider"></div>
        <div class="sh-kv"><div class="sh-k">Jam Truk Datang</div><div class="sh-v">{{ $mc->truck_arrival_time ? date('H:i', strtotime($mc->truck_arrival_time)) : '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">Tgl Tangkap</div><div class="sh-v">{{ $mc->catch_date?->format('d/m/Y') ?? '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">SPPA</div><div class="sh-v">{{ $mc->sppa_no ?? '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">Order ID</div><div class="sh-v sh-mono">{{ $mc->order_id ?? '—' }}</div></div>
        <div class="sh-kv"><div class="sh-k">Tanggal SPPA</div><div class="sh-v">{{ $mc->sppa_date?->format('d/m/Y') ?? '—' }}</div></div>
        <div class="sh-kv sh-kv-full">
          <div class="sh-k">Total</div>
          <div class="sh-v">{{ $mc->total_chicken ?? 0 }} ekor · {{ number_format((float)($mc->total_kilo ?? 0), 2) }} kg · ABW {{ number_format((float)($mc->abw ?? 0), 2) }}</div>
        </div>
      </div>
    </div>

    {{-- Summary Card --}}
    <div class="sh-card sh-summary-card">
      <div class="sh-card-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"><path d="M3 3v18h18"/>
          <path d="M7 14l3-3 4 4 6-7"/>
        </svg>
        Ringkasan
      </div>

      <div class="sh-summary-list"
           data-dead="{{ (int)($form->dead_count ?? 0) }}"
           data-retur="{{ (int)($form->retur_count ?? 0) }}"
           data-total-chicken="{{ (int)($totalChicken ?? 0) }}">
        <div class="sh-summary-row">
          <span class="sh-summary-key">Total Shackle Kosong</span>
          <span class="sh-summary-val">
            <span id="total-kosong" class="sh-summary-num">{{ $totalKosong }}</span>
            <span class="sh-summary-unit">Pcs</span>
          </span>
        </div>
        <div class="sh-summary-row">
          <span class="sh-summary-key">Jumlah Ayam Mati</span>
          <span class="sh-summary-val">
            <span class="sh-summary-num">{{ (int)($form->dead_count ?? 0) }}</span>
            <span class="sh-summary-unit">Ekor</span>
          </span>
        </div>
        <div class="sh-summary-row">
          <span class="sh-summary-key">Jumlah Ayam Retur</span>
          <span class="sh-summary-val">
            <span class="sh-summary-num">{{ (int)($form->retur_count ?? 0) }}</span>
            <span class="sh-summary-unit">Ekor</span>
        </div>
        <div class="sh-summary-row">
          <span class="sh-summary-key">Total berat retur</span>
          <span class="sh-summary-val">
            <span class="sh-summary-num">{{ number_format((float)($form->retur_total_kg ?? 0), 2) }}</span>
            <span class="sh-summary-unit">Kg</span>
          </span>
        </div>
        <div class="sh-summary-row sh-summary-highlight">
          <span class="sh-summary-key">Jumlah Ayam Diterima</span>
          <span class="sh-summary-val">
            <span id="total-ayam" class="sh-summary-num">{{ $totalAyam }}</span>
            <span class="sh-summary-unit">Ekor</span>
          </span>
        </div>
        <div class="sh-summary-row">
          <span class="sh-summary-key">Jumlah Blok Terisi Penuh</span>
          <span class="sh-summary-val">
            <span id="blok-penuh" class="sh-summary-num">{{ $fullBlockCount }}</span>
            <span class="sh-summary-unit">Blok</span>
          </span>
        </div>
        <div class="sh-summary-row">
          <span class="sh-summary-key">Selisih</span>
          <span class="sh-summary-val">
            <span id="selisih-ayam" class="sh-summary-num">{{ $selisihAyam }}</span>
            <span class="sh-summary-unit">Ekor</span>
          </span>
        </div>
      </div>
    </div>
  </div>

  {{-- ── TABLE CARD ── --}}
  <div class="sh-card sh-table-card">
    <div class="sh-card-label sh-card-label-pad">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2"/>
        <path d="M3 9h18M9 21V9"/>
      </svg>
      Data Hanging
    </div>

    <div class="sh-table-wrap">
      <table class="sh-table">
        <thead>
          <tr>
            <th class="sh-th-no" rowspan="2">No</th>
            <th class="sh-th-blok" rowspan="2">BLOK</th>
            <th class="sh-th-rule" rowspan="2">Jml Shackle</th>
            @for($s=1;$s<=$setCount;$s++)
              <th class="sh-th-set" colspan="2">KOLOM {{ $s }}</th>
            @endfor
          </tr>
          <tr>
            @for($s=1;$s<=$setCount;$s++)
              <th class="sh-th-sub">KOSONG</th>
              <th class="sh-th-sub">AYAM</th>
            @endfor
          </tr>
        </thead>

        <tbody>
          @php
            $lastLineNo = (int) ($form->lines->max('line_no') ?? 0);
            $lastSetNo = (int) $setCount;
          @endphp

          @foreach($form->lines as $line)
            @php
              $maxCap = $customCaps[$location][$line->line_no] ?? 50;
            @endphp
            <tr class="sh-tr">
              <td class="sh-td sh-td-center sh-td-no">{{ $line->line_no }}</td>
              <td class="sh-td sh-td-blok">
                <span class="sh-blok-label">{{ $line->shackle_label }}</span>
              </td>
              <td class="sh-td sh-td-center">
                <span class="sh-rule">{{ $line->rule_min }}–{{ $line->rule_max }}</span>
              </td>

              @for($s=1;$s<=$setCount;$s++)
                @php
                  $cell     = $line->sets->firstWhere('set_no',$s);
                  $emptyRaw = $cell?->empty_count;
                  $empty    = is_null($emptyRaw) ? null : (int) $emptyRaw;
                  $ayam     = is_null($empty) ? 0 : ($maxCap - $empty);
                  $isLastSet = ((int) $line->line_no === $lastLineNo) && ((int) $s === $lastSetNo);
                @endphp

                <td class="sh-td sh-td-center sh-td-ctrl">
                  <div class="sh-counter">
                    <button type="button" class="sh-ctr-btn sh-ctr-minus"
                            @disabled($isLocked)
                            onclick="changeEmpty({{ $cell->id }}, -1)">−</button>

                    <input  id="empty-{{ $cell->id }}"
                            value="{{ is_null($empty) ? '' : $empty }}"
                            class="sh-ctr-input"
                            inputmode="numeric"
                            data-max="{{ $maxCap }}"
                            @disabled($isLocked)
                            onchange="updateCell({{ $cell->id }}, this.value)"/>

                    <button type="button" class="sh-ctr-btn sh-ctr-plus"
                            @disabled($isLocked)
                            onclick="changeEmpty({{ $cell->id }}, 1)">+</button>
                  </div>
                </td>

                <td class="sh-td sh-td-center">
                  <span id="ayam-{{ $cell->id }}" class="sh-ayam-val"
                        data-empty="{{ is_null($empty) ? '' : $empty }}"
                        data-cap="{{ $maxCap }}"
                        data-last-set="{{ $isLastSet ? '1' : '0' }}">{{ $ayam }}</span>
                </td>
              @endfor
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function refreshTotals() {
  let totalKosong = 0;
  let totalAyamShackle = 0;
  let blokPenuh = 0;

  document.querySelectorAll('.sh-ayam-val').forEach(el => {
    const s = el.getAttribute('data-empty');
    const cap = parseInt(el.getAttribute('data-cap') || '50', 10);
    const isLastSet = el.getAttribute('data-last-set') === '1';

    if (isLastSet) {
      blokPenuh++;
    }

    if (!s && s !== '0') return;

    const e = parseInt(s, 10);
    if (isNaN(e) || isNaN(cap)) return;

    totalKosong += e;
    totalAyamShackle += (cap - e);

    if (cap === 50 && e === 0 && !isLastSet) {
      blokPenuh++;
    }
  });

  const summaryEl = document.querySelector('.sh-summary-list');
  const dead  = parseInt(summaryEl?.getAttribute('data-dead') || '0', 10);
  const retur = parseInt(summaryEl?.getAttribute('data-retur') || '0', 10);
  const totalMC = parseInt(summaryEl?.getAttribute('data-total-chicken') || '0', 10);

  const targetMC = totalMC - dead - retur;
  const selisih  = targetMC - totalAyamShackle;

  const ke = document.getElementById('total-kosong');
  const ae = document.getElementById('total-ayam');
  const se = document.getElementById('selisih-ayam');
  const bp = document.getElementById('blok-penuh');

  if (ke) ke.textContent = totalKosong;
  if (ae) ae.textContent = totalAyamShackle;
  if (se) se.textContent = selisih;
  if (bp) bp.textContent = blokPenuh;

  // highlight sel kosong
  document.querySelectorAll('.sh-td-ctrl').forEach(td => {
    const input = td.querySelector('.sh-ctr-input');
    if (!input) return;
    const val = input.value === '' ? 0 : parseInt(input.value, 10);
    td.classList.toggle('sh-has-empty', !isNaN(val) && val > 0);
  });
}

function changeEmpty(id, delta) {
  const inputEl = document.getElementById(`empty-${id}`);
  const max = parseInt(inputEl.dataset.max || '50', 10);

  let n = (inputEl.value === '' ? 0 : parseInt(inputEl.value, 10));
  if (isNaN(n)) n = 0;

  let next = n + delta;
  if (next < 0) next = 0;
  if (next > max) next = max;

  inputEl.value = String(next);
  updateCell(id, next);
}

async function updateCell(id, emptyCount) {
  const inputEl = document.getElementById(`empty-${id}`);
  const max = parseInt(inputEl?.dataset.max || '50', 10);

  let v = emptyCount;
  if (v === '' || v === null || v === undefined) v = null;
  if (v !== null) {
    v = parseInt(v, 10);
    if (isNaN(v)) v = 0;
    if (v < 0) v = 0;
    if (v > max) v = max;
  }

  const res = await fetch(`{{ url('/hanging-cells') }}/${id}`, {
    method: 'PATCH',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ empty_count: v })
  });

  let json = null;
  try { json = await res.json(); } catch (e) {}

  if (!res.ok) {
    alert(`Gagal update (${res.status}). ` + (json?.message || ''));
    return;
  }

  const ayamEl  = document.getElementById(`ayam-${id}`);
  const maxCap  = json?.max ?? max;

  inputEl.value          = json.empty_count === null ? '' : json.empty_count;
  ayamEl.textContent     = json.ayam;
  ayamEl.setAttribute('data-empty', json.empty_count === null ? '' : String(json.empty_count));
  ayamEl.setAttribute('data-cap', String(maxCap));

  ayamEl.classList.add('sh-flash');
  setTimeout(() => ayamEl.classList.remove('sh-flash'), 400);

  refreshTotals();
}

document.addEventListener('DOMContentLoaded', refreshTotals);
</script>

<style>
/* ── TOKENS ── */
:root {
  --sh-bg:        #F0F2F7;
  --sh-surface:   #FFFFFF;
  --sh-border:    #E2E5EE;
  --sh-text:      #0D1117;
  --sh-muted:     #6B7896;
  --sh-accent:    #E85D2F;
  --sh-accent-hv: #D04A1E;
  --sh-accent-xl: rgba(232,93,47,.08);
  --sh-success:   #10B981;
  --sh-warning:   #F59F00;
  --sh-error:     #EF4444;
  --sh-r:         14px;
  --sh-shadow:    0 1px 4px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);
}

/* ── LAYOUT ── */
.sh-wrap { max-width: 1320px; margin: 0 auto; padding: 32px 24px; }

/* ── BREADCRUMB ── */
.sh-breadcrumb {
  display: flex; align-items: center; gap: 7px;
  font-size: .78rem; color: var(--sh-muted);
  margin-bottom: 22px;
}
.sh-bc-link {
  display: inline-flex; align-items: center; gap: 5px;
  color: var(--sh-accent); text-decoration: none;
  font-weight: 600;
  transition: opacity .15s;
}
.sh-bc-link:hover { opacity: .75; }
.sh-breadcrumb > svg { color: #C5CAD8; }

/* ── HEADER ── */
.sh-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
}
.sh-header-left { display: flex; align-items: center; gap: 16px; }
.sh-icon {
  width: 52px; height: 52px; flex-shrink: 0;
  background: var(--sh-accent-xl); color: var(--sh-accent);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
}
.sh-title-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 5px; }
.sh-title { font-size: 1.45rem; font-weight: 800; color: var(--sh-text); margin: 0; letter-spacing: -.01em; }

.sh-status {
  padding: 4px 12px; border-radius: 999px;
  font-size: .68rem; font-weight: 900; letter-spacing: .08em;
  border: 1.5px solid transparent;
}
.sh-status-draft   { background: #F3F4F8; color: #4B5563; border-color: rgba(75,85,99,.2); }
.sh-status-running { background: rgba(245,159,0,.12); color: #92400E; border-color: rgba(245,159,0,.3); }
.sh-status-done    { background: rgba(16,185,129,.12); color: #065F46; border-color: rgba(16,185,129,.25); }

.sh-subtitle { margin: 0; font-size: .82rem; color: var(--sh-muted); font-weight: 600;
               display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
.sh-chip {
  font-family: 'Fira Code', 'Courier New', monospace;
  background: #F3F4F8; color: #4B5563;
  padding: 2px 10px; border-radius: 7px; font-size: .76rem;
}
.sh-sep { color: #C5CAD8; }

.sh-header-actions { display: flex; align-items: center; gap: 10px; }
.sh-btn-back {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 16px;
  border: 1.5px solid var(--sh-border); border-radius: 10px;
  background: #fff; color: var(--sh-muted);
  text-decoration: none; font-size: .84rem; font-weight: 600;
  transition: all .15s;
}
.sh-btn-back:hover { border-color: #C5CAD8; color: var(--sh-text); }

.sh-btn-start {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 18px;
  border: none; border-radius: 10px;
  background: var(--sh-accent); color: #fff;
  font-size: .84rem; font-weight: 700;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(232,93,47,.3);
  transition: all .18s;
}
.sh-btn-start:hover { background: var(--sh-accent-hv); transform: translateY(-1px); }

/* ── CARD BASE ── */
.sh-card {
  background: var(--sh-surface);
  border: 1px solid var(--sh-border);
  border-radius: var(--sh-r);
  box-shadow: var(--sh-shadow);
}
.sh-card-label {
  display: flex; align-items: center; gap: 7px;
  font-size: .7rem; font-weight: 800; letter-spacing: .09em; text-transform: uppercase;
  color: var(--sh-accent);
  padding: 14px 18px; border-bottom: 1px solid var(--sh-border);
}
.sh-card-label-pad { padding: 14px 18px; border-bottom: 1px solid var(--sh-border); }

/* ── TOP GRID ── */
.sh-top-grid {
  display: grid;
  grid-template-columns: 1fr 1.5fr 0.6fr;
  gap: 14px;
  margin-bottom: 14px;
}
@media (max-width: 1100px) { .sh-top-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 720px)  { .sh-top-grid { grid-template-columns: 1fr; } }

/* ── FINISH PANEL ── */
.sh-finish-panel { display: flex; flex-direction: column; }
.sh-finish-form  { padding: 16px 18px; flex: 1; display: flex; flex-direction: column; gap: 14px; }
.sh-finish-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 520px) { .sh-finish-grid { grid-template-columns: 1fr; } }

.sh-done-banner {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 18px;
  background: rgba(16,185,129,.08);
  border-bottom: 1px solid rgba(16,185,129,.2);
  color: #065F46; font-size: .82rem; font-weight: 700;
}
.sh-done-banner svg { flex-shrink: 0; }

.sh-form-group  {}
.sh-label { display: block; font-size: .8rem; font-weight: 700; color: var(--sh-text); margin-bottom: 7px; }
.sh-input-wrap {
  display: flex; align-items: center;
  border: 1.5px solid var(--sh-border);
  border-radius: 10px; background: #FAFBFD;
  transition: border-color .18s, box-shadow .18s;
  overflow: hidden;
}
.sh-input-wrap:focus-within { border-color: var(--sh-accent); box-shadow: 0 0 0 3px rgba(232,93,47,.12); background: #fff; }
.sh-input-wrap.sh-has-error { border-color: var(--sh-error); background: rgba(239,68,68,.04); }
.sh-input-icon { width: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; color: var(--sh-muted); }
.sh-input {
  flex: 1; border: none; outline: none; background: transparent;
  padding: 10px 12px 10px 0; font-size: .875rem; color: var(--sh-text);
}
.sh-error { color: var(--sh-error); font-size: .76rem; font-weight: 600; margin-top: 5px; }

.sh-finish-footer { display: flex; justify-content: flex-end; margin-top: auto; }
.sh-btn-finish {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 20px;
  border: none; border-radius: 10px;
  background: var(--sh-success); color: #fff;
  font-size: .84rem; font-weight: 700;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(16,185,129,.3);
  transition: all .18s;
}
.sh-btn-finish:hover:not(:disabled) { filter: brightness(.93); transform: translateY(-1px); }
.sh-btn-finish:disabled { opacity: .5; cursor: not-allowed; }

/* ── KV GRID ── */
.sh-kv-grid {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 12px; padding: 16px 18px;
}
@media (max-width: 720px) { .sh-kv-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 460px) { .sh-kv-grid { grid-template-columns: 1fr; } }
.sh-kv {}
.sh-kv-full { grid-column: 1 / -1; }
.sh-kv-divider { grid-column: 1 / -1; height: 1px; background: var(--sh-border); }
.sh-k { font-size: .72rem; color: var(--sh-muted); margin-bottom: 3px; font-weight: 600; }
.sh-v { font-weight: 700; color: var(--sh-text); font-size: .875rem; }
.sh-mono { font-family: 'Fira Code','Courier New',monospace; font-size: .82rem; }
.sh-upper { text-transform: uppercase; letter-spacing: .04em; }

/* ── SUMMARY ── */
.sh-summary-list { padding: 14px 18px; display: flex; flex-direction: column; gap: 10px; }
.sh-summary-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 12px; border-radius: 10px;
  background: #FAFBFD; border: 1px solid var(--sh-border);
}
.sh-summary-highlight {
  background: var(--sh-accent-xl); border-color: rgba(232,93,47,.2);
}
.sh-summary-key  { font-size: .78rem; color: var(--sh-muted); font-weight: 600; }
.sh-summary-val  { display: flex; align-items: baseline; gap: 4px; }
.sh-summary-num  { font-size: 1.1rem; font-weight: 900; color: var(--sh-text); }
.sh-summary-highlight .sh-summary-num { color: var(--sh-accent); }
.sh-summary-unit { font-size: .75rem; color: var(--sh-muted); font-weight: 600; }

/* ── TABLE CARD ── */
.sh-table-card { overflow: hidden; }
.sh-table-wrap { overflow-x: auto; }
.sh-table {
  width: 100%; border-collapse: collapse;
  font-size: .875rem;
  min-width: 1100px;
  table-layout: fixed;
}

/* Column widths */
.sh-th-no   { width: 54px; }
.sh-th-blok { width: 150px; }
.sh-th-rule { width: 110px; }
.sh-th-set  { min-width: 160px; }
.sh-th-sub  { min-width: 80px; }

.sh-table thead tr:first-child { background: #FAFBFD; border-bottom: 1px solid var(--sh-border); }
.sh-table thead tr:last-child  { background: #F5F7FB; border-bottom: 1px solid var(--sh-border); }
.sh-table th {
  padding: 10px 12px;
  color: var(--sh-muted); font-size: .7rem; font-weight: 800;
  letter-spacing: .07em; text-transform: uppercase;
  text-align: left; white-space: nowrap;
}
.sh-th-set { text-align: center; border-left: 1px solid var(--sh-border); }
.sh-th-sub { text-align: center; }

.sh-tr { border-bottom: 1px solid var(--sh-border); transition: background .12s; }
.sh-tr:last-child { border-bottom: none; }
.sh-tr:hover { background: #FAFBFE; }

.sh-td { padding: 10px 12px; color: var(--sh-text); vertical-align: middle; }
.sh-td-center { text-align: center; }
.sh-td-no { color: var(--sh-muted); font-weight: 700; font-size: .8rem; }
.sh-td-ctrl { padding: 8px 12px; }

.sh-blok-label { font-weight: 800; color: var(--sh-text); }
.sh-rule { font-size: .8rem; color: var(--sh-muted); font-weight: 700; }

/* ── COUNTER ── */
.sh-counter { display: inline-flex; align-items: center; gap: 6px; }
.sh-ctr-btn {
  width: 30px; height: 30px;
  border: none; border-radius: 8px;
  font-size: 1rem; font-weight: 900; line-height: 1;
  cursor: pointer;
  transition: transform .1s, filter .1s;
}
.sh-ctr-btn:active { transform: scale(.94); }
.sh-ctr-btn:disabled { opacity: .4; cursor: not-allowed; }
.sh-ctr-minus { background: #FDECEC; color: #D64545; }
.sh-ctr-minus:hover:not(:disabled) { background: #fbd8d8; }
.sh-ctr-plus  { background: #E6FAF5; color: #0CA678; }
.sh-ctr-plus:hover:not(:disabled)  { background: #ccf5e8; }

.sh-td-ctrl.sh-has-empty { background: rgba(245,159,0,.08); }
.sh-td-ctrl.sh-has-empty .sh-ctr-input { border-color: #F59F00; }
.sh-td-ctrl.sh-has-empty .sh-ctr-minus,
.sh-td-ctrl.sh-has-empty .sh-ctr-plus { filter: saturate(1.2); }

.sh-ctr-input {
  width: 56px; text-align: center;
  border: 1.5px solid var(--sh-border);
  border-radius: 8px; padding: 6px;
  font-weight: 800; font-size: .875rem;
  background: #fff; color: var(--sh-text);
  transition: border-color .15s, box-shadow .15s;
}
.sh-ctr-input:focus { outline: none; border-color: var(--sh-accent); box-shadow: 0 0 0 3px rgba(232,93,47,.12); }
.sh-ctr-input:disabled { background: #F5F7FA; color: var(--sh-muted); }

.sh-ayam-val { font-weight: 900; color: var(--sh-text); transition: color .3s; }
.sh-flash { color: var(--sh-success) !important; }
</style>
@endsection