<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Charoen Pokphand Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg: #f0f2f7;
            --text-main: #0d1117;
            --text-muted: #6b7896;
            --accent: #e85d2f;
            --white: #ffffff;
            --card-border: #e4e8f0;
            --header-bg: rgba(255,255,255,0.92);
            --success: #10b981;
            --error: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
        }

        /* Topbar */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 0 22px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
        }
        .brand img {
            width: 34px; height: 34px;
            border-radius: 8px;
            object-fit: contain;
            background: white;
            border: 1px solid var(--card-border);
        }
        .brand-title {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 14px;
            line-height: 1.1;
            color: var(--text-main);
        }
        .brand-sub {
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .topnav {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .topnav-link,
        .topnav-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            line-height: 1;
        }
        .topnav-link:hover,
        .topnav-button:hover {
            background: #f5f7fc;
            border-color: #d0d7e6;
            color: var(--text-main);
        }
        .topnav-link.active,
        .topnav-button.active {
            background: rgba(232,93,47,0.10);
            border-color: rgba(232,93,47,0.25);
            color: var(--accent);
        }

        .pill-live {
            margin-left: 4px;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(232,93,47,0.12);
            color: var(--accent);
        }

        /* Dropdown */
        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            position: absolute;
            top: 44px;
            left: 0;
            min-width: 210px;
            background: white;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 14px 36px rgba(0,0,0,0.10);
            padding: 6px;
            z-index: 60;
            display: none;
        }
        .dropdown-menu.open { display: block; }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 10px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-main);
            font-size: 13px;
            font-weight: 600;
        }
        .dropdown-item:hover {
            background: #f5f7fc;
        }
        .dropdown-item.active {
            color: var(--accent);
            background: rgba(232,93,47,0.08);
        }

        /* User area */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: white;
        }
        .avatar {
            width: 30px; height: 30px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: white;
            font-size: 12px;
        }
        .user-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 12px;
            background: rgba(232,93,47,0.08);
            border: 1px solid rgba(232,93,47,0.2);
            border-radius: 10px;
            color: #e85d2f;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            line-height: 1;
        }
        .btn-logout:hover {
            background: rgba(232,93,47,0.15);
            border-color: rgba(232,93,47,0.4);
        }

        /* Content */
        .content-area {
            padding: 22px;
        }

        /* Alerts (pakai style Anda) */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 13px;
        }
        .alert-success {
            background: rgba(16,185,129,0.08);
            border: 1px solid rgba(16,185,129,0.25);
            color: #065f46;
        }
        .alert-error {
            background: rgba(239,68,68,0.07);
            border: 1px solid rgba(239,68,68,0.25);
            color: #7f1d1d;
        }
        .alert-icon { flex-shrink: 0; margin-top: 1px; }
        .alert-title { font-weight: 800; margin-bottom: 3px; }
        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.5;
            padding: 0;
            flex-shrink: 0;
        }
        .alert-close:hover { opacity: 1; }

        /* Responsive */
        @media (max-width: 980px) {
            .brand { min-width: auto; }
            .user-name { display: none; }
        }
        /* ===== Hamburger + Mobile Drawer ===== */
        .hamburger {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px; height: 40px;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: white;
            color: var(--text-main);
            cursor: pointer;
        }

        .mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 80;
            display: none;
        }

        .mobile-drawer {
            position: fixed;
            top: 0; right: 0;
            width: min(86vw, 360px);
            height: 100vh;
            background: white;
            border-left: 1px solid var(--card-border);
            box-shadow: -18px 0 40px rgba(0,0,0,0.12);
            z-index: 90;
            transform: translateX(100%);
            transition: transform .18s ease;
            display: flex;
            flex-direction: column;
        }

        .mobile-drawer.open { transform: translateX(0); }
        .mobile-overlay.open { display: block; }

        .mobile-drawer-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 14px;
            border-bottom: 1px solid var(--card-border);
        }

        .mobile-drawer-title {
            font-weight: 900;
            letter-spacing: .02em;
        }

        .mobile-close {
            width: 38px; height: 38px;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: white;
            cursor: pointer;
        }

        .mobile-nav {
            padding: 12px 12px 18px;
            overflow-y: auto;
        }

        .mobile-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 12px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 800;
            font-size: 14px;
        }
        .mobile-link:hover { background: #f5f7fc; }
        .mobile-link.active {
            background: rgba(232,93,47,0.10);
            color: var(--accent);
            border: 1px solid rgba(232,93,47,0.25);
        }

        .mobile-accordion {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 12px;
            border-radius: 12px;
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            font-weight: 900;
            font-size: 14px;
            color: var(--text-main);
        }
        .mobile-accordion:hover { background: #f5f7fc; }
        .mobile-accordion.active {
            background: rgba(232,93,47,0.08);
            border-color: rgba(232,93,47,0.2);
            color: var(--accent);
        }
        .mobile-accordion .chev { opacity: .65; }

        .mobile-acc-body {
            display: none;
            padding: 6px 6px 10px 6px;
            margin-bottom: 6px;
        }
        .mobile-acc-body.open { display: block; }

        .mobile-sub {
            display: block;
            padding: 10px 12px;
            margin: 6px 6px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
            color: var(--text-main);
            background: #fafbff;
            border: 1px solid rgba(228,232,240,0.9);
        }
        .mobile-sub:hover { background: #f5f7fc; }
        .mobile-sub.active {
            border-color: rgba(232,93,47,0.25);
            color: var(--accent);
            background: rgba(232,93,47,0.08);
        }

        .mobile-divider {
            height: 1px;
            background: var(--card-border);
            margin: 10px 6px;
        }

        /* ===== Responsive behavior ===== */
        @media (max-width: 820px) {
            .hamburger { display: inline-flex; }
            .topnav { display: none; } /* desktop nav disembunyikan di mobile */
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <img src="{{ asset('images/logo small.png') }}" alt="Logo">
            <div>
                <div class="brand-title">CPI - SH</div>
                <div class="brand-sub">Paperless System</div>
            </div>
        </div>

        <button type="button" class="hamburger" aria-label="Buka menu" onclick="toggleMobileNav()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>
            </svg>
        </button>

        <nav class="topnav" aria-label="Main navigation">
            <a href="{{ route('dashboard') }}" class="topnav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            {{-- Master Data --}}
            <div class="dropdown" id="ddMaster">
                <button type="button"
                        class="topnav-button {{ request()->routeIs('master.*') ? 'active' : '' }}"
                        onclick="toggleDropdown('ddMasterMenu')">
                    Master Data
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="opacity:.7">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div class="dropdown-menu" id="ddMasterMenu">
                    <a class="dropdown-item {{ request()->routeIs('master.users.*') ? 'active' : '' }}" href="{{ route('master.users.index') }}">Data User</a>
                    <a class="dropdown-item {{ request()->routeIs('master.expeditions.*') ? 'active' : '' }}" href="{{ route('master.expeditions.index') }}">Data Ekspedisi</a>
                    <a class="dropdown-item {{ request()->routeIs('master.farms.*') ? 'active' : '' }}" href="{{ route('master.farms.index') }}">Data Farm</a>
                </div>
            </div>

            {{-- Produksi --}}
            <div class="dropdown" id="ddProduksi">
                <button type="button"
                        class="topnav-button {{ request()->routeIs('monitor-controls.*') || request()->routeIs('hanging.*') || request()->routeIs('hanging-forms.*') ? 'active' : '' }}"
                        onclick="toggleDropdown('ddProduksiMenu')">
                    Produksi
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="opacity:.7">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div class="dropdown-menu" id="ddProduksiMenu">
                    <a class="dropdown-item {{ request()->routeIs('monitor-controls.*') ? 'active' : '' }}"
                    href="{{ route('monitor-controls.index') }}">
                        Kontrol Monitor
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('hanging.landing') || request()->routeIs('hanging.*') || request()->routeIs('hanging-forms.*') ? 'active' : '' }}"
                    href="{{ route('hanging.landing') }}">
                        Form Hanging Ayam
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('retur-mati.*') ? 'active' : '' }}"
                    href="{{ route('retur-mati.landing') }}">
                        Ayam Retur &amp; Mati
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('conditions.*') ? 'active' : '' }}"
                    href="{{ route('conditions.landing') }}">
                        QC Kondisi
                    </a>
                </div>
            </div>

            <a href="{{ route('monitor.show','SH01') }}" target="_blank" rel="noopener noreferrer" class="topnav-link">
                Monitor SH01 <span class="pill-live">LIVE</span>
            </a>
            <a href="{{ route('monitor.show','SH02') }}" target="_blank" rel="noopener noreferrer" class="topnav-link">
                Monitor SH02 <span class="pill-live">LIVE</span>
            </a>
        </nav>

        <div class="topbar-right">
            <div class="user-chip" title="{{ auth()->user()->name ?? 'Admin' }}">
                <div class="avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
        </div>
    </header>

    <div id="mobileNavOverlay" class="mobile-overlay" onclick="closeMobileNav()"></div>

    <div id="mobileNav" class="mobile-drawer" aria-hidden="true">
        <div class="mobile-drawer-head">
            <div class="mobile-drawer-title">Menu</div>
            <button type="button" class="mobile-close" aria-label="Tutup menu" onclick="closeMobileNav()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="mobile-nav">
            <a href="{{ route('dashboard') }}" class="mobile-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <button type="button"
                    class="mobile-accordion {{ request()->routeIs('master.*') ? 'active' : '' }}"
                    onclick="toggleAccordion('accMaster')">
                Master Data
                <span class="chev">▾</span>
            </button>
            <div id="accMaster" class="mobile-acc-body">
                <a class="mobile-sub {{ request()->routeIs('master.users.*') ? 'active' : '' }}" href="{{ route('master.users.index') }}">Data User</a>
                <a class="mobile-sub {{ request()->routeIs('master.expeditions.*') ? 'active' : '' }}" href="{{ route('master.expeditions.index') }}">Data Ekspedisi</a>
                <a class="mobile-sub {{ request()->routeIs('master.farms.*') ? 'active' : '' }}" href="{{ route('master.farms.index') }}">Data Farm</a>
            </div>

            <button type="button"
                    class="mobile-accordion {{ request()->routeIs('monitor-controls.*') || request()->routeIs('hanging.*') || request()->routeIs('hanging-forms.*') ? 'active' : '' }}"
                    onclick="toggleAccordion('accProduksi')">
                Produksi
                <span class="chev">▾</span>
            </button>
            <div id="accProduksi" class="mobile-acc-body">
                <a class="mobile-sub {{ request()->routeIs('monitor-controls.*') ? 'active' : '' }}" href="{{ route('monitor-controls.index') }}">Kontrol Monitor</a>
                <a class="mobile-sub {{ request()->routeIs('hanging.landing') || request()->routeIs('hanging.*') || request()->routeIs('hanging-forms.*') ? 'active' : '' }}" href="{{ route('hanging.landing') }}">Form Hanging Ayam</a>
            </div>

            <div class="mobile-divider"></div>

            <a href="{{ route('monitor.show','SH01') }}" target="_blank" rel="noopener noreferrer" class="mobile-link">
                Monitor SH01 <span class="pill-live">LIVE</span>
            </a>
            <a href="{{ route('monitor.show','SH02') }}" target="_blank" rel="noopener noreferrer" class="mobile-link">
                Monitor SH02 <span class="pill-live">LIVE</span>
            </a>
        </div>
    </div>

    <main class="content-area">
        @if(session('status'))
            <div class="alert alert-success">
                <svg class="alert-icon" width="18" height="18" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <div>
                    <div class="alert-title">Berhasil</div>
                    <div>{{ session('status') }}</div>
                </div>
                <button class="alert-close" onclick="this.closest('.alert').remove()" type="button">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <svg class="alert-icon" width="18" height="18" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div>
                    <div class="alert-title">Terjadi Kesalahan</div>
                    @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
                </div>
                <button class="alert-close" onclick="this.closest('.alert').remove()" type="button">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        function toggleDropdown(menuId) {
            const menu = document.getElementById(menuId);
            if (!menu) return;
            menu.classList.toggle('open');
        }

        function openDropdown(menuId) {
            document.getElementById(menuId)?.classList.add('open');
        }

        function closeDropdown(menuId) {
            document.getElementById(menuId)?.classList.remove('open');
        }

        // ===== Mobile Drawer =====
        function toggleMobileNav() {
            const drawer = document.getElementById('mobileNav');
            const overlay = document.getElementById('mobileNavOverlay');
            const isOpen = drawer?.classList.contains('open');

            if (isOpen) {
                closeMobileNav();
            } else {
                drawer?.classList.add('open');
                overlay?.classList.add('open');
                drawer?.setAttribute('aria-hidden', 'false');

                // auto-open accordion yang active (Master/Produksi)
                const activeAcc = document.querySelector('.mobile-accordion.active');
                if (activeAcc) {
                    const onclick = activeAcc.getAttribute('onclick') || '';
                    const match = onclick.match(/'([^']+)'/);
                    const targetId = match ? match[1] : null;
                    if (targetId) document.getElementById(targetId)?.classList.add('open');
                }
            }
        }

        function closeMobileNav() {
            document.getElementById('mobileNav')?.classList.remove('open');
            document.getElementById('mobileNavOverlay')?.classList.remove('open');
            document.getElementById('mobileNav')?.setAttribute('aria-hidden', 'true');
        }

        function toggleAccordion(id) {
            document.getElementById(id)?.classList.toggle('open');
        }

        // Klik di luar (desktop dropdown)
        document.addEventListener('click', function (e) {
            const ddMaster = document.getElementById('ddMaster');
            const masterMenu = document.getElementById('ddMasterMenu');
            if (ddMaster && masterMenu && !ddMaster.contains(e.target)) {
                masterMenu.classList.remove('open');
            }

            const ddProduksi = document.getElementById('ddProduksi');
            const produksiMenu = document.getElementById('ddProduksiMenu');
            if (ddProduksi && produksiMenu && !ddProduksi.contains(e.target)) {
                produksiMenu.classList.remove('open');
            }
        });

        // ESC -> tutup dropdown + drawer
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.getElementById('ddMasterMenu')?.classList.remove('open');
                document.getElementById('ddProduksiMenu')?.classList.remove('open');
                closeMobileNav();
            }
        });

        // resize ke desktop -> tutup drawer
        window.addEventListener('resize', () => {
            if (window.innerWidth > 820) closeMobileNav();
        });
    </script>
</body>
</html>