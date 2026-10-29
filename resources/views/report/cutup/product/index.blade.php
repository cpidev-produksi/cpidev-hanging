@extends('layouts.app')

@push('styles')
    @include('report.cutup._styles')
@endpush

@section('content')
@php
    $modal = old('_modal');          // 'create' | 'edit' (dipakai untuk membuka ulang modal jika validasi gagal)
    $modalId = old('_id');
@endphp

<div class="cu-wrap">

    <div class="cu-head">
        <div>
            <div class="cu-crumb">Form · Cut Up</div>
            <h1 class="cu-title">Daftar Produk</h1>
            <div class="cu-sub">Master nama produk yang dipakai pada Verifikasi Sensitivitas Metal Detector.</div>
        </div>
        <button type="button" class="cu-btn primary" onclick="openCreate()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Produk
        </button>
    </div>

    <div class="panel">
        <form method="GET" action="{{ route('cut-up.products.index') }}" class="cu-toolbar">
            <div class="f grow">
                <label class="cu-label" for="q">Cari produk</label>
                <input type="text" id="q" name="q" class="cu-input" value="{{ $search }}" placeholder="Ketik nama produk...">
            </div>
            <button type="submit" class="cu-btn">Cari</button>
            @if($search !== '')
                <a href="{{ route('cut-up.products.index') }}" class="cu-btn">Reset</a>
            @endif
            <div style="margin-left:auto;" class="cu-badge neutral">{{ $total }} produk</div>
        </form>

        <div class="cu-table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:70px;">No</th>
                        <th>Nama Produk</th>
                        <th style="width:170px;">Ditambahkan</th>
                        <th style="width:190px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $product)
                        <tr>
                            <td class="cu-mono">{{ $products->firstItem() + $i }}</td>
                            <td style="font-weight:700;">{{ $product->name }}</td>
                            <td class="cu-mono" style="color:var(--text-muted);">{{ $product->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="cu-actions">
                                    <button type="button" class="cu-btn sm"
                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            onclick="openEdit(this)">Edit</button>
                                    <button type="button" class="cu-btn sm danger"
                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            onclick="openDelete(this)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="cu-empty">
                                    <strong>{{ $search !== '' ? 'Produk tidak ditemukan' : 'Belum ada produk' }}</strong>
                                    {{ $search !== '' ? 'Coba kata kunci lain.' : 'Klik "Tambah Produk" untuk mulai mengisi daftar.' }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('report.cutup._pager', ['paginator' => $products])
    </div>
</div>

{{-- ============ Modal Create / Update ============ --}}
<div class="cu-overlay" id="productModal">
    <div class="cu-modal" role="dialog" aria-modal="true" aria-labelledby="productModalTitle">
        <form method="POST" id="productForm" action="{{ route('cut-up.products.store') }}">
            @csrf
            <input type="hidden" name="_method" id="productMethod" value="POST">
            <input type="hidden" name="_modal" id="productModalMode" value="create">
            <input type="hidden" name="_id" id="productModalId" value="">
            <input type="hidden" name="q" value="{{ $search }}">
            <input type="hidden" name="page" value="{{ $products->currentPage() }}">

            <div class="cu-modal-head">
                <div class="cu-modal-title" id="productModalTitle">Tambah Produk</div>
                <button type="button" class="cu-modal-x" onclick="closeModals()" aria-label="Tutup">&times;</button>
            </div>
            <div class="cu-modal-body">
                <label class="cu-label" for="productName">Nama Produk</label>
                <input type="text" id="productName" name="name" maxlength="150" class="cu-input @error('name') is-error @enderror"
                       value="{{ $modal ? old('name') : '' }}" placeholder="mis. Chicken Nugget 1 Kg" autocomplete="off" required>
                @error('name') <div class="cu-field-error">{{ $message }}</div> @enderror
            </div>
            <div class="cu-modal-foot">
                <button type="button" class="cu-btn" onclick="closeModals()">Batal</button>
                <button type="submit" class="cu-btn primary" id="productSubmit">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ============ Modal Delete ============ --}}
<div class="cu-overlay" id="deleteModal">
    <div class="cu-modal" role="dialog" aria-modal="true">
        <form method="POST" id="deleteForm" action="">
            @csrf
            @method('DELETE')
            <input type="hidden" name="q" value="{{ $search }}">
            <input type="hidden" name="page" value="{{ $products->currentPage() }}">

            <div class="cu-modal-head">
                <div class="cu-modal-title">Hapus Produk</div>
                <button type="button" class="cu-modal-x" onclick="closeModals()" aria-label="Tutup">&times;</button>
            </div>
            <div class="cu-modal-body">
                <p class="cu-modal-text">Yakin ingin menghapus produk <strong id="deleteName"></strong>?</p>
                <p class="cu-hint">Laporan Metal Detector yang sudah dibuat tidak terpengaruh — nama produk tetap tersimpan di laporan tersebut.</p>
            </div>
            <div class="cu-modal-foot">
                <button type="button" class="cu-btn" onclick="closeModals()">Batal</button>
                <button type="submit" class="cu-btn solid-danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const URL_STORE  = @json(route('cut-up.products.store'));
    const URL_UPDATE = @json(route('cut-up.products.update', '__ID__'));
    const URL_DELETE = @json(route('cut-up.products.destroy', '__ID__'));

    const $ = (id) => document.getElementById(id);

    function closeModals() {
        $('productModal').classList.remove('open');
        $('deleteModal').classList.remove('open');
        document.body.style.overflow = '';
    }
    function showModal(id) {
        $(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function openCreate() {
        $('productModalTitle').textContent = 'Tambah Produk';
        $('productForm').action = URL_STORE;
        $('productMethod').value = 'POST';
        $('productModalMode').value = 'create';
        $('productModalId').value = '';
        $('productName').value = '';
        showModal('productModal');
        setTimeout(() => $('productName').focus(), 50);
    }

    function openEdit(btn) {
        $('productModalTitle').textContent = 'Edit Produk';
        $('productForm').action = URL_UPDATE.replace('__ID__', btn.dataset.id);
        $('productMethod').value = 'PUT';
        $('productModalMode').value = 'edit';
        $('productModalId').value = btn.dataset.id;
        $('productName').value = btn.dataset.name;
        showModal('productModal');
        setTimeout(() => { $('productName').focus(); $('productName').select(); }, 50);
    }

    function openDelete(btn) {
        $('deleteForm').action = URL_DELETE.replace('__ID__', btn.dataset.id);
        $('deleteName').textContent = btn.dataset.name;
        showModal('deleteModal');
    }

    // Klik latar / ESC menutup modal
    document.querySelectorAll('.cu-overlay').forEach(o => o.addEventListener('click', e => { if (e.target === o) closeModals(); }));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModals(); });

    // Cegah submit ganda
    $('productForm').addEventListener('submit', () => { $('productSubmit').disabled = true; });

    // Buka ulang modal jika validasi server gagal
    @if($modal === 'create')
        openCreate(); $('productName').value = @json(old('name'));
    @elseif($modal === 'edit' && $modalId)
        $('productModalTitle').textContent = 'Edit Produk';
        $('productForm').action = URL_UPDATE.replace('__ID__', @json($modalId));
        $('productMethod').value = 'PUT';
        $('productModalMode').value = 'edit';
        $('productModalId').value = @json($modalId);
        $('productName').value = @json(old('name'));
        showModal('productModal');
    @endif
</script>
@endpush
