@extends('layouts.app')

@section('content')
@php
    use App\Models\DefeatReportItem as DI;

    $editing = (bool) $item;
    $backUrl = route('defeathering.efficiency.index', [
        'date_from' => $report->report_date->toDateString(),
        'date_to' => $report->report_date->toDateString(),
    ]) . '#report-' . $report->id;

    // nilai awal input: old() > data tersimpan; desimal tampil dengan koma, mm.ss apa adanya
    $val = function (string $key) use ($item) {
        $v = old($key, $item?->getAttribute($key));
        if ($v === null || $v === '') {
            return '';
        }

        return DI::FIELDS[$key]['kind'] === 'time' ? (string) $v : str_replace('.', ',', (string) $v);
    };

    // baris parameter di sisi kanan (AVG DTA ada di kartu kiri)
    $rows = collect(DI::FIELDS)->except('avg_dta')->groupBy('group', true);

    $placeholders = [
        'speed_live_bird' => '5500', 'speed_evis_c' => '5500',
        'bleeding_time' => '01.16', 'scalder1_time' => '02.15', 'scalder2_time' => '02.15', 'scalder3_time' => '02.15',
    ];

    $selected = old('monitor_control_id', $editing ? $item->monitor_control_id : '');
@endphp

<style>
    .df-wrap { font-family:'Inter',sans-serif; }
    .df-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:9px 16px; border-radius:10px; font-size:13.5px; font-weight:600; border:1px solid var(--card-border); background:#fff; color:var(--text-main); cursor:pointer; text-decoration:none; }
    .df-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .df-btn:disabled { opacity:.6; cursor:not-allowed; }

    .ctx { display:flex; gap:22px; flex-wrap:wrap; margin-bottom:16px; }
    .ctx .lbl { font-size:10.5px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); }
    .ctx .val { font-size:14px; font-weight:700; margin-top:1px; }
    .alert-err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:10px 14px; font-size:13px; margin-bottom:14px; }
    .alert-err ul { margin:0; padding-left:18px; }
    .hint { font-size:12px; margin:0 0 12px; color:var(--text-muted); min-height:16px; }
    .hint.warn { color:#b45309; }

    .ff-layout { display:grid; grid-template-columns:340px minmax(0,1fr); gap:28px; align-items:stretch; }
    @media (max-width:900px){ .ff-layout { grid-template-columns:1fr; } }

    /* kartu kiri */
    .ff-card { border:1px solid #cfd6e4; border-radius:22px; padding:24px 22px; display:flex; flex-direction:column; background:#fff; min-height:520px; }
    .ff-lbl { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.02em; color:#6b7280; margin-bottom:6px; }
    .ff-big { font-size:28px; font-weight:800; color:#0d3b6e; line-height:1.15; min-height:32px; }
    .ff-block { margin-bottom:22px; }
    .ff-lbl.blue { color:#0d3b6e; }
    .ff-sel { width:100%; padding:10px 12px; border:1px solid #cfd6e4; border-radius:10px; font-family:'Inter',sans-serif; font-size:14px; background:#fff; color:var(--text-main); }
    .ff-sel:disabled { background:#f3f4f6; color:#374151; }
    .dta-row { display:flex; align-items:center; gap:10px; }
    .dta-row input { width:130px; text-align:center; font-size:20px; font-weight:700; color:#0d3b6e; padding:8px 10px; border:1px solid #cfd6e4; border-radius:10px; font-family:'Inter',sans-serif; }
    .dta-row span { color:#6b7280; font-size:15px; }
    .manual-time { display:none; gap:8px; align-items:center; margin-top:8px; }
    .manual-time.on { display:flex; }
    .manual-time input { padding:8px 10px; border:1px solid #cfd6e4; border-radius:10px; font-family:'Inter',sans-serif; font-size:14px; width:100%; }
    .manual-note { font-size:11.5px; color:#b45309; margin-top:6px; display:none; }
    .manual-note.on { display:block; }
    .ff-actions { margin-top:auto; display:flex; gap:12px; padding-top:20px; }

    /* sisi kanan */
    .ff-params { border-top:1px solid #cfd6e4; }
    .pr-row { display:grid; grid-template-columns:190px minmax(0,1fr) minmax(0,1fr); gap:18px; align-items:start; padding:16px 6px 18px; border-bottom:1px solid #cfd6e4; }
    @media (max-width:640px){ .pr-row { grid-template-columns:1fr 1fr; } .pr-name { grid-column:1 / -1; } }
    .pr-name { font-size:15px; font-weight:800; color:#0d3b6e; text-transform:uppercase; letter-spacing:.02em; padding-top:2px; }
    .pr-field label { display:block; font-size:13px; color:#6b7280; margin-bottom:6px; }
    .pr-field input { width:100%; padding:10px 12px; border:1px solid #cfd6e4; border-radius:10px; font-family:'Inter',sans-serif; font-size:14px; background:#fff; color:var(--text-main); }
    input:focus { outline:none; border-color:var(--accent) !important; box-shadow:0 0 0 3px rgba(232,93,47,.12); }
    input.clamped { border-color:#ef4444 !important; background:#fef2f2 !important; }
</style>

<form id="farmForm" method="POST" autocomplete="off"
      action="{{ $editing ? route('defeathering.efficiency.items.update', [$report, $item]) : route('defeathering.efficiency.items.store', $report) }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="panel df-wrap">
        <div class="panel-header">
            <div class="panel-title">{{ $editing ? 'Ubah' : 'Tambah' }} Farm &mdash; Efficiency Defeathering</div>
            <a href="{{ $backUrl }}" class="df-btn">Kembali</a>
        </div>

        <div class="panel-body">
            <div class="ctx">
                <div><div class="lbl">Tanggal Operasional</div><div class="val">{{ $report->report_date->format('d/m/Y') }}</div></div>
                <div><div class="lbl">Shift</div><div class="val">{{ ucfirst($report->shift) }}</div></div>
                <div><div class="lbl">Lokasi</div><div class="val">{{ $report->location }}</div></div>
            </div>

            @if($errors->any())
                <div class="alert-err"><ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
            @endif

            <p class="hint" id="hint"></p>

            <div class="ff-layout">
                {{-- ============ KARTU KIRI ============ --}}
                <aside class="ff-card">
                    <div class="ff-block">
                        <label class="ff-lbl blue" for="farmSel">Nama Farm</label>
                        @if($editing)
                            <select class="ff-sel" id="farmSel" disabled>
                                @foreach($farms as $o)
                                    <option value="{{ $o['id'] }}" selected>{{ $o['label'] }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="ff-sel" id="farmSel" name="monitor_control_id" required>
                                <option value="">— Pilih farm —</option>
                                @foreach($farms as $o)
                                    <option value="{{ $o['id'] }}" @selected((string) $selected === (string) $o['id'])>{{ $o['label'] }}</option>
                                @endforeach
                            </select>
                            @if(empty($farms))
                                <div class="manual-note on" style="margin-top:8px;">Tidak ada farm berstatus Selesai (Kontrol Monitor) yang belum diisi pada tanggal, shift, dan lokasi ini.</div>
                            @endif
                        @endif
                    </div>

                    <div class="ff-block">
                        <div class="ff-lbl">Size Ayam</div>
                        <div class="ff-big" id="sizeText">–</div>
                    </div>

                    <div class="ff-block">
                        <div class="ff-lbl">Waktu</div>
                        <div class="ff-big" id="timeText">–</div>
                        <div class="manual-time" id="manualTime">
                            <input type="time" name="time_start" id="time_start" value="{{ old('time_start', $item?->time_start) }}" disabled>
                            <span>–</span>
                            <input type="time" name="time_end" id="time_end" value="{{ old('time_end', $item?->time_end) }}" disabled>
                        </div>
                        <div class="manual-note" id="manualNote">Waktu hanging belum tersedia pada Form Hanging farm ini. Isi manual.</div>
                    </div>

                    <div class="ff-block">
                        <label class="ff-lbl" for="f_avg_dta">Average DTA</label>
                        <div class="dta-row">
                            <input type="text" id="f_avg_dta" name="avg_dta" class="num dec" inputmode="decimal" data-key="avg_dta" data-max="{{ DI::FIELDS['avg_dta']['max'] }}"
                                   value="{{ $val('avg_dta') }}" required>
                            <span>KG</span>
                        </div>
                    </div>

                    <div class="ff-actions">
                        <button type="submit" class="df-btn primary" id="submitBtn">Simpan</button>
                        <a href="{{ $backUrl }}" class="df-btn">Batal</a>
                    </div>
                </aside>

                {{-- ============ PARAMETER (KANAN) ============ --}}
                <section class="ff-params">
                    @foreach($rows as $group => $cols)
                        <div class="pr-row">
                            <div class="pr-name">{{ $group }}</div>
                            @foreach($cols as $key => $f)
                                @php $cls = $f['kind'] === 'time' ? 'mmss' : ($f['kind'] === 'int' ? 'int' : 'dec'); @endphp
                                <div class="pr-field">
                                    <label for="f_{{ $key }}">{{ $f['label'] }}</label>
                                    <input type="text" id="f_{{ $key }}" name="{{ $key }}" class="num {{ $cls }}" data-key="{{ $key }}" data-kind="{{ $f['kind'] }}"
                                           @if($f['max'] !== null) data-max="{{ $f['max'] }}" @endif
                                           @if(! empty($f['hist'])) data-hist="1" @endif
                                           @if($f['kind'] === 'time') maxlength="5" @endif
                                           inputmode="{{ $f['kind'] === 'dec' || $f['kind'] === 'time' ? 'decimal' : 'numeric' }}"
                                           placeholder="{{ $placeholders[$key] ?? '' }}"
                                           value="{{ $val($key) }}" @if($f['req']) required @endif>
                                </div>
                            @endforeach
                        </div>
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
        hasOld: @json(old('monitor_control_id') !== null || $errors->any()),
    };
</script>

@verbatim
<script>
(function () {
    const D = window.DEFEAT;
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
    const num = v => { const n = parseFloat(String(v).replace(',', '.')); return isNaN(n) ? null : n; };

    const sel = $('#farmSel');
    const sizeText = $('#sizeText');
    const timeText = $('#timeText');
    const manual = $('#manualTime');
    const manualNote = $('#manualNote');
    const hint = $('#hint');
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

    if (sel && !D.editing) sel.addEventListener('change', () => refresh(true));

    /* ---------- sanitasi & batas nilai ---------- */
    document.addEventListener('input', e => {
        const el = e.target;
        if (!el.classList || !el.classList.contains('num')) return;

        if (el.classList.contains('int')) el.value = el.value.replace(/[^0-9]/g, '');
        else if (el.classList.contains('mmss')) el.value = el.value.replace(/[^0-9.,:]/g, '');
        else el.value = el.value.replace(/[^0-9.,]/g, '');

        const max = el.dataset.max ? parseFloat(el.dataset.max) : null;
        const n = num(el.value);
        el.classList.remove('clamped');
        if (!el.classList.contains('mmss') && n !== null && max !== null && n > max) {
            el.value = String(max).replace('.', ',');
            el.classList.add('clamped');
            el.title = 'Maksimal ' + max;
            setTimeout(() => el.classList.remove('clamped'), 1200);
        }
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
    refresh(false);
})();
</script>
@endverbatim
@endsection