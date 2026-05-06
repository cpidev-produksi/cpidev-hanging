(function () {
  // ── Helpers ────────────────────────────────────────────────────────────────

  const $ = (id) => document.getElementById(id);

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, m => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[m]));
  }

  function escapeAttr(str) { return escapeHtml(str); }

  function formatBytes(bytes) {
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let b = bytes, i = 0;
    while (b >= 1024 && i < units.length - 1) { b /= 1024; i++; }
    return `${b.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
  }

  function debounce(fn, ms) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  }

  // ── Toast ──────────────────────────────────────────────────────────────────

  function toast(msg, type = 'info', duration = 4000) {
    const icons = { success: '✅', error: '❌', warn: '⚠️', info: 'ℹ️' };
    const container = $('toastContainer');
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `
      <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
      <span class="toast-body">${escapeHtml(msg)}</span>
      <span class="toast-close" onclick="this.parentElement.remove()">✕</span>
      <div class="toast-progress" style="animation-duration:${duration}ms"></div>`;
    container.appendChild(el);
    setTimeout(() => {
      el.classList.add('removing');
      setTimeout(() => el.remove(), 220);
    }, duration);
  }

  // ── Modal ──────────────────────────────────────────────────────────────────

  function openModal(id) { $(id).classList.add('open'); }
  function closeModal(id) { $(id).classList.remove('open'); }
  window.closeModal = closeModal; // expose for inline onclick

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
      hideCtxMenu();
    }
  });

  // ── API ────────────────────────────────────────────────────────────────────

  async function api(url, opts = {}) {
    const res = await fetch(url, {
      headers: {
        'X-CSRF-TOKEN': window.SHFI.csrf,
        'Accept': 'application/json',
        ...(opts.headers || {})
      },
      credentials: 'same-origin',
      ...opts
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
  }

  // ── State ──────────────────────────────────────────────────────────────────

  let state = {
    root_id: null,
    folder_id: null,
    path: [],         // [{id, name}]
    clipboard: null,  // {mode:'copy'|'cut', type:'file'|'folder', id}
    ctxTarget: null,  // current right-clicked tr
    lastFolders: [],
    lastFiles: [],
  };

  // ── Context Menu ───────────────────────────────────────────────────────────

  const ctxMenu = $('ctxMenu');

  function showCtxMenu(x, y, tr) {
    state.ctxTarget = tr;
    const type = tr ? tr.dataset.type : null;

    // Show/hide relevant items
    ctxMenu.querySelector('[data-action="open"]').style.display     = (type === 'folder') ? '' : 'none';
    ctxMenu.querySelector('[data-action="download"]').style.display = (type === 'file')   ? '' : 'none';
    ctxMenu.querySelector('[data-action="rename"]').style.display   = tr ? '' : 'none';
    ctxMenu.querySelector('[data-action="copy"]').style.display     = tr ? '' : 'none';
    ctxMenu.querySelector('[data-action="cut"]').style.display      = tr ? '' : 'none';
    ctxMenu.querySelector('[data-action="delete"]').style.display   = tr ? '' : 'none';
    ctxMenu.querySelector('[data-action="paste"]').style.display    = state.clipboard ? '' : 'none';

    // Position
    const vw = window.innerWidth, vh = window.innerHeight;
    const menuW = 185, menuH = 260;
    ctxMenu.style.left = (x + menuW > vw ? vw - menuW - 8 : x) + 'px';
    ctxMenu.style.top  = (y + menuH > vh ? vh - menuH - 8 : y) + 'px';
    ctxMenu.classList.add('visible');
  }

  function hideCtxMenu() { ctxMenu.classList.remove('visible'); state.ctxTarget = null; }

  document.addEventListener('click', e => {
    if (!ctxMenu.contains(e.target)) hideCtxMenu();
  });

  ctxMenu.addEventListener('click', async e => {
    const item = e.target.closest('[data-action]');
    if (!item) return;
    const action = item.dataset.action;
    const tr = state.ctxTarget;
    hideCtxMenu();

    switch (action) {
      case 'open':      if (tr) openFolder(tr); break;
      case 'download':  if (tr) downloadFile(tr); break;
      case 'rename':    if (tr) showRenameModal(tr); break;
      case 'copy':      if (tr) copyCut(tr, 'copy'); break;
      case 'cut':       if (tr) copyCut(tr, 'cut'); break;
      case 'paste':     pasteHere(); break;
      case 'newfolder': showNewFolderModal(); break;
      case 'upload':    $('uploadInput').click(); break;
      case 'delete':    if (tr) showDeleteModal(tr); break;
    }
  });

  // ── Sidebar Tree ───────────────────────────────────────────────────────────

  // We'll render a tree from the root's folder structure.
  // For simplicity we lazy-load children on expand.

  let treeRoots = [];

  function buildTreeNode(folder, depth = 0) {
    const indent = depth * 18;
    return `
      <div class="tree-node" data-tree-id="${folder.id}" data-tree-name="${escapeAttr(folder.name)}">
        <div class="tree-row" data-folder-id="${folder.id}" style="padding-left:${8 + indent}px">
          <span class="tree-chevron" data-chevron="${folder.id}">▶</span>
          <span class="tree-icon">📁</span>
          <span class="tree-label">${escapeHtml(folder.name)}</span>
        </div>
        <div class="tree-children" data-children="${folder.id}"></div>
      </div>`;
  }

  function buildRootTreeNode(root) {
    return `
      <div class="tree-node" data-root-node="${root.id}">
        <div class="tree-row" data-root-id="${root.id}" style="padding-left:8px">
          <span class="tree-chevron" data-chevron="root-${root.id}">▶</span>
          <span class="tree-icon">🗄️</span>
          <span class="tree-label">${escapeHtml(root.name)}</span>
        </div>
        <div class="tree-children" data-children="root-${root.id}"></div>
      </div>`;
  }

  async function loadTreeChildren(parentId, isRoot, containerEl, chevronEl) {
    const isOpen = containerEl.classList.contains('open');
    if (isOpen) {
      containerEl.classList.remove('open');
      chevronEl.classList.remove('open');
      return;
    }

    // Already loaded
    if (containerEl.childElementCount > 0) {
      containerEl.classList.add('open');
      chevronEl.classList.add('open');
      return;
    }

    chevronEl.textContent = '…';
    try {
      const params = new URLSearchParams();
      const rootId = isRoot ? parentId : (state.root_id || treeRoots[0]?.id);
      params.set('root_id', rootId);
      if (!isRoot) params.set('parent_id', parentId);

      const out = await api(window.SHFI.routes.folders + '?' + params.toString());
      const folders = out.data || [];

      if (!folders.length) {
        containerEl.innerHTML = `<div style="padding:4px 8px 4px 32px;font-size:12px;color:var(--text3);font-style:italic">No subfolders</div>`;
      } else {
        const depth = isRoot ? 0 : 1;
        containerEl.innerHTML = folders.map(f => buildTreeNode(f, depth)).join('');
        bindTreeEvents(containerEl);
      }

      containerEl.classList.add('open');
      chevronEl.textContent = '▶';
      chevronEl.classList.add('open');
    } catch(err) {
      chevronEl.textContent = '▶';
      toast('Failed to load tree: ' + err.message, 'error');
    }
  }

  function updateTreeActive() {
    document.querySelectorAll('.tree-row').forEach(row => {
      const fid = row.dataset.folderId;
      const rid = row.dataset.rootId;
      const isActive = (fid && fid == state.folder_id) ||
                       (rid && rid == state.root_id && !state.folder_id && !fid);
      row.classList.toggle('active', isActive);
    });
  }

  function bindTreeEvents(container) {
    container.querySelectorAll('.tree-row').forEach(row => {
      row.addEventListener('click', async e => {
        e.stopPropagation();

        if (row.dataset.rootId) {
          // Clicked a root
          const rid = row.dataset.rootId;
          state.root_id = rid;
          $('rootSelect').value = rid;
          state.folder_id = null;
          state.path = [];
          updateTreeActive();

          const chevron = container.querySelector(`[data-chevron="root-${rid}"]`);
          const children = container.querySelector(`[data-children="root-${rid}"]`);
          if (chevron && children) {
            await loadTreeChildren(rid, true, children, chevron);
          }
          await loadList();
        } else if (row.dataset.folderId) {
          // Clicked a folder
          state.folder_id = row.dataset.folderId;
          updateTreeActive();

          const fid = row.dataset.folderId;
          const chevron = container.querySelector(`[data-chevron="${fid}"]`) ||
                          document.querySelector(`[data-chevron="${fid}"]`);
          const children = container.querySelector(`[data-children="${fid}"]`) ||
                           document.querySelector(`[data-children="${fid}"]`);
          if (chevron && children) {
            await loadTreeChildren(fid, false, children, chevron);
          }
          await loadList();
        }
      });
    });
  }

  async function initTree(roots) {
    treeRoots = roots;
    const sidebar = $('sidebarTree');
    sidebar.innerHTML = roots.map(r => buildRootTreeNode(r)).join('');
    bindTreeEvents(sidebar);
    updateTreeActive();
  }

  // ── Breadcrumbs ────────────────────────────────────────────────────────────

  async function loadBreadcrumbs() {
    if (!state.root_id) return;
    const params = new URLSearchParams();
    params.set('root_id', state.root_id);
    if (state.folder_id) params.set('folder_id', state.folder_id);
    const out = await api(window.SHFI.routes.breadcrumbs + '?' + params.toString());
    state.path = out.data || [];
  }

  function renderBreadcrumbs() {
    const el = $('breadcrumbs');
    const rootName = $('rootSelect').options[$('rootSelect').selectedIndex]?.text || 'Root';
    const parts = [];

    parts.push(`<a href="#" data-crumb="root">🗄️ ${escapeHtml(rootName)}</a>`);
    state.path.forEach((p, idx) => {
      parts.push(`<span class="sep">›</span>`);
      const isLast = idx === state.path.length - 1;
      parts.push(`<a href="#" data-crumb="${idx}" data-id="${p.id}" class="${isLast ? 'crumb-active' : ''}">${escapeHtml(p.name)}</a>`);
    });

    el.innerHTML = parts.join('');

    el.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', async e => {
        e.preventDefault();
        const c = a.dataset.crumb;
        if (c === 'root') {
          state.folder_id = null;
        } else {
          const idx = parseInt(c, 10);
          state.folder_id = state.path[idx].id;
        }
        updateTreeActive();
        await loadBreadcrumbs();
        await loadList();
      });
    });
  }

  // ── Table Rows ─────────────────────────────────────────────────────────────

  function rowHtmlFolder(f) {
    return `
      <tr data-type="folder" data-id="${f.id}" data-name="${escapeAttr(f.name)}">
        <td>
          <div class="name-cell">
            <span class="fi-icon">📁</span>
            <strong>${escapeHtml(f.name)}</strong>
          </div>
        </td>
        <td><span class="badge folder">Folder</span></td>
        <td>—</td>
        <td style="font-size:12px;color:var(--text3)">${f.created_at ? new Date(f.created_at).toLocaleString('id-ID') : '—'}</td>
      </tr>`;
  }

  function rowHtmlFile(f) {
    return `
      <tr data-type="file" data-id="${f.id}" data-name="${escapeAttr(f.name)}" data-download="${escapeAttr(f.download_url || '')}">
        <td>
          <div class="name-cell">
            <span class="fi-icon">${getFileIcon(f.mime_type)}</span>
            <a href="${f.download_url}" target="_blank">${escapeHtml(f.name)}</a>
          </div>
        </td>
        <td><span class="badge">${escapeHtml(f.mime_type || 'file')}</span></td>
        <td style="font-family:var(--mono);font-size:12px">${formatBytes(f.size || 0)}</td>
        <td style="font-size:12px;color:var(--text3)">${f.uploaded_at ? new Date(f.uploaded_at).toLocaleString('id-ID') : '—'}</td>
      </tr>`;
  }

  function getFileIcon(mime) {
    if (!mime) return '📄';
    if (mime.startsWith('image/')) return '🖼️';
    if (mime.startsWith('video/')) return '🎬';
    if (mime.startsWith('audio/')) return '🎵';
    if (mime.includes('pdf')) return '📕';
    if (mime.includes('word') || mime.includes('document')) return '📝';
    if (mime.includes('excel') || mime.includes('spreadsheet')) return '📊';
    if (mime.includes('zip') || mime.includes('rar') || mime.includes('archive')) return '🗜️';
    if (mime.includes('text')) return '📃';
    return '📄';
  }

  // ── Load Roots ─────────────────────────────────────────────────────────────

  async function loadRoots() {
    const out = await api(window.SHFI.routes.roots);
    const sel = $('rootSelect');
    sel.innerHTML = out.data.map(r => `<option value="${r.id}">${escapeHtml(r.name)}</option>`).join('');
    state.root_id = sel.value;
    await initTree(out.data);
  }

  // ── Load List ──────────────────────────────────────────────────────────────

  async function loadList() {
    await loadBreadcrumbs();
    renderBreadcrumbs();
    updateTreeActive();

    const params = new URLSearchParams();
    params.set('root_id', state.root_id);
    if (state.folder_id) params.set('folder_id', state.folder_id);

    const q = $('qInput').value.trim();
    if (q) params.set('q', q);

    const month = $('monthInput').value;
    const from  = $('fromInput').value;
    const to    = $('toInput').value;

    if (month) params.set('month', month);
    else {
      if (from) params.set('from', from);
      if (to)   params.set('to', to);
    }

    params.set('sort', $('sortSelect').value);
    params.set('dir',  $('dirSelect').value);

    const body = $('listBody');
    body.innerHTML = `<tr><td colspan="4" style="padding:20px;color:var(--text3);text-align:center"><span class="spinner"></span></td></tr>`;

    try {
      const out = await api(window.SHFI.routes.list + '?' + params.toString());
      const folders = out.data?.folders || [];
      const files   = out.data?.files   || [];

      state.lastFolders = folders;
      state.lastFiles   = files;

      if (!folders.length && !files.length) {
        body.innerHTML = `<tr><td colspan="4"><div class="empty-state"><div class="empty-icon">📭</div><p>This folder is empty.</p></div></td></tr>`;
      } else {
        body.innerHTML = folders.map(rowHtmlFolder).join('') + files.map(rowHtmlFile).join('');
      }

      updateStatusBar(folders, files);
    } catch(err) {
      body.innerHTML = `<tr><td colspan="4"><div class="empty-state"><div class="empty-icon">❌</div><p>${escapeHtml(err.message)}</p></div></td></tr>`;
      toast(err.message, 'error');
    }
  }

  // ── Status Bar ─────────────────────────────────────────────────────────────

  function updateStatusBar(folders, files) {
    $('statusFolders').textContent = folders.length;
    $('statusFiles').textContent = files.length;
    const total = files.reduce((s, f) => s + (f.size || 0), 0);
    $('statusSize').textContent = formatBytes(total);
  }

  // ── Folder navigation ─────────────────────────────────────────────────────

  function openFolder(tr) {
    state.folder_id = tr.dataset.id;
    loadList();
  }

  function downloadFile(tr) {
    const url = tr.dataset.download;
    if (url) { window.open(url, '_blank'); }
  }

  // ── New Folder Modal ───────────────────────────────────────────────────────

  function showNewFolderModal() {
    $('newFolderName').value = '';
    $('newFolderError').style.display = 'none';
    openModal('modalNewFolder');
    setTimeout(() => $('newFolderName').focus(), 80);
  }

  async function createFolder() {
    const name = $('newFolderName').value.trim();
    const errEl = $('newFolderError');

    if (!name) { errEl.textContent = 'Folder name is required.'; errEl.style.display = 'block'; return; }
    if (name.length > 120) { errEl.textContent = 'Name too long (max 120 characters).'; errEl.style.display = 'block'; return; }
    if (/[/\\:*?"<>|]/.test(name)) { errEl.textContent = 'Name contains invalid characters.'; errEl.style.display = 'block'; return; }

    const btn = $('btnNewFolderConfirm');
    btn.disabled = true; btn.textContent = 'Creating...';

    try {
      await api(window.SHFI.routes.folderCreate, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          root_id: parseInt(state.root_id, 10),
          parent_id: state.folder_id ? parseInt(state.folder_id, 10) : null,
          name
        })
      });
      closeModal('modalNewFolder');
      toast(`Folder "${name}" created.`, 'success');
      await loadList();
    } catch(err) {
      errEl.textContent = err.message;
      errEl.style.display = 'block';
    }

    btn.disabled = false; btn.textContent = 'Create Folder';
  }

  $('btnNewFolderConfirm').addEventListener('click', createFolder);
  $('newFolderName').addEventListener('keydown', e => { if (e.key === 'Enter') createFolder(); });

  // ── Rename Modal ───────────────────────────────────────────────────────────

  let pendingRenameTr = null;

  function showRenameModal(tr) {
    pendingRenameTr = tr;
    $('renameOldName').textContent = tr.dataset.name;
    $('renameNewName').value = tr.dataset.name;
    $('renameError').style.display = 'none';
    openModal('modalRename');
    setTimeout(() => { $('renameNewName').focus(); $('renameNewName').select(); }, 80);
  }

  async function doRename() {
    const tr = pendingRenameTr;
    if (!tr) return;

    const newName = $('renameNewName').value.trim();
    const errEl   = $('renameError');

    if (!newName) { errEl.textContent = 'Name is required.'; errEl.style.display='block'; return; }
    if (newName === tr.dataset.name) { closeModal('modalRename'); return; }
    if (/[/\\:*?"<>|]/.test(newName)) { errEl.textContent = 'Name contains invalid characters.'; errEl.style.display='block'; return; }

    const btn = $('btnRenameConfirm');
    btn.disabled = true; btn.textContent = 'Renaming...';

    try {
      await api(window.SHFI.routes.rename, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: tr.dataset.type, id: parseInt(tr.dataset.id, 10), new_name: newName })
      });
      closeModal('modalRename');
      toast(`Renamed to "${newName}".`, 'success');
      await loadList();
    } catch(err) {
      errEl.textContent = err.message;
      errEl.style.display = 'block';
    }

    btn.disabled = false; btn.textContent = 'Rename';
  }

  $('btnRenameConfirm').addEventListener('click', doRename);
  $('renameNewName').addEventListener('keydown', e => { if (e.key === 'Enter') doRename(); });

  // ── Delete Modal ───────────────────────────────────────────────────────────

  let pendingDeleteTr = null;

  function showDeleteModal(tr) {
    pendingDeleteTr = tr;
    const name = tr.dataset.name;
    const label = tr.dataset.type === 'folder' ? `folder "${name}" and all its contents` : `file "${name}"`;
    $('deleteItemName').textContent = label;
    openModal('modalDelete');
  }

  async function doDelete() {
    const tr = pendingDeleteTr;
    if (!tr) return;

    const btn = $('btnDeleteConfirm');
    btn.disabled = true; btn.textContent = 'Moving to trash...';

    try {
      const payload = { type: tr.dataset.type, id: parseInt(tr.dataset.id, 10) };
      if (tr.dataset.type === 'folder') payload.confirm = true;

      await api(window.SHFI.routes.delete, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      closeModal('modalDelete');
      toast(`"${tr.dataset.name}" moved to trash.`, 'info');
      await loadList();
    } catch(err) {
      toast(err.message, 'error');
    }

    btn.disabled = false; btn.textContent = 'Move to Trash';
  }

  $('btnDeleteConfirm').addEventListener('click', doDelete);

  // ── Copy / Cut / Paste ─────────────────────────────────────────────────────

  function copyCut(tr, mode) {
    state.clipboard = { mode, type: tr.dataset.type, id: parseInt(tr.dataset.id, 10), name: tr.dataset.name };
    toast(`"${tr.dataset.name}" ${mode === 'copy' ? 'copied' : 'cut'}. Navigate to destination and paste (Ctrl+V).`, 'info', 5000);
  }

  async function pasteHere() {
    if (!state.clipboard) return toast('Clipboard is empty.', 'warn');
    if (!state.folder_id) return toast('Please open a destination folder first.', 'warn');

    const { mode, type, id, name } = state.clipboard;
    const payload = { type, id, target_folder_id: parseInt(state.folder_id, 10) };
    const url = mode === 'copy' ? window.SHFI.routes.copy : window.SHFI.routes.move;

    try {
      await api(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      if (mode === 'cut') state.clipboard = null;
      toast(`"${name}" ${mode === 'copy' ? 'copied' : 'moved'} successfully.`, 'success');
      await loadList();
    } catch(err) {
      toast(err.message, 'error');
    }
  }

  // ── Upload ─────────────────────────────────────────────────────────────────

  async function uploadFiles(files) {
    let successCount = 0, failCount = 0;

    for (const file of files) {
      const fd = new FormData();
      fd.append('root_id', state.root_id);
      if (state.folder_id) fd.append('folder_id', state.folder_id);
      fd.append('file', file);

      try {
        await api(window.SHFI.routes.upload, { method: 'POST', body: fd });
        successCount++;
      } catch(err) {
        failCount++;
        toast(`Failed to upload "${file.name}": ${err.message}`, 'error');
      }
    }

    if (successCount > 0) {
      toast(`${successCount} file${successCount > 1 ? 's' : ''} uploaded successfully.`, 'success');
      await loadList();
    }
    if (failCount > 0) {
      toast(`${failCount} file${failCount > 1 ? 's' : ''} failed to upload.`, 'error');
    }
  }

  // ── Drag & Drop ────────────────────────────────────────────────────────────

  const dragOverlay = $('dragOverlay');
  let dragCounter = 0;

  document.addEventListener('dragenter', e => {
    if (!e.dataTransfer?.types?.includes('Files')) return;
    dragCounter++;
    if (dragCounter === 1) dragOverlay.classList.add('visible');
  });

  document.addEventListener('dragleave', () => {
    dragCounter--;
    if (dragCounter <= 0) { dragCounter = 0; dragOverlay.classList.remove('visible'); }
  });

  document.addEventListener('dragover', e => e.preventDefault());

  document.addEventListener('drop', async e => {
    e.preventDefault();
    dragCounter = 0;
    dragOverlay.classList.remove('visible');

    const files = Array.from(e.dataTransfer.files || []);
    if (!files.length) return;

    if (!state.root_id) return toast('Please select a root first.', 'warn');
    await uploadFiles(files);
  });

  // ── Event Bindings ─────────────────────────────────────────────────────────

  function bindEvents() {
    $('rootSelect').addEventListener('change', async e => {
      state.root_id = e.target.value;
      state.folder_id = null;
      state.path = [];
      updateTreeActive();
      await loadList();
    });

    ['qInput', 'monthInput', 'fromInput', 'toInput', 'sortSelect', 'dirSelect'].forEach(id => {
      $(id).addEventListener('change', loadList);
      if (id === 'qInput') $(id).addEventListener('keyup', debounce(loadList, 300));
    });

    $('btnNewFolder').addEventListener('click', showNewFolderModal);

    $('uploadInput').addEventListener('change', async e => {
      const files = Array.from(e.target.files || []);
      e.target.value = '';
      if (files.length) await uploadFiles(files);
    });

    // Double-click to open folder
    $('listBody').addEventListener('dblclick', e => {
      const tr = e.target.closest('tr');
      if (!tr || tr.dataset.type !== 'folder') return;
      openFolder(tr);
    });

    // Right-click context menu
    $('listBody').addEventListener('contextmenu', e => {
      e.preventDefault();
      const tr = e.target.closest('tr[data-type]');
      showCtxMenu(e.clientX, e.clientY, tr || null);
    });

    // Ctrl+V paste shortcut
    document.addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'v') {
        pasteHere();
      }
    });
  }

  // ── Boot ───────────────────────────────────────────────────────────────────

  async function boot() {
    await loadRoots();
    bindEvents();
    await loadList();
  }

  boot().catch(err => toast(err.message || String(err), 'error'));

})();