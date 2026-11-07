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
      <p class="hg-sub">Daftar hanging yang sudah selesai (DONE). Klik salah satu baris untuk melihat summary.</p>
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
          @if($canReview && !$group['allReviewed'])
            <form method="POST" action="{{ route('defeathering.hanging.bulk-review') }}" class="hg-inline-bulk"
                  onsubmit="return confirm('Review semua form yang belum direview pada bulan {{ $group['label'] }}?')">
              @csrf
              <input type="hidden" name="month" value="{{ $group['key'] }}">
              <input type="hidden" name="location" value="{{ $location }}">
              <input type="hidden" name="shift" value="{{ $shift }}">
              <button type="submit" class="hg-btn hg-btn-review">Review Semua — bulan ini</button>
            </form>
          @endif
          @if($canApprove && !$group['allApproved'])
            <form method="POST" action="{{ route('defeathering.hanging.bulk-approve') }}" class="hg-inline-bulk"
                  onsubmit="return confirm('Approve semua form yang belum disetujui pada bulan {{ $group['label'] }}?')">
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
                @if($canReview && !$day['allReviewed'])
                  <form method="POST" action="{{ route('defeathering.hanging.bulk-review') }}" class="hg-inline-bulk"
                        onsubmit="return confirm('Review semua form yang belum direview pada tanggal {{ $day['label'] }}?')">
                    @csrf
                    <input type="hidden" name="date" value="{{ $day['key'] }}">
                    <input type="hidden" name="location" value="{{ $location }}">
                    <input type="hidden" name="shift" value="{{ $shift }}">
                    <button type="submit" class="hg-btn hg-btn-review">Review Semua — hari ini</button>
                  </form>
                @endif
                @if($canApprove && !$day['allApproved'])
                  <form method="POST" action="{{ route('defeathering.hanging.bulk-approve') }}" class="hg-inline-bulk"
                        onsubmit="return confirm('Approve semua form yang belum disetujui pada tanggal {{ $day['label'] }}?')">
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
                          $qcComplete = !empty($form->basket_condition)
                              && !empty($form->truck_platform_condition)
                              && !empty($form->feather_condition);
                        @endphp
                        <tr class="hg-row"
                            tabindex="0"
                            data-summary="{{ route('monitor-controls.summary', $mc) }}"
                            data-title="{{ $mc->report_code }}"
                            data-pdf="{{ route('defeathering.hanging.pdf', $form) }}">
                          <td>{{ $mc->location }}</td>
                          <td>{{ ucfirst($mc->shift) }}</td>
                          <td>{{ $mc->truck_no }}</td>
                          <td>
                            <span class="hg-chip">{{ $mc->report_code }}</span>
                            @unless($qcComplete)
                              <span class="hg-badge hg-badge-warn" title="Summary baru bisa dibuka setelah QC Kondisi lengkap">QC belum lengkap</span>
                            @endunless
                          </td>
                          <td>{{ $mc->farm?->name ?? '—' }}</td>
                          <td>{{ $form->creator_name ?: '—' }}</td>
                          <td>
                            @if($form->known_at)
                              <div class="hg-who">{{ $form->known_name }}</div>
                              <div class="hg-when">{{ $form->known_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
                            @else
                              <span class="hg-badge">Belum direview</span>
                            @endif
                          </td>
                          <td>
                            @if($form->approved_at)
                              <div class="hg-who">{{ $form->approved_name }}</div>
                              <div class="hg-when">{{ $form->approved_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
                            @else
                              <span class="hg-badge">Belum disetujui</span>
                            @endif
                          </td>
                          <td class="hg-actions">
                            @if($canReview && !$form->known_at)
                              <form method="POST" action="{{ route('defeathering.hanging.review', $form) }}"
                                    onsubmit="return confirm('Review (Diketahui oleh) form {{ $mc->report_code }} sebagai {{ auth()->user()->name }}?')">
                                @csrf
                                <button type="submit" class="hg-btn hg-btn-review">Review</button>
                              </form>
                            @endif
                            @if($canApprove && !$form->approved_at)
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
  .hg-wrap { --hg-accent:#E85D2F; --hg-border:#E5E8F0; --hg-text:#1F2937; --hg-muted:#6B7280; max-width:1280px; margin:0 auto; }
  .hg-header { margin-bottom:16px; }
  .hg-title { margin:0; font-size:1.35rem; font-weight:800; color:var(--hg-text); }
  .hg-sub { margin:4px 0 0; font-size:.85rem; color:var(--hg-muted); font-weight:600; }

  .hg-card { background:#fff; border:1px solid var(--hg-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
  .hg-filter { display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px; padding:14px 16px; margin-bottom:14px; }
  .hg-filter label { display:flex; flex-direction:column; gap:4px; font-size:.72rem; font-weight:800; color:var(--hg-muted); text-transform:uppercase; letter-spacing:.06em; }
  .hg-filter input, .hg-filter select { padding:8px 10px; border:1.5px solid var(--hg-border); border-radius:9px; font:inherit; font-size:.85rem; color:var(--hg-text); background:#fff; }
  .hg-overview { display:flex; flex-wrap:wrap; align-items:center; gap:10px; padding:12px 16px; margin-bottom:14px; font-size:.85rem; }
  .hg-periods { display:grid; gap:10px; }
  .hg-month, .hg-day { background:#fff; border:1px solid var(--hg-border); border-radius:12px; overflow:hidden; }
  .hg-period-summary { display:flex; flex-wrap:wrap; align-items:center; gap:10px; padding:13px 16px; cursor:pointer; list-style:none; }
  .hg-period-summary::-webkit-details-marker { display:none; }
  .hg-period-summary::before { content:'›'; color:var(--hg-muted); font-size:1.2rem; font-weight:800; transition:transform .15s; }
  details[open] > .hg-period-summary::before { transform:rotate(90deg); }
  .hg-period-title { font-weight:800; color:var(--hg-text); }
  .hg-period-meta { margin-left:auto; color:var(--hg-muted); font-size:.8rem; font-weight:700; }
  .hg-month > .hg-period-summary { background:#F7F8FB; }
  .hg-day-summary { border-top:1px solid var(--hg-border); }
  .hg-month-body { padding:0 12px 12px; }
  .hg-day-body { padding:0 10px 12px; }
  .hg-inline-bulk { display:inline-block; margin:4px 6px 10px 0; }

  .hg-table-wrap { overflow-x:auto; }
  .hg-table { width:100%; border-collapse:collapse; font-size:.85rem; }
  .hg-table th { text-align:left; padding:11px 14px; font-size:.7rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; color:var(--hg-muted); background:#F7F8FB; border-bottom:1px solid var(--hg-border); white-space:nowrap; }
  .hg-table td { padding:11px 14px; border-bottom:1px solid #F0F2F7; vertical-align:middle; color:var(--hg-text); }
  .hg-row { cursor:pointer; transition:background .12s; }
  .hg-row:hover, .hg-row:focus { background:#FFF8F5; outline:none; }
  .hg-th-actions, .hg-actions { text-align:right; }
  .hg-actions { white-space:nowrap; }
  .hg-actions form { display:inline-block; margin:0 4px 0 0; }
  .hg-empty { text-align:center; padding:34px 14px !important; color:var(--hg-muted); font-weight:600; }

  .hg-chip { font-family:'Fira Code','Courier New',monospace; background:#F3F4F8; color:#4B5563; padding:2px 9px; border-radius:7px; font-size:.76rem; }
  .hg-badge { display:inline-block; padding:2px 9px; border-radius:999px; font-size:.7rem; font-weight:700; background:#F3F4F8; color:#6B7280; }
  .hg-badge-warn { background:rgba(245,159,0,.14); color:#92400E; margin-left:6px; }
  .hg-who { font-weight:700; }
  .hg-when { font-size:.72rem; color:var(--hg-muted); }

  .hg-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 13px; border:1.5px solid var(--hg-border); border-radius:9px; background:#fff; color:var(--hg-text); font:inherit; font-size:.8rem; font-weight:700; text-decoration:none; cursor:pointer; transition:all .15s; }
  .hg-btn:hover { border-color:#C5CAD8; }
  .hg-btn-primary { background:var(--hg-accent); border-color:var(--hg-accent); color:#fff; }
  .hg-btn-review  { background:#2563EB; border-color:#2563EB; color:#fff; }
  .hg-btn-approve { background:#059669; border-color:#059669; color:#fff; }
  .hg-badge-done { background:rgba(5,150,105,.12); color:#047857; }

  .hg-modal { position:fixed; inset:0; z-index:200; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(17,24,39,.55); }
  .hg-modal.open { display:flex; }
  .hg-modal-box { width:min(1100px,100%); height:min(88vh,900px); display:flex; flex-direction:column; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3); }
  .hg-modal-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:12px 16px; border-bottom:1px solid var(--hg-border); }
  .hg-modal-title { font-weight:800; display:flex; align-items:center; gap:8px; }
  .hg-modal-tools { display:flex; gap:8px; }
  .hg-frame { flex:1; width:100%; border:0; background:#F7F8FB; }
</style>
@endsection