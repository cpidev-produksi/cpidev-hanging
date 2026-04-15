@extends('layouts.app')

@section('content')
<div class="ke-wrap">
  @php
  $slug = auth()->user()?->role?->slug;
  $canEditDone = in_array($slug, ['supervisor','superadmin'], true);
  $isLocked = ($form->status === 'done') && !$canEditDone;

    $mc = $form->monitorControl;
    $condColor = function(string $val): string {
        return match($val) {
            'kering', 'bak_kering'              => 'green',
            'basah', 'medium_basah'             => 'yellow',
            'bak_berisi_air', 'benda_lain'      => 'orange',
            'sangat_basah'                      => 'red',
            default                             => '',
        };
    };
  @endphp

  {{-- ── HEADER ── --}}
  <div class="ke-header">
    <div class="ke-header-left">
      <div class="ke-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 11l3 3L22 4"/>
          <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
      </div>
      <div>
        <h1 class="ke-title">Kondisi</h1>
        <div class="ke-breadcrumb">
          <code class="ke-bc-code">{{ $mc->report_code }}</code>
          <span class="ke-bc-sep">·</span>
          <span class="ke-bc-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/>
              <path d="M16 7V5a2 2 0 0 0-4 0v2"/>
            </svg>
            {{ $mc->location }}
          </span>
          <span class="ke-bc-sep">·</span>
          <span class="ke-bc-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="2"/>
              <path d="M16 8h4l3 5v3h-7V8z"/>
            </svg>
            Truk #{{ $mc->truck_no }}
          </span>
        </div>
      </div>
    </div>
    <a class="ke-btn-back" href="{{ route('conditions.landing') }}">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      Kembali
    </a>
  </div>

  {{-- ── LOCK BANNER ── --}}
  @if($form->status === 'done')
    <div class="ke-lock-banner">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
      Form sudah <strong>DONE</strong>. Data tidak bisa diubah.
    </div>
  @endif

  {{-- ── FORM CARD ── --}}
  <form method="POST" action="{{ route('conditions.update', $form) }}" class="ke-card">
    @csrf

    {{-- ── COLOR DEFINITION MAP untuk JS ── --}}
    {{-- Dipakai JS supaya saat klik radio, warna option langsung berubah --}}
    <div id="ke-color-map" data-map='{
      "sangat_basah":    "red",
      "basah":           "yellow",
      "medium_basah":    "yellow",
      "kering":          "green",
      "bak_berisi_air":  "orange",
      "bak_kering":      "green",
      "benda_lain":      "orange"
    }' style="display:none"></div>

    {{-- 1) Kondisi Keranjang --}}
    <div class="ke-section">
      <div class="ke-section-head">
        <span class="ke-section-num">1</span>
        <span class="ke-section-title">Ayam dimasukkan dalam kondisi keranjang</span>
        @php $cur1 = old('basket_condition', $form->basket_condition); @endphp
        @if($cur1)
          <span class="ke-cond-badge cond-{{ $condColor($cur1) }}">
            {{ $cur1 }}
          </span>
        @endif
      </div>
      @php $v = $cur1; @endphp
      <div class="ke-radio-grid">
        @foreach(['sangat_basah' => 'Sangat Basah', 'basah' => 'Basah', 'kering' => 'Kering'] as $val => $label)
          @php $color = $condColor($val); @endphp
          <label class="ke-radio cond-{{ $color }} {{ $v === $val ? 'ke-radio-checked' : '' }} {{ $isLocked ? 'ke-radio-disabled' : '' }}">
            <input type="radio" name="basket_condition" value="{{ $val }}"
                   @checked($v === $val) @disabled($isLocked)>
            <span class="ke-radio-dot"></span>
            <span class="ke-radio-color-dot cond-dot-{{ $color }}"></span>
            {{ $label }}
          </label>
        @endforeach
      </div>
      @error('basket_condition')
        <div class="ke-err">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="ke-divider"></div>

    {{-- 2) Kondisi Plat Form Truck --}}
    <div class="ke-section">
      <div class="ke-section-head">
        <span class="ke-section-num">2</span>
        <span class="ke-section-title">Kondisi Plat Form Truck</span>
        @php $cur2 = old('truck_platform_condition', $form->truck_platform_condition); @endphp
        @if($cur2)
          <span class="ke-cond-badge cond-{{ $condColor($cur2) }}">
            {{ $cur2 }}
          </span>
        @endif
      </div>
      @php $v = $cur2; @endphp
      <div class="ke-radio-grid">
        @foreach(['bak_berisi_air' => 'Bak berisi air', 'bak_kering' => 'Bak kering', 'benda_lain' => 'Benda lain-lain yang memberatkan timbangan'] as $val => $label)
          @php $color = $condColor($val); @endphp
          <label class="ke-radio cond-{{ $color }} {{ $v === $val ? 'ke-radio-checked' : '' }} {{ $isLocked ? 'ke-radio-disabled' : '' }}">
            <input type="radio" name="truck_platform_condition" value="{{ $val }}"
                   @checked($v === $val) @disabled($isLocked)>
            <span class="ke-radio-dot"></span>
            <span class="ke-radio-color-dot cond-dot-{{ $color }}"></span>
            {{ $label }}
          </label>
        @endforeach
      </div>
      @error('truck_platform_condition')
        <div class="ke-err">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="ke-divider"></div>

    {{-- 3) Kondisi Bulu Ayam --}}
    <div class="ke-section">
      <div class="ke-section-head">
        <span class="ke-section-num">3</span>
        <span class="ke-section-title">Kondisi Bulu Ayam</span>
        @php $cur3 = old('feather_condition', $form->feather_condition); @endphp
        @if($cur3)
          <span class="ke-cond-badge cond-{{ $condColor($cur3) }}">
            {{ $cur3 }}
          </span>
        @endif
      </div>
      @php $v = $cur3; @endphp
      <div class="ke-radio-grid">
        @foreach(['sangat_basah' => 'Sangat Basah', 'medium_basah' => 'Medium Basah', 'basah' => 'Basah', 'kering' => 'Kering'] as $val => $label)
          @php $color = $condColor($val); @endphp
          <label class="ke-radio cond-{{ $color }} {{ $v === $val ? 'ke-radio-checked' : '' }} {{ $isLocked ? 'ke-radio-disabled' : '' }}">
            <input type="radio" name="feather_condition" value="{{ $val }}"
                   @checked($v === $val) @disabled($isLocked)>
            <span class="ke-radio-dot"></span>
            <span class="ke-radio-color-dot cond-dot-{{ $color }}"></span>
            {{ $label }}
          </label>
        @endforeach
      </div>
      @error('feather_condition')
        <div class="ke-err">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ $message }}
        </div>
      @enderror
    </div>

    {{-- ── FOOTER ── --}}
    <div class="ke-footer">
      <a class="ke-btn-cancel" href="{{ route('conditions.landing') }}">Batalkan</a>
      <button class="ke-btn-save" type="submit" @disabled($isLocked)>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
        </svg>
        Simpan
      </button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const colorMap = JSON.parse(document.getElementById('ke-color-map').dataset.map);
  const allColors = ['green','yellow','orange','red'];

  document.querySelectorAll('.ke-radio-grid').forEach(function (grid) {
    grid.querySelectorAll('.ke-radio').forEach(function (label) {
      label.addEventListener('click', function () {
        if (label.classList.contains('ke-radio-disabled')) return;

        const input = label.querySelector('input[type="radio"]');
        if (!input) return;

        // ── uncheck siblings
        grid.querySelectorAll('.ke-radio').forEach(function (l) {
          l.classList.remove('ke-radio-checked');
        });

        // ── check this one
        label.classList.add('ke-radio-checked');
        input.checked = true;

        // ── update section badge
        const section = label.closest('.ke-section');
        if (section) {
          let badge = section.querySelector('.ke-cond-badge');
          const val = input.value;
          const color = colorMap[val] || '';
          const labels = {
            sangat_basah: 'Sangat Basah', basah: 'Basah',
            medium_basah: 'Medium Basah', kering: 'Kering',
            bak_berisi_air: 'Bak berisi air', bak_kering: 'Bak kering',
            benda_lain: 'Benda lain-lain'
          };
          if (!badge) {
            badge = document.createElement('span');
            badge.className = 'ke-cond-badge';
            section.querySelector('.ke-section-head').appendChild(badge);
          }
          // reset colors
          allColors.forEach(c => badge.classList.remove('cond-' + c));
          if (color) badge.classList.add('cond-' + color);
          badge.textContent = labels[val] || val;
        }
      });
    });
  });
});
</script>

<style>
:root {
  --ke-text:    #0D1117;
  --ke-muted:   #6B7896;
  --ke-border:  #E2E5EE;
  --ke-surface: #FFFFFF;
  --ke-accent:  #4F67FF;
  --ke-acc-hv:  #3A50E0;
  --ke-acc-xl:  rgba(79,103,255,.08);
  --ke-r:       14px;
  --ke-sh:      0 1px 4px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);

  /* ── Condition color tokens ── */
  --cg-bg:  #DCFCE7; --cg-border: #86EFAC; --cg-text: #166534; --cg-dot: #22C55E;
  --cy-bg:  #FEF9C3; --cy-border: #FDE047; --cy-text: #854D0E; --cy-dot: #EAB308;
  --co-bg:  #FFEDD5; --co-border: #FDBA74; --co-text: #9A3412; --co-dot: #F97316;
  --cr-bg:  #FEE2E2; --cr-border: #FCA5A5; --cr-text: #991B1B; --cr-dot: #EF4444;
}

.ke-wrap { max-width: 860px; margin: 0 auto; padding: 32px 24px; }

/* ── HEADER ── */
.ke-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
.ke-header-left { display:flex; align-items:center; gap:16px; }
.ke-icon {
  width:52px; height:52px; flex-shrink:0;
  background:var(--ke-acc-xl); color:var(--ke-accent);
  border-radius:14px; display:flex; align-items:center; justify-content:center;
}
.ke-title { font-size:1.45rem; font-weight:800; color:var(--ke-text); margin:0 0 6px; letter-spacing:-.01em; }
.ke-breadcrumb { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.ke-bc-code {
  font-family:'Fira Code','Courier New',monospace;
  font-size:.76rem; background:#F3F4F8; color:#4B5563;
  padding:2px 8px; border-radius:6px;
}
.ke-bc-sep { color:#D1D5E0; font-weight:400; }
.ke-bc-item { display:inline-flex; align-items:center; gap:4px; font-size:.78rem; font-weight:700; color:var(--ke-muted); }

.ke-btn-back {
  display:inline-flex; align-items:center; gap:6px;
  padding:9px 16px;
  border:1.5px solid var(--ke-border); border-radius:10px;
  background:#fff; color:var(--ke-muted);
  font-size:.83rem; font-weight:700; text-decoration:none;
  transition:all .15s;
}
.ke-btn-back:hover { background:#F0F2F7; border-color:#C5CAD8; color:var(--ke-text); }

/* ── LOCK BANNER ── */
.ke-lock-banner {
  display:flex; align-items:center; gap:8px;
  padding:12px 16px; border-radius:11px;
  background:rgba(245,159,0,.08);
  border:1px solid rgba(245,159,0,.25);
  color:#7c4a00; font-size:.84rem; font-weight:800;
  margin-bottom:14px;
}

/* ── LEGEND ── */
.ke-legend {
  display:flex; align-items:center; gap:10px; flex-wrap:wrap;
  padding:10px 14px; border-radius:10px;
  background:#F8F9FC; border:1px solid var(--ke-border);
  margin-bottom:14px; font-size:.78rem;
}
.ke-legend-title { font-weight:800; color:var(--ke-muted); margin-right:2px; }
.ke-legend-item {
  display:inline-flex; align-items:center; gap:5px;
  font-weight:700; padding:3px 10px; border-radius:999px;
  border:1.5px solid;
}
.ke-legend-dot {
  width:8px; height:8px; border-radius:50%; flex-shrink:0;
}
/* legend color variants */
.ke-legend-item.cond-green  { background:var(--cg-bg); border-color:var(--cg-border); color:var(--cg-text); }
.ke-legend-item.cond-yellow { background:var(--cy-bg); border-color:var(--cy-border); color:var(--cy-text); }
.ke-legend-item.cond-orange { background:var(--co-bg); border-color:var(--co-border); color:var(--co-text); }
.ke-legend-item.cond-red    { background:var(--cr-bg); border-color:var(--cr-border); color:var(--cr-text); }
.ke-legend-item.cond-green  .ke-legend-dot { background:var(--cg-dot); }
.ke-legend-item.cond-yellow .ke-legend-dot { background:var(--cy-dot); }
.ke-legend-item.cond-orange .ke-legend-dot { background:var(--co-dot); }
.ke-legend-item.cond-red    .ke-legend-dot { background:var(--cr-dot); }

/* ── CARD ── */
.ke-card {
  background:var(--ke-surface);
  border:1px solid var(--ke-border);
  border-radius:var(--ke-r);
  box-shadow:var(--ke-sh);
  overflow:hidden;
}

/* ── SECTION ── */
.ke-section { padding:18px 20px; }
.ke-section-head { display:flex; align-items:center; gap:10px; margin-bottom:13px; flex-wrap:wrap; }
.ke-section-num {
  width:24px; height:24px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  border-radius:7px;
  background:var(--ke-acc-xl); color:var(--ke-accent);
  font-size:.76rem; font-weight:900;
  border:1px solid rgba(79,103,255,.2);
}
.ke-section-title { font-size:.88rem; font-weight:800; color:var(--ke-text); }

/* ── SECTION BADGE (warna kondisi terpilih) ── */
.ke-cond-badge {
  display:inline-flex; align-items:center;
  padding:2px 10px; border-radius:999px;
  font-size:.72rem; font-weight:900; letter-spacing:.02em;
  border:1.5px solid; margin-left:auto;
  transition:all .2s;
}
.ke-cond-badge.cond-green  { background:var(--cg-bg); border-color:var(--cg-border); color:var(--cg-text); }
.ke-cond-badge.cond-yellow { background:var(--cy-bg); border-color:var(--cy-border); color:var(--cy-text); }
.ke-cond-badge.cond-orange { background:var(--co-bg); border-color:var(--co-border); color:var(--co-text); }
.ke-cond-badge.cond-red    { background:var(--cr-bg); border-color:var(--cr-border); color:var(--cr-text); }

/* ── RADIO GRID ── */
.ke-radio-grid { display:grid; grid-template-columns:1fr; gap:8px; }
.ke-radio {
  display:flex; align-items:center; gap:10px;
  padding:11px 14px;
  border:1.5px solid var(--ke-border);
  border-radius:11px;
  background:#FAFBFD;
  font-size:.85rem; font-weight:700; color:var(--ke-text);
  cursor:pointer;
  transition:border-color .15s, background .15s;
}

/* color-tinted unchecked hover per warna kondisi */
.ke-radio.cond-green:hover:not(.ke-radio-disabled)  { border-color:var(--cg-border); background:rgba(220,252,231,.45); }
.ke-radio.cond-yellow:hover:not(.ke-radio-disabled) { border-color:var(--cy-border); background:rgba(254,249,195,.45); }
.ke-radio.cond-orange:hover:not(.ke-radio-disabled) { border-color:var(--co-border); background:rgba(255,237,213,.45); }
.ke-radio.cond-red:hover:not(.ke-radio-disabled)    { border-color:var(--cr-border); background:rgba(254,226,226,.45); }

/* checked states per warna kondisi */
.ke-radio.ke-radio-checked.cond-green  { border-color:var(--cg-border); background:var(--cg-bg); color:var(--cg-text); }
.ke-radio.ke-radio-checked.cond-yellow { border-color:var(--cy-border); background:var(--cy-bg); color:var(--cy-text); }
.ke-radio.ke-radio-checked.cond-orange { border-color:var(--co-border); background:var(--co-bg); color:var(--co-text); }
.ke-radio.ke-radio-checked.cond-red    { border-color:var(--cr-border); background:var(--cr-bg); color:var(--cr-text); }

.ke-radio-disabled { opacity:.55; cursor:not-allowed; }
.ke-radio input[type="radio"] { display:none; }

/* ── Radio default dot (putih/abu saat belum checked) ── */
.ke-radio-dot {
  width:16px; height:16px; flex-shrink:0;
  border-radius:50%;
  border:2px solid #C5CAD8;
  display:flex; align-items:center; justify-content:center;
  transition:border-color .15s, background .15s;
  position:relative;
}
/* warna dot saat checked mengikuti kondisi */
.ke-radio-checked.cond-green  .ke-radio-dot { border-color:var(--cg-dot); background:var(--cg-dot); }
.ke-radio-checked.cond-yellow .ke-radio-dot { border-color:var(--cy-dot); background:var(--cy-dot); }
.ke-radio-checked.cond-orange .ke-radio-dot { border-color:var(--co-dot); background:var(--co-dot); }
.ke-radio-checked.cond-red    .ke-radio-dot { border-color:var(--cr-dot); background:var(--cr-dot); }
.ke-radio-checked .ke-radio-dot::after {
  content:''; width:6px; height:6px;
  border-radius:50%; background:#fff; position:absolute;
}

/* ── Small color indicator dot (always visible) ── */
.ke-radio-color-dot {
  width:10px; height:10px; flex-shrink:0;
  border-radius:50%;
  margin-left:auto;
  opacity:.6;
  transition:opacity .15s, transform .15s;
}
.ke-radio-checked .ke-radio-color-dot { opacity:1; transform:scale(1.2); }
.cond-dot-green  { background:var(--cg-dot); }
.cond-dot-yellow { background:var(--cy-dot); }
.cond-dot-orange { background:var(--co-dot); }
.cond-dot-red    { background:var(--cr-dot); }

/* ── DIVIDER ── */
.ke-divider { height:1px; background:var(--ke-border); margin:0 20px; }

/* ── ERROR ── */
.ke-err {
  display:flex; align-items:center; gap:6px;
  margin-top:8px; color:#EF4444;
  font-size:.78rem; font-weight:800;
}

/* ── FOOTER ── */
.ke-footer {
  display:flex; align-items:center; justify-content:flex-end; gap:10px;
  padding:16px 20px;
  border-top:1px solid var(--ke-border);
  background:#FAFBFD;
}
.ke-btn-cancel {
  display:inline-flex; align-items:center; gap:6px;
  padding:10px 18px;
  border:1.5px solid var(--ke-border); border-radius:10px;
  background:#fff; color:var(--ke-muted);
  font-size:.85rem; font-weight:700; text-decoration:none;
  transition:all .14s;
}
.ke-btn-cancel:hover { background:#F0F2F7; border-color:#C5CAD8; color:var(--ke-text); }
.ke-btn-save {
  display:inline-flex; align-items:center; gap:7px;
  padding:10px 22px;
  background:var(--ke-accent); color:#fff;
  border:none; border-radius:10px;
  font-size:.85rem; font-weight:700; cursor:pointer;
  box-shadow:0 2px 10px rgba(79,103,255,.28);
  transition:all .18s;
}
.ke-btn-save:hover:not(:disabled) { background:var(--ke-acc-hv); transform:translateY(-1px); }
.ke-btn-save:disabled { opacity:.45; cursor:not-allowed; box-shadow:none; }
</style>
@endsection