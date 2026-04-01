@php
  /** @var \Illuminate\Support\Collection $rows */
  $domId = 'done-' . $key;
@endphp

@if($rows->isNotEmpty())
  <div class="dn-wrap" id="{{ $domId }}">
    <button type="button" class="dn-toggle" onclick="toggleDone('{{ $domId }}')">
      <span class="dn-toggle-left">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Selesai (DONE)
      </span>
      <span class="dn-count">{{ $rows->count() }}</span>
      <svg class="dn-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
    </button>

    <div class="dn-body" id="{{ $domId }}-body" style="display:none">
      @foreach($rows as $it)
        <div class="dn-row">
          <span class="dn-num">#{{ $it->truck_no ?? '–' }}</span>
          <code class="dn-code">{{ $it->report_code }}</code>
          @if($it->size)
            <span class="dn-size">{{ $it->size }}</span>
          @endif
          <span class="dn-meta">{{ $it->plateNumber?->plate_number ?? '–' }}</span>
          <span class="dn-meta">{{ $it->expedition?->name ?? '–' }}</span>
          <span class="dn-meta">{{ $it->farm?->name ?? '–' }}</span>
        </div>
      @endforeach
    </div>
  </div>
@endif

<script>
function toggleDone(id) {
  const wrap   = document.getElementById(id);
  const body   = document.getElementById(id + '-body');
  const chev   = wrap.querySelector('.dn-chevron');
  const isOpen = body.style.display !== 'none';

  if (isOpen) {
    body.style.opacity   = '0';
    body.style.transform = 'translateY(-4px)';
    setTimeout(() => {
      body.style.display = 'none';
      body.style.opacity = '';
      body.style.transform = '';
    }, 180);
    wrap.classList.remove('dn-open');
  } else {
    body.style.display   = 'flex';
    body.style.opacity   = '0';
    body.style.transform = 'translateY(-4px)';
    requestAnimationFrame(() => {
      body.style.transition = 'opacity .18s, transform .18s';
      body.style.opacity    = '1';
      body.style.transform  = 'translateY(0)';
    });
    wrap.classList.add('dn-open');
  }

  chev.style.transform = isOpen ? '' : 'rotate(180deg)';
}
</script>

<style>
/* ── DONE SECTION ── */
.dn-wrap { margin-top: 10px; }

.dn-toggle {
  width: 100%;
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px;
  border: 1.5px dashed #D1D5E0;
  border-radius: 10px;
  background: transparent;
  cursor: pointer;
  font-size: .78rem; font-weight: 700; color: #9CA3AF;
  transition: border-color .15s, background .15s, color .15s;
}
.dn-toggle:hover {
  border-color: #10B981;
  background: rgba(16,185,129,.04);
  color: #065F46;
}
.dn-open .dn-toggle {
  border-style: solid;
  border-color: rgba(16,185,129,.3);
  background: rgba(16,185,129,.05);
  color: #065F46;
  border-radius: 10px 10px 0 0;
  border-bottom-color: transparent;
}
.dn-toggle-left { display:flex; align-items:center; gap:6px; flex:1; }
.dn-toggle-left svg { color: #10B981; }
.dn-count {
  display:inline-flex; align-items:center; justify-content:center;
  min-width:20px; height:20px; padding:0 5px;
  border-radius:6px;
  background:rgba(16,185,129,.12); color:#065F46;
  font-size:.7rem; font-weight:900;
}
.dn-chevron { flex-shrink:0; transition:transform .18s; }

/* ── DONE BODY ── */
.dn-body {
  flex-direction:column; gap:6px;
  padding:10px 12px;
  border:1.5px solid rgba(16,185,129,.25);
  border-top:none;
  border-radius:0 0 10px 10px;
  background:rgba(16,185,129,.03);
}

/* ── DONE ROW ── */
.dn-row {
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
  padding:8px 10px;
  border:1px solid rgba(16,185,129,.15);
  border-radius:9px;
  background:#fff;
}
.dn-num {
  padding:2px 9px; border-radius:999px;
  background:rgba(16,185,129,.12); color:#065F46;
  font-size:.74rem; font-weight:900;
  border:1px solid rgba(16,185,129,.2);
}
.dn-code {
  font-family:'Fira Code','Courier New',monospace;
  background:#F3F4F8; color:#4B5563;
  padding:2px 8px; border-radius:6px;
  font-size:.74rem;
}
.dn-size {
  padding:2px 8px; border-radius:999px;
  background:rgba(79,103,255,.1); color:#3730A3;
  font-size:.68rem; font-weight:900;
  border:1px solid rgba(79,103,255,.2);
}
.dn-meta { color:#9CA3AF; font-size:.76rem; font-weight:700; }
</style>