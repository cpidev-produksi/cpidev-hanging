@extends('layouts.app')

@section('content')
<div class="form-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('master.expeditions.index') }}" class="breadcrumb-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="6" width="18" height="13" rx="2"/>
                <path d="M7 10h6"/>
                <path d="M7 14h10"/>
                <path d="M8 3h8"/>
            </svg>
            Data Ekspedisi
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
        <span>Tambah Ekspedisi</span>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div class="page-icon-wrap">
            <div class="page-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="6" width="18" height="13" rx="2"/>
                    <path d="M7 10h6"/>
                    <path d="M7 14h10"/>
                    <path d="M8 3h8"/>
                </svg>
            </div>
        </div>
        <div>
            <h1 class="page-title">Tambah Ekspedisi Baru</h1>
            <p class="page-subtitle">Isi formulir di bawah untuk menambahkan ekspedisi</p>
        </div>
    </div>

    {{-- Form Card --}}
    <form method="POST" action="{{ route('master.expeditions.store') }}" class="form-card">
        @csrf

        <div class="form-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Informasi Ekspedisi
        </div>

        <div class="form-grid">
            {{-- Nama Ekspedisi --}}
            <div class="form-group full-width">
                <label class="form-label" for="name">
                    Nama Ekspedisi
                    <span class="required">*</span>
                </label>
                <div class="input-wrapper @error('name') has-error @enderror">
                    <div class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="6" width="18" height="13" rx="2"/>
                            <path d="M7 10h6"/>
                            <path d="M7 14h10"/>
                        </svg>
                    </div>
                    <input id="name" name="name" type="text" value="{{ old('name') }}"
                           class="form-input" placeholder="Masukkan nama ekspedisi">
                </div>
                @error('name')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Plate Numbers --}}
            <div class="form-group full-width">
                <label class="form-label">
                    Plate Number + Sopir
                </label>

                @error('plate_numbers')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror

                <div class="table-wrap @error('plate_numbers.*.plate_number') has-error @enderror">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:28%;">No Polisi</th>
                                <th style="width:36%;">Nama Sopir</th>
                                <th style="width:26%;">No HP Sopir</th>
                                <th style="width:10%;"></th>
                            </tr>
                        </thead>
                        <tbody id="plateRows">
                            {{-- injected --}}
                        </tbody>
                    </table>
                </div>

                @error('plate_numbers.*.plate_number')
                    <div class="form-error" style="margin-top:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror

                <div class="table-actions">
                    <button type="button" id="btnAddRow" class="btn-add-row">
                        + Tambah Baris
                    </button>
                </div>

                <div class="help-text">
                    Catatan: 1 plat nomor hanya boleh dipakai oleh 1 ekspedisi.
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="form-footer">
            <a href="{{ route('master.expeditions.index') }}" class="btn-cancel">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Batal
            </a>
            <button type="submit" class="btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Simpan Ekspedisi
            </button>
        </div>
    </form>
</div>

<script>
const oldRows = @json(old('plate_numbers', []));

const plateRowsEl = document.getElementById('plateRows');
const btnAddRow = document.getElementById('btnAddRow');

function escapeHtml(str) {
  return String(str ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function rowTemplate(i, row = {}) {
  const plate = (row && typeof row === 'object') ? (row.plate_number ?? '') : (row ?? '');
  const driverName = (row && typeof row === 'object') ? (row.driver_name ?? '') : '';
  const driverPhone = (row && typeof row === 'object') ? (row.driver_phone ?? '') : '';

  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>
      <input class="form-input" name="plate_numbers[${i}][plate_number]" value="${escapeHtml(plate)}" placeholder="B 1234 XX">
    </td>
    <td>
      <input class="form-input" name="plate_numbers[${i}][driver_name]" value="${escapeHtml(driverName)}" placeholder="Nama sopir">
    </td>
    <td>
      <input class="form-input" name="plate_numbers[${i}][driver_phone]" value="${escapeHtml(driverPhone)}" placeholder="08xxxxxxxxxx">
    </td>
    <td style="text-align:right;">
      <button type="button" class="btn-remove">Hapus</button>
    </td>
  `;

  tr.querySelector('.btn-remove').addEventListener('click', () => {
    tr.remove();
    reindexRows();
  });

  return tr;
}

function reindexRows() {
  [...plateRowsEl.querySelectorAll('tr')].forEach((tr, idx) => {
    const inputs = tr.querySelectorAll('input');
    inputs.forEach((input) => {
      input.name = input.name
        .replace(/plate_numbers\[\d+\]\[plate_number\]/, `plate_numbers[${idx}][plate_number]`)
        .replace(/plate_numbers\[\d+\]\[driver_name\]/, `plate_numbers[${idx}][driver_name]`)
        .replace(/plate_numbers\[\d+\]\[driver_phone\]/, `plate_numbers[${idx}][driver_phone]`);
    });
  });
}

function addRow(row = {}) {
  const i = plateRowsEl.querySelectorAll('tr').length;
  plateRowsEl.appendChild(rowTemplate(i, row));
}

btnAddRow.addEventListener('click', () => addRow({}));

document.addEventListener('DOMContentLoaded', () => {
  if (oldRows && oldRows.length) {
    oldRows.forEach(r => addRow(r));
  } else {
    addRow({});
  }
});
</script>

<style>
:root {
    --c-bg: #F5F6FA;
    --c-card: #FFFFFF;
    --c-border: #E8EAF0;
    --c-text: #1A1D2E;
    --c-muted: #6B7280;
    --c-accent: #4F67FF;
    --c-accent-hover: #3D53E8;
    --c-accent-light: #EEF0FF;
    --c-danger: #F03E3E;
    --c-danger-light: #FFF5F5;
    --c-success: #0CA678;
    --radius: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
}

.form-page { max-width: 720px; margin: 0 auto; padding: 28px 20px; }

/* Breadcrumb */
.breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: .78rem; color: var(--c-muted);
    margin-bottom: 20px;
}
.breadcrumb-link {
    display: inline-flex; align-items: center; gap: 5px;
    color: var(--c-accent); text-decoration: none; font-weight: 500;
    transition: opacity .15s;
}
.breadcrumb-link:hover { opacity: .75; }
.breadcrumb svg { color: #C5C9D6; }

/* Header */
.page-header {
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 24px;
}
.page-icon {
    width: 48px; height: 48px;
    background: var(--c-accent-light);
    color: var(--c-accent);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.page-title { font-size: 1.3rem; font-weight: 700; color: var(--c-text); margin: 0 0 3px; }
.page-subtitle { font-size: .8rem; color: var(--c-muted); margin: 0; }

/* Form Card */
.form-card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 28px;
}

.form-section-title {
    display: flex; align-items: center; gap: 7px;
    font-size: .72rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--c-accent);
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--c-border);
}

/* Grid */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 28px;
}
.full-width { grid-column: 1 / -1; }
@media (max-width: 600px) {
    .form-grid { grid-template-columns: 1fr; }
    .full-width { grid-column: auto; }
}

/* Labels */
.form-label {
    display: block;
    font-size: .8rem; font-weight: 600;
    color: var(--c-text);
    margin-bottom: 6px;
}
.required { color: var(--c-danger); margin-left: 2px; }

/* Inputs */
.input-wrapper {
    position: relative;
    display: flex; align-items: center;
    border: 1.5px solid var(--c-border);
    border-radius: 9px;
    background: #FAFBFD;
    transition: border-color .18s, box-shadow .18s;
    overflow: hidden;
}
.input-wrapper:focus-within {
    border-color: var(--c-accent);
    box-shadow: 0 0 0 3px rgba(79,103,255,.12);
    background: #fff;
}
.input-wrapper.has-error {
    border-color: var(--c-danger);
    background: var(--c-danger-light);
}
.input-wrapper.has-error:focus-within {
    box-shadow: 0 0 0 3px rgba(240,62,62,.1);
}

.input-icon {
    display: flex; align-items: center; justify-content: center;
    width: 40px; min-width: 40px;
    color: var(--c-muted);
    pointer-events: none;
}

.form-input {
    flex: 1;
    border: none; outline: none;
    background: transparent;
    padding: 10px 12px 10px 0;
    font-size: .875rem;
    color: var(--c-text);
    width: 100%;
}
.form-input::placeholder { color: #B0B7C3; }

/* Table */
.table-wrap {
    border: 1px solid var(--c-border);
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
}
.table { width:100%; border-collapse: collapse; }
.table thead th {
    text-align:left;
    padding: 12px 12px;
    font-size: .75rem;
    color: var(--c-muted);
    background: #FAFBFD;
    border-bottom: 1px solid var(--c-border);
    font-weight: 700;
}
.table tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #F1F2F6;
    vertical-align: top;
}
.table tbody tr:last-child td { border-bottom: none; }
.table .form-input { padding-left: 12px; }

/* Table actions */
.table-actions { margin-top: 12px; display: flex; justify-content: flex-end; }

.btn-add-row {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border: 1.5px solid var(--c-border);
    border-radius: 9px;
    font-size: .84rem; font-weight: 500;
    color: var(--c-accent);
    background: #fff;
    cursor: pointer;
    transition: all .15s;
}
.btn-add-row:hover { border-color: var(--c-accent); background: var(--c-accent-light); }

/* Remove button */
.btn-remove {
    padding: 6px 12px;
    border-radius: 7px;
    border: 1.5px solid #F7C9C9;
    background: #fff;
    color: var(--c-danger);
    font-weight: 600;
    font-size: .78rem;
    cursor: pointer;
    transition: all .15s;
}
.btn-remove:hover { background: var(--c-danger); color: #fff; border-color: var(--c-danger); }

/* Error */
.form-error {
    display: flex; align-items: center; gap: 5px;
    color: var(--c-danger);
    font-size: .76rem; font-weight: 500;
    margin-top: 5px;
}

/* Footer */
.form-footer {
    display: flex; align-items: center; gap: 10px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid var(--c-border);
}

.btn-cancel {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border: 1.5px solid var(--c-border);
    border-radius: 9px;
    font-size: .84rem; font-weight: 500;
    color: var(--c-muted);
    text-decoration: none;
    background: #fff;
    transition: all .15s;
}
.btn-cancel:hover { border-color: #C5C9D6; color: var(--c-text); background: #F5F6FA; }

.btn-submit {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 22px;
    background: var(--c-accent);
    color: #fff;
    border: none; border-radius: 9px;
    font-size: .84rem; font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(79,103,255,.28);
    transition: all .18s;
}
.btn-submit:hover {
    background: var(--c-accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(79,103,255,.35);
}
.btn-submit:active { transform: translateY(0); }

.help-text { margin-top: 10px; font-size: .78rem; color: var(--c-muted); }
</style>
@endsection