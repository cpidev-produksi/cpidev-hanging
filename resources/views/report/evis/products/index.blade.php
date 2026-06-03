@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Master Produk Evisceration</div>
        <button type="button" class="topnav-link" onclick="openAddModal()" style="background: var(--accent); color: white; border-color: var(--accent);">
            + Tambah Produk
        </button>
    </div>

    <div class="panel-body">
        <div style="overflow-x: auto;">
            {{-- Search --}}
            <form method="GET" action="{{ route('product-evis.index') }}" style="margin-bottom: 14px;">
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <div style="flex:1; min-width: 240px;">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search Material Number / Nama Produk..."
                        style="width:100%; padding:10px 12px; border:1px solid var(--card-border); border-radius:8px; font-family:'Inter', sans-serif; font-size:13px;"
                    >
                    </div>

                    <button type="submit" class="topnav-link"
                    style="background: var(--accent); color: white; border-color: var(--accent);">
                    Search
                    </button>

                    @if(request('q'))
                    <a href="{{ route('product-evis.index') }}" class="topnav-link"
                        style="background: transparent; color: var(--text); border-color: var(--card-border);">
                        Reset
                    </a>
                    @endif
                </div>

                @if(request('q'))
                    <div style="margin-top:8px; font-size:12px; color: var(--text-muted);">
                    Menampilkan hasil untuk: <span style="font-weight:600; color:var(--text);">{{ request('q') }}</span>
                    </div>
                @endif
            </form>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 48px;">No.</th>
                        <th>Material Number</th>
                        <th>Product Name</th>
                        <th>Satuan per Bag</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td style="color: var(--text-muted);">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                            <td>
                                <code style="background: #f5f7fc; padding: 3px 8px; border-radius: 5px; font-size: 12px; border: 1px solid var(--card-border); font-family: 'Courier New', monospace;">{{ $product->material_number }}</code>
                            </td>
                            <td style="font-weight: 500;">{{ $product->name }}</td>
                            <td style=font-variant-numeric: tabular-nums;">
                                {{ number_format((float)($product->satuan ?? 0)) }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="topnav-link" style="padding: 5px 10px; font-size: 12px;"
                                        onclick="openEditModal({{ $product->id }}, '{{ addslashes($product->material_number) }}', '{{ addslashes($product->name) }}', '{{ (float)($product->satuan ?? 0) }}')">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('product-evis.destroy', $product) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="topnav-link"
                                                onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                                    style="
                                                    display: inline-flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    width: 32px;
                                                    height: 32px;
                                                    border-radius: 7px;
                                                    background: #FFF1F2;
                                                    color: #E11D48;
                                                    border: 1px solid #FECDD3;
                                                    cursor: pointer;
                                                    transition: background 0.15s;
                                                    font-family: inherit;
                                                "
                                                onmouseover="this.style.background='#FFE4E6'"
                                                onmouseout="this.style.background='#FFF1F2'"
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6M14 11v6"/>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px 12px;">
                                Belum ada produk. Silakan tambah produk terlebih dahulu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">

            {{-- Info teks: Menampilkan X–Y dari Z --}}
            <div style="font-size: 12px; color: var(--text-muted);">
                Menampilkan
                <span style="font-weight: 600; color: var(--text);">{{ $products->firstItem() }}</span>
                –
                <span style="font-weight: 600; color: var(--text);">{{ $products->lastItem() }}</span>
                dari
                <span style="font-weight: 600; color: var(--text);">{{ $products->total() }}</span>
                produk
            </div>

            {{-- Tombol halaman --}}
            <div style="display: flex; align-items: center; gap: 4px;">

                {{-- Tombol Previous --}}
                @if($products->onFirstPage())
                    <span style="
                        display: inline-flex; align-items: center; justify-content: center;
                        width: 32px; height: 32px; border-radius: 7px;
                        border: 1px solid var(--card-border);
                        color: var(--text-muted); font-size: 13px;
                        opacity: 0.4; cursor: not-allowed;
                    ">&#8592;</span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" style="
                        display: inline-flex; align-items: center; justify-content: center;
                        width: 32px; height: 32px; border-radius: 7px;
                        border: 1px solid var(--card-border);
                        color: var(--text-muted); font-size: 13px;
                        text-decoration: none; transition: background 0.15s, border-color 0.15s;
                    " onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background=''"
                    >&#8592;</a>
                @endif

                {{-- Nomor halaman --}}
                @foreach($products->links()->elements as $element)
                    @if(is_string($element))
                        {{-- Ellipsis --}}
                        <span style="
                            display: inline-flex; align-items: center; justify-content: center;
                            width: 32px; height: 32px; font-size: 13px;
                            color: var(--text-muted); letter-spacing: 1px;
                        ">{{ $element }}</span>
                    @elseif(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $products->currentPage())
                                <span style="
                                    display: inline-flex; align-items: center; justify-content: center;
                                    width: 32px; height: 32px; border-radius: 7px;
                                    background: var(--accent); color: white;
                                    font-size: 13px; font-weight: 600;
                                    border: 1px solid var(--accent);
                                ">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" style="
                                    display: inline-flex; align-items: center; justify-content: center;
                                    width: 32px; height: 32px; border-radius: 7px;
                                    border: 1px solid var(--card-border);
                                    color: var(--text); font-size: 13px;
                                    text-decoration: none; transition: background 0.15s, border-color 0.15s;
                                " onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background=''"
                                >{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" style="
                        display: inline-flex; align-items: center; justify-content: center;
                        width: 32px; height: 32px; border-radius: 7px;
                        border: 1px solid var(--card-border);
                        color: var(--text-muted); font-size: 13px;
                        text-decoration: none; transition: background 0.15s, border-color 0.15s;
                    " onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background=''"
                    >&#8594;</a>
                @else
                    <span style="
                        display: inline-flex; align-items: center; justify-content: center;
                        width: 32px; height: 32px; border-radius: 7px;
                        border: 1px solid var(--card-border);
                        color: var(--text-muted); font-size: 13px;
                        opacity: 0.4; cursor: not-allowed;
                    ">&#8594;</span>
                @endif

            </div>
        </div>
        @endif

    </div>
</div>

{{-- Modal Add/Edit --}}
<div id="productModal" class="logout-modal-overlay" role="dialog" aria-modal="true">
    <div class="logout-modal" style="max-width: 420px; text-align: left;">
        <div class="logout-modal-title" id="modalTitle" style="margin-bottom: 20px; text-align: left;">Tambah Produk Baru</div>

        <form id="productForm" method="POST" action="{{ route('product-evis.store') }}">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            <input type="hidden" id="productId" name="product_id" value="">
            <input type="hidden" id="redirectPage" name="redirect_page" value="{{ request('page', 1) }}">
            <input type="hidden" id="redirectQ" name="redirect_q" value="{{ request('q', '') }}">

            <div style="margin-bottom: 14px;">
                <label style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 5px;">
                    Material Number *
                </label>
                <input type="text" id="materialNumber" name="material_number"
                    placeholder="Contoh: MAT001"
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid var(--card-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px;">
                <small id="materialError" style="color: var(--error); display: none; font-size: 12px; margin-top: 4px;"></small>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 5px;">
                    Nama Produk *
                </label>
                <input type="text" id="productName" name="name"
                    placeholder="Contoh: Produk Frozen Grade A"
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid var(--card-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px;">
                <small id="nameError" style="color: var(--error); display: none; font-size: 12px; margin-top: 4px;"></small>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 5px;">
                    Satuan (Kg per Bag) *
                </label>
                <input type="number" step="0.01" min="0" id="productSatuan" name="satuan"
                    placeholder="Contoh: 10"
                    required
                    style="width: 100%; padding: 10px 12px; border: 1px solid var(--card-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px;">
                <small id="satuanError" style="color: var(--error); display: none; font-size: 12px; margin-top: 4px;"></small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-cancel" onclick="closeProductModal()" style="flex: 1;">Batal</button>
                <button type="submit" class="btn-confirm-logout" style="flex: 1; width: auto;">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Produk Baru';
    document.getElementById('productForm').action = '{{ route("product-evis.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('productId').value = '';
    document.getElementById('materialNumber').value = '';
    document.getElementById('materialNumber').removeAttribute('readonly');
    document.getElementById('productName').value = '';
    document.getElementById('productSatuan').value = '';
    document.getElementById('productModal').classList.add('open');
    document.getElementById('redirectPage').value = new URLSearchParams(window.location.search).get('page') || 1;
    document.getElementById('redirectQ').value = new URLSearchParams(window.location.search).get('q') || '';
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('materialNumber').focus(), 100);
}
function openEditModal(id, materialNumber, productName, satuan) {
    document.getElementById('modalTitle').textContent = 'Edit Produk';
    document.getElementById('productForm').action = '{{ route("product-evis.update", ":id") }}'.replace(':id', id);
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('productId').value = id;
    document.getElementById('materialNumber').value = materialNumber;
    document.getElementById('productName').value = productName;
    document.getElementById('productSatuan').value = (satuan ?? '');
    document.getElementById('productModal').classList.add('open');
    document.getElementById('redirectPage').value = new URLSearchParams(window.location.search).get('page') || 1;
    document.getElementById('redirectQ').value = new URLSearchParams(window.location.search).get('q') || '';
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('productName').focus(), 100);
}
function closeProductModal() {
    document.getElementById('productModal').classList.remove('open');
    document.body.style.overflow = '';
    document.getElementById('materialError').style.display = 'none';
    document.getElementById('nameError').style.display = 'none';
    const satuanErr = document.getElementById('satuanError');
    if (satuanErr) satuanErr.style.display = 'none';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeProductModal();
});
</script>
@endsection