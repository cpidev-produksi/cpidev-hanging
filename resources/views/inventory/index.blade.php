@extends('layouts.app')

@section('content')
<div class="panel">
  <div class="panel-header">
    <div class="panel-title">Slaughter House File Inventory</div>
    <div style="display:flex; gap:10px; align-items:center;">
      <a href="{{ route('inventory.trash') }}" class="topnav-link">Trash</a>
    </div>
  </div>

  <div class="panel-body">
    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:end; margin-bottom:12px;">
      <div>
        <div style="font-size:11px; font-weight:800; color:#6b7896; margin-bottom:6px;">Root</div>
        <select id="rootSelect" style="padding:10px 12px; border:1px solid #e4e8f0; border-radius:12px;">
          <option value="">Loading...</option>
        </select>
      </div>

      <div style="flex:1; min-width:220px;">
        <div style="font-size:11px; font-weight:800; color:#6b7896; margin-bottom:6px;">Search</div>
        <input id="qInput" placeholder="Cari nama file/folder..." style="width:100%; padding:10px 12px; border:1px solid #e4e8f0; border-radius:12px;">
      </div>

      <div>
        <div style="font-size:11px; font-weight:800; color:#6b7896; margin-bottom:6px;">Month</div>
        <input id="monthInput" type="month" style="padding:10px 12px; border:1px solid #e4e8f0; border-radius:12px;">
      </div>

      <div>
        <div style="font-size:11px; font-weight:800; color:#6b7896; margin-bottom:6px;">From</div>
        <input id="fromInput" type="date" style="padding:10px 12px; border:1px solid #e4e8f0; border-radius:12px;">
      </div>

      <div>
        <div style="font-size:11px; font-weight:800; color:#6b7896; margin-bottom:6px;">To</div>
        <input id="toInput" type="date" style="padding:10px 12px; border:1px solid #e4e8f0; border-radius:12px;">
      </div>

      <div>
        <div style="font-size:11px; font-weight:800; color:#6b7896; margin-bottom:6px;">Sort</div>
        <select id="sortSelect" style="padding:10px 12px; border:1px solid #e4e8f0; border-radius:12px;">
          <option value="name">Name</option>
          <option value="uploaded_at">Uploaded Date</option>
        </select>
      </div>

      <div>
        <div style="font-size:11px; font-weight:800; color:#6b7896; margin-bottom:6px;">Dir</div>
        <select id="dirSelect" style="padding:10px 12px; border:1px solid #e4e8f0; border-radius:12px;">
          <option value="asc">ASC</option>
          <option value="desc">DESC</option>
        </select>
      </div>

      <div style="display:flex; gap:8px;">
        <button id="btnNewFolder" type="button" class="topnav-button" style="border:1px solid #e4e8f0; background:#fff;">New Folder</button>
        <label class="topnav-button" style="border:1px solid #e4e8f0; background:#fff; cursor:pointer;">
          Upload
          <input id="uploadInput" type="file" hidden>
        </label>
      </div>
    </div>

    <div id="breadcrumbs" style="font-size:12px; font-weight:700; color:#6b7896; margin: 10px 0 14px;"></div>

    <table class="table">
      <thead>
        <tr>
          <th style="width:52%;">Name</th>
          <th>Type</th>
          <th>Size</th>
          <th>Uploaded</th>
        </tr>
      </thead>
      <tbody id="listBody">
        <tr><td colspan="4" style="color:#6b7896;">Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
window.SHFI = {
  routes: {
    roots: @json(route('inventory.api.roots')),
    list: @json(route('inventory.api.list')),
    breadcrumbs: @json(route('inventory.api.breadcrumbs')),
    upload: @json(route('inventory.api.upload')),
    folderCreate: @json(route('inventory.api.folder.create')),
    rename: @json(route('inventory.api.rename')),
    move: @json(route('inventory.api.move')),
    copy: @json(route('inventory.api.copy')),
    delete: @json(route('inventory.api.delete')),
  },
  csrf: @json(csrf_token())
};
</script>

{{-- @vite(['resources/js/inventory.js']) --}}
@endpush  