@extends('layouts.app')

@section('content')
@php
  $slug       = auth()->user()?->role?->slug;
  $canReview  = $slug === 'foreman';
  $canApprove = $slug === 'supervisor';
@endphp

<div class="hg-wrap">

  <div class="hg-header">
    <div>
      <h1 class="hg-title">Form Hanging Ayam</h1>
      <p class="hg-sub">Klik salah satu baris untuk melihat summary.</p>
    </div>
  </div>

  {{-- Filter --}}
  <form method="GET" action="{{ route('defeathering.hanging.index') }}" class="hg-card hg-filter" id="hgFilters">
    <label>Harian
      <input type="date" name="date" value="{{ $date }}">
    </label>
    <label>Bulanan
      <input type="month" name="month" value="{{ $month ?? '' }}">
    </label>
    <label>Lokasi
      <select name="location">
        <option value="">Semua</option>
        @foreach(['SH01','SH02'] as $loc)
          <option value="{{ $loc }}" @selected($location === $loc)>{{ $loc }}</option>
        @endforeach
      </select>
    </label>
    <label>Shift
      <select name="shift">
        <option value="">Semua</option>
        <option value="pagi"  @selected($shift === 'pagi')>Pagi</option>
        <option value="malam" @selected($shift === 'malam')>Malam</option>
      </select>
    </label>
    <a href="{{ route('defeathering.hanging.index') }}" class="hg-btn">Reset</a>
  </form>

  @if($summary['total'] > 0)
    <div class="hg-card hg-overview">
      <strong>Ringkasan:</strong>
      <span>{{ $summary['total'] }} form</span>
      <span class="{{ $summary['allReviewed'] ? 'hg-badge hg-badge-done' : 'hg-badge hg-badge-warn' }}">
        {{ $summary['allReviewed'] ? 'Semua sudah direview' : ($summary['total'] - $summary['reviewed']) . ' belum direview' }}
      </span>
      <span class="{{ $summary['allApproved'] ? 'hg-badge hg-badge-done' : 'hg-badge hg-badge-warn' }}">
        {{ $summary['allApproved'] ? 'Semua sudah di-approve' : ($summary['total'] - $summary['approved']) . ' belum di-approve' }}
      </span>
    </div>
  @endif

  <div class="hg-periods">
    @forelse($groups as $group)
      <details class="hg-month">
        <summary class="hg-period-summary">
          <span class="hg-period-title">{{ $group['label'] }}</span>
          <span class="hg-period-meta">{{ $group['total'] }} form</span>
          <span class="{{ $group['allReviewed'] ? 'hg-badge hg-badge-done' : 'hg-badge hg-badge-warn' }}">
            {{ $group['allReviewed'] ? 'Semua direview' : ($group['total'] - $group['reviewed']) . ' belum review' }}
          </span>
          <span class="{{ $group['allApproved'] ? 'hg-badge hg-badge-done' : 'hg-badge hg-badge-warn' }}">
            {{ $group['allApproved'] ? 'Semua di-approve' : ($group['total'] - $group['approved']) . ' belum approve' }}
          </span>
        </summary>
        <div class="hg-month-body">
          @if($group['qcIncomplete'] > 0)
            <span class="hg-badge hg-badge-warn">{{ $group['qcIncomplete'] }} form QC belum lengkap</span>
          @endif
          @if($canReview && $group['qcReviewPending'] > 0)
            <form method="POST" action="{{ route('defeathering.hanging.bulk-review') }}" class="hg-inline-bulk"
                  onsubmit="return confirm('Review semua form yang belum direview dan QC-nya lengkap pada bulan {{ $group['label'] }}?')">
              @csrf
              <input type="hidden" name="month" value="{{ $group['key'] }}">
              <input type="hidden" name="location" value="{{ $location }}">
              <input type="hidden" name="shift" value="{{ $shift }}">
              <button type="submit" class="hg-btn hg-btn-review">Review Semua — bulan ini</button>
            </form>
          @endif
          @if($canApprove && $group['qcApprovePending'] > 0)
            <form method="POST" action="{{ route('defeathering.hanging.bulk-approve') }}" class="hg-inline-bulk"
                  onsubmit="return confirm('Approve semua form yang belum disetujui dan QC-nya lengkap pada bulan {{ $group['label'] }}?')">
              @csrf
              <input type="hidden" name="month" value="{{ $group['key'] }}">
              <input type="hidden" name="location" value="{{ $location }}">
              <input type="hidden" name="shift" value="{{ $shift }}">
              <button type="submit" class="hg-btn hg-btn-approve">Approve Semua — bulan ini</button>
            </form>
          @endif

          @foreach($group['dates'] as $day)
            <details class="hg-day">
              <summary class="hg-period-summary hg-day-summary">
                <span class="hg-period-title">{{ $day['label'] }}</span>
                <span class="hg-period-meta">{{ $day['total'] }} form</span>
                <span class="{{ $day['allReviewed'] ? 'hg-badge hg-badge-done' : 'hg-badge hg-badge-warn' }}">
                  {{ $day['allReviewed'] ? 'Semua direview' : ($day['total'] - $day['reviewed']) . ' belum review' }}
                </span>
                <span class="{{ $day['allApproved'] ? 'hg-badge hg-badge-done' : 'hg-badge hg-badge-warn' }}">
                  {{ $day['allApproved'] ? 'Semua di-approve' : ($day['total'] - $day['approved']) . ' belum approve' }}
                </span>
              </summary>
              <div class="hg-day-body">
                @if($day['qcIncomplete'] > 0)
                  <span class="hg-badge hg-badge-warn">{{ $day['qcIncomplete'] }} form QC belum lengkap</span>
                @endif
                @if($canReview && $day['qcReviewPending'] > 0)
                  <form method="POST" action="{{ route('defeathering.hanging.bulk-review') }}" class="hg-inline-bulk"
                        onsubmit="return confirm('Review semua form yang belum direview dan QC-nya lengkap pada tanggal {{ $day['label'] }}?')">
                    @csrf
                    <input type="hidden" name="date" value="{{ $day['key'] }}">
                    <input type="hidden" name="location" value="{{ $location }}">
                    <input type="hidden" name="shift" value="{{ $shift }}">
                    <button type="submit" class="hg-btn hg-btn-review">Review Semua — hari ini</button>
                  </form>
                @endif
                @if($canApprove && $day['qcApprovePending'] > 0)
                  <form method="POST" action="{{ route('defeathering.hanging.bulk-approve') }}" class="hg-inline-bulk"
                        onsubmit="return confirm('Approve semua form yang belum disetujui dan QC-nya lengkap pada tanggal {{ $day['label'] }}?')">
                    @csrf
                    <input type="hidden" name="date" value="{{ $day['key'] }}">
                    <input type="hidden" name="location" value="{{ $location }}">
                    <input type="hidden" name="shift" value="{{ $shift }}">
                    <button type="submit" class="hg-btn hg-btn-approve">Approve Semua — hari ini</button>
                  </form>
                @endif

                <div class="hg-card hg-table-wrap">
                  <table class="hg-table">
                    <thead>
                      <tr>
                        <th>Lokasi</th>
                        <th>Shift</th>
                        <th>Truk</th>
                        <th>Kode Laporan</th>
                        <th>Farm</th>
                        <th>Dibuat oleh</th>
                        <th>Diketahui oleh</th>
                        <th>Disetujui oleh</th>
                        <th class="hg-th-actions">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($day['forms'] as $form)
                        @php
                          $mc = $form->monitorControl;
                          $qcComplete = $form->hasCompleteQc();
                        @endphp
                        <tr class="hg-row"
                            tabindex="0"
                            data-summary="{{ route('monitor-controls.summary', ['monitorControl' => $mc, 'hanging_modal' => 1]) }}"
                            data-title="{{ $mc->report_code }}"
                            data-pdf="{{ route('defeathering.hanging.pdf', $form) }}">
                          <td data-label="Lokasi">{{ $mc->location }}</td>
                          <td data-label="Shift">{{ ucfirst($mc->shift) }}</td>
                          <td data-label="Truk">{{ $mc->truck_no }}</td>
                          <td data-label="Kode Laporan">
                            <span class="hg-chip">{{ $mc->report_code }}</span>
                            @unless($qcComplete)
                              <span class="hg-badge hg-badge-warn" title="Summary dapat dilihat, tetapi tidak dapat direview atau disetujui sampai QC lengkap">QC belum lengkap</span>
                            @endunless
                          </td>
                          <td data-label="Farm">{{ $mc->farm?->name ?? '—' }}</td>
                          <td data-label="Dibuat oleh">{{ $form->creator_name ?: '—' }}</td>
                          <td data-label="Diketahui oleh">
                            @if($form->known_at)
                              <div class="hg-who">{{ $form->known_name }}</div>
                              <div class="hg-when">{{ $form->known_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
                            @else
                              <span class="hg-badge">Belum direview</span>
                            @endif
                          </td>
                          <td data-label="Disetujui oleh">
                            @if($form->approved_at)
                              <div class="hg-who">{{ $form->approved_name }}</div>
                              <div class="hg-when">{{ $form->approved_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
                            @else
                              <span class="hg-badge">Belum disetujui</span>
                            @endif
                          </td>
                          <td class="hg-actions" data-label="Aksi">
                            @if(!$qcComplete)
                              <span class="hg-badge hg-badge-warn">Review/Approve diblokir</span>
                            @elseif($canReview && !$form->known_at)
                              <form method="POST" action="{{ route('defeathering.hanging.review', $form) }}"
                                    onsubmit="return confirm('Review (Diketahui oleh) form {{ $mc->report_code }} sebagai {{ auth()->user()->name }}?')">
                                @csrf
                                <button type="submit" class="hg-btn hg-btn-review">Review</button>
                              </form>
                            @endif
                            @if($qcComplete && $canApprove && !$form->approved_at)
                              <form method="POST" action="{{ route('defeathering.hanging.approve', $form) }}"
                                    onsubmit="return confirm('Approve (Disetujui oleh) form {{ $mc->report_code }} sebagai {{ auth()->user()->name }}?')">
                                @csrf
                                <button type="submit" class="hg-btn hg-btn-approve">Approve</button>
                              </form>
                            @endif
                            <a href="{{ route('defeathering.hanging.pdf', $form) }}" class="hg-btn">PDF</a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </details>
          @endforeach
        </div>
      </details>
    @empty
      <div class="hg-card hg-empty">Belum ada form hanging yang selesai untuk filter ini.</div>
    @endforelse
  </div>
  @if($groups->hasPages())
    <div class="hg-pagination">
      {{ $groups->onEachSide(1)->links() }}
    </div>
  @endif
</div>

{{-- Modal Summary --}}
<div id="hgModal" class="hg-modal" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="hg-modal-box">
    <div class="hg-modal-head">
      <div class="hg-modal-title">Summary <span id="hgModalTitle" class="hg-chip"></span></div>
      <div class="hg-modal-tools">
        <a id="hgModalOpen" href="#" target="_blank" rel="noopener" class="hg-btn">Buka halaman penuh</a>
        <a id="hgModalPdf" href="#" class="hg-btn">PDF</a>
        <button type="button" class="hg-btn" onclick="hgCloseModal()">Tutup</button>
      </div>
    </div>
    <iframe id="hgFrame" class="hg-frame" title="Summary"></iframe>
  </div>
</div>

<script>
(function () {
  const modal = document.getElementById('hgModal');
  const frame = document.getElementById('hgFrame');
  const filters = document.getElementById('hgFilters');
  const dateFilter = document.querySelector('input[name="date"]');
  const monthFilter = document.querySelector('input[name="month"]');

  dateFilter.addEventListener('change', function () {
    if (dateFilter.value) monthFilter.value = '';
    filters.requestSubmit();
  });
  monthFilter.addEventListener('change', function () {
    if (monthFilter.value) dateFilter.value = '';
    filters.requestSubmit();
  });
  filters.querySelectorAll('select').forEach(function (filter) {
    filter.addEventListener('change', function () { filters.requestSubmit(); });
  });

  // Sembunyikan navbar layout di dalam iframe supaya hanya isi summary yang tampil.
  frame.addEventListener('load', function () {
    try {
      const doc = frame.contentDocument;
      if (!doc || !doc.head) return;
      const st = doc.createElement('style');
      st.textContent = '.topbar,#mobileNavOverlay,#mobileNav,#logoutModal{display:none!important}'
                     + '.content-area{padding:14px!important}';
      doc.head.appendChild(st);
    } catch (e) { /* beda origin: abaikan */ }
  });

  function openModal(row) {
    document.getElementById('hgModalTitle').textContent = row.dataset.title || '';
    document.getElementById('hgModalOpen').href = row.dataset.summary;
    document.getElementById('hgModalPdf').href  = row.dataset.pdf;
    frame.src = row.dataset.summary;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  window.hgCloseModal = function () {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    frame.src = 'about:blank';
    document.body.style.overflow = '';
  };

  document.querySelectorAll('.hg-row').forEach(function (row) {
    row.addEventListener('click', function (e) {
      if (e.target.closest('a, button, form')) return; // aksi tidak membuka modal
      openModal(row);
    });
    row.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && e.target === row) openModal(row);
    });
  }); 

  modal.addEventListener('click', function (e) { if (e.target === modal) hgCloseModal(); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('open')) hgCloseModal();
  });
})();
</script>

<style>
  .hg-wrap { --hg-ink:var(--text-main,#0d1117); --hg-muted:var(--text-muted,#6b7896); --hg-line:var(--card-border,#e4e8f0); --hg-accent:var(--accent,#e85d2f); --hg-soft:#f6f8fc; --hg-hover:#fafbfe; max-width:1440px; margin:0 auto; font-family:'Inter',system-ui,sans-serif; color:var(--hg-ink); }
  .hg-header { display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:14px 24px; margin-bottom:22px; }
  .hg-title { margin:0; font-size:26px; font-weight:800; letter-spacing:-.025em; line-height:1.15; }
  .hg-sub { margin:6px 0 0; max-width:56ch; font-size:14px; line-height:1.5; color:var(--hg-muted); }

  .hg-card { background:#fff; border:1px solid var(--hg-line); border-radius:16px; }
  .hg-filter { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)) auto; gap:12px; align-items:end; padding:14px 16px; margin-bottom:26px; }
  .hg-filter label { display:block; min-width:0; font-size:12px; font-weight:600; color:var(--hg-muted); }
  .hg-filter input, .hg-filter select { display:block; width:100%; min-height:40px; margin-top:6px; padding:9px 12px; border:1px solid var(--hg-line); border-radius:10px; font:500 13.5px 'Inter',sans-serif; color:var(--hg-ink); background:#fff; transition:border-color .15s,box-shadow .15s; }
  .hg-filter input:focus, .hg-filter select:focus { outline:none; border-color:var(--hg-accent); box-shadow:0 0 0 3px rgba(232,93,47,.15); }
  .hg-filter > .hg-btn { min-height:40px; }
  .hg-overview { display:flex; flex-wrap:wrap; align-items:center; gap:10px; padding:14px 16px; margin:-12px 0 14px; font-size:13px; }
  .hg-periods { display:grid; gap:14px; }
  .hg-pagination { margin-top:20px; }
  .hg-month, .hg-day { background:#fff; border:1px solid var(--hg-line); border-radius:16px; overflow:hidden; }
  .hg-period-summary { display:flex; flex-wrap:wrap; align-items:center; gap:10px; padding:15px 18px; cursor:pointer; list-style:none; }
  .hg-period-summary::-webkit-details-marker { display:none; }
  .hg-period-summary::before { content:'›'; color:var(--hg-muted); font-size:1.2rem; font-weight:800; transition:transform .15s; }
  details[open] > .hg-period-summary::before { transform:rotate(90deg); }
  .hg-period-title { font-size:14px; font-weight:700; letter-spacing:-.01em; }
  .hg-period-meta { margin-left:auto; color:var(--hg-muted); font-size:12px; font-weight:600; }
  .hg-month > .hg-period-summary { background:var(--hg-soft); }
  .hg-day-summary { border-top:1px solid var(--hg-line); }
  .hg-month-body { padding:0 14px 14px; }
  .hg-day-body { padding:0 10px 14px; }
  .hg-inline-bulk { display:inline-block; margin:4px 6px 10px 0; }

  .hg-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
  .hg-table { width:100%; border-collapse:collapse; font-size:13px; font-variant-numeric:tabular-nums; }
  .hg-table th, .hg-table td { padding:12px 14px; border-bottom:1px solid var(--hg-line); text-align:left; vertical-align:middle; }
  .hg-table th { background:var(--hg-soft); font-size:12px; font-weight:600; color:var(--hg-muted); white-space:nowrap; }
  .hg-table tbody tr:last-child td { border-bottom:0; }
  .hg-row { cursor:pointer; transition:background-color .15s; }
  .hg-row:hover td, .hg-row:focus td { background:var(--hg-hover); }
  .hg-row:focus-visible { outline:2px solid var(--hg-accent); outline-offset:-2px; }
  .hg-th-actions, .hg-actions { text-align:right !important; }
  .hg-actions { white-space:nowrap; }
  .hg-actions form { display:inline-block; margin:0 4px 0 0; }
  .hg-empty { padding:44px 16px !important; text-align:center; font-size:14px; color:var(--hg-muted); border-style:dashed; }

  .hg-chip { display:inline-flex; align-items:center; padding:2px 9px; border:1px solid var(--hg-line); border-radius:999px; background:var(--hg-soft); color:var(--hg-ink); font:600 11.5px 'Fira Code','Courier New',monospace; }
  .hg-badge { display:inline-flex; align-items:center; gap:6px; padding:3px 10px 3px 8px; border:1px solid var(--hg-line); border-radius:999px; background:var(--hg-soft); color:var(--hg-muted); font-size:12px; font-weight:600; white-space:nowrap; }
  .hg-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
  .hg-badge-warn { border-color:#fde68a; background:#fef9c3; color:#92400e; }
  .hg-badge-done { border-color:rgba(16,185,129,.25); background:rgba(16,185,129,.10); color:#047857; }
  .hg-who { font-weight:600; }
  .hg-when { font-size:12px; color:var(--hg-muted); }

  .hg-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:9px 15px; border:1px solid var(--hg-line); border-radius:10px; background:#fff; color:var(--hg-ink); font:600 13px/1.2 'Inter',sans-serif; text-decoration:none; cursor:pointer; transition:background-color .15s,border-color .15s,color .15s; }
  .hg-btn:hover { border-color:#cfd6e4; background:var(--hg-soft); }
  .hg-btn:focus-visible { outline:2px solid var(--hg-accent); outline-offset:2px; }
  .hg-btn-review, .hg-btn-approve { border-color:var(--hg-accent); background:var(--hg-accent); color:#fff; box-shadow:0 1px 2px rgba(232,93,47,.3); }
  .hg-btn-review:hover, .hg-btn-approve:hover { border-color:#d44f24; background:#d44f24; }

  .hg-modal { position:fixed; inset:0; z-index:200; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(13,17,23,.5); }
  .hg-modal.open { display:flex; }
  .hg-modal-box { width:min(1100px,100%); height:min(88vh,900px); display:flex; flex-direction:column; overflow:hidden; border:1px solid var(--hg-line); border-radius:18px; background:#fff; box-shadow:0 24px 60px rgba(15,23,42,.28); }
  .hg-modal-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:14px 18px; border-bottom:1px solid var(--hg-line); }
  .hg-modal-title { display:flex; align-items:center; gap:8px; font-weight:700; }
  .hg-modal-tools { display:flex; flex-wrap:wrap; gap:8px; }
  .hg-frame { flex:1; width:100%; border:0; background:var(--hg-soft); }

  @media (max-width:980px) {
    .hg-filter { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .hg-filter > .hg-btn { grid-column:1 / -1; }
  }
  @media (max-width:720px) {
    .hg-period-summary { gap:8px; padding:13px 14px; }
    .hg-period-meta { margin-left:0; }
    .hg-month-body { padding:0 8px 10px; }
    .hg-day-body { padding:0 6px 10px; }
    .hg-table, .hg-table tbody, .hg-table tr, .hg-table td { display:block; width:100%; }
    .hg-table thead { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0 0 0 0); }
    .hg-table tbody tr { padding:12px 16px; border-bottom:1px solid var(--hg-line); }
    .hg-table tbody tr:last-child { border-bottom:0; }
    .hg-table td { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:6px 0; border:0; white-space:normal; text-align:right; }
    .hg-table td::before { content:attr(data-label); flex:none; font-size:12px; font-weight:600; color:var(--hg-muted); text-align:left; }
    .hg-table td.hg-actions { display:flex; flex-wrap:wrap; justify-content:flex-start; padding-top:10px; text-align:left !important; }
    .hg-table td.hg-actions::before { content:none; }
  }
  @media (max-width:480px) {
    .hg-title { font-size:21px; }
    .hg-filter { grid-template-columns:1fr; }
    .hg-filter > .hg-btn { grid-column:auto; }
    .hg-overview { align-items:flex-start; }
    .hg-modal { padding:10px; }
    .hg-modal-box { height:92vh; }
    .hg-modal-tools { width:100%; }
    .hg-modal-tools .hg-btn { flex:1; }
  }
  @media (prefers-reduced-motion:reduce) {
    .hg-btn, .hg-filter input, .hg-filter select, .hg-period-summary::before { transition:none; }
  }
</style>
@endsection