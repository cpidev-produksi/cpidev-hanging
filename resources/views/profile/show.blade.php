@extends('layouts.app')

@section('content')

<style>
.profile-wrap {
    font-family: 'Plus Jakarta Sans', var(--font-sans, sans-serif);
    max-width: 780px;
    margin: 0 auto;
    padding: 1.5rem 0;
}
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 1rem;
}
.breadcrumb a { color: var(--text-muted); text-decoration: none; }
.breadcrumb a:hover { color: var(--text-main); }
.breadcrumb-sep { opacity: 0.4; font-size: 10px; }
.page-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 2px;
    letter-spacing: -0.4px;
}
.page-sub {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0 0 1.5rem;
}
.avatar-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 1.25rem 1.5rem;
    background: white;
    border: 0.5px solid var(--card-border);
    border-radius: 14px;
    margin-bottom: 12px;
}
.avatar-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #E8F4FE;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    color: #185FA5;
    flex-shrink: 0;
    border: 2px solid #B5D4F4;
}
.avatar-info p { margin: 0; }
.avatar-name { font-size: 15px; font-weight: 600; color: var(--text-main); }
.avatar-role {
    display: inline-block;
    margin-top: 4px;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 20px;
    background: #E1F5EE;
    color: #0F6E56;
}
.profile-card {
    background: white;
    border: 0.5px solid var(--card-border);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 12px;
}
.profile-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 20px;
    border-bottom: 0.5px solid var(--card-border);
    background: #fafafa;
}
.profile-card-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-card-icon.blue { background: #E6F1FB; }
.profile-card-icon.amber { background: #FAEEDA; }
.profile-card-header-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
}
.profile-card-body { padding: 20px; }
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.form-group input {
    padding: 10px 12px;
    border-radius: 10px;
    border: 0.5px solid var(--card-border);
    background: white;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-main);
    font-family: inherit;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
}
.form-group input:focus {
    border-color: #85B7EB;
    box-shadow: 0 0 0 3px rgba(55, 138, 221, 0.08);
}
.form-group input[readonly] {
    background: #f7f7f7;
    color: var(--text-muted);
    cursor: default;
}
.pw-group { position: relative; }
.pw-group input { padding-right: 38px; width: 100%; box-sizing: border-box; }
.pw-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    padding: 2px;
    display: flex;
}
.pw-toggle:hover { color: var(--text-main); }
.pw-strength {
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.strength-bars { display: flex; gap: 3px; }
.strength-bar {
    width: 24px;
    height: 3px;
    border-radius: 2px;
    background: var(--card-border);
    transition: background 0.2s;
}
.strength-label { font-size: 11px; color: var(--text-muted); }
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 10px;
    border: none;
    background: #378ADD;
    color: white;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s, transform 0.1s;
}
.btn-save:hover { background: #185FA5; }
.btn-save:active { transform: scale(0.98); }
.btn-pw {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 10px;
    border: none;
    background: #EF9F27;
    color: white;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s, transform 0.1s;
}
.btn-pw:hover { background: #BA7517; }
.btn-pw:active { transform: scale(0.98); }
@media (max-width: 580px) {
    .form-grid { grid-template-columns: 1fr; }
    .avatar-circle { width: 44px; height: 44px; font-size: 16px; }
}
</style>

<div class="profile-wrap">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep">›</span>
        <span>Profil Saya</span>
    </div>

    <p class="page-title">Profil Saya</p>
    <p class="page-sub">Lihat dan perbarui informasi akun Anda</p>

    {{-- Avatar Row --}}
    <div class="avatar-row">
        <div class="avatar-circle">
            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' ') ?: '_', 1, 1)) }}
        </div>
        <div class="avatar-info">
            <p class="avatar-name">{{ $user->name }}</p>
            <span class="avatar-role">{{ $user->role?->name ?? $user->role?->slug ?? '-' }}</span>
        </div>
    </div>

    {{-- Success / Error Alerts --}}
    @if (session('status') === 'profile-updated')
        <div style="padding:10px 16px;border-radius:10px;background:#E1F5EE;color:#0F6E56;font-size:13px;font-weight:600;margin-bottom:12px;">
            ✓ Profil berhasil diperbarui.
        </div>
    @endif
    @if (session('status') === 'password-updated')
        <div style="padding:10px 16px;border-radius:10px;background:#E1F5EE;color:#0F6E56;font-size:13px;font-weight:600;margin-bottom:12px;">
            ✓ Password berhasil diganti.
        </div>
    @endif
    @if ($errors->any())
        <div style="padding:10px 16px;border-radius:10px;background:#FCEBEB;color:#A32D2D;font-size:13px;font-weight:600;margin-bottom:12px;">
            @foreach ($errors->all() as $error)
                <div>✕ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Informasi Akun --}}
    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-card-icon blue">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#378ADD" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <span class="profile-card-header-title">Informasi Akun</span>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <input type="text" value="{{ $user->role?->name ?? $user->role?->slug ?? '-' }}" readonly>
                    </div>
                </div>
                <button type="submit" class="btn-save">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Simpan Profil
                </button>
            </form>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-card-icon amber">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#BA7517" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <span class="profile-card-header-title">Ganti Password</span>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PATCH')
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Password Lama</label>
                        <div class="pw-group">
                            <input type="password" name="current_password" id="pw1" required placeholder="••••••••">
                            <button type="button" class="pw-toggle" onclick="togglePw('pw1')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="pw-group">
                            <input type="password" name="new_password" id="pw2" required placeholder="••••••••" oninput="checkStrength(this)">
                            <button type="button" class="pw-toggle" onclick="togglePw('pw2')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <div class="pw-strength" id="strength-row" style="opacity:0;">
                            <div class="strength-bars">
                                <div class="strength-bar" id="s1"></div>
                                <div class="strength-bar" id="s2"></div>
                                <div class="strength-bar" id="s3"></div>
                                <div class="strength-bar" id="s4"></div>
                            </div>
                            <span class="strength-label" id="strength-text"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="pw-group">
                            <input type="password" name="new_password_confirmation" id="pw3" required placeholder="••••••••">
                            <button type="button" class="pw-toggle" onclick="togglePw('pw3')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-pw">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Ganti Password
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function togglePw(id) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
}

function checkStrength(inp) {
    const v = inp.value;
    const row = document.getElementById('strength-row');
    if (!v) { row.style.opacity = 0; return; }
    row.style.opacity = 1;
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const colors = ['#E24B4A', '#EF9F27', '#63B923', '#1D9E75'];
    const labels = ['Lemah', 'Cukup', 'Kuat', 'Sangat kuat'];
    for (let i = 1; i <= 4; i++) {
        document.getElementById('s' + i).style.background =
            i <= score ? colors[score - 1] : '#e2e2e2';
    }
    const txt = document.getElementById('strength-text');
    txt.textContent = labels[score - 1] || '';
    txt.style.color = colors[score - 1] || '';
}
</script>

@endsection