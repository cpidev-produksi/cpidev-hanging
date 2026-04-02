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
                @error('location')<div class="form-error">{{ $message }}</div>@enderror
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
                @error('process_date')<div class="form-error">{{ $message }}</div>@enderror
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
                @error('shift')<div class="form-error">{{ $message }}</div>@enderror
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
                @error('size')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- No Urut Truk (readonly) --}}
            <div class="form-group">
                <label class="form-label" for="truck_no">No Urut Truk</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/>
                        </svg>
                    </div>
                    <input id="truck_no" type="text" class="form-input" value="(Auto setelah simpan)" readonly>
                </div>
                <div class="field-hint">No urut truk otomatis reset per lokasi + tanggal.</div>
            </div>

            {{-- Farm --}}
            <div class="form-group">
                <label class="form-label" for="farm_id">Farm <span class="required">*</span></label>
                <div class="input-wrapper @error('farm_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 21h18"/><path d="M5 21V9l7-4 7 4v12"/><path d="M9 21v-6h6v6"/>
                        </svg>
                    </div>
                    <select id="farm_id" name="farm_id" class="form-input select2-farm">
                        <option value="">— Cari atau Pilih Farm —</option>
                        @foreach($farms as $f)
                            <option value="{{ $f->id }}" @selected(old('farm_id') == $f->id)>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('farm_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Ekspedisi dengan Select2 --}}
            <div class="form-group full-width">
                <label class="form-label" for="expedition_id">Ekspedisi <span class="required">*</span></label>
                <div class="input-wrapper @error('expedition_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>
                        </svg>
                    </div>
                    <select id="expedition_id" name="expedition_id" class="form-input select2-expedition">
                        <option value="">— Cari atau Pilih Ekspedisi —</option>
                        @foreach($expeditions as $e)
                            <option value="{{ $e->id }}" @selected(old('expedition_id') == $e->id)>
                                {{ $e->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('expedition_id')<div class="form-error">{{ $message }}</div>@enderror

                <div style="height:12px"></div>

                {{-- No Polisi dengan Select2 --}}
                <label class="form-label" for="plate_number_id">No Polisi <span class="required">*</span></label>
                <div class="input-wrapper @error('plate_number_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <select id="plate_number_id" name="plate_number_id" class="form-input select2-plate">
                        <option value="">— Pilih No Polisi —</option>
                        @foreach($expeditions as $e)
                            @foreach($e->plateNumbers as $pn)
                                <option value="{{ $pn->id }}" data-expedition="{{ $e->id }}"
                                    @selected(old('plate_number_id') == $pn->id)>
                                    {{ $pn->plate_number }} ({{ $e->name }})
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                @error('plate_number_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- ========== DATA TAMBAHAN PRODUKSI ========== --}}
            <div class="form-section-title full-width" style="margin-top: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8"/><path d="M17 16V8"/><path d="M7 16V8"/><path d="M3 16V8"/><path d="M10 20h4"/><path d="M12 4v16"/>
                </svg>
                Data Truk & Dokumen
            </div>

            <div class="form-group">
                <label class="form-label" for="seal_no">No. Segel</label>
                <div class="input-wrapper @error('seal_no') has-error @enderror">
                    <div class="input-icon">#</div>
                    <input id="seal_no" name="seal_no" type="text" value="{{ old('seal_no') }}" class="form-input" placeholder="No segel">
                </div>
                @error('seal_no')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="truck_arrival_time">Jam Truk Datang</label>
                <div class="input-wrapper @error('truck_arrival_time') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6l3 2"/></svg>
                    </div>
                    <input id="truck_arrival_time" name="truck_arrival_time" type="time" value="{{ old('truck_arrival_time', isset($data) ? \Carbon\Carbon::parse($data->truck_arrival_time)->format('H:i') : '') }}" class="form-input">
                </div>
                @error('truck_arrival_time')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="catch_date">Tgl Tangkap</label>
                <div class="input-wrapper @error('catch_date') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    </div>
                    <input id="catch_date" name="catch_date" type="date" value="{{ old('catch_date') }}" class="form-input">
                </div>
                @error('catch_date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="total_chicken">Total Ekor</label>
                <div class="input-wrapper @error('total_chicken') has-error @enderror">
                    <div class="input-icon">Σ</div>
                    <input id="total_chicken" name="total_chicken" type="number" step="1" value="{{ old('total_chicken') }}" class="form-input" placeholder="0">
                </div>
                @error('total_chicken')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="total_kilo">Total Kilo</label>
                <div class="input-wrapper @error('total_kilo') has-error @enderror">
                    <div class="input-icon">kg</div>
                    <input id="total_kilo" name="total_kilo" type="number" step="0.01" value="{{ old('total_kilo') }}" class="form-input" placeholder="0.00">
                </div>
                @error('total_kilo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="abw">ABW</label>
                <div class="input-wrapper @error('abw') has-error @enderror">
                    <div class="input-icon">abw</div>
                    <input id="abw" name="abw" type="number" step="0.01" value="{{ old('abw') }}" class="form-input" placeholder="0.00">
                </div>
                @error('abw')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="sppa_no">No. SPPA</label>
                <div class="input-wrapper @error('sppa_no') has-error @enderror">
                    <div class="input-icon">#</div>
                    <input id="sppa_no" name="sppa_no" type="text" value="{{ old('sppa_no') }}" class="form-input" placeholder="No SPPA">
                </div>
                @error('sppa_no')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="order_id">Order ID</label>
                <div class="input-wrapper @error('order_id') has-error @enderror">
                    <div class="input-icon">ID</div>
                    <input id="order_id" name="order_id" type="text" value="{{ old('order_id') }}" class="form-input" placeholder="Order ID (string)">
                </div>
                @error('order_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="sppa_date">Tanggal SPPA</label>
                <div class="input-wrapper @error('sppa_date') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    </div>
                    <input id="sppa_date" name="sppa_date" type="date" value="{{ old('sppa_date') }}" class="form-input">
                </div>
                @error('sppa_date')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
    // Inisialisasi Select2 untuk Farm
    if ($('#farm_id').length) {
        $('#farm_id').select2({
            placeholder: "Cari nama farm...",
            allowClear: true,
            width: '100%',
            theme: 'default'
        });
    }

    // Inisialisasi Select2 untuk Ekspedisi
    $('#expedition_id').select2({
        placeholder: "Cari nama ekspedisi...",
        allowClear: true,
        width: '100%',
        theme: 'default',
        language: {
            noResults: function() {
                return "Tidak ada ekspedisi ditemukan";
            }
        }
    });

    // Inisialisasi Select2 untuk No Polisi
    $('#plate_number_id').select2({
        placeholder: "Cari nomor polisi...",
        allowClear: true,
        width: '100%',
        theme: 'default',
        language: {
            noResults: function() {
                return "Tidak ada nomor polisi ditemukan";
            }
        }
    });

    const $plateSelect = $('#plate_number_id');
    const originalPlateOptions = $plateSelect.html();
    $plateSelect.data('original-options', originalPlateOptions);

    // Function untuk filter plate berdasarkan ekspedisi
    function filterPlateByExpedition() {
        const expeditionId = $('#expedition_id').val();
        
        if (!expeditionId) {
            // Jika tidak ada ekspedisi dipilih, tampilkan semua option
            $plateSelect.html($plateSelect.data('original-options'));
        } else {
            // Filter option berdasarkan data-expedition
            const $originalOptions = $($plateSelect.data('original-options'));
            const $filteredOptions = $originalOptions.filter(function() {
                const $option = $(this);
                const optionExpId = $option.data('expedition');
                // Ambil option placeholder (value kosong) dan option yang sesuai dengan ekspedisi
                return $option.val() === '' || optionExpId == expeditionId;
            });
            
            $plateSelect.html($filteredOptions);
        }
        
        // Reset nilai yang dipilih jika tidak sesuai
        const currentVal = $plateSelect.val();
        if (currentVal) {
            const selectedOption = $plateSelect.find(`option[value="${currentVal}"]`);
            if (selectedOption.length === 0 || selectedOption.css('display') === 'none') {
                $plateSelect.val('').trigger('change');
            }
        }
        
        // Refresh Select2
        $plateSelect.trigger('change.select2');
    }

    // Event listener untuk perubahan ekspedisi
    $('#expedition_id').on('change', function() {
        filterPlateByExpedition();
    });

    // Panggil filter saat halaman load (untuk old value)
    filterPlateByExpedition();
});
</script>

<style>
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #E2E5EE;
        border-radius: 10px;
        height: 44px;
        padding: 5px;
        background: #FAFBFD;
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: #E85D2F;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        color: #0D1117;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
    }

    .select2-dropdown {
        border-color: #E2E5EE;
        border-radius: 10px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #E85D2F;
    }

    .select2-search__field {
        border: 1.5px solid #E2E5EE !important;
        border-radius: 8px !important;
        padding: 8px !important;
    }

    .select2-search__field:focus {
        border-color: #E85D2F !important;
        outline: none !important;
    }
    .field-hint{font-size:.74rem;color:#9CA3AF;margin-top:6px;font-weight:600}
</style>
@endsection