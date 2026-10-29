<style>
    /* ===== Cut Up (Daftar Produk & Metal Detector) — memakai variabel dari layouts.app ===== */
    .cu-wrap { max-width: 1180px; margin: 0 auto; }
    .cu-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
    .cu-title { font-family:'Syne',sans-serif; font-weight:800; font-size:22px; color:var(--text-main); line-height:1.15; }
    .cu-sub { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:4px; }
    .cu-crumb { font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:var(--accent); margin-bottom:6px; }

    /* Buttons */
    .cu-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 16px; border-radius:10px; border:1px solid var(--card-border); background:#fff; color:var(--text-main); font-size:13px; font-weight:700; font-family:inherit; cursor:pointer; text-decoration:none; line-height:1; transition:background .15s,border-color .15s,transform .05s; white-space:nowrap; }
    .cu-btn:hover { background:#f5f7fc; border-color:#d0d7e6; }
    .cu-btn:active { transform:translateY(1px); }
    .cu-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
    .cu-btn.primary:hover { background:#d44f24; border-color:#d44f24; }
    .cu-btn.danger { background:#fff; color:var(--error); border-color:rgba(239,68,68,.35); }
    .cu-btn.danger:hover { background:rgba(239,68,68,.08); }
    .cu-btn.solid-danger { background:var(--error); border-color:var(--error); color:#fff; }
    .cu-btn.solid-danger:hover { background:#dc2626; border-color:#dc2626; }
    .cu-btn.sm { padding:7px 11px; font-size:12px; border-radius:8px; }
    .cu-btn[disabled] { opacity:.6; cursor:not-allowed; }

    /* Form controls */
    .cu-label { display:block; font-size:11px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px; }
    .cu-input, .cu-select { width:100%; padding:10px 12px; border:1px solid var(--card-border); border-radius:10px; background:#fff; font-size:14px; font-family:inherit; color:var(--text-main); font-weight:500; outline:none; transition:border-color .15s, box-shadow .15s; }
    .cu-input:focus, .cu-select:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(232,93,47,.12); }
    .cu-input[readonly] { background:#f5f7fc; color:var(--text-muted); }
    .cu-input.is-error { border-color:var(--error); }
    .cu-hint { font-size:12px; color:var(--text-muted); margin-top:5px; font-weight:500; }
    .cu-unit { position:relative; }
    .cu-unit .cu-input { padding-right:44px; }
    .cu-unit::after { content:'mm'; position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:12px; font-weight:700; color:var(--text-muted); pointer-events:none; }

    /* Grid */
    .cu-grid { display:grid; gap:14px; }
    .cu-grid.c2 { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .cu-grid.c3 { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .cu-grid.c4 { grid-template-columns:repeat(4,minmax(0,1fr)); }
    @media (max-width:820px){ .cu-grid.c3, .cu-grid.c4 { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:520px){ .cu-grid.c2, .cu-grid.c3, .cu-grid.c4 { grid-template-columns:1fr; } }

    /* Stat cards */
    .cu-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:22px; }
    @media (max-width:900px){ .cu-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    .cu-stat { background:#fff; border:1px solid var(--card-border); border-radius:16px; padding:16px 18px; box-shadow:0 1px 4px rgba(0,0,0,.04); }
    .cu-stat-label { font-size:11px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--text-muted); }
    .cu-stat-value { font-family:'Syne',sans-serif; font-weight:800; font-size:28px; margin-top:6px; line-height:1; color:var(--text-main); }
    .cu-stat-note { font-size:12px; color:var(--text-muted); margin-top:6px; font-weight:500; }
    .cu-stat.warn .cu-stat-value { color:var(--error); }
    .cu-stat.good .cu-stat-value { color:var(--success); }

    /* Toolbar / filter */
    .cu-toolbar { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; padding:16px 20px; border-bottom:1px solid var(--card-border); }
    .cu-toolbar .f { min-width:140px; }
    .cu-toolbar .f.grow { flex:1; min-width:220px; }

    /* Badges */
    .cu-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; letter-spacing:.02em; }
    .cu-badge.neutral { background:#eef1f8; color:#4a5878; }
    .cu-badge.ok { background:rgba(16,185,129,.12); color:#0b8f64; }
    .cu-badge.bad { background:rgba(239,68,68,.12); color:#c42b2b; }
    .cu-badge.accent { background:rgba(232,93,47,.12); color:var(--accent); }

    .cu-actions { display:flex; gap:6px; justify-content:flex-end; flex-wrap:wrap; }
    .cu-table-wrap { overflow-x:auto; }
    .cu-empty { text-align:center; padding:48px 20px; color:var(--text-muted); font-size:14px; font-weight:500; }
    .cu-empty strong { display:block; color:var(--text-main); font-size:15px; margin-bottom:4px; }
    .cu-mono { font-variant-numeric:tabular-nums; font-feature-settings:'tnum'; }

    /* Pager */
    .cu-pager { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-top:1px solid var(--card-border); font-size:12px; color:var(--text-muted); font-weight:600; flex-wrap:wrap; }
    .cu-pager .pg { display:flex; gap:8px; }

    /* Modal */
    .cu-overlay { position:fixed; inset:0; background:rgba(13,17,23,.45); backdrop-filter:blur(3px); z-index:200; display:none; align-items:center; justify-content:center; padding:20px; }
    .cu-overlay.open { display:flex; }
    .cu-modal { width:100%; max-width:460px; background:#fff; border-radius:18px; box-shadow:0 24px 60px rgba(0,0,0,.22); overflow:hidden; }
    .cu-modal-head { padding:18px 22px; border-bottom:1px solid var(--card-border); background:#fafbff; display:flex; align-items:center; justify-content:space-between; }
    .cu-modal-title { font-family:'Syne',sans-serif; font-weight:800; font-size:16px; }
    .cu-modal-x { border:none; background:transparent; cursor:pointer; color:var(--text-muted); font-size:22px; line-height:1; padding:0 4px; }
    .cu-modal-body { padding:20px 22px; }
    .cu-modal-foot { padding:14px 22px; border-top:1px solid var(--card-border); display:flex; justify-content:flex-end; gap:10px; background:#fafbff; }
    .cu-modal-text { font-size:14px; color:var(--text-main); line-height:1.5; font-weight:500; }
    .cu-field-error { font-size:12px; color:var(--error); font-weight:600; margin-top:6px; }

    /* Segmented (radio) */
    .seg { display:inline-flex; border:1px solid var(--card-border); border-radius:10px; overflow:hidden; background:#fff; max-width:100%; }
    .seg-opt { position:relative; cursor:pointer; margin:0; }
    .seg-opt input { position:absolute; opacity:0; width:100%; height:100%; left:0; top:0; margin:0; cursor:pointer; }
    .seg-opt span { display:block; padding:9px 14px; font-size:12px; font-weight:800; color:var(--text-muted); border-right:1px solid var(--card-border); transition:background .12s,color .12s; white-space:nowrap; }
    .seg-opt:last-child span { border-right:none; }
    .seg-opt:hover span { background:#f5f7fc; }
    .seg-opt input:focus-visible + span { outline:2px solid var(--accent); outline-offset:-2px; }
    .seg-opt input:checked + span { background:var(--accent); color:#fff; }
    .seg-opt.ok input:checked + span, .seg-opt.use input:checked + span { background:var(--success); color:#fff; }
    .seg-opt.bad input:checked + span, .seg-opt.nouse input:checked + span { background:var(--error); color:#fff; }
    .seg.sm .seg-opt span { padding:6px 10px; font-size:11px; }
    .seg.block { display:flex; width:100%; } .seg.block .seg-opt { flex:1; } .seg.block .seg-opt span { text-align:center; }

    /* Form sections & rows */
    .cu-section-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
    .cu-step { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(232,93,47,.12); color:var(--accent); font-size:12px; font-weight:800; margin-right:10px; }
    .rows { counter-reset:rowno; display:flex; flex-direction:column; gap:12px; }
    .row-card { counter-increment:rowno; border:1px solid var(--card-border); border-radius:14px; padding:14px 16px 16px; background:#fcfdff; position:relative; }
    .row-card.is-bad { border-color:rgba(239,68,68,.45); background:#fffafa; }
    .row-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
    .row-no::before { content:'#' counter(rowno); font-size:12px; font-weight:800; color:var(--text-muted); letter-spacing:.04em; }
    .row-tools { display:flex; gap:6px; }
    .row-empty { border:1.5px dashed var(--card-border); border-radius:14px; padding:26px 16px; text-align:center; font-size:13px; color:var(--text-muted); font-weight:500; }
    .spec-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-top:14px; }
    @media (max-width:820px){ .spec-grid { grid-template-columns:1fr; } }
    .spec-box { border:1px solid var(--card-border); border-radius:12px; padding:10px 12px; background:#fff; }
    .spec-title { font-size:12px; font-weight:800; color:var(--text-main); margin-bottom:8px; display:flex; justify-content:space-between; gap:6px; }
    .spec-title em { font-style:normal; color:var(--text-muted); font-weight:700; }
    .spec-line { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:4px 0; }
    .spec-line b { font-size:12px; color:var(--text-muted); width:60px; font-weight:700; }

    /* Combobox produk */
    .combo { position:relative; }
    .combo-list { position:absolute; z-index:40; left:0; right:0; top:calc(100% + 4px); background:#fff; border:1px solid var(--card-border); border-radius:12px; box-shadow:0 14px 36px rgba(0,0,0,.12); max-height:230px; overflow:auto; padding:4px; display:none; list-style:none; margin:0; }
    .combo-list.open { display:block; }
    .combo-list li { padding:9px 10px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
    .combo-list li:hover, .combo-list li.hl { background:rgba(232,93,47,.08); color:var(--accent); }
    .combo-list li.none { color:var(--text-muted); cursor:default; background:transparent; }

    .cu-actionbar { position:sticky; bottom:0; z-index:30; margin-top:6px; padding:14px 0 6px; background:linear-gradient(to top, var(--bg) 70%, rgba(240,242,247,0)); display:flex; justify-content:flex-end; gap:10px; }
</style>
