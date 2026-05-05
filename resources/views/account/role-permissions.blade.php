@extends('layouts.app')

@section('content')

<style>
  @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap');

  .rp-wrap * { box-sizing: border-box; margin: 0; padding: 0; }

  .rp-wrap {
    font-family: 'Sora', sans-serif;
    max-width: 560px;
    padding: 2rem 0;
  }

  .rp-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 1.75rem;
  }

  .rp-head-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1D9E75, #0F6E56);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .rp-head-icon svg {
    width: 18px;
    height: 18px;
    stroke: #fff;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .rp-head-title {
    font-size: 17px;
    font-weight: 700;
    color: #111;
  }

  .rp-head-sub {
    font-size: 12px;
    color: #6b7896;
    margin-top: 2px;
  }

  .rp-role-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fc;
    border-radius: 14px;
    padding: 10px 14px;
    margin-bottom: 1.5rem;
    border: 1px solid #e4e8f0;
  }

  .rp-role-label {
    font-size: 11px;
    font-weight: 700;
    color: #6b7896;
    text-transform: uppercase;
    letter-spacing: .06em;
    white-space: nowrap;
  }

  .rp-role-select {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 500;
    color: #111;
    outline: none;
    font-family: 'Sora', sans-serif;
    cursor: pointer;
  }

  .rp-section-label {
    font-size: 11px;
    font-weight: 700;
    color: #9aa3b5;
    text-transform: uppercase;
    letter-spacing: .1em;
    margin-bottom: 10px;
  }

  .rp-grid {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .rp-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid #e4e8f0;
    background: #fff;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: border-color .15s, background .15s;
  }

  .rp-card:hover {
    border-color: #c0c8d8;
  }

  .rp-card.active {
    border-color: #1D9E75;
  }

  .rp-card.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #1D9E75;
    border-radius: 2px;
  }

  .rp-check {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 1.5px solid #d0d5e0;
    background: #f5f6f9;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all .15s;
  }

  .rp-card.active .rp-check {
    background: #1D9E75;
    border-color: #1D9E75;
  }

  .rp-check svg {
    width: 11px;
    height: 11px;
    stroke: #fff;
    fill: none;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
    opacity: 0;
    transform: scale(.6);
    transition: all .15s;
  }

  .rp-card.active .rp-check svg {
    opacity: 1;
    transform: scale(1);
  }

  .rp-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .rp-icon svg {
    width: 16px;
    height: 16px;
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .rp-text { flex: 1; }

  .rp-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .rp-name {
    font-size: 12px;
    font-weight: 700;
    color: #111;
    text-transform: uppercase;
    letter-spacing: .04em;
  }

  .rp-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .rp-desc {
    font-size: 12px;
    color: #6b7896;
    margin-top: 3px;
  }

  .rp-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1.5rem;
  }

  .rp-count {
    font-size: 12px;
    color: #6b7896;
  }

  .rp-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1D9E75;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 11px 24px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'Sora', sans-serif;
    cursor: pointer;
    transition: opacity .15s;
    text-decoration: none;
  }

  .rp-btn:hover { opacity: .88; }

  .rp-btn svg {
    width: 14px;
    height: 14px;
    stroke: #fff;
    fill: none;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
  }
</style>

<div class="rp-wrap">

  {{-- Header --}}
  <div class="rp-head">
    <div class="rp-head-icon">
      <svg viewBox="0 0 24 24">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
    </div>
    <div>
      <div class="rp-head-title">Role Permissions</div>
      <div class="rp-head-sub">Slaughter House · File Inventory</div>
    </div>
  </div>

  @if(!$selectedRole)
    <div style="font-size:14px; color:#6b7896; padding:1rem 0;">Belum ada data role.</div>
  @else

    {{-- Role Selector --}}
    <form method="GET" action="{{ route('account.role-permissions.index') }}">
      <div class="rp-role-wrap">
        <div class="rp-role-label">Role</div>
        <select name="role_id" class="rp-role-select" onchange="this.form.submit()">
          @foreach($roles as $r)
            <option value="{{ $r->id }}" {{ $selectedRole->id === $r->id ? 'selected' : '' }}>
              {{ $r->name }} ({{ $r->slug }})
            </option>
          @endforeach
        </select>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7896" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </div>
    </form>

    {{-- Permissions Form --}}
    <form method="POST" action="{{ route('account.role-permissions.store') }}">
      @csrf
      <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">

      <div class="rp-section-label">Akses & Izin</div>

      @php
        $permMeta = [
          'view'    => ['label'=>'View',    'desc'=>'Lihat & download file',                          'badge'=>'Read',   'bg'=>'#E6F1FB','color'=>'#185FA5','icon'=>'eye'],
          'upload'  => ['label'=>'Upload',  'desc'=>'Upload file baru',                               'badge'=>'Write',  'bg'=>'#EAF3DE','color'=>'#3B6D11','icon'=>'upload'],
          'edit'    => ['label'=>'Edit',    'desc'=>'Buat folder, rename, move / cut / copy / paste', 'badge'=>'Modify', 'bg'=>'#FAEEDA','color'=>'#854F0B','icon'=>'edit'],
          'delete'  => ['label'=>'Delete',  'desc'=>'Hapus file (soft delete)',                       'badge'=>'Danger', 'bg'=>'#FCEBEB','color'=>'#A32D2D','icon'=>'trash'],
          'restore' => ['label'=>'Restore', 'desc'=>'Restore file dari trash',                        'badge'=>'Admin',  'bg'=>'#EEEDFE','color'=>'#533089','icon'=>'refresh'],
        ];
      @endphp

      <div class="rp-grid">
        @foreach($keys as $k)
          @php $m = $permMeta[$k] ?? []; $checked = $current[$k] ?? false; @endphp
          <label class="rp-card {{ $checked ? 'active' : '' }}" onclick="this.classList.toggle('active')">
            <input type="checkbox" name="perm[]" value="{{ $k }}" {{ $checked ? 'checked' : '' }} style="display:none"
              onchange="">

            <div class="rp-check">
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>

            <div class="rp-icon" style="background:{{ $m['bg'] ?? '#f0f0f0' }}">
              @if(($m['icon'] ?? '') === 'eye')
                <svg viewBox="0 0 24 24" stroke="{{ $m['color'] }}"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              @elseif(($m['icon'] ?? '') === 'upload')
                <svg viewBox="0 0 24 24" stroke="{{ $m['color'] }}"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
              @elseif(($m['icon'] ?? '') === 'edit')
                <svg viewBox="0 0 24 24" stroke="{{ $m['color'] }}"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              @elseif(($m['icon'] ?? '') === 'trash')
                <svg viewBox="0 0 24 24" stroke="{{ $m['color'] }}"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              @elseif(($m['icon'] ?? '') === 'refresh')
                <svg viewBox="0 0 24 24" stroke="{{ $m['color'] }}"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
              @endif
            </div>

            <div class="rp-text">
              <div class="rp-name-row">
                <div class="rp-name">{{ $m['label'] ?? $k }}</div>
                <div class="rp-badge" style="background:{{ $m['bg'] ?? '#eee' }}; color:{{ $m['color'] ?? '#333' }}">
                  {{ $m['badge'] ?? '' }}
                </div>
              </div>
              <div class="rp-desc">{{ $m['desc'] ?? '' }}</div>
            </div>
          </label>
        @endforeach
      </div>

      <div class="rp-footer">
        <div class="rp-count" id="rp-count-label">
          Dipilih: <strong id="rp-count">{{ collect($keys)->filter(fn($k) => $current[$k] ?? false)->count() }}</strong> dari {{ count($keys) }} izin
        </div>
        <button type="submit" class="rp-btn">
          <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          Simpan
        </button>
      </div>
    </form>

  @endif
</div>

<script>
  document.querySelectorAll('.rp-card').forEach(card => {
    card.addEventListener('click', function () {
      const cb = this.querySelector('input[type=checkbox]');
      if (cb) {
        cb.checked = !cb.checked;
        this.classList.toggle('active', cb.checked);
        updateCount();
      }
    });
  });

  function updateCount() {
    const total = document.querySelectorAll('.rp-card').length;
    const checked = document.querySelectorAll('.rp-card.active').length;
    const el = document.getElementById('rp-count');
    if (el) el.textContent = checked;
  }
</script>

@endsection