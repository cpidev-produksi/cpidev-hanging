@extends('layouts.app')

@section('content')
<div class="page-container">

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
        <span>Data Farm</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="page-title-group">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
            <div>
                <h1 class="page-title">Data Farm</h1>
                <p class="page-subtitle">Kelola seluruh data farm/mitra peternakan</p>
            </div>
        </div>
        <a href="{{ route('master.farms.create') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Farm
        </a>
    </div>

    {{-- Table Card --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar Farm</div>
            <div class="card-meta">Total: {{ $farms->total() }}</div>
        </div>

        {{-- Search & Filter Toolbar --}}
        <div class="farm-toolbar">

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('master.farms.index') }}" id="search-form" class="farm-search-form">
                @if(request('letter'))
                    <input type="hidden" name="letter" value="{{ request('letter') }}">
                @endif
                <div class="farm-search-wrap">
                    <span class="farm-search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        value="{{ request('search') }}"
                        placeholder="Cari nama farm..."
                        autocomplete="off"
                        class="farm-search-input"
                    >
                    @if(request('search'))
                    <button type="button" onclick="clearSearch()" class="farm-search-clear" title="Hapus pencarian">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                    @endif
                </div>
                <button type="submit" class="farm-search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Cari
                </button>
            </form>

            {{-- Active Filter Badge --}}
            @if(request('search') || request('letter'))
            <div class="farm-filter-badge">
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
                    &nbsp;·&nbsp;<strong>{{ $farms->total() }}</strong> hasil
                </span>
                <a href="{{ route('master.farms.index') }}" class="farm-filter-reset">
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
                <a href="{{ route('master.farms.index', array_merge(request()->only(['search']), [])) }}"
                   class="alpha-btn {{ !request('letter') ? 'alpha-btn--active' : '' }}">
                    Semua
                </a>
                @foreach(range('A', 'Z') as $letter)
                    <a href="{{ route('master.farms.index', array_merge(request()->only(['search']), ['letter' => $letter])) }}"
                       class="alpha-btn {{ request('letter') === $letter ? 'alpha-btn--active' : '' }}">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Table --}}
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Nama Farm
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Depo
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <path d="M8 12h8"/>
                                </svg>
                                Vendor Code
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <line x1="3" y1="9" x2="21" y2="9"/>
                                    <line x1="9" y1="21" x2="9" y2="9"/>
                                </svg>
                                Kategori Area
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="3" y1="12" x2="21" y2="12"/>
                                    <polyline points="15 6 21 12 15 18"/>
                                </svg>
                                Jarak
                            </div>
                        </th>
                        <th class="th-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($farms as $farm)
                    <tr>
                        <td>
                            <div class="farm-cell">
                                <div class="farm-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>
                                <span class="farm-name">{{ $farm->name }}</span>
                            </div>
                        </td>
                        <td>{{ $farm->city }}</td>
                        <td><code class="vendor-code">{{ $farm->vendor_code }}</code></td>
                        <td>
                            @php
                                $areaLabels = [1 => '1', 2 => '2', 3 => '3', 4 => '4'];
                                $areaClasses = [
                                    1 => 'badge badge-success',
                                    2 => 'badge badge-accent',
                                    3 => 'badge badge-warning',
                                    4 => 'badge badge-muted',
                                ];
                            @endphp
                            <span class="{{ $areaClasses[$farm->area_category] ?? 'badge badge-muted' }}">
                                {{ $areaLabels[$farm->area_category] ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $farm->distance }}</td>
                        <td class="action-cell">
                            <a href="{{ route('master.farms.edit', $farm) }}" class="btn-icon-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('master.farms.destroy', $farm) }}" class="inline-form" onsubmit="return confirm('Hapus farm {{ addslashes($farm->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-delete" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                        <td colspan="6" class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            @if(request('search') || request('letter'))
                                <p>Tidak ada farm yang cocok dengan filter</p>
                                <a href="{{ route('master.farms.index') }}" class="btn-primary-sm">Lihat Semua Farm</a>
                            @else
                                <p>Belum ada data farm</p>
                                <a href="{{ route('master.farms.create') }}" class="btn-primary-sm">+ Tambah Sekarang</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($farms->hasPages())
        <div class="farm-pagination">

            {{-- Info --}}
            <p class="pagination-info">
                Menampilkan
                <span class="pagination-info--highlight">{{ $farms->firstItem() }}–{{ $farms->lastItem() }}</span>
                dari
                <span class="pagination-info--highlight">{{ $farms->total() }}</span>
                farm
            </p>

            {{-- Nav --}}
            <div class="pagination-nav">

                {{-- Prev --}}
                @if($farms->onFirstPage())
                    <span class="page-btn page-btn--disabled" aria-disabled="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                @else
                    <a href="{{ $farms->previousPageUrl() }}&{{ http_build_query(request()->only(['search', 'letter'])) }}"
                       class="page-btn" title="Halaman sebelumnya">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @php
                    $cur   = $farms->currentPage();
                    $last  = $farms->lastPage();
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
                        <a href="{{ $farms->url($page) }}&{{ http_build_query(request()->only(['search', 'letter'])) }}"
                           class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($farms->hasMorePages())
                    <a href="{{ $farms->nextPageUrl() }}&{{ http_build_query(request()->only(['search', 'letter'])) }}"
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

{{-- Styles --}}
<style>
/* ═══════════════════════════════════════════
   SEARCH TOOLBAR
═══════════════════════════════════════════ */
.farm-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px 12px;
    flex-wrap: wrap;
}

.farm-search-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 220px;
    max-width: 420px;
}

.farm-search-wrap {
    position: relative;
    flex: 1;
}

.farm-search-icon {
    position: absolute;
    inset-y: 0;
    left: 0;
    padding-left: 11px;
    display: flex;
    align-items: center;
    pointer-events: none;
    color: #9ca3af;
}

.farm-search-input {
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
.farm-search-input::placeholder { color: #b0b7c3; }
.farm-search-input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}

.farm-search-clear {
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
.farm-search-clear:hover { color: #ef4444; }

.farm-search-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #fff;
    background: #6366f1;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s, transform .1s;
}
.farm-search-btn:hover  { background: #4f46e5; }
.farm-search-btn:active { transform: scale(.96); }

/* Active filter badge */
.farm-filter-badge {
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
.farm-filter-badge strong { color: #1d4ed8; }

.farm-filter-reset {
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
.farm-filter-reset:hover { background: #fee2e2; color: #dc2626; }

/* ═══════════════════════════════════════════
   ALPHABET FILTER
═══════════════════════════════════════════ */
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
    background: #6366f1;
    color: #fff;
    border-color: #6366f1;
    box-shadow: 0 1px 4px rgba(99,102,241,.3);
}
.alpha-btn--active:hover {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #fff;
}

/* ═══════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════ */
.farm-pagination {
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
    background: #6366f1;
    color: #fff;
    border-color: #6366f1;
    box-shadow: 0 1px 6px rgba(99,102,241,.35);
    font-weight: 600;
    cursor: default;
}
.page-btn--active:hover {
    background: #6366f1;
    color: #fff;
    border-color: #6366f1;
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