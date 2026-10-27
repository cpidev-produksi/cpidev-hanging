@extends('layouts.app')

@section('content')
@php
    $editing = (bool) $item;
    $backUrl = route('evisceration.efficiency.show', $report);
    $selectedFarm = old('monitor_control_id', $item?->monitor_control_id ?? '');

    // nilai awal input: old() > data tersimpan; desimal tampil dengan koma
    $val = function (string $key) use ($item) {
        $v = old($key, $item?->getAttribute($key));

        return ($v === null || $v === '') ? '' : str_replace('.', ',', (string) $v);
    };

    $sprayer = old('sprayer_status', $item?->sprayer_status);
@endphp

<style>
    .df-wrap {
        --ink:#0d3b6e; --line:#e3e8f1; --soft:#f6f8fc; --field:#d5dce9;
        --ok:#047857; --ok-bar:#34d399; --bad:#b91c1c; --warn:#b45309;
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
    .inp input { width:100%; height:44px; padding:0 12px; border:1px solid var(--field); border-radius:10px; font-family:inherit; font-size:14.5px; font-variant-numeric:tabular-nums; background:#fff; color:var(--text-main); transition:border-color .15s, box-shadow .15s; }
    .inp.has-unit input { padding-right:56px; }
    .inp .unit { position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:12px; color:var(--text-muted); pointer-events:none; }
    .inp input:focus, .ff-sel:focus, .manual-time input:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(232,93,47,.14); }
    .inp input.is-invalid, .inp input.clamped { border-color:#ef4444; background:#fef2f2; }
    .inp input::placeholder { color:#b6bfce; }
    .fld label { display:block; font-size:13px; color:var(--text-muted); margin-bottom:6px; }

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

    /* hasil pemeriksaan: persentase dari Jumlah Sampling */
    .qc-grid { display:grid; grid-template-columns:minmax(0,1.1fr) minmax(0,1fr); gap:16px; align-items:start; }
    @media (max-width:720px){ .qc-grid { grid-template-columns:1fr; } }
    .qc-res .r-lbl { font-size:13px; color:var(--text-muted); margin-bottom:2px; }
    .qc-pct { font-size:30px; font-weight:800; color:var(--ink); line-height:1.1; letter-spacing:-.02em; font-variant-numeric:tabular-nums; }
    .qc-pct small { font-size:15px; font-weight:700; margin-left:2px; }
    .qc-pct.empty { color:#b6bfce; }
    .bar { display:flex; height:8px; border-radius:99px; background:#e8ecf4; overflow:hidden; margin-top:10px; }
    .bar .seg { height:100%; width:0; background:var(--ink); transition:width .25s ease; }
    .qc-cap { grid-column:1 / -1; font-size:12.5px; color:var(--text-muted); min-height:18px; }
    .qc-cap.err { color:var(--bad); font-weight:600; }
    @media (prefers-reduced-motion: reduce){ .bar .seg { transition:none; } }

    /* pilihan sprayer */
    .choice { display:inline-flex; padding:4px; border:1px solid var(--field); border-radius:12px; background:#fff; gap:4px; }
    .choice label { position:relative; margin:0; cursor:pointer; }
    .choice input { position:absolute; opacity:0; inset:0; }
    .choice span { display:block; padding:9px 18px; border-radius:9px; font-size:13.5px; font-weight:600; color:var(--text-muted); transition:background-color .15s, color .15s; }
    .choice input:checked + span { background:var(--ink); color:#fff; }
    .choice input:focus-visible + span { outline:2px solid var(--accent); outline-offset:2px; }
    .choice.is-invalid { border-color:#ef4444; background:#fef2f2; }
</style>

<form id="farmForm" method="POST" autocomplete="off"
      action="{{ $editing ? route('evisceration.efficiency.items.update', [$report, $item]) : route('evisceration.efficiency.items.store', $report) }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="panel df-wrap">
        <div class="panel-header">
            <div class="panel-title">{{ $editing ? 'Ubah' : 'Tambah' }} farm &mdash; Efficiency Evisceration</div>
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
                                    <option value="{{ $o['id'] }}" @selected((string) $selectedFarm === (string) $o['id'])>{{ $o['label'] }}</option>
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

                    <div class="rail-actions">
                        <button type="submit" class="df-btn primary" id="submitBtn">Simpan</button>
                        <a href="{{ $backUrl }}" class="df-btn">Batal</a>
                    </div>
                </aside>

                {{-- ============ PANEL KANAN: PARAMETER ============ --}}
                <section>
                    <div class="band">
                        <h3>Data lini</h3>
                        <p>Terisi otomatis dari Efficiency Defeathering Report farm yang sama, dan tetap bisa diubah.</p>
                    </div>
                    <div class="fgroup">
                        <div class="fg-name">Speed line evis</div>
                        <div class="fg-fields">
                            @include('report.evis.effieciency.partials.field', [
                                'key' => 'speed_evis', 'label' => 'Speed Line Evis (BPH)', 'value' => $val('speed_evis'),
                                'max' => 99999, 'autofill' => 'speed_evis', 'invalid' => $errors->has('speed_evis'),
                            ])
                        </div>
                    </div>
                    <div class="fgroup">
                        <div>
                            <div class="fg-name">Jumlah sampling</div>
                            <div class="fg-sub">Dasar perhitungan persentase.</div>
                        </div>
                        <div class="fg-fields">
                            @include('report.evis.effieciency.partials.field', [
                                'key' => 'sampling_count', 'label' => 'Jumlah ayam yang disampling', 'value' => $val('sampling_count'),
                                'max' => 999999, 'unit' => 'ekor', 'autofill' => 'sampling_count', 'invalid' => $errors->has('sampling_count'),
                            ])
                        </div>
                    </div>

                    <div class="band">
                        <h3>Pemeriksaan kualitas</h3>
                        <p>Persentase = jumlah temuan ÷ Jumlah Sampling × 100.</p>
                    </div>
                    @foreach([
                        ['vent', 'vent_not_torn', 'Vent Opening Cutter', 'Vent Tidak Sobek'],
                        ['viscera', 'viscera_not_removed', 'Nouva', 'Jeroan Tidak Terambil'],
                    ] as [$id, $key, $title, $label])
                        <div class="fgroup">
                            <div class="fg-name">{{ $title }}</div>
                            <div class="qc-grid">
                                @include('report.evis.effieciency.partials.field', [
                                    'key' => $key, 'label' => $label, 'value' => $val($key),
                                    'max' => 999999, 'unit' => 'ekor', 'invalid' => $errors->has($key),
                                ])
                                <div class="qc-res">
                                    <div class="r-lbl">Persentase</div>
                                    <div class="qc-pct empty" id="pct_{{ $id }}">–</div>
                                    <div class="bar" aria-hidden="true"><span class="seg" id="bar_{{ $id }}"></span></div>
                                </div>
                                <div class="qc-cap" id="cap_{{ $id }}" data-label="{{ $label }}" aria-live="polite"></div>
                            </div>
                        </div>
                    @endforeach

                    <div class="band">
                        <h3>Inside - Outside Washer</h3>
                    </div>
                    <div class="fgroup">
                        <div class="fg-name">Sprayer</div>
                        <div>
                            <div class="choice{{ $errors->has('sprayer_status') ? ' is-invalid' : '' }}" role="radiogroup" aria-label="Status sprayer">
                                <label><input type="radio" name="sprayer_status" value="operating" @checked($sprayer === 'operating') required><span>Beroperasi</span></label>
                                <label><input type="radio" name="sprayer_status" value="not_operating" @checked($sprayer === 'not_operating') required><span>Tidak beroperasi</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="fgroup">
                        <div class="fg-name">Temuan grit</div>
                        <div class="fg-fields">
                            @include('report.evis.effieciency.partials.field', [
                                'key' => 'grit_findings', 'label' => 'Jumlah temuan', 'value' => $val('grit_findings'),
                                'max' => 999999, 'unit' => 'ekor', 'invalid' => $errors->has('grit_findings'),
                            ])
                        </div>
                    </div>
                    <div class="fgroup">
                        <div class="fg-name">Debit air</div>
                        <div class="fg-fields">
                            @include('report.evis.effieciency.partials.field', [
                                'key' => 'water_flow_start', 'label' => 'Awal', 'value' => $val('water_flow_start'),
                                'kind' => 'dec', 'max' => 99999999, 'invalid' => $errors->has('water_flow_start'),
                            ])
                            @include('report.evis.effieciency.partials.field', [
                                'key' => 'water_flow_end', 'label' => 'Akhir', 'value' => $val('water_flow_end'),
                                'kind' => 'dec', 'max' => 99999999, 'invalid' => $errors->has('water_flow_end'),
                            ])
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</form>

<script>
    window.EVIS = {
        editing: @json($editing),
        farms: @json($farms),
    };
</script>

@verbatim
<script>
(function () {
    const D = window.EVIS;
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
    const int = v => { const n = parseInt(String(v).replace(/[^0-9]/g, ''), 10); return isNaN(n) ? null : n; };
    const id = n => n.toLocaleString('id-ID');
    const pct = n => n.toLocaleString('id-ID', { maximumFractionDigits: 2 });

    const sel = $('#farmSel');
    const sizeText = $('#sizeText');
    const timeText = $('#timeText');
    const manual = $('#manualTime');
    const manualNote = $('#manualNote');
    const hint = $('#hint');
    const sampling = $('#f_sampling_count');
    const autofillInputs = $$('input[data-autofill]');
    const checks = [
        { input: $('#f_vent_not_torn'), out: $('#pct_vent'), bar: $('#bar_vent'), cap: $('#cap_vent') },
        { input: $('#f_viscera_not_removed'), out: $('#pct_viscera'), bar: $('#bar_viscera'), cap: $('#cap_viscera') },
    ];

    /* ---------- tandai isian yang diubah user (tidak ditimpa data Defeathering) ---------- */
    document.addEventListener('input', e => {
        if (e.target.matches('input[data-autofill]')) e.target.dataset.touched = '1';
    });

    function findOption() {
        if (D.editing) return D.farms[0] || null; // mode ubah: hanya 1 opsi (farm yang sedang diubah)
        return D.farms.find(f => String(f.id) === String(sel.value)) || null;
    }

    /* ---------- Size & Waktu otomatis ---------- */
    function showAuto(opt) {
        sizeText.textContent = opt ? (opt.size || '–').replace('-', ' - ') : '–';

        const hasTime = !!(opt && opt.time_start && opt.time_end);
        timeText.textContent = hasTime ? opt.time_start.replace(':', '.') + ' - ' + opt.time_end.replace(':', '.') : '–';

        const needManual = !!opt && !hasTime;
        manual.classList.toggle('on', needManual);
        manualNote.classList.toggle('on', needManual);
        $$('input', manual).forEach(i => i.disabled = !needManual);
    }

    /* ---------- Speed Line Evis & Jumlah Sampling dari Defeathering Report (tetap editable) ---------- */
    function applyDefeat(opt, force) {
        if (D.editing) return;

        const d = opt && opt.defeat;
        autofillInputs.forEach(el => {
            if (force) delete el.dataset.touched;           // ganti farm = data baru
            if (el.dataset.touched === '1') return;
            if (!force && el.value !== '') return;
            const v = d ? d[el.dataset.autofill] : null;
            el.value = (v === null || v === undefined) ? '' : String(v);
        });

        hint.className = 'hint';
        if (!opt) {
            hint.textContent = '';
        } else if (d) {
            hint.textContent = 'Speed Line Evis dan Jumlah Sampling terisi dari Defeathering Report' + (d.date ? ' (' + d.date + ')' : '') + '. Silakan ubah bila berbeda.';
        } else {
            hint.className = 'hint warn';
            hint.textContent = 'Belum ada data Defeathering Report untuk farm ini. Isi Speed Line Evis dan Jumlah Sampling secara manual.';
        }
    }

    /* ---------- persentase = temuan / Jumlah Sampling ---------- */
    function updateChecks() {
        const s = int(sampling.value);
        const has = s !== null && s > 0;

        checks.forEach(c => {
            const n = int(c.input.value);
            const over = has && n !== null && n > s;
            const p = has && n !== null ? n / s * 100 : null;

            c.input.setCustomValidity(over ? c.cap.dataset.label + ' tidak boleh melebihi Jumlah Sampling.' : '');
            c.input.classList.toggle('is-invalid', over);

            c.out.classList.toggle('empty', p === null);
            c.out.innerHTML = p === null ? '–' : pct(p) + '<small>%</small>';
            c.bar.style.width = (p === null ? 0 : Math.min(p, 100)) + '%';

            c.cap.className = 'qc-cap' + (over ? ' err' : '');
            c.cap.textContent = over ? c.cap.dataset.label + ' (' + id(n) + ') melebihi Jumlah Sampling (' + id(s) + ').' : '';
        });
    }

    function refresh(force) {
        const opt = findOption();
        showAuto(opt);
        applyDefeat(opt, force);
        updateChecks();
    }

    if (sel && !D.editing) sel.addEventListener('change', () => refresh(true));

    /* ---------- sanitasi & batas nilai ---------- */
    document.addEventListener('input', e => {
        const el = e.target;
        if (!el.classList || !el.classList.contains('num')) return;

        el.value = el.classList.contains('int') ? el.value.replace(/[^0-9]/g, '') : el.value.replace(/[^0-9.,]/g, '');

        const max = el.dataset.max ? parseFloat(el.dataset.max) : null;
        const n = parseFloat(el.value.replace(',', '.'));
        el.classList.remove('clamped');
        if (!isNaN(n) && max !== null && n > max) {
            el.value = String(max).replace('.', ',');
            el.classList.add('clamped');
            el.title = 'Maksimal ' + max;
            setTimeout(() => el.classList.remove('clamped'), 1200);
        }

        updateChecks();
    });

    $('#farmForm').addEventListener('submit', () => {
        const b = $('#submitBtn');
        b.disabled = true;
        b.textContent = 'Menyimpan…';
    });

    /* ---------- init: setelah gagal validasi / mode ubah, nilai yang sudah ada tidak ditimpa ---------- */
    refresh(false);
})();
</script>
@endverbatim
@endsection