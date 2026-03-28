@extends('layouts.app')

@section('content')
<div class="form-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('master.users.index') }}" class="breadcrumb-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Data User
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        <span>Edit User</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="user-header-card">
            <div class="user-avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
                <h1 class="page-title">{{ $user->name }}</h1>
                <p class="page-subtitle">
                    <code class="username-chip">{{ $user->username }}</code>
                    · {{ $user->email }}
                </p>
            </div>
        </div>
        <div class="edit-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Mode Edit
        </div>
    </div>

    {{-- Form Card --}}
    <form method="POST" action="{{ route('master.users.update', $user) }}" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Perbarui Informasi Akun
        </div>

        <div class="form-grid">
            {{-- Nama --}}
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                <div class="input-wrapper @error('name') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                           class="form-input" placeholder="Nama lengkap">
                </div>
                @error('name')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email <span class="required">*</span></label>
                <div class="input-wrapper @error('email') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                           class="form-input" placeholder="Email">
                </div>
                @error('email')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Username --}}
            <div class="form-group">
                <label class="form-label" for="username">Username <span class="required">*</span></label>
                <div class="input-wrapper @error('username') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}"
                           class="form-input mono" placeholder="Username">
                </div>
                @error('username')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">
                    Password
                    <span class="optional-tag">Opsional</span>
                </label>
                <div class="input-wrapper @error('password') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <input id="password" name="password" type="password"
                           class="form-input" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="field-hint">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Biarkan kosong jika tidak ingin mengubah password
                </div>
                @error('password')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Role --}}
            <div class="form-group full-width">
                <label class="form-label" for="role_id">Role <span class="required">*</span></label>
                <div class="input-wrapper @error('role_id') has-error @enderror select-wrapper">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <select id="role_id" name="role_id" class="form-input form-select">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" @selected(old('role_id', $user->role_id) == $r->id)>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                @error('role_id')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="form-footer">
            <a href="{{ route('master.users.index') }}" class="btn-cancel">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Batal
            </a>
            <button type="submit" class="btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<style>
:root {
    --c-bg: #F5F6FA;
    --c-card: #FFFFFF;
    --c-border: #E8EAF0;
    --c-text: #1A1D2E;
    --c-muted: #6B7280;
    --c-accent: #4F67FF;
    --c-accent-hover: #3D53E8;
    --c-accent-light: #EEF0FF;
    --c-danger: #F03E3E;
    --c-danger-light: #FFF5F5;
    --c-warning: #F59F00;
    --c-warning-light: #FFF9E8;
    --radius: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
}

.form-page { max-width: 720px; margin: 0 auto; padding: 28px 20px; }

/* Breadcrumb */
.breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: .78rem; color: var(--c-muted);
    margin-bottom: 20px;
}
.breadcrumb-link {
    display: inline-flex; align-items: center; gap: 5px;
    color: var(--c-accent); text-decoration: none; font-weight: 500;
    transition: opacity .15s;
}
.breadcrumb-link:hover { opacity: .75; }

/* Header */
.page-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; margin-bottom: 24px;
    flex-wrap: wrap;
}
.user-header-card { display: flex; align-items: center; gap: 14px; }

.user-avatar-lg {
    width: 52px; height: 52px; border-radius: 50%;
    background: var(--c-accent-light);
    color: var(--c-accent);
    font-size: 1.1rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    border: 2px solid rgba(79,103,255,.2);
}

.page-title { font-size: 1.2rem; font-weight: 700; color: var(--c-text); margin: 0 0 4px; }
.page-subtitle {
    font-size: .8rem; color: var(--c-muted); margin: 0;
    display: flex; align-items: center; gap: 6px;
}

.username-chip {
    background: #F3F4F8; color: #4B5563;
    padding: 2px 8px; border-radius: 5px;
    font-size: .75rem;
    font-family: 'Fira Code', 'Courier New', monospace;
}

.edit-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px;
    background: var(--c-warning-light);
    color: var(--c-warning);
    border-radius: 20px;
    font-size: .73rem; font-weight: 600;
    border: 1px solid rgba(245,159,0,.2);
}

/* Form Card */
.form-card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 28px;
}

.form-section-title {
    display: flex; align-items: center; gap: 7px;
    font-size: .72rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--c-accent);
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--c-border);
}

/* Form Grid */
.form-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 18px; margin-bottom: 28px;
}
.full-width { grid-column: 1 / -1; }

@media (max-width: 600px) {
    .form-grid { grid-template-columns: 1fr; }
    .full-width { grid-column: auto; }
}

/* Labels */
.form-label {
    display: flex; align-items: center; gap: 6px;
    font-size: .8rem; font-weight: 600;
    color: var(--c-text); margin-bottom: 6px;
}
.required { color: var(--c-danger); }
.optional-tag {
    font-size: .68rem; font-weight: 500;
    background: #F0F1F4; color: #9CA3AF;
    padding: 2px 7px; border-radius: 4px;
    margin-left: 2px;
}

/* Inputs */
.input-wrapper {
    position: relative; display: flex; align-items: center;
    border: 1.5px solid var(--c-border);
    border-radius: 9px; background: #FAFBFD;
    transition: border-color .18s, box-shadow .18s;
    overflow: hidden;
}
.input-wrapper:focus-within {
    border-color: var(--c-accent);
    box-shadow: 0 0 0 3px rgba(79,103,255,.12);
    background: #fff;
}
.input-wrapper.has-error { border-color: var(--c-danger); background: var(--c-danger-light); }
.input-wrapper.has-error:focus-within { box-shadow: 0 0 0 3px rgba(240,62,62,.1); }

.input-icon {
    display: flex; align-items: center; justify-content: center;
    width: 40px; min-width: 40px; color: var(--c-muted); pointer-events: none;
}

.form-input {
    flex: 1; border: none; outline: none; background: transparent;
    padding: 10px 12px 10px 0;
    font-size: .875rem; color: var(--c-text); width: 100%;
}
.form-input::placeholder { color: #B0B7C3; }
.form-input.mono { font-family: 'Fira Code', 'Courier New', monospace; }

.select-wrapper { position: relative; }
.form-select { appearance: none; padding-right: 36px; cursor: pointer; }
.select-arrow {
    position: absolute; right: 12px; color: var(--c-muted);
    pointer-events: none; display: flex; align-items: center;
}

/* Hint & Error */
.field-hint {
    display: flex; align-items: center; gap: 5px;
    color: #9CA3AF; font-size: .74rem; margin-top: 5px;
}

.form-error {
    display: flex; align-items: center; gap: 5px;
    color: var(--c-danger); font-size: .76rem; font-weight: 500;
    margin-top: 5px;
}

/* Footer */
.form-footer {
    display: flex; align-items: center; gap: 10px;
    justify-content: flex-end;
    padding-top: 20px; border-top: 1px solid var(--c-border);
}

.btn-cancel {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border: 1.5px solid var(--c-border);
    border-radius: 9px; font-size: .84rem; font-weight: 500;
    color: var(--c-muted); text-decoration: none; background: #fff;
    transition: all .15s;
}
.btn-cancel:hover { border-color: #C5C9D6; color: var(--c-text); background: #F5F6FA; }

.btn-submit {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 22px; background: var(--c-accent);
    color: #fff; border: none; border-radius: 9px;
    font-size: .84rem; font-weight: 600; cursor: pointer;
    box-shadow: 0 2px 8px rgba(79,103,255,.28);
    transition: all .18s;
}
.btn-submit:hover {
    background: var(--c-accent-hover); transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(79,103,255,.35);
}
.btn-submit:active { transform: translateY(0); }
</style>
@endsection