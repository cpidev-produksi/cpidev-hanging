@extends('layouts.app')

@push('styles')
    @include('report.cutup._styles')
    <style>
        .v-top { display:grid; grid-template-columns:120px 150px minmax(0,1fr) 190px; gap:14px; }
        @media (max-width:820px){ .v-top { grid-template-columns:repeat(2,minmax(0,1fr)); } .v-top .v-product { grid-column:1 / -1; } }
        @media (max-width:520px){ .v-top { grid-template-columns:1fr; } }
        .cu-input.code { text-transform:uppercase; letter-spacing:.06em; font-weight:700; }
        .cnt { font-variant-numeric:tabular-nums; }
        .cnt.full { color:var(--success); font-weight:700; }

        /* ===== Editor (row-card) + tabel data ===== */
        .row-card[hidden] { display:none; }
        .row-card.editor { margin-bottom:16px; border-color:rgba(232,93,47,.45); background:#fffdfc; box-shadow:0 0 0 3px rgba(232,93,47,.08); }
        .row-title { font-size:13px; font-weight:800; color:var(--text-main); }
        .row-foot { display:flex; align-items:center; justify-content:flex-end; gap:8px; flex-wrap:wrap; margin-top:16px; padding-top:14px; border-top:1px solid var(--card-border); }
        .row-foot .cu-field-error { margin:0 auto 0 0; }
        .rec-wrap { overflow-x:auto; border:1px solid var(--card-border); border-radius:12px; }
        .rec-wrap .table { min-width:760px; }
        .rec-wrap .table th, .rec-wrap .table td { white-space:nowrap; }
        .rec-wrap .table td.wrap { white-space:normal; min-width:140px; }
        .rec-table tr.is-editing td { background:rgba(232,93,47,.06); }
        .rec-actions { display:flex; gap:6px; justify-content:flex-end; }
        .res-set { display:inline-flex; gap:3px; }
        .res { display:inline-flex; align-items:center; justify-content:center; min-width:22px; height:22px; padding:0 5px; border-radius:6px; font-size:11px; font-weight:800; }
        .res.ok { background:rgba(16,185,129,.12); color:#0b8f64; }
        .res.bad { background:rgba(239,68,68,.12); color:#c42b2b; }
        .res.none { background:#eef1f8; color:#8a96b2; }
        .th-dia { display:block; font-style:normal; font-weight:700; letter-spacing:0; text-transform:none; }
        .row-card.editor.is-bad { border-color:rgba(239,68,68,.45); background:#fffafa; box-shadow:0 0 0 3px rgba(239,68,68,.08); }
    </style>
@endpush

@section('content')
@php
    $isEdit = (bool) $report;
    $hm = fn ($t) => $t ? substr($t, 0, 5) : '';

    $checksInit = old('checks', $isEdit
        ? $report->checks->map(fn ($c) => [
            'metal_detector_no' => $c->metal_detector_no,
            'check_time'        => $hm($c->check_time),
            'fe_result'         => $c->fe_result,
            'nonfe_result'      => $c->nonfe_result,
            'sus316_result'     => $c->sus316_result,
            'conclusion'        => $c->conclusion,
            'remark'            => $c->remark,
        ])->values()->all()
        : []);

    $verifInit = old('verifications', $isEdit
        ? $report->verifications->map(function ($v) use ($hm) {
            return array_merge([
                'check_time'        => $hm($v->check_time),
                'metal_detector_no' => $v->metal_detector_no,
                'product_id'        => $v->product_id,
                'product_name'      => $v->product_name,
                'product_code'      => $v->product_code,
            ], collect(\App\Models\CutUpMdVerification::RESULT_COLUMNS)->mapWithKeys(fn ($c) => [$c => $v->{$c}])->all());
        })->values()->all()
        : []);

    $val = fn ($key, $fallback = null) => old($key, $fallback);
    $dia = fn ($col) => old($col, $isEdit ? $report->{$col} : ($defaults->{$col} ?? null));
@endphp

<div class="cu-wrap">

    <div class="cu-head">
        <div>
            <div class="cu-crumb">Form · Cut Up · Pemeriksaan Metal Detector</div>
            <h1 class="cu-title">{{ $isEdit ? 'Edit Laporan' : 'Form Baru' }}</h1>
            <div class="cu-sub">Isi header, lalu catat pemeriksaan &amp; verifikasi sensitivitas secara bergantian sepanjang shift.</div>
        </div>
        <a href="{{ route('cut-up.metal-detector.index') }}" class="cu-btn">← Kembali</a>
    </div>

    <form method="POST" id="mdForm"
          action="{{ $isEdit ? route('cut-up.metal-detector.update', $report) : route('cut-up.metal-detector.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- ================= 1. HEADER ================= --}}
        <div class="panel">
            <div class="panel-header"><div class="panel-title"><span class="cu-step">1</span>Informasi Laporan</div></div>
            <div class="panel-body">
                <div class="cu-grid c4">
                    <div>
                        <label class="cu-label" for="report_date">Tanggal</label>
                        <input type="date" id="report_date" name="report_date" class="cu-input"
                               value="{{ $val('report_date', $isEdit ? $report->report_date->toDateString() : now()->toDateString()) }}" required>
                    </div>
                    <div>
                        <label class="cu-label" for="day_name">Hari</label>
                        <input type="text" id="day_name" class="cu-input" readonly tabindex="-1">
                    </div>
                    <div>
                        <label class="cu-label">Lokasi</label>
                        @php $loc = $val('location', $isEdit ? $report->location : null); @endphp
                        <div class="seg block">
                            <label class="seg-opt"><input type="radio" name="location" value="SH01" @checked($loc === 'SH01') required><span>SH01</span></label>
                            <label class="seg-opt"><input type="radio" name="location" value="SH02" @checked($loc === 'SH02')><span>SH02</span></label>
                        </div>
                    </div>
                    <div>
                        <label class="cu-label">Shift</label>
                        @php $sh = $val('shift', $isEdit ? $report->shift : null); @endphp
                        <div class="seg block">
                            <label class="seg-opt"><input type="radio" name="shift" value="Pagi" @checked($sh === 'Pagi') required><span>Pagi</span></label>
                            <label class="seg-opt"><input type="radio" name="shift" value="Malam" @checked($sh === 'Malam')><span>Malam</span></label>
                        </div>
                    </div>
                </div>

                <div class="cu-grid c3" style="margin-top:14px;">
                    <div>
                        <label class="cu-label" for="start_time">Jam Mulai</label>
                        <input type="time" id="start_time" name="start_time" class="cu-input" value="{{ $val('start_time', $isEdit ? $hm($report->start_time) : null) }}">
                    </div>
                    <div>
                        <label class="cu-label" for="break_time">Jam Istirahat</label>
                        <input type="time" id="break_time" name="break_time" class="cu-input" value="{{ $val('break_time', $isEdit ? $hm($report->break_time) : null) }}">
                    </div>
                    <div>
                        <label class="cu-label" for="end_time">Jam Pulang</label>
                        <input type="time" id="end_time" name="end_time" class="cu-input" value="{{ $val('end_time', $isEdit ? $hm($report->end_time) : null) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= DIAMETER SPESIMEN ================= --}}
        <div class="panel">
            <div class="panel-header"><div class="panel-title"><span class="cu-step">2</span>Diameter Spesimen</div></div>
            <div class="panel-body">
                <div class="cu-grid c3">
                    <div>
                        <label class="cu-label" for="fe_diameter">Fe — Ø</label>
                        <div class="cu-unit"><input type="number" step="0.01" min="0" id="fe_diameter" name="fe_diameter" class="cu-input cu-mono" value="{{ $dia('fe_diameter') }}" required></div>
                    </div>
                    <div>
                        <label class="cu-label" for="nonfe_diameter">Non Fe — Ø</label>
                        <div class="cu-unit"><input type="number" step="0.01" min="0" id="nonfe_diameter" name="nonfe_diameter" class="cu-input cu-mono" value="{{ $dia('nonfe_diameter') }}" required></div>
                    </div>
                    <div>
                        <label class="cu-label" for="sus316_diameter">SUS 316 — Ø</label>
                        <div class="cu-unit"><input type="number" step="0.01" min="0" id="sus316_diameter" name="sus316_diameter" class="cu-input cu-mono" value="{{ $dia('sus316_diameter') }}" required></div>
                    </div>
                </div>
                <div class="cu-hint">Diameter ini dipakai di seluruh form (pemeriksaan maupun verifikasi).</div>
            </div>
        </div>

        {{-- ================= 3. PEMERIKSAAN SENSITIVITAS ================= --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="cu-step">3</span>Pemeriksaan Sensitivitas</div>
                <button type="button" class="cu-btn sm primary" onclick="openEditor('check')">+ Tambah Pemeriksaan</button>
            </div>
            <div class="panel-body">
                {{-- Form isian: tersembunyi sampai "Tambah Pemeriksaan" / "Edit" diklik --}}
                <div class="row-card editor" id="checkEditor" data-kind="check" hidden></div>

                <div class="rec-wrap" id="checkTableWrap" hidden>
                    <table class="table rec-table">
                        <thead>
                            <tr>
                                <th>#</th><th>Pukul</th><th>No. MD</th>
                                <th>Fe<em class="th-dia" data-dia="fe"></em></th>
                                <th>Non Fe<em class="th-dia" data-dia="nonfe"></em></th>
                                <th>SUS 316<em class="th-dia" data-dia="sus316"></em></th>
                                <th>Kesimpulan</th><th>Keterangan</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="checkBody"></tbody>
                    </table>
                </div>
                <div class="row-empty" id="checkEmpty">Belum ada pemeriksaan. Klik "Tambah Pemeriksaan" setiap metal detector diperiksa.</div>
                <div id="checkHidden"></div>
            </div>
        </div>

        {{-- ================= 4. VERIFIKASI SENSITIVITAS ================= --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="cu-step">4</span>Verifikasi Sensitivitas</div>
                <button type="button" class="cu-btn sm primary" onclick="openEditor('verif')">+ Tambah Verifikasi</button>
            </div>
            <div class="panel-body">
                {{-- Form isian: tersembunyi sampai "Tambah Verifikasi" / "Edit" diklik --}}
                <div class="row-card editor" id="verifEditor" data-kind="verif" hidden></div>

                <div class="rec-wrap" id="verifTableWrap" hidden>
                    <table class="table rec-table">
                        <thead>
                            <tr>
                                <th>#</th><th>Pukul</th><th>No. MD</th><th>Nama Produk</th><th>Kode Produk</th>
                                <th>Fe<em class="th-dia" data-dia="fe"></em></th>
                                <th>Non Fe<em class="th-dia" data-dia="nonfe"></em></th>
                                <th>SUS 316<em class="th-dia" data-dia="sus316"></em></th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="verifBody"></tbody>
                    </table>
                </div>
                <div class="row-empty" id="verifEmpty">
                    Belum ada verifikasi produk. Lanjutkan setelah metal detector dinyatakan dapat digunakan.
                </div>
                <div class="cu-hint" id="verifHint" hidden>
                    D = Depan · T = Tengah · B = Belakang (hijau = OK, merah = NOT OK).
                </div>
                <div class="cu-hint" style="margin-top:10px;">
                    Produk tidak ditemukan? Tambahkan dulu di
                    <a href="{{ route('cut-up.products.index') }}" target="_blank" rel="noopener" style="color:var(--accent); font-weight:700;">Daftar Produk</a>,
                    lalu muat ulang halaman ini (isi form yang belum disimpan akan hilang).
                </div>
                <div id="verifHidden"></div>
            </div>
        </div>

        {{-- ================= 5. PENGESAHAN ================= --}}
        <div class="panel">
            <div class="panel-header"><div class="panel-title"><span class="cu-step">5</span>Pengesahan</div></div>
            <div class="panel-body">
                <div class="cu-grid c3">
                    <div>
                        <label class="cu-label" for="made_by">Dibuat oleh (Operator MD)</label>
                        <input type="text" id="made_by" name="made_by" maxlength="100" class="cu-input" value="{{ $val('made_by', $isEdit ? $report->made_by : null) }}" placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label class="cu-label" for="approved_by">Disetujui oleh (SPV / Foreman / lady Produksi)</label>
                        <input type="text" id="approved_by" name="approved_by" maxlength="100" class="cu-input" value="{{ $val('approved_by', $isEdit ? $report->approved_by : null) }}" placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label class="cu-label" for="known_by">Diketahui oleh (Foreman / lady QC)</label>
                        <input type="text" id="known_by" name="known_by" maxlength="100" class="cu-input" value="{{ $val('known_by', $isEdit ? $report->known_by : null) }}" placeholder="Nama lengkap">
                    </div>
                </div>
                <div class="cu-hint">Boleh dikosongkan — kolom nama di PDF akan tetap kosong.</div>
            </div>
        </div>

        <div class="cu-actionbar">
            <a href="{{ route('cut-up.metal-detector.index') }}" class="cu-btn">Batal</a>
            <button type="submit" class="cu-btn primary" id="saveBtn">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const PRODUCTS   = @json($products);
    const CHECK_INIT = Object.values(@json($checksInit) || {});
    const VERIF_INIT = Object.values(@json($verifInit) || {});

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const nowHM = () => new Date().toTimeString().slice(0, 5);

    const RES  = [['OK', 'OK', 'ok'], ['NOT OK', 'NOT OK', 'bad']];
    const SPEC = [['fe', 'Fe'], ['nonfe', 'Non Fe'], ['sus316', 'SUS 316']];
    const POS  = [['d', 'Depan'], ['t', 'Tengah'], ['b', 'Belakang']];
    const VERIF_RESULTS = SPEC.flatMap(([k]) => POS.map(([p]) => `${k}_${p}`));

    // Kontrol di editor tidak terhubung ke form utama -> tidak ikut terkirim & tidak memblokir submit
    const NF = 'form="__editor"';

    const KIND = {
        check: {
            label: 'Pemeriksaan', prefix: 'checks',
            root: 'checkEditor', body: 'checkBody', wrap: 'checkTableWrap', empty: 'checkEmpty', hidden: 'checkHidden',
            fields: ['metal_detector_no', 'check_time', 'fe_result', 'nonfe_result', 'sus316_result', 'conclusion', 'remark'],
        },
        verif: {
            label: 'Verifikasi', prefix: 'verifications',
            root: 'verifEditor', body: 'verifBody', wrap: 'verifTableWrap', empty: 'verifEmpty', hint: 'verifHint', hidden: 'verifHidden',
            fields: ['check_time', 'metal_detector_no', 'product_id', 'product_code', ...VERIF_RESULTS],
        },
    };

    // Data baris yang sudah disimpan ke tabel (dikirim lewat input hidden saat form disimpan)
    const state   = { check: [], verif: [] };
    // null = editor tertutup, -1 = baris baru, >= 0 = mengedit baris ke-n
    const editing = { check: null, verif: null };

    /* ---------- Helper markup ---------- */
    function seg(ns, field, opts, extra = '') {
        return `<div class="seg ${extra}">` + opts.map(o =>
            `<label class="seg-opt ${o[2]}"><input type="radio" ${NF} name="${ns}_${field}" value="${esc(o[0])}" data-f="${field}"><span>${o[1]}</span></label>`
        ).join('') + `</div>`;
    }
    function mdOptions() {
        return `<option value="">Pilih</option>` + ['01', '02', '03'].map(n => `<option value="${n}">MD ${n}</option>`).join('');
    }
    const footerHtml = () => `
        <div class="row-foot">
            <span class="cu-field-error" data-err hidden></span>
            <button type="button" class="cu-btn sm" data-cancel>Batal</button>
            <button type="button" class="cu-btn sm primary" data-save>Simpan ke Tabel</button>
        </div>`;

    function checkEditorHtml() {
        return `
        <div class="row-top"><span class="row-title" data-title></span></div>
        <div class="cu-grid c2" style="max-width:420px; margin-bottom:14px;">
            <div><label class="cu-label">Pukul</label>
                <input type="time" class="cu-input" ${NF} data-f="check_time"></div>
            <div><label class="cu-label">No. Metal Detector</label>
                <select class="cu-select" ${NF} data-f="metal_detector_no">${mdOptions()}</select></div>
        </div>
        <div class="spec-grid" style="margin-top:0;">
            ${SPEC.map(([k, label]) => `
            <div class="spec-box">
                <div class="spec-title">${label} <em data-dia="${k}"></em></div>
                ${seg('ec', `${k}_result`, RES, 'block sm')}
            </div>`).join('')}
        </div>
        <div class="cu-grid c2" style="margin-top:14px;">
            <div><label class="cu-label">Kesimpulan</label>
                ${seg('ec', 'conclusion', [['USABLE', 'Dapat Digunakan', 'use'], ['NOT_USABLE', 'Tidak Dapat Digunakan', 'nouse']], 'block')}</div>
            <div><label class="cu-label">Keterangan</label>
                <input type="text" class="cu-input" ${NF} data-f="remark" data-remark maxlength="255" placeholder="Diisi jika tidak dapat digunakan"></div>
        </div>
        ${footerHtml()}`;
    }

    function verifEditorHtml() {
        return `
        <div class="row-top">
            <span class="row-title" data-title></span>
            <div class="row-tools"><button type="button" class="cu-btn sm" data-allok>Semua OK</button></div>
        </div>
        <div class="v-top">
            <div><label class="cu-label">Pukul</label>
                <input type="time" class="cu-input" ${NF} data-f="check_time"></div>
            <div><label class="cu-label">No. MD</label>
                <select class="cu-select" ${NF} data-f="metal_detector_no">${mdOptions()}</select></div>
            <div class="v-product"><label class="cu-label">Nama Produk</label>
                <div class="combo">
                    <input type="text" class="cu-input combo-input" ${NF} placeholder="Cari nama produk..." autocomplete="off">
                    <input type="hidden" ${NF} data-f="product_id">
                    <ul class="combo-list"></ul>
                </div>
                <div class="cu-field-error" data-orphan hidden>Produk ini sudah dihapus dari daftar — pilih produk lagi.</div>
            </div>
            <div><label class="cu-label">Kode Produk</label>
                <input type="text" class="cu-input code" ${NF} data-f="product_code" data-code maxlength="10" placeholder="10 karakter" autocomplete="off">
                <div class="cu-hint cnt"><span data-cnt>0</span>/10 karakter</div></div>
        </div>
        <div class="spec-grid">
            ${SPEC.map(([k, label]) => `
            <div class="spec-box">
                <div class="spec-title">${label} <em data-dia="${k}"></em></div>
                ${POS.map(([p, pl]) => `
                <div class="spec-line"><b>${pl}</b>${seg('ev', `${k}_${p}`, RES, 'sm')}</div>`).join('')}
            </div>`).join('')}
        </div>
        ${footerHtml()}`;
    }

    /* ---------- Baca / tulis isian editor ---------- */
    function readEditor(root) {
        const d = {};
        root.querySelectorAll('[data-f]').forEach(el => {
            const f = el.dataset.f;
            if (el.type === 'radio') { if (el.checked) d[f] = el.value; else if (!(f in d)) d[f] = ''; }
            else d[f] = el.value;
        });
        if ('product_code' in d) d.product_code = d.product_code.toUpperCase();
        return d;
    }

    function productName(d) {
        return d.product_name || (PRODUCTS.find(p => String(p.id) === String(d.product_id))?.name ?? '');
    }

    function updateCnt(root) {
        const code = root.querySelector('[data-code]');
        if (!code) return;
        const cnt = root.querySelector('.cnt');
        cnt.querySelector('[data-cnt]').textContent = code.value.length;
        cnt.classList.toggle('full', code.value.length === 10);
    }

    function writeEditor(root, d) {
        root.querySelectorAll('[data-f]').forEach(el => {
            const v = d[el.dataset.f] ?? '';
            if (el.type === 'radio') el.checked = el.value === v;
            else el.value = v;
        });
        const ci = root.querySelector('.combo-input');
        if (ci) {
            ci.value = productName(d);
            const orphan = !d.product_id && !!ci.value;
            ci.classList.toggle('is-error', orphan);
            root.querySelector('[data-orphan]').hidden = !orphan;
        }
        updateCnt(root);
    }

    function syncConclusion(root) {
        const sel = root.querySelector('[data-f="conclusion"]:checked');
        if (!sel) return;
        const bad = sel.value === 'NOT_USABLE';
        root.classList.toggle('is-bad', bad);
        root.querySelector('[data-remark]').placeholder = bad ? 'Wajib diisi — mis. Mati lampu / perbaikan' : 'Diisi jika tidak dapat digunakan';
    }

    function setErr(root, msg) {
        const el = root.querySelector('[data-err]');
        el.textContent = msg;
        el.hidden = !msg;
    }

    /* ---------- Hari otomatis & label diameter ---------- */
    function updateDay() {
        const v = $('report_date').value;
        $('day_name').value = v ? new Date(v + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'long' }) : '';
    }
    function updateDia() {
        SPEC.forEach(([k]) => {
            const v = $(k + '_diameter').value;
            document.querySelectorAll(`[data-dia="${k}"]`).forEach(el => el.textContent = 'Ø ' + (v || '—') + ' mm');
        });
    }

    function lastMd() {
        const last = state.verif[state.verif.length - 1] || state.check[state.check.length - 1];
        return last ? last.metal_detector_no : '';
    }

    /* ---------- Editor: buka / simpan / tutup ---------- */
    function openEditor(kind, idx = -1) {
        const cfg = KIND[kind], root = $(cfg.root);
        const d = idx < 0 ? { check_time: nowHM(), metal_detector_no: lastMd() } : state[kind][idx];

        root.classList.remove('is-bad');
        writeEditor(root, d);
        if (kind === 'check') syncConclusion(root);
        root.querySelector('[data-title]').textContent = idx < 0 ? `${cfg.label} Baru` : `Edit ${cfg.label}`;
        root.querySelector('[data-save]').textContent = idx < 0 ? 'Simpan ke Tabel' : 'Perbarui';
        setErr(root, '');

        editing[kind] = idx;
        root.hidden = false;
        render(kind);
        updateDia();
        root.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        const first = (kind === 'verif' && idx < 0) ? root.querySelector('.combo-input') : root.querySelector('[data-f="check_time"]');
        if (first) first.focus({ preventScroll: true });
    }

    function closeEditor(kind) {
        $(KIND[kind].root).hidden = true;
        editing[kind] = null;
        render(kind);
    }

    function validateCheck(d) {
        const miss = [];
        if (!d.check_time) miss.push('Pukul');
        if (!d.metal_detector_no) miss.push('No. Metal Detector');
        SPEC.forEach(([k, l]) => { if (!d[`${k}_result`]) miss.push(l); });
        if (!d.conclusion) miss.push('Kesimpulan');
        if (miss.length) return 'Lengkapi dulu: ' + miss.join(', ') + '.';
        if (d.conclusion === 'NOT_USABLE' && !d.remark.trim()) return 'Keterangan wajib diisi jika metal detector tidak dapat digunakan.';
        return '';
    }

    function validateVerif(d) {
        const miss = [];
        if (!d.check_time) miss.push('Pukul');
        if (!d.metal_detector_no) miss.push('No. MD');
        if (!d.product_id) miss.push('Nama Produk (pilih dari daftar)');
        if (d.product_code.length !== 10) miss.push('Kode Produk (harus 10 karakter)');
        SPEC.forEach(([k, l]) => { if (POS.some(([p]) => !d[`${k}_${p}`])) miss.push(`${l} (Depan/Tengah/Belakang)`); });
        return miss.length ? 'Lengkapi dulu: ' + miss.join(', ') + '.' : '';
    }

    function saveEditor(kind) {
        const root = $(KIND[kind].root);
        const d = readEditor(root);
        const msg = kind === 'check' ? validateCheck(d) : validateVerif(d);
        if (msg) { setErr(root, msg); return; }

        if (kind === 'verif') d.product_name = productName({ product_id: d.product_id });
        if (editing[kind] < 0) state[kind].push(d);
        else state[kind][editing[kind]] = d;
        closeEditor(kind);
    }

    function removeItem(kind, idx) {
        if (!confirm(`Hapus ${KIND[kind].label.toLowerCase()} ini dari tabel?`)) return;
        state[kind].splice(idx, 1);
        if (editing[kind] === idx) { closeEditor(kind); return; }
        if (editing[kind] !== null && editing[kind] > idx) editing[kind]--;
        render(kind);
    }

    /* ---------- Render tabel + input hidden ---------- */
    const resBadge = (v) => v === 'OK' ? '<span class="cu-badge ok">OK</span>'
        : v === 'NOT OK' ? '<span class="cu-badge bad">NOT OK</span>' : '–';

    function checkRow(d, i, n) {
        const bad = d.conclusion === 'NOT_USABLE';
        return `
        <tr class="${editing.check === i ? 'is-editing' : ''}">
            <td class="cu-mono">${n}</td>
            <td class="cu-mono">${esc(d.check_time)}</td>
            <td>MD ${esc(d.metal_detector_no)}</td>
            <td>${resBadge(d.fe_result)}</td>
            <td>${resBadge(d.nonfe_result)}</td>
            <td>${resBadge(d.sus316_result)}</td>
            <td><span class="cu-badge ${bad ? 'bad' : 'ok'}">${bad ? 'Tidak Dapat Digunakan' : 'Dapat Digunakan'}</span></td>
            <td class="wrap">${esc(d.remark) || '–'}</td>
            <td>${actionsHtml('check', i)}</td>
        </tr>`;
    }

    function verifRow(d, i, n) {
        const name = productName(d);
        const specCell = (k) => `<span class="res-set">` + POS.map(([p, pl]) => {
            const v = d[`${k}_${p}`];
            return `<span class="res ${v === 'OK' ? 'ok' : v === 'NOT OK' ? 'bad' : 'none'}" title="${pl}: ${esc(v || '-')}">${p.toUpperCase()}</span>`;
        }).join('') + `</span>`;
        return `
        <tr class="${editing.verif === i ? 'is-editing' : ''}">
            <td class="cu-mono">${n}</td>
            <td class="cu-mono">${esc(d.check_time)}</td>
            <td>MD ${esc(d.metal_detector_no)}</td>
            <td class="wrap" style="font-weight:700;">${esc(name) || '–'}${d.product_id ? '' : ' <span class="cu-badge bad">Pilih ulang</span>'}</td>
            <td class="cu-mono" style="letter-spacing:.05em;">${esc(d.product_code)}</td>
            <td>${specCell('fe')}</td>
            <td>${specCell('nonfe')}</td>
            <td>${specCell('sus316')}</td>
            <td>${actionsHtml('verif', i)}</td>
        </tr>`;
    }

    const actionsHtml = (kind, i) => `
        <div class="rec-actions">
            <button type="button" class="cu-btn sm" data-edit="${kind}" data-i="${i}">Edit</button>
            <button type="button" class="cu-btn sm danger" data-del="${kind}" data-i="${i}">Hapus</button>
        </div>`;

    function render(kind) {
        const cfg = KIND[kind], list = state[kind];

        // urut sesuai pukul (sama seperti urutan saat disimpan & di PDF)
        const order = list.map((_, i) => i).sort((a, b) => (list[a].check_time || '99:99').localeCompare(list[b].check_time || '99:99'));
        $(cfg.body).innerHTML = order.map((i, pos) => (kind === 'check' ? checkRow : verifRow)(list[i], i, pos + 1)).join('');

        $(cfg.wrap).hidden  = list.length === 0;
        if (cfg.hint) $(cfg.hint).hidden = list.length === 0;
        $(cfg.empty).style.display = list.length ? 'none' : '';

        $(cfg.hidden).innerHTML = list.map((d, i) => cfg.fields.map(f =>
            `<input type="hidden" name="${cfg.prefix}[${i}][${f}]" value="${esc(d[f] ?? '')}">`).join('')).join('');
        updateDia();
    }

    /* ---------- Event ---------- */
    document.addEventListener('click', (e) => {
        const edit = e.target.closest('[data-edit]');
        if (edit) { openEditor(edit.dataset.edit, Number(edit.dataset.i)); return; }

        const del = e.target.closest('[data-del]');
        if (del) { removeItem(del.dataset.del, Number(del.dataset.i)); return; }

        const save = e.target.closest('[data-save]');
        if (save) { saveEditor(save.closest('.row-card').dataset.kind); return; }

        const cancel = e.target.closest('[data-cancel]');
        if (cancel) { closeEditor(cancel.closest('.row-card').dataset.kind); return; }

        const ok = e.target.closest('[data-allok]');
        if (ok) ok.closest('.row-card').querySelectorAll('input[type=radio][value="OK"]').forEach(r => r.checked = true);
    });

    $('checkEditor').addEventListener('change', (e) => {
        if (e.target.matches('[data-f="conclusion"]')) {
            syncConclusion($('checkEditor'));
            if (e.target.value === 'NOT_USABLE') $('checkEditor').querySelector('[data-remark]').focus();
        }
    });

    // Kode produk: huruf besar + counter
    $('verifEditor').addEventListener('input', (e) => {
        if (!e.target.matches('[data-code]')) return;
        e.target.value = e.target.value.toUpperCase();
        updateCnt($('verifEditor'));
    });

    /* ---------- Combobox produk (search dari Daftar Produk) ---------- */
    function renderList(combo, term) {
        const list = combo.querySelector('.combo-list');
        const q = term.trim().toLowerCase();
        const hits = PRODUCTS.filter(p => p.name.toLowerCase().includes(q)).slice(0, 60);
        list.innerHTML = hits.length
            ? hits.map(p => `<li data-id="${p.id}" data-name="${esc(p.name)}">${esc(p.name)}</li>`).join('')
            : `<li class="none">${PRODUCTS.length ? 'Produk tidak ditemukan' : 'Daftar Produk masih kosong'}</li>`;
        list.classList.add('open');
    }
    function closeLists(except = null) {
        document.querySelectorAll('.combo-list.open').forEach(l => { if (l !== except) l.classList.remove('open'); });
    }

    $('verifEditor').addEventListener('focusin', (e) => {
        if (!e.target.matches('.combo-input')) return;
        const combo = e.target.closest('.combo');
        closeLists(combo.querySelector('.combo-list'));
        renderList(combo, e.target.value);
    });
    $('verifEditor').addEventListener('input', (e) => {
        if (!e.target.matches('.combo-input')) return;
        const combo = e.target.closest('.combo');
        combo.querySelector('input[type=hidden]').value = '';   // harus memilih dari daftar
        e.target.classList.add('is-error');
        $('verifEditor').querySelector('[data-orphan]').hidden = true;
        renderList(combo, e.target.value);
    });
    $('verifEditor').addEventListener('mousedown', (e) => {
        const li = e.target.closest('.combo-list li[data-id]');
        if (!li) return;
        e.preventDefault();
        const combo = li.closest('.combo');
        combo.querySelector('input[type=hidden]').value = li.dataset.id;
        const input = combo.querySelector('.combo-input');
        input.value = li.dataset.name;
        input.classList.remove('is-error');
        $('verifEditor').querySelector('[data-orphan]').hidden = true;
        combo.querySelector('.combo-list').classList.remove('open');
        const code = $('verifEditor').querySelector('[data-code]');
        if (code && !code.value) code.focus();
    });
    document.addEventListener('mousedown', (e) => { if (!e.target.closest('.combo')) closeLists(); });

    // true bila event sudah ditangani combobox
    function comboKey(e) {
        const list = e.target.closest('.combo').querySelector('.combo-list');
        const items = [...list.querySelectorAll('li[data-id]')];
        let idx = items.findIndex(li => li.classList.contains('hl'));
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if (!list.classList.contains('open')) renderList(e.target.closest('.combo'), e.target.value);
            items.forEach(li => li.classList.remove('hl'));
            idx = e.key === 'ArrowDown' ? Math.min(idx + 1, items.length - 1) : Math.max(idx - 1, 0);
            if (items[idx]) { items[idx].classList.add('hl'); items[idx].scrollIntoView({ block: 'nearest' }); }
            return true;
        }
        if (e.key === 'Enter') {
            const target = items[idx] || (items.length === 1 ? items[0] : null);
            if (target) { e.preventDefault(); target.dispatchEvent(new MouseEvent('mousedown', { bubbles: true })); return true; }
        }
        if (e.key === 'Escape') { list.classList.remove('open'); return true; }
        return false;
    }

    // Enter di dalam editor = simpan ke tabel (bukan submit seluruh form)
    ['checkEditor', 'verifEditor'].forEach(id => $(id).addEventListener('keydown', (e) => {
        if (e.target.matches('.combo-input') && comboKey(e)) return;
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            e.preventDefault();
            saveEditor($(id).dataset.kind);
        }
    }));

    /* ---------- Init ---------- */
    $('checkEditor').innerHTML = checkEditorHtml();
    $('verifEditor').innerHTML = verifEditorHtml();

    const pick = (d, fields) => Object.fromEntries(fields.map(f => [f, d[f] ?? '']));
    CHECK_INIT.forEach(d => state.check.push(pick(d, KIND.check.fields)));
    VERIF_INIT.forEach(d => {
        const o = pick(d, KIND.verif.fields);
        o.product_code = String(o.product_code).toUpperCase();
        o.product_name = d.product_name || (PRODUCTS.find(p => String(p.id) === String(o.product_id))?.name ?? '');
        state.verif.push(o);
    });

    $('report_date').addEventListener('change', updateDay);
    SPEC.forEach(([k]) => $(k + '_diameter').addEventListener('input', updateDia));
    updateDay(); render('check'); render('verif');

    // Pengaman sebelum menyimpan laporan
    $('mdForm').addEventListener('submit', (e) => {
        const orphan = state.verif.findIndex(d => !d.product_id);
        if (orphan > -1) {
            e.preventDefault();
            alert('Ada verifikasi dengan produk yang sudah dihapus dari daftar. Pilih produk lagi.');
            openEditor('verif', orphan);
            return;
        }
        if ((editing.check !== null || editing.verif !== null) &&
            !confirm('Masih ada isian pemeriksaan/verifikasi yang belum disimpan ke tabel dan tidak akan ikut tersimpan. Lanjutkan?')) {
            e.preventDefault();
            return;
        }
        $('saveBtn').disabled = true; $('saveBtn').textContent = 'Menyimpan...';   // cegah submit ganda
    });
</script>
@endpush