<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — SlaughterHouse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
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
            --input-bg: #191d28;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
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
        }

        /* Grid texture */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .login-wrap {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            padding: 24px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #6b7280;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #111827;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        .brand-panel {
            flex: 1;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: slideLeft 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .brand-logo {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .brand-logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            filter: drop-shadow(0 0 8px rgba(232,93,47,0.3));
        }
        
        .brand-logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }
        
        .brand-logo-text span {
            color: var(--accent);
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-soft);
            border: 1px solid rgba(232,93,47,0.3);
            border-radius: 100px;
            padding: 5px 14px 5px 8px;
            margin-bottom: 28px;
            width: fit-content;
        }
        .brand-badge-dot {
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        .brand-badge span {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent);
        }

        .brand-title {
            font-family: 'Syne', sans-serif;
            font-size: 48px;
            font-weight: 800;
            color: var(--text);
            line-height: 1.05;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }
        .brand-title span {
            color: var(--accent);
        }

        .brand-desc {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
            max-width: 300px;
        }

        .stats-row {
            display: flex;
            gap: 20px;
            margin-top: 36px;
        }
        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .stat-num {
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
        }
        .stat-label {
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .stat-divider {
            width: 1px;
            height: 40px;
            background: var(--border);
            align-self: center;
        }

        /* Login Card */
        .login-card {
            width: 400px;
            flex-shrink: 0;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow:
                0 0 0 1px var(--border),
                0 30px 80px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.04);
            animation: slideRight 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.1s;
        }

        .card-header {
            margin-bottom: 32px;
        }
        .card-header h2 {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }
        .card-header p {
            color: var(--muted);
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }
        .input-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            width: 16px; height: 16px;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px 12px 42px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-wrap input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232,93,47,0.15);
        }
        .input-wrap input::placeholder { color: #3a4055; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--accent) 0%, #c94820 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 13px 20px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.03em;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            box-shadow: 0 4px 20px rgba(232,93,47,0.3);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(232,93,47,0.4);
        }
        .btn-login:active { transform: translateY(0); }

        .seed-hint {
            margin-top: 24px;
            padding: 12px 14px;
            background: rgba(255,255,255,0.025);
            border: 1px dashed var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .seed-hint svg { color: var(--accent2); flex-shrink: 0; }
        .seed-hint p {
            font-size: 11px;
            color: var(--muted);
        }
        .seed-hint code {
            font-family: 'Courier New', monospace;
            color: var(--accent2);
            background: rgba(245,166,35,0.1);
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        @media (max-width: 768px) {
            .brand-panel { display: none; }
            .login-card { width: 100%; max-width: 400px; }
            .login-wrap {
                padding: 16px;
                justify-content: center;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .login-wrap {
                max-width: 860px;
                padding: 24px;
            }
            .brand-panel {
                padding: 40px 24px;
            }
            .brand-title {
                font-size: 40px;
            }
            .login-card {
                width: 360px;
                padding: 32px 28px;
            }
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <!-- Brand Panel -->
    <div class="brand-panel">
        <div class="brand-logo">
            <img src="{{ asset('images/logo small.png') }}" alt="SlaughterHouse Logo">
        </div>
        <div class="brand-badge">
            <span class="brand-badge-dot"></span>
            <span>System Active</span>
        </div>
        <h1 class="brand-title">Slaughter<span>House</span></h1>
        <p class="brand-desc">Sistem operasional terpadu untuk kontrol dan monitoring seluruh aktivitas SlaughterHouse secara real-time.</p>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <div class="card-header">
            <h2>Selamat Datang</h2>
            <p>Masuk menggunakan akun Anda untuk melanjutkan.</p>
        </div>
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" autocomplete="username" />
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        oninput="toggleEyeIcon(this)"
                    />
                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePasswordBtn"
                        onclick="togglePasswordVisibility()"
                        aria-label="Lihat password"
                        style="display: none;"
                    >
                        {{-- Eye icon (password tersembunyi) --}}
                        <svg id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{-- Eye-off icon (password terlihat) --}}
                        <svg id="eyeOffIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Login
            </button>
        </form>
        <div class="seed-hint">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p>Silahkan hubungi supervisor Anda untuk registrasi akun.</p>
        </div>
    </div>
</div>
</body>

<script>
function toggleEyeIcon(input) {
    const btn = document.getElementById('togglePasswordBtn');
    btn.style.display = input.value.length > 0 ? 'flex' : 'none';
}

function togglePasswordVisibility() {
    const input = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');
    const btn = document.getElementById('togglePasswordBtn');

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    eyeIcon.style.display = isPassword ? 'none' : 'block';
    eyeOffIcon.style.display = isPassword ? 'block' : 'none';

    btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
}
</script>
</html>