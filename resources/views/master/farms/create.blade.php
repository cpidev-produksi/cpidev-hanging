@extends('layouts.app')

@section('content')
<div class="form-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('master.farms.index') }}" class="breadcrumb-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 7h18"/><path d="M6 7v13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7"/>
                <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/>
            </svg>
            Data Farm
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <span>Tambah Farm</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="page-icon-wrap">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"/><path d="M5 21V9l7-4 7 4v12"/><path d="M9 21v-6h6v6"/>
                </svg>
            </div>
        </div>
        <div>
            <h1 class="page-title">Tambah Farm Baru</h1>
            <p class="page-subtitle">Isi formulir di bawah untuk menambahkan farm</p>
        </div>
    </div>

    {{-- Form Card --}}
    <form method="POST" action="{{ route('master.farms.store') }}" class="form-card">
        @csrf

        <div class="form-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Informasi Farm
        </div>

        <div class="form-grid one-col">
            {{-- Nama --}}
            <div class="form-group full-width">
                <label class="form-label" for="name">
                    Nama Farm
                    <span class="required">*</span>
                </label>

                <div class="input-wrapper @error('name') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18"/><path d="M5 21V9l7-4 7 4v12"/><path d="M9 21v-6h6v6"/>
                        </svg>
                    </div>
                    <input id="name" name="name" type="text" value="{{ old('name') }}"
                           class="form-input" placeholder="Contoh: Farm A">
                </div>

                @error('name')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="form-footer">
            <a href="{{ route('master.farms.index') }}" class="btn-cancel">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Batal
            </a>
            <button type="submit" class="btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Simpan Farm
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
.breadcrumb svg { color: #C5C9D6; }

/* Header */
.page-header {
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 24px;
}
.page-icon {
    width: 48px; height: 48px;
    background: var(--c-accent-light);
    color: var(--c-accent);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.page-title { font-size: 1.3rem; font-weight: 700; color: var(--c-text); margin: 0 0 3px; }
.page-subtitle { font-size: .8rem; color: var(--c-muted); margin: 0; }

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
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 28px;
}
.form-grid.one-col { grid-template-columns: 1fr; }
.full-width { grid-column: 1 / -1; }

@media (max-width: 600px) {
    .form-grid { grid-template-columns: 1fr; }
    .full-width { grid-column: auto; }
}

/* Labels */
.form-label {
    display: block;
    font-size: .8rem; font-weight: 600;
    color: var(--c-text);
    margin-bottom: 6px;
}
.required { color: var(--c-danger); margin-left: 2px; }

/* Inputs */
.input-wrapper {
    position: relative;
    display: flex; align-items: center;
    border: 1.5px solid var(--c-border);
    border-radius: 9px;
    background: #FAFBFD;
    transition: border-color .18s, box-shadow .18s;
    overflow: hidden;
}
.input-wrapper:focus-within {
    border-color: var(--c-accent);
    box-shadow: 0 0 0 3px rgba(79,103,255,.12);
    background: #fff;
}
.input-wrapper.has-error {
    border-color: var(--c-danger);
    background: var(--c-danger-light);
}
.input-wrapper.has-error:focus-within {
    box-shadow: 0 0 0 3px rgba(240,62,62,.1);
}

.input-icon {
    display: flex; align-items: center; justify-content: center;
    width: 40px; min-width: 40px;
    color: var(--c-muted);
    pointer-events: none;
}

.form-input {
    flex: 1;
    border: none; outline: none;
    background: transparent;
    padding: 10px 12px 10px 0;
    font-size: .875rem;
    color: var(--c-text);
    width: 100%;
}
.form-input::placeholder { color: #B0B7C3; }

/* Error */
.form-error {
    display: flex; align-items: center; gap: 5px;
    color: var(--c-danger);
    font-size: .76rem; font-weight: 500;
    margin-top: 5px;
}

/* Footer */
.form-footer {
    display: flex; align-items: center; gap: 10px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid var(--c-border);
}

.btn-cancel {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border: 1.5px solid var(--c-border);
    border-radius: 9px;
    font-size: .84rem; font-weight: 500;
    color: var(--c-muted);
    text-decoration: none;
    background: #fff;
    transition: all .15s;
}
.btn-cancel:hover { border-color: #C5C9D6; color: var(--c-text); background: #F5F6FA; }

.btn-submit {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 22px;
    background: var(--c-accent);
    color: #fff;
    border: none; border-radius: 9px;
    font-size: .84rem; font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(79,103,255,.28);
    transition: all .18s;
}
.btn-submit:hover {
    background: var(--c-accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(79,103,255,.35);
}
.btn-submit:active { transform: translateY(0); }
</style>
@endsection