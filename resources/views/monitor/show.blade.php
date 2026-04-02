<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Monitor • {{ $location }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:           #080a0d;
            --surface:      #0f1217;
            --card:         #13161e;
            --border:       #1c2030;
            --border-hi:    #2c3550;
            --accent:       #e8522a;
            --accent-glow:  rgba(232, 82, 42, 0.18);
            --accent-dim:   rgba(232, 82, 42, 0.08);
            --gold:         #f0a500;
            --gold-dim:     rgba(240, 165, 0, 0.1);
            --text:         #eef0f5;
            --muted:        #5a6380;
            --success:      #22c97a;
            --info:         #3b8aff;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            font-family: 'DM Sans', system-ui, sans-serif;
            height: 100vh;
            overflow: hidden;
            color: var(--text);
        }

        /* Ambient glow layers */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 70% 55% at 10% 90%, rgba(232,82,42,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 50% 45% at 90% 5%,  rgba(240,165,0,0.05) 0%, transparent 55%);
            pointer-events: none; z-index: 0;
        }

        /* Subtle grid */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.012) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.012) 1px, transparent 1px);
            background-size: 44px 44px;
            pointer-events: none; z-index: 0;
        }

        /* ═══════════════════════════════════════
           LAYOUT WRAPPER
        ═══════════════════════════════════════ */
        .wrap {
            position: relative; z-index: 1;
            height: 100vh;
            display: grid;
            grid-template-rows: auto auto 1fr 64px auto;
            gap: 0.65rem;
            padding: 0.7rem 1.2rem;
        }

        /* ═══════════════════════════════════════
           HEADER
        ═══════════════════════════════════════ */
        .header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid var(--border);
        }

        /* Logo */
        .logo-box {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: var(--card);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
        }
        .logo-box img { width: 100%; height: 100%; object-fit: cover; }

        /* Brand */
        .brand h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.3rem;
            letter-spacing: 1.5px;
            background: linear-gradient(135deg, #fff 25%, var(--gold) 100%);
            -webkit-background-clip: text; background-clip: text; color: transparent;
            line-height: 1.1;
        }
        .brand p {
            font-size: 0.58rem;
            color: var(--muted);
            letter-spacing: 0.4px;
        }

        /* Spacer */
        .h-gap { flex: 1; }

        /* Location badge */
        .loc-badge {
            display: flex; align-items: center; gap: 0.45rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 0.3rem 0.85rem;
        }
        .loc-badge svg { color: var(--accent); flex-shrink: 0; }
        .loc-lbl { font-size: 0.58rem; font-weight: 600; color: var(--muted); letter-spacing: 0.5px; }
        .loc-val {
            font-size: 0.82rem; font-weight: 700; color: #fff;
            background: var(--accent-dim);
            padding: 0.1rem 0.45rem; border-radius: 5px;
        }

        /* DateTime */
        .datetime-block {
            text-align: right;
        }
        .dt-time {
            font-family: 'Bebas Neue', monospace;
            font-size: 2rem; font-weight: 400;
            color: var(--text);
            letter-spacing: 3px;
            line-height: 1;
        }
        .dt-date {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: 0.3px;
            margin-top: 2px;
        }

        /* Report code */
        .report-pill {
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 2px solid var(--gold);
            border-radius: 8px;
            padding: 0.2rem 0.55rem;
            text-align: center;
        }
        .report-pill .lbl { font-size: 0.45rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .report-pill .val {
            font-family: 'DM Mono', monospace;
            font-size: 0.62rem; font-weight: 500;
            color: var(--gold);
        }

        /* Fullscreen button */
        .fs-btn {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0.3rem 0.7rem;
            cursor: pointer;
            display: flex; align-items: center; gap: 0.4rem;
            color: var(--muted);
            font-size: 0.65rem; font-weight: 500;
            font-family: inherit;
            transition: all 0.18s ease;
        }
        .fs-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-dim);
        }
        .fs-btn svg { width: 14px; height: 14px; }

        /* ═══════════════════════════════════════
           MAIN HERO — Total Ayam (SANGAT BESAR)
        ═══════════════════════════════════════ */
        .hero {
            background: linear-gradient(135deg, rgba(232,82,42,0.12) 0%, rgba(240,165,0,0.06) 100%);
            border: 1px solid rgba(232,82,42,0.28);
            border-radius: 20px;
            padding: 0.55rem 1.2rem 0.65rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            min-height: 0;
        }

        .hero-main { flex: 1; min-height: 0; }
        .hero-lbl {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 0.1rem;
        }
        .hero-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(8rem, 28vh, 22rem);
            line-height: 0.88;
            background: linear-gradient(135deg, #fff 0%, var(--gold) 60%, var(--accent) 100%);
            -webkit-background-clip: text; background-clip: text; color: transparent;
            letter-spacing: 2px;
        }
        .hero-pulse {
            font-size: 0.58rem;
            color: var(--success);
            display: flex; align-items: center; gap: 0.3rem;
            margin-top: 0.25rem;
        }
        .pulse-dot {
            width: 6px; height: 6px;
            background: var(--success); border-radius: 50%;
            animation: blink 1.4s ease-in-out infinite;
        }

        /* Hero secondary stats — total ekor & truck no */
        .hero-stats {
            display: flex;
            flex-direction: row;
            gap: 0.5rem;
            flex-shrink: 0;
        }
        .hstat {
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.4rem 1rem;
            text-align: center;
            min-width: 130px;
        }
        .hstat .lbl {
            font-size: 0.55rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
        }
        .hstat .val {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            line-height: 1;
            color: var(--text);
        }
        .hstat.accent-hi .val { color: var(--accent); }
        .hstat.gold-hi .val   { color: var(--gold);   }

        /* ═══════════════════════════════════════
           SUB MAIN — Ekspedisi & Farm
        ═══════════════════════════════════════ */
        .sub-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.65rem;
            height: 64px;
        }

        .sub-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.4rem 0.7rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: border-color 0.2s;
            overflow: hidden;
        }
        .sub-card:hover { border-color: var(--border-hi); }

        .sc-icon {
            width: 28px; height: 28px; flex-shrink: 0;
            background: var(--accent-dim);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--accent);
        }
        .sc-icon svg { width: 13px; height: 13px; }

        .sc-body { min-width: 0; }
        .sc-lbl {
            font-size: 0.48rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
            margin-bottom: 0.15rem;
        }
        .sc-val {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sc-sub {
            font-size: 0.6rem;
            color: var(--muted);
            margin-top: 0.05rem;
        }

        .sc-icon.gold { background: var(--gold-dim); color: var(--gold); }

        /* ═══════════════════════════════════════
           EMPTY STATE (inside hero area)
        ═══════════════════════════════════════ */
        .empty-state {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 0.4rem;
            text-align: center;
            height: 100%;
            padding: 1rem;
        }
        .empty-icon {
            width: 44px; height: 44px;
            background: var(--accent-dim);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: var(--accent);
        }
        .empty-title { font-size: 0.85rem; font-weight: 600; }
        .empty-desc  { font-size: 0.65rem; color: var(--muted); }

        /* ═══════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════ */
        .footer {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--border);
            font-size: 0.58rem;
            color: var(--muted);
            flex-shrink: 0;
        }

        .refresh-badge {
            display: flex; align-items: center; gap: 0.35rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 0.22rem 0.65rem;
            flex-shrink: 0;
        }
        .refresh-badge svg { color: var(--success); animation: spin 2s linear infinite; }

        .footer-counter {
            display: flex; align-items: center; gap: 0.5rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.35rem 1rem;
        }
        .fc-lbl { color: var(--muted); font-size: 0.7rem; font-weight: 500; }
        .fc-val {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem;
            line-height: 1;
            color: var(--text);
            letter-spacing: 1px;
        }

        .footer-right { margin-left: auto; }

        /* ═══════════════════════════════════════
           UTILITY
        ═══════════════════════════════════════ */
        .hidden { display: none !important; }

        @keyframes blink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.75); }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero, .sub-card { animation: fadeUp 0.35s ease-out both; }
        .sub-card:nth-child(2) { animation-delay: 0.06s; }

        /* Fullscreen overrides */
        :fullscreen .hero-num,
        :-webkit-full-screen .hero-num,
        :-moz-full-screen .hero-num {
            font-size: clamp(10rem, 34vh, 28rem);
        }
        :fullscreen .wrap,
        :-webkit-full-screen .wrap,
        :-moz-full-screen .wrap {
            padding: 0.9rem 1.5rem;
        }

        @media (max-width: 860px) {
            .hero-num { font-size: 4.5rem; }
            .sub-grid { grid-template-columns: 1fr; }
            .hero-stats { flex-direction: row; }
        }
        @media (max-width: 600px) {
            .datetime-block { display: none; }
            .hero { flex-direction: column; gap: 0.75rem; }
            .hero-stats { flex-direction: row; width: 100%; }
            .hstat { flex: 1; }
        }
        @media (max-width: 860px) {
            .hero-num { font-size: 4.5rem; }
            .sub-grid { 
                grid-template-columns: 1fr; 
                gap: 0.5rem;
            }
            .hero-stats { flex-direction: row; }
            
            /* Perbaikan untuk sub-card */
            .sub-card {
                padding: 0.5rem 0.7rem;
                gap: 0.5rem;
            }
            
            .sc-body {
                min-width: 0; /* Allow text truncation */
                flex: 1;
            }
            
            .sc-val {
                font-size: 0.7rem;
                white-space: normal; /* Allow text to wrap */
                word-break: break-word;
                line-height: 1.3;
            }
            
            .sc-sub {
                font-size: 0.55rem;
                white-space: normal;
                word-break: break-word;
            }
            
            .sc-icon {
                width: 32px;
                height: 32px;
                flex-shrink: 0;
            }
        }

        @media (max-width: 600px) {
            .wrap {
                padding: 0.5rem 0.8rem;
                gap: 0.5rem;
            }
            
            .datetime-block { display: none; }
            .hero { 
                flex-direction: column; 
                gap: 0.75rem;
                padding: 0.75rem;
            }
            .hero-stats { 
                flex-direction: row; 
                width: 100%;
                gap: 0.5rem;
            }
            .hstat { 
                flex: 1; 
                padding: 0.3rem 0.5rem;
                min-width: 0;
            }
            .hstat .val {
                font-size: 1.2rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .hstat .lbl {
                font-size: 0.5rem;
                white-space: nowrap;
            }
            
            /* Perbaikan sub-card di mobile */
            .sub-card {
                padding: 0.5rem;
                gap: 0.5rem;
            }
            
            .sc-val {
                font-size: 0.65rem;
                line-height: 1.2;
            }
            
            .sc-sub {
                font-size: 0.5rem;
                margin-top: 0.1rem;
            }
            
            /* Footer responsive */
            .footer {
                flex-wrap: wrap;
                gap: 0.5rem;
                padding-top: 0.5rem;
            }
            
            .footer-counter {
                padding: 0.2rem 0.6rem;
            }
            
            .footer-counter .fc-lbl {
                font-size: 0.6rem;
            }
            
            .footer-counter .fc-val {
                font-size: 1.1rem;
            }
            
            .refresh-badge {
                padding: 0.2rem 0.5rem;
            }
            
            .refresh-badge span {
                font-size: 0.5rem;
            }
            
            .footer-right {
                font-size: 0.5rem;
                margin-left: 0;
            }
        }

        /* Untuk layar sangat kecil (<= 400px) */
        @media (max-width: 400px) {
            .hero-stats {
                flex-direction: column;
                gap: 0.4rem;
            }
            
            .hstat {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                padding: 0.3rem 0.8rem;
            }
            
            .hstat .lbl {
                font-size: 0.55rem;
                margin-bottom: 0;
                white-space: normal;
            }
            
            .hstat .val {
                font-size: 1rem;
            }
            
            .sub-card {
                flex-wrap: wrap;
            }
            
            .sc-body {
                flex: 1;
                min-width: 0;
            }
            
            /* Header mobile */
            .header {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .loc-badge {
                order: 1;
            }
            
            .report-pill {
                order: 2;
            }
            
            .fs-btn {
                order: 3;
            }
            
            .h-gap {
                display: none;
            }
        }

        /* Untuk tampilan landscape di mobile */
        @media (max-width: 860px) and (orientation: landscape) {
            .wrap {
                padding: 0.4rem 0.8rem;
                gap: 0.4rem;
            }
            
            .hero {
                padding: 0.4rem;
            }
            
            .hero-num {
                font-size: 3rem;
            }
            
            .sub-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .footer {
                flex-wrap: nowrap;
            }
        }

        /* Tooltip untuk text yang terpotong */
        .sc-val[title], .sc-sub[title] {
            cursor: help;
        }

        /* Tambahkan text truncation dengan ellipsis untuk single line */
        .sc-val.single-line {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
<div class="wrap">

    <!-- ════════════════ HEADER ════════════════ -->
    <div class="header">
        <!-- Logo + Brand -->
        <div class="logo-box">
            <img src="{{ asset('images/logo small.png') }}" alt="Logo"
                 onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23e8522a%22 stroke-width=%221.5%22%3E%3Cpath d=%22M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5%22/%3E%3C/svg%3E';">
        </div>
        <div class="brand">
            <h1>Slaughter House</h1>
            <p>Live Production Monitor</p>
        </div>

        <div class="h-gap"></div>

        <!-- Location -->
        <div class="loc-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span class="loc-lbl">LOKASI</span>
            <span class="loc-val">{{ $location }}</span>
        </div>

        <!-- Date & Time -->
        <div class="datetime-block">
            <div class="dt-time" id="dtTime">--:--:--</div>
            <div class="dt-date" id="dtDate">--</div>
        </div>

        <!-- Report Code -->
        <div class="report-pill">
            <div class="lbl">Report Code</div>
            <div class="val" id="reportCode">—</div>
        </div>

        <!-- Fullscreen -->
        <button class="fs-btn" id="fsBtn" title="Fullscreen">
            <svg id="fsIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
            </svg>
            <span id="fsText">Fullscreen</span>
        </button>
    </div>

    <!-- ════════════════ HERO (MAIN) ════════════════ -->
    <div class="hero" id="heroSection">
        <!-- Left: giant number -->
        <div class="hero-main">
            <div class="hero-lbl">TOTAL AYAM DIPROSES</div>
            <div class="hero-num" id="heroAyam">0</div>
            <div class="hero-pulse" id="heroPulse">
                <span class="pulse-dot"></span>
                <span>Real-time</span>
            </div>
        </div>

        <!-- Right: Total Ekor + No Truk -->
        <div class="hero-stats">
            <div class="hstat gold-hi">
                <div class="lbl">TOTAL EKOR</div>
                <div class="val" id="statTotalEkor">0</div>
            </div>
            <div class="hstat accent-hi">
                <div class="lbl">NO. TRUK</div>
                <div class="val" id="statTruckNo">—</div>
            </div>
        </div>
    </div>

    <!-- ════════════════ SUB MAIN ════════════════ -->
    <div class="sub-grid" id="subGrid">
        <!-- Ekspedisi + Sopir -->
        <div class="sub-card" id="cardExpedisi">
            <div class="sc-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="sc-body">
                <div class="sc-lbl">EKSPEDISI &amp; SOPIR</div>
                <div class="sc-val" id="subExpedisi" title="">—</div>
                <div class="sc-sub" id="subDriver" title="">—</div>
            </div>
        </div>

        <!-- Size + Farm -->
        <div class="sub-card" id="cardFarm">
            <div class="sc-icon gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div class="sc-body">
                <div class="sc-lbl">SIZE AYAM &amp; FARM</div>
                <div class="sc-val" id="subFarm" title="">—</div>
                <div class="sc-sub" id="subSize" title="">—</div>
            </div>
        </div>
    </div>

    <!-- Empty State (overlay, shown when not active) -->
    <div id="emptyOverlay" class="hidden" style="grid-row: 2 / 4; align-self: stretch;">
        <div class="empty-state" style="height:100%; background: var(--card); border:1px solid var(--border); border-radius:20px;">
            <div class="empty-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8"  y1="2" x2="8"  y2="6"/>
                    <line x1="3"  y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="empty-title">Tidak Ada Proses Running</div>
            <div class="empty-desc">Belum ada aktivitas pemotongan saat ini</div>
        </div>
    </div>

    <!-- ════════════════ FOOTER ════════════════ -->
    <div class="footer">
        <!-- Auto refresh indicator -->
        <div class="refresh-badge">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            <span>Auto refresh 2 dtk</span>
        </div>

        <!-- Counter ayam hari ini -->
        <div class="footer-counter" style="border-color: rgba(240,165,0,0.3);">
            <span class="fc-lbl">🐔 Ayam hari ini</span>
            <span class="fc-val" id="footerAyam" style="color: var(--gold);">0</span>
        </div>

        <!-- Counter truk hari ini -->
        <div class="footer-counter" style="border-color: rgba(232,82,42,0.3);">
            <span class="fc-lbl">🚛 Truk hari ini</span>
            <span class="fc-val" id="footerTruck" style="color: var(--accent);">0</span>
        </div>

        <div class="footer-right">
            Update: <span id="lastUpdate" style="font-family:'DM Mono',monospace;">—</span>
        </div>
    </div>
</div>

<script>
    /* ─── Helpers ─── */
    function fmt(v) {
        return new Intl.NumberFormat('id-ID').format(Number(v) || 0);
    }
    function pad(n) { return String(n).padStart(2, '0'); }

    /* ─── Clock ─── */
    const DAYS_ID  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const MONTHS_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    function tickClock() {
        const now = new Date();
        document.getElementById('dtTime').textContent =
            `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        document.getElementById('dtDate').textContent =
            `${DAYS_ID[now.getDay()]}, ${now.getDate()} ${MONTHS_ID[now.getMonth()]} ${now.getFullYear()}`;
    }
    tickClock();
    setInterval(tickClock, 1000);

    /* ─── Fullscreen ─── */
    const fsBtn  = document.getElementById('fsBtn');
    const fsIcon = document.getElementById('fsIcon');
    const fsText = document.getElementById('fsText');

    const ICON_ENTER = '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>';
    const ICON_EXIT  = '<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/>';

    function syncFsBtn() {
        const full = !!document.fullscreenElement;
        fsIcon.innerHTML = full ? ICON_EXIT  : ICON_ENTER;
        fsText.textContent = full ? 'Exit'      : 'Fullscreen';
    }
    fsIcon.innerHTML = ICON_ENTER;
    fsBtn.addEventListener('click', () => {
        document.fullscreenElement
            ? document.exitFullscreen()
            : document.documentElement.requestFullscreen().catch(console.error);
    });
    document.addEventListener('fullscreenchange', syncFsBtn);

    /* ─── Data fetch ─── */
    function setActive(on) {
        const heroSection = document.getElementById('heroSection');
        const subGrid     = document.getElementById('subGrid');
        const emptyOverlay= document.getElementById('emptyOverlay');

        if (on) {
            heroSection.style.display  = '';
            subGrid.style.display      = '';
            emptyOverlay.style.display = 'none';
        } else {
            heroSection.style.display  = 'none';
            subGrid.style.display      = 'none';
            emptyOverlay.style.display = '';
        }
    }

    async function refresh() {
        try {
            const res = await fetch(`{{ route('monitor.data', $location) }}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const j = await res.json();

            /* Footer counters — always updated */
            document.getElementById('footerAyam').textContent  = fmt(j.today_total_ayam);
            document.getElementById('footerTruck').textContent = fmt(j.today_truck_count);

            const now = new Date();
            document.getElementById('lastUpdate').textContent =
                `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

            if (!j.active) {
                setActive(false);
                document.getElementById('reportCode').textContent = '—';
                return;
            }

            setActive(true);

            /* Header */
            document.getElementById('reportCode').textContent = j.report_code || '—';

            /* Hero */
            document.getElementById('heroAyam').textContent   = fmt(j.total_ayam_running);
            document.getElementById('statTotalEkor').textContent = fmt(j.total_ekor);
            document.getElementById('statTruckNo').textContent   = j.truck_no || '—';

            /* Sub main — Ekspedisi & Sopir */
            const expedisiName = j.expedition_name || '—';
            const driverName = j.driver_name || '—';
            const driverPhone = j.driver_phone ? '  ·  ' + j.driver_phone : '';
            const driverLine = driverName + driverPhone;
            
            const subExpedisi = document.getElementById('subExpedisi');
            const subDriver = document.getElementById('subDriver');
            
            subExpedisi.textContent = expedisiName;
            subExpedisi.title = expedisiName; // Tooltip untuk text panjang
            
            subDriver.textContent = driverLine;
            subDriver.title = driverLine; // Tooltip untuk text panjang

            /* Sub main — Size & Farm */
            const farmName = j.farm_name || '—';
            const sizeText = j.size ? 'Ukuran: ' + j.size : '—';
            
            const subFarm = document.getElementById('subFarm');
            const subSize = document.getElementById('subSize');
            
            subFarm.textContent = farmName;
            subFarm.title = farmName;
            
            subSize.textContent = sizeText;
            subSize.title = sizeText;

        } catch (err) {
            console.error('Refresh error:', err);
        }
    }

    /* Boot */
    refresh();
    const _iv = setInterval(refresh, 2000);
    window.addEventListener('beforeunload', () => clearInterval(_iv));
</script>
</body>
</html>