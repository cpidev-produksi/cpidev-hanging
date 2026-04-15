@extends('layouts.app')

@section('content')
<div class="ed-wrap">

  @php
  $slug = auth()->user()?->role?->slug;
  $canEditDone = in_array($slug, ['supervisor','superadmin'], true);
  $isLocked = ($form->status === 'done') && !$canEditDone;

  $mc = $form->monitorControl;
  $oldWeights = old('retur_weights');
  $oldPhotos  = old('retur_photo_existing');

  if (is_array($oldWeights)) {
      $weights = $oldWeights;
      $photos  = is_array($oldPhotos) ? $oldPhotos : [];
  } else {
      $weights = $form->returItems->pluck('weight_kg')->toArray();
      $photos  = $form->returItems->pluck('photo_path')->toArray();
  }

  if (count($weights) === 0) {
      $weights = [''];
      $photos  = [''];
  }
  @endphp

  {{-- ── BREADCRUMB ── --}}
  <nav class="ed-bc">
    <a href="{{ route('hanging-forms.show', $form) }}" class="ed-bc-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/>
        <path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>
      </svg>
      Detail Form
    </a>
    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Retur & Mati</span>
  </nav>

  {{-- ── HEADER ── --}}
  <div class="ed-header">
    <div class="ed-header-left">
      <div class="ed-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <div>
        <h1 class="ed-title">Ayam Retur & Mati</h1>
        <div class="ed-meta">
          <span class="ed-chip">{{ $mc->report_code }}</span>
          <span class="ed-sep">·</span>
          <span>{{ $mc->location }}</span>
          <span class="ed-sep">·</span>
          <span>Truk <strong>#{{ $mc->truck_no }}</strong></span>
        </div>
      </div>
    </div>
    <a class="ed-btn-back" href="{{ route('retur-mati.landing', $form) }}">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
      </svg>
      Kembali
    </a>
  </div>

  {{-- ── DONE BANNER ── --}}
  @if($form->status === 'done' && $isLocked)
    <div class="ed-done-banner">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
        <polyline points="9 12 12 15 16 9"/>
      </svg>
      Form sudah <strong>DONE</strong> — data tidak dapat diubah.
    </div>
  @endif

  <form method="POST" action="{{ route('retur-mati.update', $form) }}" enctype="multipart/form-data">
    @csrf

    <div class="ed-grid">

      {{-- ── AYAM MATI ── --}}
      <div class="ed-card">
        <div class="ed-card-head">
          <div class="ed-card-icon ed-icon-red">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <div>
            <div class="ed-card-title">Ayam Mati</div>
            <div class="ed-card-sub">Jumlah ayam yang mati saat proses</div>
          </div>
        </div>
        <div class="ed-card-body">
          <label class="ed-label" for="dead_count">Jumlah Ayam Mati</label>
          <div class="ed-big-counter">
            <button type="button" class="ed-big-minus" id="deadMinus"
                    onclick="stepDead(-1)" @disabled($isLocked)>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
            </button>
            <input id="dead_count" name="dead_count" type="number" min="0" step="1"
                   value="{{ old('dead_count', $form->dead_count ?? 0) }}"
                   class="ed-big-input" @disabled($isLocked)>
            <button type="button" class="ed-big-plus" id="deadPlus"
                    onclick="stepDead(1)" @disabled($isLocked)>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
            </button>
          </div>
          @error('dead_count')<p class="ed-error">{{ $message }}</p>@enderror
          <p class="ed-hint">Masukkan jumlah ekor ayam yang ditemukan mati.</p>
        </div>
      </div>

      {{-- ── AYAM RETUR ── --}}
      <div class="ed-card">
        <div class="ed-card-head">
          <div class="ed-card-icon ed-icon-orange">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/>
              <path d="M3.51 15a9 9 0 1 0 .49-4"/>
            </svg>
          </div>
          <div>
            <div class="ed-card-title">Berat Ayam Retur</div>
            <div class="ed-card-sub">Input berat per ekor dalam Kg</div>
          </div>
        </div>

        <div class="ed-card-body">
          <div id="returList" class="ed-retur-list">
            @foreach($weights as $idx => $w)
              <div class="ed-retur-row">
                <div class="ed-row-num">{{ $idx + 1 }}</div>
                <div class="ed-input-wrap">
                  <span class="ed-input-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"><path d="M6 2v20M18 2v20M2 12h20"/>
                    </svg>
                  </span>
                  <input type="number" step="0.01" min="0"
                         class="ed-input retur-weight"
                         name="retur_weights[]"
                         value="{{ $w }}"
                         placeholder="0.00"
                         @disabled($isLocked)>
                  <span class="ed-input-suffix">Kg</span>
                </div>

                {{-- PHOTO --}}
                <div class="ed-photo">
                  <input type="hidden" name="retur_photo_existing[]" value="{{ $photos[$idx] ?? '' }}">
                  <input type="hidden" name="retur_photo_remove[]" value="0" class="retur-photo-remove">

                  <input type="file"
                        name="retur_photos[]"
                        accept="image/jpeg"
                        class="retur-photo-input"
                        style="display:none"
                        @disabled($isLocked)>

                  @if(!$isLocked)
                  <div class="ed-photo-trigger">
                    <button type="button" class="ed-photo-btn-main" title="Foto">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                      </svg>
                      <span>Foto</span>
                      <svg class="ed-caret" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="ed-photo-menu">
                      <button type="button" class="ed-photo-menu-item" data-camera="0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5">
                          <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                          <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        Upload dari Galeri
                      </button>
                      <button type="button" class="ed-photo-menu-item" data-camera="1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5">
                          <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                          <circle cx="12" cy="13" r="4"/>
                        </svg>
                        Ambil dari Kamera
                      </button>
                    </div>
                  </div>
                  @endif

                  <div class="ed-photo-preview">
                    @if(!empty($photos[$idx]))
                      <div class="ed-photo-thumb">
                        <img src="{{ asset('storage/'.$photos[$idx]) }}" alt="photo">
                        @if(!$isLocked)
                        <button type="button" class="ed-photo-remove" onclick="removePhoto(this)">
                          <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                               stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                          </svg>
                        </button>
                        @endif
                      </div>
                    @endif
                  </div>
                </div>

                <button type="button" class="ed-btn-remove"
                        onclick="removeRow(this, true)"
                        @disabled($isLocked)
                        title="Hapus baris ini">
                  <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4h6v2"/>
                  </svg>
                </button>
              </div>
            @endforeach
          </div>

          <div class="ed-retur-actions" style="margin-top:12px;text-align:right;">
            <button type="button" class="ed-btn-add" onclick="addRow()" @disabled($isLocked)>
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
              Tambah
            </button>
          </div>
          @error('retur_weights')    <p class="ed-error">{{ $message }}</p>@enderror
          @error('retur_weights.*')  <p class="ed-error">{{ $message }}</p>@enderror
        </div>

        {{-- Summary strip --}}
        <div class="ed-summary">
          <div class="ed-summary-item">
            <div class="ed-summary-label">Jumlah Retur</div>
            <div class="ed-summary-val"><span id="returCount">0</span> <span class="ed-summary-unit">Ekor</span></div>
          </div>
          <div class="ed-summary-divider"></div>
          <div class="ed-summary-item">
            <div class="ed-summary-label">Total Berat</div>
            <div class="ed-summary-val"><span id="returKg">0.00</span> <span class="ed-summary-unit">Kg</span></div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="ed-footer">
      <a class="ed-btn-cancel" href="{{ route('retur-mati.landing', $form) }}">Batal</a>
      <button class="ed-btn-save" type="submit" @disabled($isLocked)>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/>
        </svg>
        Simpan
      </button>
    </div>
  </form>
</div>

<script>
function stepDead(delta) {
  const inp = document.getElementById('dead_count');
  let v = parseInt(inp.value, 10) || 0;
  v = Math.max(0, v + delta);
  inp.value = v;
}

function recalc() {
  let count = 0, total = 0;
  document.querySelectorAll('.retur-weight').forEach(i => {
    const n = parseFloat(i.value);
    if (!isNaN(n) && n > 0) { count++; total += n; }
  });
  document.getElementById('returCount').textContent = count;
  document.getElementById('returKg').textContent    = total.toFixed(2);
  renumberRows();
}

function renumberRows() {
  document.querySelectorAll('.ed-retur-row').forEach((row, i) => {
    const num = row.querySelector('.ed-row-num');
    if (num) num.textContent = i + 1;
  });
}

function addRow() {
  const list  = document.getElementById('returList');
  const count = list.querySelectorAll('.ed-retur-row').length;
  const row   = document.createElement('div');
  row.className = 'ed-retur-row ed-row-new';
  row.innerHTML = `
    <div class="ed-row-num">${count + 1}</div>
    <div class="ed-input-wrap">
      <span class="ed-input-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2"><path d="M6 2v20M18 2v20M2 12h20"/></svg>
      </span>
      <input type="number" step="0.01" min="0"
             class="ed-input retur-weight"
             name="retur_weights[]"
             value=""
             placeholder="0.00">
      <span class="ed-input-suffix">Kg</span>
    </div>

    <div class="ed-photo">
      <input type="hidden" name="retur_photo_existing[]" value="">
      <input type="hidden" name="retur_photo_remove[]" value="0" class="retur-photo-remove">
      <input type="file" name="retur_photos[]" accept="image/jpeg" class="retur-photo-input" style="display:none">
      <div class="ed-photo-trigger">
        <button type="button" class="ed-photo-btn-main" title="Foto">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5">
            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
            <circle cx="12" cy="13" r="4"/>
          </svg>
          <span>Foto</span>
          <svg class="ed-caret" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="ed-photo-menu">
          <button type="button" class="ed-photo-menu-item" data-camera="0">Upload dari Galeri</button>
          <button type="button" class="ed-photo-menu-item" data-camera="1">Ambil dari Kamera</button>
        </div>
      </div>
      <div class="ed-photo-preview"></div>
    </div>

    <button type="button" class="ed-btn-remove" title="Hapus baris ini" @disabled($isLocked)>
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
      </svg>
    </button>
  `;
  list.appendChild(row);

  const removeBtn = row.querySelector('.ed-btn-remove');
  if (removeBtn) removeBtn.onclick = () => removeRow(removeBtn, true);

  bindPhotoInput(row);

  requestAnimationFrame(() => {
    row.classList.add('ed-row-visible');
    const weightInput = row.querySelector('.retur-weight');
    if (weightInput) weightInput.focus();
  });

  recalc();
}

function removeRow(btn, ask) {
  if (ask && !confirm('Hapus input ayam retur ini?')) return;
  const row = btn.closest('.ed-retur-row');
  if (!row) return;
  row.style.transition = 'opacity .2s, transform .2s';
  row.style.opacity    = '0';
  row.style.transform  = 'translateX(12px)';
  setTimeout(() => { row.remove(); recalc(); }, 200);
}

function togglePhotoMenu(btn, event) {
  if (event) { event.stopPropagation(); event.preventDefault(); }
  const trigger = btn.closest('.ed-photo-trigger');
  const isOpen = trigger.classList.contains('open');

  document.querySelectorAll('.ed-photo-trigger.open').forEach(t => {
    t.classList.remove('open');
    const m = t.querySelector('.ed-photo-menu');
    if (m) m.style.display = 'none';
  });

  if (!isOpen) {
    trigger.classList.add('open');
    const menu = trigger.querySelector('.ed-photo-menu');
    menu.style.display = 'block';
  }
}

function pickPhoto(menuItem, useCamera, event) {
  if (event) { event.stopPropagation(); event.preventDefault(); }
  const trigger = menuItem.closest('.ed-photo-trigger');
  const row = trigger.closest('.ed-retur-row');
  const input = row.querySelector('.retur-photo-input');
  trigger.classList.remove('open');
  const menu = trigger.querySelector('.ed-photo-menu');
  if (menu) menu.style.display = 'none';

  if (!input) return;
  if (useCamera) input.setAttribute('capture', 'environment');
  else input.removeAttribute('capture');
  input.click();
}

function bindPhotoInput(row) {
  const input = row.querySelector('.retur-photo-input');
  const preview = row.querySelector('.ed-photo-preview');
  const removeFlag = row.querySelector('.retur-photo-remove');
  const trigger = row.querySelector('.ed-photo-trigger');
  if (!input) return;

  input.addEventListener('change', () => {
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
      const thumb = document.createElement('div');
      thumb.className = 'ed-photo-thumb';

      const img = document.createElement('img');
      img.src = URL.createObjectURL(input.files[0]);
      thumb.appendChild(img);

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'ed-photo-remove';
      removeBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
      removeBtn.onclick = (e) => { e.stopPropagation(); removePhoto(removeBtn); };
      thumb.appendChild(removeBtn);

      preview.appendChild(thumb);
      if (removeFlag) removeFlag.value = '0';
      if (trigger) trigger.classList.add('has-photo');
    }
  });
}

function removePhoto(btn) {
  const row = btn.closest('.ed-retur-row');
  const preview = row.querySelector('.ed-photo-preview');
  const input = row.querySelector('.retur-photo-input');
  const removeFlag = row.querySelector('.retur-photo-remove');
  const existing = row.querySelector('input[name="retur_photo_existing[]"]');
  const trigger = row.querySelector('.ed-photo-trigger');

  if (input) input.value = '';
  if (preview) preview.innerHTML = '';
  if (removeFlag) removeFlag.value = '1';
  if (existing) existing.value = '';
  if (trigger) trigger.classList.remove('has-photo');
}

// === GLOBAL HANDLER (works for new rows) ===
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.ed-photo-btn-main');
  if (btn) { togglePhotoMenu(btn, e); return; }

  const item = e.target.closest('.ed-photo-menu-item');
  if (item) {
    const useCamera = item.getAttribute('data-camera') === '1';
    pickPhoto(item, useCamera, e);
    return;
  }

  if (!e.target.closest('.ed-photo-trigger')) {
    document.querySelectorAll('.ed-photo-trigger.open').forEach(t => {
      t.classList.remove('open');
      const m = t.querySelector('.ed-photo-menu');
      if (m) m.style.display = 'none';
    });
  }
});

document.querySelectorAll('.ed-retur-row').forEach(bindPhotoInput);

document.addEventListener('input', e => {
  if (e.target?.classList.contains('retur-weight')) recalc();
});
window.addEventListener('pageshow', recalc);
document.addEventListener('DOMContentLoaded', recalc);
</script>

<style>
/* ── TOKENS ── */
:root{
  --ed-text:     #0D1117;
  --ed-muted:    #6B7896;
  --ed-border:   #E2E5EE;
  --ed-surface:  #FFFFFF;
  --ed-bg:       #F0F2F7;
  --ed-accent:   #E85D2F;
  --ed-acc-hv:   #D04A1E;
  --ed-acc-xl:   rgba(232,93,47,.09);
  --ed-red:      #EF4444;
  --ed-red-xl:   rgba(239,68,68,.08);
  --ed-orange:   #F59F00;
  --ed-ora-xl:   rgba(245,159,0,.10);
  --ed-green:    #10B981;
  --ed-grn-xl:   rgba(16,185,129,.10);
  --ed-r:        14px;
  --ed-sh:       0 1px 4px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);
}

.ed-retur-actions { margin-top: 10px; display:flex; }
.ed-btn-add-bottom {
  display:inline-flex; align-items:center; gap:6px;
  padding:8px 14px;
  border:1.5px solid rgba(232,93,47,.3); border-radius:9px;
  background:var(--ed-acc-xl); color:var(--ed-accent);
  font-size:.8rem; font-weight:700; cursor:pointer;
  transition:all .15s;
}
.ed-btn-add-bottom:hover:not(:disabled){ background:var(--ed-accent); color:#fff; border-color:var(--ed-accent); }
.ed-btn-add-bottom:disabled{ opacity:.4; cursor:not-allowed; }

.ed-photo { display:flex; flex-direction:column; gap:6px; }

/* ── PHOTO TRIGGER (1 button + dropdown) ── */
.ed-photo-trigger { position: relative; display: inline-block; }

.ed-photo-btn-main {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 7px 11px;
  border-radius: 9px;
  border: 1.5px solid rgba(16,185,129,.35);
  background: rgba(16,185,129,.07);
  color: #059669;
  font-size: .75rem; font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  transition: all .15s;
}
.ed-photo-btn-main:hover { background: #059669; color: #fff; border-color: #059669; }
.ed-photo-trigger.has-photo .ed-photo-btn-main {
  border-color: rgba(16,185,129,.5);
  background: rgba(16,185,129,.12);
}
.ed-caret { transition: transform .15s; flex-shrink: 0; }
.ed-photo-trigger.open .ed-caret { transform: rotate(180deg); }

/* dropdown menu */
.ed-photo-menu {
  display: none;
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  min-width: 180px;
  background: #fff;
  border: 1.5px solid var(--ed-border);
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,.12);
  z-index: 9999;
  overflow: hidden;
  animation: menuIn .12s ease;
}

@keyframes menuIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
.ed-photo-trigger.open .ed-photo-menu { display: block; }

.ed-photo-menu-item {
  display: flex; align-items: center; gap: 9px;
  width: 100%; padding: 10px 14px;
  border: none; background: transparent;
  font-size: .8rem; font-weight: 600; color: var(--ed-text);
  cursor: pointer; text-align: left;
  transition: background .12s;
}
.ed-photo-menu-item:hover { background: #F3F4F8; }
.ed-photo-menu-item + .ed-photo-menu-item { border-top: 1px solid var(--ed-border); }
.ed-photo-menu-item svg { flex-shrink: 0; color: var(--ed-muted); }

/* Thumbnail */
.ed-photo-preview { display: flex; flex-wrap: wrap; gap: 6px; }
.ed-photo-thumb {
  position: relative; display: inline-block;
}
.ed-photo-thumb img {
  width: 64px; height: 64px; object-fit: cover;
  border-radius: 8px; border: 1.5px solid var(--ed-border);
  display: block;
}
.ed-photo-remove {
  position: absolute; top: -5px; right: -5px;
  width: 18px; height: 18px;
  border-radius: 50%;
  border: 1.5px solid #ef4444;
  background: #fff; color: #ef4444;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  padding: 0;
  transition: all .15s;
}
.ed-photo-remove:hover { background: #ef4444; color: #fff; }

/* ── LAYOUT ── */
.ed-wrap { max-width: 1240px; margin: 0 auto; padding: 32px 24px; }

/* ── BREADCRUMB ── */
.ed-bc {
  display: flex; align-items: center; gap: 7px;
  font-size: .78rem; color: var(--ed-muted);
  margin-bottom: 22px;
}
.ed-bc-link {
  display: inline-flex; align-items: center; gap: 5px;
  color: var(--ed-accent); text-decoration: none; font-weight: 600;
  transition: opacity .15s;
}
.ed-bc-link:hover { opacity: .7; }
.ed-bc > svg { color: #C5CAD8; }

/* ── HEADER ── */
.ed-header {
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
}
.ed-header-left { display: flex; align-items: center; gap: 16px; }
.ed-icon {
  width: 52px; height: 52px; flex-shrink: 0;
  background: var(--ed-acc-xl); color: var(--ed-accent);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
}
.ed-title { font-size: 1.45rem; font-weight: 800; color: var(--ed-text); margin: 0 0 5px; letter-spacing: -.01em; }
.ed-meta {
  display: flex; align-items: center; gap: 7px; flex-wrap: wrap;
  font-size: .82rem; color: var(--ed-muted); font-weight: 600;
}
.ed-chip {
  font-family: 'Fira Code','Courier New',monospace;
  background: #F3F4F8; color: #4B5563;
  padding: 2px 10px; border-radius: 7px; font-size: .76rem;
}
.ed-sep { color: #C5CAD8; }
.ed-btn-back {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 16px;
  border: 1.5px solid var(--ed-border); border-radius: 10px;
  background: var(--ed-surface); color: var(--ed-muted);
  text-decoration: none; font-size: .84rem; font-weight: 600;
  transition: all .15s;
}
.ed-btn-back:hover { border-color: #C5CAD8; color: var(--ed-text); }

/* ── DONE BANNER ── */
.ed-done-banner {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px;
  border-radius: 12px;
  background: var(--ed-grn-xl);
  border: 1.5px solid rgba(16,185,129,.25);
  color: #065F46; font-size: .85rem; font-weight: 700;
  margin-bottom: 18px;
}

/* ── GRID ── */
.ed-grid {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
@media (max-width: 760px) { .ed-grid { grid-template-columns: 1fr; } }

/* ── CARD ── */
.ed-card {
  background: var(--ed-surface);
  border: 1px solid var(--ed-border);
  border-radius: var(--ed-r);
  box-shadow: var(--ed-sh);
  display: flex; flex-direction: column;
}
.ed-card-head {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 18px;
  border-bottom: 1px solid var(--ed-border);
  background: #FAFBFD;
  border-radius: var(--ed-r) var(--ed-r) 0 0;
}
.ed-card-icon {
  width: 36px; height: 36px; flex-shrink: 0;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
}
.ed-icon-red    { background: var(--ed-red-xl); color: var(--ed-red); }
.ed-icon-orange { background: var(--ed-ora-xl); color: var(--ed-orange); }
.ed-card-title { font-size: .9rem; font-weight: 800; color: var(--ed-text); margin-bottom: 2px; }
.ed-card-sub   { font-size: .76rem; color: var(--ed-muted); font-weight: 600; }
.ed-card-body  { padding: 20px 18px; flex: 1; }

/* ── LABEL / HINT ── */
.ed-label { display: block; font-size: .82rem; font-weight: 700; color: var(--ed-text); margin-bottom: 10px; }
.ed-hint  { font-size: .76rem; color: var(--ed-muted); margin-top: 10px; }
.ed-error { color: var(--ed-red); font-size: .78rem; font-weight: 600; margin-top: 6px; }

/* ── BIG COUNTER (ayam mati) ── */
.ed-big-counter {
  display: flex; align-items: center; gap: 0;
  border: 1.5px solid var(--ed-border);
  border-radius: 12px; overflow: hidden; background: #fff;
  transition: border-color .18s, box-shadow .18s;
}
.ed-big-counter:focus-within { border-color: var(--ed-accent); box-shadow: 0 0 0 3px var(--ed-acc-xl); }
.ed-big-minus, .ed-big-plus {
  width: 52px; height: 52px; flex-shrink: 0;
  border: none; background: #F5F7FA;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: var(--ed-text);
  transition: background .15s;
}
.ed-big-minus:hover:not(:disabled) { background: #FDECEC; color: var(--ed-red); }
.ed-big-plus:hover:not(:disabled)  { background: #E6FAF5; color: var(--ed-green); }
.ed-big-minus:disabled, .ed-big-plus:disabled { opacity: .4; cursor: not-allowed; }
.ed-big-input {
  flex: 1; border: none; outline: none; background: transparent;
  text-align: center; font-size: 1.6rem; font-weight: 900;
  color: var(--ed-text); padding: 10px 0;
}
.ed-big-input:disabled { color: var(--ed-muted); }

/* ── RETUR LIST ── */
.ed-retur-list { display: flex; flex-direction: column; gap: 8px; }

/* ── RETUR ROW ── */
.ed-retur-row {
  display: grid; grid-template-columns: 28px 1fr auto 36px;
  align-items: start; gap: 8px;
}
.ed-row-new { opacity: 0; transform: translateY(-6px); transition: opacity .2s, transform .2s; }
.ed-row-visible { opacity: 1; transform: translateY(0); }
.ed-row-num {
  width: 24px; height: 24px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 6px; background: #F0F2F7;
  font-size: .7rem; font-weight: 900; color: var(--ed-muted);
}
.ed-input-wrap {
  display: flex; align-items: center;
  border: 1.5px solid var(--ed-border);
  border-radius: 10px; background: #FAFBFD;
  overflow: hidden;
  transition: border-color .18s, box-shadow .18s;
}
.ed-input-wrap:focus-within { border-color: var(--ed-accent); box-shadow: 0 0 0 3px var(--ed-acc-xl); background: #fff; }
.ed-input-icon { width: 38px; display: flex; align-items: center; justify-content: center; color: var(--ed-muted); flex-shrink: 0; }
.ed-input {
  flex: 1; border: none; outline: none; background: transparent;
  padding: 10px 0; font-size: .9rem; font-weight: 700; color: var(--ed-text);
  min-width: 0;
}
.ed-input:disabled { color: var(--ed-muted); }
.ed-input-suffix {
  padding: 0 12px; font-size: .76rem; font-weight: 800;
  color: var(--ed-muted); background: #F0F2F7;
  border-left: 1px solid var(--ed-border);
  align-self: stretch; display: flex; align-items: center;
}
.ed-btn-remove {
  width: 34px; height: 34px;
  border: 1.5px solid var(--ed-border); border-radius: 9px;
  background: #fff; color: var(--ed-muted);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: all .15s;
}
.ed-btn-remove:hover:not(:disabled) { background: var(--ed-red); border-color: var(--ed-red); color: #fff; }
.ed-btn-remove:disabled { opacity: .35; cursor: not-allowed; }

/* ── ADD BUTTON ── */
.ed-btn-add {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; margin-left: auto;
  border: 1.5px solid rgba(232,93,47,.3); border-radius: 9px;
  background: var(--ed-acc-xl); color: var(--ed-accent);
  font-size: .8rem; font-weight: 700;
  cursor: pointer;
  transition: all .15s;
}
.ed-btn-add:hover:not(:disabled) { background: var(--ed-accent); color: #fff; border-color: var(--ed-accent); }
.ed-btn-add:disabled { opacity: .4; cursor: not-allowed; }

/* ── SUMMARY STRIP ── */
.ed-summary {
  display: flex; align-items: stretch;
  border-top: 1px solid var(--ed-border);
  background: #FAFBFD;
}
.ed-summary-item {
  flex: 1; padding: 14px 18px;
  display: flex; flex-direction: column; gap: 3px;
}
.ed-summary-divider { width: 1px; background: var(--ed-border); }
.ed-summary-label { font-size: .74rem; color: var(--ed-muted); font-weight: 600; }
.ed-summary-val   { display: flex; align-items: baseline; gap: 4px; }
.ed-summary-val span:first-child { font-size: 1.3rem; font-weight: 900; color: var(--ed-accent); }
.ed-summary-unit  { font-size: .76rem; color: var(--ed-muted); font-weight: 600; }

/* ── FOOTER ── */
.ed-footer {
  display: flex; align-items: center; justify-content: flex-end; gap: 10px;
  padding: 16px 0 0;
}
.ed-btn-cancel {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 10px 20px;
  border: 1.5px solid var(--ed-border); border-radius: 10px;
  background: var(--ed-surface); color: var(--ed-muted);
  text-decoration: none; font-size: .85rem; font-weight: 600;
  transition: all .15s;
}
.ed-btn-cancel:hover { border-color: #C5CAD8; color: var(--ed-text); }
.ed-btn-save {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 22px;
  border: none; border-radius: 10px;
  background: var(--ed-accent); color: #fff;
  font-size: .85rem; font-weight: 700;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(232,93,47,.3);
  transition: all .18s;
}
.ed-btn-save:hover:not(:disabled) { background: var(--ed-acc-hv); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(232,93,47,.35); }
.ed-btn-save:active { transform: translateY(0); }
.ed-btn-save:disabled { opacity: .45; cursor: not-allowed; }
</style>
@endsection