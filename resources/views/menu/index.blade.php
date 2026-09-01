@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

  :root {
    --blue:       #1a56db;
    --blue-mid:   #2563eb;
    --blue-light: #dbeafe;
    --green:      #0d9488;
    --green-light:#ccfbf1;
    --text:       #0f172a;
    --muted:      #64748b;
    --border:     #e2e8f0;
    --bg:         #f8fafc;
    --surface:    #ffffff;
    --radius:     20px;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  .hp-root {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--bg);
    min-height: 100vh;
    overflow-x: hidden;
  }

  /* ════════════════════════════
    HERO
  ════════════════════════════ */
  .hp-hero {
    position: relative;
    padding: 72px 40px 80px;
    overflow: hidden;
    background: linear-gradient(135deg, #0f2860 0%, #1a3a8a 45%, #1e5fa0 100%);
  }

  /* animated mesh blobs */
  .hp-hero::before,
  .hp-hero::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: .45;
    animation: blobFloat 8s ease-in-out infinite alternate;
  }
  .hp-hero::before {
    width: 520px; height: 520px;
    background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
    top: -160px; right: -100px;
  }
  .hp-hero::after {
    width: 400px; height: 400px;
    background: radial-gradient(circle, #0d9488 0%, transparent 70%);
    bottom: -120px; left: 10%;
    animation-delay: -4s;
  }

  @keyframes blobFloat {
    0%   { transform: translate(0, 0) scale(1); }
    100% { transform: translate(30px, -20px) scale(1.08); }
  }

  /* dot grid overlay */
  .hp-hero-grid {
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.12) 1px, transparent 1px);
    background-size: 32px 32px;
    pointer-events: none;
  }

  .hp-hero-inner {
    position: relative;
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    flex-wrap: wrap;
  }

  .hp-hero-text { flex: 1; min-width: 280px; }

  .hp-chip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    backdrop-filter: blur(8px);
    border-radius: 100px;
    padding: 5px 14px 5px 8px;
    margin-bottom: 22px;
    animation: fadeUp .6s ease both;
  }

  .hp-chip-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #34d399;
    box-shadow: 0 0 0 3px rgba(52,211,153,.3);
    animation: pulse 2s ease-in-out infinite;
  }

  @keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(52,211,153,.3); }
    50%       { box-shadow: 0 0 0 6px rgba(52,211,153,.1); }
  }

  .hp-chip span {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: rgba(255,255,255,.9);
  }

  .hp-hero-title {
    font-size: clamp(28px, 4vw, 48px);
    font-weight: 900;
    color: #fff;
    line-height: 1.12;
    letter-spacing: -.5px;
    margin-bottom: 16px;
    animation: fadeUp .6s .1s ease both;
  }

  .hp-hero-title em {
    font-style: normal;
    background: linear-gradient(90deg, #7dd3fc, #34d399);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hp-hero-sub {
    font-size: 15px;
    font-weight: 500;
    color: rgba(255,255,255,.65);
    line-height: 1.7;
    max-width: 460px;
    margin-bottom: 32px;
    animation: fadeUp .6s .2s ease both;
  }

  .hp-hero-stats {
    display: flex;
    gap: 28px;
    flex-wrap: wrap;
    animation: fadeUp .6s .3s ease both;
  }

  .hp-stat {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .hp-stat-val {
    font-size: 22px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.3px;
  }

  .hp-stat-lbl {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: rgba(255,255,255,.45);
  }

  .hp-stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255,255,255,.15);
    align-self: center;
  }

  /* hero illustration */
  .hp-hero-visual {
    flex-shrink: 0;
    width: 280px;
    height: 200px;
    position: relative;
    animation: fadeUp .6s .25s ease both;
  }

  .hp-visual-card {
    position: absolute;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 16px;
    padding: 16px 20px;
    color: #fff;
  }

  .hp-visual-card.main {
    width: 220px;
    top: 10px; left: 0;
    animation: cardFloat 6s ease-in-out infinite;
  }

  .hp-visual-card.mini {
    width: 150px;
    bottom: 0; right: 0;
    animation: cardFloat 6s ease-in-out infinite reverse;
    animation-delay: -3s;
  }

  @keyframes cardFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-10px); }
  }

  .hp-vc-label { font-size: 10px; font-weight: 700; opacity: .6; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
  .hp-vc-val   { font-size: 28px; font-weight: 900; letter-spacing: -.5px; }
  .hp-vc-sub   { font-size: 11px; font-weight: 600; opacity: .55; margin-top: 4px; }

  .hp-vc-bar { height: 4px; border-radius: 4px; background: rgba(255,255,255,.15); margin-top: 12px; }
  .hp-vc-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg,#7dd3fc,#34d399); width: 72%; }

  /* ════════════════════════════
    SECTION
  ════════════════════════════ */
  .hp-section {
    max-width: 1100px;
    margin: 0 auto;
    padding: 48px 40px 64px;
  }

  .hp-section-head {
    margin-bottom: 28px;
    animation: fadeUp .5s ease both;
  }

  .hp-section-label {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--blue-mid);
    margin-bottom: 6px;
  }

  .hp-section-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -.2px;
  }

  /* ════════════════════════════
    CARDS GRID
  ════════════════════════════ */
  .hp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
  }

  .hp-card {
    position: relative;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 28px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    gap: 20px;
    overflow: hidden;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    animation: fadeUp .5s ease both;
    box-shadow: 0 1px 3px rgba(15,23,42,.05);
  }

  .hp-card:nth-child(2) { animation-delay: .08s; }

  .hp-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(15,23,42,.1);
    border-color: transparent;
  }

  /* Colored left rail on hover */
  .hp-card::before {
    content: '';
    position: absolute;
    left: 0; top: 20%; bottom: 20%;
    width: 3px;
    border-radius: 0 3px 3px 0;
    opacity: 0;
    transition: opacity .22s ease;
  }

  .hp-card:hover::before { opacity: 1; }

  .hp-card.blue::before  { background: var(--blue-mid); }
  .hp-card.green::before { background: var(--green); }

  /* top shimmer on hover */
  .hp-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0) 60%, rgba(255,255,255,.6) 100%);
    opacity: 0;
    transition: opacity .22s ease;
    pointer-events: none;
  }

  .hp-card:hover::after { opacity: 1; }

  /* top accent bar */
  .hp-card-bar {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: var(--radius) var(--radius) 0 0;
  }

  .hp-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
  }

  .hp-card-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
  }

  .hp-card-icon::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.4), transparent);
  }

  .hp-card-icon svg {
    width: 24px; height: 24px;
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
    position: relative;
    z-index: 1;
  }

  .hp-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 100px;
  }

  .hp-card-body { flex: 1; }

  .hp-card-name {
    font-size: 17px;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 8px;
    letter-spacing: -.2px;
  }

  .hp-card-desc {
    font-size: 13px;
    font-weight: 500;
    color: var(--muted);
    line-height: 1.65;
  }

  .hp-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 16px;
    border-top: 1px solid var(--border);
  }

  .hp-card-tags {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .hp-tag {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 100px;
    border: 1px solid var(--border);
    color: var(--muted);
    background: var(--bg);
  }

  .hp-card-cta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    transition: gap .18s ease;
  }

  .hp-card:hover .hp-card-cta { gap: 10px; }

  .hp-card-cta svg {
    width: 14px; height: 14px;
    fill: none;
    stroke-width: 2.2;
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: transform .18s ease;
  }

  .hp-card:hover .hp-card-cta svg { transform: translateX(3px); }

  /* ════════════════════════════
    FEATURE STRIP
  ════════════════════════════ */
  .hp-strip {
    background: var(--surface);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 28px 40px;
  }

  .hp-strip-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    gap: 0;
    flex-wrap: wrap;
  }

  .hp-feat {
    flex: 1;
    min-width: 180px;
    padding: 12px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-right: 1px solid var(--border);
    animation: fadeUp .5s ease both;
  }

  .hp-feat:last-child { border-right: none; }
  .hp-feat:nth-child(2) { animation-delay: .07s; }
  .hp-feat:nth-child(3) { animation-delay: .14s; }
  .hp-feat:nth-child(4) { animation-delay: .21s; }

  .hp-feat-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .hp-feat-icon svg {
    width: 18px; height: 18px;
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .hp-feat-name {
    font-size: 12px;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 2px;
  }

  .hp-feat-sub {
    font-size: 11px;
    font-weight: 500;
    color: var(--muted);
  }

  /* ════════════════════════════
    ANIMATIONS
  ════════════════════════════ */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
  }
</style>

<div class="hp-root">

  {{-- ═══ HERO ═══ --}}
  <section class="hp-hero">
    <div class="hp-hero-grid"></div>
    <div class="hp-hero-inner">

      <div class="hp-hero-text">
        <div class="hp-chip">
          <span class="hp-chip-dot"></span>
          <span>Sistem Aktif &amp; Real-Time</span>
        </div>
        <h1 class="hp-hero-title">
          Paperless System Management<br>
          <em>Slaughter House</em> Dept.
        </h1>
        <p class="hp-hero-sub">
          Platform terpadu untuk monitoring produksi, pengelolaan inventaris, dan analisis data
          Slaughter House secara real-time.
        </p>
        <div class="hp-hero-stats">
          <div class="hp-stat">
            <span class="hp-stat-val">Real-Time</span>
            <span class="hp-stat-lbl">Monitoring</span>
          </div>
          <div class="hp-stat-divider"></div>
          <div class="hp-stat">
            <span class="hp-stat-val">3 Modul</span>
            <span class="hp-stat-lbl">Terintegrasi</span>
          </div>
          <div class="hp-stat-divider"></div>
          <div class="hp-stat">
            <span class="hp-stat-val">Live</span>
            <span class="hp-stat-lbl">Data</span>
          </div>
        </div>
      </div>

      {{-- floating visual --}}
      <div class="hp-hero-visual">
          <div class="hp-visual-card main">
              <div class="hp-vc-label">Produksi Hari Ini</div>
              <div class="hp-vc-val" id="ayamCount">{{ number_format($totalAyamDiterima ?? 0) }}</div>
              <div class="hp-vc-sub">
                  ekor · dari target {{ number_format($planChicken ?? 0) }}
              </div>
              <div class="hp-vc-bar">
                  <div class="hp-vc-fill" style="width: {{ $ayamProgress ?? 0 }}%"></div>
              </div>
          </div>
          <div class="hp-visual-card mini">
              <div class="hp-vc-label">Truk Masuk</div>
              <div class="hp-vc-val" id="trukCount">{{ number_format($totalTrukTerhitung ?? 0) }}</div>
              <div class="hp-vc-sub">
                  unit hari ini · dari {{ number_format($planTruck ?? 0) }}
              </div>
          </div>
      </div>

    </div>
  </section>

  {{-- ═══ FEATURE STRIP ═══ --}}
  <div class="hp-strip">
    <div class="hp-strip-inner">
      <div class="hp-feat">
        <div class="hp-feat-icon" style="background:#eff6ff">
          <svg viewBox="0 0 24 24" stroke="#2563eb"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div>
          <div class="hp-feat-name">Monitoring Real-Time</div>
          <div class="hp-feat-sub">Data diperbarui otomatis</div>
        </div>
      </div>
      <div class="hp-feat">
        <div class="hp-feat-icon" style="background:#f0fdf4">
          <svg viewBox="0 0 24 24" stroke="#16a34a"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div>
          <div class="hp-feat-name">Manajemen File</div>
          <div class="hp-feat-sub">Upload, download, kelola</div>
        </div>
      </div>
      <div class="hp-feat">
        <div class="hp-feat-icon" style="background:#fefce8">
          <svg viewBox="0 0 24 24" stroke="#ca8a04"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div class="hp-feat-name">Rekap Harian</div>
          <div class="hp-feat-sub">Laporan per tanggal / range</div>
        </div>
      </div>
      <div class="hp-feat">
        <div class="hp-feat-icon" style="background:#fdf4ff">
          <svg viewBox="0 0 24 24" stroke="#a21caf"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div>
          <div class="hp-feat-name">Akses Terkelola</div>
          <div class="hp-feat-sub">Login &amp; hak akses pengguna</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══ MAIN CARDS ═══ --}}
  <section class="hp-section">
    <div class="hp-section-head">
      <div class="hp-section-label">Modul Utama</div>
      <div class="hp-section-title">Pilih menu yang ingin Anda akses</div>
    </div>

    <div class="hp-grid">

      {{-- Dashboard Live Bird --}}
      <a href="{{ route('dashboard') }}" class="hp-card blue">
        <div class="hp-card-bar" style="background: linear-gradient(90deg, #1d4ed8, #38bdf8);"></div>

        <div class="hp-card-top">
          <div class="hp-card-icon" style="background: linear-gradient(135deg,#dbeafe,#bfdbfe);">
            <svg viewBox="0 0 24 24" stroke="#1d4ed8">
              <rect x="2" y="3" width="20" height="14" rx="2"/>
              <line x1="8" y1="21" x2="16" y2="21"/>
              <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
          </div>
          <div class="hp-badge" style="background:#eff6ff; color:#1d4ed8;">
            <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" fill="#22c55e"/></svg>
            Live Now
          </div>
        </div>

        <div class="hp-card-body">
          <div class="hp-card-name">Dashboard Live Bird</div>
          <div class="hp-card-desc">
            Monitoring produksi secara real-time. Pantau data hanging ayam, jumlah truk masuk,
            rekap harian, dan seluruh metrik operasional Slaughter House.
          </div>
        </div>

        <div class="hp-card-footer">
          <div class="hp-card-tags">
            <span class="hp-tag">Hanging Ayam</span>
            <span class="hp-tag">Rekap</span>
            <span class="hp-tag">Truk</span>
          </div>
          <div class="hp-card-cta" style="color:#1d4ed8;">
            Buka
            <svg viewBox="0 0 24 24" stroke="#1d4ed8">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 19"/>
            </svg>
          </div>
        </div>
      </a>

      {{-- Uniformity Report --}}
      <a href="{{ route('daily-uniformities.index') }}" class="hp-card blue">
        <div class="hp-card-bar" style="background: linear-gradient(90deg, #7c3aed, #a78bfa);"></div>

        <div class="hp-card-top">
          <div class="hp-card-icon" style="background: linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <svg viewBox="0 0 24 24" stroke="#7c3aed">
              <path d="M3 3v18h18"/>
              <path d="M18 17V9"/>
              <path d="M13 17V5"/>
              <path d="M8 17v-3"/>
            </svg>
          </div>
          <div class="hp-badge" style="background:#f5f3ff; color:#7c3aed;">
            Sampling
          </div>
        </div>

        <div class="hp-card-body">
          <div class="hp-card-name">Uniformity Report</div>
          <div class="hp-card-desc">
            Trial sampling berat live bird per truk: input berat satu per satu,
            lihat sebaran terhadap range uniformity, dan rekap harian.
          </div>
        </div>

        <div class="hp-card-footer">
          <div class="hp-card-tags">
            <span class="hp-tag">Sampling</span>
            <span class="hp-tag">Uniformity</span>
            <span class="hp-tag">Rekap</span>
          </div>
          <div class="hp-card-cta" style="color:#7c3aed;">
            Buka
            <svg viewBox="0 0 24 24" stroke="#7c3aed">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 19"/>
            </svg>
          </div>
        </div>
      </a>

      {{-- Yield --}}
      <a href="{{ route('daily-yields.index') }}" class="hp-card blue">
        <div class="hp-card-bar" style="background: linear-gradient(90deg, #edb43a, #fae68b);"></div>

        <div class="hp-card-top">
          <div class="hp-card-icon" style="background: linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <svg viewBox="0 0 24 24" stroke="#7c3aed">
              <path d="M3 3v18h18"/>
              <path d="M18 17V9"/>
              <path d="M13 17V5"/>
              <path d="M8 17v-3"/>
            </svg>
          </div>
          <div class="hp-badge" style="background:#f5f3ff; color:#edb43a;">
            Yield
          </div>
        </div>

        <div class="hp-card-body">
          <div class="hp-card-name">Monitoring Yield</div>
          <div class="hp-card-desc">
            Summary Yield H0 - H4.
          </div>
        </div>

        <div class="hp-card-footer">
          <div class="hp-card-tags">
            <span class="hp-tag">Yield</span>
            <span class="hp-tag">Monitoring</span>
            <span class="hp-tag">Report</span>
          </div>
          <div class="hp-card-cta" style="color:#edb43a;">
            Buka
            <svg viewBox="0 0 24 24" stroke="#edb43a">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 19"/>
            </svg>
          </div>
        </div>
      </a>

      {{-- File Inventory --}}
      <a href="{{ route('inventory.index') }}" class="hp-card green">
        <div class="hp-card-bar" style="background: linear-gradient(90deg, #0d9488, #34d399);"></div>

        <div class="hp-card-top">
          <div class="hp-card-icon" style="background: linear-gradient(135deg,#ccfbf1,#99f6e4);">
            <svg viewBox="0 0 24 24" stroke="#0d9488">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <div class="hp-badge" style="background:#f0fdf4; color:#0d9488;">
            Slaughter House
          </div>
        </div>

        <div class="hp-card-body">
          <div class="hp-card-name">File Inventory</div>
          <div class="hp-card-desc">
            Cari, filter, sort, upload, download, serta kelola folder dan file inventaris secara
            lengkap dan terstruktur untuk kebutuhan operasional.
          </div>
        </div>

        <div class="hp-card-footer">
          <div class="hp-card-tags">
            <span class="hp-tag">Upload</span>
            <span class="hp-tag">Download</span>
            <span class="hp-tag">Filter</span>
          </div>
          <div class="hp-card-cta" style="color:#0d9488;">
            Buka
            <svg viewBox="0 0 24 24" stroke="#0d9488">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 19"/>
            </svg>
          </div>
        </div>
      </a>

    </div>
  </section>

</div>
<script>
// Auto-refresh untuk data real-time
let refreshInterval = null;

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num || 0);
}

async function fetchRealTimeData() {
    try {
        // Fetch data dari API endpoint yang sudah dibuat
        const response = await fetch('/api/dashboard/today-stats', {
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('API Error:', data.error);
            return;
        }
        
        // Update DOM dengan data real-time
        const ayamElement = document.getElementById('ayamCount');
        const trukElement = document.getElementById('trukCount');
        
        if (ayamElement) {
            ayamElement.textContent = formatNumber(data.ayam_received);
            // Add smooth transition effect
            ayamElement.style.transform = 'scale(1.05)';
            setTimeout(() => {
                ayamElement.style.transform = 'scale(1)';
            }, 200);
        }
        
        if (trukElement) {
            trukElement.textContent = formatNumber(data.truk_counted);
            trukElement.style.transform = 'scale(1.05)';
            setTimeout(() => {
                trukElement.style.transform = 'scale(1)';
            }, 200);
        }
        
        // Update progress bar
        const ayamFill = document.querySelector('.hp-visual-card.main .hp-vc-fill');
        if (ayamFill) {
            ayamFill.style.width = data.ayam_progress + '%';
        }
        
        // Update subtext
        const ayamSub = document.querySelector('.hp-visual-card.main .hp-vc-sub');
        if (ayamSub) {
            ayamSub.innerHTML = `ekor · dari target ${formatNumber(data.plan_chicken)}`;
        }
        
        const trukSub = document.querySelector('.hp-visual-card.mini .hp-vc-sub');
        if (trukSub) {
            trukSub.innerHTML = `unit hari ini · dari ${formatNumber(data.plan_truck)}`;
        }
        
        // Optional: Update progress bar truk jika ada
        const trukFill = document.querySelector('.hp-visual-card.mini .hp-vc-fill');
        if (trukFill && data.truk_progress > 0) {
            trukFill.style.width = data.truk_progress + '%';
        }
        
    } catch (error) {
        console.error('Error fetching real-time data:', error);
        
        // Fallback: tampilkan pesan error di console tapi tidak mengganggu UI
        const ayamElement = document.getElementById('ayamCount');
        if (ayamElement && ayamElement.textContent === '0') {
            console.warn('Data masih 0, pastikan ada data hanging form yang sudah di-count');
        }
    }
}

// Start auto-refresh setiap 5 detik (lebih ringan)
function startAutoRefresh() {
    if (refreshInterval) clearInterval(refreshInterval);
    refreshInterval = setInterval(fetchRealTimeData, 5000);
}

// Stop auto-refresh saat page di-unload
window.addEventListener('beforeunload', () => {
    if (refreshInterval) clearInterval(refreshInterval);
});

// Initial load dan start refresh
document.addEventListener('DOMContentLoaded', () => {
    // Delay initial load untuk memastikan DOM siap
    setTimeout(() => {
        fetchRealTimeData();
        startAutoRefresh();
    }, 500);
});

// CSS transition untuk smooth update
const style = document.createElement('style');
style.textContent = `
    #ayamCount, #trukCount {
        transition: transform 0.2s ease-in-out;
        display: inline-block;
    }
    .hp-vc-fill {
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
`;
document.head.appendChild(style);
</script>
@endsection