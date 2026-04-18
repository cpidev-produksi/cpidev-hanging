@extends('layouts.app')

@section('content')
<div class="history-header">
    <div>
        <div class="history-breadcrumb">
            <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4"><polyline points="9 18 15 12 9 6"></polyline></svg>
            <span style="color:var(--text-muted)">History Perubahan</span>
        </div>
        <h1 class="history-title">History Perubahan</h1>
        <p class="history-subtitle">Rekam jejak seluruh aktivitas perubahan data dalam sistem</p>
    </div>
    <div class="history-stats">
        <div class="stat-pill">
            <div class="stat-dot"></div>
            <span>{{ $logs->total() }} Total Log</span>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent)">
                <polyline points="12 8 12 12 14 14"></polyline>
                <path d="M3.05 11a9 9 0 1 0 .5-4.5"></path>
                <polyline points="3 3 3 8 8 8"></polyline>
            </svg>
            Log Aktivitas
        </span>
        <span class="panel-badge">{{ $logs->count() }} entri</span>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        <div class="th-inner">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Waktu
                        </div>
                    </th>
                    <th>
                        <div class="th-inner">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            User
                        </div>
                    </th>
                    <th>
                        <div class="th-inner">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            Role
                        </div>
                    </th>
                    <th>
                        <div class="th-inner">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            Form
                        </div>
                    </th>
                    <th>
                        <div class="th-inner">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                            Aksi
                        </div>
                    </th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>
                        <div class="time-cell">
                            <span class="time-date">{{ $log->created_at->format('d/m/Y') }}</span>
                            <span class="time-hour">{{ $log->created_at->format('H:i') }}</span>
                        </div>
                    </td>
                    <td>
                        @if($log->user_name)
                            <div class="user-cell">
                                <div class="user-avatar-sm">{{ strtoupper(substr($log->user_name, 0, 1)) }}</div>
                                <span>{{ $log->user_name }}</span>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($log->user_role)
                            <span class="role-badge">{{ $log->user_role }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="form-key">{{ $log->form_key }}</span>
                    </td>
                    <td>
                        <span class="action-badge action-{{ strtolower($log->action) }}">{{ $log->action }}</span>
                    </td>
                    <td>
                        <a href="{{ route('history.show', $log) }}" class="btn-detail">
                            Detail
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                            </div>
                            <div class="empty-title">Belum ada log</div>
                            <div class="empty-desc">Aktivitas perubahan akan muncul di sini</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="panel-footer">
            <div class="pagination-info">
                Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} entri
            </div>
            <div class="pagination-nav">
                {{-- Previous --}}
                @if($logs->onFirstPage())
                    <span class="page-btn page-btn--disabled">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="page-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @php
                    $cur   = $logs->currentPage();
                    $last  = $logs->lastPage();
                    $pages = collect(range(1, $last))
                        ->filter(fn($p) => $p === 1 || $p === $last || abs($p - $cur) <= 2)
                        ->values();
                @endphp

                @foreach($pages as $i => $page)
                    @if($i > 0 && $page - $pages[$i - 1] > 1)
                        <span class="page-ellipsis">…</span>
                    @endif

                    @if($page === $cur)
                        <span class="page-btn page-btn--active">{{ $page }}</span>
                    @else
                        <a href="{{ $logs->url($page) }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="page-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                @else
                    <span class="page-btn page-btn--disabled">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
/* ===== History Page Styles ===== */
.history-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}
.history-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.breadcrumb-link {
    color: var(--text-muted);
    text-decoration: none;
    font-weight: 600;
}
.breadcrumb-link:hover { color: var(--accent); }

.history-title {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 22px;
    color: var(--text-main);
    line-height: 1.2;
}
.history-subtitle {
    font-size: 13px;
    color: var(--text-muted);
    margin-top: 3px;
}
.history-stats {
    display: flex;
    align-items: center;
    gap: 8px;
}
.stat-pill {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    background: white;
    border: 1px solid var(--card-border);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-main);
}
.stat-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--accent);
    flex-shrink: 0;
}

/* Panel override */
.panel {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--card-border);
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    overflow: hidden;
}
.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--card-border);
    background: #fafbff;
}
.panel-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 14px;
    color: var(--text-main);
}
.panel-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    background: rgba(232,93,47,0.08);
    color: var(--accent);
    border: 1px solid rgba(232,93,47,0.15);
}

/* Table */
.table-wrapper {
    overflow-x: auto;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.data-table thead tr {
    background: #f8f9fc;
}
.data-table th {
    padding: 11px 16px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 800;
    color: var(--text-muted);
    letter-spacing: 0.07em;
    text-transform: uppercase;
    border-bottom: 1px solid var(--card-border);
    white-space: nowrap;
}
.th-inner {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.data-table td {
    padding: 13px 16px;
    border-bottom: 1px solid #f0f2f7;
    vertical-align: middle;
    color: var(--text-main);
    font-weight: 500;
}
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr { transition: background .12s; }
.data-table tbody tr:hover td { background: #fafbff; }

/* Time cell */
.time-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.time-date {
    font-weight: 700;
    font-size: 13px;
    color: var(--text-main);
}
.time-hour {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
}

/* User cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}
.user-avatar-sm {
    width: 26px; height: 26px;
    border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: white;
    font-size: 11px;
    flex-shrink: 0;
}

/* Role badge */
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    background: #f0f2f7;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    white-space: nowrap;
}

/* Form key */
.form-key {
    font-family: 'DM Mono', 'Courier New', monospace;
    font-size: 12px;
    font-weight: 600;
    color: #4f46e5;
    background: rgba(79,70,229,0.07);
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid rgba(79,70,229,0.12);
    white-space: nowrap;
}

/* Action badge */
.action-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .03em;
    text-transform: uppercase;
    white-space: nowrap;
}
.action-create, .action-created, .action-tambah {
    background: rgba(16,185,129,0.10);
    color: #065f46;
    border: 1px solid rgba(16,185,129,0.2);
}
.action-update, .action-updated, .action-edit, .action-ubah {
    background: rgba(59,130,246,0.10);
    color: #1e3a8a;
    border: 1px solid rgba(59,130,246,0.2);
}
.action-delete, .action-deleted, .action-hapus {
    background: rgba(239,68,68,0.08);
    color: #7f1d1d;
    border: 1px solid rgba(239,68,68,0.18);
}
/* default untuk aksi lain */
.action-badge:not([class*="action-create"]):not([class*="action-update"]):not([class*="action-delete"]):not([class*="action-edit"]):not([class*="action-tambah"]):not([class*="action-ubah"]):not([class*="action-hapus"]) {
    background: #f0f2f7;
    color: var(--text-muted);
    border: 1px solid var(--card-border);
}

.text-muted { color: var(--text-muted); }

/* Detail button */
.btn-detail {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 8px;
    background: white;
    border: 1px solid var(--card-border);
    color: var(--text-main);
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.btn-detail:hover {
    background: var(--accent);
    border-color: var(--accent);
    color: white;
}

/* Empty state */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 48px 24px;
    text-align: center;
}
.empty-icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    background: #f5f7fc;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    margin-bottom: 14px;
}
.empty-title {
    font-weight: 800;
    font-size: 15px;
    color: var(--text-main);
    margin-bottom: 4px;
}
.empty-desc {
    font-size: 13px;
    color: var(--text-muted);
}

/* Panel footer / pagination */
.panel-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-top: 1px solid var(--card-border);
    background: #fafbff;
    gap: 12px;
    flex-wrap: wrap;
}
.pagination-info {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 600;
}
.pagination-links nav { display: flex; align-items: center; }

/* Pagination Styles untuk index.blade */
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 4px;
}

.page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 6px;
    font-size: 12px;
    font-weight: 500;
    color: #4b5563;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 7px;
    text-decoration: none;
    transition: background .13s, color .13s, border-color .13s, box-shadow .13s;
    cursor: pointer;
}

.page-btn:hover {
    background: #eef2ff;
    color: #4338ca;
    border-color: #c7d2fe;
}

.page-btn--active {
    background: var(--accent, #E85D2F);
    color: #fff;
    border-color: var(--accent, #E85D2F);
    box-shadow: 0 1px 6px rgba(232,93,47,.35);
    font-weight: 600;
    cursor: default;
}

.page-btn--active:hover {
    background: var(--accent, #E85D2F);
    color: #fff;
    border-color: var(--accent, #E85D2F);
}

.page-btn--disabled {
    background: #f9fafb;
    color: #d1d5db;
    border-color: #f3f4f6;
    cursor: not-allowed;
    pointer-events: none;
}

.page-ellipsis {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 32px;
    font-size: 13px;
    color: #9ca3af;
    letter-spacing: .1em;
}

@media (max-width: 640px) {
    .history-header { flex-direction: column; align-items: flex-start; }
    .history-stats { width: 100%; }
    .panel-footer { flex-direction: column; align-items: flex-start; }
    /* Kolom role disembunyikan di layar kecil */
    .data-table th:nth-child(3),
    .data-table td:nth-child(3) { display: none; }
    .pagination-nav {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>
@endsection