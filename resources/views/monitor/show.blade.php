<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Live Monitor • Slaughter House</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --grad-bg: radial-gradient(ellipse at 20% 30%, #0B2B26, #05161a);
            --accent-orange: #F97316;
            --accent-gold: #F59E0B;
            --accent-blue: #3B82F6;
            --accent-green: #10B981;
            --text-dark: #0F172A;
            --text-muted: #475569;
            --card-white: #FFFFFF;
            --shadow-xl: 0 25px 40px -12px rgba(0, 0, 0, 0.25);
            --shadow-card: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--grad-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* subtle grid texture */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDQwIDQwIj48cGF0aCBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDMiIGQ9Ik0wIDBoNDB2NDBIMHoiLz48cGF0aCBkPSJNMjAgMjBhMTAgMTAgMCAwIDEgMC0yMCAxMCAxMCAwIDAgMSAwIDIweiIgZmlsbD0iI2ZmZiIgZmlsbC1vcGFjaXR5PSIwLjAxIi8+PC9zdmc+');
            background-repeat: repeat;
            opacity: 0.3;
            pointer-events: none;
        }

        /* dashboard container — NO outer border, full bleed */
        .dashboard {
            width: 100%;
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 0;
            box-shadow: none;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            backdrop-filter: blur(0px);
        }

        .dashboard-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.2rem 1.5rem;
            gap: 1rem;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .dashboard-inner::-webkit-scrollbar {
            width: 4px;
            background: #E2E8F0;
        }

        /* HEADER */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.8rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(0, 0, 0, 0.06);
        }

        .brand-area {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .logo-icon {
            width: 46px;
            height: 46px;
            min-width: 46px;
            max-width: 46px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 12px -6px rgba(249,115,22,0.3);
            color: white;
            overflow: hidden;
        }
        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;  /* atau 'contain' tergantung kebutuhan */
            display: block;
        }
        .brand-text h2 {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.3px;
            background: linear-gradient(120deg, #1E293B, #F97316);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }
        .brand-text p {
            font-size: 0.65rem;
            font-weight: 500;
            color: #475569;
        }

        .info-chips {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .chip {
            background: white;
            border-radius: 60px;
            padding: 0.4rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            border: 1px solid #E9EEF3;
            font-weight: 500;
            font-size: 0.75rem;
        }
        .chip-loc {
            background: #FFF7ED;
            border-left: 3px solid var(--accent-orange);
        }
        .chip-code {
            font-family: 'DM Mono', monospace;
            background: #1E293B;
            color: #FEF08A;
            border: none;
        }
        .datetime {
            text-align: right;
        }
        .time-digital {
            font-size: 1.3rem;
            font-weight: 700;
            font-family: 'DM Mono', monospace;
            letter-spacing: 1px;
            color: #0F172A;
        }
        .date-text {
            font-size: 0.65rem;
            color: #64748B;
        }

        /* HERO — total ayam */
        .hero-grid {
            background: linear-gradient(125deg, #fffede 0%, #dbffe1 100%);
            border-radius: 1.8rem;
            padding: 1rem 1.8rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            align-items: center;
            box-shadow: var(--shadow-card);
            border: 1px solid rgba(249,115,22,0.2);
        }
        .hero-left {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hero-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--accent-orange);
        }
        /* angka sangat besar, maks 4 digit masih proporsional */
        .hero-number {
            font-size: clamp(4.5rem, 15vw, 9rem);
            font-weight: 900;
            background: linear-gradient(135deg, #F97316, #DC2626);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            line-height: 1;
            letter-spacing: -2px;
            margin: 0.2rem 0;
        }
        .hero-meta {
            display: flex;
            gap: 0.8rem;
            margin-top: 0.2rem;
        }
        .hero-stats-right {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        .stat-card {
            background: white;
            border-radius: 1.2rem;
            padding: 0.6rem 1.2rem;
            min-width: 120px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.02);
            border: 1px solid #F1F5F9;
        }
        .stat-card .stat-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
        }
        .stat-card .stat-value {
            font-size: 1.9rem;
            font-weight: 800;
            line-height: 1.2;
            color: #1E293B;
        }

        /* CAROUSEL — 2 slide saja (1: farm + size, 2: ekspedisi + sopir) */
        .carousel-module {
            background: white;
            border-radius: 1.5rem;
            padding: 0.6rem 1rem;
            box-shadow: var(--shadow-card);
            border: 1px solid #EDF2F7;
        }
        .carousel-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            padding-left: 0.2rem;
        }
        .carousel-header span {
            font-size: 0.65rem;
            font-weight: 700;
            background: #F1F5F9;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            color: #334155;
        }
        .slide-container {
            position: relative;
            min-height: 100px;
        }
        .slide {
            transition: opacity 0.55s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            opacity: 0;
            visibility: hidden;
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            width: 100%;
            pointer-events: none;
        }
        .slide.active {
            opacity: 1;
            visibility: visible;
            position: relative;
            pointer-events: auto;
        }
        .slide-item {
            background: #F8FAFE;
            border-radius: 1.2rem;
            padding: 0.7rem 1.2rem;
            flex: 1;
            border-left: 5px solid var(--accent-orange);
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.2s;
        }
        .slide-icon {
            width: 42px;
            height: 42px;
            background: #FFEDD5;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #EA580C;
        }
        .slide-content p:first-child {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .slide-content p:last-child {
            font-weight: 800;
            font-size: 1.2rem;
            color: #0F172A;
            word-break: break-word;
            line-height: 1.3;
        }
        .carousel-indicators {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 12px;
        }
        .indicator {
            width: 7px;
            height: 7px;
            border-radius: 20px;
            background: #CBD5E1;
            transition: all 0.2s;
            cursor: pointer;
        }
        .indicator.active {
            width: 24px;
            background: var(--accent-orange);
        }

        /* PROGRESS STRIP — angka diperbesar */
        .progress-strip {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .progress-card {
            background: #FFFFFF;
            border-radius: 1.5rem;
            padding: 0.9rem 1.2rem;
            box-shadow: var(--shadow-card);
            border: 1px solid #EFF3F8;
        }
        .progress-title {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1E293B;
        }
        .progress-title span:first-child { font-size: 0.8rem; }
        .progress-title span:last-child { font-size: 1rem; font-weight: 800; background: #F8FAFC; padding: 0.1rem 0.6rem; border-radius: 20px; }
        .progress-bar-bg {
            background: #E2E8F0;
            border-radius: 40px;
            height: 10px;
            overflow: hidden;
        }
        .progress-fill {
            width: 0%;
            height: 100%;
            border-radius: 40px;
            transition: width 0.5s ease;
        }
        .fill-ayam { background: linear-gradient(90deg, #F59E0B, #F97316); }
        .fill-truk { background: linear-gradient(90deg, #3B82F6, #06B6D4); }
        .stats-numbers {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
        }
        .stats-numbers strong {
            font-size: 1rem;
            color: #0F172A;
        }

        .footer-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.65rem;
            color: #475569;
            padding-top: 0.4rem;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        .live-badge {
            background: #ECFDF5;
            color: #059669;
            border-radius: 40px;
            padding: 0.2rem 0.9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .empty-state-modern {
            background: white;
            border-radius: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            text-align: center;
            padding: 2rem;
            color: #475569;
            border: 1px solid #F1F5F9;
        }
        .hidden { display: none !important; }

        /* ── Shift Complete Banner ── */
        @keyframes shiftBannerIn {
            from { opacity: 0; transform: translateY(-14px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)     scale(1);    }
        }
        @keyframes shiftGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.35), 0 6px 24px rgba(16,185,129,0.12); }
            50%       { box-shadow: 0 0 0 8px rgba(16,185,129,0),  0 8px 28px rgba(16,185,129,0.22); }
        }
        @keyframes checkPop {
            0%   { transform: scale(0) rotate(-15deg); opacity: 0; }
            60%  { transform: scale(1.25) rotate(5deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg);   opacity: 1; }
        }
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position:  200% center; }
        }
        .shift-complete-banner {
            display: none;
            align-items: center;
            gap: 1rem;
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 50%, #D1FAE5 100%);
            background-size: 200% auto;
            border: 2px solid #34D399;
            border-radius: 1.5rem;
            padding: 0.9rem 1.4rem;
            animation: shiftBannerIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both,
                       shiftGlow 2.4s ease-in-out 0.5s infinite,
                       shimmer 3.5s linear 0.5s infinite;
        }
        .shift-complete-banner.visible { display: flex; }
        .shift-complete-icon {
            width: 46px; height: 46px; min-width: 46px;
            background: linear-gradient(135deg, #10B981, #059669);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.4rem;
            animation: checkPop 0.6s cubic-bezier(0.34,1.56,0.64,1) 0.3s both;
            box-shadow: 0 4px 12px rgba(16,185,129,0.4);
        }
        .shift-complete-text { flex: 1; }
        .shift-complete-text .sct-title {
            font-size: 1rem; font-weight: 800; color: #065F46; letter-spacing: -0.2px;
        }
        .shift-complete-text .sct-sub {
            font-size: 0.72rem; font-weight: 600; color: #047857; margin-top: 2px;
        }
        .shift-complete-badge {
            background: #059669; color: white;
            border-radius: 40px; padding: 0.3rem 0.9rem;
            font-size: 0.72rem; font-weight: 800; letter-spacing: 0.05em;
            text-transform: uppercase; white-space: nowrap;
            box-shadow: 0 2px 8px rgba(5,150,105,0.4);
        }

        /* responsive: tidak ada border luar, semua menyesuaikan */
        @media (max-width: 800px) {
            .dashboard-inner { padding: 0.9rem 1rem; gap: 0.8rem; }
            .hero-grid { grid-template-columns: 1fr; gap: 0.6rem; text-align: center; }
            .hero-stats-right { justify-content: center; }
            .hero-number { font-size: 5rem; }
            .slide-item { flex-direction: column; align-items: flex-start; }
            .slide-content p:last-child { font-size: 1rem; }
            .progress-strip { grid-template-columns: 1fr; }
        }
        @media (max-width: 550px) {
            .top-bar { flex-direction: column; align-items: stretch; }
            .info-chips { justify-content: space-between; }
            .stat-card .stat-value { font-size: 1.3rem; }
            .hero-number { font-size: 3.8rem; }
            .logo-icon { width: 46px; height: 46px; min-width: 46px; max-width: 46px; }
        }
        body { overflow: hidden; }
        .dashboard, .dashboard-inner { overflow-y: auto; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="dashboard-inner">
        <!-- header -->
        <div class="top-bar">
            <div class="brand-area">
                <div class="logo-icon">
                    <img src="{{ asset('images/logo small.png') }}" alt="Logo">
                </div>
                <div class="brand-text">
                    <h2>Slaughter House</h2>
                    <p>Charoen Pokphand Indonesia • Live Monitor</p>
                </div>
            </div>
            <div class="info-chips">
                <div class="chip chip-loc">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#F97316" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span id="locationLabel">{{ $location }}</span>
                </div>
                <div class="chip chip-code">
                    <span>📄 REPORT</span>
                    <strong id="reportCodeDisplay">—</strong>
                </div>
                <div class="datetime">
                    <div class="time-digital" id="liveClock">--:--:--</div>
                    <div class="date-text" id="liveDate">--</div>
                </div>
            </div>
        </div>

        <!-- HERO : TOTAL AYAM DIPROSES -->
        <div class="hero-grid" id="heroGrid">
            <div class="hero-left">
                <div class="hero-label">TOTAL AYAM DIPROSES</div>
                <div class="hero-number" id="heroAyamTotal">0</div>
                <div class="hero-meta">
                    <span class="live-badge" style="background:#FFF0E6; color:#C2410C;">⚡ Real Count by Slaughtering Section</span>
                </div>
            </div>
            <div class="hero-stats-right">
                <div class="stat-card">
                    <div class="stat-label">📋 PLANNING EKOR</div>
                    <div class="stat-value" id="statTotalEkor">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">🚛 NO. TRUCK</div>
                    <div class="stat-value" id="statTruckNo">—</div>
                </div>
            </div>
        </div>

        <!-- CAROUSEL hanya 2 slide (FARM+SIZE digabung, EKSPEDISI+SOPIR) -->
        <div class="carousel-module">
            <div class="carousel-header">
                <span>📋 INFORMASI</span>
            </div>
            <div class="slide-container" id="carouselSlidesContainer">
                <!-- SLIDE 0 : FARM + SIZE (digabung, tanpa detail lokasi farm) -->
                <div class="slide active" data-slide="0">
                    <div class="slide-item">
                        <div class="slide-icon">🌾</div>
                        <div class="slide-content">
                            <p>FARM</p>
                            <p id="carouselFarm">—</p>
                        </div>
                    </div>
                    <div class="slide-item">
                        <div class="slide-icon">⚖️</div>
                        <div class="slide-content">
                            <p>SIZE AYAM</p>
                            <p id="carouselSize" style="font-size:1.3rem; font-weight:800;">—</p>
                        </div>
                    </div>
                </div>
                <!-- SLIDE 1 : EKSPEDISI + SOPIR -->
                <div class="slide" data-slide="1">
                    <div class="slide-item">
                        <div class="slide-icon">🚛</div>
                        <div class="slide-content">
                            <p>EKSPEDISI</p>
                            <p id="carouselExpedisi">—</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-indicators" id="carouselIndicators"></div>
        </div>

        <!-- PROGRESS: ayam & truk -->
        <div class="progress-strip">
            <div class="progress-card">
                <div class="progress-title">
                    <span>🐔 AYAM HARI INI</span>
                    <span><strong id="todayAyamCount">0</strong> / <strong id="planningAyamTotal">0</strong> ekor</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill fill-ayam" id="progressAyamFill" style="width:0%"></div>
                </div>
                <div class="stats-numbers">
                    <span>Daily Planning</span>
                    <span><strong id="ayamPercentLabel">0%</strong></span>
                </div>
            </div>
            <div class="progress-card">
                <div class="progress-title">
                    <span>🚚 NUMBER OF TRUCKS</span>
                    <span><strong id="todayTruckCount">0</strong> / <strong id="planningTruckTotal">0</strong> truck</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill fill-truk" id="progressTruckFill" style="width:0%"></div>
                </div>
                <div class="stats-numbers">
                    <span>Load Progress</span>
                    <span><strong id="truckPercentLabel">0%</strong></span>
                </div>
            </div>
        </div>

        <div class="footer-status">
            <div class="live-badge">
                <span class="pulse-dot" style="width:8px;height:8px;background:#10B981;border-radius:50%;display:inline-block;animation: pulse 1s infinite alternate;"></span>
                <span>Auto refresh 2 detik</span>
            </div>
            <div>Update: <span id="lastUpdateTime" style="font-family: monospace; font-weight:600;">--:--:--</span></div>
            <button id="fsBtnNew" style="background:transparent;border:none;color:#475569;font-size:0.7rem;cursor:pointer;display:flex;align-items:center;gap:5px;font-weight:500;">⛶ Fullscreen</button>
        </div>

        <!-- empty state -->
        <div id="emptyStateOverlay" class="empty-state-modern hidden">
            <div style="font-size:2.8rem;">🐓</div>
            <div style="font-weight:800;">Tidak Ada Proses Hanging</div>
            <div style="font-size:0.75rem;">Belum ada aktivitas pemotongan saat ini</div>
        </div>

        <div id="emptyReasonText" style="font-size:0.8rem;font-weight:700;color:#64748B;"></div>

        <!-- ── Shift Complete Banner ── -->
        <div class="shift-complete-banner" id="shiftCompleteBanner">
            <div class="shift-complete-icon">✓</div>
            <div class="shift-complete-text">
                <div class="sct-title" id="shiftCompleteTitle">Shift Selesai</div>
                <div class="sct-sub" id="shiftCompleteSub">Semua proses hanging pada shift ini telah selesai.</div>
            </div>
            <div class="shift-complete-badge">SELESAI</div>
        </div>
    </div>
</div>

<script>
    function fmt(v) { return new Intl.NumberFormat('id-ID').format(Number(v) || 0); }
    function pad(n) { return String(n).padStart(2, '0'); }

    // jam realtime
    function updateClock() {
        const now = new Date();
        document.getElementById('liveClock').innerText = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        document.getElementById('liveDate').innerHTML = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    }
    updateClock(); setInterval(updateClock, 1000);

    // fullscreen
    const fsBtn = document.getElementById('fsBtnNew');
    fsBtn?.addEventListener('click', () => {
        if (!document.fullscreenElement) document.documentElement.requestFullscreen();
        else document.exitFullscreen();
    });

    // carousel 2 slide (farm+size, expedisi+sopir)
    // CAROUSEL - Versi SMOOTH tanpa merusak data
    let activeSlide = 0;
    let carouselInterval;
    const slides = document.querySelectorAll('.slide');
    const indicatorsContainer = document.getElementById('carouselIndicators');

    function buildIndicators(count) {
        if(!indicatorsContainer) return;
        indicatorsContainer.innerHTML = '';
        for(let i = 0; i < count; i++) {
            const dot = document.createElement('div');
            dot.classList.add('indicator');
            if(i === activeSlide) dot.classList.add('active');
            dot.addEventListener('click', () => {
                stopCarousel();
                setActiveSlide(i);
                startCarousel();
            });
            indicatorsContainer.appendChild(dot);
        }
    }

    function setActiveSlide(index) {
        if(index === activeSlide) return;
        
        // Hapus class active dari semua slide
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Tambahkan class active ke slide yang dituju
        slides[index].classList.add('active');
        
        // Update indicators
        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach((dot, i) => {
            if(i === index) dot.classList.add('active');
            else dot.classList.remove('active');
        });
        
        activeSlide = index;
    }

    function nextSlide() {
        let next = (activeSlide + 1) % slides.length;
        setActiveSlide(next);
    }

    function startCarousel() {
        if(carouselInterval) clearInterval(carouselInterval);
        carouselInterval = setInterval(nextSlide, 4200);
    }

    function stopCarousel() {
        if(carouselInterval) clearInterval(carouselInterval);
    }

    // Inisialisasi carousel
    if(slides.length) {
        buildIndicators(slides.length);
        startCarousel();
    }

    // DOM elements
    const heroAyamSpan = document.getElementById('heroAyamTotal');
    const statEkorSpan = document.getElementById('statTotalEkor');
    const statTruckNoSpan = document.getElementById('statTruckNo');
    const reportCodeSpan = document.getElementById('reportCodeDisplay');
    const carouselFarm = document.getElementById('carouselFarm');
    const carouselSize = document.getElementById('carouselSize');
    const carouselExpedisi = document.getElementById('carouselExpedisi');
    const carouselDriver = document.getElementById('carouselDriver');
    const todayAyamSpan = document.getElementById('todayAyamCount');
    const planningAyamSpan = document.getElementById('planningAyamTotal');
    const todayTruckSpan = document.getElementById('todayTruckCount');
    const planningTruckSpan = document.getElementById('planningTruckTotal');
    const progressAyamFill = document.getElementById('progressAyamFill');
    const progressTruckFill = document.getElementById('progressTruckFill');
    const ayamPercentLabel = document.getElementById('ayamPercentLabel');
    const truckPercentLabel = document.getElementById('truckPercentLabel');
    const lastUpdateSpan = document.getElementById('lastUpdateTime');
    const emptyOverlay = document.getElementById('emptyStateOverlay');
    const heroGrid = document.getElementById('heroGrid');
    const carouselModule = document.querySelector('.carousel-module');
    const progressStrip = document.querySelector('.progress-strip');

    function setActiveUI(active) {
        if(active) {
            emptyOverlay.classList.add('hidden');
            heroGrid.style.display = '';
            carouselModule.style.display = '';
            // PROGRESS STRIP TETAP DITAMPILKAN meskipun tidak active
            progressStrip.style.display = '';
        } else {
            emptyOverlay.classList.remove('hidden');
            heroGrid.style.display = 'none';
            carouselModule.style.display = 'none';
            // Progress strip tetap ditampilkan (jangan disembunyikan)
            // progressStrip.style.display = 'none';  // HAPUS atau COMMENT baris ini
        }
    }

    async function fetchData() {
        try {
            const res = await fetch(`{{ route('monitor.data', $location) }}`, { headers: { 'Accept': 'application/json' } });
            if(!res.ok) throw new Error();
            const data = await res.json();
            const now = new Date();
            lastUpdateSpan.innerText = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

            const todayAyam = data.today_total_ayam || 0;
            const totalPlanningAyam = data.total_planning_ayam || 0;
            const todayTruck = data.today_truck_count || 0;
            const totalPlanningTruk = data.total_planning_truk || 0;

            todayAyamSpan.innerText = fmt(todayAyam);
            planningAyamSpan.innerText = fmt(totalPlanningAyam);
            todayTruckSpan.innerText = fmt(todayTruck);
            planningTruckSpan.innerText = fmt(totalPlanningTruk);
            let ayamPercent = totalPlanningAyam > 0 ? (todayAyam / totalPlanningAyam) * 100 : 0;
            let truckPercent = totalPlanningTruk > 0 ? (todayTruck / totalPlanningTruk) * 100 : 0;
            ayamPercent = Math.min(ayamPercent, 100);
            truckPercent = Math.min(truckPercent, 100);
            progressAyamFill.style.width = ayamPercent + '%';
            progressTruckFill.style.width = truckPercent + '%';
            ayamPercentLabel.innerText = Math.floor(ayamPercent) + '%';
            truckPercentLabel.innerText = Math.floor(truckPercent) + '%';

            // ── Shift Complete Banner ──
            const shiftBanner     = document.getElementById('shiftCompleteBanner');
            const shiftTitleEl    = document.getElementById('shiftCompleteTitle');
            const shiftSubEl      = document.getElementById('shiftCompleteSub');
            const isShiftDone     = !data.active && data.no_process_reason === 'target_reached' && data.shift_done_message;
            if (shiftBanner) {
                if (isShiftDone) {
                    shiftTitleEl.textContent = data.shift_done_message;
                    shiftSubEl.textContent   = 'Semua proses hanging pada shift ini telah selesai.';
                    // Re-trigger animation by removing + re-adding class
                    shiftBanner.classList.remove('visible');
                    void shiftBanner.offsetWidth;
                    shiftBanner.classList.add('visible');
                } else {
                    shiftBanner.classList.remove('visible');
                }
            }

            if (!data.active) {
                setActiveUI(false);
                reportCodeSpan.innerText = '—';

                const reasonEl = document.getElementById('emptyReasonText');
                if (reasonEl) {
                    reasonEl.textContent = ''; // Banner sudah menampilkan pesan shift selesai
                }
                
                // Kosongkan atau set default value untuk field yang tidak ada datanya
                heroAyamSpan.innerText = '0';
                statEkorSpan.innerText = '0';
                statTruckNoSpan.innerText = '—';
                carouselFarm.innerText = '—';
                carouselSize.innerText = '—';
                carouselExpedisi.innerText = '—';
                carouselDriver.innerText = '—';
                
                // Update progress saja, tanpa return
                todayAyamSpan.innerText = fmt(data.today_total_ayam || 0);
                planningAyamSpan.innerText = fmt(data.total_planning_ayam || 0);
                todayTruckSpan.innerText = fmt(data.today_truck_count || 0);
                planningTruckSpan.innerText = fmt(data.total_planning_truk || 0);
                
                let ayamPercent = (data.total_planning_ayam || 0) > 0 ? ((data.today_total_ayam || 0) / (data.total_planning_ayam || 1)) * 100 : 0;
                let truckPercent = (data.total_planning_truk || 0) > 0 ? ((data.today_truck_count || 0) / (data.total_planning_truk || 1)) * 100 : 0;
                ayamPercent = Math.min(ayamPercent, 100);
                truckPercent = Math.min(truckPercent, 100);
                progressAyamFill.style.width = ayamPercent + '%';
                progressTruckFill.style.width = truckPercent + '%';
                ayamPercentLabel.innerText = Math.floor(ayamPercent) + '%';
                truckPercentLabel.innerText = Math.floor(truckPercent) + '%';
                
                return;
            }
            setActiveUI(true);
            reportCodeSpan.innerText = data.report_code || '—';
            heroAyamSpan.innerText = fmt(data.total_ayam_running);
            statEkorSpan.innerText = fmt(data.total_ekor);
            statTruckNoSpan.innerText = data.truck_no || '—';

            // Update Carousel: Farm + Size (tanpa detail lokasi farm)
            const farmName = data.farm_name || '—';
            const sizeValue = data.size || 'Standar';
            carouselFarm.innerText = farmName;
            carouselSize.innerText = sizeValue;
            // ukuran teks size diperbesar via css, class sudah besar
            
            // Ekspedisi + Sopir
            const expedition = data.expedition_name || '—';
            let driverInfo = data.driver_name || '—';
            if (data.driver_phone) driverInfo += `  ·  ${data.driver_phone}`;
            carouselExpedisi.innerText = expedition;
            carouselDriver.innerText = driverInfo;
            
            // tooltips untuk info tambahan
            carouselFarm.setAttribute('title', farmName);
            carouselSize.setAttribute('title', sizeValue);
            carouselExpedisi.setAttribute('title', expedition);
            carouselDriver.setAttribute('title', driverInfo);
        } catch(e) { console.warn(e); }
    }

    fetchData();
    const intervalId = setInterval(fetchData, 2000);
    window.addEventListener('beforeunload', ()=> clearInterval(intervalId));

    const pulseStyle = document.createElement('style');
    pulseStyle.innerText = `@keyframes pulse { 0% { opacity:0.4; transform:scale(0.9);} 100%{ opacity:1; transform:scale(1.2);}} .pulse-dot { animation: pulse 0.9s infinite alternate; }`;
    document.head.appendChild(pulseStyle);
</script>
</body>
</html>