@extends('layouts.app')

@section('content')
<div class="page-container">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('master.farms.index') }}" class="breadcrumb-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            Data Farm
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <span>Edit Farm</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="page-title-group">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
            <div>
                <h1 class="page-title">{{ $farm->name }}</h1>
                <p class="page-subtitle">Perbarui informasi farm/mitra</p>
            </div>
        </div>
        <div class="edit-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Mode Edit
        </div>
    </div>

    {{-- Form Card --}}
    <form method="POST" action="{{ route('master.farms.update', $farm) }}" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Perbarui Informasi Farm
        </div>

        <div class="form-grid">
            {{-- Nama Farm --}}
            <div class="form-group full-width">
                <label class="form-label" for="name">
                    Nama Farm
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper @error('name') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <input id="name" name="name" type="text" value="{{ old('name', $farm->name) }}"
                           class="form-input" placeholder="Nama farm">
                </div>
                @error('name')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="form-group full-width">
                <label class="form-label" for="address">
                    Alamat
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper @error('address') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <input id="address" name="address" type="text" value="{{ old('address', $farm->address) }}"
                           class="form-input" placeholder="Alamat lengkap farm">
                </div>
                @error('address')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Kota --}}
            <div class="form-group">
                <label class="form-label" for="city">
                    Kota
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper @error('city') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 8v4l3 3M12 2a15 15 0 0 0 0 20 15 15 0 0 0 0-20z"/>
                        </svg>
                    </div>
                    <input id="city" name="city" type="text" value="{{ old('city', $farm->city) }}"
                           class="form-input" placeholder="Nama kota">
                </div>
                @error('city')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Vendor Code --}}
            <div class="form-group">
                <label class="form-label" for="vendor_code">
                    Vendor Code
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper @error('vendor_code') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M8 12h8"/>
                        </svg>
                    </div>
                    <input id="vendor_code" name="vendor_code" type="text" value="{{ old('vendor_code', $farm->vendor_code) }}"
                           class="form-input" placeholder="Kode vendor">
                </div>
                @error('vendor_code')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Area Category --}}
            <div class="form-group">
                <label class="form-label" for="area_category">
                    Kategori Area
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper select-wrapper @error('area_category') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="9" y1="21" x2="9" y2="9"/>
                        </svg>
                    </div>
                    <select id="area_category" name="area_category" class="form-input form-select">
                        <option value="">— Pilih Kategori Area —</option>
                        <option value="1" {{ old('area_category', $farm->area_category) == 1 ? 'selected' : '' }}>Area 1 (Terdekat)</option>
                        <option value="2" {{ old('area_category', $farm->area_category) == 2 ? 'selected' : '' }}>Area 2</option>
                        <option value="3" {{ old('area_category', $farm->area_category) == 3 ? 'selected' : '' }}>Area 3</option>
                        <option value="4" {{ old('area_category', $farm->area_category) == 4 ? 'selected' : '' }}>Area 4 (Terjauh)</option>
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                </div>
                @error('area_category')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Distance --}}
            <div class="form-group">
                <label class="form-label" for="distance">
                    Jarak
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper @error('distance') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <polyline points="15 6 21 12 15 18"/>
                        </svg>
                    </div>
                    <input id="distance" name="distance" type="text" value="{{ old('distance', $farm->distance) }}"
                           class="form-input" placeholder="Contoh: 15 km">
                </div>
                @error('distance')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
                <div class="help-text">Format jarak bebas, contoh: "15 km", "10 menit", dll.</div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="form-footer">
            <a href="{{ route('master.farms.index') }}" class="btn-cancel">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Batal
            </a>
            <button type="submit" class="btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection