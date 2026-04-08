@if($list->isNotEmpty())
  <div class="lst-head" style="margin-top:12px;background:#F9FAFB;border:1px dashed #E2E5EE;border-radius:10px">
    <div class="lst-head-left">
      <div class="lst-loc-badge" style="background:#E8F7EF;color:#065F46">DONE</div>
      <span class="lst-head-title">Selesai</span>
    </div>
    <div class="lst-total-pill">{{ $list->count() }} Truk</div>
  </div>

  <div class="lst-grid" style="margin-top:8px;opacity:.7">
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