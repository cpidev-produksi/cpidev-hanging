@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

  .du-root { font-family:'Plus Jakarta Sans',sans-serif; background:#f8fafc; min-height:100vh; padding:32px; }
  .du-wrap { max-width:760px; margin:0 auto; }

  .du-back { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#64748b; text-decoration:none; margin-bottom:16px; }
  .du-title { font-size:22px; font-weight:800; color:#0f172a; letter-spacing:-.2px; margin-bottom:4px; }
  .du-sub { font-size:13px; color:#64748b; margin-bottom:24px; }

  .du-card { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:28px; }

  .du-field { margin-bottom:18px; }
  .du-field label { display:block; font-size:12px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
  .du-field select, .du-field input { width:100%; border:1px solid #e2e8f0; border-radius:12px; padding:11px 14px; font-family:inherit; font-size:14px; color:#0f172a; background:#fff; }
  .du-field select:focus, .du-field input:focus { outline:none; border-color:#1a56db; box-shadow:0 0 0 3px #dbeafe; }
  .du-field input[readonly] { background:#f8fafc; color:#475569; }
  .du-error { color:#dc2626; font-size:12px; font-weight:600; margin-top:4px; }

  .du-grid-2 { display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:14px; }

  .du-section-label { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#1a56db; margin:22px 0 12px; }

  .du-btn { display:inline-flex; align-items:center; gap:6px; padding:12px 20px; border-radius:12px; font-weight:700; font-size:14px; text-decoration:none; border:none; cursor:pointer; }
  .du-btn-primary { background:#1a56db; color:#fff; width:100%; justify-content:center; }
  .du-btn-primary:hover { background:#1743ab; }

  .du-hint { font-size:12px; color:#94a3b8; margin-top:6px; }

  /* ===== Scroll to top ===== */
  .du-scrolltop {
    position:fixed; right:24px; bottom:24px; width:46px; height:46px; border-radius:50%;
    background:#1a56db; color:#fff; border:none; padding:0;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; box-shadow:0 10px 24px rgba(26,86,219,.35); z-index:60;
    opacity:0; visibility:hidden; transform:translateY(10px);
    transition:opacity .2s ease, transform .2s ease, visibility .2s ease;
  }
  .du-scrolltop.show { opacity:1; visibility:visible; transform:translateY(0); }
  .du-scrolltop svg { width:20px; height:20px; stroke:#fff; fill:none; stroke-width:2.5; display:block; }
</style>

<div class="du-root">
  <div class="du-wrap">

    <a href="{{ route('daily-uniformities.index') }}" class="du-back">&larr; Kembali ke Daftar</a>
    <div class="du-title">Buat Laporan Daily Uniformity</div>
    <div class="du-sub">Pilih No. SPPA yang sudah terdaftar di Kontrol Monitor, data terkait akan terisi otomatis.</div>

    <div class="du-card">
      <form action="{{ route('daily-uniformities.store') }}" method="POST">
        @csrf

        <div class="du-field">
          <label for="monitor_control_id">No. SPPA</label>
          <select id="monitor_control_id" name="monitor_control_id" onchange="duFillFromMc(this)" required>
            <option value="">-- Pilih No. SPPA --</option>
            @foreach ($monitorControls as $mc)
              <option
                value="{{ $mc->id }}"
                data-date="{{ optional($mc->process_date)->format('d-m-Y') }}"
                data-shift="{{ ucfirst($mc->shift) }}"
                data-sppa="{{ $mc->sppa_no }}"
                data-farm="{{ $mc->farm->name ?? '-' }}"
                data-plate="{{ $mc->plateNumber->plate_number ?? '-' }}"
                data-ekspedisi="{{ $mc->expedition->name ?? '-' }}"
                data-sopir="{{ $mc->plateNumber->driver_name ?? '-' }}"
                data-abw="{{ $mc->abw ?? '-' }}"
                data-size="{{ $mc->size }}"
                {{ old('monitor_control_id') == $mc->id ? 'selected' : '' }}
              >
                {{ $mc->sppa_no }} — {{ $mc->report_code }} ({{ optional($mc->process_date)->format('d/m/Y') }} · {{ ucfirst($mc->shift) }})
              </option>
            @endforeach
          </select>
          @error('monitor_control_id') <div class="du-error">{{ $message }}</div> @enderror
          <div class="du-hint">Hanya menampilkan Kontrol Monitor yang punya No. SPPA dan belum punya laporan uniformity.</div>
        </div>

        <div class="du-section-label">Data Otomatis</div>

        <div class="du-grid-2">
          <div class="du-field">
            <label>Tanggal</label>
            <input type="text" id="du_date" readonly placeholder="-">
          </div>
          <div class="du-field">
            <label>Shift</label>
            <input type="text" id="du_shift" readonly placeholder="-">
          </div>
          <div class="du-field">
            <label>Nama Farm</label>
            <input type="text" id="du_farm" readonly placeholder="-">
          </div>
          <div class="du-field">
            <label>No. Polisi</label>
            <input type="text" id="du_plate" readonly placeholder="-">
          </div>
          <div class="du-field">
            <label>Ekspedisi</label>
            <input type="text" id="du_ekspedisi" readonly placeholder="-">
          </div>
          <div class="du-field">
            <label>Sopir</label>
            <input type="text" id="du_sopir" readonly placeholder="-">
          </div>
          <div class="du-field">
            <label>ABW</label>
            <input type="text" id="du_abw" readonly placeholder="-">
          </div>
          <div class="du-field">
            <label>Uniformity (Size Ayam)</label>
            <input type="text" id="du_size" readonly placeholder="-">
          </div>
        </div>

        <div class="du-section-label">Input Manual</div>

        <div class="du-grid-2">
          <div class="du-field">
            <label for="avg_rpa">Rata-rata RPA</label>
            <input type="number" step="0.01" min="0" id="avg_rpa" name="avg_rpa" value="{{ old('avg_rpa') }}" placeholder="Contoh: 2.45">
            @error('avg_rpa') <div class="du-error">{{ $message }}</div> @enderror
          </div>
          <div class="du-field">
            <label for="berat_rpa">Berat RPA</label>
            <input type="number" step="0.01" min="0" id="berat_rpa" name="berat_rpa" value="{{ old('berat_rpa') }}" placeholder="Contoh: 1.80">
            @error('berat_rpa') <div class="du-error">{{ $message }}</div> @enderror
          </div>
        </div>

        <button type="submit" class="du-btn du-btn-primary">Simpan &amp; Lanjut Input Berat Sampling</button>
      </form>
    </div>

  </div>
</div>

<button type="button" class="du-scrolltop" id="duScrollTop" title="Kembali ke atas">
  <svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<script>
  function duFillFromMc(select) {
    const opt = select.options[select.selectedIndex];
    const map = {
      du_date: 'date', du_shift: 'shift', du_farm: 'farm', du_plate: 'plate',
      du_ekspedisi: 'ekspedisi', du_sopir: 'sopir', du_abw: 'abw', du_size: 'size',
    };
    Object.entries(map).forEach(([id, key]) => {
      document.getElementById(id).value = opt.dataset[key] || '-';
    });
  }

  (function () {
    const btn = document.getElementById('duScrollTop');
    if (!btn) return;
    window.addEventListener('scroll', function () {
      if (window.scrollY > 280) {
        btn.classList.add('show');
      } else {
        btn.classList.remove('show');
      }
    });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  })();
</script>
@endsection