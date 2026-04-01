@extends('layouts.app')

@section('content')
<div class="ke-wrap">
  @php $mc = $form->monitorControl; @endphp

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

    {{-- 1) Kondisi Keranjang --}}
    <div class="ke-section">
      <div class="ke-section-head">
        <span class="ke-section-num">1</span>
        <span class="ke-section-title">Ayam dimasukkan dalam kondisi keranjang</span>
      </div>
      @php $v = old('basket_condition', $form->basket_condition); @endphp
      <div class="ke-radio-grid">
        @foreach(['sangat_basah' => 'Sangat Basah', 'basah' => 'Basah', 'kering' => 'Kering'] as $val => $label)
          <label class="ke-radio {{ $v === $val ? 'ke-radio-checked' : '' }} {{ $form->status === 'done' ? 'ke-radio-disabled' : '' }}">
            <input type="radio" name="basket_condition" value="{{ $val }}"
                   @checked($v === $val) @disabled($form->status === 'done')>
            <span class="ke-radio-dot"></span>
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
      </div>
      @php $v = old('truck_platform_condition', $form->truck_platform_condition); @endphp
      <div class="ke-radio-grid">
        @foreach(['bak_berisi_air' => 'Bak berisi air', 'bak_kering' => 'Bak kering', 'benda_lain' => 'Benda lain-lain yang memberatkan timbangan'] as $val => $label)
          <label class="ke-radio {{ $v === $val ? 'ke-radio-checked' : '' }} {{ $form->status === 'done' ? 'ke-radio-disabled' : '' }}">
            <input type="radio" name="truck_platform_condition" value="{{ $val }}"
                   @checked($v === $val) @disabled($form->status === 'done')>
            <span class="ke-radio-dot"></span>
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
      </div>
      @php $v = old('feather_condition', $form->feather_condition); @endphp
      <div class="ke-radio-grid">
        @foreach(['sangat_basah' => 'Sangat Basah', 'medium_basah' => 'Medium Basah', 'basah' => 'Basah', 'kering' => 'Kering'] as $val => $label)
          <label class="ke-radio {{ $v === $val ? 'ke-radio-checked' : '' }} {{ $form->status === 'done' ? 'ke-radio-disabled' : '' }}">
            <input type="radio" name="feather_condition" value="{{ $val }}"
                   @checked($v === $val) @disabled($form->status === 'done')>
            <span class="ke-radio-dot"></span>
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
      <button class="ke-btn-save" type="submit" @disabled($form->status === 'done')>
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
  document.querySelectorAll('.ke-radio-grid').forEach(function (grid) {
    grid.querySelectorAll('.ke-radio').forEach(function (label) {
      label.addEventListener('click', function () {
        if (label.classList.contains('ke-radio-disabled')) return;
        // remove checked from siblings
        grid.querySelectorAll('.ke-radio').forEach(function (l) {
          l.classList.remove('ke-radio-checked');
        });
        label.classList.add('ke-radio-checked');
        // actually check the hidden input
        const input = label.querySelector('input[type="radio"]');
        if (input) input.checked = true;
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
.ke-section-head { display:flex; align-items:center; gap:10px; margin-bottom:13px; }
.ke-section-num {
  width:24px; height:24px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  border-radius:7px;
  background:var(--ke-acc-xl); color:var(--ke-accent);
  font-size:.76rem; font-weight:900;
  border:1px solid rgba(79,103,255,.2);
}
.ke-section-title { font-size:.88rem; font-weight:800; color:var(--ke-text); }

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
.ke-radio:hover:not(.ke-radio-disabled) {
  border-color:rgba(79,103,255,.35);
  background:var(--ke-acc-xl);
}
.ke-radio-checked {
  border-color:rgba(79,103,255,.4);
  background:var(--ke-acc-xl);
  color:var(--ke-accent);
}
.ke-radio-disabled { opacity:.55; cursor:not-allowed; }
.ke-radio input[type="radio"] { display:none; }
.ke-radio-dot {
  width:16px; height:16px; flex-shrink:0;
  border-radius:50%;
  border:2px solid #C5CAD8;
  display:flex; align-items:center; justify-content:center;
  transition:border-color .15s, background .15s;
  position:relative;
}
.ke-radio-checked .ke-radio-dot {
  border-color:var(--ke-accent);
  background:var(--ke-accent);
}
.ke-radio-checked .ke-radio-dot::after {
  content:'';
  width:6px; height:6px;
  border-radius:50%;
  background:#fff;
  position:absolute;
}

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