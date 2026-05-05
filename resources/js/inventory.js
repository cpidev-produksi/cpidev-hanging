(function () {
  const $ = (id) => document.getElementById(id);

  let state = {
    root_id: null,
    folder_id: null,
    path: [], // [{id,name}]
    clipboard: null // {mode:'copy'|'cut', type:'file'|'folder', id}
  };

  async function api(url, opts = {}) {
    const res = await fetch(url, {
      headers: {
        'X-CSRF-TOKEN': window.SHFI.csrf,
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      ...opts
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
  }

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

    const parts = [];
    parts.push(`<a href="#" data-crumb="root">Root</a>`);
    state.path.forEach((p, idx) => {
      parts.push(`<a href="#" data-crumb="${idx}" data-id="${p.id}">${escapeHtml(p.name)}</a>`);
    });

    el.innerHTML = parts.join(' / ');

    el.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', async (e) => {
        e.preventDefault();
        const c = a.dataset.crumb;
        if (c === 'root') {
          state.folder_id = null;
        } else {
          const idx = parseInt(c, 10);
          state.folder_id = state.path[idx].id;
        }
        await loadBreadcrumbs();
        await loadList();
      });
    });
  }

  function rowHtmlFolder(f) {
    return `
      <tr data-type="folder" data-id="${f.id}" data-name="${escapeAttr(f.name)}">
        <td><strong>📁 ${escapeHtml(f.name)}</strong></td>
        <td>Folder</td>
        <td>—</td>
        <td>${f.created_at ? new Date(f.created_at).toLocaleString() : '—'}</td>
      </tr>`;
  }

  function rowHtmlFile(f) {
    return `
      <tr data-type="file" data-id="${f.id}" data-name="${escapeAttr(f.name)}">
        <td>📄 <a href="${f.download_url}" target="_blank">${escapeHtml(f.name)}</a></td>
        <td>${escapeHtml(f.mime_type || 'file')}</td>
        <td>${formatBytes(f.size || 0)}</td>
        <td>${f.uploaded_at ? new Date(f.uploaded_at).toLocaleString() : '—'}</td>
      </tr>`;
  }

  async function loadRoots() {
    const out = await api(window.SHFI.routes.roots);
    const sel = $('rootSelect');
    sel.innerHTML = out.data.map(r => `<option value="${r.id}">${escapeHtml(r.name)}</option>`).join('');
    state.root_id = sel.value;
  }

  async function loadList() {
    await loadBreadcrumbs();
    renderBreadcrumbs();

    const params = new URLSearchParams();
    params.set('root_id', state.root_id);
    if (state.folder_id) params.set('folder_id', state.folder_id);

    const q = $('qInput').value.trim();
    if (q) params.set('q', q);

    const month = $('monthInput').value;
    const from = $('fromInput').value;
    const to = $('toInput').value;

    if (month) params.set('month', month);
    else {
      if (from) params.set('from', from);
      if (to) params.set('to', to);
    }

    params.set('sort', $('sortSelect').value);
    params.set('dir', $('dirSelect').value);

    const out = await api(window.SHFI.routes.list + '?' + params.toString());
    const body = $('listBody');

    const folders = out.data.folders || [];
    const files = out.data.files || [];

    if (!folders.length && !files.length) {
      body.innerHTML = `<tr><td colspan="4" style="color:#6b7896;">Tidak ada data</td></tr>`;
      return;
    }

    body.innerHTML = folders.map(rowHtmlFolder).join('') + files.map(rowHtmlFile).join('');
  }

  async function createFolder() {
    const name = prompt('Nama folder:');
    if (!name) return;

    const payload = {
      root_id: parseInt(state.root_id, 10),
      parent_id: state.folder_id ? parseInt(state.folder_id, 10) : null,
      name
    };

    await api(window.SHFI.routes.folderCreate, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.SHFI.csrf, 'Accept': 'application/json'},
      body: JSON.stringify(payload)
    });

    await loadList();
  }

  async function uploadFile(file) {
    const fd = new FormData();
    fd.append('root_id', state.root_id);
    if (state.folder_id) fd.append('folder_id', state.folder_id);
    fd.append('file', file);

    await api(window.SHFI.routes.upload, { method: 'POST', body: fd });
    await loadList();
  }

  async function renameSelected(tr) {
    const type = tr.dataset.type;
    const id = tr.dataset.id;
    const oldName = tr.dataset.name;

    const newName = prompt('Rename:', oldName);
    if (!newName || newName === oldName) return;

    await api(window.SHFI.routes.rename, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.SHFI.csrf, 'Accept': 'application/json'},
      body: JSON.stringify({type, id: parseInt(id,10), new_name: newName})
    });
    await loadList();
  }

  async function deleteSelected(tr) {
    const type = tr.dataset.type;
    const id = tr.dataset.id;
    const name = tr.dataset.name;

    if (type === 'folder') {
      const ok = confirm(`Hapus folder "${name}" beserta seluruh isinya?`);
      if (!ok) return;

      await api(window.SHFI.routes.delete, {
        method: 'DELETE',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.SHFI.csrf, 'Accept': 'application/json'},
        body: JSON.stringify({type, id: parseInt(id,10), confirm: true})
      });
    } else {
      const ok = confirm(`Hapus file "${name}"?`);
      if (!ok) return;

      await api(window.SHFI.routes.delete, {
        method: 'DELETE',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.SHFI.csrf, 'Accept': 'application/json'},
        body: JSON.stringify({type, id: parseInt(id,10)})
      });
    }

    await loadList();
  }

  function copyCut(tr, mode) {
    state.clipboard = {mode, type: tr.dataset.type, id: parseInt(tr.dataset.id,10)};
    alert(`${mode.toUpperCase()} siap. Silakan buka folder tujuan lalu Paste.`);
  }

  async function pasteHere() {
    if (!state.clipboard) return alert('Clipboard kosong.');

    if (!state.folder_id) {
      return alert('Paste disini tidak diperbolehkan. Silakan masuk ke folder tujuan terlebih dahulu.');
    }

    const {mode, type, id} = state.clipboard;
    const payload = {type, id, target_folder_id: parseInt(state.folder_id,10)};

    if (mode === 'copy') {
      await api(window.SHFI.routes.copy, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':window.SHFI.csrf,'Accept':'application/json'},
        body: JSON.stringify(payload)
      });
    } else {
      await api(window.SHFI.routes.move, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':window.SHFI.csrf,'Accept':'application/json'},
        body: JSON.stringify(payload)
      });
      state.clipboard = null;
    }

    await loadList();
  }

  function bindEvents() {
    $('rootSelect').addEventListener('change', async (e) => {
      state.root_id = e.target.value;
      state.folder_id = null;
      state.path = [];
      await loadList();
    });

    ['qInput','monthInput','fromInput','toInput','sortSelect','dirSelect'].forEach(id => {
      $(id).addEventListener('change', loadList);
      if (id === 'qInput') $(id).addEventListener('keyup', debounce(loadList, 300));
    });

    $('btnNewFolder').addEventListener('click', createFolder);

    $('uploadInput').addEventListener('change', async (e) => {
      const file = e.target.files?.[0];
      e.target.value = '';
      if (file) await uploadFile(file);
    });

    $('listBody').addEventListener('dblclick', async (e) => {
      const tr = e.target.closest('tr');
      if (!tr) return;
      if (tr.dataset.type === 'folder') {
        state.folder_id = tr.dataset.id;
        await loadList();
      }
    });

    // context menu sederhana via right click
    $('listBody').addEventListener('contextmenu', async (e) => {
      const tr = e.target.closest('tr');
      if (!tr) return;
      e.preventDefault();

      const action = prompt('Action: rename | delete | copy | cut | paste', 'rename');
      if (!action) return;

      if (action === 'rename') return renameSelected(tr);
      if (action === 'delete') return deleteSelected(tr);
      if (action === 'copy') return copyCut(tr, 'copy');
      if (action === 'cut') return copyCut(tr, 'cut');
      if (action === 'paste') return pasteHere();
    });

    // shortcut paste: Ctrl+V
    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'v') {
        pasteHere();
      }
    });
  }

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, (m) => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  function escapeAttr(str) {
    return escapeHtml(str).replace(/"/g, '&quot;');
  }

  function formatBytes(bytes) {
    const units = ['B','KB','MB','GB','TB'];
    let b = bytes, i = 0;
    while (b >= 1024 && i < units.length - 1) { b /= 1024; i++; }
    return `${b.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
  }

  function debounce(fn, ms) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  }

  async function boot() {
    await loadRoots();
    bindEvents();
    await loadList();
  }

  boot().catch(err => alert(err.message || String(err)));
})();