@extends('layouts.app')

@section('content')
<div class="users-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}" class="breadcrumb-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-8H9v8H4a2 2 0 0 1-2-2z"/>
            </svg>
            Dashboard
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <span>Data User</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="page-title-group">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <h1 class="page-title">Data User</h1>
                <p class="page-subtitle">Kelola seluruh pengguna sistem</p>
            </div>
        </div>
        <a href="{{ route('master.users.create') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </a>
    </div>

    {{-- Table Card --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar User</div>
            <div class="card-meta">Total: {{ $users->total() }}</div>
        </div>

        {{-- Search & Filter Toolbar --}}
        <div class="user-toolbar">

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('master.users.index') }}" id="search-form" class="user-search-form">
                @if(request('letter'))
                    <input type="hidden" name="letter" value="{{ request('letter') }}">
                @endif
                <div class="user-search-wrap">
                    <span class="user-search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        value="{{ request('search') }}"
                        placeholder="Cari nama atau email..."
                        autocomplete="off"
                        class="user-search-input"
                    >
                    @if(request('search'))
                    <button type="button" onclick="clearSearch()" class="user-search-clear" title="Hapus pencarian">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                    @endif
                </div>
                <button type="submit" class="user-search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Cari
                </button>
            </form>

            {{-- Active Filter Badge --}}
            @if(request('search') || request('letter'))
            <div class="user-filter-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span>
                    @if(request('search'))
                        "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if(request('search') && request('letter'))&nbsp;·&nbsp;@endif
                    @if(request('letter'))
                        Huruf <strong>{{ request('letter') }}</strong>
                    @endif
                    &nbsp;·&nbsp;<strong>{{ $users->total() }}</strong> hasil
                </span>
                <a href="{{ route('master.users.index') }}" class="user-filter-reset">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Reset
                </a>
            </div>
            @endif

        </div>

        {{-- Alphabet Filter --}}
        <div class="alpha-filter-wrap">
            <span class="alpha-label">Filter:</span>
            <div class="alpha-list">
                <a href="{{ route('master.users.index', array_merge(request()->only(['search']), [])) }}"
                   class="alpha-btn {{ !request('letter') ? 'alpha-btn--active' : '' }}">
                    Semua
                </a>
                @foreach(range('A', 'Z') as $letter)
                    <a href="{{ route('master.users.index', array_merge(request()->only(['search']), ['letter' => $letter])) }}"
                       class="alpha-btn {{ request('letter') === $letter ? 'alpha-btn--active' : '' }}">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Nama
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                Email
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                Username
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                Role
                            </div>
                        </th>
                        <th class="th-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                                <span class="user-name">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">{{ $u->email }}</span>
                        </td>
                        <td>
                            <code class="username-badge">{{ $u->username }}</code>
                        </td>
                        <td>
                            @if($u->role?->name)
                                <span class="role-badge">{{ $u->role->name }}</span>
                            @else
                                <span class="text-empty">—</span>
                            @endif
                        </td>
                        <td class="action-cell">
                            <a href="{{ route('master.users.edit', $u) }}" class="btn-icon-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('master.users.destroy', $u) }}" class="inline-form" onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-delete" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            @if(request('search') || request('letter'))
                                <p>Tidak ada user yang cocok dengan filter</p>
                                <a href="{{ route('master.users.index') }}" class="btn-primary-sm">Lihat Semua User</a>
                            @else
                                <p>Belum ada data user</p>
                                <a href="{{ route('master.users.create') }}" class="btn-primary-sm">+ Tambah Sekarang</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="user-pagination">

            {{-- Info --}}
            <p class="pagination-info">
                Menampilkan
                <span class="pagination-info--highlight">{{ $users->firstItem() }}–{{ $users->lastItem() }}</span>
                dari
                <span class="pagination-info--highlight">{{ $users->total() }}</span>
                user
            </p>

            {{-- Nav --}}
            <div class="pagination-nav">

                {{-- Prev --}}
                @if($users->onFirstPage())
                    <span class="page-btn page-btn--disabled" aria-disabled="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                @else
                    <a href="{{ $users->previousPageUrl() }}&{{ http_build_query(request()->only(['search', 'letter'])) }}"
                       class="page-btn" title="Halaman sebelumnya">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @php
                    $cur   = $users->currentPage();
                    $last  = $users->lastPage();
                    $pages = collect(range(1, $last))
                        ->filter(fn($p) => $p === 1 || $p === $last || abs($p - $cur) <= 2)
                        ->values();
                @endphp

                @foreach($pages as $i => $page)
                    @if($i > 0 && $page - $pages[$i - 1] > 1)
                        <span class="page-ellipsis">…</span>
                    @endif

                    @if($page === $cur)
                        <span class="page-btn page-btn--active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $users->url($page) }}&{{ http_build_query(request()->only(['search', 'letter'])) }}"
                           class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}&{{ http_build_query(request()->only(['search', 'letter'])) }}"
                       class="page-btn" title="Halaman berikutnya">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                @else
                    <span class="page-btn page-btn--disabled" aria-disabled="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                @endif

            </div>
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

/* ===== BREADCRUMB ===== */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    font-size: 0.8rem;
    color: var(--c-muted);
}
.breadcrumb-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--c-muted);
    text-decoration: none;
    transition: color .15s;
}
.breadcrumb-link:hover {
    color: var(--c-accent);
}

/* ===== PAGE LAYOUT ===== */
.users-page { max-width: 1100px; margin: 0 auto; padding: 28px 20px; }

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

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    background: #FAFBFD;
    border-bottom: 1px solid var(--c-border);
}
.card-title {
    font-weight: 700;
    font-size: .9rem;
    color: var(--c-text);
}
.card-meta {
    font-size: .75rem;
    color: var(--c-muted);
    background: #F0F1F5;
    padding: 2px 10px;
    border-radius: 20px;
}

/* ===== SEARCH TOOLBAR ===== */
.user-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px 12px;
    flex-wrap: wrap;
}

.user-search-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 220px;
    max-width: 420px;
}

.user-search-wrap {
    position: relative;
    flex: 1;
}

.user-search-icon {
    position: absolute;
    inset-y: 0;
    left: 0;
    padding-left: 11px;
    display: flex;
    align-items: center;
    pointer-events: none;
    color: #9ca3af;
}

.user-search-input {
    width: 100%;
    padding: 8px 32px 8px 34px;
    font-size: 13px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    color: #374151;
    transition: border-color .15s, box-shadow .15s, background .15s;
    outline: none;
}
.user-search-input::placeholder { color: #b0b7c3; }
.user-search-input:focus {
    border-color: var(--c-accent);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(79,103,255,.12);
}

.user-search-clear {
    position: absolute;
    inset-y: 0;
    right: 0;
    padding-right: 10px;
    display: flex;
    align-items: center;
    color: #9ca3af;
    background: none;
    border: none;
    cursor: pointer;
    transition: color .15s;
}
.user-search-clear:hover { color: #ef4444; }

.user-search-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #fff;
    background: var(--c-accent);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s, transform .1s;
}
.user-search-btn:hover  { background: var(--c-accent-hover); }
.user-search-btn:active { transform: scale(.96); }

/* Active filter badge */
.user-filter-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 20px;
    font-size: 12px;
    color: #3b82f6;
    flex-shrink: 0;
}
.user-filter-badge strong { color: #1d4ed8; }

.user-filter-reset {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 4px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 600;
    color: #ef4444;
    background: #fff1f1;
    border: 1px solid #fecaca;
    border-radius: 12px;
    text-decoration: none;
    transition: background .15s, color .15s;
}
.user-filter-reset:hover { background: #fee2e2; color: #dc2626; }

/* ===== ALPHABET FILTER ===== */
.alpha-filter-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 20px 14px;
    border-bottom: 1px solid #f1f5f9;
}

.alpha-label {
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
    flex-shrink: 0;
}

.alpha-list {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.alpha-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    height: 28px;
    padding: 0 6px;
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    background: #f3f4f6;
    border: 1.5px solid transparent;
    border-radius: 6px;
    text-decoration: none;
    transition: background .13s, color .13s, border-color .13s;
    user-select: none;
}
.alpha-btn:hover {
    background: #e0e7ff;
    color: #4338ca;
    border-color: #c7d2fe;
}
.alpha-btn--active {
    background: var(--c-accent);
    color: #fff;
    border-color: var(--c-accent);
    box-shadow: 0 1px 4px rgba(79,103,255,.3);
}
.alpha-btn--active:hover {
    background: var(--c-accent-hover);
    border-color: var(--c-accent-hover);
    color: #fff;
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
.user-cell { display: flex; align-items: center; gap: 10px; }

.avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: var(--c-accent-light);
    color: var(--c-accent);
    font-size: .78rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.user-name { font-weight: 600; color: var(--c-text); }
.text-muted { color: var(--c-muted); font-size: .84rem; }
.text-empty { color: #C5C9D6; }

.username-badge {
    background: #F3F4F8;
    color: #4B5563;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: .78rem;
    font-family: 'Fira Code', 'Courier New', monospace;
    letter-spacing: .02em;
}

.role-badge {
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
.user-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 20px;
    border-top: 1px solid #f1f5f9;
}

.pagination-info {
    font-size: 12px;
    color: #9ca3af;
}
.pagination-info--highlight {
    font-weight: 600;
    color: #374151;
}

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
    user-select: none;
}
.page-btn:hover {
    background: #eef2ff;
    color: #4338ca;
    border-color: #c7d2fe;
}

.page-btn--active {
    background: var(--c-accent);
    color: #fff;
    border-color: var(--c-accent);
    box-shadow: 0 1px 6px rgba(79,103,255,.35);
    font-weight: 600;
    cursor: default;
}
.page-btn--active:hover {
    background: var(--c-accent);
    color: #fff;
    border-color: var(--c-accent);
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
</style>

<script>
function clearSearch() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// Auto-submit dengan debounce 600ms
let searchTimer;
document.getElementById('search-input')?.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const val = this.value;
    searchTimer = setTimeout(() => {
        if (val.length === 0 || val.length >= 2) {
            this.closest('form').submit();
        }
    }, 600);
});
</script>
@endsection