@if($list->isEmpty())
  <div class="lst-empty">
    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/>
    </svg>
    <p>Belum ada kontrol monitor aktif untuk <strong>{{ $location }}</strong>.</p>
  </div>
@else
  <div class="lst-grid">
    @foreach($list as $it)
      @php
        $hf       = $it->hangingForm;
        $hfStatus = $hf?->status;
      @endphp

      @include('transaction.hanging_landing.partials.list_row', [
        'it' => $it,
        'hf' => $hf,
        'hfStatus' => $hfStatus
      ])
    @endforeach
  </div>
@endif