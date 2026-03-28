<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Monitor • SlaughterHouse</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        /* Premium design system - COMPACT SINGLE SCREEN VIEW */
        :root {
            --bg: #0a0c0f;
            --card: #111318;
            --border: #1e2330;
            --border-glow: #2e3a52;
            --accent: #e85d2f;
            --accent-soft: rgba(232, 93, 47, 0.12);
            --accent2: #f5a623;
            --text: #f0f2f5;
            --muted: #6b7591;
            --success: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            font-family: 'DM Sans', 'Inter', system-ui, -apple-system, sans-serif;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Atmospheric background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 15% 80%, rgba(232,93,47,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 10%, rgba(245,166,35,0.06) 0%, transparent 55%);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        .monitor-container {
            position: relative;
            z-index: 1;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0.75rem 1.25rem;
            gap: 0.75rem;
            overflow: hidden;
        }

        /* Header - Compact */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
            gap: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .company-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--card);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-text h1 {
            font-family: 'Syne', 'DM Sans', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.3px;
            background: linear-gradient(135deg, #fff 30%, var(--accent2) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 0.6rem;
            color: var(--muted);
        }

        .location-badge {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 0.35rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .location-icon {
            color: var(--accent);
            width: 14px;
            height: 14px;
        }

        .location-text {
            font-size: 0.7rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .location-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text);
            background: var(--accent-soft);
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
        }

        .report-code-card {
            background: rgba(17, 19, 24, 0.8);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 0.4rem 1rem;
            text-align: center;
            border-left: 2px solid var(--accent);
        }

        .report-label {
            font-size: 0.55rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
        }

        .report-value {
            font-family: monospace;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--accent2);
        }

        /* Fullscreen Button */
        .fullscreen-btn {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.35rem 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            color: var(--muted);
            font-size: 0.7rem;
            font-weight: 500;
        }

        .fullscreen-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-soft);
        }

        .fullscreen-btn svg {
            width: 16px;
            height: 16px;
        }

        /* HERO TOTAL AYAM - LARGE BUT COMPACT */
        .hero-ayam-section {
            background: linear-gradient(135deg, rgba(232,93,47,0.1) 0%, rgba(245,166,35,0.05) 100%);
            border-radius: 24px;
            padding: 0.6rem 1rem;
            border: 1px solid rgba(232,93,47,0.25);
            flex-shrink: 0;
        }

        .hero-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            color: var(--accent2);
            text-align: center;
        }

        .hero-number {
            font-family: 'Syne', monospace;
            font-size: 4.5rem;
            font-weight: 900;
            text-align: center;
            line-height: 1;
            background: linear-gradient(135deg, #FFFFFF 0%, var(--accent2) 70%, var(--accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -1px;
        }

        .hero-sub {
            text-align: center;
            color: var(--muted);
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        /* 3-Column Stats Grid - Compact */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 0.7rem 0.8rem;
            transition: all 0.2s;
        }

        .stat-card:hover {
            border-color: var(--border-glow);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .stat-title {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: var(--muted);
        }

        .stat-icon {
            width: 24px;
            height: 24px;
            background: var(--accent-soft);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
        }

        .stat-icon svg {
            width: 12px;
            height: 12px;
        }

        .stat-value {
            font-family: 'Syne', monospace;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1.2;
        }

        .stat-value-sm {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .stat-sub {
            font-size: 0.55rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(16, 185, 129, 0.1);
            padding: 0.2rem 0.6rem;
            border-radius: 100px;
            font-size: 0.65rem;
            color: var(--success);
        }

        /* Content Panel - Compact Data Grid */
        .content-panel {
            background: rgba(17, 19, 24, 0.5);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 0.8rem 1rem;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
            height: 100%;
        }

        .data-item {
            background: rgba(10, 12, 15, 0.5);
            border-radius: 16px;
            padding: 0.7rem;
            text-align: center;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .data-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }

        .data-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            word-break: break-word;
        }

        .data-value-sm {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .empty-icon {
            width: 48px;
            height: 48px;
            background: var(--accent-soft);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .empty-title {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .empty-desc {
            font-size: 0.7rem;
            color: var(--muted);
        }

        /* Footer Compact */
        .footer-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            padding-top: 0.5rem;
            border-top: 1px solid var(--border);
            font-size: 0.55rem;
            color: var(--muted);
        }

        .auto-refresh {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--card);
            padding: 0.25rem 0.7rem;
            border-radius: 100px;
            border: 1px solid var(--border);
        }

        .hidden {
            display: none !important;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeSlideUp 0.3s ease-out forwards;
        }

        /* Fullscreen mode adjustments */
        :-webkit-full-screen .monitor-container {
            padding: 1rem 1.5rem;
        }
        :-moz-full-screen .monitor-container {
            padding: 1rem 1.5rem;
        }
        :fullscreen .monitor-container {
            padding: 1rem 1.5rem;
        }

        :-webkit-full-screen .hero-number {
            font-size: 6rem;
        }
        :-moz-full-screen .hero-number {
            font-size: 6rem;
        }
        :fullscreen .hero-number {
            font-size: 6rem;
        }

        /* Compact Responsive */
        @media (max-width: 900px) {
            .hero-number { font-size: 3rem; }
            :fullscreen .hero-number { font-size: 4.5rem; }
            .stat-value { font-size: 1rem; }
            .data-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 700px) {
            .stats-grid { grid-template-columns: 1fr; }
            .data-grid { grid-template-columns: 1fr; }
            .header-section { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
<div class="monitor-container">
    <!-- Header - Compact with Fullscreen Button -->
    <div class="header-section">
        <div class="logo-area">
            <div class="company-logo">
                <img src="{{ asset('images/logo small.png') }}" alt="Logo" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2242%22%20height%3D%2242%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23e85d2f%22%20stroke-width%3D%221.5%22%3E%3Cpath%20d%3D%22M12%202L2%207l10%205%2010-5-10-5zM2%2017l10%205%2010-5M2%2012l10%205%2010-5%22%2F%3E%3C%2Fsvg%3E';">
            </div>
            <div class="brand-text">
                <h1>Slaughter House</h1>
                <p>Live Production Monitor</p>
            </div>
        </div>
        <div class="location-badge">
            <svg class="location-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span class="location-text">LOKASI</span>
            <span class="location-name" id="locationName">{{ $location }}</span>
        </div>
        <div class="report-code-card">
            <div class="report-label">Report Code</div>
            <div class="report-value" id="reportCode">—</div>
        </div>
        <button class="fullscreen-btn" id="fullscreenBtn" title="Fullscreen Mode">
            <svg id="fullscreenIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
            </svg>
            <span id="fullscreenText">Fullscreen</span>
        </button>
    </div>

    <!-- HERO TOTAL AYAM - ANGKA PALING BESAR -->
    <div class="hero-ayam-section">
        <div class="hero-label">TOTAL AYAM DIPROSES</div>
        <div class="hero-number" id="heroTotalAyam">0</div>
        <div class="hero-sub">
            <span class="pulse-dot"></span>
            <span>Real-time Count</span>
        </div>
    </div>

    <!-- 3-Column Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">NOMINAL EKORAN</span>
                <span class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1v22M17 5H9.5M17 5a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V8a3 3 0 0 1 3-3h10Z"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value" id="statNominal">0</div>
            <div class="stat-sub">Total ekor ayam</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">AKTIFITAS</span>
                <span class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
            </div>
            <div class="status-badge" id="statusBadge">
                <span class="pulse-dot"></span>
                <span id="statusText">Menunggu</span>
            </div>
            <div class="stat-sub" id="activeDetail">-</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">TRUCK</span>
                <span class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value stat-value-sm" id="truckExpedition">—</div>
            <div class="stat-sub">nomor truk / ekspedisi</div>
        </div>
    </div>

    <!-- Content Panel - Detail Data -->
    <div class="content-panel">
        <!-- Empty State -->
        <div id="emptyState" class="empty-state">
            <div class="empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="empty-title">Tidak Ada Proses Running</div>
            <div class="empty-desc">Belum ada aktivitas pemotongan</div>
        </div>

        <!-- Active Data Row -->
        <div id="activeDataRow" class="hidden" style="height: 100%;">
            <div class="data-grid">
                <div class="data-item">
                    <div class="data-label">Sopir & Ekspedisi</div>
                    <div class="data-value data-value-sm" id="expDriver">—</div>
                </div>
                <div class="data-item">
                    <div class="data-label">Ukuran & Farm</div>
                    <div class="data-value data-value-sm" id="sizeFarm">—</div>
                </div>
                <div class="data-item">
                    <div class="data-label">Total Ayam</div>
                    <div class="data-value" id="totalAyamDetail">0</div>
                </div>
                <div class="data-item">
                    <div class="data-label">Nominal Ekoran</div>
                    <div class="data-value" id="nominalDetail">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Compact -->
    <div class="footer-info">
        <div class="auto-refresh">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 12a11 11 0 0 1-22 0 11 11 0 0 1 22 0z"/>
                <path d="M12 8v4l3 3"/>
            </svg>
            <span>Auto refresh 2dtk</span>
        </div>
        <div>© 2025 SlaughterHouse</div>
        <div id="lastUpdate">Update: <span id="lastUpdateTime">—</span></div>
    </div>
</div>

<script>
    // Format angka tanpa Rp (numeric dengan separator)
    function formatNumber(value) {
        if (value === undefined || value === null) return '0';
        return new Intl.NumberFormat('id-ID').format(Number(value));
    }

    // Update timestamp
    function updateTimestamp() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const updateSpan = document.getElementById('lastUpdateTime');
        if (updateSpan) updateSpan.textContent = timeStr;
    }

    // ==================== FULLSCREEN FUNCTIONALITY ====================
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const fullscreenIcon = document.getElementById('fullscreenIcon');
    const fullscreenText = document.getElementById('fullscreenText');
    
    // Icons for fullscreen/exit
    const enterFullscreenIcon = '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>';
    const exitFullscreenIcon = '<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/>';
    
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            // Enter fullscreen
            document.documentElement.requestFullscreen().catch(err => {
                console.error(`Fullscreen error: ${err.message}`);
            });
        } else {
            // Exit fullscreen
            document.exitFullscreen();
        }
    }
    
    function updateFullscreenButton() {
        const isFullscreen = !!document.fullscreenElement;
        if (isFullscreen) {
            fullscreenIcon.innerHTML = exitFullscreenIcon;
            fullscreenText.textContent = 'Exit';
            fullscreenBtn.title = 'Exit Fullscreen';
        } else {
            fullscreenIcon.innerHTML = enterFullscreenIcon;
            fullscreenText.textContent = 'Fullscreen';
            fullscreenBtn.title = 'Fullscreen Mode';
        }
    }
    
    // Event listeners for fullscreen
    fullscreenBtn.addEventListener('click', toggleFullscreen);
    document.addEventListener('fullscreenchange', updateFullscreenButton);
    document.addEventListener('webkitfullscreenchange', updateFullscreenButton);
    document.addEventListener('mozfullscreenchange', updateFullscreenButton);
    document.addEventListener('MSFullscreenChange', updateFullscreenButton);
    
    // Initialize icon
    fullscreenIcon.innerHTML = enterFullscreenIcon;
    // ==================== END FULLSCREEN ====================

    // Main refresh function
    async function refresh() {
        try {
            const res = await fetch(`{{ route('monitor.data', $location) }}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Network error');
            const j = await res.json();

            const emptyState = document.getElementById('emptyState');
            const activeRow = document.getElementById('activeDataRow');
            const reportCodeSpan = document.getElementById('reportCode');
            const statusTextSpan = document.getElementById('statusText');
            const activeDetailSpan = document.getElementById('activeDetail');
            const heroTotalSpan = document.getElementById('heroTotalAyam');
            const statNominalSpan = document.getElementById('statNominal');
            const truckExpeditionSpan = document.getElementById('truckExpedition');

            // Check if active process exists
            const isActive = j.active === true || (j.active !== false && j.truck_no && j.truck_no !== null && j.truck_no !== '');

            if (!isActive) {
                activeRow.classList.add('hidden');
                emptyState.classList.remove('hidden');
                reportCodeSpan.textContent = '—';
                statusTextSpan.textContent = 'Tidak Aktif';
                activeDetailSpan.textContent = 'Tidak ada proses';
                heroTotalSpan.textContent = '0';
                statNominalSpan.textContent = '0';
                document.getElementById('totalAyamDetail').textContent = '0';
                document.getElementById('nominalDetail').textContent = '0';
                truckExpeditionSpan.textContent = '—';
                document.getElementById('expDriver').textContent = '—';
                document.getElementById('sizeFarm').textContent = '—';
                updateTimestamp();
                return;
            }

            // Active process
            emptyState.classList.add('hidden');
            activeRow.classList.remove('hidden');

            // Extract data
            const reportCode = j.report_code || '-';
            const truckNo = j.truck_no || '-';
            const expeditionName = j.expedition_name || '-';
            const driverName = j.driver_name || '-';
            const size = j.size || '-';
            const farmName = j.farm_name || '-';
            const totalAyam = (j.total_ayam !== undefined && j.total_ayam !== null) ? Number(j.total_ayam) : 0;
            const farmFee = (j.farm_fee_amount !== undefined && j.farm_fee_amount !== null) ? Number(j.farm_fee_amount) : 0;

            // Update all displays
            reportCodeSpan.textContent = reportCode;
            statusTextSpan.textContent = 'AKTIF • RUNNING';
            activeDetailSpan.textContent = `Report: ${reportCode}`;
            
            // Hero Total Ayam - ANGKA SANGAT BESAR
            heroTotalSpan.textContent = formatNumber(totalAyam);
            
            // Nominal tanpa Rp
            statNominalSpan.textContent = formatNumber(farmFee);
            document.getElementById('nominalDetail').textContent = formatNumber(farmFee);
            document.getElementById('totalAyamDetail').textContent = formatNumber(totalAyam);
            
            // Truck & Expedition
            truckExpeditionSpan.textContent = `${truckNo}`;
            if (expeditionName !== '-') {
                truckExpeditionSpan.textContent = `${truckNo} • ${expeditionName}`;
            }
            
            // Detail fields
            document.getElementById('expDriver').textContent = `${expeditionName} - ${driverName}`;
            document.getElementById('sizeFarm').textContent = `${size} - ${farmName}`;
            
            updateTimestamp();

        } catch (error) {
            console.error('Refresh error:', error);
            const emptyState = document.getElementById('emptyState');
            const activeRow = document.getElementById('activeDataRow');
            if (activeRow) activeRow.classList.add('hidden');
            if (emptyState) {
                emptyState.classList.remove('hidden');
                const emptyTitle = emptyState.querySelector('.empty-title');
                const emptyDesc = emptyState.querySelector('.empty-desc');
                if (emptyTitle) emptyTitle.textContent = 'Koneksi Error';
                if (emptyDesc) emptyDesc.textContent = 'Gagal mengambil data, coba refresh halaman';
            }
            document.getElementById('reportCode').textContent = 'ERR';
            document.getElementById('heroTotalAyam').textContent = '0';
        }
    }

    // Initial load and interval
    refresh();
    const intervalId = setInterval(refresh, 2000);

    window.addEventListener('beforeunload', () => {
        if (intervalId) clearInterval(intervalId);
    });

    // Add subtle animation
    document.querySelectorAll('.stat-card, .hero-ayam-section, .content-panel').forEach(el => {
        el.classList.add('animate-in');
    });
</script>
</body>
</html>