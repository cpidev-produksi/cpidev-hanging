@extends('layouts.app')

@section('content')
<div class="detail-header">
    <div>
        <div class="detail-breadcrumb">
            <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4"><polyline points="9 18 15 12 9 6"></polyline></svg>
            <a href="{{ route('history.index') }}" class="breadcrumb-link">History Perubahan</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4"><polyline points="9 18 15 12 9 6"></polyline></svg>
            <span style="color:var(--text-muted)">Detail</span>
        </div>
        <h1 class="detail-title">Detail Perubahan</h1>
    </div>
    <a href="{{ route('history.index') }}" class="btn-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        Kembali
    </a>
</div>

{{-- Meta Info Card --}}
<div class="meta-grid">
    <div class="meta-card">
        <div class="meta-icon" style="background:rgba(59,130,246,0.08);color:#3b82f6">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="meta-label">Waktu</div>
            <div class="meta-value">{{ $auditLog->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="meta-card">
        <div class="meta-icon" style="background:rgba(99,102,241,0.08);color:#6366f1">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <div>
            <div class="meta-label">User</div>
            <div class="meta-value user-row">
                <div class="mini-avatar">{{ strtoupper(substr($auditLog->user_name ?? 'A', 0, 1)) }}</div>
                {{ $auditLog->user_name ?? '—' }}
            </div>
        </div>
    </div>

    <div class="meta-card">
        <div class="meta-icon" style="background:rgba(16,185,129,0.08);color:#10b981">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <div>
            <div class="meta-label">Role</div>
            <div class="meta-value">{{ $auditLog->user_role ?? '—' }}</div>
        </div>
    </div>

    <div class="meta-card">
        <div class="meta-icon" style="background:rgba(232,93,47,0.08);color:var(--accent)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
        <div>
            <div class="meta-label">Form</div>
            <div class="meta-value"><span class="form-key-lg">{{ $auditLog->form_key }}</span></div>
        </div>
    </div>

    <div class="meta-card">
        <div class="meta-icon" style="background:rgba(245,158,11,0.08);color:#d97706">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
        </div>
        <div>
            <div class="meta-label">Aksi</div>
            <div class="meta-value">
                <span class="action-badge action-{{ strtolower($auditLog->action) }}">{{ $auditLog->action }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Changes Section --}}
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent)">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
            Rincian Perubahan
        </span>
        @php $changeCount = count($auditLog->changes ?? []); @endphp
        @if($changeCount > 0)
        <span class="panel-badge">{{ $changeCount }} field</span>
        @endif
    </div>

    <div class="panel-body">
        @forelse(($auditLog->changes ?? []) as $field => $ch)
        <div class="change-row">
            <div class="change-field">
                <div class="field-icon">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <span class="field-name">{{ $field }}</span>
            </div>
            <div class="change-values">
                <div class="value-block value-before">
                    <div class="value-label">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Sebelum
                    </div>
                    <div class="value-content">
                        @if(is_null($ch['before']) || $ch['before'] === '')
                            <span class="value-empty">kosong</span>
                        @else
                            {{ is_array($ch['before']) || is_object($ch['before']) ? json_encode($ch['before'], JSON_UNESCAPED_UNICODE) : $ch['before'] }}
                        @endif
                    </div>
                </div>

                <div class="change-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </div>

                <div class="value-block value-after">
                    <div class="value-label">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Sesudah
                    </div>
                    <div class="value-content">
                        @if(is_null($ch['after']) || $ch['after'] === '')
                            <span class="value-empty">kosong</span>
                        @else
                            {{ is_array($ch['after']) || is_object($ch['after']) ? json_encode($ch['after'], JSON_UNESCAPED_UNICODE) : $ch['after'] }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-changes">
            <div class="empty-icon-wrap">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <div class="empty-title">Tidak ada data perubahan</div>
            <div class="empty-desc">Log ini tidak menyimpan rincian perubahan field</div>
        </div>
        @endforelse
    </div>
</div>

<style>
/* ===== Detail Page Styles ===== */
.detail-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.detail-breadcrumb {
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

.detail-title {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 22px;
    color: var(--text-main);
    line-height: 1.2;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 16px;
    border-radius: 10px;
    border: 1px solid var(--card-border);
    background: white;
    color: var(--text-main);
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.btn-back:hover {
    background: #f5f7fc;
    border-color: #c8d0df;
}

/* Meta grid */
.meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.meta-card {
    background: white;
    border: 1px solid var(--card-border);
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.meta-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.meta-label {
    font-size: 10.5px;
    font-weight: 800;
    color: var(--text-muted);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.meta-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
    line-height: 1.4;
}
.user-row {
    display: flex;
    align-items: center;
    gap: 7px;
}
.mini-avatar {
    width: 22px; height: 22px;
    border-radius: 6px;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: white;
    font-size: 10px;
    flex-shrink: 0;
}
.form-key-lg {
    font-family: 'DM Mono', 'Courier New', monospace;
    font-size: 12px;
    font-weight: 600;
    color: #4f46e5;
    background: rgba(79,70,229,0.07);
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid rgba(79,70,229,0.12);
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

/* Panel */
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
.panel-body {
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* Change rows */
.change-row {
    background: #fafbff;
    border: 1px solid var(--card-border);
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.change-row:hover {
    border-color: rgba(232,93,47,0.2);
    background: #fffaf8;
}
.change-field {
    display: flex;
    align-items: center;
    gap: 8px;
}
.field-icon {
    width: 20px; height: 20px;
    border-radius: 6px;
    background: rgba(232,93,47,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    flex-shrink: 0;
}
.field-name {
    font-size: 12px;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: .01em;
    text-transform: capitalize;
}

.change-values {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 10px;
    align-items: center;
}
.value-block {
    border-radius: 8px;
    padding: 10px 12px;
    min-height: 48px;
}
.value-before {
    background: rgba(239,68,68,0.05);
    border: 1px solid rgba(239,68,68,0.15);
}
.value-after {
    background: rgba(16,185,129,0.05);
    border: 1px solid rgba(16,185,129,0.15);
}
.value-label {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.value-before .value-label { color: #b91c1c; }
.value-after .value-label { color: #065f46; }

.value-content {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-main);
    word-break: break-word;
    line-height: 1.5;
}
.value-empty {
    font-style: italic;
    opacity: .5;
    font-size: 12px;
}

.change-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    opacity: .5;
    flex-shrink: 0;
}

/* Empty state */
.empty-changes {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px 24px;
    text-align: center;
}
.empty-icon-wrap {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: #f5f7fc;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    margin-bottom: 12px;
}
.empty-title {
    font-weight: 800;
    font-size: 14px;
    color: var(--text-main);
    margin-bottom: 4px;
}
.empty-desc {
    font-size: 12px;
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 640px) {
    .detail-header { flex-direction: column; align-items: flex-start; }
    .btn-back { align-self: flex-start; }
    .meta-grid { grid-template-columns: 1fr 1fr; }
    .change-values {
        grid-template-columns: 1fr;
    }
    .change-arrow { transform: rotate(90deg); justify-content: flex-start; }
}
@media (max-width: 400px) {
    .meta-grid { grid-template-columns: 1fr; }
}
</style>
@endsection