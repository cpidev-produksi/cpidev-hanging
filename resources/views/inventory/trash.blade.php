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
    --radius: 10px;
    --radius-sm: 6px;
    --shadow: 0 1px 3px rgba(30,40,100,.07), 0 4px 16px rgba(30,40,100,.06);
    --shadow-lg: 0 8px 32px rgba(30,40,100,.13);
    --font: 'DM Sans', sans-serif;
    --mono: 'DM Mono', monospace;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: var(--bg); color: var(--text); }

  .tr-panel {
    max-width: 1100px;
    margin: 24px auto;
    padding: 0 20px;
  }

  /* Banner */
  .tr-banner {
    background: linear-gradient(135deg, #fff7ed 0%, #fff1e6 100%);
    border: 1px solid #fed7aa;
    border-radius: 14px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
  }
  .tr-banner-icon { font-size: 36px; flex-shrink: 0; }
  .tr-banner-body { flex: 1; }
  .tr-banner-body h2 { font-size: 16px; font-weight: 700; color: #9a3412; margin-bottom: 4px; }
  .tr-banner-body p { font-size: 13px; color: #c2410c; line-height: 1.5; }
  .tr-banner-actions { display: flex; gap: 8px; align-items: center; }

  /* Toolbar */
  .tr-toolbar {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }

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

  .fi-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
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
    text-decoration: none;
  }
  .fi-btn:hover { background: var(--surface2); border-color: var(--border2); color: var(--text); }
  .fi-btn.success { background: var(--success-bg); border-color: #86efac; color: #14532d; }
  .fi-btn.success:hover { background: #dcfce7; }
  .fi-btn.danger { background: #fef2f2; border-color: #fca5a5; color: var(--error); }
  .fi-btn.danger:hover { background: #fee2e2; }
  .fi-btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
  .fi-btn.primary:hover { background: #2d5ce8; }

  /* Table */
  .tr-table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
  }

  .fi-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .fi-table thead th {
    padding: 10px 12px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--text3);
    border-bottom: 1px solid var(--border);
    background: var(--surface2);
  }
  .fi-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .1s;
  }
  .fi-table tbody tr:last-child { border-bottom: none; }
  .fi-table tbody tr:hover { background: var(--surface2); }
  .fi-table tbody td {
    padding: 10px 12px;
    color: var(--text2);
    vertical-align: middle;
  }
  .fi-table tbody td:first-child { color: var(--text); font-weight: 500; }
  .name-cell { display: flex; align-items: center; gap: 8px; }
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
  .badge.folder { background: #eff6ff; color: #1d4ed8; }

  .action-btns { display: flex; gap: 6px; }

  /* Modals */
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
    padding: 28px;
    width: 420px;
    max-width: 90vw;
    transform: translateY(12px) scale(.98);
    transition: transform .18s;
  }
  .modal-overlay.open .modal { transform: translateY(0) scale(1); }
  .modal-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
  .modal-body { font-size: 13px; color: var(--text2); line-height: 1.6; }
  .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 22px; }
  .modal-warn {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    font-size: 13px;
    color: #991b1b;
    display: flex; align-items: flex-start; gap: 8px;
    line-height: 1.5;
    margin-top: 12px;
  }

  /* Toast */
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
  .toast-close { font-size: 14px; cursor: pointer; opacity: .5; }
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

  .empty-state { padding: 52px 20px; text-align: center; color: var(--text3); }
  .empty-state .empty-icon { font-size: 44px; margin-bottom: 10px; }
  .empty-state p { font-size: 14px; margin-bottom: 6px; }
  .empty-state small { font-size: 12px; }
  .spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin .6s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .fi-label { font-size: 11px; font-weight: 700; letter-spacing:.05em; text-transform:uppercase; color:var(--text3); }
</style>
@endpush

@section('content')

<div class="toast-container" id="toastContainer"></div>

{{-- Modal: Purge Single --}}
<div class="modal-overlay" id="modalPurge">
  <div class="modal">
    <div class="modal-title">⚠️ Permanently Delete</div>
    <div class="modal-body">
      This action will <strong>permanently delete</strong> <strong id="purgeItemName"></strong>.
      <div class="modal-warn">
        <span style="font-size:18px">🔥</span>
        <div>This cannot be undone. The item will be gone forever.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="fi-btn" onclick="closeModal('modalPurge')">Cancel</button>
      <button class="fi-btn danger" id="btnPurgeConfirm">Delete Forever</button>
    </div>
  </div>
</div>

{{-- Modal: Empty Trash --}}
<div class="modal-overlay" id="modalEmpty">
  <div class="modal">
    <div class="modal-title">🗑️ Empty Trash</div>
    <div class="modal-body">
      This will <strong>permanently delete all</strong> items in the trash. This action cannot be undone.
      <div class="modal-warn">
        <span style="font-size:18px">🔥</span>
        <div>All <strong id="emptyCount">0</strong> items in trash will be gone forever.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="fi-btn" onclick="closeModal('modalEmpty')">Cancel</button>
      <button class="fi-btn danger" id="btnEmptyConfirm">Empty Trash</button>
    </div>
  </div>
</div>

<div class="tr-panel">

  {{-- Banner --}}
  <div class="tr-banner">
    <div class="tr-banner-icon">🗑️</div>
    <div class="tr-banner-body">
      <h2>Trash — Deleted Items</h2>
      <p>Items in trash will be permanently deleted after 30 days. You can restore items or delete them permanently.</p>
    </div>
    <div class="tr-banner-actions">
      <a href="{{ route('inventory.index') }}" class="fi-btn">← Back to Explorer</a>
      <button class="fi-btn danger" id="btnEmptyTrash">
        🔥 Empty Trash
      </button>
    </div>
  </div>

  {{-- Toolbar --}}
  <div class="tr-toolbar">
    <div style="display:flex;flex-direction:column;gap:4px;flex:1;min-width:200px;">
      <div class="fi-label">Search</div>
      <input id="trashSearch" class="fi-input" placeholder="Filter by name..." style="width:100%">
    </div>
    <div style="display:flex;flex-direction:column;gap:4px;">
      <div class="fi-label">Type</div>
      <select id="trashTypeFilter" class="fi-input" style="padding:7px 28px 7px 11px;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238890b5' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 8px center;cursor:pointer;">
        <option value="">All</option>
        <option value="file">Files only</option>
        <option value="folder">Folders only</option>
      </select>
    </div>
    <div style="margin-left:auto;display:flex;align-items:flex-end">
      <span id="trashCount" style="font-size:12px;color:var(--text3);font-weight:600;padding-bottom:2px;"></span>
    </div>
  </div>

  {{-- Table --}}
  <div class="tr-table-card">
    <table class="fi-table">
      <thead>
        <tr>
          <th style="width:45%">Name</th>
          <th>Type</th>
          <th>Size</th>
          <th>Deleted</th>
          <th style="width:160px">Actions</th>
        </tr>
      </thead>
      <tbody id="trashBody">
        <tr><td colspan="5"><div class="empty-state"><span class="spinner"></span></div></td></tr>
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
    trashList: @json(route('inventory.api.trash.list')),
    trashRestore: @json(route('inventory.api.trash.restore')),
    trashPurge: @json(route('inventory.api.trash.purge')),
    trashEmpty: @json(route('inventory.api.trash.empty')),
  },
  csrf: @json(csrf_token())
};
</script>
<script>
// ── Toast ──────────────────────────────────────────────
function toast(msg, type = 'info', duration = 4000) {
  const icons = { success:'✅', error:'❌', warn:'⚠️', info:'ℹ️' };
  const c = document.getElementById('toastContainer');
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `
    <span class="toast-icon">${icons[type]||'ℹ️'}</span>
    <span class="toast-body">${msg}</span>
    <span class="toast-close" onclick="this.parentElement.remove()">✕</span>
    <div class="toast-progress" style="animation-duration:${duration}ms"></div>`;
  c.appendChild(el);
  setTimeout(() => { el.classList.add('removing'); setTimeout(()=>el.remove(),220); }, duration);
}

// ── Modal ──────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('keydown', e => { if(e.key==='Escape') document.querySelectorAll('.modal-overlay.open').forEach(m=>m.classList.remove('open')); });

// ── API ────────────────────────────────────────────────
async function api(url, opts = {}) {
  const res = await fetch(url, {
    headers: { 'X-CSRF-TOKEN': window.SHFI.csrf, 'Accept': 'application/json', ...(opts.headers||{}) },
    credentials: 'same-origin', ...opts
  });
  const data = await res.json().catch(()=>({}));
  if (!res.ok) throw new Error(data.message || 'Request failed');
  return data;
}

// ── State ──────────────────────────────────────────────
let allItems = [];
let allRoots = [];
let pendingPurgeId = null, pendingPurgeType = null;

function escapeHtml(str) {
  return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}
function formatBytes(bytes) {
  const units=['B','KB','MB','GB','TB']; let b=bytes,i=0;
  while(b>=1024&&i<units.length-1){b/=1024;i++;} return `${b.toFixed(i===0?0:1)} ${units[i]}`;
}

// ── Load Roots then Trash ─────────────────────────────
async function loadRootsAndTrash() {
  try {
    const out = await api(window.SHFI.routes.roots);
    allRoots = out.data || [];
    await loadTrash();
  } catch(e) {
    toast(e.message, 'error');
  }
}

function toFormBody(obj) {
  const p = new URLSearchParams();
  Object.entries(obj).forEach(([k,v]) => p.set(k, String(v)));
  return p.toString();
}

async function apiForm(url, method, payload) {
  return api(url, {
    method,
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body: toFormBody(payload)
  });
}

// ── Load Trash ─────────────────────────────────────────
async function loadTrash() {
  try {
    // Fetch trash across all roots and merge
    const requests = allRoots.map(r =>
      api(window.SHFI.routes.trashList + '?root_id=' + r.id).catch(() => ({ data: [] }))
    );
    const results = await Promise.all(requests);
    allItems = results.flatMap(r => r.data || []);
    renderTrash();
  } catch(e) {
    toast(e.message, 'error');
    document.getElementById('trashBody').innerHTML = `<tr><td colspan="5"><div class="empty-state"><div class="empty-icon">❌</div><p>Failed to load trash.</p></div></td></tr>`;
  }
}

function renderTrash() {
  const q = document.getElementById('trashSearch').value.trim().toLowerCase();
  const typeF = document.getElementById('trashTypeFilter').value;
  const body = document.getElementById('trashBody');

  let items = allItems;
  if (q) items = items.filter(i => i.name.toLowerCase().includes(q));
  if (typeF) items = items.filter(i => i.type === typeF);

  document.getElementById('trashCount').textContent = `${items.length} item${items.length!==1?'s':''}`;
  document.getElementById('emptyCount').textContent = allItems.length;

  if (!items.length) {
    body.innerHTML = `
      <tr><td colspan="5">
        <div class="empty-state">
          <div class="empty-icon">✨</div>
          <p>${q||typeF ? 'No items match your filter.' : 'Trash is empty.'}</p>
          <small>${q||typeF ? '' : 'Deleted files will appear here.'}</small>
        </div>
      </td></tr>`;
    return;
  }

  body.innerHTML = items.map(item => `
    <tr data-id="${item.id}" data-type="${item.type}" data-name="${escapeHtml(item.name)}">
      <td>
        <div class="name-cell">
          <span style="font-size:16px">${item.type==='folder'?'📁':'📄'}</span>
          ${escapeHtml(item.name)}
        </div>
      </td>
      <td>
        <span class="badge ${item.type==='folder'?'folder':''}">${item.type==='folder'?'Folder':(item.mime_type||'File')}</span>
      </td>
      <td>${item.size ? formatBytes(item.size) : '—'}</td>
      <td style="font-size:12px;color:var(--text3)">${item.deleted_at ? new Date(item.deleted_at).toLocaleString('id-ID') : '—'}</td>
      <td>
        <div class="action-btns">
          <button class="fi-btn success" data-action="restore" title="Restore">
            ♻️ Restore
          </button>
          <button class="fi-btn danger" data-action="purge" title="Delete permanently">
            🔥 Purge
          </button>
        </div>
      </td>
    </tr>`).join('');
}

// ── Event Delegation ───────────────────────────────────
document.getElementById('trashBody').addEventListener('click', async e => {
  const btn = e.target.closest('[data-action]');
  if (!btn) return;
  const tr = btn.closest('tr');
  const id = tr.dataset.id;
  const type = tr.dataset.type;
  const name = tr.dataset.name;
  const action = btn.dataset.action;

  if (action === 'restore') {
    try {
      btn.disabled = true; btn.textContent = '...';
      await apiForm(window.SHFI.routes.trashRestore, 'POST', { type, id: parseInt(id,10) });
      allItems = allItems.filter(i => !(i.id==id && i.type===type));
      renderTrash();
      toast(`"${name}" has been restored successfully.`, 'success');
    } catch(err) { toast(err.message, 'error'); btn.disabled=false; btn.innerHTML='♻️ Restore'; }
  }

  if (action === 'purge') {
    pendingPurgeId = parseInt(id);
    pendingPurgeType = type;
    document.getElementById('purgeItemName').textContent = `"${name}"`;
    openModal('modalPurge');
  }
});

document.getElementById('btnPurgeConfirm').addEventListener('click', async () => {
  const btn = document.getElementById('btnPurgeConfirm');
  btn.disabled = true; btn.textContent = 'Deleting...';
  try {
    await apiForm(window.SHFI.routes.trashPurge, 'DELETE', { type: pendingPurgeType, id: pendingPurgeId });
    allItems = allItems.filter(i => !(i.id===pendingPurgeId && i.type===pendingPurgeType));
    renderTrash();
    closeModal('modalPurge');
    toast('Item permanently deleted.', 'warn');
  } catch(err) { toast(err.message,'error'); }
  btn.disabled=false; btn.innerHTML='🔥 Delete Forever';
});

document.getElementById('btnEmptyTrash').addEventListener('click', () => {
  document.getElementById('emptyCount').textContent = allItems.length;
  openModal('modalEmpty');
});

document.getElementById('btnEmptyConfirm').addEventListener('click', async () => {
  const btn = document.getElementById('btnEmptyConfirm');
  btn.disabled = true; btn.textContent = 'Emptying...';
  try {
    await api(window.SHFI.routes.trashEmpty, { method: 'DELETE' });
    allItems = [];
    renderTrash();
    closeModal('modalEmpty');
    toast('Trash has been emptied.', 'warn');
  } catch(err) { toast(err.message, 'error'); }
  btn.disabled=false; btn.innerHTML='🔥 Empty Trash';
});

document.getElementById('trashSearch').addEventListener('input', renderTrash);
document.getElementById('trashTypeFilter').addEventListener('change', renderTrash);

loadRootsAndTrash();
</script>
@endpush