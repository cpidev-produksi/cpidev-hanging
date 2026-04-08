@php
  /** @var \App\Models\MonitorControl $it */
@endphp

<div class="lst-row">
  {{-- Truck badge --}}
  <div class="lst-truck-badge">
    <span class="lst-truck-no">#{{ $it->truck_no ?? '–' }}</span>
  </div>

  {{-- Main info --}}
  <div class="lst-info">
    <div class="lst-info-top">
      <code class="lst-code">{{ $it->report_code }}</code>
      <span class="lst-status lst-status-{{ $it->status }}">{{ $it->status }}</span>
    </div>
    <div class="lst-info-bottom">
      <span class="lst-meta-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/>
          <path d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
        {{ $it->process_date?->format('d/m/Y') }}
      </span>
      <span class="lst-dot">·</span>
      <span class="lst-badge-shift">{{ strtoupper($it->shift) }}</span>
      <span class="lst-dot">·</span>

      {{-- SIZE AYAM --}}
      @if($it->size)
        <span class="lst-badge-size">
          <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5">
            <path d="M12 2v20"/>
            <path d="M7 6h10"/>
            <path d="M7 12h10"/>
            <path d="M7 18h10"/>
          </svg>
          {{ $it->size }}
        </span>
        <span class="lst-dot">·</span>
      @endif

      <span class="lst-meta-item">{{ $it->farm?->name ?? '–' }}</span>
      <span class="lst-dot">·</span>
      <span class="lst-meta-item">{{ $it->expedition?->name ?? '–' }}</span>
      <span class="lst-dot">·</span>
      <code class="lst-plate">{{ $it->plateNumber?->plate_number ?? '–' }}</code>
    </div>
  </div>

  {{-- Actions --}}
  <div class="lst-actions">
    <form method="POST" action="{{ route('hanging.open', $it) }}" style="display:inline">
      @csrf
      <button type="submit" class="lst-btn-open">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
        </svg>
        Buka
      </button>
    </form>

    @if($hf)
      @if($hfStatus === 'draft')
        <form method="POST" action="{{ route('hanging.start', $hf) }}" style="display:inline">
          @csrf
          <button type="submit" class="lst-btn-start"
                  onclick="return confirm('Mulai proses hanging untuk truk ini?')">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"><polygon points="6 3 20 12 6 21 6 3"/>
            </svg>
            Mulai
          </button>
        </form>
      @elseif($hfStatus === 'running')
        <a class="lst-btn-lanjut" href="{{ route('hanging-forms.show', $hf) }}">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5"><polygon points="6 3 20 12 6 21 6 3"/>
          </svg>
          Lanjutkan
        </a>
      @else
        <span class="lst-done-pill">
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.8"><polyline points="20 6 9 17 4 12"/>
          </svg>
          DONE
        </span>
      @endif
    @else
      <span class="lst-no-form">Belum ada form</span>
    @endif
  </div>
</div>