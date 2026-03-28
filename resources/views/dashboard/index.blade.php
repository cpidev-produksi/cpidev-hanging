@extends('layouts.app')

@section('content')
<style>
    .dash-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    @media (max-width: 1100px) { .dash-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .dash-grid { grid-template-columns: 1fr; } }

    /* Stat Cards */
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid #e4e8f0;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 16px 16px 0 0;
    }
    .stat-card.green::before  { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card.blue::before   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-card.orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .stat-card.red::before    { background: linear-gradient(90deg, #ef4444, #f87171); }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8090b0;
        margin-bottom: 6px;
    }
    .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 36px;
        font-weight: 800;
        color: #0d1117;
        line-height: 1;
        letter-spacing: -1px;
    }
    .stat-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.green  { background: rgba(16,185,129,0.1); color: #10b981; }
    .stat-icon.blue   { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .stat-icon.orange { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .stat-icon.purple { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .stat-icon.red    { background: rgba(239,68,68,0.1);  color: #ef4444; }

    .stat-footer {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #8090b0;
    }
    .stat-trend {
        font-weight: 600;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 2px;
    }
    .stat-trend.up   { color: #10b981; }
    .stat-trend.down { color: #ef4444; }
    .stat-trend.neutral { color: #8090b0; }

    /* Progress bar inside card */
    .stat-progress {
        height: 4px;
        background: #f0f2f7;
        border-radius: 100px;
        margin-top: 14px;
        overflow: hidden;
    }
    .stat-progress-bar {
        height: 100%;
        border-radius: 100px;
        transition: width 1s ease;
    }
    .green .stat-progress-bar  { background: linear-gradient(90deg, #10b981, #34d399); }
    .blue .stat-progress-bar   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .orange .stat-progress-bar { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .purple .stat-progress-bar { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    /* Bottom row */
    .dash-bottom {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 16px;
    }
    @media (max-width: 900px) { .dash-bottom { grid-template-columns: 1fr; } }

    /* Panel */
    .panel {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e4e8f0;
        overflow: hidden;
    }
    .panel-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f0f2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .panel-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #0d1117;
    }
    .panel-action {
        font-size: 12px;
        color: #e85d2f;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .panel-action:hover { opacity: 0.8; }

    /* Project Table */
    .project-table { width: 100%; border-collapse: collapse; }
    .project-table th {
        text-align: left;
        padding: 10px 22px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8090b0;
        background: #f8f9fc;
        border-bottom: 1px solid #f0f2f7;
    }
    .project-table td {
        padding: 14px 22px;
        border-bottom: 1px solid #f5f7fc;
        font-size: 13px;
        vertical-align: middle;
    }
    .project-table tr:last-child td { border-bottom: none; }
    .project-table tr:hover td { background: #fafbff; }

    .proj-name {
        font-weight: 600;
        color: #0d1117;
        margin-bottom: 2px;
    }
    .proj-meta { font-size: 11px; color: #8090b0; }

    /* Status badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge.running  { background: rgba(245,158,11,0.1); color: #b45309; }
    .badge.running .badge-dot { background: #f59e0b; }
    .badge.done     { background: rgba(16,185,129,0.1); color: #065f46; }
    .badge.done .badge-dot { background: #10b981; }
    .badge.pending  { background: rgba(139,92,246,0.1); color: #5b21b6; }
    .badge.pending .badge-dot { background: #8b5cf6; }
    .badge.stopped  { background: rgba(239,68,68,0.1); color: #991b1b; }
    .badge.stopped .badge-dot { background: #ef4444; }

    /* Progress col */
    .prog-wrap { min-width: 100px; }
    .prog-label {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #8090b0;
        margin-bottom: 4px;
    }
    .prog-bar {
        height: 6px;
        background: #f0f2f7;
        border-radius: 100px;
        overflow: hidden;
    }
    .prog-fill {
        height: 100%;
        border-radius: 100px;
    }
    .prog-fill.green  { background: linear-gradient(90deg, #10b981, #34d399); }
    .prog-fill.blue   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .prog-fill.orange { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .prog-fill.purple { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    /* Activity Feed */
    .activity-list { padding: 8px 0; }
    .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px 22px;
        transition: background 0.15s;
    }
    .activity-item:hover { background: #fafbff; }
    .activity-dot-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
        flex-shrink: 0;
        padding-top: 3px;
    }
    .activity-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .activity-line {
        width: 1px;
        flex: 1;
        min-height: 20px;
        background: #e4e8f0;
        margin-top: 4px;
    }
    .activity-item:last-child .activity-line { display: none; }
    .activity-content { flex: 1; }
    .activity-text {
        font-size: 12px;
        color: #2d3748;
        line-height: 1.5;
    }
    .activity-text strong { color: #0d1117; font-weight: 600; }
    .activity-time {
        font-size: 10px;
        color: #8090b0;
        margin-top: 2px;
    }

    /* Header area */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .page-header-left h2 {
        font-family: 'Syne', sans-serif;
        font-size: 24px;
        font-weight: 800;
        color: #0d1117;
        letter-spacing: -0.5px;
    }
    .page-header-left p {
        font-size: 13px;
        color: #8090b0;
        margin-top: 3px;
    }
    .header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s;
        border: none;
        font-family: 'DM Sans', sans-serif;
        white-space: nowrap;
        text-decoration: none;
    }
    .btn-primary {
        background: linear-gradient(135deg, #e85d2f, #c94820);
        color: white;
        box-shadow: 0 3px 14px rgba(232,93,47,0.25);
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(232,93,47,0.35);
    }
    .btn-secondary {
        background: #fff;
        color: #4a5577;
        border: 1px solid #e4e8f0;
    }
    .btn-secondary:hover { background: #f5f7fc; }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h2>Dashboard</h2>
        <p>Selamat datang kembali! Berikut ringkasan operasional hari ini.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="dash-grid">
    <!-- Total -->
    <div class="stat-card green">
        <div class="stat-card-top">
            <div>
                <div class="stat-label">Total Project</div>
                <div class="stat-value">24</div>
            </div>
            <div class="stat-icon green">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-trend up">↑ 2.5%</span>
            <span>dari bulan lalu</span>
        </div>
        <div class="stat-progress"><div class="stat-progress-bar" style="width:75%"></div></div>
    </div>

    <!-- Ended -->
    <div class="stat-card blue">
        <div class="stat-card-top">
            <div>
                <div class="stat-label">Selesai</div>
                <div class="stat-value">10</div>
            </div>
            <div class="stat-icon blue">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-trend up">↑ 1.2%</span>
            <span>dari bulan lalu</span>
        </div>
        <div class="stat-progress"><div class="stat-progress-bar" style="width:42%"></div></div>
    </div>

    <!-- Running -->
    <div class="stat-card orange">
        <div class="stat-card-top">
            <div>
                <div class="stat-label">Berjalan</div>
                <div class="stat-value">12</div>
            </div>
            <div class="stat-icon orange">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-trend up">↑ 3.1%</span>
            <span>dari bulan lalu</span>
        </div>
        <div class="stat-progress"><div class="stat-progress-bar" style="width:50%"></div></div>
    </div>

    <!-- Pending -->
    <div class="stat-card purple">
        <div class="stat-card-top">
            <div>
                <div class="stat-label">Pending</div>
                <div class="stat-value">2</div>
            </div>
            <div class="stat-icon purple">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-footer">
            <span class="stat-trend neutral">—</span>
            <span>Dalam diskusi</span>
        </div>
        <div class="stat-progress"><div class="stat-progress-bar" style="width:8%"></div></div>
    </div>
</div>

<!-- Bottom Row -->
<div class="dash-bottom">
    <!-- Projects Table -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Daftar Project Aktif</span>
            <a href="#" class="panel-action">
                Lihat Semua
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <table class="project-table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Status</th>
                    <th>Progres</th>
                    <th>Farm</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="proj-name">SH01 — Batch Maret</div>
                        <div class="proj-meta">Dimulai 10 Mar 2025</div>
                    </td>
                    <td><span class="badge running"><span class="badge-dot"></span>Berjalan</span></td>
                    <td>
                        <div class="prog-wrap">
                            <div class="prog-label"><span>72%</span></div>
                            <div class="prog-bar"><div class="prog-fill orange" style="width:72%"></div></div>
                        </div>
                    </td>
                    <td><span style="font-size:12px;color:#4a5577">Farm Unggas A</span></td>
                </tr>
                <tr>
                    <td>
                        <div class="proj-name">SH02 — Batch Februari</div>
                        <div class="proj-meta">Dimulai 1 Feb 2025</div>
                    </td>
                    <td><span class="badge done"><span class="badge-dot"></span>Selesai</span></td>
                    <td>
                        <div class="prog-wrap">
                            <div class="prog-label"><span>100%</span></div>
                            <div class="prog-bar"><div class="prog-fill green" style="width:100%"></div></div>
                        </div>
                    </td>
                    <td><span style="font-size:12px;color:#4a5577">Farm Unggas B</span></td>
                </tr>
                <tr>
                    <td>
                        <div class="proj-name">SH01 — Batch April</div>
                        <div class="proj-meta">Dimulai 1 Apr 2025</div>
                    </td>
                    <td><span class="badge pending"><span class="badge-dot"></span>Pending</span></td>
                    <td>
                        <div class="prog-wrap">
                            <div class="prog-label"><span>0%</span></div>
                            <div class="prog-bar"><div class="prog-fill purple" style="width:0%"></div></div>
                        </div>
                    </td>
                    <td><span style="font-size:12px;color:#4a5577">Farm Unggas C</span></td>
                </tr>
                <tr>
                    <td>
                        <div class="proj-name">SH02 — Batch Maret</div>
                        <div class="proj-meta">Dimulai 15 Mar 2025</div>
                    </td>
                    <td><span class="badge running"><span class="badge-dot"></span>Berjalan</span></td>
                    <td>
                        <div class="prog-wrap">
                            <div class="prog-label"><span>45%</span></div>
                            <div class="prog-bar"><div class="prog-fill blue" style="width:45%"></div></div>
                        </div>
                    </td>
                    <td><span style="font-size:12px;color:#4a5577">Farm Unggas A</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Activity Feed -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Aktivitas Terbaru</span>
            <a href="#" class="panel-action">Semua</a>
        </div>
        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#10b981"></div>
                    <div class="activity-line"></div>
                </div>
                <div class="activity-content">
                    <div class="activity-text"><strong>SH02 Batch Feb</strong> telah selesai diproses</div>
                    <div class="activity-time">5 menit lalu</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#3b82f6"></div>
                    <div class="activity-line"></div>
                </div>
                <div class="activity-content">
                    <div class="activity-text">Truk <strong>B 1234 XY</strong> terdaftar ke ekspedisi</div>
                    <div class="activity-time">32 menit lalu</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#f59e0b"></div>
                    <div class="activity-line"></div>
                </div>
                <div class="activity-content">
                    <div class="activity-text">Monitor <strong>SH01</strong> diperbarui secara otomatis</div>
                    <div class="activity-time">1 jam lalu</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#8b5cf6"></div>
                    <div class="activity-line"></div>
                </div>
                <div class="activity-content">
                    <div class="activity-text">User <strong>operator_1</strong> ditambahkan ke sistem</div>
                    <div class="activity-time">3 jam lalu</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:#e85d2f"></div>
                    <div class="activity-line"></div>
                </div>
                <div class="activity-content">
                    <div class="activity-text">Project <strong>SH01 Batch April</strong> dibuat baru</div>
                    <div class="activity-time">5 jam lalu</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Animate progress bars on load
    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.stat-progress-bar, .prog-fill');
        bars.forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => { bar.style.width = w; }, 100);
        });
    });
</script>
@endsection