@extends('layouts.app')

@section('content')
@php
    use App\Models\DefeatReportItem as DI;

    $editing = (bool) $item;
    $backUrl = route('defeathering.efficiency.index', [
        'date' => $report->report_date->toDateString(),
    ]) . '#report-' . $report->id;

    // nilai awal input: old() > data tersimpan; desimal tampil dengan koma, mm.ss apa adanya
    $val = function (string $key) use ($item) {
        $v = old($key, $item?->getAttribute($key));
        if ($v === null || $v === '') {
            return '';
        }

        return DI::FIELDS[$key]['kind'] === 'time' ? (string) $v : str_replace('.', ',', (string) $v);
    };

    // satuan yang ditampilkan di dalam kolom (label yang sudah memuat satuan tidak diulang)
    $unit = fn (string $key, array $f): ?string => match (true) {
        $f['kind'] === 'time' => 'mm.ss',
        $key === 'amp' => 'A',
        $key === 'volt' => 'V',
        in_array($key, ['scalder_fallen', 'sampling_count'], true), str_ends_with($key, '_ok') => 'ekor',
        default => null,
    };

    $placeholders = [
        'speed_live_bird' => '5500', 'speed_evis_c' => '5500',
        'bleeding_time' => '01.16', 'scalder1_time' => '02.15', 'scalder2_time' => '02.15', 'scalder3_time' => '02.15',
        'scalder_fallen' => '0',
    ];

    // judul bagian di atas kelompok tertentu
    $bands = [
        'Speed Line' => ['Parameter proses', null],
        'Jumlah Sampling' => ['Pemeriksaan kualitas', 'Persentase OK dihitung dari Jumlah Sampling.'],
        'ATP' => ['Lainnya', null],
    ];

    // semua kelompok input di sisi kanan (AVG DTA ada di panel kiri; % OK dihitung sistem)
    $groups = collect(DI::FIELDS)
        ->except('avg_dta')
        ->reject(fn ($f) => ! empty($f['calc']))
        ->groupBy('group', true);

    $samplingValue = old('sampling_count', $item?->sampling_count);
    $selected = old('monitor_control_id', $editing ? $item->monitor_control_id : '');
@endphp

<style>
    .df-wrap {
        --ink:#0d3b6e; --line:#e3e8f1; --soft:#f6f8fc; --field:#d5dce9;
        --ok:#047857; --ok-bar:#34d399; --bad:#b91c1c; --bad-bar:#f87171; --warn:#b45309;
        font-family:'Inter',sans-serif;
    }
    .df-wrap *, .df-wrap *::before, .df-wrap *::after { box-sizing:border-box; }
    .df-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 18px; border-radius:10px; font-size:13.5px; font-weight:600; border:1px solid var(--card-border); background:#fff; color:var(--text-main); cursor:pointer; text-decoration:none; transition:background-color .15s, border-color .15s; }
    .df-btn:hover { background:#f8fafc; border-color:#cbd3e3; }
    .df-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .df-btn.primary:hover { background:#d44f24; border-color:#d44f24; }
    .df-btn:disabled { opacity:.6; cursor:not-allowed; }
    .df-btn:focus-visible { outline:2px solid var(--accent); outline-offset:2px; }

    .ctx { display:flex; gap:28px; flex-wrap:wrap; margin-bottom:18px; }
    .ctx .lbl { font-size:12px; color:var(--text-muted); }
    .ctx .val { font-size:15px; font-weight:700; margin-top:1px; color:var(--text-main); }
    .alert-err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:10px 14px; font-size:13px; margin-bottom:14px; }
    .alert-err ul { margin:0; padding-left:18px; }
    .hint { font-size:12.5px; margin:0 0 14px; color:var(--text-muted); min-height:18px; }
    .hint.warn { color:var(--warn); }

    .ff-layout { display:grid; grid-template-columns:320px minmax(0,1fr); gap:36px; align-items:start; }
    @media (max-width:960px){ .ff-layout { grid-template-columns:1fr; gap:22px; } }

    /* panel kiri: identitas farm */
    .rail { position:sticky; top:16px; border:1px solid var(--line); border-radius:16px; background:var(--soft); padding:22px 20px; }
    @media (max-width:960px){ .rail { position:static; } }
    .rail-blk { margin-bottom:20px; }
    .rail-lbl { display:block; font-size:12.5px; font-weight:600; color:var(--text-muted); margin-bottom:6px; }
    .rail-big { font-size:24px; font-weight:800; color:var(--ink); line-height:1.2; min-height:30px; letter-spacing:-.01em; }
    .ff-sel { width:100%; height:44px; padding:0 12px; border:1px solid var(--field); border-radius:10px; font-family:inherit; font-size:14px; background:#fff; color:var(--text-main); }
    .ff-sel:disabled { background:#eef1f7; color:#374151; }
    .manual-time { display:none; gap:8px; align-items:center; margin-top:10px; }
    .manual-time.on { display:flex; }
    .manual-time input { height:40px; padding:0 10px; border:1px solid var(--field); border-radius:10px; font-family:inherit; font-size:14px; width:100%; background:#fff; }
    .manual-note { font-size:12px; color:var(--warn); margin-top:8px; display:none; }
    .manual-note.on { display:block; }
    .rail-actions { display:flex; gap:10px; padding-top:18px; border-top:1px solid var(--line); }
    .rail-actions .df-btn { flex:1; }

    /* kolom input */
    .inp { position:relative; }
    .inp input, .rail .inp input { width:100%; height:44px; padding:0 12px; border:1px solid var(--field); border-radius:10px; font-family:inherit; font-size:14.5px; font-variant-numeric:tabular-nums; background:#fff; color:var(--text-main); transition:border-color .15s, box-shadow .15s; }
    .inp.has-unit input { padding-right:56px; }
    .inp .unit { position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:12px; color:var(--text-muted); pointer-events:none; }
    .inp input:focus, .ff-sel:focus, .manual-time input:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(232,93,47,.14); }
    .inp input:disabled { background:#f1f4f9; color:#94a3b8; cursor:not-allowed; }
    .inp input.is-invalid { border-color:#ef4444; background:#fef2f2; }
    .inp input.clamped { border-color:#ef4444; background:#fef2f2; }
    .inp input::placeholder { color:#b6bfce; }
    .fld label { display:block; font-size:13px; color:var(--text-muted); margin-bottom:6px; }
    .rail .inp input { font-size:22px; font-weight:800; color:var(--ink); }

    /* panel kanan: kelompok parameter */
    .band { margin:34px 0 4px; }
    .band:first-child { margin-top:0; }
    .band h3 { font-size:17px; font-weight:800; color:var(--text-main); letter-spacing:-.01em; margin:0; }
    .band p { font-size:13px; color:var(--text-muted); margin:3px 0 0; }
    .fgroup { display:grid; grid-template-columns:180px minmax(0,1fr); gap:12px 28px; padding:20px 0; border-bottom:1px solid var(--line); }
    .band + .fgroup { border-top:1px solid var(--line); margin-top:12px; }
    @media (max-width:720px){ .fgroup { grid-template-columns:1fr; } }
    .fg-name { font-size:15px; font-weight:700; color:var(--ink); padding-top:2px; }
    .fg-sub { font-size:12.5px; font-weight:400; color:var(--text-muted); margin-top:3px; line-height:1.45; }
    .fg-fields { display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:16px; }
    .fg-fields > *, .qc-grid > *, .fgroup > * { min-width:0; }

    /* hasil pemeriksaan kualitas */
    .qc-grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr) minmax(0,1.15fr); gap:16px; align-items:start; }
    @media (max-width:720px){ .qc-grid { grid-template-columns:1fr 1fr; } .qc-res { grid-column:1 / -1; } }
    .qc-ok label { color:var(--ok); font-weight:600; }
    .qc-nok label { color:var(--bad); font-weight:600; }
    .qc-res { padding-left:4px; }
    .qc-res .r-lbl { font-size:13px; color:var(--text-muted); margin-bottom:2px; }
    .qc-pct { font-size:30px; font-weight:800; color:var(--ink); line-height:1.1; letter-spacing:-.02em; font-variant-numeric:tabular-nums; }
    .qc-pct small { font-size:15px; font-weight:700; margin-left:2px; }
    .qc-pct.empty { color:#b6bfce; }
    .bar { display:flex; height:8px; border-radius:99px; background:#e8ecf4; overflow:hidden; margin-top:10px; }
    .bar .seg { height:100%; width:0; transition:width .25s ease; }
    .bar .seg.ok { background:var(--ok-bar); }
    .bar .seg.nok { background:var(--bad-bar); }
    .qc-cap { grid-column:1 / -1; font-size:12.5px; color:var(--text-muted); min-height:18px; }
    .qc-cap.warn { color:var(--warn); }
    .qc-cap.err { color:var(--bad); font-weight:600; }
    .qc-cap.good { color:var(--ok); }
    .qc-lock { font-size:12.5px; color:var(--warn); margin-top:8px; }
    .qc-lock[hidden] { display:none; }
    @media (prefers-reduced-motion: reduce){ .bar .seg { transition:none; } }
</style>

<form id="farmForm" method="POST" autocomplete="off"
      action="{{ $editing ? route('defeathering.efficiency.items.update', [$report, $item]) : route('defeathering.efficiency.items.store', $report) }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="panel df-wrap">
        <div class="panel-header">
            <div class="panel-title">{{ $editing ? 'Ubah' : 'Tambah' }} farm &mdash; Efficiency Defeathering</div>
            <a href="{{ $backUrl }}" class="df-btn">Kembali</a>
        </div>

        <div class="panel-body">
            <div class="ctx">
                <div><div class="lbl">Tanggal operasional</div><div class="val">{{ $report->report_date->format('d/m/Y') }}</div></div>
                <div><div class="lbl">Shift</div><div class="val">{{ ucfirst($report->shift) }}</div></div>
                <div><div class="lbl">Lokasi</div><div class="val">{{ $report->location }}</div></div>
            </div>

            @if($errors->any())
                <div class="alert-err"><ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
            @endif

            <p class="hint" id="hint"></p>

            <div class="ff-layout">
                {{-- ============ PANEL KIRI: IDENTITAS FARM ============ --}}
                <aside class="rail">
                    <div class="rail-blk">
                        <label class="rail-lbl" for="farmSel">Nama farm</label>
                        @if($editing)
                            <select class="ff-sel" id="farmSel" disabled>
                                @foreach($farms as $o)
                                    <option value="{{ $o['id'] }}" selected>{{ $o['label'] }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="ff-sel" id="farmSel" name="monitor_control_id" required>
                                <option value="">Pilih farm</option>
                                @foreach($farms as $o)
                                    <option value="{{ $o['id'] }}" @selected((string) $selected === (string) $o['id'])>{{ $o['label'] }}</option>
                                @endforeach
                            </select>
                            @if(empty($farms))
                                <div class="manual-note on">Tidak ada farm berstatus Selesai (Kontrol Monitor) yang belum diisi pada tanggal, shift, dan lokasi ini.</div>
                            @endif
                        @endif
                    </div>

                    <div class="rail-blk">
                        <div class="rail-lbl">Size ayam</div>
                        <div class="rail-big" id="sizeText">–</div>
                    </div>

                    <div class="rail-blk">
                        <div class="rail-lbl">Waktu</div>
                        <div class="rail-big" id="timeText">–</div>
                        <div class="manual-time" id="manualTime">
                            <input type="time" name="time_start" id="time_start" value="{{ old('time_start', $item?->time_start) }}" disabled>
                            <span>–</span>
                            <input type="time" name="time_end" id="time_end" value="{{ old('time_end', $item?->time_end) }}" disabled>
                        </div>
                        <div class="manual-note" id="manualNote">Waktu hanging belum tersedia pada Form Hanging farm ini. Isi manual.</div>
                    </div>

                    <div class="rail-blk">
                        <label class="rail-lbl" for="f_avg_dta">Average DTA</label>
                        <div class="inp has-unit">
                            <input type="text" id="f_avg_dta" name="avg_dta" class="num dec{{ $errors->has('avg_dta') ? ' is-invalid' : '' }}" inputmode="decimal"
                                   data-key="avg_dta" data-max="{{ DI::FIELDS['avg_dta']['max'] }}" value="{{ $val('avg_dta') }}" required>
                            <span class="unit">kg</span>
                        </div>
                    </div>

                    <div class="rail-actions">
                        <button type="submit" class="df-btn primary" id="submitBtn">Simpan</button>
                        <a href="{{ $backUrl }}" class="df-btn">Batal</a>
                    </div>
                </aside>

                {{-- ============ PANEL KANAN: PARAMETER ============ --}}
                <section>
                    @foreach($groups as $group => $cols)
                        @if(isset($bands[$group]))
                            <div class="band">
                                <h3>{{ $bands[$group][0] }}</h3>
                                @if($bands[$group][1])<p>{{ $bands[$group][1] }}</p>@endif
                            </div>
                        @endif

                        @if($group === 'Jumlah Sampling')
                            <div class="fgroup">
                                <div>
                                    <div class="fg-name">Jumlah sampling</div>
                                    <div class="fg-sub">Dasar perhitungan % OK.</div>
                                </div>
                                <div class="fg-fields">
                                    @include('report.defeat.partials.field', [
                                        'key' => 'sampling_count', 'f' => $cols['sampling_count'], 'label' => 'Jumlah ayam yang disampling',
                                        'value' => $val('sampling_count'), 'unit' => 'ekor', 'invalid' => $errors->has('sampling_count'),
                                    ])
                                </div>
                            </div>
                            <div class="qc-lock" id="qcLock" @if($samplingValue) hidden @endif>Isi Jumlah Sampling terlebih dahulu untuk membuka kolom OK dan Not OK.</div>

                        @elseif(in_array($group, DI::QC, true))
                            @php $qk = array_search($group, DI::QC, true); @endphp
                            <div class="fgroup">
                                <div>
                                    <div class="fg-name">{{ $group }}</div>
                                    <div class="fg-sub">OK ÷ Jumlah Sampling</div>
                                </div>
                                <div class="qc-grid">
                                    <div class="qc-ok">
                                        @include('report.defeat.partials.field', [
                                            'key' => $qk . '_ok', 'f' => $cols[$qk . '_ok'], 'label' => 'OK',
                                            'value' => $val($qk . '_ok'), 'unit' => 'ekor', 'qc' => true,
                                            'invalid' => $errors->has($qk . '_ok'), 'disabled' => ! $samplingValue,
                                        ])
                                    </div>
                                    <div class="qc-nok">
                                        @include('report.defeat.partials.field', [
                                            'key' => $qk . '_not_ok', 'f' => $cols[$qk . '_not_ok'], 'label' => 'Not OK',
                                            'value' => $val($qk . '_not_ok'), 'unit' => 'ekor', 'qc' => true,
                                            'invalid' => $errors->has($qk . '_not_ok'), 'disabled' => ! $samplingValue,
                                        ])
                                    </div>
                                    <div class="qc-res">
                                        <div class="r-lbl">% OK</div>
                                        <div class="qc-pct empty" id="pct_{{ $qk }}">–</div>
                                        <div class="bar" aria-hidden="true"><span class="seg ok" id="bar_ok_{{ $qk }}"></span><span class="seg nok" id="bar_nok_{{ $qk }}"></span></div>
                                    </div>
                                    <div class="qc-cap" id="cap_{{ $qk }}" aria-live="polite"></div>
                                </div>
                            </div>

                        @else
                            <div class="fgroup">
                                <div class="fg-name">{{ $group }}</div>
                                <div class="fg-fields">
                                    @foreach($cols as $key => $f)
                                        @include('report.defeat.partials.field', [
                                            'key' => $key, 'f' => $f, 'value' => $val($key), 'unit' => $unit($key, $f),
                                            'label' => $key === 'scalder_fallen' ? 'Jumlah ayam jatuh' : null,
                                            'placeholder' => $placeholders[$key] ?? '', 'invalid' => $errors->has($key),
                                        ])
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </section>
            </div>
        </div>
    </div>
</form>

<script>
    window.DEFEAT = {
        editing: @json($editing),
        farms: @json($farms),
        qc: @json(array_keys(DI::QC)),
        hasOld: @json(old('monitor_control_id') !== null || $errors->any()),
    };
</script>

@verbatim
<script>
(function () {
    const D = window.DEFEAT;
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
    const int = v => { const n = parseInt(String(v).replace(/[^0-9]/g, ''), 10); return isNaN(n) ? null : n; };
    const id = n => n.toLocaleString('id-ID');
    const pct1 = n => n.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

    const sel = $('#farmSel');
    const sizeText = $('#sizeText');
    const timeText = $('#timeText');
    const manual = $('#manualTime');
    const manualNote = $('#manualNote');
    const hint = $('#hint');
    const sampling = $('#f_sampling_count');
    const qcLock = $('#qcLock');
    const qcInputs = $$('.qc-in');
    const histInputs = $$('input[data-hist]');

    /* ---------- tandai isian yang diubah user (tidak ditimpa riwayat) ---------- */
    document.addEventListener('input', e => {
        if (e.target.matches('input[data-hist]')) e.target.dataset.touched = '1';
    });

    function findOption() {
        if (D.editing) return D.farms[0] || null; // mode ubah: hanya 1 opsi (farm yang sedang diubah)
        return D.farms.find(f => String(f.id) === String(sel.value)) || null;
    }

    /* ---------- Size & Waktu otomatis ---------- */
    function showAuto(opt) {
        sizeText.textContent = opt ? (opt.size_label || '–') : '–';

        const hasTime = !!(opt && opt.time_start && opt.time_end);
        timeText.textContent = hasTime ? opt.time_start.replace(':', '.') + ' - ' + opt.time_end.replace(':', '.') : '–';

        const needManual = !!opt && !hasTime;
        manual.classList.toggle('on', needManual);
        manualNote.classList.toggle('on', needManual);
        $$('input', manual).forEach(i => i.disabled = !needManual);
    }

    /* ---------- Speed Line & Time dari riwayat farm sebelumnya (tetap editable) ---------- */
    function applyHistory(opt, force) {
        if (D.editing) return;

        const h = opt && opt.history;
        histInputs.forEach(el => {
            if (el.dataset.touched === '1') return;
            if (!force && el.value !== '') return;
            const v = h ? h[el.dataset.key] : null;
            el.value = (v === null || v === undefined) ? '' : (el.dataset.kind === 'dec' ? String(v).replace('.', ',') : String(v));
        });

        hint.className = 'hint';
        if (!opt) {
            hint.textContent = '';
        } else if (h) {
            hint.textContent = 'Speed Line dan Time terisi dari riwayat farm ini (' + h.date + '). Silakan ubah bila berbeda.';
        } else {
            hint.className = 'hint warn';
            hint.textContent = 'Belum ada riwayat untuk farm ini. Isi Speed Line dan Time secara manual.';
        }
    }

    function refresh(force) {
        const opt = findOption();
        showAuto(opt);
        applyHistory(opt, force);
    }

    /* ---------- % OK = OK / Jumlah Sampling, plus pengecekan OK + Not OK ---------- */
    function updateQc() {
        const s = int(sampling.value);
        const has = s !== null && s > 0;

        qcInputs.forEach(i => { i.disabled = !has; });
        if (qcLock) qcLock.hidden = has;

        D.qc.forEach(k => {
            const okIn = $('#f_' + k + '_ok');
            const nokIn = $('#f_' + k + '_not_ok');
            const out = $('#pct_' + k);
            const barOk = $('#bar_ok_' + k);
            const barNok = $('#bar_nok_' + k);
            const cap = $('#cap_' + k);

            const ok = int(okIn.value);
            const nok = int(nokIn.value);
            const used = (ok || 0) + (nok || 0);
            const over = has && used > s;

            // pesan validasi hanya di kolom OK supaya satu pesan saja yang muncul saat Simpan
            okIn.setCustomValidity(over ? 'OK + Not OK (' + id(used) + ') melebihi Jumlah Sampling (' + id(s) + ').' : '');
            nokIn.setCustomValidity('');
            okIn.classList.toggle('is-invalid', over);
            nokIn.classList.toggle('is-invalid', over);

            // persentase OK
            const pct = has && ok !== null ? ok / s * 100 : null;
            out.classList.toggle('empty', pct === null);
            out.innerHTML = pct === null ? '–' : pct1(pct) + '<small>%</small>';

            // bar: OK (hijau), Not OK (merah), sisanya belum terklasifikasi
            const okW = has ? Math.min(ok || 0, s) / s * 100 : 0;
            const nokW = has ? Math.min(nok || 0, s - Math.min(ok || 0, s)) / s * 100 : 0;
            barOk.style.width = okW + '%';
            barNok.style.width = nokW + '%';

            // keterangan
            cap.className = 'qc-cap';
            if (!has) {
                cap.textContent = '';
            } else if (over) {
                cap.classList.add('err');
                cap.textContent = 'OK + Not OK (' + id(used) + ') melebihi Jumlah Sampling (' + id(s) + ').';
            } else if (ok === null && nok === null) {
                cap.textContent = '';
            } else if (used < s) {
                cap.classList.add('warn');
                cap.textContent = 'Belum terklasifikasi: ' + id(s - used) + ' dari ' + id(s) + ' ekor.';
            } else {
                cap.classList.add('good');
                cap.textContent = 'Seluruh ' + id(s) + ' ekor sampel sudah terklasifikasi.';
            }
        });
    }

    if (sel && !D.editing) sel.addEventListener('change', () => refresh(true));

    /* ---------- sanitasi & batas nilai ---------- */
    document.addEventListener('input', e => {
        const el = e.target;
        if (!el.classList || !el.classList.contains('num')) return;

        if (el.classList.contains('int')) el.value = el.value.replace(/[^0-9]/g, '');
        else if (el.classList.contains('mmss')) el.value = el.value.replace(/[^0-9.,:]/g, '');
        else el.value = el.value.replace(/[^0-9.,]/g, '');

        const max = el.dataset.max ? parseFloat(el.dataset.max) : null;
        const n = parseFloat(el.value.replace(',', '.'));
        el.classList.remove('clamped');
        if (!el.classList.contains('mmss') && !isNaN(n) && max !== null && n > max) {
            el.value = String(max).replace('.', ',');
            el.classList.add('clamped');
            el.title = 'Maksimal ' + max;
            setTimeout(() => el.classList.remove('clamped'), 1200);
        }

        if (el === sampling || el.classList.contains('qc-in')) updateQc();
    });

    document.addEventListener('change', e => {
        if (e.target.classList && e.target.classList.contains('mmss')) {
            const m = e.target.value.trim().replace(/[,:]/g, '.').match(/^(\d{1,2})\.(\d{1,2})$/);
            if (m) e.target.value = m[1].padStart(2, '0') + '.' + m[2].padEnd(2, '0');
        }
    });

    $('#farmForm').addEventListener('submit', () => {
        const b = $('#submitBtn');
        b.disabled = true;
        b.textContent = 'Menyimpan…';
    });

    /* ---------- init: setelah gagal validasi / mode ubah, nilai yang sudah ada tidak ditimpa ---------- */
    updateQc();
    refresh(false);
})();
</script>
@endverbatim
@endsection
