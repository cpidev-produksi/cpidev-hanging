@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #f5f6fa;
    --surface: #ffffff;
    --surface2: #f0f2f8;
    --surface3: #e8eaf4;
    --border: #e2e5f0;
    --border2: #cdd1e8;
    --accent: #3b6ef8;
    --accent-light: #eef2ff;
    --accent-hover: #2d5ce8;
    --text: #1a1d2e;
    --text2: #4b5278;
    --text3: #8890b5;
    --success: #16a34a;
    --success-bg: #f0fdf4;
    --warn: #d97706;
    --warn-bg: #fffbeb;
    --error: #dc2626;
    --error-bg: #fef2f2;
    --info: #0284c7;
    --info-bg: #f0f9ff;
    --sidebar-w: 260px;
    --radius: 10px;
    --radius-sm: 6px;
    --shadow: 0 1px 3px rgba(30,40,100,.07), 0 4px 16px rgba(30,40,100,.06);
    --shadow-lg: 0 8px 32px rgba(30,40,100,.13);
    --font: 'DM Sans', sans-serif;
    --mono: 'DM Mono', monospace;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: var(--bg); color: var(--text); }

  /* ── Layout ── */
  .fi-shell { display: flex; height: calc(100vh - 56px); overflow: hidden; }

  /* ── Sidebar ── */
  .fi-sidebar {
    width: var(--sidebar-w);
    min-width: var(--sidebar-w);
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .fi-sidebar-header {
    padding: 14px 16px 10px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .fi-sidebar-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--text3);
  }
  .fi-sidebar-scroll { flex: 1; overflow-y: auto; padding: 8px 0; }
  .fi-sidebar-scroll::-webkit-scrollbar { width: 4px; }
  .fi-sidebar-scroll::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }

  /* tree nodes */
  .tree-node { user-select: none; }
  .tree-row {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px 5px 0;
    cursor: pointer;
    border-radius: var(--radius-sm);
    margin: 1px 6px;
    transition: background .12s;
    font-size: 13px;
    color: var(--text2);
    position: relative;
  }
  .tree-row:hover { background: var(--surface2); }
  .tree-row.active {
    background: var(--accent-light);
    color: var(--accent);
    font-weight: 600;
  }
  .tree-row.active::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 4px; bottom: 4px;
    width: 3px;
    background: var(--accent);
    border-radius: 2px;
  }
  .tree-chevron {
    width: 16px; height: 16px;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    color: var(--text3);
    transition: transform .15s;
    font-size: 10px;
  }
  .tree-chevron.open { transform: rotate(90deg); }
  .tree-icon { font-size: 14px; flex-shrink: 0; }
  .tree-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .tree-children { display: none; }
  .tree-children.open { display: block; }

  /* ── Main area ── */
  .fi-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--bg);
  }

  /* toolbar */
  .fi-toolbar {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
  .fi-toolbar-group { display: flex; align-items: center; gap: 6px; }
  .fi-toolbar-sep { width: 1px; height: 22px; background: var(--border); margin: 0 2px; }

  .fi-input {
    padding: 7px 11px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 13px;
    color: var(--text);
    background: var(--surface2);
    transition: border-color .15s, box-shadow .15s;
    outline: none;
  }
  .fi-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,110,248,.1); background: #fff; }
  .fi-input.search { min-width: 180px; }
  .fi-input.month, .fi-input.date { width: 140px; }
  .fi-select {
    padding: 7px 28px 7px 11px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 13px;
    color: var(--text);
    background: var(--surface2);
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238890b5' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    cursor: pointer;
    outline: none;
    transition: border-color .15s;
  }
  .fi-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,110,248,.1); }

  .fi-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 13px;
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text2);
    transition: all .12s;
    white-space: nowrap;
  }
  .fi-btn:hover { background: var(--surface2); border-color: var(--border2); color: var(--text); }
  .fi-btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
  .fi-btn.primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
  .fi-btn.danger { background: #fef2f2; border-color: #fca5a5; color: var(--error); }
  .fi-btn.danger:hover { background: #fee2e2; }

  /* breadcrumb */
  .fi-breadcrumb {
    padding: 8px 16px 0;
    font-size: 12px;
    font-weight: 600;
    color: var(--text3);
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
  }
  .fi-breadcrumb a {
    color: var(--text3);
    text-decoration: none;
    transition: color .12s;
    display: flex; align-items: center; gap: 3px;
  }
  .fi-breadcrumb a:hover { color: var(--accent); }
  .fi-breadcrumb a.crumb-active { color: var(--text); font-weight: 700; }
  .fi-breadcrumb .sep { color: var(--border2); }

  /* table area */
  .fi-table-wrap { flex: 1; overflow-y: auto; padding: 10px 16px 0; }
  .fi-table-wrap::-webkit-scrollbar { width: 6px; }
  .fi-table-wrap::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }

  .fi-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .fi-table thead th {
    padding: 8px 10px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--text3);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    background: var(--bg);
    z-index: 1;
  }
  .fi-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .1s;
    cursor: pointer;
  }
  .fi-table tbody tr:hover { background: var(--surface); }
  .fi-table tbody tr.selected { background: var(--accent-light); }
  .fi-table tbody td {
    padding: 9px 10px;
    color: var(--text2);
    vertical-align: middle;
  }
  .fi-table tbody td:first-child { color: var(--text); font-weight: 500; }
  .fi-table .name-cell { display: flex; align-items: center; gap: 8px; }
  .fi-table .fi-icon { font-size: 16px; flex-shrink: 0; }
  .fi-table a { color: var(--accent); text-decoration: none; }
  .fi-table a:hover { text-decoration: underline; }
  .badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    background: var(--surface3);
    color: var(--text3);
    font-family: var(--mono);
  }

  /* status bar */
  .fi-statusbar {
    border-top: 1px solid var(--border);
    padding: 6px 16px;
    background: var(--surface);
    display: flex;
    align-items: center;
    gap: 18px;
    font-size: 11.5px;
    color: var(--text3);
    font-weight: 500;
  }
  .fi-statusbar span { display: flex; align-items: center; gap: 4px; }

  /* ── Context Menu ── */
  .ctx-menu {
    position: fixed;
    z-index: 9000;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    padding: 5px;
    min-width: 185px;
    opacity: 0;
    transform: scale(.95) translateY(-4px);
    transform-origin: top left;
    transition: opacity .12s, transform .12s;
    pointer-events: none;
  }
  .ctx-menu.visible {
    opacity: 1;
    transform: scale(1) translateY(0);
    pointer-events: all;
  }
  .ctx-item {
    display: flex; align-items: center; gap: 9px;
    padding: 8px 12px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    color: var(--text2);
    cursor: pointer;
    transition: background .1s, color .1s;
  }
  .ctx-item:hover { background: var(--surface2); color: var(--text); }
  .ctx-item.danger:hover { background: #fef2f2; color: var(--error); }
  .ctx-item .ctx-icon { font-size: 14px; width: 18px; text-align: center; }
  .ctx-sep { height: 1px; background: var(--border); margin: 4px 8px; }

  /* ── Modals ── */
  .modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15,20,50,.35);
    backdrop-filter: blur(3px);
    z-index: 8000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0;
    transition: opacity .18s;
    pointer-events: none;
  }
  .modal-overlay.open { opacity: 1; pointer-events: all; }
  .modal {
    background: var(--surface);
    border-radius: 14px;
    box-shadow: var(--shadow-lg);
    padding: 28px 28px 22px;
    width: 400px;
    max-width: 90vw;
    transform: translateY(12px) scale(.98);
    transition: transform .18s;
  }
  .modal-overlay.open .modal { transform: translateY(0) scale(1); }
  .modal-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 18px;
    display: flex; align-items: center; gap: 8px;
  }
  .modal-label {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--text3);
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 6px;
  }
  .modal-input {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 14px;
    color: var(--text);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    background: var(--surface2);
  }
  .modal-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,110,248,.1); background: #fff; }
  .modal-hint {
    font-size: 12px;
    color: var(--text3);
    margin-top: 5px;
  }
  .modal-error {
    font-size: 12px;
    color: var(--error);
    margin-top: 5px;
    display: none;
  }
  .modal-footer {
    display: flex; justify-content: flex-end; gap: 8px;
    margin-top: 22px;
  }
  .modal-delete-info {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    font-size: 13px;
    color: #991b1b;
    display: flex; align-items: flex-start; gap: 8px;
    line-height: 1.5;
  }

  /* ── Toast ── */
  .toast-container {
    position: fixed;
    bottom: 24px; right: 20px;
    z-index: 9999;
    display: flex; flex-direction: column; gap: 8px;
    align-items: flex-end;
  }
  .toast {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 14px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    font-size: 13px;
    font-weight: 500;
    min-width: 260px;
    max-width: 340px;
    background: var(--surface);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    animation: toastIn .22s ease;
    transition: opacity .2s, transform .2s;
  }
  .toast.removing { opacity: 0; transform: translateX(16px); }
  @keyframes toastIn { from { opacity: 0; transform: translateX(16px); } to { opacity: 1; transform: translateX(0); } }
  .toast.success { background: var(--success-bg); border-color: #bbf7d0; color: #14532d; }
  .toast.error   { background: var(--error-bg);   border-color: #fca5a5; color: #7f1d1d; }
  .toast.warn    { background: var(--warn-bg);    border-color: #fde68a; color: #78350f; }
  .toast.info    { background: var(--info-bg);    border-color: #bae6fd; color: #0c4a6e; }
  .toast-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
  .toast-body { flex: 1; line-height: 1.4; }
  .toast-close { font-size: 14px; cursor: pointer; opacity: .5; align-self: flex-start; margin-top: 1px; }
  .toast-close:hover { opacity: 1; }
  .toast-progress {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: currentColor;
    opacity: .25;
    border-radius: 0 0 var(--radius) var(--radius);
    animation: toastProgress linear forwards;
  }
  @keyframes toastProgress { from { width: 100%; } to { width: 0%; } }

  /* ── Drag overlay ── */
  .drag-overlay {
    position: fixed; inset: 0;
    z-index: 9800;
    background: rgba(59,110,248,.1);
    border: 3px dashed var(--accent);
    display: none;
    align-items: center; justify-content: center;
    flex-direction: column;
    gap: 12px;
    font-size: 18px;
    font-weight: 700;
    color: var(--accent);
    backdrop-filter: blur(2px);
    pointer-events: none;
    animation: dragPulse 1s ease-in-out infinite alternate;
  }
  .drag-overlay.visible { display: flex; }
  @keyframes dragPulse { from { background: rgba(59,110,248,.07); } to { background: rgba(59,110,248,.14); } }
  .drag-overlay .drop-icon { font-size: 52px; }

  /* ── Toolbar label ── */
  .fi-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--text3);
    white-space: nowrap;
  }

  /* root select label */
  .root-wrap { display: flex; flex-direction: column; gap: 4px; }

  /* misc */
  .empty-state {
    padding: 48px 20px;
    text-align: center;
    color: var(--text3);
    font-size: 14px;
  }
  .empty-state .empty-icon { font-size: 40px; margin-bottom: 10px; }
  .spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin .6s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')

{{-- Drag & Drop Overlay --}}
<div class="drag-overlay" id="dragOverlay">
  <div class="drop-icon">📂</div>
  <div>Drop files to upload here</div>
</div>

{{-- Toast Container --}}
<div class="toast-container" id="toastContainer"></div>

{{-- Context Menu --}}
<div class="ctx-menu" id="ctxMenu">
  <div class="ctx-item" data-action="open">      <span class="ctx-icon">📂</span> Open</div>
  <div class="ctx-item" data-action="download">  <span class="ctx-icon">⬇️</span> Download</div>
  <div class="ctx-sep"></div>
  <div class="ctx-item" data-action="rename">    <span class="ctx-icon">✏️</span> Rename</div>
  <div class="ctx-item" data-action="copy">      <span class="ctx-icon">📋</span> Copy</div>
  <div class="ctx-item" data-action="cut">       <span class="ctx-icon">✂️</span> Move (Cut)</div>
  <div class="ctx-item" data-action="paste">     <span class="ctx-icon">📌</span> Paste here</div>
  <div class="ctx-sep"></div>
  <div class="ctx-item" data-action="newfolder"> <span class="ctx-icon">🗂️</span> New Folder Here</div>
  <div class="ctx-item" data-action="upload">    <span class="ctx-icon">⬆️</span> Upload Here</div>
  <div class="ctx-sep"></div>
  <div class="ctx-item danger" data-action="delete"><span class="ctx-icon">🗑️</span> Delete</div>
</div>

{{-- Modal: New Folder --}}
<div class="modal-overlay" id="modalNewFolder">
  <div class="modal">
    <div class="modal-title">🗂️ New Folder</div>
    <div class="modal-label">Folder Name</div>
    <input class="modal-input" id="newFolderName" placeholder="e.g. Documents 2025" maxlength="120">
    <div class="modal-error" id="newFolderError"></div>
    <div class="modal-footer">
      <button class="fi-btn" onclick="closeModal('modalNewFolder')">Cancel</button>
      <button class="fi-btn primary" id="btnNewFolderConfirm">Create Folder</button>
    </div>
  </div>
</div>

{{-- Modal: Rename --}}
<div class="modal-overlay" id="modalRename">
  <div class="modal">
    <div class="modal-title">✏️ Rename</div>
    <div class="modal-label">Current Name</div>
    <div id="renameOldName" style="font-size:13px;color:var(--text2);margin-bottom:14px;padding:8px 12px;background:var(--surface2);border-radius:var(--radius-sm);font-family:var(--mono)"></div>
    <div class="modal-label">New Name</div>
    <input class="modal-input" id="renameNewName" placeholder="Enter new name..." maxlength="120">
    <div class="modal-error" id="renameError"></div>
    <div class="modal-footer">
      <button class="fi-btn" onclick="closeModal('modalRename')">Cancel</button>
      <button class="fi-btn primary" id="btnRenameConfirm">Rename</button>
    </div>
  </div>
</div>

{{-- Modal: Delete --}}
<div class="modal-overlay" id="modalDelete">
  <div class="modal">
    <div class="modal-title">🗑️ Move to Trash</div>
    <div class="modal-delete-info">
      <span style="font-size:18px;">⚠️</span>
      <div>
        <strong id="deleteItemName"></strong> will be moved to trash.<br>
        You can restore it later from the Trash page.
      </div>
    </div>
    <div class="modal-footer">
      <button class="fi-btn" onclick="closeModal('modalDelete')">Cancel</button>
      <button class="fi-btn danger" id="btnDeleteConfirm">Move to Trash</button>
    </div>
  </div>
</div>

{{-- Modal: Move/Copy --}}
<div class="modal-overlay" id="modalMoveCopy">
  <div class="modal">
    <div class="modal-title" id="moveCopyTitle">📋 Copy Item</div>
    <div class="modal-label">Destination Path <span style="font-weight:400;color:var(--text3)">(Folder ID)</span></div>
    <input class="modal-input" id="moveCopyDest" placeholder="Enter destination folder ID..." type="number" min="1">
    <div class="modal-hint">Enter the ID of the destination folder, or navigate there first and use Paste.</div>
    <div class="modal-error" id="moveCopyError"></div>
    <div class="modal-footer">
      <button class="fi-btn" onclick="closeModal('modalMoveCopy')">Cancel</button>
      <button class="fi-btn primary" id="btnMoveCopyConfirm">Confirm</button>
    </div>
  </div>
</div>

{{-- Main Shell --}}
<div class="fi-shell">

  {{-- Sidebar Tree --}}
  <aside class="fi-sidebar">
    <div class="fi-sidebar-header">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--accent)"><path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
      <span class="fi-sidebar-title">Explorer</span>
    </div>
    <div class="fi-sidebar-scroll" id="sidebarTree">
      <div style="padding:16px;color:var(--text3);font-size:12px;display:flex;gap:8px;align-items:center;">
        <span class="spinner"></span> Loading tree...
      </div>
    </div>
  </aside>

  {{-- Main --}}
  <main class="fi-main">

    {{-- Toolbar --}}
    <div class="fi-toolbar">
      <div class="root-wrap">
        <div class="fi-label">Root</div>
        <select id="rootSelect" class="fi-select">
          <option value="">Loading...</option>
        </select>
      </div>

      <div class="fi-toolbar-sep"></div>

      <div style="display:flex;flex-direction:column;gap:4px;flex:1;min-width:180px;">
        <div class="fi-label">Search</div>
        <input id="qInput" class="fi-input search" placeholder="Search files & folders...">
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;">
        <div class="fi-label">Month</div>
        <input id="monthInput" type="month" class="fi-input month">
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;">
        <div class="fi-label">From</div>
        <input id="fromInput" type="date" class="fi-input date">
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;">
        <div class="fi-label">To</div>
        <input id="toInput" type="date" class="fi-input date">
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;">
        <div class="fi-label">Sort</div>
        <select id="sortSelect" class="fi-select">
          <option value="name">Name</option>
          <option value="uploaded_at">Upload Date</option>
        </select>
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;">
        <div class="fi-label">Dir</div>
        <select id="dirSelect" class="fi-select">
          <option value="asc">ASC</option>
          <option value="desc">DESC</option>
        </select>
      </div>

      <div class="fi-toolbar-sep"></div>

      <div class="fi-toolbar-group" style="margin-top:auto;">
        <button id="btnNewFolder" type="button" class="fi-btn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
          New Folder
        </button>
        <label class="fi-btn primary" style="cursor:pointer;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
          Upload
          <input id="uploadInput" type="file" hidden multiple>
        </label>
        <a href="{{ route('inventory.trash') }}" class="fi-btn">
          🗑️ Trash
        </a>
      </div>
    </div>

    {{-- Breadcrumb --}}
    <div class="fi-breadcrumb" id="breadcrumbs"></div>

    {{-- Table --}}
    <div class="fi-table-wrap">
      <table class="fi-table">
        <thead>
          <tr>
            <th style="width:50%">Name</th>
            <th id="thLocation" style="display:none">Location</th>
            <th>Type</th>
            <th>Size</th>
            <th>Uploaded</th>
          </tr>
        </thead>
        <tbody id="listBody">
          <tr><td colspan="4"><div class="empty-state"><div class="empty-icon">⏳</div>Loading...</div></td></tr>
        </tbody>
      </table>
    </div>

    {{-- Status Bar --}}
    <div class="fi-statusbar" id="statusBar">
      <span>📁 <span id="statusFolders">—</span> folders</span>
      <span>📄 <span id="statusFiles">—</span> files</span>
      <span>💾 <span id="statusSize">—</span> total</span>
      <span style="margin-left:auto;color:var(--border2)">Right-click for options · Double-click to open folder · Ctrl+V to paste</span>
    </div>

  </main>
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
    folders: @json(route('inventory.api.folders')),
  },
  csrf: @json(csrf_token())
};
</script>
<script src="{{ asset('js/inventory.js') }}?v={{ date('YmdHis') }}" defer></script>
@endpush