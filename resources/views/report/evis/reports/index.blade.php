@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Daily Report Evisceration</div>
        <a href="{{ route('report-evis.create') }}" class="topnav-link" style="background: var(--accent); color: white; border-color: var(--accent);">
            + Buat Report Baru
        </a>
    </div>

    <div class="panel-body">
        {{-- Filter --}}
        <form method="GET" style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 20px; flex-wrap: wrap;">
            <div>
                <label style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 5px;">Status</label>
                <select name="status" style="padding: 8px 12px; border: 1px solid var(--card-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; color: var(--text-main); background: white;">
                    <option value="">Semua</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                </select>
            </div>
            <div>
                <label style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 5px;">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    style="padding: 8px 12px; border: 1px solid var(--card-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px;">
            </div>
            <button type="submit" class="topnav-link" style="background: var(--accent); color: white; border-color: var(--accent);">Filter</button>
            <a href="{{ route('report-evis.index') }}" class="topnav-link">Reset</a>
        </form>

        {{-- Table --}}
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 48px;">No.</th>
                        <th>Tanggal</th>
                        <th>Dibuat Oleh</th>
                        <th>Status</th>
                        <th style="width: 180px;">Disetujui Oleh</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            {{-- No --}}
                            <td style="color: var(--text-muted);">{{ ($reports->currentPage() - 1) * $reports->perPage() + $loop->iteration }}</td>

                            {{-- Tanggal --}}
                            <td style="font-weight: 500;">{{ $report->report_date->format('d/m/Y') }}</td>

                            {{-- Dibuat Oleh --}}
                            <td>{{ $report->createdBy->name }}</td>

                            {{-- Status --}}
                            <td>
                                @if($report->isDraft())
                                    {{-- Belum disetujui: tampilkan tanda — dengan warna muted --}}
                                    <span style="
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 5px;
                                        padding: 4px 10px;
                                        border-radius: 6px;
                                        font-size: 12px;
                                        font-weight: 500;
                                        background: #F3F4F6;
                                        color: var(--text-muted);
                                        border: 1px solid var(--card-border);
                                    ">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="8" y1="12" x2="16" y2="12"/>
                                        </svg>
                                        Belum Disetujui
                                    </span>
                                    {{-- Draft: abu-abu dengan ikon jam/pensil --}}
                                    <span style="
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 5px;
                                        padding: 4px 10px;
                                        border-radius: 6px;
                                        font-size: 12px;
                                        font-weight: 600;
                                        background: #FEF9C3;
                                        color: #92400E;
                                        border: 1px solid #FDE68A;
                                    ">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Draft
                                    </span>
                                @else
                                    {{-- Approved: hijau dengan ikon centang --}}
                                    <span style="
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 5px;
                                        padding: 4px 10px;
                                        border-radius: 6px;
                                        font-size: 12px;
                                        font-weight: 600;
                                        background: rgba(16,185,129,0.10);
                                        color: var(--success);
                                        border: 1px solid rgba(16,185,129,0.25);
                                    ">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                        Approved
                                    </span>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">
                                        {{ $report->approved_at?->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>

                            {{-- Disetujui Oleh --}}
                            <td>
                                @if($report->isDraft())
                                    {{-- Tombol Approve (jika bisa) --}}
                                    @if($report->canBeApproved())
                                        <div style="margin-top: 5px;">
                                            <button type="button" onclick="openApproveModal({{ $report->id }})"
                                                style="
                                                    display: inline-flex;
                                                    align-items: center;
                                                    gap: 4px;
                                                    padding: 4px 10px;
                                                    border-radius: 6px;
                                                    font-size: 11px;
                                                    font-weight: 600;
                                                    background: rgba(16,185,129,0.08);
                                                    color: var(--success);
                                                    border: 1px solid rgba(16,185,129,0.2);
                                                    cursor: pointer;
                                                    font-family: inherit;
                                                ">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                                Belum Disetujui - Klik untuk Setujui
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    {{-- Sudah disetujui: tampilkan nama + ikon centang hijau --}}
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            width: 22px;
                                            height: 22px;
                                            border-radius: 50%;
                                            background: rgba(16,185,129,0.12);
                                            color: var(--success);
                                            flex-shrink: 0;
                                        ">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <div style="font-size: 13px; font-weight: 600; color: var(--success);">{{ $report->approvedBy?->name }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ $report->approved_at?->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- Aksi: ikon saja --}}
                            <td>
                                <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">

                                    {{-- Edit (hanya jika draft) --}}
                                    @if($report->isDraft())
                                        <a href="{{ route('report-evis.edit', $report) }}"
                                            title="Edit"
                                            style="
                                                display: inline-flex;
                                                align-items: center;
                                                justify-content: center;
                                                width: 32px;
                                                height: 32px;
                                                border-radius: 7px;
                                                background: #EFF6FF;
                                                color: #3B82F6;
                                                border: 1px solid #BFDBFE;
                                                transition: background 0.15s, border-color 0.15s;
                                            "
                                            onmouseover="this.style.background='#DBEAFE'"
                                            onmouseout="this.style.background='#EFF6FF'"
                                        >
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Lihat --}}
                                    <a href="{{ route('report-evis.show', $report) }}"
                                        title="Lihat Detail"
                                        style="
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            width: 32px;
                                            height: 32px;
                                            border-radius: 7px;
                                            background: #F0FDF4;
                                            color: #16A34A;
                                            border: 1px solid #BBF7D0;
                                            transition: background 0.15s;
                                        "
                                        onmouseover="this.style.background='#DCFCE7'"
                                        onmouseout="this.style.background='#F0FDF4'"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>

                                    {{-- PDF --}}
                                    <a href="{{ route('report-evis.pdf', $report) }}" 
                                        title="Download PDF"
                                        class="download-pdf-btn"
                                        data-report-id="{{ $report->id }}"
                                        style="
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            width: 32px;
                                            height: 32px;
                                            border-radius: 7px;
                                            background: #FFF7ED;
                                            color: #EA580C;
                                            border: 1px solid #FED7AA;
                                            transition: all 0.2s;
                                            text-decoration: none;
                                            cursor: pointer;
                                        "
                                        onmouseover="this.style.background='#FFEDD5'; this.style.transform='translateY(-1px)'"
                                        onmouseout="this.style.background='#FFF7ED'; this.style.transform='translateY(0)'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="12" y1="18" x2="12" y2="12"/>
                                            <polyline points="9 15 12 18 15 15"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <button type="button"
                                        title="Hapus"
                                        onclick="openDeleteModal({{ $report->id }})"
                                        style="
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            width: 32px;
                                            height: 32px;
                                            border-radius: 7px;
                                            background: #FFF1F2;
                                            color: #E11D48;
                                            border: 1px solid #FECDD3;
                                            cursor: pointer;
                                            transition: background 0.15s;
                                            font-family: inherit;
                                        "
                                        onmouseover="this.style.background='#FFE4E6'"
                                        onmouseout="this.style.background='#FFF1F2'"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 12px;">
                                Tidak ada data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $reports->links() }}
        </div>
    </div>
</div>

{{-- Modal Approve --}}
<div id="approveModal" class="logout-modal-overlay" role="dialog" aria-modal="true">
    <div class="logout-modal">
        <div class="logout-modal-icon" style="background: rgba(16,185,129,0.10); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <div class="logout-modal-title">Setujui Report?</div>
        <div class="logout-modal-desc">Report ini akan disetujui dan tidak bisa diedit lagi.</div>
        <div class="logout-modal-actions">
            <button type="button" class="btn-cancel" onclick="closeApproveModal()">Batal</button>
            <form method="POST" id="approveForm" style="flex: 1;">
                @csrf
                <button type="submit" class="btn-confirm-logout" style="width: 100%; background: var(--success);">Ya, Setujui</button>
            </form>
        </div>
    </div>
</div>

{{-- Modal Delete --}}
<div id="deleteModal" class="logout-modal-overlay" role="dialog" aria-modal="true">
    <div class="logout-modal">
        <div class="logout-modal-icon" style="background: rgba(225,29,72,0.10); color: #E11D48;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
        </div>
        <div class="logout-modal-title">Hapus Report?</div>
        <div class="logout-modal-desc">Report ini akan dihapus permanen dan tidak bisa dikembalikan.</div>
        <div class="logout-modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
            <form method="POST" id="deleteForm" style="flex: 1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-logout" style="width: 100%; background: #E11D48;">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
function openApproveModal(reportId) {
    document.getElementById('approveForm').action = `/report-evis/${reportId}/approve`;
    document.getElementById('approveModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeApproveModal() {
    document.getElementById('approveModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) closeApproveModal();
});

function openDeleteModal(reportId) {
    document.getElementById('deleteForm').action = `/report-evis/${reportId}`;
    document.getElementById('deleteModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
// function downloadPDF(event, url) {
//     // Prevent default link behavior
//     event.preventDefault();
    
//     // Show loading indicator
//     const btn = event.currentTarget;
//     const originalHTML = btn.innerHTML;
//     const originalColor = btn.style.color;
    
//     // Disable button and show loading
//     btn.style.opacity = '0.6';
//     btn.style.cursor = 'wait';
//     btn.innerHTML = `
//         <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="spin">
//             <circle cx="12" cy="12" r="10"/>
//             <path d="M12 2a10 10 0 1 0 10 10"/>
//         </svg>
//     `;
    
//     // Add spinning animation
//     const style = document.createElement('style');
//     style.textContent = `
//         .spin {
//             animation: spin 1s linear infinite;
//         }
//         @keyframes spin {
//             0% { transform: rotate(0deg); }
//             100% { transform: rotate(360deg); }
//         }
//     `;
//     document.head.appendChild(style);
    
//     // Create hidden iframe for download
//     const iframe = document.createElement('iframe');
//     iframe.style.display = 'none';
//     iframe.src = url;
//     document.body.appendChild(iframe);
    
//     // Reset button after 2 seconds
//     setTimeout(() => {
//         btn.style.opacity = '1';
//         btn.style.cursor = 'pointer';
//         btn.innerHTML = originalHTML;
//         btn.style.color = originalColor;
//         document.body.removeChild(iframe);
//     }, 2000);
    
//     // Also try direct download as backup
//     setTimeout(() => {
//         window.location.href = url;
//     }, 100);
// }
</script>
@endsection