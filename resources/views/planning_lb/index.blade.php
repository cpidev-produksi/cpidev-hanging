@extends('layouts.app')

@section('content')
<div class="pl-wrap">

  {{-- Header --}}
  <div class="pl-header">
    <div>
      <h1 class="pl-title">Planning Live Birds</h1>
      <p class="pl-sub">Planning Pemotongan Ayam per tanggal operasional</p>
    </div>
    <div class="pl-actions">
      <form method="GET" class="pl-filter">
        <input type="date" name="date" value="{{ $date ?? '' }}">
        <button type="submit" class="btn-filter">Filter</button>
        @if(!empty($date) && $date != now()->toDateString())
          <a href="{{ route('planning-lb.index') }}" class="btn-reset">Reset</a>
        @endif
      </form>
      <a href="{{ route('planning-lb.create') }}" class="btn-new">+ Buat Planning</a>
    </div>
  </div>

  {{-- Summary Cards --}}
  <div class="pl-summary">
    <div class="pl-stat">
      <div class="pl-stat-label">Total Planning</div>
      <div class="pl-stat-val">{{ $sh01->total() + $sh02->total() }}</div>
    </div>
    <div class="pl-stat">
      <div class="pl-stat-label">Lokasi SH01</div>
      <div class="pl-stat-val">{{ $sh01->total() }}</div>
    </div>
    <div class="pl-stat">
      <div class="pl-stat-label">Lokasi SH02</div>
      <div class="pl-stat-val">{{ $sh02->total() }}</div>
    </div>
    <div class="pl-stat">
      <div class="pl-stat-label">Total Ekor</div>
      <div class="pl-stat-val">{{ number_format($sh01->sum('total_plan_chicken') + $sh02->sum('total_plan_chicken')) }}</div>
    </div>
    <div class="pl-stat">
      <div class="pl-stat-label">Total Truk</div>
      <div class="pl-stat-val">{{ number_format($sh01->sum('total_plan_truck') + $sh02->sum('total_plan_truck')) }}</div>
    </div>
  </div>

  {{-- Card per Lokasi --}}
  @foreach(['sh01' => $sh01, 'sh02' => $sh02] as $locKey => $locItems)
    @php $loc = strtoupper($locKey); @endphp
    <div class="pl-loc-section">
      <div class="pl-loc-header">
        <span class="pl-badge pl-badge-{{ $locKey }}">{{ $loc }}</span>
        <span class="pl-loc-count">{{ $locItems->total() }} planning</span>
      </div>

      <div class="pl-card pl-card-{{ $locKey }}">
        <div class="pl-table-wrapper">
          <table class="pl-table">
            <thead>
              <tr>
                <th>Tanggal Operasional</th>
                <th>Plan Ekor</th>
                <th>Plan Truk</th>
                <th style="width: 180px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($locItems as $it)
                <tr>
                  <td class="date-cell">{{ $it->process_date?->format('d/m/Y') }}</td>
                  <td class="num">{{ number_format($it->total_plan_chicken) }}</td>
                  <td class="num">{{ number_format($it->total_plan_truck) }}</td>
                  <td class="action-cell">
                    <div class="pl-action-buttons">
                      <button class="btn-detail" type="button"
                        onclick="openShow({
                          id: {{ $it->id }},
                          location: '{{ $it->location }}',
                          process_date: '{{ $it->process_date?->format('d/m/Y') }}',
                          total_plan_chicken: '{{ number_format($it->total_plan_chicken) }}',
                          total_plan_truck: '{{ number_format($it->total_plan_truck) }}'
                        })">Detail</button>
                      <a href="{{ route('planning-lb.edit', ['planning_lb' => $it->id]) }}" class="btn-edit-link">Edit</a>
                      <button type="button" class="btn-del" onclick="confirmDeleteModal({{ $it->id }}, '{{ $it->process_date?->format('d/m/Y') }}', '{{ number_format($it->total_plan_chicken) }}')">Hapus</button>
                      <form id="delete-form-{{ $it->id }}" method="POST" action="{{ route('planning-lb.destroy', ['planning_lb' => $it->id]) }}" style="display: none;">
                        @csrf @method('DELETE')
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="pl-empty">Belum ada planning untuk lokasi ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($locItems->hasPages())
          <div class="pl-pagination">
            {{ $locItems->appends(['date' => $date ?? ''])->links() }}
          </div>
        @endif
      </div>
    </div>
  @endforeach

</div>

{{-- Modal Detail --}}
<div id="showModal" class="pl-overlay" style="display:none" onclick="closeShow(event)">
  <div class="pl-modal" onclick="event.stopPropagation()">
    <div class="pl-modal-head">
      <h2>Detail Planning LB</h2>
      <button class="pl-modal-close" onclick="closeShow()">&times;</button>
    </div>
    <div id="modalBody" class="pl-modal-body"></div>
    <div class="pl-modal-foot">
      <button class="btn-filter" onclick="closeShow()">Tutup</button>
      <a id="modalEditLink" href="#" class="btn-new">Edit</a>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="delModal" class="pl-overlay" style="display:none" onclick="closeDelModal(event)">
  <div class="pl-modal" onclick="event.stopPropagation()">
    <div class="pl-modal-head">
      <h2>Konfirmasi Hapus</h2>
      <button class="pl-modal-close" onclick="closeDelModal()">&times;</button>
    </div>
    <div class="pl-del-warning">
      ⚠️ Yakin ingin menghapus planning ini? Tindakan tidak dapat dibatalkan.
    </div>
    <div id="delBody" class="pl-modal-body"></div>
    <div class="pl-modal-foot">
      <button class="btn-filter" onclick="closeDelModal()">Batal</button>
      <button class="btn-del" id="delConfirmBtn">Hapus</button>
    </div>
  </div>
</div>

@push('styles')
<style>
/* ============================
   LAYOUT & RESET
============================= */
.pl-wrap { 
  padding: 1.5rem; 
  max-width: 1200px; 
  margin: 0 auto; 
}

/* Header */
.pl-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.pl-title { 
  font-size: 24px; 
  font-weight: 600; 
  color: #1a1a1a; 
  margin: 0;
}

.pl-sub { 
  font-size: 14px; 
  color: #6b7280; 
  margin-top: 4px; 
}

.pl-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.pl-filter {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pl-filter input[type=date] {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #374151;
  background: #fff;
}

/* Buttons */
.btn-filter, .btn-reset {
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
  background: #f9fafb;
  color: #374151;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s;
}

.btn-reset { color: #9ca3af; }
.btn-filter:hover, .btn-reset:hover { background: #f3f4f6; }

.btn-new {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
  border: none;
  background: #1D9E75;
  color: white;
  font-weight: 500;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s;
}

.btn-new:hover { background: #168a64; color: white; }

/* Summary Cards */
.pl-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
  margin-bottom: 2rem;
}

.pl-stat {
  background: #f9fafb;
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid #e5e7eb;
}

.pl-stat-label { 
  font-size: 12px; 
  color: #6b7280; 
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.pl-stat-val { 
  font-size: 24px; 
  font-weight: 600; 
  color: #1a1a1a; 
}

/* Lokasi Sections */
.pl-loc-section { 
  margin-bottom: 2rem; 
}

.pl-loc-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.pl-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

.pl-badge-sh01 { background: #DBEAFE; color: #1E40AF; }
.pl-badge-sh02 { background: #D1FAE5; color: #065F46; }

.pl-loc-count { 
  font-size: 13px; 
  color: #9ca3af; 
}

/* Card & Table */
.pl-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.pl-card-sh01 { border-left: 4px solid #3B82F6; }
.pl-card-sh02 { border-left: 4px solid #10B981; }

.pl-table-wrapper {
  overflow-x: auto;
}

.pl-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  min-width: 500px;
}

.pl-table thead tr { 
  background: #f9fafb; 
  border-bottom: 1px solid #e5e7eb;
}

.pl-table th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  font-size: 13px;
}

.pl-table td {
  padding: 12px 16px;
  color: #1a1a1a;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: middle;
}

.pl-table tbody tr:last-child td { border-bottom: none; }
.pl-table tbody tr:hover { background: #fafafa; }

.date-cell {
  font-weight: 500;
}

.num { 
  font-variant-numeric: tabular-nums;
  font-weight: 500;
}

.action-cell {
  padding: 8px 16px;
}

.pl-action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
  flex-wrap: wrap;
}

.btn-detail, .btn-edit-link, .btn-del {
  font-size: 12px;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  white-space: nowrap;
}

.btn-detail {
  border: 1px solid #93C5FD;
  color: #1D4ED8;
  background: #EFF6FF;
}

.btn-detail:hover { background: #DBEAFE; }

.btn-edit-link {
  border: 1px solid #d1d5db;
  color: #374151;
  background: #f9fafb;
}

.btn-edit-link:hover { background: #f3f4f6; }

.btn-del {
  border: 1px solid #FCA5A5;
  color: #DC2626;
  background: #FEF2F2;
}

.btn-del:hover { background: #FEE2E2; }

.pl-empty {
  padding: 3rem;
  text-align: center;
  color: #9ca3af;
  font-size: 14px;
}

/* Pagination */
.pl-pagination {
  padding: 1rem;
  border-top: 1px solid #f3f4f6;
}

.pl-pagination nav {
  display: flex;
  justify-content: center;
}

/* Modal */
.pl-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(2px);
}

.pl-modal {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 1.5rem;
  min-width: 320px;
  max-width: 450px;
  width: 90%;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.pl-modal-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #f3f4f6;
}

.pl-modal-head h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.pl-modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #9ca3af;
  line-height: 1;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.pl-modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.pl-modal-body .pl-modal-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #f3f4f6;
  font-size: 14px;
}

.pl-modal-body .pl-modal-row:last-child { border-bottom: none; }

.pl-modal-lbl { 
  color: #6b7280; 
  font-weight: 500;
}

.pl-modal-val { 
  font-weight: 600; 
  color: #1a1a1a; 
}

.pl-modal-foot {
  margin-top: 1.5rem;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.pl-del-warning {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
  font-size: 13px;
  color: #DC2626;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Session Messages */
.alert-success {
  background: #D1FAE5;
  border: 1px solid #6EE7B7;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 1.5rem;
  color: #065F46;
  font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script>
let pendingDeleteId = null;

function openShow(data) {
  const locBadge = data.location === 'SH01'
    ? '<span class="pl-badge pl-badge-sh01">SH01</span>'
    : '<span class="pl-badge pl-badge-sh02">SH02</span>';

  document.getElementById('modalBody').innerHTML = `
    <div class="pl-modal-row">
      <span class="pl-modal-lbl">Lokasi</span>
      <span class="pl-modal-val">${locBadge}</span>
    </div>
    <div class="pl-modal-row">
      <span class="pl-modal-lbl">Tanggal Operasional</span>
      <span class="pl-modal-val">${data.process_date}</span>
    </div>
    <div class="pl-modal-row">
      <span class="pl-modal-lbl">Total Plan Ekor</span>
      <span class="pl-modal-val">${data.total_plan_chicken} ekor</span>
    </div>
    <div class="pl-modal-row">
      <span class="pl-modal-lbl">Total Plan Truk</span>
      <span class="pl-modal-val">${data.total_plan_truck} truk</span>
    </div>
  `;

  // Perbaikan di sini - menggunakan route dengan parameter yang benar
  const editUrl = '{{ route("planning-lb.edit", ["planning_lb" => ":id"]) }}';
  document.getElementById('modalEditLink').href = editUrl.replace(':id', data.id);

  document.getElementById('showModal').style.display = 'flex';
}

function closeShow(e) {
  if (!e || e.target === document.getElementById('showModal') || e.target.classList?.contains('pl-modal-close')) {
    document.getElementById('showModal').style.display = 'none';
  }
}

function confirmDeleteModal(id, date, total) {
  pendingDeleteId = id;
  
  document.getElementById('delBody').innerHTML = `
    <div class="pl-modal-row">
      <span class="pl-modal-lbl">Tanggal</span>
      <span class="pl-modal-val">${date}</span>
    </div>
    <div class="pl-modal-row">
      <span class="pl-modal-lbl">Plan Ekor</span>
      <span class="pl-modal-val">${total} ekor</span>
    </div>
  `;

  document.getElementById('delModal').style.display = 'flex';
}

function closeDelModal(e) {
  if (!e || e.target === document.getElementById('delModal') || e.target.classList?.contains('pl-modal-close')) {
    document.getElementById('delModal').style.display = 'none';
    pendingDeleteId = null;
  }
}

document.getElementById('delConfirmBtn')?.addEventListener('click', function() {
  if (pendingDeleteId) {
    document.getElementById('delete-form-' + pendingDeleteId).submit();
  }
});

// Auto close session message after 3 seconds
setTimeout(() => {
  document.querySelectorAll('.alert-success').forEach(el => {
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  });
}, 3000);
</script>
@endpush
@endsection