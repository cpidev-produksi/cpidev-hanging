@extends('layouts.app')

@section('content')
<div class="sum-page">

  {{-- ══ HEADER ══ --}}
  <div class="sum-header">
    <div class="sum-header-left">
      <div class="sum-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="9" y1="13" x2="15" y2="13"/>
          <line x1="9" y1="17" x2="13" y2="17"/>
        </svg>
      </div>
      <div>
        <h1 class="sum-title">Data Summary</h1>
        <div class="sum-meta">
          <span class="sum-chip code">{{ $mc->report_code }}</span>
          <span class="sum-dot">·</span>
          <span class="sum-meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 21s7-4.5 7-10a7 7 0 0 0-14 0c0 5.5 7 10 7 10z"/><circle cx="12" cy="11" r="2"/></svg>
            {{ $mc->location }}
          </span>
          <span class="sum-dot">·</span>
          <span class="sum-meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Truk #{{ $mc->truck_no }}
          </span>
        </div>
      </div>
    </div>
    <div class="sum-actions">
      @if($mc->supervisor_signature)
        <a class="sum-btn-export" target="_blank" href="{{ route('monitor-controls.summary.pdf', $mc) }}">
          ... Export PDF ...
        </a>
      @else
        <button class="sum-btn-export" type="button" disabled style="opacity:.55;cursor:not-allowed">
          ... Export PDF (butuh tanda tangan) ...
        </button>
      @endif
      <a class="sum-btn-back" href="{{ route('monitor-controls.index') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
      </a>
    </div>
  </div>

  {{-- ══ MAIN GRID ══ --}}
  <div class="sum-grid">

    {{-- Kolom Kiri: Kontrol Monitor --}}
    <div class="sum-card">
      <div class="sum-card-head">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 8h8"/><path d="M8 12h5"/></svg>
        DTA
      </div>

      <div class="sum-kv-group">
        <div class="sum-kv">
          <span>Tanggal</span>
          <b>{{ $mc->process_date?->format('d/m/Y') ?? '—' }}</b>
        </div>
        <div class="sum-kv">
          <span>Shift</span>
          <b><span class="sum-chip shift">{{ strtoupper($mc->shift) }}</span></b>
        </div>
        <div class="sum-kv">
          <span>Size</span>
          <b><span class="sum-chip size">{{ $mc->size }}</span></b>
        </div>
        <div class="sum-kv">
          <span>Farm</span>
          <b>{{ $mc->farm?->name ?? '—' }}</b>
        </div>
        <div class="sum-kv">
          <span>Ekspedisi</span>
          <b>{{ $mc->expedition?->name ?? '—' }}</b>
        </div>
        <div class="sum-kv">
          <span>No Polisi</span>
          <b><span class="sum-chip plate">{{ $mc->plateNumber?->plate_number ?? '—' }}</span></b>
        </div>
        <div class="sum-kv">
          <span>No Segel</span>
          <b>{{ $mc->seal_no ?? '—' }}</b>
        </div>
        <div class="sum-kv">
          <span>Jam Truk Datang</span>
          <b>
            @if($mc->truck_arrival_time)
              @if(is_string($mc->truck_arrival_time))
                {{ \Carbon\Carbon::parse($mc->truck_arrival_time)->format('H:i') }}
              @else
                {{ $mc->truck_arrival_time->format('H:i') }}
              @endif
            @else
              —
            @endif
          </b>
        </div>
        <div class="sum-kv">
          <span>Tgl Tangkap</span>
          <b>{{ $mc->catch_date?->format('d/m/Y') ?? '—' }}</b>
        </div>
      </div>

      <div class="sum-divider"></div>

      <div class="sum-stats-row">
        <div class="sum-stat">
          <div class="sum-stat-val">{{ number_format((int)($mc->total_chicken ?? 0)) }}</div>
          <div class="sum-stat-label">Total Ekor</div>
        </div>
        <div class="sum-stat">
          <div class="sum-stat-val">{{ number_format((float)($mc->total_kilo ?? 0), 2) }}</div>
          <div class="sum-stat-label">Total Kilo (Kg)</div>
        </div>
        <div class="sum-stat">
          <div class="sum-stat-val">{{ number_format((float)($mc->abw ?? 0), 2) }}</div>
          <div class="sum-stat-label">ABW</div>
        </div>
      </div>

      <div class="sum-divider"></div>

      <div class="sum-kv-group">
        <div class="sum-kv">
          <span>No SPPA</span>
          <b>{{ $mc->sppa_no ?? '—' }}</b>
        </div>
        <div class="sum-kv">
          <span>Order ID</span>
          <b>{{ $mc->order_id ?? '—' }}</b>
        </div>
        <div class="sum-kv">
          <span>Tanggal SPPA</span>
          <b>{{ $mc->sppa_date?->format('d/m/Y') ?? '—' }}</b>
        </div>
      </div>
    </div>

    {{-- Kolom Kanan --}}
    <div class="sum-col-right">

      {{-- Hanging --}}
      <div class="sum-card">
        <div class="sum-card-head">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="2" x2="12" y2="6"/><path d="M12 6a6 6 0 0 1 6 6v6H6v-6a6 6 0 0 1 6-6z"/></svg>
          Hanging
        </div>
        <div class="sum-kv-group">
          <div class="sum-kv">
            <span>Jam Bongkar</span>
            <b>{{ $form->unloading_time?->format('H:i') ?? '—' }}</b>
          </div>
          <div class="sum-kv">
            <span>Jam Selesai</span>
            <b>{{ $form->finish_time?->format('H:i') ?? '—' }}</b>
          </div>
          <div class="sum-kv">
            <span>Total Shackle Kosong</span>
            <b class="sum-num">{{ $totalKosong }}</b>
          </div>
          <div class="sum-kv">
            <span>Jumlah Ayam Diterima</span>
            <b class="sum-num">{{ $totalAyamTerima }}</b>
          </div>
        </div>
      </div>

      {{-- Retur & Mati --}}
      <div class="sum-card">
        <div class="sum-card-head retur">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.8"/></svg>
          Ayam Retur &amp; Mati
        </div>
        <div class="sum-retur-row">
          <div class="sum-retur-item dead">
            <div class="sum-retur-val">{{ (int)($form->dead_count ?? 0) }}</div>
            <div class="sum-retur-label">Ayam Mati</div>
          </div>
          <div class="sum-retur-item retur">
            <div class="sum-retur-val">{{ (int)($form->retur_count ?? 0) }}</div>
            <div class="sum-retur-label">Ayam Retur</div>
          </div>
          <div class="sum-retur-item kg">
            <div class="sum-retur-val">{{ number_format((float)($form->retur_total_kg ?? 0), 2) }}</div>
            <div class="sum-retur-label">Berat Retur (Kg)</div>
          </div>
        </div>
      </div>

      {{-- QC Kondisi --}}
      <div class="sum-card">
        <div class="sum-card-head qc">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
          QC Kondisi
        </div>
        <div class="sum-qc-row">
          <div class="sum-qc-item">
            <div class="sum-qc-label">Keranjang</div>
            <div class="sum-qc-val">{{ $form->basket_condition }}</div>
          </div>
          <div class="sum-qc-item">
            <div class="sum-qc-label">Platform Truck</div>
            <div class="sum-qc-val">{{ $form->truck_platform_condition }}</div>
          </div>
          <div class="sum-qc-item">
            <div class="sum-qc-label">Bulu Ayam</div>
            <div class="sum-qc-val">{{ $form->feather_condition }}</div>
          </div>
        </div>
      </div>

    </div>{{-- /col-right --}}
  </div>{{-- /grid --}}

  {{-- ══ SIGNATURE ══ --}}
  {{-- <div class="sum-sig">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    Dibuat oleh: <strong>{{ $createdBy }}</strong>
  </div> --}}

  {{-- ══ SUPERVISOR SIGNATURE ══ --}}
  <div class="sum-card" style="margin-top:14px">
    <div class="sum-card-head qc">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
      </svg>
      Approval Supervisor (Tanda Tangan)
    </div>

    @if($mc->supervisor_signature)
      <div style="display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap">
        <div>
          <div style="font-weight:900;margin-bottom:8px">Tanda tangan tersimpan</div>
          <img src="{{ $mc->supervisor_signature }}" alt="Signature" style="border:1px solid #E8EAF0;border-radius:12px;width:320px;max-width:100%;background:#fff">
          <div style="margin-top:8px;color:#6B7280;font-weight:800;font-size:.82rem">
            Nama: <b>{{ $mc->supervisor_signed_name ?? '—' }}</b><br>
            Waktu: <b>{{ $mc->supervisor_signed_at?->format('d/m/Y H:i') ?? '—' }}</b>
          </div>
        </div>

        <form method="POST" action="{{ route('monitor-controls.summary.unsign', $mc) }}"
              onsubmit="return confirm('Hapus tanda tangan supervisor?')">
          @csrf
          @method('DELETE')
          <button class="sum-btn-back" type="submit">Hapus Tanda Tangan</button>
        </form>
      </div>
    @else
      <div style="color:#6B7280;font-weight:800;margin-bottom:10px">
        Silahkan supervisor tanda tangan terlebih dahulu. Export PDF akan aktif setelah tanda tangan tersimpan.
      </div>

      <div style="border:1.5px dashed #D1D5DB;border-radius:14px;padding:12px;background:#FAFBFD;max-width:520px">
        <canvas id="sigPad" width="500" height="200" style="width:100%;height:auto;background:#fff;border-radius:12px;border:1px solid #E8EAF0"></canvas>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;align-items:center">
          <input id="signedName" type="text" placeholder="Nama supervisor (opsional)"
                style="flex:1;min-width:220px;padding:10px 12px;border:1.5px solid #E5E7EB;border-radius:10px;font-weight:800">

          <button type="button" class="sum-btn-back" onclick="clearSig()">Clear</button>

          <form id="sigForm" method="POST" action="{{ route('monitor-controls.summary.sign', $mc) }}">
            @csrf
            <input type="hidden" name="signature" id="signatureInput">
            <input type="hidden" name="signed_name" id="signedNameInput">
            <button type="button" class="sum-btn-export" onclick="submitSig()">Simpan Tanda Tangan</button>
          </form>
        </div>
      </div>
    @endif
  </div>
</div>

<script>
(function(){
  const canvas = document.getElementById('sigPad');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  ctx.lineWidth = 2.2;
  ctx.lineCap = 'round';
  ctx.strokeStyle = '#111827';

  let drawing = false;

  function getPos(e){
    const r = canvas.getBoundingClientRect();
    const x = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
    const y = (e.touches ? e.touches[0].clientY : e.clientY) - r.top;
    // scale for CSS size vs actual size
    return {
      x: x * (canvas.width / r.width),
      y: y * (canvas.height / r.height),
    };
  }

  function start(e){ drawing = true; const p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); e.preventDefault(); }
  function move(e){ if(!drawing) return; const p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); e.preventDefault(); }
  function end(){ drawing = false; }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);

  canvas.addEventListener('touchstart', start, {passive:false});
  canvas.addEventListener('touchmove', move, {passive:false});
  canvas.addEventListener('touchend', end);

  window.clearSig = function(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
  }

  window.submitSig = function(){
    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById('signatureInput').value = dataUrl;
    document.getElementById('signedNameInput').value = document.getElementById('signedName').value || '';
    document.getElementById('sigForm').submit();
  }
})();
</script>

<style>
/* ── PAGE ── */
.sum-page { max-width: 1120px; margin: 0 auto; padding: 24px 18px 32px; }

/* ── HEADER ── */
.sum-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  gap: 12px; flex-wrap: wrap; margin-bottom: 18px;
}
.sum-header-left { display: flex; align-items: center; gap: 14px; }
.sum-icon {
  width: 46px; height: 46px; border-radius: 13px; flex-shrink: 0;
  background: linear-gradient(135deg, #064e3b, #059669);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
}
.sum-icon svg { width: 22px; height: 22px; }
.sum-title { font-size: 1.3rem; font-weight: 900; margin: 0 0 5px; color: #111827; }
.sum-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.sum-dot { color: #D1D5DB; font-weight: 700; }
.sum-meta-item { display: flex; align-items: center; gap: 4px; color: #6B7280; font-size: .82rem; font-weight: 700; }
.sum-meta-item svg { color: #9CA3AF; }

/* chips */
.sum-chip {
  display: inline-flex; align-items: center;
  padding: 2px 9px; border-radius: 999px;
  font-size: .74rem; font-weight: 800;
}
.sum-chip.code {
  background: #F3F4F8; color: #374151;
  font-family: 'Fira Code', 'Courier New', monospace;
  border: 1px solid #E5E7EB; border-radius: 7px;
}
.sum-chip.shift { background: rgba(59,130,246,.1); color: #1D4ED8; border: 1px solid rgba(59,130,246,.2); }
.sum-chip.size { background: rgba(139,92,246,.1); color: #6D28D9; border: 1px solid rgba(139,92,246,.2); }
.sum-chip.plate { background: #F3F4F8; color: #374151; font-family: 'Fira Code', monospace; border: 1px solid #E5E7EB; }
.sum-chip.status-done { background: rgba(16,185,129,.1); color: #065F46; border: 1px solid rgba(16,185,129,.25); }

/* ── ACTIONS ── */
.sum-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.sum-btn-export {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 16px; border-radius: 10px; text-decoration: none;
  font-size: .82rem; font-weight: 800;
  background: linear-gradient(135deg, #059669 0%, #0ea5a0 100%);
  color: #fff;
  box-shadow: 0 2px 8px rgba(5,150,105,.3);
  transition: transform .13s, box-shadow .15s, filter .15s;
}
.sum-btn-export:hover {
  transform: translateY(-1px);
  box-shadow: 0 5px 14px rgba(5,150,105,.4);
  filter: brightness(1.06);
}
.sum-btn-back {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 16px; border-radius: 10px; text-decoration: none;
  font-size: .82rem; font-weight: 800;
  background: #fff; color: #6B7280;
  border: 1.5px solid #E5E7EB;
  transition: border-color .15s, color .15s, background .15s;
}
.sum-btn-back:hover { border-color: #9CA3AF; color: #374151; background: #F9FAFB; }

/* ── GRID ── */
.sum-grid {
  display: grid;
  grid-template-columns: 1.1fr .9fr;
  gap: 14px;
  align-items: start;
}
@media (max-width: 900px) { .sum-grid { grid-template-columns: 1fr; } }
.sum-col-right { display: flex; flex-direction: column; gap: 14px; }

/* ── CARD ── */
.sum-card {
  background: #fff;
  border: 1px solid #E8EAF0;
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}

/* card head */
.sum-card-head {
  display: flex; align-items: center; gap: 7px;
  font-size: .8rem; font-weight: 900; letter-spacing: .05em;
  text-transform: uppercase; color: #059669;
  margin-bottom: 12px;
  padding-bottom: 10px;
  border-bottom: 1.5px solid #D1FAE5;
}
.sum-card-head.retur { color: #DC2626; border-bottom-color: #FEE2E2; }
.sum-card-head.qc { color: #7C3AED; border-bottom-color: #EDE9FE; }

/* ── KV ROWS ── */
.sum-kv-group { display: flex; flex-direction: column; gap: 0; }
.sum-kv {
  display: flex; justify-content: space-between; align-items: center;
  gap: 12px; padding: 8px 0;
  border-bottom: 1px dashed rgba(229,231,235,.9);
  font-size: .83rem;
}
.sum-kv:last-child { border-bottom: none; }
.sum-kv span { color: #6B7280; font-weight: 700; flex-shrink: 0; }
.sum-kv b { color: #111827; font-weight: 800; text-align: right; }
.sum-num { color: #065F46 !important; font-weight: 900 !important; }

/* ── DIVIDER ── */
.sum-divider { height: 1px; background: #F3F4F6; margin: 12px 0; }

/* ── STATS ROW ── */
.sum-stats-row {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;
}
.sum-stat {
  background: #F0FDF7;
  border: 1px solid #D1FAE5;
  border-radius: 10px;
  padding: 10px 8px;
  text-align: center;
}
.sum-stat-val { font-size: 1.15rem; font-weight: 900; color: #065F46; line-height: 1.1; }
.sum-stat-label { font-size: .68rem; font-weight: 800; color: #6B7280; margin-top: 3px; text-transform: uppercase; letter-spacing: .05em; }

/* ── RETUR ROW ── */
.sum-retur-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.sum-retur-item {
  border-radius: 10px; padding: 10px 8px; text-align: center;
  border: 1px solid;
}
.sum-retur-item.dead { background: #FFF1F2; border-color: #FECDD3; }
.sum-retur-item.retur { background: #FFFBEB; border-color: #FDE68A; }
.sum-retur-item.kg { background: #F0F9FF; border-color: #BAE6FD; }
.sum-retur-val { font-size: 1.1rem; font-weight: 900; }
.sum-retur-item.dead .sum-retur-val { color: #B91C1C; }
.sum-retur-item.retur .sum-retur-val { color: #92400E; }
.sum-retur-item.kg .sum-retur-val { color: #0369A1; }
.sum-retur-label { font-size: .67rem; font-weight: 800; color: #6B7280; margin-top: 3px; text-transform: uppercase; letter-spacing: .04em; }

/* ── QC ROW ── */
.sum-qc-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.sum-qc-item {
  background: #F5F3FF; border: 1px solid #DDD6FE;
  border-radius: 10px; padding: 10px 8px; text-align: center;
}
.sum-qc-label { font-size: .67rem; font-weight: 800; color: #6B7280; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
.sum-qc-val { font-size: .88rem; font-weight: 900; color: #6D28D9; }

/* ── SIGNATURE ── */
.sum-sig {
  display: flex; align-items: center; gap: 7px;
  margin-top: 18px; padding-top: 14px;
  border-top: 1.5px solid #E8EAF0;
  color: #6B7280; font-size: .85rem; font-weight: 700;
}
.sum-sig strong { color: #111827; font-weight: 900; font-size: .9rem; }
.sum-sig svg { color: #9CA3AF; flex-shrink: 0; }
</style>
@endsection