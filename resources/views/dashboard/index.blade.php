@extends('layouts.app')

@section('content')
<style>
    /* ── Typography ── */
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&display=swap');

    /* ── Grand Summary Grid ── */
    .dash-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }
    @media (max-width: 1100px) { .dash-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .dash-grid { grid-template-columns: 1fr; } }

    /* ── Stat Cards ── */
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid #e4e8f0;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 16px 16px 0 0;
    }
    .stat-card.green::before  { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card.blue::before   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-card.orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8090b0;
        margin-bottom: 6px;
    }
    .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 34px;
        font-weight: 800;
        color: #0d1117;
        line-height: 1;
        letter-spacing: -1px;
    }
    .stat-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.green  { background: rgba(16,185,129,0.1);  color: #10b981; }
    .stat-icon.blue   { background: rgba(59,130,246,0.1);  color: #3b82f6; }
    .stat-icon.orange { background: rgba(245,158,11,0.1);  color: #f59e0b; }
    .stat-icon.purple { background: rgba(139,92,246,0.1);  color: #8b5cf6; }

    .stat-footer {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #8090b0;
        font-weight: 600;
    }

    .stat-progress {
        height: 4px;
        background: #f0f2f7;
        border-radius: 100px;
        margin-top: 14px;
        overflow: hidden;
    }
    .stat-progress-bar {
        height: 100%;
        border-radius: 100px;
        transition: width 1s ease;
    }
    .green  .stat-progress-bar { background: linear-gradient(90deg, #10b981, #34d399); }
    .blue   .stat-progress-bar { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .orange .stat-progress-bar { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .purple .stat-progress-bar { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    /* ── Panel ── */
    .panel {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e4e8f0;
        overflow: hidden;
        margin-bottom: 16px;
    }
    .panel-header {
        padding: 16px 22px;
        border-bottom: 1px solid #f0f2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .panel-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #0d1117;
    }
    .panel-meta {
        font-size: 12px;
        color: #8090b0;
        font-weight: 600;
    }
    .panel-body {
        padding: 16px 22px;
    }

    /* ── Per Lokasi Grid ── */
    .loc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    @media (max-width: 900px) { .loc-grid { grid-template-columns: 1fr; } }

    /* ── Mini stat inside lokasi panel ── */
    .mini-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin: 14px 0;
    }
    .mini-stat {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid #f0f2f7;
    }
    .mini-stat-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #8090b0;
        margin-bottom: 6px;
    }
    .mini-stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: #0d1117;
        line-height: 1;
    }

    /* ── Ayam received row ── */
    .ayam-row {
        background: #f8f9fc;
        border: 1px solid #f0f2f7;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 600;
        color: #4a5577;
        margin-bottom: 12px;
    }
    .ayam-row b {
        font-family: 'Syne', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: #0d1117;
    }

    /* ── Live Running block ── */
    .running-block {
        background: rgba(245,158,11,0.05);
        border: 1px solid rgba(245,158,11,0.2);
        border-radius: 12px;
        padding: 14px 16px;
    }
    .running-block-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .running-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #b45309;
    }
    .pulse-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #f59e0b;
        animation: pulse 1.6s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.4; transform: scale(0.7); }
    }
    .running-empty {
        font-size: 12px;
        font-weight: 600;
        color: #8090b0;
        padding: 4px 0;
    }
    .running-report {
        font-size: 13px;
        font-weight: 700;
        color: #0d1117;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .running-detail {
        font-size: 12px;
        font-weight: 600;
        color: #8090b0;
        margin-bottom: 8px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .running-ayam {
        font-size: 13px;
        font-weight: 700;
        color: #4a5577;
    }
    .running-ayam b {
        font-family: 'Syne', sans-serif;
        color: #0d1117;
    }

    /* ── Badge ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge.running { background: rgba(245,158,11,0.1); color: #b45309; }
    .badge.running .badge-dot { background: #f59e0b; }

    /* ── Master Data ── */
    .master-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        padding: 16px 22px;
    }
    @media (max-width: 700px) { .master-grid { grid-template-columns: 1fr; } }

    /* ── Page header ── */
    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .page-title {
        font-family: 'Syne', sans-serif;
        font-size: 26px;
        font-weight: 800;
        color: #0d1117;
        margin: 0;
    }
    .page-subtitle {
        font-size: 13px;
        font-weight: 600;
        color: #8090b0;
        margin-top: 2px;
    }
    .readonly-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f2f7;
        border: 1px solid #e4e8f0;
        border-radius: 100px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 700;
        color: #8090b0;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
</style>

<div style="max-width:1200px;margin:0 auto;padding:28px 18px">

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">Dashboard</h2>
            <div class="page-subtitle">
                Ringkasan operasional tanggal
                <strong style="color:#0d1117">{{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}</strong>
            </div>
        </div>
    </div>

    {{-- Grand Summary --}}
    <div class="dash-grid">

        {{-- Total Truk --}}
        <div class="stat-card green">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Total Truk Hari Ini</div>
                    <div class="stat-value">{{ $grand['truk_total'] }}</div>
                </div>
                <div class="stat-icon green">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m9 0H5m8 0h3m-3 0V9m3 7h2a1 1 0 001-1v-4l-3-4h-2m0 0v4"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">Semua truk terdaftar hari ini</div>
            @php $pct_total = $grand['truk_total'] > 0 ? 100 : 0; @endphp
            <div class="stat-progress"><div class="stat-progress-bar" style="width:{{ $pct_total }}%"></div></div>
        </div>

        {{-- Truk Counted --}}
        <div class="stat-card blue">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Sudah Dihitung</div>
                    <div class="stat-value">{{ $grand['truk_counted'] }}</div>
                </div>
                <div class="stat-icon blue">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">Truk sudah dihitung hanging</div>
            @php $pct_counted = $grand['truk_total'] > 0 ? round(($grand['truk_counted'] / $grand['truk_total']) * 100) : 0; @endphp
            <div class="stat-progress"><div class="stat-progress-bar" style="width:{{ $pct_counted }}%"></div></div>
        </div>

        {{-- Truk Antrian --}}
        <div class="stat-card orange">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Antrian (Draft)</div>
                    <div class="stat-value">{{ $grand['truk_queue'] }}</div>
                </div>
                <div class="stat-icon orange">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">Menunggu proses penghitungan</div>
            @php $pct_queue = $grand['truk_total'] > 0 ? round(($grand['truk_queue'] / $grand['truk_total']) * 100) : 0; @endphp
            <div class="stat-progress"><div class="stat-progress-bar" style="width:{{ $pct_queue }}%"></div></div>
        </div>

        {{-- Ayam Diterima --}}
        <div class="stat-card purple">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Ayam Diterima</div>
                    <div class="stat-value">{{ number_format($grand['ayam_received']) }}</div>
                </div>
                <div class="stat-icon purple">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer">Total ekor diterima hari ini</div>
            <div class="stat-progress"><div class="stat-progress-bar" style="width:80%"></div></div>
        </div>

    </div>

    {{-- Per Lokasi --}}
    <div class="loc-grid">
        @foreach($statsByLoc as $loc => $s)
        <div class="panel">

            <div class="panel-header">
                <span class="panel-title">Lokasi {{ $loc }}</span>
                <span class="panel-meta">Total truk: <strong style="color:#0d1117">{{ $s['truk_total'] }}</strong></span>
            </div>

            <div class="panel-body">

                {{-- Mini stats --}}
                <div class="mini-stat-grid">
                    <div class="mini-stat">
                        <div class="mini-stat-label">Dihitung</div>
                        <div class="mini-stat-value">{{ $s['truk_counted'] }}</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-label">Antrian</div>
                        <div class="mini-stat-value">{{ $s['truk_queue'] }}</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-label">Run / Done</div>
                        <div class="mini-stat-value" style="font-size:18px">
                            {{ $s['truk_running'] }}<span style="color:#c5cce0;font-size:14px;margin:0 2px">/</span>{{ $s['truk_done'] }}
                        </div>
                    </div>
                </div>

                {{-- Ayam received --}}
                <div class="ayam-row">
                    <span>Ayam diterima hari ini</span>
                    <b>{{ number_format($s['ayam_received']) }}</b>
                </div>

                {{-- Live Running --}}
                <div class="running-block">
                    <div class="running-block-header">
                        <div class="pulse-dot"></div>
                        <span class="running-label">Live Running</span>
                    </div>

                    @if(!$s['running'])
                        <div class="running-empty">Tidak ada proses running saat ini.</div>
                    @else
                        <div class="running-report">
                            <span class="badge running">
                                <span class="badge-dot"></span>Running
                            </span>
                            <code style="background:#f0f2f7;padding:2px 8px;border-radius:6px;font-size:12px">
                                {{ $s['running']['report_code'] }}
                            </code>
                            <span>Truk <strong>#{{ $s['running']['truck_no'] }}</strong></span>
                        </div>
                        <div class="running-detail">
                            <span>
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:3px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $s['running']['expedition'] ?? '—' }}
                            </span>
                            <span>
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:3px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                {{ $s['running']['farm'] ?? '—' }}
                            </span>
                            <span>
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:3px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="2.5"/><path stroke-linecap="round" stroke-width="2.5" d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                {{ $s['running']['plate'] ?? '—' }}
                            </span>
                        </div>
                        <div class="running-ayam">
                            Total ayam running: <b>{{ number_format($s['running']['total_ayam']) }}</b>
                        </div>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Master Data --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Master Data</span>
        </div>
        <div class="master-grid">

            <div class="stat-card green" style="padding:16px 18px">
                <div class="stat-card-top" style="margin-bottom:6px">
                    <div>
                        <div class="stat-label">Ekspedisi</div>
                        <div class="stat-value" style="font-size:28px">{{ $master['expeditions'] }}</div>
                    </div>
                    <div class="stat-icon green" style="width:36px;height:36px">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 17l4 4 4-4m-4-5v9M3 5h18M3 5a2 2 0 002 2h14a2 2 0 002-2M3 5a2 2 0 012-2h14a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-footer">Ekspedisi tersimpan</div>
            </div>

            <div class="stat-card blue" style="padding:16px 18px">
                <div class="stat-card-top" style="margin-bottom:6px">
                    <div>
                        <div class="stat-label">Farm</div>
                        <div class="stat-value" style="font-size:28px">{{ $master['farms'] }}</div>
                    </div>
                    <div class="stat-icon blue" style="width:36px;height:36px">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-footer">Farm tersimpan</div>
            </div>

            @if(!is_null($master['plates']))
            <div class="stat-card orange" style="padding:16px 18px">
                <div class="stat-card-top" style="margin-bottom:6px">
                    <div>
                        <div class="stat-label">Kendaraan Truk</div>
                        <div class="stat-value" style="font-size:28px">{{ $master['plates'] }}</div>
                    </div>
                    <div class="stat-icon orange" style="width:36px;height:36px">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="10" rx="2" ry="2" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 11h.01M17 11h.01"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-footer">Plat Nomor tersimpan</div>
            </div>
            @endif

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bars = document.querySelectorAll('.stat-progress-bar');
        bars.forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => { bar.style.width = w; }, 120);
        });
    });
</script>

@endsection