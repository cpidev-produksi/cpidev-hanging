@php
  $hf     = $it->hangingForm;
  $filled = $hf && $hf->basket_condition && $hf->truck_platform_condition && $hf->feather_condition;
  $isDone = $hf && $hf->status === 'done';
@endphp

<div class="kl-row {{ $isDone ? 'kl-row-done' : '' }}">
  <div class="kl-truck-col">
    <div class="kl-truck-num">#{{ $it->truck_no ?? '–' }}</div>
  </div>

  <div class="kl-info">
    <div class="kl-info-top">
      <code class="kl-code">{{ $it->report_code }}</code>
      <span class="kl-pill {{ $filled ? 'kl-pill-ok' : 'kl-pill-warn' }}">
        {{ $filled ? 'Sudah Terisi' : 'Belum Diisi' }}
      </span>
      @if($isDone)
        <span class="kl-done-badge">DONE</span>
      @endif
    </div>
    <div class="kl-info-bottom">
      <span class="kl-meta">{{ $it->process_date?->format('d/m/Y') ?? '–' }}</span>
      <span class="kl-dot">·</span>
      <span class="kl-meta">{{ $it->expedition?->name ?? '–' }}</span>
      <span class="kl-dot">·</span>
      <code class="kl-plate">{{ $it->plateNumber?->plate_number ?? '–' }}</code>
      <span class="kl-dot">·</span>
      <span class="kl-meta">{{ $it->farm?->name ?? '–' }}</span>
    </div>
  </div>

  <div class="kl-actions">
    <form method="POST" action="{{ route('conditions.open', $it) }}" style="display:inline">
      @csrf
      <button type="submit"
              class="kl-btn {{ $isDone ? 'kl-btn-view' : ($filled ? 'kl-btn-edit' : 'kl-btn-input') }}">
        {{ $isDone ? 'Lihat' : ($filled ? 'Ubah Kondisi' : 'Input Kondisi') }}
      </button>
    </form>
  </div>
</div>