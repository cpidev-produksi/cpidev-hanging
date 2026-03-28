@extends('layouts.app')

@section('content')
<div class="form-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('monitor-controls.index') }}" class="breadcrumb-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="14" rx="2"/>
                <path d="M7 20h10"/>
                <path d="M9 16v4"/>
                <path d="M15 16v4"/>
            </svg>
            Kontrol Monitor
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <span>Buat Kontrol Monitor</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="page-icon-wrap">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="14" rx="2"/>
                    <path d="M8 8h8"/>
                    <path d="M8 12h5"/>
                    <path d="M7 20h10"/>
                </svg>
            </div>
        </div>
        <div>
            <h1 class="page-title">Buat Kontrol Monitor</h1>
            <p class="page-subtitle">Isi formulir di bawah untuk membuat draft kontrol monitor</p>
        </div>
    </div>

    {{-- Form Card --}}
    <form method="POST" action="{{ route('monitor-controls.store') }}" class="form-card">
        @csrf

        <div class="form-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Informasi Proses
        </div>

        <div class="form-grid">
            {{-- Lokasi --}}
            <div class="form-group">
                <label class="form-label" for="location">Lokasi <span class="required">*</span></label>
                <div class="input-wrapper select-wrapper @error('location') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 21s7-4.5 7-10a7 7 0 0 0-14 0c0 5.5 7 10 7 10z"/>
                            <circle cx="12" cy="11" r="2"/>
                        </svg>
                    </div>
                    <select id="location" name="location" class="form-input form-select">
                        <option value="SH01" @selected(old('location', 'SH01') === 'SH01')>SH01</option>
                        <option value="SH02" @selected(old('location') === 'SH02')>SH02</option>
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                @error('location')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Tanggal --}}
            <div class="form-group">
                <label class="form-label" for="process_date">Tanggal <span class="required">*</span></label>
                <div class="input-wrapper @error('process_date') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                    </div>
                    <input id="process_date" type="date" name="process_date"
                           value="{{ old('process_date', date('Y-m-d')) }}"
                           class="form-input">
                </div>
                @error('process_date')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Shift --}}
            <div class="form-group">
                <label class="form-label" for="shift">Shift <span class="required">*</span></label>
                <div class="input-wrapper select-wrapper @error('shift') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v6l3 2"/>
                        </svg>
                    </div>
                    <select id="shift" name="shift" class="form-input form-select">
                        <option value="pagi" @selected(old('shift', 'pagi') === 'pagi')>Pagi</option>
                        <option value="malam" @selected(old('shift') === 'malam')>Malam</option>
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                @error('shift')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Size --}}
            <div class="form-group">
                <label class="form-label" for="size">Size Ayam <span class="required">*</span></label>
                <div class="input-wrapper select-wrapper @error('size') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20"/>
                            <path d="M7 6h10"/>
                            <path d="M7 12h10"/>
                            <path d="M7 18h10"/>
                        </svg>
                    </div>
                    <select id="size" name="size" class="form-input form-select">
                        @foreach($sizes as $s)
                            <option value="{{ $s }}" @selected(old('size') == $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                @error('size')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Sopir --}}
            <div class="form-group">
                <label class="form-label" for="driver_name">Sopir <span class="required">*</span></label>
                <div class="input-wrapper @error('driver_name') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <input id="driver_name" name="driver_name" type="text" value="{{ old('driver_name') }}"
                           class="form-input" placeholder="Nama sopir">
                </div>
                @error('driver_name')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Nominal Ekoran Farm --}}
            <div class="form-group">
                <label class="form-label" for="farm_fee_amount">Nominal Ekoran Farm <span class="required">*</span></label>
                <div class="input-wrapper @error('farm_fee_amount') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 1v22"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <input id="farm_fee_amount" name="farm_fee_amount" type="number" step="1"
                           value="{{ old('farm_fee_amount', 0) }}"
                           class="form-input" placeholder="0">
                </div>
                @error('farm_fee_amount')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Truk --}}
            <div class="form-group full-width">
                <label class="form-label" for="truck_id">Truk (No / Polisi / Ekspedisi) <span class="required">*</span></label>
                <div class="input-wrapper select-wrapper @error('truck_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7z"/>
                            <circle cx="7" cy="19" r="2"/><circle cx="17" cy="19" r="2"/>
                        </svg>
                    </div>
                    <select id="truck_id" name="truck_id" class="form-input form-select">
                        @foreach($trucks as $t)
                            <option value="{{ $t->id }}" @selected(old('truck_id') == $t->id)>
                                {{ $t->no_truck }} - {{ $t->plate_number }} ({{ $t->expedition->name }})
                            </option>
                        @endforeach
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                @error('truck_id')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Farm --}}
            <div class="form-group full-width">
                <label class="form-label" for="farm_id">Farm <span class="required">*</span></label>
                <div class="input-wrapper select-wrapper @error('farm_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18"/><path d="M5 21V9l7-4 7 4v12"/><path d="M9 21v-6h6v6"/>
                        </svg>
                    </div>
                    <select id="farm_id" name="farm_id" class="form-input form-select">
                        @foreach($farms as $f)
                            <option value="{{ $f->id }}" @selected(old('farm_id') == $f->id)>{{ $f->name }}</option>
                        @endforeach
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                @error('farm_id')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="form-footer">
            <a href="{{ route('monitor-controls.index') }}" class="btn-cancel">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Batal
            </a>
            <button class="btn-submit" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Simpan (Draft)
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

.form-page { max-width: 920px; margin: 0 auto; padding: 28px 20px; }

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
.full-width { grid-column: 1 / -1; }

@media (max-width: 720px) {
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

/* Select */
.select-wrapper { position: relative; }
.form-select { appearance: none; padding-right: 36px; cursor: pointer; }
.select-arrow {
    position: absolute; right: 12px;
    color: var(--c-muted); pointer-events: none;
    display: flex; align-items: center;
}

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