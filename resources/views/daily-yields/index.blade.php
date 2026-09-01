@extends('layouts.app')

@section('title', 'Daily Monitoring Yield')

@php
    $allowedUploadRoles = ['admin', 'supervisor', 'superadmin', 'manager'];
    $canUpload = in_array(auth()->user()?->role?->slug, $allowedUploadRoles, true);
@endphp

@push('styles')
<style>
    /* ===== Daily Monitoring Yield — mengikuti design token layout (panel/table/alert) ===== */
    .dmy-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }
    .dmy-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 20px;
        color: var(--text-main);
        margin-bottom: 2px;
    }
    .dmy-subtitle {
        font-size: 13px;
        color: var(--text-muted);
    }
    .dmy-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .dmy-select {
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid var(--card-border);
        background: white;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        color: var(--text-main);
        cursor: pointer;
    }
    .dmy-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid transparent;
        line-height: 1;
        transition: background .15s, border-color .15s;
    }
    .dmy-btn-outline { background: white; border-color: var(--card-border); color: var(--text-main); }
    .dmy-btn-outline:hover { background: #f5f7fc; }
    .dmy-btn-primary { background: var(--accent); border-color: rgba(232,93,47,0.3); color: white; }
    .dmy-btn-primary:hover { background: #d44f24; }
    .dmy-btn-link {
        background: none; border: none; color: var(--accent);
        font-weight: 700; font-size: 12px; cursor: pointer; padding: 0;
        font-family: 'DM Sans', sans-serif;
    }
    .dmy-btn-link:hover { text-decoration: underline; }

    .dmy-panel-header-actions { display: flex; align-items: center; gap: 14px; }
    .dmy-panel-desc { font-size: 12px; color: var(--text-muted); margin-top: 4px; display: block; }
    .dmy-panel-title-col { display: flex; flex-direction: column; }

    .dmy-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 999px;
        border: 1px solid var(--card-border); background: #fafbff;
        font-size: 12px; font-weight: 600; color: var(--text-main); cursor: pointer;
        user-select: none;
    }
    .dmy-chip input { accent-color: var(--accent); cursor: pointer; }

    .dmy-badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .dmy-badge-success { background: rgba(16,185,129,0.12); color: #065f46; }
    .dmy-badge-muted { background: #eef0f6; color: var(--text-muted); }
    .dmy-badge-danger { background: rgba(239,68,68,0.12); color: #7f1d1d; }

    .dmy-form-group { margin-bottom: 16px; }
    .dmy-form-group:last-child { margin-bottom: 0; }
    .dmy-form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
    .dmy-form-control, .dmy-form-select {
        width: 100%; padding: 10px 12px; border-radius: 10px;
        border: 1px solid var(--card-border); font-family: 'DM Sans', sans-serif;
        font-size: 13px; color: var(--text-main); background: white;
    }
    .dmy-form-control:focus, .dmy-form-select:focus { outline: none; border-color: var(--accent); }
    .dmy-form-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 6px; line-height: 1.5; }
    .dmy-form-hint a { color: var(--accent); font-weight: 600; }

    .dmy-empty {
        background: #fafbff; border: 1px dashed var(--card-border);
        border-radius: 14px; padding: 24px; color: var(--text-muted);
        font-size: 13px; text-align: center;
    }

    /* Tabel Detail Plant — header berkelompok (Cell H0..H4 + subcell) */
    .dmy-table-scroll { overflow-x: auto; }
    .dmy-detail-table { min-width: 900px; }
    .dmy-detail-table th, .dmy-detail-table td { text-align: center; white-space: nowrap; }
    .dmy-detail-table thead th { vertical-align: middle; }
    .dmy-detail-table th:not(:last-child),
    .dmy-detail-table td:not(:last-child) {
        border-right: 1px solid var(--card-border);
    }
    .dmy-detail-table td.is-empty {
        background: rgba(239,68,68,0.08);
        color: #7f1d1d;
        font-weight: 700;
    }
    .dmy-last-update {
        padding: 14px 20px 18px;
        font-size: 12.5px;
        color: var(--text-muted);
        border-top: 1px solid var(--card-border);
    }
    .dmy-last-update strong { color: var(--text-main); font-weight: 700; }

    /* Modal upload — pola sama dengan .logout-modal-overlay di layout, tanpa dependensi Bootstrap JS */
    .dmy-modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px); z-index: 200; display: none;
        align-items: center; justify-content: center; padding: 20px;
    }
    .dmy-modal-overlay.open { display: flex; }
    .dmy-modal {
        background: white; border-radius: 18px; box-shadow: 0 24px 60px rgba(0,0,0,0.18);
        max-width: 460px; width: 100%; max-height: 90vh; overflow-y: auto;
        animation: dmyModalIn .22s cubic-bezier(.4,0,.2,1);
    }
    @keyframes dmyModalIn {
        from { transform: scale(0.94); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    .dmy-modal-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px; border-bottom: 1px solid var(--card-border);
    }
    .dmy-modal-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 16px; color: var(--text-main); }
    .dmy-modal-close { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 4px; line-height: 0; }
    .dmy-modal-close:hover { color: var(--text-main); }
    .dmy-modal-body { padding: 20px 22px; }
    .dmy-modal-actions { display: flex; gap: 10px; padding: 16px 22px 20px; border-top: 1px solid var(--card-border); }
    .dmy-modal-actions .dmy-btn { flex: 1; justify-content: center; }

    @media (max-width: 640px) {
        .dmy-toolbar { align-items: flex-start; }
        .dmy-actions { width: 100%; }
    }
</style>
@endpush

@section('content')

<div class="dmy-toolbar">
    <div>
        <div class="dmy-title">Daily Monitoring Yield</div>
        <div class="dmy-subtitle">
            @if($currentUpload)
                Periode: <strong>{{ $currentUpload->periode_label }}</strong>
                &middot; Diupload {{ $currentUpload->created_at->format('d M Y H:i') }}
                @if($currentUpload->uploader) oleh {{ $currentUpload->uploader->name }} @endif
            @else
                Belum ada data yang diupload.
            @endif
        </div>
    </div>

    <div class="dmy-actions">
        {{-- Filter periode --}}
        @if($periodeOptions->isNotEmpty())
            <form method="GET" action="{{ route('daily-yields.index') }}">
                <select name="upload_id" class="dmy-select" onchange="this.form.submit()">
                    @foreach($periodeOptions as $opt)
                        <option value="{{ $opt->id }}" @selected($currentUpload && $currentUpload->id === $opt->id)>
                            {{ $bulanNames[$opt->bulan] ?? $opt->bulan }} {{ $opt->tahun }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif

        <a href="{{ route('daily-yields.template.download') }}" class="dmy-btn dmy-btn-outline">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3v12"></path><path d="M7 10l5 5 5-5"></path><path d="M5 21h14"></path>
            </svg>
            Unduh Template
        </a>

        @if($canUpload)
            <button type="button" class="dmy-btn dmy-btn-primary" onclick="openUploadModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Data
            </button>
        @endif
    </div>
</div>

{{-- Layout sudah otomatis menampilkan banner untuk session('status') dan $errors->any() di atas @yield('content'),
     jadi pesan sukses di halaman ini memakai komponen .alert yang sama persis supaya konsisten. --}}
@if(session('success'))
    <div class="alert alert-success">
        <svg class="alert-icon" width="18" height="18" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <div>
            <div class="alert-title">Berhasil</div>
            <div>{{ session('success') }}</div>
        </div>
        <button class="alert-close" onclick="this.closest('.alert').remove()" type="button">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
    </div>
@endif
{{-- Catatan: layout ini otomatis menampilkan banner error dari $errors->any() juga,
     jadi blok error terpisah di sini sengaja tidak diduplikasi. Kalau controller
     men-flash pesan sukses ke session('status') alih-alih session('success'),
     blok manual di atas bahkan bisa dihapus karena layout sudah menanganinya. --}}

@if($plants->isEmpty())
    <div class="dmy-empty">
        Belum ada data Daily Monitoring Yield untuk ditampilkan. Silakan unduh template lalu upload data melalui tombol "Tambah Data".
    </div>
@else

    {{-- Filter checkbox plant --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Filter Plant</span>
            <div class="dmy-panel-header-actions">
                <button type="button" id="btnCheckAll" class="dmy-btn-link">Pilih Semua</button>
                <button type="button" id="btnUncheckAll" class="dmy-btn-link">Kosongkan</button>
            </div>
        </div>
        <div class="panel-body">
            <div id="plantCheckboxes" style="display:flex; flex-wrap:wrap; gap:8px;">
                @foreach($plants as $p)
                    <label class="dmy-chip">
                        <input type="checkbox" class="plant-filter" value="{{ $p->plant }}" checked>
                        {{ $p->plant }}
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Grafik utama --}}
    <div class="panel">
        <div class="panel-header">
            <div class="dmy-panel-title-col">
                <span class="panel-title">Ach. Yield H0 - H4 &amp; Yield FG per Plant</span>
                <span class="dmy-panel-desc">Klik salah satu bar plant untuk melihat detail lengkap di bawah. Bar berwarna merah menandakan nilai 0 / data belum terisi.</span>
            </div>
        </div>
        <div class="panel-body">
            <canvas id="yieldChart" height="90"></canvas>
        </div>
    </div>

    {{-- Detail plant (muncul setelah klik bar) --}}
    <div class="panel" id="detailSection" style="display:none;">
        <div class="panel-header">
            <span class="panel-title">Detail Plant: <span id="detailPlantName">-</span></span>
        </div>
        <div class="panel-body" style="padding:0;">
            <div class="dmy-table-scroll">
                <table class="table dmy-detail-table" id="detailTable">
                    <thead>
                        <tr>
                            <th colspan="2">H0</th>
                            <th colspan="2">H1 (GRILLER)</th>
                            <th colspan="2">H2 (PARTING)</th>
                            <th colspan="2">H3 (CUT-UP)</th>
                            <th colspan="2">H4 (BONELESS)</th>
                            <th rowspan="2">YIELD FG</th>
                            <th rowspan="2">TOTAL FG + BP</th>
                            <th rowspan="2">SUMPO (RM+BP-SVUV)</th>
                            <th rowspan="2">LOST</th>
                        </tr>
                        <tr>
                            <th>Yield Titik 0</th>
                            <th>Ach. Yield</th>
                            <th>Yield H1 thd Titik 0</th>
                            <th>Ach. Yield</th>
                            <th>Yield H2 thd Titik 0</th>
                            <th>Ach. Yield</th>
                            <th>Yield H3 thd Titik 0</th>
                            <th>Ach. Yield</th>
                            <th>Yield H4 thd Titik 0</th>
                            <th>Ach. Yield</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- diisi oleh JS: satu baris data plant --}}
                    </tbody>
                </table>
            </div>
            <div class="dmy-last-update" id="detailLastUpdate"></div>
        </div>
    </div>

    {{-- Riwayat upload --}}
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Riwayat Upload</span>
        </div>
        <div class="panel-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Nama File</th>
                        <th>Diupload Oleh</th>
                        <th>Tanggal Upload</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatUpload as $u)
                        <tr>
                            <td>{{ $bulanNames[$u->bulan] ?? $u->bulan }} {{ $u->tahun }}</td>
                            <td>{{ $u->file_name ?? '-' }}</td>
                            <td>{{ $u->uploader->name ?? '-' }}</td>
                            <td>{{ $u->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($u->is_latest)
                                    <span class="dmy-badge dmy-badge-success">Versi Terbaru</span>
                                @else
                                    <span class="dmy-badge dmy-badge-muted">Versi Lama</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Modal Tambah Data --}}
@if($canUpload)
<div class="dmy-modal-overlay {{ $errors->any() ? 'open' : '' }}" id="uploadModalOverlay">
    <div class="dmy-modal">
        <form method="POST" action="{{ route('daily-yields.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="dmy-modal-head">
                <span class="dmy-modal-title">Tambah Data Daily Monitoring Yield</span>
                <button type="button" class="dmy-modal-close" onclick="closeUploadModal()">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
            <div class="dmy-modal-body">
                <div class="dmy-form-group">
                    <label class="dmy-form-label">Periode (Bulan)</label>
                    <select name="bulan" class="dmy-form-select" required>
                        <option value="">-- Pilih Bulan --</option>
                        @foreach($bulanNames as $num => $name)
                            <option value="{{ $num }}" @selected(old('bulan') == $num || (!old('bulan') && (int) date('n') === $num))>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="dmy-form-group">
                    <label class="dmy-form-label">Tahun</label>
                    <input type="number" name="tahun" class="dmy-form-control" min="2000" max="2100"
                           value="{{ old('tahun', date('Y')) }}" required>
                </div>
                <div class="dmy-form-group">
                    <label class="dmy-form-label">Upload File Excel</label>
                    <input type="file" name="file" class="dmy-form-control" accept=".xlsx,.xls" required>
                    <div class="dmy-form-hint">
                        Gunakan format sesuai
                        <a href="{{ route('daily-yields.template.download') }}">template ini</a>.
                        Jika periode yang sama sudah pernah diupload sebelumnya, data lama tetap tersimpan
                        sebagai riwayat dan dashboard akan menampilkan versi terbaru.
                    </div>
                </div>
            </div>
            <div class="dmy-modal-actions">
                <button type="button" class="dmy-btn dmy-btn-outline" onclick="closeUploadModal()">Batal</button>
                <button type="submit" class="dmy-btn dmy-btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@php
    // Dipindahkan keluar dari @json() agar tidak ada closure/arrow function
    // yang ditaruh langsung di dalam directive Blade (sering memicu
    // false-positive "unclosed bracket" pada beberapa linter/IDE).
    $plantsDataForChart = $plants->map(fn ($p) => [
        'plant' => $p->plant,
        'yield_titik_0' => $p->yield_titik_0,
        'ach_yield_h0' => $p->ach_yield_h0,
        'yield_h1' => $p->yield_h1,
        'ach_yield_h1' => $p->ach_yield_h1,
        'yield_h2' => $p->yield_h2,
        'ach_yield_h2' => $p->ach_yield_h2,
        'yield_h3' => $p->yield_h3,
        'ach_yield_h3' => $p->ach_yield_h3,
        'yield_h4' => $p->yield_h4,
        'ach_yield_h4' => $p->ach_yield_h4,
        'yield_fg' => $p->yield_fg,
        'total_fg_bp' => $p->total_fg_bp,
        'sumpo' => $p->sumpo,
        'lost' => $p->lost,
        'tanggal_update_terakhir' => optional($p->tanggal_update_terakhir)->format('d M Y'),
    ])->values();
@endphp
<script>
(function () {
    // Data mentah per plant untuk periode yang sedang ditampilkan
    // eslint-disable-next-line no-undef
    const plantsData = @json($plantsDataForChart);

    // ===== Modal Tambah Data (tanpa dependensi Bootstrap JS) =====
    window.openUploadModal = function () {
        const overlay = document.getElementById('uploadModalOverlay');
        if (!overlay) return;
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    };
    window.closeUploadModal = function () {
        const overlay = document.getElementById('uploadModalOverlay');
        if (!overlay) return;
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    };
    document.getElementById('uploadModalOverlay')?.addEventListener('click', function (e) {
        if (e.target === this) closeUploadModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeUploadModal();
    });

    const canvas = document.getElementById('yieldChart');
    if (!canvas || plantsData.length === 0) return;

    const SERIES = [
        { key: 'ach_yield_h0', label: 'Ach. Yield H0', color: '#0d6efd' },
        { key: 'ach_yield_h1', label: 'Ach. Yield H1', color: '#6610f2' },
        { key: 'ach_yield_h2', label: 'Ach. Yield H2', color: '#d63384' },
        { key: 'ach_yield_h3', label: 'Ach. Yield H3', color: '#fd7e14' },
        { key: 'ach_yield_h4', label: 'Ach. Yield H4', color: '#198754' },
        { key: 'yield_fg', label: 'Yield FG', color: '#20c997' },
    ];

    function hexToRgba(hex, alpha) {
        const v = hex.replace('#', '');
        const r = parseInt(v.substring(0, 2), 16);
        const g = parseInt(v.substring(2, 4), 16);
        const b = parseInt(v.substring(4, 6), 16);
        return `rgba(${r},${g},${b},${alpha})`;
    }

    // Baris "AVERAGE" sengaja tidak disimpan/diikutkan (sudah difilter sejak proses upload)
    function getVisiblePlants() {
        const checked = Array.from(document.querySelectorAll('.plant-filter:checked')).map(cb => cb.value);
        return plantsData.filter(p => checked.includes(p.plant));
    }

    function buildDatasets(visiblePlants) {
        return SERIES.map(s => ({
            label: s.label,
            data: visiblePlants.map(p => (p[s.key] ?? 0) * 100),
            backgroundColor: visiblePlants.map(p => {
                const val = p[s.key];
                return (val === 0 || val === null) ? hexToRgba('#dc3545', 0.85) : hexToRgba(s.color, 0.85);
            }),
            borderColor: visiblePlants.map(p => {
                const val = p[s.key];
                return (val === 0 || val === null) ? '#dc3545' : s.color;
            }),
            borderWidth: 1,
        }));
    }

    let currentLabels = [];

    let chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    ticks: { callback: (v) => v + '%' },
                    title: { display: true, text: 'Persentase (%)' },
                },
                x: {
                    ticks: { autoSkip: false, maxRotation: 60, minRotation: 30 },
                },
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const val = ctx.raw;
                            const flag = (val === 0) ? ' \u26a0 Data kosong' : '';
                            return ctx.dataset.label + ': ' + val.toFixed(2) + '%' + flag;
                        },
                    },
                },
            },
            onClick: function (evt, elements) {
                if (!elements.length) return;
                const idx = elements[0].index;
                const plantName = currentLabels[idx];
                showDetail(plantName);
            },
        },
    });

    function renderChart() {
        const visible = getVisiblePlants();
        currentLabels = visible.map(p => p.plant);
        chart.data.labels = currentLabels;
        chart.data.datasets = buildDatasets(visible);
        chart.update();
    }

    // Urutan kolom mengikuti header tabel: H0, H1(GRILLER), H2, H3, H4 (2 subcell tiap grup),
    // lalu YIELD FG, TOTAL FG + BP, SUMPO (RM+BP-SVUV), LOST (1 kolom masing-masing).
    const DETAIL_COLUMNS = [
        'yield_titik_0', 'ach_yield_h0',
        'yield_h1', 'ach_yield_h1',
        'yield_h2', 'ach_yield_h2',
        'yield_h3', 'ach_yield_h3',
        'yield_h4', 'ach_yield_h4',
        'yield_fg', 'total_fg_bp', 'sumpo', 'lost',
    ];

    function showDetail(plantName) {
        const row = plantsData.find(p => p.plant === plantName);
        if (!row) return;

        document.getElementById('detailPlantName').textContent = plantName;
        const tbody = document.querySelector('#detailTable tbody');
        tbody.innerHTML = '';

        const tr = document.createElement('tr');
        DETAIL_COLUMNS.forEach((key) => {
            const val = row[key];
            const isEmpty = (val === 0 || val === null || val === undefined);
            const td = document.createElement('td');
            td.textContent = (val === null || val === undefined) ? '-' : (val * 100).toFixed(2) + '%';
            if (isEmpty) {
                td.classList.add('is-empty');
                td.title = 'Data kosong';
                td.textContent += ' \u26a0';
            }
            tr.appendChild(td);
        });
        tbody.appendChild(tr);

        // Keterangan di luar tabel
        const lastUpdateEl = document.getElementById('detailLastUpdate');
        lastUpdateEl.innerHTML = 'Last Update: <strong>' + (row.tanggal_update_terakhir ?? '-') + '</strong>';

        document.getElementById('detailSection').style.display = '';
        document.getElementById('detailSection').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    document.querySelectorAll('.plant-filter').forEach(cb => cb.addEventListener('change', renderChart));
    document.getElementById('btnCheckAll')?.addEventListener('click', () => {
        document.querySelectorAll('.plant-filter').forEach(cb => (cb.checked = true));
        renderChart();
    });
    document.getElementById('btnUncheckAll')?.addEventListener('click', () => {
        document.querySelectorAll('.plant-filter').forEach(cb => (cb.checked = false));
        renderChart();
    });

    renderChart();
})();
</script>
@endpush