@extends('layouts.app')

@section('content')
<div class="monitor-page">

    <div class="page-header">
        <div class="page-title-group">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="14" rx="2"/>
                    <path d="M7 20h10"/>
                    <path d="M9 16v4"/>
                    <path d="M15 16v4"/>
                </svg>
            </div>
            <div>
                <h1 class="page-title">Kontrol Monitor</h1>
                <p class="page-subtitle">Kelola draft, proses berjalan, dan laporan selesai</p>
            </div>
        </div>

        <a href="{{ route('monitor-controls.create') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Kontrol
        </a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Report</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Ekspedisi</th>
                        <th>No Polisi</th>
                        <th>Farm</th>
                        <th>Status</th>
                        <th class="th-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($items as $it)
                    @php
                        $status = $it->status;
                        $statusClass = match ($status) {
                            'draft' => 'status-draft',
                            'running' => 'status-running',
                            default => 'status-done',
                        };

                        $lock = in_array($it->location, $runningLocations ?? []);
                    @endphp
                    <tr>
                        <td><code class="mono-badge">{{ $it->report_code }}</code></td>
                        <td><span class="text-muted">{{ $it->location }}</span></td>
                        <td><span class="text-muted">{{ $it->process_date->format('d/m/Y') }}</span></td>
                        <td><span class="shift-badge">{{ strtoupper($it->shift) }}</span></td>
                        <td>{{ $it->expedition?->name ?? '-' }}</td>
                        <td class="mono">{{ $it->plateNumber?->plate_number ?? '-' }}</td>
                        <td>{{ $it->farm?->name ?? '-' }}</td>
                        <td><span class="status-pill {{ $statusClass }}">{{ $status }}</span></td>
                        <td class="action-cell">
                            @if($it->status === 'draft')
                                <a href="{{ route('monitor-controls.edit', $it) }}" class="btn-icon-edit">Edit</a>

                                <form method="POST" action="{{ route('monitor-controls.start', $it) }}" class="inline-form">
                                    @csrf
                                    <button type="submit"
                                            class="btn-icon-start"
                                            {{ $lock ? 'disabled' : '' }}
                                            style="{{ $lock ? 'opacity:.45;cursor:not-allowed;' : '' }}"
                                            title="{{ $lock ? 'Lokasi sedang RUNNING' : 'Mulai Proses' }}">
                                        Mulai
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('monitor-controls.destroy', $it) }}" class="inline-form" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon-delete">Hapus</button>
                                </form>
                            @else
                                @if($it->hangingForm)
                                    <a href="{{ route('hanging-forms.show', $it->hangingForm) }}" class="btn-icon-open">Buka Hanging</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">
                            <p>Belum ada data kontrol monitor</p>
                            <a href="{{ route('monitor-controls.create') }}" class="btn-primary-sm">+ Buat Sekarang</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="pagination-wrapper">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.monitor-page { max-width: 1200px; margin: 0 auto; padding: 12px 6px; }
.page-header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.page-title-group { display:flex; align-items:center; gap:14px; }
.page-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:#EEF0FF; color:#4F67FF; }
.page-title { font-size: 1.25rem; font-weight: 800; margin:0; }
.page-subtitle { font-size:.8rem; color:#6B7280; margin:2px 0 0; }

.card { background:#fff; border:1px solid #E8EAF0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.04); overflow:hidden; }
.table-wrapper { overflow-x:auto; }
.data-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.data-table thead tr { background:#FAFBFD; border-bottom:1px solid #E8EAF0; }
.data-table th { padding:12px 14px; text-align:left; color:#6B7280; font-size:.72rem; font-weight:800; letter-spacing:.06em; text-transform:uppercase; white-space:nowrap; }
.data-table td { padding:12px 14px; border-top:1px solid #E8EAF0; vertical-align:middle; }
.th-right { text-align:right; }
.action-cell { text-align:right; white-space:nowrap; }
.inline-form { display:inline; }
.text-muted { color:#6B7280; }
.mono { font-family: 'Fira Code', 'Courier New', monospace; }

.mono-badge { background:#F3F4F8; color:#4B5563; padding:3px 9px; border-radius:6px; font-size:.78rem; font-family: 'Fira Code','Courier New',monospace; }
.shift-badge { display:inline-flex; padding:3px 10px; border-radius:999px; background:#EEF0FF; color:#4F67FF; font-size:.72rem; font-weight:900; letter-spacing:.06em; }

.status-pill { display:inline-flex; padding:3px 10px; border-radius:999px; font-size:.74rem; font-weight:900; text-transform:lowercase; border:1px solid transparent; }
.status-draft { background:#F3F4F8; color:#4B5563; border-color:rgba(75,85,99,.15); }
.status-running { background:#FFF8E1; color:#F59F00; border-color:rgba(245,159,0,.25); }
.status-done { background:#E6FAF5; color:#0CA678; border-color:rgba(12,166,120,.2); }

.btn-primary { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px; background:#4F67FF; color:#fff; text-decoration:none; font-weight:800; font-size:.85rem; }
.btn-primary-sm { display:inline-flex; align-items:center; padding:7px 15px; border-radius:8px; background:#4F67FF; color:#fff; text-decoration:none; font-weight:800; font-size:.8rem; }

.btn-icon-edit, .btn-icon-delete, .btn-icon-start, .btn-icon-open {
  display:inline-flex; align-items:center; gap:5px;
  padding:6px 12px; border-radius:8px;
  font-size:.78rem; font-weight:800;
  border:none; cursor:pointer; text-decoration:none;
  margin-left:4px;
}
.btn-icon-edit { background:#EEF0FF; color:#4F67FF; }
.btn-icon-start { background:#FFF8E1; color:#F59F00; }
.btn-icon-delete { background:#FFF0F0; color:#F03E3E; }
.btn-icon-open { background:#111827; color:#fff; }

.empty-state { text-align:center; padding:40px 16px; color:#6B7280; }
.pagination-wrapper { padding:14px 16px; border-top:1px solid #E8EAF0; background:#FAFBFD; }
</style>
@endsection