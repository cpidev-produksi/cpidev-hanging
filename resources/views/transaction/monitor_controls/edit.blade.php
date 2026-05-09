@extends('layouts.app')

@section('content')
<div class="form-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('monitor-controls.index') }}" class="breadcrumb-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="14" rx="2"/>
                <path d="M7 20h10"/><path d="M9 16v4"/><path d="M15 16v4"/>
            </svg>
            Kontrol Monitor
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <span>Edit Kontrol Monitor</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="page-icon-wrap">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
        </div>
        <div>
            <h1 class="page-title">Edit Kontrol Monitor</h1>
            <p class="page-subtitle">
                Report: <code class="mono-chip">{{ $monitor->report_code }}</code>
                <span class="badge-truck"># {{ $monitor->truck_no }}</span>
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('monitor-controls.update', $monitor) }}" class="form-card">
        @csrf
        @method('PUT')

        {{-- ===== INFORMASI PROSES ===== --}}
        <div class="form-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Informasi Proses
        </div>

        <div class="form-grid">

            {{-- Lokasi (readonly) --}}
            <div class="form-group">
                <label class="form-label">Lokasi</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 21s7-4.5 7-10a7 7 0 0 0-14 0c0 5.5 7 10 7 10z"/>
                            <circle cx="12" cy="11" r="2"/>
                        </svg>
                    </div>
                    <input class="form-input" value="{{ $monitor->location }}" readonly>
                </div>
                <div class="field-hint">Lokasi tidak bisa diubah setelah dibuat.</div>
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
                           value="{{ old('process_date', $monitor->process_date?->format('Y-m-d')) }}"
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
                        <option value="pagi"  @selected(old('shift', $monitor->shift) === 'pagi')>Pagi</option>
                        <option value="malam" @selected(old('shift', $monitor->shift) === 'malam')>Malam</option>
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
                            <path d="M12 2v20"/><path d="M7 6h10"/><path d="M7 12h10"/><path d="M7 18h10"/>
                        </svg>
                    </div>
                    <select id="size" name="size" class="form-input form-select">
                        @foreach($sizes as $s)
                            <option value="{{ $s }}" @selected(old('size', $monitor->size) == $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <div class="select-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                @error('size')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Farm — datalist native --}}
            <div class="form-group">
                <label class="form-label" for="farm_name_input">Farm <span class="required">*</span></label>
                <div class="input-wrapper @error('farm_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <input list="farm-list" id="farm_name_input" class="form-input"
                           autocomplete="off" placeholder="Ketik atau pilih farm…"
                           value="{{ old('_farm_name', $monitor->farm?->name) }}">
                    <input type="hidden" id="farm_id" name="farm_id"
                           value="{{ old('farm_id', $monitor->farm_id) }}">
                    <datalist id="farm-list">
                        @foreach($farms as $f)
                            <option data-id="{{ $f->id }}" value="{{ $f->name }}">
                        @endforeach
                    </datalist>
                </div>
                @error('farm_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Ekspedisi — datalist native --}}
            <div class="form-group">
                <label class="form-label" for="expedition_name_input">Ekspedisi <span class="required">*</span></label>
                <div class="input-wrapper @error('expedition_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13" rx="1"/>
                            <path d="M16 8h4l3 5v3h-7V8z"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                    <input list="expedition-list" id="expedition_name_input" class="form-input"
                           autocomplete="off" placeholder="Ketik atau pilih ekspedisi…"
                           value="{{ old('_expedition_name', $monitor->expedition?->name) }}">
                    <input type="hidden" id="expedition_id" name="expedition_id"
                           value="{{ old('expedition_id', $monitor->expedition_id) }}">
                    <datalist id="expedition-list">
                        @foreach($expeditions as $e)
                            <option data-id="{{ $e->id }}" value="{{ $e->name }}">
                        @endforeach
                    </datalist>
                </div>
                @error('expedition_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- No Polisi — datalist native, difilter JS --}}
            <div class="form-group">
                <label class="form-label" for="plate_name_input">No Polisi <span class="required">*</span></label>
                <div class="input-wrapper @error('plate_number_id') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="10" rx="2"/>
                            <path d="M6 11h.01M18 11h.01M10 11h4"/>
                        </svg>
                    </div>
                    <input list="plate-list" id="plate_name_input" class="form-input"
                           autocomplete="off" placeholder="Pilih ekspedisi dulu…"
                           value="{{ old('_plate_name', $monitor->plateNumber?->plate_number) }}">
                    <input type="hidden" id="plate_number_id" name="plate_number_id"
                           value="{{ old('plate_number_id', $monitor->plate_number_id) }}">
                    <datalist id="plate-list">
                        {{-- diisi oleh JS --}}
                    </datalist>
                </div>
                @error('plate_number_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- ===== DATA TRUK & DOKUMEN ===== --}}
            <div class="form-section-title full-width" style="margin-top:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8"/><path d="M17 16V8"/><path d="M7 16V8"/><path d="M3 16V8"/>
                    <path d="M10 20h4"/><path d="M12 4v16"/>
                </svg>
                Data Truk &amp; Dokumen
            </div>

            {{-- No. Segel --}}
            <div class="form-group">
                <label class="form-label" for="seal_no">No. Segel</label>
                <div class="input-wrapper @error('seal_no') has-error @enderror">
                    <div class="input-icon">#</div>
                    <input id="seal_no" name="seal_no" type="text"
                           value="{{ old('seal_no', $monitor->seal_no) }}"
                           class="form-input">
                </div>
                @error('seal_no')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Jam Truk Datang — format 24 jam --}}
            <div class="form-group">
                <label class="form-label" for="truck_arrival_time">Jam Truk Datang</label>
                <div class="input-wrapper @error('truck_arrival_time') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6l3 2"/></svg>
                    </div>
                    <input id="truck_arrival_time" name="truck_arrival_time" type="time"
                           value="{{ old('truck_arrival_time', $monitor->truck_arrival_time ? \Carbon\Carbon::parse($monitor->truck_arrival_time)->format('H:i') : '') }}"
                           step="60"
                           class="form-input time-24">
                </div>
                @error('truck_arrival_time')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Tgl Tangkap --}}
            <div class="form-group">
                <label class="form-label" for="catch_date">Tgl Tangkap</label>
                <div class="input-wrapper @error('catch_date') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    </div>
                    <input id="catch_date" name="catch_date" type="date"
                           value="{{ old('catch_date', $monitor->catch_date?->format('Y-m-d')) }}"
                           class="form-input">
                </div>
                @error('catch_date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Total Ekor --}}
            <div class="form-group">
                <label class="form-label" for="total_chicken">Total Ekor</label>
                <div class="input-wrapper @error('total_chicken') has-error @enderror">
                    <div class="input-icon">Σ</div>
                    <input id="total_chicken" name="total_chicken" type="number" step="1"
                           value="{{ old('total_chicken', $monitor->total_chicken) }}"
                           class="form-input">
                </div>
                @error('total_chicken')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Total Kilo --}}
            <div class="form-group">
                <label class="form-label" for="total_kilo">Total Kilo</label>
                <div class="input-wrapper @error('total_kilo') has-error @enderror">
                    <div class="input-icon">kg</div>
                    <input id="total_kilo" name="total_kilo" type="number" step="0.01"
                           value="{{ old('total_kilo', $monitor->total_kilo) }}"
                           class="form-input">
                </div>
                @error('total_kilo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- ABW --}}
            <div class="form-group">
                <label class="form-label" for="abw">ABW</label>
                <div class="input-wrapper @error('abw') has-error @enderror">
                    <div class="input-icon">abw</div>
                    <input id="abw" name="abw" type="number" step="0.01"
                           value="{{ old('abw', $monitor->abw) }}"
                           class="form-input">
                </div>
                @error('abw')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- No. SPPA --}}
            <div class="form-group">
                <label class="form-label" for="sppa_no">No. SPPA</label>
                <div class="input-wrapper @error('sppa_no') has-error @enderror">
                    <div class="input-icon">#</div>
                    <input id="sppa_no" name="sppa_no" type="text"
                           value="{{ old('sppa_no', $monitor->sppa_no) }}"
                           class="form-input">
                </div>
                @error('sppa_no')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Order ID --}}
            <div class="form-group">
                <label class="form-label" for="order_id">Order ID</label>
                <div class="input-wrapper @error('order_id') has-error @enderror">
                    <div class="input-icon">ID</div>
                    <input id="order_id" name="order_id" type="text"
                           value="{{ old('order_id', $monitor->order_id) }}"
                           class="form-input">
                </div>
                @error('order_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Tanggal SPPA --}}
            <div class="form-group">
                <label class="form-label" for="sppa_date">Tanggal SPPA</label>
                <div class="input-wrapper @error('sppa_date') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    </div>
                    <input id="sppa_date" name="sppa_date" type="date"
                           value="{{ old('sppa_date', $monitor->sppa_date?->format('Y-m-d')) }}"
                           class="form-input">
                </div>
                @error('sppa_date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

        </div>{{-- /form-grid --}}

        <div class="form-footer">
            <a href="{{ route('monitor-controls.index') }}" class="btn-cancel">Batal</a>
            <button class="btn-submit" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</div>

{{-- ============================================================
     Master data ekspedisi → plat nomor (untuk filtering JS)
     ============================================================ --}}
@php
    $expeditionsJson = $expeditions->map(fn ($e) => [
        'id'     => $e->id,
        'name'   => $e->name,
        'plates' => $e->plateNumbers->map(fn ($pn) => [
            'id'    => $pn->id,
            'plate' => $pn->plate_number,
        ])->values(),
    ])->values();
@endphp
<script>
(function () {
    // ── Master data dari Blade ──────────────────────────────────────────────
    const EXPEDITIONS = @json($expeditionsJson);

    // Nilai awal dari model / old()
    const INIT_EXP_ID   = "{{ old('expedition_id', $monitor->expedition_id) }}";
    const INIT_PLATE_ID = "{{ old('plate_number_id', $monitor->plate_number_id) }}";

    // ── Elemen ─────────────────────────────────────────────────────────────
    const farmNameInput  = document.getElementById('farm_name_input');
    const farmIdHidden   = document.getElementById('farm_id');
    const farmDatalist   = document.getElementById('farm-list');

    const expNameInput   = document.getElementById('expedition_name_input');
    const expIdHidden    = document.getElementById('expedition_id');
    const expDatalist    = document.getElementById('expedition-list');

    const plateNameInput = document.getElementById('plate_name_input');
    const plateIdHidden  = document.getElementById('plate_number_id');
    const plateDatalist  = document.getElementById('plate-list');

    // ── Helper: resolve datalist option → id ───────────────────────────────
    function resolveId(inputEl, datalistEl, hiddenEl) {
        const val = inputEl.value.trim();
        const opts = datalistEl.querySelectorAll('option');
        for (const opt of opts) {
            if (opt.value === val) {
                hiddenEl.value = opt.dataset.id ?? '';
                return opt.dataset.id ?? '';
            }
        }
        hiddenEl.value = '';
        return '';
    }

    // ── Farm ───────────────────────────────────────────────────────────────
    farmNameInput.addEventListener('input',  () => resolveId(farmNameInput, farmDatalist, farmIdHidden));
    farmNameInput.addEventListener('change', () => resolveId(farmNameInput, farmDatalist, farmIdHidden));

    // ── Bangun datalist plat berdasarkan ekspedisi ──────────────────────────
    function buildPlateDatalist(expId, restorePlateId) {
        plateDatalist.innerHTML = '';

        if (!expId) {
            plateNameInput.disabled    = true;
            plateNameInput.placeholder = 'Pilih ekspedisi dulu…';
            return;
        }

        const exp = EXPEDITIONS.find(e => String(e.id) === String(expId));
        if (!exp || !exp.plates.length) {
            plateNameInput.disabled    = true;
            plateNameInput.placeholder = 'Tidak ada plat untuk ekspedisi ini';
            return;
        }

        exp.plates.forEach(p => {
            const opt = document.createElement('option');
            opt.value      = p.plate;
            opt.dataset.id = p.id;
            plateDatalist.appendChild(opt);
        });

        plateNameInput.disabled    = false;
        plateNameInput.placeholder = 'Ketik atau pilih nomor polisi…';

        // Restore nilai plat jika valid di ekspedisi ini
        if (restorePlateId) {
            const plate = exp.plates.find(p => String(p.id) === String(restorePlateId));
            if (plate) {
                plateNameInput.value = plate.plate;
                plateIdHidden.value  = plate.id;
            } else {
                // plat tidak cocok dengan ekspedisi saat ini → kosongkan
                plateNameInput.value = '';
                plateIdHidden.value  = '';
            }
        }
    }

    function onExpeditionChange() {
        const expId = resolveId(expNameInput, expDatalist, expIdHidden);
        // Saat user mengubah ekspedisi secara manual, tidak perlu restore plat lama
        buildPlateDatalist(expId, null);
        plateNameInput.value = '';
        plateIdHidden.value  = '';
    }

    expNameInput.addEventListener('input',  onExpeditionChange);
    expNameInput.addEventListener('change', onExpeditionChange);

    // ── Plat nomor ─────────────────────────────────────────────────────────
    plateNameInput.addEventListener('input',  () => resolveId(plateNameInput, plateDatalist, plateIdHidden));
    plateNameInput.addEventListener('change', () => resolveId(plateNameInput, plateDatalist, plateIdHidden));

    // ── Init: muat plat sesuai ekspedisi model / old() ─────────────────────
    if (INIT_EXP_ID) {
        buildPlateDatalist(INIT_EXP_ID, INIT_PLATE_ID);
    }
})();
</script>

<style>
/* ── Badge no urut di header edit ───────────────────── */
.badge-truck {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .72rem;
    font-weight: 700;
    color: #E85D2F;
    background: #FEF0EB;
    border: 1px solid #FBBFA4;
    border-radius: 20px;
    padding: 2px 10px;
    margin-left: 8px;
    vertical-align: middle;
    letter-spacing: .01em;
}

/* ── Jam 24 jam: sembunyikan AM/PM di browser yg mendukung ── */
.time-24::-webkit-datetime-edit-ampm-field { display: none !important; }
input[type="time"].time-24 { letter-spacing: .05em; }

/* ── Misc ───────────────────────────────────────────── */
.field-hint  { font-size: .74rem; color: #9CA3AF; margin-top: 6px; font-weight: 600; }
.mono-chip   { font-family: 'Fira Code','Courier New',monospace; background: #F3F4F8; padding: 2px 8px; border-radius: 8px; }
</style>
@endsection