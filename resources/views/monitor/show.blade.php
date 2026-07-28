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

        /* HERO JETSON — kartu live count kamera AI, sejajar dengan .hero-grid */
        .jetson-hero-grid {
            background: linear-gradient(125deg, #eff6ff 0%, #e0f2fe 100%);
            border: 1px solid rgba(59,130,246,0.2);
        }
        .jetson-hero-grid .hero-number {
            background: linear-gradient(135deg, #3B82F6, #1D4ED8);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }
        .jetson-hero-grid .hero-label {
            color: var(--accent-blue);
        }

        /* CAROUSEL — 2 slide saja (1: farm + size, 2: ekspedisi) */
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

        /* ── Shift Done Infographic Panel ── */
        @keyframes infoPanelIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: scale(0.7); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes ringFill {
            from { stroke-dashoffset: 220; }
        }
        @keyframes pulseRing {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50%       { opacity: 1;   transform: scale(1.06); }
        }

        .shift-info-panel {
            display: none;
            flex-direction: column;
            gap: 0.9rem;
            animation: infoPanelIn 0.6s cubic-bezier(0.22,1,0.36,1) both;
        }
        .shift-info-panel.visible { display: flex; }

        /* --- Top row: 2 donut charts + meta --- */
        .sip-top {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.9rem;
        }

        /* Donut chart card */
        .donut-card {
            background: #fff;
            border-radius: 1.4rem;
            padding: 1rem 1.2rem;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .donut-wrap {
            position: relative;
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }
        .donut-wrap svg {
            transform: rotate(-90deg);
        }
        .donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .donut-pct {
            font-size: 1.05rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.5px;
        }
        .donut-unit {
            font-size: 0.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94A3B8;
        }
        .donut-info { flex: 1; min-width: 0; }
        .donut-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #94A3B8;
            margin-bottom: 0.25rem;
        }
        .donut-main {
            font-size: 1.5rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.5px;
        }
        .donut-sub {
            font-size: 0.65rem;
            font-weight: 600;
            color: #64748B;
            margin-top: 0.15rem;
        }
        .donut-detail {
            font-size: 0.62rem;
            font-weight: 700;
            margin-top: 0.35rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 30px;
            padding: 0.15rem 0.6rem;
            color: #475569;
        }

        /* --- Stat pills row --- */
        .sip-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.7rem;
        }

        .sip-pill {
            border-radius: 1.2rem;
            padding: 0.8rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
            animation: infoPanelIn 0.5s cubic-bezier(0.22,1,0.36,1) both;
        }
        .sip-pill::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .pill-ayam {
            background: linear-gradient(135deg, #FFF7ED, #FFEDD5);
            border-color: #FED7AA;
        }
        .pill-truk {
            background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
            border-color: #BFDBFE;
        }
        .pill-mati {
            background: linear-gradient(135deg, #FFF1F2, #FFE4E6);
            border-color: #FECDD3;
        }
        .pill-retur {
            background: linear-gradient(135deg, #FFFBEB, #FEF3C7);
            border-color: #FDE68A;
        }

        .sip-pill-icon {
            font-size: 1.3rem;
            margin-bottom: 0.1rem;
        }
        .sip-pill-label {
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #64748B;
        }
        .sip-pill-value {
            font-size: 1.4rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            line-height: 1;
            animation: countUp 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .sip-pill-sub {
            font-size: 0.6rem;
            font-weight: 600;
            color: #94A3B8;
        }
        .val-orange { color: #EA580C; }
        .val-blue   { color: #2563EB; }
        .val-red    { color: #DC2626; }
        .val-amber  { color: #D97706; }

        /* --- Meta row: lokasi, tanggal, jam --- */
        .sip-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.7rem;
        }
        .sip-meta-card {
            background: #fff;
            border-radius: 1.2rem;
            padding: 0.75rem 1rem;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }
        .sip-meta-icon {
            width: 36px; height: 36px; min-width: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .meta-icon-loc   { background: #FFF7ED; }
        .meta-icon-date  { background: #EFF6FF; }
        .meta-icon-time  { background: #F0FDF4; }
        .sip-meta-body {}
        .sip-meta-label {
            font-size: 0.55rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.12em;
            color: #94A3B8;
        }
        .sip-meta-value {
            font-size: 0.9rem; font-weight: 800;
            color: #0F172A; letter-spacing: -0.2px;
        }
        .sip-meta-value.time-range {
            font-size: 0.78rem;
        }

        /* responsive */
        @media (max-width: 700px) {
            .sip-top    { grid-template-columns: 1fr; }
            .sip-stats  { grid-template-columns: 1fr 1fr; }
            .sip-meta   { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 440px) {
            .sip-stats  { grid-template-columns: 1fr 1fr; }
            .sip-meta   { grid-template-columns: 1fr; }
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

        <!-- HERO JETSON : LIVE COUNT KAMERA AI (sejajar dengan hero-grid utama) -->
        <div class="hero-grid jetson-hero-grid" id="jetsonHeroGrid">
            <div class="hero-left">
                <div class="hero-label">🎥 LIVE COUNT JETSON KAMERA AI</div>
                <div class="hero-number" id="jetsonCurrentBatchCount">0</div>
                <div class="hero-meta">
                    <span class="live-badge" style="background:#EFF6FF; color:#1D4ED8;">⚡ Real-count by Jetson Edge Counter Camera</span>
                </div>
            </div>
            <div class="hero-stats-right">
                <div class="stat-card">
                    <div class="stat-label">📊 TOTAL HARI INI</div>
                    <div class="stat-value" id="jetsonTodayTotalCount">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">🔁 TOTAL BATCH HARI INI</div>
                    <div class="stat-value" id="jetsonTodayTotalBatches">0</div>
                </div>
            </div>
        </div>

        <!-- CAROUSEL hanya 2 slide (FARM+SIZE digabung, EKSPEDISI) -->
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
                <!-- SLIDE 1 : EKSPEDISI -->
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

        <!-- ── Shift Done Infographic ── -->
        <div class="shift-info-panel" id="shiftInfoPanel">

            <!-- Row 1: 2 Donut charts -->
            <div class="sip-top">
                <!-- Donut: Ayam -->
                <div class="donut-card">
                    <div class="donut-wrap">
                        <svg width="80" height="80" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="34" fill="none" stroke="#F1F5F9" stroke-width="10"/>
                            <circle id="donutAyamRing" cx="40" cy="40" r="34" fill="none"
                                stroke="url(#gradOrange)" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="213.6"
                                stroke-dashoffset="213.6"
                                style="transition: stroke-dashoffset 1.2s cubic-bezier(0.22,1,0.36,1)"/>
                            <defs>
                                <linearGradient id="gradOrange" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#F59E0B"/>
                                    <stop offset="100%" stop-color="#F97316"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="donut-center">
                            <span class="donut-pct val-orange" id="donutAyamPct">0%</span>
                            <span class="donut-unit">target</span>
                        </div>
                    </div>
                    <div class="donut-info">
                        <div class="donut-label">🐔 Total Ayam Diterima</div>
                        <div class="donut-main val-orange" id="sipTotalAyam">0</div>
                        <div class="donut-sub">ekor</div>
                        <div class="donut-detail">
                            <span>Planning:</span>
                            <strong id="sipPlanAyam">0</strong> ekor
                        </div>
                    </div>
                </div>

                <!-- Donut: Truk -->
                <div class="donut-card">
                    <div class="donut-wrap">
                        <svg width="80" height="80" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="34" fill="none" stroke="#F1F5F9" stroke-width="10"/>
                            <circle id="donutTrukRing" cx="40" cy="40" r="34" fill="none"
                                stroke="url(#gradBlue)" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="213.6"
                                stroke-dashoffset="213.6"
                                style="transition: stroke-dashoffset 1.2s cubic-bezier(0.22,1,0.36,1)"/>
                            <defs>
                                <linearGradient id="gradBlue" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#3B82F6"/>
                                    <stop offset="100%" stop-color="#06B6D4"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="donut-center">
                            <span class="donut-pct val-blue" id="donutTrukPct">0%</span>
                            <span class="donut-unit">target</span>
                        </div>
                    </div>
                    <div class="donut-info">
                        <div class="donut-label">🚛 Truk Terhitung</div>
                        <div class="donut-main val-blue" id="sipTotalTruk">0</div>
                        <div class="donut-sub">unit</div>
                        <div class="donut-detail">
                            <span>Planning:</span>
                            <strong id="sipPlanTruk">0</strong> unit
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: stat pills -->
            <div class="sip-stats">
                <div class="sip-pill pill-ayam" style="animation-delay:0.05s">
                    <div class="sip-pill-icon">🐔</div>
                    <div class="sip-pill-label">Ayam Diterima</div>
                    <div class="sip-pill-value val-orange" id="sipPillAyam">0</div>
                    <div class="sip-pill-sub">ekor</div>
                </div>
                <div class="sip-pill pill-truk" style="animation-delay:0.1s">
                    <div class="sip-pill-icon">🚚</div>
                    <div class="sip-pill-label">Jumlah Truk</div>
                    <div class="sip-pill-value val-blue" id="sipPillTruk">0</div>
                    <div class="sip-pill-sub">unit</div>
                </div>
                <div class="sip-pill pill-mati" style="animation-delay:0.15s">
                    <div class="sip-pill-icon">💀</div>
                    <div class="sip-pill-label">Ayam Mati</div>
                    <div class="sip-pill-value val-red" id="sipPillMati">0</div>
                    <div class="sip-pill-sub">ekor</div>
                </div>
                <div class="sip-pill pill-retur" style="animation-delay:0.2s">
                    <div class="sip-pill-icon">↩️</div>
                    <div class="sip-pill-label">Ayam Retur</div>
                    <div class="sip-pill-value val-amber" id="sipPillRetur">0</div>
                    <div class="sip-pill-sub" id="sipReturWeight">0 kg</div>
                </div>
            </div>

            <!-- Row 3: meta info -->
            <div class="sip-meta">
                <div class="sip-meta-card">
                    <div class="sip-meta-icon meta-icon-loc">📍</div>
                    <div class="sip-meta-body">
                        <div class="sip-meta-label">Lokasi</div>
                        <div class="sip-meta-value" id="sipLokasi">—</div>
                    </div>
                </div>
                <div class="sip-meta-card">
                    <div class="sip-meta-icon meta-icon-date">📅</div>
                    <div class="sip-meta-body">
                        <div class="sip-meta-label">Tanggal Operasional</div>
                        <div class="sip-meta-value" id="sipTanggal">—</div>
                    </div>
                </div>
                <div class="sip-meta-card">
                    <div class="sip-meta-icon meta-icon-time">⏱️</div>
                    <div class="sip-meta-body">
                        <div class="sip-meta-label">Durasi Operasional</div>
                        <div class="sip-meta-value time-range" id="sipJam">—</div>
                    </div>
                </div>
            </div>

        </div><!-- /shift-info-panel -->
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

    // carousel 2 slide (farm+size, expedisi)
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
    const jetsonCard = document.getElementById('jetsonCard');
    //const statJetsonAyamSpan = document.getElementById('statJetsonAyam');
    const jetsonCurrentBatchCountSpan = document.getElementById('jetsonCurrentBatchCount');
    const jetsonTodayTotalCountSpan = document.getElementById('jetsonTodayTotalCount');
    const jetsonTodayTotalBatchesSpan = document.getElementById('jetsonTodayTotalBatches');
    const statEkorSpan = document.getElementById('statTotalEkor');
    const statTruckNoSpan = document.getElementById('statTruckNo');
    const reportCodeSpan = document.getElementById('reportCodeDisplay');
    const carouselFarm = document.getElementById('carouselFarm');
    const carouselSize = document.getElementById('carouselSize');
    const carouselExpedisi = document.getElementById('carouselExpedisi');
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

    // Flag: animasi shift-selesai hanya sekali per sesi
    let shiftDoneAnimated = false;

    // ── Animasi step-increment untuk Jetson Current Batch Count ──
    // (meniru perilaku "naik 1 per 1" di web sumber Jetson)
    let jetsonDisplayedCount = null;
    let jetsonAnimTarget = null;
    let jetsonAnimTimer = null;

    function setJetsonCountDisplay(n) {
        jetsonDisplayedCount = n;
        jetsonCurrentBatchCountSpan.innerText = fmt(n);
    }

    function snapJetsonCount(n) {
        if (jetsonAnimTimer !== null) {
            clearInterval(jetsonAnimTimer);
            jetsonAnimTimer = null;
        }
        jetsonAnimTarget = n;
        setJetsonCountDisplay(n);
    }

    function stepJetsonCountToward(target) {
        jetsonAnimTarget = target;
        if (jetsonDisplayedCount === null) {
            setJetsonCountDisplay(target);
            return;
        }
        if (target <= jetsonDisplayedCount) {
            snapJetsonCount(target);
            return;
        }
        if (jetsonAnimTimer !== null) {
            clearInterval(jetsonAnimTimer);
            jetsonAnimTimer = null;
        }
        // makin besar selisihnya, makin cepat step-nya, supaya tidak lama nyusul
        const delta = Math.max(1, target - jetsonDisplayedCount);
        const stepMs = Math.max(40, Math.min(150, Math.floor(1800 / delta)));
        jetsonAnimTimer = setInterval(() => {
            if (jetsonDisplayedCount === null || jetsonDisplayedCount >= jetsonAnimTarget) {
                clearInterval(jetsonAnimTimer);
                jetsonAnimTimer = null;
                setJetsonCountDisplay(jetsonAnimTarget);
                return;
            }
            setJetsonCountDisplay(jetsonDisplayedCount + 1);
        }, stepMs);
    }

    // Update tampilan Jetson current-batch dengan animasi naik 1-per-1,
    // dan snap langsung kalau batch baru mulai (angka turun) atau data belum pernah tampil.
    function updateJetsonCurrentBatch(rawCount) {
        const target = Number(rawCount) || 0;
        const currentTarget = jetsonAnimTarget ?? jetsonDisplayedCount;

        if (jetsonDisplayedCount === null || target < currentTarget) {
            // Batch baru dimulai (reset) atau load pertama kali → langsung snap
            snapJetsonCount(target);
        } else if (target > currentTarget) {
            stepJetsonCountToward(target);
        }
        // kalau target sama dengan currentTarget, tidak perlu apa-apa (biarkan animasi jalan/selesai)
    }

    function setActiveUI(active) {
        if (active) {
            emptyOverlay.classList.add('hidden');
            heroGrid.style.display       = '';
            carouselModule.style.display = '';
            progressStrip.style.display  = '';
        } else {
            emptyOverlay.classList.remove('hidden');
            heroGrid.style.display       = 'none';
            carouselModule.style.display = 'none';
            progressStrip.style.display  = '';   // tetap tampil saat idle biasa
        }
    }

    // Saat shift selesai: sembunyikan semua section kecuali header + banner + infographic
    function setShiftDoneUI(isDone) {
        const sections = [heroGrid, carouselModule, progressStrip, emptyOverlay];
        if (isDone) {
            sections.forEach(el => { if (el) el.style.display = 'none'; });
            emptyOverlay.classList.add('hidden');
        } else {
            progressStrip.style.display = '';
        }
    }

    async function fetchData() {
        try {
            const res = await fetch(`{{ route('monitor.data', $location) }}`, { headers: { 'Accept': 'application/json' } });
            if(!res.ok) throw new Error();
            const data = await res.json();

            const jetsonCurrentBatchCountSpan = document.getElementById('jetsonCurrentBatchCount');
            const jetsonTodayTotalCountSpan = document.getElementById('jetsonTodayTotalCount');
            const jetsonTodayTotalBatchesSpan = document.getElementById('jetsonTodayTotalBatches');
            const jetsonIsFresh = data.jetson_is_fresh !== false;  // default true jika tidak ada

            jetsonTodayTotalCountSpan.innerText = fmt(data.jetson_today_total_count || 0);
            jetsonTodayTotalBatchesSpan.innerText = fmt(data.jetson_today_total_batches || 0);
            updateJetsonCurrentBatch(data.jetson_current_batch_count || 0);

            if (jetsonIsFresh) {
                // Data fresh dari API
                jetsonCurrentBatchCountSpan.classList.remove('data-cached');
                jetsonCurrentBatchCountSpan.classList.add('data-fresh');
                jetsonCurrentBatchCountSpan.title = 'Data live dari Jetson AI';
            } else {
                // Data dari fallback cache
                jetsonCurrentBatchCountSpan.classList.remove('data-fresh');
                jetsonCurrentBatchCountSpan.classList.add('data-cached');
                jetsonCurrentBatchCountSpan.title = 'Data ini adalah cache (Jetson API tidak accessible)';
            }

            const jetsonElements = [jetsonTodayTotalCountSpan, jetsonTodayTotalBatchesSpan];
            jetsonElements.forEach(el => {
                if (jetsonIsFresh) {
                    el.classList.remove('data-cached');
                    el.classList.add('data-fresh');
                } else {
                    el.classList.remove('data-fresh');
                    el.classList.add('data-cached');
                    el.style.opacity = '0.7';  // Slight fade untuk indicate cached
                }
            });
            
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

            // ── Deteksi status shift ──
            const shiftBanner    = document.getElementById('shiftCompleteBanner');
            const shiftTitleEl   = document.getElementById('shiftCompleteTitle');
            const shiftSubEl     = document.getElementById('shiftCompleteSub');
            const shiftInfoPanel = document.getElementById('shiftInfoPanel');
            const isShiftDone    = !data.active
                                   && data.no_process_reason === 'target_reached'
                                   && !!data.shift_done_message;

            // ── Shift selesai: sembunyikan semua section utama ──
            setShiftDoneUI(isShiftDone);

            // ── Shift Complete Banner ──
            if (shiftBanner) {
                if (isShiftDone) {
                    shiftTitleEl.textContent = data.shift_done_message;
                    shiftSubEl.textContent   = 'Semua proses hanging pada shift ini telah selesai.';
                    if (!shiftDoneAnimated) {
                        // Pertama kali: trigger animasi masuk
                        shiftBanner.classList.remove('visible');
                        void shiftBanner.offsetWidth;
                        shiftBanner.classList.add('visible');
                    } else {
                        // Fetch berikutnya: pastikan tampil, tanpa re-animasi
                        shiftBanner.classList.add('visible');
                    }
                } else {
                    shiftBanner.classList.remove('visible');
                    shiftDoneAnimated = false; // reset saat shift tidak lagi selesai
                }
            }

            // ── Shift Info Panel (infographic) ──
            if (shiftInfoPanel) {
                if (isShiftDone) {
                    if (!shiftDoneAnimated) {
                        shiftInfoPanel.classList.remove('visible');
                        void shiftInfoPanel.offsetWidth;
                        shiftInfoPanel.classList.add('visible');
                    } else {
                        shiftInfoPanel.classList.add('visible');
                    }

                    const _todayAyam   = data.today_total_ayam    || 0;
                    const _planAyam    = data.total_planning_ayam  || 0;
                    const _todayTruk   = data.today_truck_count    || 0;
                    const _planTruk    = data.total_planning_truk  || 0;
                    const _deadCount   = data.total_dead_count     || 0;
                    const _returCount  = data.total_retur_count    || 0;
                    const _returWeight = data.total_retur_weight   || 0;

                    const _ayamPct = _planAyam > 0 ? Math.min((_todayAyam / _planAyam) * 100, 100) : 0;
                    const _trukPct = _planTruk > 0 ? Math.min((_todayTruk / _planTruk) * 100, 100)  : 0;
                    const CIRC = 213.6;

                    document.getElementById('donutAyamRing').style.strokeDashoffset = CIRC - (CIRC * _ayamPct / 100);
                    document.getElementById('donutTrukRing').style.strokeDashoffset = CIRC - (CIRC * _trukPct / 100);
                    document.getElementById('donutAyamPct').textContent = Math.round(_ayamPct) + '%';
                    document.getElementById('donutTrukPct').textContent = Math.round(_trukPct) + '%';

                    document.getElementById('sipTotalAyam').textContent = fmt(_todayAyam);
                    document.getElementById('sipPlanAyam').textContent  = fmt(_planAyam);
                    document.getElementById('sipTotalTruk').textContent = fmt(_todayTruk);
                    document.getElementById('sipPlanTruk').textContent  = fmt(_planTruk);

                    document.getElementById('sipPillAyam').textContent  = fmt(_todayAyam);
                    document.getElementById('sipPillTruk').textContent  = fmt(_todayTruk);
                    document.getElementById('sipPillMati').textContent  = fmt(_deadCount);
                    document.getElementById('sipPillRetur').textContent = fmt(_returCount);
                    document.getElementById('sipReturWeight').textContent = _returWeight > 0
                        ? fmt(_returWeight) + ' kg' : '— kg';

                    document.getElementById('sipLokasi').textContent = data.location || '—';

                    if (data.process_date) {
                        const d = new Date(data.process_date + 'T00:00:00');
                        const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                        document.getElementById('sipTanggal').textContent =
                            `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
                    }

                    const startT  = data.shift_start_time  || '—';
                    const finishT = data.shift_finish_time || '—';
                    document.getElementById('sipJam').textContent = `${startT} → ${finishT}`;

                    // Tandai sudah dianimasikan — fetch berikutnya tidak re-trigger
                    shiftDoneAnimated = true;

                } else {
                    shiftInfoPanel.classList.remove('visible');
                }
            }

            // ── Tidak ada proses aktif (bukan shift selesai, hanya idle) ──
            if (!data.active) {
                if (!isShiftDone) {
                    setActiveUI(false);
                    // Update progress strip saat idle biasa
                    todayAyamSpan.innerText    = fmt(data.today_total_ayam || 0);
                    planningAyamSpan.innerText = fmt(data.total_planning_ayam || 0);
                    todayTruckSpan.innerText   = fmt(data.today_truck_count || 0);
                    planningTruckSpan.innerText = fmt(data.total_planning_truk || 0);
                    let _ap = (data.total_planning_ayam || 0) > 0
                        ? ((data.today_total_ayam || 0) / (data.total_planning_ayam || 1)) * 100 : 0;
                    let _tp = (data.total_planning_truk || 0) > 0
                        ? ((data.today_truck_count || 0) / (data.total_planning_truk || 1)) * 100 : 0;
                    _ap = Math.min(_ap, 100); _tp = Math.min(_tp, 100);
                    progressAyamFill.style.width = _ap + '%';
                    progressTruckFill.style.width = _tp + '%';
                    ayamPercentLabel.innerText  = Math.floor(_ap) + '%';
                    truckPercentLabel.innerText = Math.floor(_tp) + '%';
                }
                reportCodeSpan.innerText = '—';
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
            
            // Ekspedisi
            const expedition = data.expedition_name || '—';
            carouselExpedisi.innerText = expedition;
            
            // tooltips untuk info tambahan
            carouselFarm.setAttribute('title', farmName);
            carouselSize.setAttribute('title', sizeValue);
            carouselExpedisi.setAttribute('title', expedition);
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