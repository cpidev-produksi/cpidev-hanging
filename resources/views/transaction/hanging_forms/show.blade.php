@extends('layouts.app')

@section('content')
<div class="hanging-page">

  {{-- Breadcrumb --}}
  <div class="breadcrumb">
    <a href="{{ route('monitor-controls.index') }}" class="breadcrumb-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="14" rx="2"/><path d="M7 20h10"/><path d="M9 16v4"/><path d="M15 16v4"/>
      </svg>
      Kontrol Monitor
    </a>
    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <polyline points="9 18 15 12 9 6"/>
    </svg>
    <span>Form Hanging Ayam</span>
  </div>

  {{-- Header --}}
  <div class="page-header">
    <div class="page-title-group">
      <div class="page-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title">Form Hanging Ayam</h1>
        <p class="page-subtitle">
          Report:
          <code class="mono-chip">{{ $form->monitorControl->report_code }}</code>
        </p>
      </div>
    </div>

    <div class="header-actions">
      <a href="{{ route('monitor-controls.index') }}" class="btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
      </a>
    </div>
  </div>

  {{-- Finish Panel --}}
  <div class="card card-pad mb-16">
    <div class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
      </svg>
      Penyelesaian Proses
    </div>

    <form method="POST" action="{{ route('hanging-forms.finish', $form) }}" class="finish-form">
      @csrf

      <div class="finish-grid">
        <div class="form-group">
          <label class="form-label" for="unloading_time">Jam Bongkar <span class="required">*</span></label>
          <div class="input-wrapper @error('unloading_time') has-error @enderror">
            <div class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><path d="M12 7v6l3 2"/>
              </svg>
            </div>
            <input type="time" id="unloading_time" name="unloading_time"
                   value="{{ old('unloading_time', $form->unloading_time?->format('H:i')) }}"
                   class="form-input">
          </div>
          @error('unloading_time')
            <div class="form-error">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="finish_time">Jam Selesai <span class="required">*</span></label>
          <div class="input-wrapper @error('finish_time') has-error @enderror">
            <div class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><path d="M12 7v6l3 2"/>
              </svg>
            </div>
            <input type="time" id="finish_time" name="finish_time"
                   value="{{ old('finish_time', $form->finish_time?->format('H:i')) }}"
                   class="form-input">
          </div>
          @error('finish_time')
            <div class="form-error">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              {{ $message }}
            </div>
          @enderror
        </div>
      </div>

      <div class="finish-actions">
        <button type="submit"
                onclick="return confirm('Selesaikan proses ini? Status akan menjadi DONE.')"
                class="btn-submit btn-success">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Selesai
        </button>
      </div>
    </form>
  </div>

  {{-- Info Card --}}
  <div class="info-grid">
    <div class="card card-pad">
      <div class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Informasi Proses
      </div>

      <div class="kv-grid">
        <div class="kv">
          <div class="k">Lokasi</div>
          <div class="v">{{ $form->monitorControl->location }}</div>
        </div>
        <div class="kv">
          <div class="k">Tanggal</div>
          <div class="v">{{ $form->monitorControl->process_date->format('d/m/Y') }}</div>
        </div>
        <div class="kv">
          <div class="k">Shift</div>
          <div class="v uppercase">{{ $form->monitorControl->shift }}</div>
        </div>

        <div class="divider"></div>

        <div class="kv">
          <div class="k">No Truk</div>
          <div class="v">{{ $form->monitorControl->truck->no_truck }}</div>
        </div>
        <div class="kv">
          <div class="k">No Polisi</div>
          <div class="v mono">{{ $form->monitorControl->truck->plate_number }}</div>
        </div>
        <div class="kv">
          <div class="k">Ekspedisi</div>
          <div class="v">{{ $form->monitorControl->truck->expedition->name }}</div>
        </div>

        <div class="kv">
          <div class="k">Sopir</div>
          <div class="v">{{ $form->monitorControl->driver_name }}</div>
        </div>
        <div class="kv">
          <div class="k">Farm</div>
          <div class="v">{{ $form->monitorControl->farm->name }}</div>
        </div>
        <div class="kv">
          <div class="k">Size</div>
          <div class="v">{{ $form->monitorControl->size }}</div>
        </div>
      </div>
    </div>

    <div class="card card-pad summary-card">
      <div class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 3v18h18"/><path d="M7 14l3-3 4 4 6-7"/>
        </svg>
        Ringkasan
      </div>

      <div class="summary-line">
        Total Shackle Kosong:
        <span id="total-kosong" class="summary-value">{{ $totalKosong }}</span>
        <span class="summary-unit">Pcs</span>
      </div>
      <div class="summary-line">
        Jumlah Ayam Mati:
        <span class="summary-value">{{ $ayamMati }}</span>
        <span class="summary-unit">Ekor</span>
      </div>
      <div class="summary-line">
        Jumlah Ayam Diterima:
        <span id="total-ayam" class="summary-value">{{ $totalAyam }}</span>
        <span class="summary-unit">Ekor</span>
      </div>
    </div>
  </div>

  @php
    $setCount = (int) $form->monitorControl->set_count;
  @endphp

  {{-- Table --}}
  <div class="card table-card">
    <div class="table-wrapper">
      <table class="data-table hanging-table">
        <thead>
          <tr>
            <th class="col-no" rowspan="2">No</th>
            <th class="col-shackle" rowspan="2">BLOK</th>
            <th class="col-rule" rowspan="2">Jumlah Shackle</th>
            @for($s=1;$s<=$setCount;$s++)
              <th class="set-head" colspan="2">KOLOM {{ $s }}</th>
            @endfor
          </tr>
          <tr>
            @for($s=1;$s<=$setCount;$s++)
              <th class="col-empty">KOSONG</th>
              <th class="col-ayam">AYAM</th>
            @endfor
          </tr>
        </thead>

        <tbody>
        @foreach($form->lines as $line)
          <tr>
            <td class="col-no">{{ $line->line_no }}</td>
            <td class="col-shackle"><span class="strong">{{ $line->shackle_label }}</span></td>
            <td class="col-rule">{{ $line->rule_min }}-{{ $line->rule_max }}</td>

            @for($s=1;$s<=$setCount;$s++)
              @php
                $cell = $line->sets->firstWhere('set_no',$s);
                $emptyRaw = $cell?->empty_count; // null atau int
                $empty = is_null($emptyRaw) ? null : (int) $emptyRaw;
                $ayam = is_null($empty) ? 0 : (50 - $empty);
              @endphp

              <td class="col-empty">
                <div class="counter">
                  <button type="button"
                          class="counter-btn minus"
                          onclick="updateCell({{ $cell->id }}, {{ max(0, ($empty ?? 0)-1) }})">-</button>

                  <input id="empty-{{ $cell->id }}"
                         value="{{ is_null($empty) ? '' : $empty }}"
                         class="counter-input"
                         inputmode="numeric"
                         onchange="updateCell({{ $cell->id }}, this.value)"/>

                  <button type="button"
                          class="counter-btn plus"
                          onclick="updateCell({{ $cell->id }}, {{ min(50, ($empty ?? 0)+1) }})">+</button>
                </div>
              </td>

              <td class="col-ayam">
                <span id="ayam-{{ $cell->id }}" class="ayam-value">{{ $ayam }}</span>
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
async function updateCell(id, emptyCount) {
  let v = emptyCount;
  if (v === '' || v === null || typeof v === 'undefined') v = null;

  if (v !== null) {
    v = parseInt(v, 10);
    if (Number.isNaN(v)) v = 0;
    if (v < 0) v = 0;
    if (v > 50) v = 50;
  }

  const res = await fetch(`{{ url('/hanging-cells') }}/${id}`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ empty_count: v })
  });

  const json = await res.json();

  document.getElementById(`empty-${id}`).value = (json.empty_count === null) ? '' : json.empty_count;
  document.getElementById(`ayam-${id}`).textContent = json.ayam;

  window.clearTimeout(window.__refreshTotalsTimer);
  window.__refreshTotalsTimer = setTimeout(() => window.location.reload(), 300);
}
</script>

<style>
:root {
  --c-bg: #F5F6FA;
  --c-card: #FFFFFF;
  --c-border: #E8EAF0;
  --c-text: #1A1D2E;
  --c-muted: #6B7280;
  --c-accent: #4F67FF;
  --c-accent-hover: #3D53E8;
  --c-accent-light: #EEF0FF;
  --c-danger: #F03E3E;
  --c-danger-light: #FFF5F5;
  --c-success: #0CA678;
  --c-success-light: #E6FAF5;
  --c-warning: #F59F00;
  --c-warning-light: #FFF8E1;
  --radius: 12px;
  --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
}

/* Layout */
.hanging-page { max-width: 1280px; margin: 0 auto; padding: 28px 20px; }
.mb-16 { margin-bottom: 16px; }

/* Breadcrumb */
.breadcrumb {
  display: flex; align-items: center; gap: 6px;
  font-size: .78rem; color: var(--c-muted);
  margin-bottom: 20px;
}
.breadcrumb-link {
  display: inline-flex; align-items: center; gap: 5px;
  color: var(--c-accent); text-decoration: none; font-weight: 500;
  transition: opacity .15s;
}
.breadcrumb-link:hover { opacity: .75; }
.breadcrumb svg { color: #C5C9D6; }

/* Header */
.page-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; flex-wrap: wrap; gap: 12px;
}
.page-title-group { display: flex; align-items: center; gap: 14px; }
.page-icon {
  width: 46px; height: 46px;
  background: var(--c-accent-light);
  color: var(--c-accent);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.page-title { font-size: 1.35rem; font-weight: 700; color: var(--c-text); margin: 0 0 2px; }
.page-subtitle { font-size: .8rem; color: var(--c-muted); margin: 0; }
.mono-chip {
  background: #F3F4F8;
  color: #4B5563;
  padding: 2px 8px;
  border-radius: 6px;
  font-size: .75rem;
  font-family: 'Fira Code', 'Courier New', monospace;
}

/* Cards */
.card {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}
.card-pad { padding: 18px; }
.card-title {
  display: flex; align-items: center; gap: 7px;
  font-size: .72rem; font-weight: 800;
  letter-spacing: .08em; text-transform: uppercase;
  color: var(--c-accent);
  margin-bottom: 14px;
}
.table-card { overflow: hidden; }

/* Buttons */
.btn-secondary {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 16px;
  border: 1.5px solid var(--c-border);
  border-radius: 9px;
  background: #fff;
  color: var(--c-muted);
  text-decoration: none;
  font-size: .84rem; font-weight: 600;
  transition: all .15s;
}
.btn-secondary:hover { border-color: #C5C9D6; color: var(--c-text); background: #F5F6FA; }

.btn-submit {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px;
  border: none; border-radius: 9px;
  font-size: .84rem; font-weight: 700;
  cursor: pointer;
  transition: all .18s;
}
.btn-success {
  background: var(--c-success);
  color: #fff;
  box-shadow: 0 2px 10px rgba(12,166,120,.25);
}
.btn-success:hover { filter: brightness(.95); transform: translateY(-1px); }

/* Form bits */
.finish-form { display: flex; flex-direction: column; gap: 12px; }
.finish-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.finish-actions { display: flex; justify-content: flex-end; }
@media (max-width: 720px) { .finish-grid { grid-template-columns: 1fr; } }

.form-label { display:block; font-size: .8rem; font-weight: 700; color: var(--c-text); margin-bottom: 6px; }
.required { color: var(--c-danger); margin-left: 2px; }

.input-wrapper {
  position: relative;
  display: flex; align-items: center;
  border: 1.5px solid var(--c-border);
  border-radius: 9px;
  background: #FAFBFD;
  transition: border-color .18s, box-shadow .18s;
  overflow: hidden;
}
.input-wrapper:focus-within { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(79,103,255,.12); background:#fff; }
.input-wrapper.has-error { border-color: var(--c-danger); background: var(--c-danger-light); }

.input-icon {
  width: 40px; min-width: 40px;
  display: flex; align-items: center; justify-content: center;
  color: var(--c-muted);
  pointer-events: none;
}
.form-input {
  flex: 1;
  border: none; outline: none;
  background: transparent;
  padding: 10px 12px 10px 0;
  font-size: .875rem;
  color: var(--c-text);
  width: 100%;
}
.form-error {
  display: flex; align-items: center; gap: 6px;
  color: var(--c-danger);
  font-size: .76rem; font-weight: 600;
  margin-top: 6px;
}

/* Info grid */
.info-grid { display: grid; grid-template-columns: 1.6fr .9fr; gap: 14px; margin-bottom: 14px; }
@media (max-width: 960px) { .info-grid { grid-template-columns: 1fr; } }

.kv-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
@media (max-width: 720px) { .kv-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 520px) { .kv-grid { grid-template-columns: 1fr; } }

.kv .k { font-size: .72rem; color: var(--c-muted); margin-bottom: 3px; }
.kv .v { font-weight: 700; color: var(--c-text); }
.kv .mono { font-family: 'Fira Code','Courier New',monospace; }
.divider { grid-column: 1 / -1; height: 1px; background: var(--c-border); margin: 2px 0; }

/* Summary */
.summary-card .summary-line { display:flex; gap: 6px; align-items: baseline; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed rgba(232,234,240,.9); }
.summary-card .summary-line:last-child { border-bottom: none; }
.summary-value { font-weight: 900; color: var(--c-text); }
.summary-unit { color: var(--c-muted); font-size: .82rem; }

/* Table */
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: .875rem; }

.data-table thead tr { background: #FAFBFD; border-bottom: 1px solid var(--c-border); }
.data-table th {
  padding: 10px 10px;
  color: var(--c-muted);
  font-size: .72rem;
  font-weight: 800;
  letter-spacing: .06em;
  text-transform: uppercase;
  text-align: left;
  white-space: nowrap;
}
.data-table td { padding: 10px 10px; color: var(--c-text); border-top: 1px solid var(--c-border); vertical-align: middle; }
.data-table tbody tr:hover { background: #FAFBFE; }
.data-table tfoot td { padding: 12px 10px; background: #FAFBFD; border-top: 1px solid var(--c-border); font-weight: 800; color: var(--c-muted); }

/* Column sizing (proporsional) */
.hanging-table { min-width: 1100px; table-layout: fixed; }
.col-no { width: 56px; text-align: center; }
.col-shackle { width: 140px; }
.col-rule { width: 110px; }
.set-head { text-align: center !important; }
.col-empty, .col-ayam { text-align: center; }

@media (max-width: 720px) {
  .hanging-table { min-width: 980px; }
}

/* Cell UI */
.strong { font-weight: 800; }
.counter {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.counter-btn {
  width: 32px; height: 32px;
  border: none;
  border-radius: 9px;
  font-weight: 900;
  cursor: pointer;
  transition: transform .12s, filter .12s;
}
.counter-btn:active { transform: translateY(1px); }
.counter-btn.minus { background: #FDECEC; color: #D64545; }
.counter-btn.plus { background: #E8FBF4; color: #0CA678; }

.counter-input {
  width: 60px;
  text-align: center;
  border: 1.5px solid var(--c-border);
  border-radius: 9px;
  padding: 7px 8px;
  font-weight: 800;
  background: #fff;
}
.ayam-value { font-weight: 900; }
</style>
@endsection