@if ($paginator->total() > 0)
    <div class="cu-pager">
        <div>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data</div>
        @if ($paginator->hasPages())
            <div class="pg">
                @if ($paginator->onFirstPage())
                    <span class="cu-btn sm" style="opacity:.5; cursor:default;">‹ Sebelumnya</span>
                @else
                    <a class="cu-btn sm" href="{{ $paginator->previousPageUrl() }}">‹ Sebelumnya</a>
                @endif
                <span class="cu-btn sm" style="cursor:default;">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
                @if ($paginator->hasMorePages())
                    <a class="cu-btn sm" href="{{ $paginator->nextPageUrl() }}">Berikutnya ›</a>
                @else
                    <span class="cu-btn sm" style="opacity:.5; cursor:default;">Berikutnya ›</span>
                @endif
            </div>
        @endif
    </div>
@endif