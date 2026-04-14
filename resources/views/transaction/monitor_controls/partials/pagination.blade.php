@php /** @var \Illuminate\Pagination\LengthAwarePaginator $list */ @endphp

@if($list->hasPages())
  <div class="mc-pagination">
    <p class="mc-pagination-info">
      Menampilkan
      <span class="mc-pagination-highlight">{{ $list->firstItem() }}–{{ $list->lastItem() }}</span>
      dari
      <span class="mc-pagination-highlight">{{ $list->total() }}</span>
      data
    </p>

    <div class="mc-pagination-nav">
      {{-- Prev --}}
      @if($list->onFirstPage())
        <span class="mc-page-btn mc-page-btn--disabled" aria-disabled="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </span>
      @else
        <a href="{{ $list->previousPageUrl() }}" class="mc-page-btn" title="Halaman sebelumnya">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      @endif

      {{-- Page Numbers --}}
      @php
        $cur   = $list->currentPage();
        $last  = $list->lastPage();
        $pages = collect(range(1, $last))
            ->filter(fn($p) => $p === 1 || $p === $last || abs($p - $cur) <= 2)
            ->values();
      @endphp

      @foreach($pages as $i => $page)
        @if($i > 0 && $page - $pages[$i - 1] > 1)
          <span class="mc-page-ellipsis">…</span>
        @endif

        @if($page === $cur)
          <span class="mc-page-btn mc-page-btn--active" aria-current="page">{{ $page }}</span>
        @else
          <a href="{{ $list->url($page) }}" class="mc-page-btn">{{ $page }}</a>
        @endif
      @endforeach

      {{-- Next --}}
      @if($list->hasMorePages())
        <a href="{{ $list->nextPageUrl() }}" class="mc-page-btn" title="Halaman berikutnya">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      @else
        <span class="mc-page-btn mc-page-btn--disabled" aria-disabled="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      @endif
    </div>
  </div>
@endif