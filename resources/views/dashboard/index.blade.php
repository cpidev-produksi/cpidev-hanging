@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800;12..96,900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap');
    :root {
        --c-bg: #f5f7fb;
        --c-surface: #ffffff;
        --c-border: #e8edf5;
        --c-text: #0f1623;
        --c-muted: #7a8aaa;
        --c-green: #0ea472;
        --c-green-bg: #edfaf4;
        --c-blue: #2563eb;
        --c-blue-bg: #eff4ff;
        --c-amber: #d97706;
        --c-amber-bg: #fffbeb;
        --c-violet: #7c3aed;
        --c-violet-bg: #f5f0ff;
        --c-red: #dc2626;
        --c-red-bg: #fff0f0;
        --c-cyan: #0891b2;
        --c-cyan-bg: #ecfeff;
        --radius: 16px;
        --radius-sm: 10px;
        --shadow: 0 1px 4px rgba(15,22,35,0.06), 0 4px 16px rgba(15,22,35,0.05);
        --shadow-md: 0 4px 12px rgba(15,22,35,0.08), 0 12px 32px rgba(15,22,35,0.07);
    }

    * { box-sizing: border-box; }

    .db-wrap {
        max-width: 1280px;
        margin: 0 auto;
        padding: 28px 20px 40px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--c-bg);
        min-height: 100vh;
    }

    /* ─── Header ─── */
    .db-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .db-header-left {
        position: relative;
    }
    .db-header-left h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(52px, 7vw, 40px);
        font-weight: 600;
        color: var(--c-text);
        margin: 0;
        letter-spacing: -3px;
        line-height: 0.92;
        /* Subtle outline on second "shadow" layer for depth */
        text-shadow:
            3px 3px 0px rgba(37,99,235,0.07),
            6px 6px 0px rgba(37,99,235,0.04);
        position: relative;
        display: inline-block;
    }
    /* Decorative accent underline */
    .db-header-left h1::after {
        content: '';
        position: absolute;
        left: 2px;
        bottom: -6px;
        width: 56px;
        height: 4px;
        border-radius: 100px;
        background: linear-gradient(90deg, #2563eb, #7c3aed);
    }
    .db-header-left p {
        font-size: 13px;
        color: var(--c-muted);
        margin: 16px 0 0;
        font-weight: 500;
    }
    .db-header-left p strong { color: var(--c-text); font-weight: 700; }
    .db-date-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 100px;
        padding: 7px 16px;
        font-size: 12px;
        font-weight: 700;
        color: var(--c-muted);
        letter-spacing: 0.04em;
        box-shadow: var(--shadow);
    }
    .db-date-badge span { color: var(--c-text); }

    /* ─── Section labels ─── */
    .section-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin: 0 0 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--c-border);
    }

    /* ─── Grand Summary Grid ─── */
    .grand-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 28px;
    }
    @media (max-width: 1100px) { .grand-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 600px)  { .grand-grid { grid-template-columns: 1fr; } }

    /* ─── KPI Card ─── */
    .kpi-card {
        background: var(--c-surface);
        border-radius: var(--radius);
        border: 1px solid var(--c-border);
        padding: 20px 20px 16px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: default;
        box-shadow: var(--shadow);
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .kpi-card-accent {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: var(--radius) var(--radius) 0 0;
    }
    .kpi-card-accent.green  { background: linear-gradient(90deg, #0ea472, #34d399); }
    .kpi-card-accent.blue   { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .kpi-card-accent.amber  { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .kpi-card-accent.violet { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

    .kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .kpi-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin-bottom: 8px;
    }
    .kpi-value {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 36px;
        font-weight: 700;
        color: var(--c-text);
        letter-spacing: -0.5px;
    }
    .kpi-value sup {
        font-size: 18px;
        letter-spacing: -0.5px;
        opacity: 0.4;
    }
    .kpi-plan-ratio {
        font-size: 12px;
        font-weight: 700;
        color: var(--c-muted);
        margin-top: 4px;
    }
    .kpi-plan-ratio strong { color: var(--c-text); }
    .kpi-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon.green  { background: var(--c-green-bg);  color: var(--c-green); }
    .kpi-icon.blue   { background: var(--c-blue-bg);   color: var(--c-blue); }
    .kpi-icon.amber  { background: var(--c-amber-bg);  color: var(--c-amber); }
    .kpi-icon.violet { background: var(--c-violet-bg); color: var(--c-violet); }

    /* Progress track */
    .kpi-progress-wrap {
        margin-top: 14px;
    }
    .kpi-progress-meta {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 700;
        color: var(--c-muted);
        margin-bottom: 6px;
    }
    .kpi-progress-meta .pct { color: var(--c-text); }
    .kpi-track {
        height: 6px;
        background: #f0f3f9;
        border-radius: 100px;
        overflow: hidden;
    }
    .kpi-bar {
        height: 100%;
        border-radius: 100px;
        transition: width 1.2s cubic-bezier(0.4,0,0.2,1);
        width: 0;
    }
    .kpi-bar.green  { background: linear-gradient(90deg, #0ea472, #34d399); }
    .kpi-bar.blue   { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .kpi-bar.amber  { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .kpi-bar.violet { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

    /* ─── Lokasi Grid ─── */
    .loc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 28px;
    }
    @media (max-width: 900px) { .loc-grid { grid-template-columns: 1fr; } }

    /* ─── Location Panel ─── */
    .loc-panel {
        background: var(--c-surface);
        border-radius: var(--radius);
        border: 1px solid var(--c-border);
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    .loc-panel-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--c-border);
        background: linear-gradient(135deg, #fafbff 0%, #f5f7fb 100%);
    }
    .loc-name {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: var(--c-text);
        letter-spacing: -0.3px;
    }
    .loc-status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #d1d9e8;
        margin-right: 6px;
        display: inline-block;
    }
    .loc-status-dot.running {
        background: #f59e0b;
        animation: pulse-dot 1.6s ease-in-out infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.7); }
    }
    .loc-plan-badge {
        font-size: 11px;
        font-weight: 700;
        color: var(--c-muted);
        background: #f0f3f9;
        border-radius: 100px;
        padding: 4px 12px;
    }
    .loc-plan-badge strong { color: var(--c-text); }

    .loc-panel-body { padding: 16px 20px; }

    /* Truk progress bar (main) */
    .loc-truk-section { margin-bottom: 16px; }
    .loc-truk-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .loc-truk-counted {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 30px;
        font-weight: 700;
        color: var(--c-text);
        letter-spacing: -0.3px;
    }
    .loc-truk-plan {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-muted);
    }
    .loc-truk-plan strong { color: var(--c-text); }
    .loc-truk-sublabel {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin-bottom: 8px;
    }
    .loc-truk-track {
        height: 8px;
        background: #f0f3f9;
        border-radius: 100px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .loc-truk-bar {
        height: 100%;
        background: linear-gradient(90deg, #2563eb, #60a5fa);
        border-radius: 100px;
        transition: width 1.2s cubic-bezier(0.4,0,0.2,1);
        width: 0;
    }

    /* Mini pill stats (queue, run, done) */
    .loc-pill-row {
        display: flex;
        gap: 8px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .loc-pill {
        display: flex;
        align-items: center;
        gap: 5px;
        background: #f5f7fb;
        border: 1px solid var(--c-border);
        border-radius: 100px;
        padding: 4px 12px 4px 8px;
        font-size: 12px;
        font-weight: 700;
        color: var(--c-muted);
    }
    .loc-pill strong { color: var(--c-text); font-size: 13px; }
    .loc-pill-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
    }
    .loc-pill-dot.queue  { background: #d1d9e8; }
    .loc-pill-dot.run    { background: #f59e0b; }
    .loc-pill-dot.done   { background: #0ea472; }

    /* Ayam row */
    .loc-ayam-section {
        background: linear-gradient(135deg, var(--c-violet-bg), #faf5ff);
        border: 1px solid #e9d5ff;
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        margin-bottom: 14px;
    }
    .loc-ayam-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .loc-ayam-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--c-violet);
        opacity: 0.7;
    }
    .loc-ayam-value {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 24px;
        font-weight: 800;
        color: var(--c-violet);
        letter-spacing: -0.2px;
        line-height: 1;
    }
    .loc-ayam-plan {
        font-size: 12px;
        font-weight: 700;
        color: var(--c-violet);
        opacity: 0.6;
    }
    .loc-ayam-plan strong { opacity: 1; font-weight: 700; }
    .loc-ayam-track {
        height: 5px;
        background: rgba(124,58,237,0.1);
        border-radius: 100px;
        overflow: hidden;
        margin-top: 8px;
    }
    .loc-ayam-bar {
        height: 100%;
        background: linear-gradient(90deg, #7c3aed, #a78bfa);
        border-radius: 100px;
        transition: width 1.2s cubic-bezier(0.4,0,0.2,1);
        width: 0;
    }

    /* Live running block */
    .running-block {
        background: linear-gradient(135deg, #fffbeb, #fef9f0);
        border: 1px solid #fde68a;
        border-radius: var(--radius-sm);
        padding: 12px 14px;
    }
    .running-block-header {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
    }
    .running-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #92400e;
    }
    .running-empty {
        font-size: 12px;
        font-weight: 600;
        color: #b45309;
        opacity: 0.6;
    }
    .running-info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 5px;
    }
    .running-code {
        font-size: 11px;
        font-weight: 700;
        background: rgba(245,158,11,0.15);
        color: #92400e;
        padding: 2px 8px;
        border-radius: 6px;
        font-family: monospace;
    }
    .running-truck-no {
        font-size: 12px;
        font-weight: 700;
        color: #0f1623;
    }
    .running-run-done {
        margin-left: auto;
        font-size: 11px;
        font-weight: 700;
        color: var(--c-muted);
        white-space: nowrap;
    }
    .running-run-done .r { color: #d97706; }
    .running-run-done .d { color: #0ea472; }
    .running-details {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 11px;
        font-weight: 600;
        color: #b45309;
        opacity: 0.8;
        margin-bottom: 6px;
    }
    .running-ayam-count {
        font-size: 12px;
        font-weight: 700;
        color: #92400e;
    }
    .running-ayam-count b {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #0f1623;
    }

    /* ─── Chart Panel ─── */
    .chart-panel {
        background: var(--c-surface);
        border-radius: var(--radius);
        border: 1px solid var(--c-border);
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: var(--shadow);
    }
    .chart-panel-header {
        padding: 18px 24px 14px;
        border-bottom: 1px solid var(--c-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .chart-panel-title {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: var(--c-text);
        letter-spacing: -0.3px;
    }
    .chart-legend {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .chart-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        color: var(--c-muted);
    }
    .chart-legend-swatch {
        width: 24px; height: 4px;
        border-radius: 2px;
        flex-shrink: 0;
    }
    .chart-legend-swatch.ayam { background: #7c3aed; }
    .chart-legend-swatch.plan { background: #94a3b8; border-top: 2px dashed #94a3b8; background: transparent; height: 0; }
    .chart-body {
        padding: 20px 24px 16px;
    }
    .chart-canvas-wrap {
        position: relative;
        width: 100%;
        height: 260px;
    }

    /* ─── Master Data ─── */
    .master-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }
    @media (max-width: 700px) { .master-row { grid-template-columns: 1fr; } }
    .master-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: var(--radius);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow);
        transition: transform 0.2s;
    }
    .master-card:hover { transform: translateY(-2px); }
    .master-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .master-icon.green  { background: var(--c-green-bg);  color: var(--c-green); }
    .master-icon.blue   { background: var(--c-blue-bg);   color: var(--c-blue); }
    .master-icon.amber  { background: var(--c-amber-bg);  color: var(--c-amber); }
    .master-info {}
    .master-count {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: var(--c-text);
        letter-spacing: -0.3px;
    }
    .master-desc {
        font-size: 11px;
        font-weight: 600;
        color: var(--c-muted);
        margin-top: 2px;
    }
</style>

<div class="db-wrap">

    {{-- ── Header ── --}}
    <div class="db-header">
        <div class="db-header-left">
            <h1>Dashboard</h1>
        </div>
        <div class="db-date-badge">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                <path stroke-width="2" d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
            <span>{{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    {{-- ── Grand KPI 4 Cards ── --}}
    <p class="section-label">Ringkasan Operasional · SH01 + SH02</p>
    @php
        $pPlanTruck   = $grand['plan_truck']   > 0 ? min(round(($grand['truk_total']    / $grand['plan_truck'])   * 100), 100) : 0;
        $pCounted     = $grand['plan_truck']   > 0 ? min(round(($grand['truk_counted']  / $grand['plan_truck'])   * 100), 100) : 0;
        $pQueue       = $grand['plan_truck']   > 0 ? min(round(($grand['truk_queue']    / $grand['plan_truck'])   * 100), 100) : 0;
        $pAyam        = $grand['plan_chicken'] > 0 ? min(round(($grand['ayam_received'] / $grand['plan_chicken']) * 100), 100) : 0;
    @endphp
    <div class="grand-grid">

        {{-- Total Truk --}}
        <div class="kpi-card">
            <div class="kpi-card-accent green"></div>
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Total Truk Hari Ini</div>
                    <div class="kpi-value">{{ $grand['truk_total'] }}<sup>/{{ $grand['plan_truck'] ?: '–' }}</sup></div>
                    <div class="kpi-plan-ratio">Target: <strong>{{ $grand['plan_truck'] ?: '–' }} truk</strong></div>
                </div>
                <div class="kpi-icon green">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m9 0H5m8 0h3m-3 0V9m3 7h2a1 1 0 001-1v-4l-3-4h-2"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-progress-wrap">
                <div class="kpi-progress-meta">
                    <span>Progress vs. Planning</span>
                    <span class="pct">{{ $pPlanTruck }}%</span>
                </div>
                <div class="kpi-track"><div class="kpi-bar green" data-w="{{ $pPlanTruck }}"></div></div>
            </div>
        </div>

        {{-- Sudah Dihitung --}}
        <div class="kpi-card">
            <div class="kpi-card-accent blue"></div>
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Sudah Dihitung</div>
                    <div class="kpi-value">{{ $grand['truk_counted'] }}<sup>/{{ $grand['plan_truck'] ?: '–' }}</sup></div>
                    <div class="kpi-plan-ratio">dari <strong>{{ $grand['truk_total'] }} truk</strong> terdaftar</div>
                </div>
                <div class="kpi-icon blue">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-progress-wrap">
                <div class="kpi-progress-meta">
                    <span>Selesai dihitung</span>
                    <span class="pct">{{ $pCounted }}%</span>
                </div>
                <div class="kpi-track"><div class="kpi-bar blue" data-w="{{ $pCounted }}"></div></div>
            </div>
        </div>

        {{-- Antrian --}}
        <div class="kpi-card">
            <div class="kpi-card-accent amber"></div>
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Antrian</div>
                    <div class="kpi-value">{{ $grand['truk_queue'] }}</div>
                    <div class="kpi-plan-ratio">Menunggu proses penghitungan</div>
                </div>
                <div class="kpi-icon amber">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-progress-wrap">
                <div class="kpi-progress-meta">
                    <span>Porsi antrian</span>
                    <span class="pct">{{ $pQueue }}%</span>
                </div>
                <div class="kpi-track"><div class="kpi-bar amber" data-w="{{ $pQueue }}"></div></div>
            </div>
        </div>

        {{-- Ayam Diterima --}}
        <div class="kpi-card">
            <div class="kpi-card-accent violet"></div>
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Ayam Diterima</div>
                    <div class="kpi-value" style="font-size:32px">{{ number_format($grand['ayam_received']) }}</div>
                    <div class="kpi-plan-ratio">Target: <strong>{{ number_format($grand['plan_chicken']) ?: '–' }} ekor</strong></div>
                </div>
                <div class="kpi-icon violet">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-progress-wrap">
                <div class="kpi-progress-meta">
                    <span>vs. Planning ekor</span>
                    <span class="pct">{{ $pAyam }}%</span>
                </div>
                <div class="kpi-track"><div class="kpi-bar violet" data-w="{{ $pAyam }}"></div></div>
            </div>
        </div>

    </div>

    {{-- ── Chart: Ayam Diterima vs Planning 7 Hari ── --}}
    <p class="section-label">Tren Pencapaian · 7 Hari Terakhir</p>
    <div class="chart-panel">
        <div class="chart-panel-header">
            <div>
                <div class="chart-panel-title">Aktual Ayam Diterima & Truk Terhitung vs Planning</div>
                <div style="font-size:12px;color:var(--c-muted);font-weight:500;margin-top:3px">SH01 + SH02 · {{ \Carbon\Carbon::parse($today)->subDays(6)->format('d/m') }} – {{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}</div>
            </div>
            <div class="chart-legend">
                <div class="chart-legend-item">
                    <div style="width:20px;height:3px;background:#7c3aed;border-radius:2px;flex-shrink:0"></div>
                    Ayam Diterima
                </div>
                <div class="chart-legend-item">
                    <div style="width:20px;height:0;border-top:2px dashed #94a3b8;flex-shrink:0"></div>
                    Planning Ayam
                </div>
                <div class="chart-legend-item">
                    <div style="width:20px;height:3px;background:#0ea472;border-radius:2px;flex-shrink:0"></div>
                    Truk Dihitung
                </div>
                <div class="chart-legend-item">
                    <div style="width:20px;height:0;border-top:2px dashed #f59e0b;flex-shrink:0"></div>
                    Planning Truk
                </div>
            </div>
        </div>
        <div class="chart-body">
            <div class="chart-canvas-wrap">
                <canvas id="ayamChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Per Lokasi ── --}}
    <p class="section-label">Detail Per Lokasi</p>
    <div class="loc-grid">
        @foreach($statsByLoc as $loc => $s)
        @php
            $locPctTruk = $s['plan_truck']   > 0 ? min(round(($s['truk_counted']  / $s['plan_truck'])   * 100), 100) : 0;
            $locPctAyam = $s['plan_chicken'] > 0 ? min(round(($s['ayam_received'] / $s['plan_chicken']) * 100), 100) : 0;
            $isRunning  = $s['running'] !== null;
        @endphp
        <div class="loc-panel">

            <div class="loc-panel-header">
                <div style="display:flex;align-items:center;gap:8px">
                    <span class="loc-status-dot {{ $isRunning ? 'running' : '' }}"></span>
                    <span class="loc-name">Lokasi {{ $loc }}</span>
                </div>
                <span class="loc-plan-badge">Plan: <strong>{{ $s['plan_truck'] ?: '–' }} truk</strong></span>
            </div>

            <div class="loc-panel-body">

                {{-- Truk dihitung vs planning --}}
                <div class="loc-truk-section">
                    <div class="loc-truk-sublabel">Truk Dihitung</div>
                    <div class="loc-truk-header">
                        <div class="loc-truk-counted">{{ $s['truk_counted'] }}
                            <span style="font-size:16px;color:var(--c-muted);font-weight:600;letter-spacing:0"> / {{ $s['plan_truck'] ?: '–' }}</span>
                        </div>
                        <div class="loc-truk-plan">Total daftar: <strong>{{ $s['truk_total'] }}</strong></div>
                    </div>
                    <div class="loc-truk-track">
                        <div class="loc-truk-bar" data-w="{{ $locPctTruk }}"></div>
                    </div>
                </div>

                {{-- Pill: Antrian / Running / Done --}}
                <div class="loc-pill-row">
                    <div class="loc-pill">
                        <span class="loc-pill-dot queue"></span>
                        Antrian <strong>{{ $s['truk_queue'] }}</strong>
                    </div>
                    <div class="loc-pill">
                        <span class="loc-pill-dot run"></span>
                        Run <strong>{{ $s['truk_running'] }}</strong>
                    </div>
                    <div class="loc-pill">
                        <span class="loc-pill-dot done"></span>
                        Done <strong>{{ $s['truk_done'] }}</strong>
                    </div>
                </div>

                {{-- Ayam diterima --}}
                <div class="loc-ayam-section">
                    <div class="loc-ayam-label">Ayam Diterima</div>
                    <div class="loc-ayam-header">
                        <div class="loc-ayam-value">{{ number_format($s['ayam_received']) }}</div>
                        <div class="loc-ayam-plan">Target <strong>{{ number_format($s['plan_chicken']) ?: '–' }}</strong> ekor</div>
                    </div>
                    <div class="loc-ayam-track">
                        <div class="loc-ayam-bar" data-w="{{ $locPctAyam }}"></div>
                    </div>
                </div>

                {{-- Live Running --}}
                <div class="running-block">
                    <div class="running-block-header">
                        <span class="loc-status-dot running" style="width:7px;height:7px"></span>
                        <span class="running-label">Live Running</span>
                        @if($s['running'])
                        <div class="running-run-done" style="margin-left:auto">
                            <span class="r">▶ {{ $s['truk_running'] }}</span>
                            <span style="color:rgba(0,0,0,0.15);margin:0 3px">·</span>
                            <span class="d">✓ {{ $s['truk_done'] }}</span>
                        </div>
                        @endif
                    </div>

                    @if(!$s['running'])
                        <div class="running-empty">Tidak ada proses running saat ini.</div>
                    @else
                        <div class="running-info-row">
                            <span class="running-code">{{ $s['running']['report_code'] }}</span>
                            <span class="running-truck-no">Truk #{{ $s['running']['truck_no'] }}</span>
                        </div>
                        <div class="running-details">
                            <span>🚛 {{ $s['running']['expedition'] ?? '—' }}</span>
                            <span>🏠 {{ $s['running']['farm'] ?? '—' }}</span>
                            <span>🔢 {{ $s['running']['plate'] ?? '—' }}</span>
                        </div>
                        <div class="running-ayam-count">
                            Ayam running: <b>{{ number_format($s['running']['total_ayam']) }}</b> ekor
                        </div>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Master Data ── --}}
    <p class="section-label">Master Data</p>
    <div class="master-row">
        <div class="master-card">
            <div class="master-icon green">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 17l4 4 4-4m-4-5v9M3 5h18M3 5a2 2 0 002 2h14a2 2 0 002-2M3 5a2 2 0 012-2h14a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="master-info">
                <div class="master-count">{{ $master['expeditions'] }}</div>
                <div class="master-desc">Ekspedisi tersimpan</div>
            </div>
        </div>
        <div class="master-card">
            <div class="master-icon blue">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div class="master-info">
                <div class="master-count">{{ $master['farms'] }}</div>
                <div class="master-desc">Farm tersimpan</div>
            </div>
        </div>
        @if(!is_null($master['plates']))
        <div class="master-card">
            <div class="master-icon amber">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="7" width="20" height="10" rx="2" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11h.01M17 11h.01"/>
                </svg>
            </div>
            <div class="master-info">
                <div class="master-count">{{ $master['plates'] }}</div>
                <div class="master-desc">Plat Nomor tersimpan</div>
            </div>
        </div>
        @endif
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Animate progress bars ──
    const animate = (selector) => {
        document.querySelectorAll(selector).forEach(el => {
            const w = el.dataset.w || 0;
            el.style.width = '0';
            setTimeout(() => { el.style.width = w + '%'; }, 150);
        });
    };
    animate('.kpi-bar');
    animate('.loc-truk-bar');
    animate('.loc-ayam-bar');

    // ── Count-up animation ──
    document.querySelectorAll('.kpi-value, .loc-truk-counted, .loc-ayam-value').forEach(el => {
        // Ambil hanya text node pertama (angka utama), abaikan <sup>
        const textNode = Array.from(el.childNodes).find(n => n.nodeType === Node.TEXT_NODE);
        if (!textNode) return;
        const raw = textNode.textContent.replace(/[^0-9]/g, '');
        if (!raw) return;
        const target = parseInt(raw);
        if (isNaN(target) || target === 0) return;
        const isFormatted = raw !== textNode.textContent.trim();
        let start = 0;
        const duration = 900;
        const step = (timestamp) => {
            if (!start) start = timestamp;
            const progress = Math.min((timestamp - start) / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(ease * target);
            textNode.textContent = isFormatted
                ? current.toLocaleString('id-ID')
                : String(current);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });

    // ── Ayam Chart ──
    @php
        $chartLabels  = collect($chartData)->pluck('label')->toJson();
        $chartAyam    = collect($chartData)->pluck('ayam')->toJson();
        $chartPlan    = collect($chartData)->pluck('plan_chicken')->toJson();
        $chartIsToday = collect($chartData)->pluck('is_today')->toJson();
        $chartTruk     = collect($chartData)->pluck('truk_counted')->toJson();
        $chartPlanTruk = collect($chartData)->pluck('plan_truck')->toJson();
    @endphp

    const labels   = {!! $chartLabels !!};
    const ayamData = {!! $chartAyam !!};
    const planData = {!! $chartPlan !!};
    const isToday  = {!! $chartIsToday !!};
    const trukData     = {!! $chartTruk !!};
    const planTrukData = {!! $chartPlanTruk !!};

    // Point radius: bigger for today
    const pointRadius = isToday.map(v => v ? 7 : 4);
    const pointHover  = isToday.map(v => v ? 9 : 6);

    // Bar background: highlight today
    const barColors = isToday.map(v =>
        v ? 'rgba(124,58,237,0.85)' : 'rgba(124,58,237,0.35)'
    );
    const barBorders = isToday.map(v =>
        v ? '#7c3aed' : 'rgba(124,58,237,0.5)'
    );

    const ctx = document.getElementById('ayamChart').getContext('2d');

    // Gradient fill under area line
    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(124,58,237,0.18)');
    gradient.addColorStop(1, 'rgba(124,58,237,0)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Ayam Diterima',
                    data: ayamData,
                    type: 'bar',
                    backgroundColor: barColors,
                    borderColor: barBorders,
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                    order: 4,
                    yAxisID: 'yAyam',
                },
                {
                    label: 'Planning Ayam',
                    data: planData,
                    type: 'line',
                    borderColor: '#94a3b8',
                    borderWidth: 2,
                    borderDash: [6, 4],
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#94a3b8',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0.35,
                    order: 3,
                    yAxisID: 'yAyam',
                },
                {
                    label: 'Truk Dihitung',
                    data: trukData,
                    type: 'bar',
                    backgroundColor: isToday.map(v =>
                        v ? 'rgba(14,164,114,0.85)' : 'rgba(14,164,114,0.3)'
                    ),
                    borderColor: isToday.map(v =>
                        v ? '#0ea472' : 'rgba(14,164,114,0.5)'
                    ),
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                    order: 2,
                    yAxisID: 'yTruk',
                },
                {
                    label: 'Planning Truk',
                    data: planTrukData,
                    type: 'line',
                    borderColor: '#f59e0b',
                    borderWidth: 2,
                    borderDash: [6, 4],
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0.35,
                    order: 1,
                    yAxisID: 'yTruk',
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f1623',
                    titleColor: 'rgba(255,255,255,0.5)',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => {
                            const val = ctx.parsed.y;
                            const formatted = val > 0 ? val.toLocaleString('id-ID') : '–';
                            const unit = ctx.dataset.yAxisID === 'yAyam' ? ' ekor' : ' truk';
                            return ` ${ctx.dataset.label}: ${formatted}${unit}`;
                        }
                    }
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { family: "'Space Grotesk', sans-serif", size: 11, weight: '600' },
                        color: (ctx) => isToday[ctx.index] ? '#7c3aed' : '#7a8aaa',
                    }
                },
                yAyam: {
                    position: 'left',
                    grid: { color: 'rgba(15,22,35,0.05)', drawBorder: false },
                    border: { display: false, dash: [4, 4] },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                        color: '#7c3aed',
                        maxTicksLimit: 5,
                        callback: (val) => val >= 1000
                            ? (val / 1000).toLocaleString('id-ID') + 'k'
                            : val.toLocaleString('id-ID'),
                    },
                    title: {
                        display: true,
                        text: 'Ekor Ayam',
                        color: '#7c3aed',
                        font: { size: 10, weight: '700', family: "'Plus Jakarta Sans', sans-serif" },
                    }
                },
                yTruk: {
                    position: 'right',
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                        color: '#0ea472',
                        maxTicksLimit: 5,
                        stepSize: 1,
                        callback: (val) => Number.isInteger(val) ? val : '',
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Truk',
                        color: '#0ea472',
                        font: { size: 10, weight: '700', family: "'Plus Jakarta Sans', sans-serif" },
                    }
                }
            }
        }
    });
});
</script>

@endsection