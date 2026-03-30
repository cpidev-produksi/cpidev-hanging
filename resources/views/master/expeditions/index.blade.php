@extends('layouts.app')

@section('content')
<div class="expeditions-page">

    {{-- Header --}}
    <div class="page-header">
        <div class="page-title-group">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="6" width="18" height="13" rx="2"/>
                    <path d="M7 10h6"/>
                    <path d="M7 14h10"/>
                    <path d="M8 3h8"/>
                </svg>
            </div>
            <div>
                <h1 class="page-title">Data Ekspedisi</h1>
                <p class="page-subtitle">Kelola ekspedisi dan daftar truk + sopir</p>
            </div>
        </div>
        <a href="{{ route('master.expeditions.create') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Ekspedisi
        </a>
    </div>

    {{-- Table Card --}}
    <div class="card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="6" width="18" height="13" rx="2"/>
                                    <path d="M7 10h6"/>
                                    <path d="M7 14h10"/>
                                </svg>
                                Nama Ekspedisi
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="6" width="20" height="12" rx="2"/>
                                    <path d="M6 12h12"/>
                                </svg>
                                Jumlah Truk
                            </div>
                        </th>
                        <th class="th-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($expeditions as $e)
                    <tr>
                        <td>
                            <div class="expedition-cell">
                                <div class="expedition-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect x="3" y="6" width="18" height="13" rx="2"/>
                                        <path d="M7 10h6"/>
                                        <path d="M7 14h10"/>
                                    </svg>
                                </div>
                                <span class="expedition-name">{{ $e->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="count-badge">{{ $e->plate_numbers_count ?? ($e->plateNumbers->count() ?? 0) }}</span>
                        </td>
                        <td class="action-cell">
                            <a href="{{ route('master.expeditions.edit', $e) }}" class="btn-icon-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('master.expeditions.destroy', $e) }}" class="inline-form" onsubmit="return confirm('Hapus ekspedisi {{ $e->name }}? Semua truk terkait juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-delete" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6"/>
                                        <path d="M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="6" width="18" height="13" rx="2"/>
                                <path d="M7 10h6"/>
                                <path d="M7 14h10"/>
                                <path d="M8 3h8"/>
                            </svg>
                            <p>Belum ada data ekspedisi</p>
                            <a href="{{ route('master.expeditions.create') }}" class="btn-primary-sm">+ Tambah Sekarang</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($expeditions->hasPages())
        <div class="pagination-wrapper">
            {{ $expeditions->links() }}
        </div>
        @endif
    </div>
</div>

<style>
/* ===== DESIGN SYSTEM ===== */
:root {
    --c-bg: #F5F6FA;
    --c-card: #FFFFFF;
    --c-border: #E8EAF0;
    --c-text: #1A1D2E;
    --c-muted: #6B7280;
    --c-accent: #4F67FF;
    --c-accent-hover: #3D53E8;
    --c-accent-light: #EEF0FF;
    --c-danger: #F03E3E;
    --c-danger-light: #FFF0F0;
    --c-success: #0CA678;
    --c-success-light: #E6FAF5;
    --c-warning: #F59F00;
    --c-warning-light: #FFF8E1;
    --radius: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
}

/* ===== PAGE LAYOUT ===== */
.expeditions-page { max-width: 1100px; margin: 0 auto; padding: 28px 20px; }

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}

.page-title-group { display: flex; align-items: center; gap: 14px; }

.page-icon {
    width: 46px; height: 46px;
    background: var(--c-accent-light);
    color: var(--c-accent);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.page-title { font-size: 1.35rem; font-weight: 700; color: var(--c-text); margin: 0 0 2px; }
.page-subtitle { font-size: .8rem; color: var(--c-muted); margin: 0; }

/* ===== BUTTONS ===== */
.btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px;
    background: var(--c-accent);
    color: #fff;
    border: none; border-radius: 9px;
    font-size: .85rem; font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background .18s, transform .12s, box-shadow .18s;
    box-shadow: 0 2px 8px rgba(79,103,255,.28);
}
.btn-primary:hover { background: var(--c-accent-hover); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(79,103,255,.35); }
.btn-primary:active { transform: translateY(0); }

.btn-primary-sm {
    display: inline-flex; align-items: center;
    padding: 7px 15px;
    background: var(--c-accent);
    color: #fff; border-radius: 8px;
    font-size: .8rem; font-weight: 600;
    text-decoration: none;
    transition: background .18s;
}
.btn-primary-sm:hover { background: var(--c-accent-hover); }

/* ===== CARD ===== */
.card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

/* ===== TABLE ===== */
.table-wrapper { overflow-x: auto; }

.data-table { width: 100%; border-collapse: collapse; font-size: .875rem; }

.data-table thead tr {
    background: #FAFBFD;
    border-bottom: 1px solid var(--c-border);
}

.data-table th {
    padding: 13px 16px;
    color: var(--c-muted);
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap;
}

.th-inner { display: flex; align-items: center; gap: 6px; }
.th-right { text-align: right; }

.data-table tbody tr {
    border-bottom: 1px solid var(--c-border);
    transition: background .12s;
}
.data-table tbody tr:last-child { border-bottom: none; }
.data-table tbody tr:hover { background: #FAFBFE; }

.data-table td { padding: 13px 16px; color: var(--c-text); vertical-align: middle; }

/* ===== CELLS ===== */
.expedition-cell { display: flex; align-items: center; gap: 10px; }

.expedition-icon {
    width: 34px; height: 34px; border-radius: 8px;
    background: var(--c-accent-light);
    color: var(--c-accent);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.expedition-name { font-weight: 600; color: var(--c-text); }

.count-badge {
    display: inline-flex;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--c-success-light);
    color: var(--c-success);
    font-size: .76rem;
    font-weight: 600;
}

/* ===== ACTION BUTTONS ===== */
.action-cell { text-align: right; white-space: nowrap; }
.inline-form { display: inline; }

.btn-icon-edit, .btn-icon-delete {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px;
    border-radius: 7px;
    font-size: .78rem; font-weight: 500;
    cursor: pointer; border: none;
    text-decoration: none;
    transition: background .15s, color .15s;
    margin-left: 4px;
}

.btn-icon-edit {
    background: var(--c-accent-light);
    color: var(--c-accent);
}
.btn-icon-edit:hover { background: var(--c-accent); color: #fff; }

.btn-icon-delete {
    background: var(--c-danger-light);
    color: var(--c-danger);
}
.btn-icon-delete:hover { background: var(--c-danger); color: #fff; }

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 56px 20px;
    color: var(--c-muted);
}
.empty-state svg { opacity: .35; margin-bottom: 12px; display: block; margin: 0 auto 12px; }
.empty-state p { font-size: .9rem; margin: 0 0 16px; }

/* ===== PAGINATION ===== */
.pagination-wrapper {
    padding: 14px 16px;
    border-top: 1px solid var(--c-border);
    background: #FAFBFD;
}
</style>
@endsection