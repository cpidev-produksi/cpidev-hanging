<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PT Charoen Pokphand Indonesia — Salatiga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:           #0a0c0f;
            --surface:      #111318;
            --surface2:     #161a23;
            --border:       #1e2330;
            --border-hi:    #2e3a52;
            --accent:       #e85d2f;
            --accent-soft:  rgba(232, 93, 47, 0.12);
            --accent2:      #f5a623;
            --accent2-soft: rgba(245,166,35,0.12);
            --text:         #f0f2f5;
            --muted:        #6b7591;
            --muted2:       #8a9ab8;
            --green:        #10b981;
            --blue:         #3b82f6;
            --purple:       #8b5cf6;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── Global grid texture ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.012) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.012) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        /* ─────────────────────────────────────────────
           NAVBAR
        ───────────────────────────────────────────── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            background: rgba(10,12,15,0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-brand img {
            height: 36px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 0 8px rgba(232,93,47,0.4));
        }

        .nav-brand-text {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.3px;
            line-height: 1.2;
        }
        .nav-brand-text span { color: var(--accent); }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nav-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 22px;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 18px rgba(232,93,47,0.3);
        }
        .btn-nav-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(232,93,47,0.45);
        }

        /* ─────────────────────────────────────────────
           HERO SECTION
        ───────────────────────────────────────────── */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 40px 80px;
            overflow: hidden;
        }

        /* Radial glow blobs */
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 60% at 15% 90%, rgba(232,93,47,0.1) 0%, transparent 55%),
                radial-gradient(ellipse 55% 50% at 88% 10%, rgba(245,166,35,0.07) 0%, transparent 50%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(59,130,246,0.04) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .hero-inner {
            position: relative;
            z-index: 1;
            max-width: 900px;
            width: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-soft);
            border: 1px solid rgba(232,93,47,0.3);
            border-radius: 100px;
            padding: 6px 16px 6px 10px;
            margin-bottom: 28px;
            opacity: 0;
            animation: fadeUp 0.8s 0.1s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        .hero-badge-dot {
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        .hero-badge span {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent);
        }

        .hero-logo {
            margin-bottom: 28px;
            opacity: 0;
            animation: fadeUp 0.8s 0.2s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        .hero-logo img {
            height: 80px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 0 24px rgba(232,93,47,0.35));
        }

        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(24px, 6vw, 68px);
            font-weight: 600;
            color: var(--text);
            line-height: 1.02;
            letter-spacing: -2px;
            margin-bottom: 12px;
            opacity: 0;
            animation: fadeUp 0.8s 0.3s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        .hero-title .accent { color: var(--accent); }
        .hero-title .accent2 { color: var(--accent2); }

        .hero-subtitle {
            font-size: 15px;
            font-weight: 500;
            color: var(--muted2);
            margin-bottom: 6px;
            letter-spacing: 0.01em;
            opacity: 0;
            animation: fadeUp 0.8s 0.38s cubic-bezier(0.22,1,0.36,1) forwards;
        }

        .hero-address {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: var(--muted);
            font-style: italic;
            margin-bottom: 40px;
            opacity: 0;
            animation: fadeUp 0.8s 0.45s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        .hero-address svg { flex-shrink: 0; color: var(--accent2); }

        .hero-about {
            max-width: 540px;
            font-size: 15px;
            line-height: 1.75;
            color: var(--muted2);
            margin-bottom: 48px;
            padding: 20px 28px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 16px;
            position: relative;
            opacity: 0;
            animation: fadeUp 0.8s 0.52s cubic-bezier(0.22,1,0.36,1) forwards;
        }
        .hero-about::before {
            content: '"';
            position: absolute;
            top: -12px; left: 20px;
            font-family: 'Syne', sans-serif;
            font-size: 60px;
            color: var(--accent);
            line-height: 1;
            opacity: 0.4;
        }

        .hero-cta-group {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
            opacity: 0;
            animation: fadeUp 0.8s 0.6s cubic-bezier(0.22,1,0.36,1) forwards;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 30px;
            border-radius: 14px;
            background: var(--accent);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 6px 24px rgba(232,93,47,0.35);
        }
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(232,93,47,0.5);
        }

        .btn-hero-ghost {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 26px;
            border-radius: 14px;
            background: transparent;
            border: 1px solid var(--border-hi);
            color: var(--muted2);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: border-color 0.2s, color 0.2s, transform 0.2s;
        }
        .btn-hero-ghost:hover {
            border-color: var(--accent);
            color: var(--text);
            transform: translateY(-2px);
        }

        /* ─────────────────────────────────────────────
           SECTION UTILITY
        ───────────────────────────────────────────── */
        .section {
            position: relative;
            z-index: 1;
            padding: 80px 40px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 12px;
        }
        .section-label::before {
            content: '';
            display: block;
            width: 20px;
            height: 2px;
            background: var(--accent);
            border-radius: 2px;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(24px, 4vw, 40px);
            font-weight: 800;
            color: var(--text);
            letter-spacing: -1px;
            line-height: 1.1;
            margin-bottom: 10px;
        }
        .section-title span { color: var(--accent); }

        .section-divider {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            margin: 0 auto;
        }

        /* ─────────────────────────────────────────────
           VIDEO SECTION
        ───────────────────────────────────────────── */
        .video-section {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 80px 40px;
        }

        .video-wrapper {
            max-width: 860px;
            margin: 0 auto;
        }

        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border-hi);
            box-shadow:
                0 0 0 1px rgba(232,93,47,0.08),
                0 24px 80px rgba(0,0,0,0.5);
            background: #000;
        }

        .video-container iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            border: none;
        }

        /* ─────────────────────────────────────────────
           INFOGRAFIS / STATS
        ───────────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 40px;
        }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }

        .stat-card-dark {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 26px 24px 22px;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s, border-color 0.25s, box-shadow 0.25s;
        }
        .stat-card-dark:hover {
            transform: translateY(-4px);
            border-color: var(--border-hi);
            box-shadow: 0 16px 50px rgba(0,0,0,0.35);
        }
        .stat-card-dark::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
        }
        .stat-card-dark.green::before  { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-card-dark.blue::before   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .stat-card-dark.orange::before { background: linear-gradient(90deg, #e85d2f, #f5a623); }
        .stat-card-dark.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .stat-card-dark.amber::before  { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .stat-card-dark.rose::before   { background: linear-gradient(90deg, #f43f5e, #fb7185); }

        .stat-card-top-d {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .stat-label-d {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .stat-value-d {
            font-family: 'Syne', sans-serif;
            font-size: 40px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -1.5px;
        }
        .stat-value-d.green  { color: #34d399; }
        .stat-value-d.blue   { color: #60a5fa; }
        .stat-value-d.orange { color: #f87040; }
        .stat-value-d.purple { color: #a78bfa; }
        .stat-value-d.amber  { color: #fbbf24; }
        .stat-value-d.rose   { color: #fb7185; }

        .stat-icon-d {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-icon-d.green  { background: rgba(16,185,129,0.1);  color: #10b981; }
        .stat-icon-d.blue   { background: rgba(59,130,246,0.1);  color: #3b82f6; }
        .stat-icon-d.orange { background: rgba(232,93,47,0.1);   color: #e85d2f; }
        .stat-icon-d.purple { background: rgba(139,92,246,0.1);  color: #8b5cf6; }
        .stat-icon-d.amber  { background: rgba(245,158,11,0.1);  color: #f59e0b; }
        .stat-icon-d.rose   { background: rgba(244,63,94,0.1);   color: #f43f5e; }

        .stat-footer-d {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
        }

        /* ─────────────────────────────────────────────
           LIVE MONITOR SECTION
        ───────────────────────────────────────────── */
        .monitor-section-lp {
            background: var(--surface);
            border-top: 1px solid var(--border);
        }

        .monitor-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }
        @media (max-width: 640px) { .monitor-cards { grid-template-columns: 1fr; } }

        .monitor-card {
            position: relative;
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 32px 28px;
            overflow: hidden;
            transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .monitor-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-hi);
        }

        .monitor-card.sh01 {
            background: linear-gradient(145deg, rgba(232,93,47,0.06) 0%, var(--surface2) 100%);
        }
        .monitor-card.sh01:hover {
            box-shadow: 0 20px 60px rgba(232,93,47,0.15);
            border-color: rgba(232,93,47,0.4);
        }
        .monitor-card.sh02 {
            background: linear-gradient(145deg, rgba(245,166,35,0.06) 0%, var(--surface2) 100%);
        }
        .monitor-card.sh02:hover {
            box-shadow: 0 20px 60px rgba(245,166,35,0.15);
            border-color: rgba(245,166,35,0.4);
        }

        .monitor-card-glow {
            position: absolute;
            top: -40px; right: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            pointer-events: none;
        }
        .sh01 .monitor-card-glow { background: var(--accent); }
        .sh02 .monitor-card-glow { background: var(--accent2); }

        .monitor-card-head {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .monitor-card-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sh01 .monitor-card-icon { background: rgba(232,93,47,0.12); color: var(--accent); }
        .sh02 .monitor-card-icon { background: rgba(245,166,35,0.12); color: var(--accent2); }

        .monitor-card-name {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }

        .monitor-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .sh01 .monitor-live-badge { background: rgba(232,93,47,0.12); color: var(--accent); border: 1px solid rgba(232,93,47,0.25); }
        .sh02 .monitor-live-badge { background: rgba(245,166,35,0.12); color: var(--accent2); border: 1px solid rgba(245,166,35,0.25); }

        .monitor-live-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        .sh01 .monitor-live-dot { background: var(--accent); }
        .sh02 .monitor-live-dot { background: var(--accent2); }

        .monitor-card-desc {
            font-size: 13px;
            line-height: 1.65;
            color: var(--muted2);
        }

        .btn-monitor-lp {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.02em;
            transition: transform 0.2s, box-shadow 0.2s;
            width: fit-content;
            margin-top: 4px;
        }
        .sh01 .btn-monitor-lp {
            background: linear-gradient(135deg, #e85d2f, #c94820);
            color: #fff;
            box-shadow: 0 4px 18px rgba(232,93,47,0.3);
        }
        .sh01 .btn-monitor-lp:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(232,93,47,0.5);
        }
        .sh02 .btn-monitor-lp {
            background: linear-gradient(135deg, #f5a623, #d4880d);
            color: #fff;
            box-shadow: 0 4px 18px rgba(245,166,35,0.3);
        }
        .sh02 .btn-monitor-lp:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(245,166,35,0.4);
        }

        /* ─────────────────────────────────────────────
           FOOTER CTA
        ───────────────────────────────────────────── */
        .footer-cta {
            position: relative;
            overflow: hidden;
            border-top: 1px solid var(--border);
            padding: 100px 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }
        .footer-cta::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 70% at 50% 100%, rgba(232,93,47,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .footer-cta-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(28px, 5vw, 52px);
            font-weight: 800;
            color: var(--text);
            letter-spacing: -1.5px;
            line-height: 1.05;
            position: relative;
            z-index: 1;
        }
        .footer-cta-title span { color: var(--accent); }

        .footer-cta-sub {
            font-size: 15px;
            color: var(--muted2);
            max-width: 400px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .btn-cta-login {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 40px;
            border-radius: 14px;
            background: var(--accent);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 6px 28px rgba(232,93,47,0.4);
            position: relative;
            z-index: 1;
        }
        .btn-cta-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 40px rgba(232,93,47,0.55);
        }

        /* ─────────────────────────────────────────────
           FOOTER
        ───────────────────────────────────────────── */
        .site-footer {
            border-top: 1px solid var(--border);
            padding: 28px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .site-footer p {
            font-size: 12px;
            color: var(--muted);
        }

        /* ─────────────────────────────────────────────
           SCROLL REVEAL
        ───────────────────────────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ─────────────────────────────────────────────
           KEYFRAMES
        ───────────────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }

        /* ─────────────────────────────────────────────
           RESPONSIVE
        ───────────────────────────────────────────── */
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .hero   { padding: 100px 20px 60px; }
            .section { padding: 60px 20px; }
            .video-section { padding: 60px 20px; }
            .monitor-section-lp .section { padding: 60px 20px; }
            .footer-cta { padding: 80px 20px; }
            .site-footer { padding: 24px 20px; flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<!-- ═══════════════════ NAVBAR ═══════════════════ -->
<nav class="navbar">
    <a href="#" class="nav-brand">
        <img src="{{ asset('images/logo small.png') }}" alt="CP Food Logo">
        <div class="nav-brand-text">CP <span>Food Division</span></div>
    </a>
    <div class="nav-actions">
        <a href="{{ route('login') }}" class="btn-nav-login">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Login
        </a>
    </div>
</nav>

<!-- ═══════════════════ HERO ═══════════════════ -->
<section class="hero">
    <div class="hero-inner">

        <div class="hero-logo">
            <img src="{{ asset('images/logo small.png') }}" alt="PT Charoen Pokphand Indonesia Logo">
        </div>

        {{-- <h1 class="hero-title">
            PT Charoen Pokphand <span class="accent">Indonesia</span><br>
        <span class="accent2">Food Division</span> --}}
        <h1 class="hero-title">
        <span class="accent">PT. Charoen Pokphand </span><br>
        <span class="accent2">Food Division</span>
        </h1>

        <p class="hero-subtitle">Salatiga - Jawa Tengah - Indonesia</p>

        <div class="hero-address">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Jl. Pattimura km 1, Desa Canden, Kel. Kutowinangun Kidul, Kec. Tingkir, Salatiga, Indonesia
        </div>

        <div class="hero-about">
            Mengembangkan Bisnis di Bidang Industri Pengolahan Makanan Berbahan Baku Ayam — memproduksi merek populer seperti Fiesta, Golden Fiesta, Champ, dan Okey dengan standar keamanan pangan tinggi (HACCP/FSSC 22000) dan halal.
        </div>

        <div class="hero-cta-group">
            <a href="#video" class="btn-hero-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polygon points="5,3 19,12 5,21" fill="currentColor" stroke="none"/>
                </svg>
                Tonton Video Promosi
            </a>
            <a href="#monitor" class="btn-hero-ghost">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 10l4.553-2.069A1 1 0 0121 8.869v6.262a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                </svg>
                Live Monitor
            </a>
        </div>

    </div>
</section>

<div class="section-divider"></div>

<!-- ═══════════════════ VIDEO ═══════════════════ -->
<section class="video-section" id="video">
    <div class="video-wrapper">
        <div class="reveal">
            <div class="section-label">Promosi</div>
            <h2 class="section-title">Mengenal Lebih Dekat <span>CP Food</span></h2>
            <p style="color:var(--muted2);font-size:14px;margin:8px 0 32px;line-height:1.65">
                Saksikan bagaimana kami beroperasi — dari proses penerimaan ayam hingga produk siap saji yang sampai ke tangan konsumen.
            </p>
        </div>
        <div class="reveal reveal-delay-1">
            <div class="video-container">
                <iframe
                    src="https://www.youtube.com/embed/PIMNc-XatDE?rel=0&modestbranding=1"
                    title="PT Charoen Pokphand Indonesia di Salatiga"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ═══════════════════ INFOGRAFIS ═══════════════════ -->
<section class="section" id="infografis">
    <div class="reveal">
        <div class="section-label">Infografis</div>
        <h2 class="section-title">Data <span>Operasional</span></h2>
        <p style="color:var(--muted2);font-size:14px;margin-top:8px;line-height:1.65;max-width:480px">
            Ringkasan data dan aktivitas operasional harian di fasilitas produksi CP Food Salatiga.
        </p>
    </div>

    <div class="stats-grid">
        <!-- Farm -->
        <div class="stat-card-dark blue reveal reveal-delay-1">
            <div class="stat-card-top-d">
                <div>
                    <div class="stat-label-d">Farm Terdaftar</div>
                    <div class="stat-value-d blue">{{ $master['farms'] ?? '—' }}</div>
                </div>
                <div class="stat-icon-d blue">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer-d">Total peternak mitra aktif</div>
        </div>

        <!-- Ekspedisi -->
        <div class="stat-card-dark green reveal reveal-delay-2">
            <div class="stat-card-top-d">
                <div>
                    <div class="stat-label-d">Ekspedisi Terkait</div>
                    <div class="stat-value-d green">{{ $master['expeditions'] ?? '—' }}</div>
                </div>
                <div class="stat-icon-d green">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 17l4 4 4-4m-4-5v9M3 5h18M3 5a2 2 0 002 2h14a2 2 0 002-2M3 5a2 2 0 012-2h14a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer-d">Mitra ekspedisi terdaftar</div>
        </div>

        <!-- Kendaraan -->
        @if(!is_null($master['plates'] ?? null))
        <div class="stat-card-dark orange reveal reveal-delay-3">
            <div class="stat-card-top-d">
                <div>
                    <div class="stat-label-d">Armada Truk</div>
                    <div class="stat-value-d orange">{{ $master['plates'] }}</div>
                </div>
                <div class="stat-icon-d orange">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="10" rx="2" ry="2" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11h.01M17 11h.01"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer-d">Plat nomor kendaraan tercatat</div>
        </div>
        @endif

        <!-- Truk Total Hari Ini -->
        <div class="stat-card-dark amber reveal reveal-delay-1">
            <div class="stat-card-top-d">
                <div>
                    <div class="stat-label-d">Truk Hari Ini</div>
                    <div class="stat-value-d amber">{{ $grand['truk_total'] ?? '—' }}</div>
                </div>
                <div class="stat-icon-d amber">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1 1h1m8-1h3l3-3V9l-3-1h-3v8z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer-d">Total truk masuk hari ini</div>
        </div>

        <!-- Ayam Diterima -->
        <div class="stat-card-dark purple reveal reveal-delay-2">
            <div class="stat-card-top-d">
                <div>
                    <div class="stat-label-d">Ayam Diproses</div>
                    <div class="stat-value-d purple" style="font-size:30px;letter-spacing:-0.5px">
                        {{ number_format($grand['ayam_received'] ?? 0) }}
                    </div>
                </div>
                <div class="stat-icon-d purple">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer-d">Ekor ayam diproses hari ini</div>
        </div>

        <!-- Truk Running -->
        <div class="stat-card-dark rose reveal reveal-delay-3">
            <div class="stat-card-top-d">
                <div>
                    <div class="stat-label-d">Sedang Berjalan</div>
                    <div class="stat-value-d rose">{{ $grand['truk_running'] ?? '—' }}</div>
                </div>
                <div class="stat-icon-d rose">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-footer-d">Proses penghitungan aktif</div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ═══════════════════ LIVE MONITOR ═══════════════════ -->
<div class="monitor-section-lp" id="monitor">
    <div class="section">
        <div class="reveal">
            <div class="section-label">Live Monitor</div>
            <h2 class="section-title">Pantau Aktivitas <span>Real-Count</span></h2>
            <p style="color:var(--muted2);font-size:14px;margin-top:8px;line-height:1.65;max-width:520px">
                Akses tampilan monitoring langsung dari setiap unit SlaughterHouse. Tidak perlu login — tersedia untuk akses publik terbatas.
            </p>
        </div>

        <div class="monitor-cards">

            <!-- SH01 -->
            <div class="monitor-card sh01 reveal reveal-delay-1">
                <div class="monitor-card-glow"></div>
                <div class="monitor-card-head">
                    <div class="monitor-card-icon">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 10l4.553-2.069A1 1 0 0121 8.869v6.262a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="monitor-card-name">SH01</div>
                        <div class="monitor-live-badge">
                            <span class="monitor-live-dot"></span>
                            Live
                        </div>
                    </div>
                </div>
                <p class="monitor-card-desc">
                    Unit SlaughterHouse 01 — fasilitas utama pemrosesan ayam. Monitor aktivitas penghitungan truk, antrian, dan data ayam masuk secara langsung.
                </p>
                <a href="{{ route('monitor.show', 'SH01') }}" target="_blank" rel="noopener" class="btn-monitor-lp">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Buka Live Monitor SH01
                </a>
            </div>

            <!-- SH02 -->
            <div class="monitor-card sh02 reveal reveal-delay-2">
                <div class="monitor-card-glow"></div>
                <div class="monitor-card-head">
                    <div class="monitor-card-icon">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 10l4.553-2.069A1 1 0 0121 8.869v6.262a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="monitor-card-name">SH02</div>
                        <div class="monitor-live-badge">
                            <span class="monitor-live-dot"></span>
                            Live
                        </div>
                    </div>
                </div>
                <p class="monitor-card-desc">
                    Unit SlaughterHouse 02 — fasilitas pendukung dengan kapasitas pemrosesan tambahan. Pantau status operasional dan laporan real-time unit ini.
                </p>
                <a href="{{ route('monitor.show', 'SH02') }}" target="_blank" rel="noopener" class="btn-monitor-lp">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Buka Live Monitor SH02
                </a>
            </div>

        </div>
    </div>
</div>

<!-- ═══════════════════ FOOTER CTA ═══════════════════ -->
<section class="footer-cta">
    <h2 class="footer-cta-title reveal">Siap Masuk ke<br><span>Dashboard?</span></h2>
    <p class="footer-cta-sub reveal reveal-delay-1">
        Login untuk mengakses sistem operasional terpadu, laporan harian, dan kontrol penuh atas aktivitas SlaughterHouse.
    </p>
    <a href="{{ route('login') }}" class="btn-cta-login reveal reveal-delay-2">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Login ke Dashboard
    </a>
</section>

<!-- ═══════════════════ SITE FOOTER ═══════════════════ -->
<footer class="site-footer">
    <p>© {{ date('Y') }} PT Charoen Pokphand Indonesia (CP Food) Salatiga. All rights reserved.</p>
    <p style="display:flex;align-items:center;gap:6px;">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--accent)">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        </svg>
        Jl. Pattimura km 1, Salatiga, Central Java - Web Development
    </p>
</footer>

<script>
    // Scroll reveal
    const revealEls = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    revealEls.forEach(el => observer.observe(el));
</script>
</body>
</html>