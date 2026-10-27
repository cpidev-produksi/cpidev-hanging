@extends('layouts.app')

@section('content')
@php
    $statusStyle = match ($report->status) {
        'approved' => 'background:rgba(16,185,129,.10);color:#047857;border:1px solid rgba(16,185,129,.25);',
        'known'    => 'background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;',
        default    => 'background:#FEF9C3;color:#92400E;border:1px solid #FDE68A;',
    };
@endphp

<style>
    .df-wrap { font-family:'Inter',sans-serif; }
    .df-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .df-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; border:1px solid var(--card-border); background:#fff; color:var(--text-main); cursor:pointer; text-decoration:none; }
    .df-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .df-btn.success { background:#059669; border-color:#059669; color:#fff; }
    .df-btn.danger  { color:#b91c1c; border-color:#fecaca; background:#fff5f5; }
    .df-btn.sm { padding:4px 9px; font-size:12px; }
    .df-meta { display:flex; gap:22px; flex-wrap:wrap; margin-bottom:18px; }
    .df-meta .lbl { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); }
    .df-meta .val { font-size:15px; font-weight:600; margin-top:2px; }

    .alert-err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:10px 14px; font-size:13px; margin-bottom:14px; }
    .alert-ok { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; border-radius:10px; padding:10px 14px; font-size:13px; margin-bottom:14px; }
    .tbl-wrap { margin-bottom:18px; }
    .note { font-size:12px; color:var(--text-muted); margin:0 0 18px; }

    .sign-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:8px; }
    @media (max-width:720px){ .sign-grid { grid-template-columns:1fr; } }
    .sign-box { border:1px solid var(--card-border); border-radius:12px; padding:14px; text-align:center; background:#fff; }
    .sign-role { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); }
    .sign-sub { font-size:11px; color:var(--text-muted); }
    .sign-body { min-height:74px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px; }
    .sign-name { font-weight:700; font-size:14px; }
    .sign-time { font-size:11.5px; color:var(--text-muted); }
    .sign-empty { color:#9aa5bd; font-size:12px; }
    .sign-ok { color:#059669; font-size:11px; font-weight:700; }
    .sign-input { max-width:100%; padding:7px 9px; border:1px solid var(--card-border); border-radius:8px; font:inherit; }
</style>

<div class="panel df-wrap">
    <div class="panel-header">
        <div class="panel-title">Efficiency Evisceration Report</div>
        <div class="df-actions">
            @if($report->canManageItems())
                <a href="{{ route('evisceration.efficiency.items.create', $report) }}" class="df-btn primary">+ Tambah Farm</a>
            @endif
            <a href="{{ route('evisceration.efficiency.pdf', $report) }}" class="df-btn">Export PDF</a>
            <a href="{{ route('evisceration.efficiency.index') }}" class="df-btn">Kembali</a>
        </div>
    </div>

    <div class="panel-body">
        @if(session('status'))<div class="alert-ok">{{ session('status') }}</div>@endif
        @if($errors->any())
            <div class="alert-err">@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
        @endif

        <div class="df-meta">
            <div><div class="lbl">Tanggal Operasional</div><div class="val">{{ $report->report_date->format('d/m/Y') }}</div></div>
            <div><div class="lbl">Shift</div><div class="val">{{ ucfirst($report->shift) }}</div></div>
            <div><div class="lbl">Lokasi</div><div class="val">{{ $report->location }}</div></div>
            <div><div class="lbl">Jumlah Farm</div><div class="val">{{ $report->items->count() }}</div></div>
            <div><div class="lbl">Status</div>
                <div class="val"><span style="display:inline-block;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;{{ $statusStyle }}">{{ $report->status_label }}</span></div>
            </div>
        </div>

        <div class="tbl-wrap" id="report-items">
            @include('report.evis.effieciency.partials.table', ['report' => $report, 'actions' => true])
        </div>
        <p class="note">Persentase Vent dan Nouva = jumlah temuan ÷ Jumlah Sampling × 100. Total dan Average ditampilkan untuk Vent Tidak Sobek, Jeroan Tidak Terambil, dan Temuan Grit.</p>

        {{-- Pengesahan --}}
        <div class="sign-grid" id="approval">
            <div class="sign-box">
                <div class="sign-role">Dibuat oleh</div>
                <div class="sign-sub">(Checker)</div>
                <div class="sign-body">
                    <div class="sign-ok">✓ Dibuat</div>
                    <div class="sign-name">{{ $report->checker_name ?: ($report->creator?->name ?? '–') }}</div>
                    <div class="sign-time">{{ $report->created_at?->format('d/m/Y H:i') }}</div>
                    @if($report->canBeEdited())
                        <form method="POST" action="{{ route('evisceration.efficiency.checker-name', $report) }}">
                            @csrf
                            <input class="sign-input" type="text" name="checker_name" value="{{ old('checker_name', $report->checker_name) }}" placeholder="Nama Checker" maxlength="255" required>
                            <button type="submit" class="df-btn">Simpan nama</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="sign-box">
                <div class="sign-role">Diketahui oleh</div>
                <div class="sign-sub">(Foreman/lady)</div>
                <div class="sign-body">
                    @if($report->known_by)
                        <div class="sign-ok">✓ Diketahui</div>
                        <div class="sign-name">{{ $report->known_name ?: $report->knower?->name }}</div>
                        <div class="sign-time">{{ $report->known_at?->format('d/m/Y H:i') }}</div>
                    @elseif($report->canBeKnown())
                        <form method="POST" action="{{ route('evisceration.efficiency.known', $report) }}"
                              onsubmit="return confirm('Tandai laporan ini sebagai Diketahui? Setelah itu laporan tidak dapat diubah.')">
                            @csrf
                            <select class="sign-input" name="known_name" required>
                                <option value="">Pilih nama Foreman/lady</option>
                                @foreach(\App\Models\EviscerationEfficiencyReport::KNOWN_NAMES as $name)
                                    <option value="{{ $name }}" @selected(old('known_name') === $name)>{{ $name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="df-btn success">Ketahui laporan</button>
                        </form>
                    @else
                        <div class="sign-empty">Menunggu Foreman/lady</div>
                    @endif
                </div>
            </div>

            <div class="sign-box">
                <div class="sign-role">Disetujui oleh</div>
                <div class="sign-sub">(Supervisor)</div>
                <div class="sign-body">
                    @if($report->approved_by)
                        <div class="sign-ok">✓ Disetujui</div>
                        <div class="sign-name">{{ $report->approved_name ?: $report->approver?->name }}</div>
                        <div class="sign-time">{{ $report->approved_at?->format('d/m/Y H:i') }}</div>
                    @elseif($report->canBeApproved())
                        <form method="POST" action="{{ route('evisceration.efficiency.approve', $report) }}"
                              onsubmit="return confirm('Setujui laporan ini?')">
                            @csrf
                            <select class="sign-input" name="approved_name" required>
                                <option value="">Pilih nama Supervisor</option>
                                @foreach(\App\Models\EviscerationEfficiencyReport::APPROVED_NAMES as $name)
                                    <option value="{{ $name }}" @selected(old('approved_name') === $name)>{{ $name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="df-btn success">Setujui laporan</button>
                        </form>
                    @else
                        <div class="sign-empty">{{ $report->status === 'known' ? 'Menunggu Supervisor' : 'Menunggu pengesahan sebelumnya' }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection