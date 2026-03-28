@extends('layouts.app')

@section('content')
<div class="form-page">
  <div class="page-header">
    <div>
      <h1 class="page-title">Edit Kontrol Monitor</h1>
      <p class="page-subtitle">Report: <code style="font-weight:900">{{ $monitor->report_code }}</code></p>
    </div>
    <a href="{{ route('monitor-controls.index') }}" class="btn-secondary">Kembali</a>
  </div>

  <form method="POST" action="{{ route('monitor-controls.update', $monitor) }}" class="form-card">
    @csrf
    @method('PUT')

    <div class="grid">
      <div class="form-group">
        <label class="form-label">Lokasi</label>
        <input class="form-input" value="{{ $monitor->location }}" disabled>
      </div>

      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input type="date" name="process_date" value="{{ old('process_date', $monitor->process_date->format('Y-m-d')) }}" class="form-input">
      </div>

      <div class="form-group">
        <label class="form-label">Shift</label>
        <select name="shift" class="form-input">
          <option value="pagi" @selected(old('shift', $monitor->shift)=='pagi')>Pagi</option>
          <option value="malam" @selected(old('shift', $monitor->shift)=='malam')>Malam</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Size Ayam</label>
        <select name="size" class="form-input">
          @foreach($sizes as $s)
            <option value="{{ $s }}" @selected((string)old('size', $monitor->size)===(string)$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Sopir</label>
        <input name="driver_name" value="{{ old('driver_name', $monitor->driver_name) }}" class="form-input">
      </div>

      <div class="form-group">
        <label class="form-label">Nominal Ekoran Farm</label>
        <input name="farm_fee_amount" type="number" step="0.01" value="{{ old('farm_fee_amount', $monitor->farm_fee_amount) }}" class="form-input">
      </div>

      <div class="form-group">
        <label class="form-label">Ekspedisi</label>
        <select id="expedition_id" name="expedition_id" class="form-input">
          <option value="">-- pilih --</option>
          @foreach($expeditions as $e)
            <option value="{{ $e->id }}" @selected(old('expedition_id', $monitor->expedition_id)==$e->id)>{{ $e->name }}</option>
          @endforeach
        </select>
        @error('expedition_id') <div class="err">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label class="form-label">No Polisi</label>
        <select id="plate_number_id" name="plate_number_id" class="form-input">
          <option value="">-- pilih ekspedisi dulu --</option>
        </select>
        @error('plate_number_id') <div class="err">{{ $message }}</div> @enderror
      </div>

      <div class="form-group span-2">
        <label class="form-label">Farm</label>
        <select name="farm_id" class="form-input">
          @foreach($farms as $f)
            <option value="{{ $f->id }}" @selected(old('farm_id', $monitor->farm_id)==$f->id)>{{ $f->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="actions">
      <button class="btn-primary">Update</button>
      <a href="{{ route('monitor-controls.index') }}" class="btn-secondary">Batal</a>
    </div>
  </form>
</div>

<script>
const expeditions = @json($expeditions->map(fn($e) => [
  'id' => $e->id,
  'name' => $e->name,
  'plates' => $e->plateNumbers->map(fn($p) => ['id' => $p->id, 'plate' => $p->plate_number])->values(),
])->values());

const expeditionSelect = document.getElementById('expedition_id');
const plateSelect = document.getElementById('plate_number_id');

function renderPlates(expeditionId, selectedPlateId = null) {
  plateSelect.innerHTML = '';
  const exp = expeditions.find(x => String(x.id) === String(expeditionId));

  if (!exp) {
    plateSelect.innerHTML = '<option value="">-- pilih ekspedisi dulu --</option>';
    return;
  }

  if (!exp.plates.length) {
    plateSelect.innerHTML = '<option value="">(Belum ada plate number)</option>';
    return;
  }

  plateSelect.appendChild(new Option('-- pilih --', ''));
  exp.plates.forEach(p => {
    const opt = new Option(p.plate, p.id);
    if (selectedPlateId && String(selectedPlateId) === String(p.id)) opt.selected = true;
    plateSelect.appendChild(opt);
  });
}

expeditionSelect.addEventListener('change', () => renderPlates(expeditionSelect.value));

document.addEventListener('DOMContentLoaded', () => {
  const expId = "{{ old('expedition_id', $monitor->expedition_id) }}";
  const plateId = "{{ old('plate_number_id', $monitor->plate_number_id) }}";
  if (expId) renderPlates(expId, plateId);
});
</script>

<style>
.form-page { max-width: 980px; margin: 0 auto; padding: 12px 6px; }
.page-header { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.page-title { font-size: 1.25rem; font-weight: 800; margin:0; }
.page-subtitle { margin:2px 0 0; font-size:.82rem; color:#6B7280; }

.form-card { background:#fff; border:1px solid #E8EAF0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.04); padding:18px; }
.grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; }
@media (max-width: 860px){ .grid { grid-template-columns: 1fr; } }
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-label { font-size:.8rem; font-weight:800; color:#1A1D2E; }
.form-input { border:1.5px solid #E8EAF0; border-radius:10px; padding:10px 12px; background:#FAFBFD; outline:none; }
.form-input:focus { border-color:#4F67FF; box-shadow:0 0 0 3px rgba(79,103,255,.12); background:#fff; }
.span-2 { grid-column: 1 / -1; }
.err { color:#F03E3E; font-size:.76rem; font-weight:700; }

.actions { display:flex; justify-content:flex-end; gap:10px; margin-top:14px; }
.btn-primary { display:inline-flex; align-items:center; justify-content:center; padding:10px 16px; border-radius:10px; background:#4F67FF; color:#fff; text-decoration:none; border:none; font-weight:900; cursor:pointer; }
.btn-secondary { display:inline-flex; align-items:center; justify-content:center; padding:10px 16px; border-radius:10px; background:#fff; color:#6B7280; text-decoration:none; border:1.5px solid #E8EAF0; font-weight:900; }
</style>
@endsection