@php
// Pastikan variabel $item terdefinisi
if(!isset($item)) {
    $item = null;
}
@endphp

<div class="pl-grid">

  @if($errors->any())
    <div class="pl-errors" style="grid-column: 1 / -1;">
      <ul>
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="pl-field">
    <label class="pl-label" for="location">Lokasi</label>
    <select name="location" id="location" class="pl-select @error('location') is-error @enderror">
      <option value="">-- Pilih Lokasi --</option>
      <option value="SH01" {{ old('location', $item && $item->location ? $item->location : '') == 'SH01' ? 'selected' : '' }}>SH01</option>
      <option value="SH02" {{ old('location', $item && $item->location ? $item->location : '') == 'SH02' ? 'selected' : '' }}>SH02</option>
    </select>
    @error('location')
      <span class="pl-error-msg">{{ $message }}</span>
    @enderror
  </div>

  <div class="pl-field">
    <label class="pl-label" for="process_date">Tanggal Operasional</label>
    <input
      type="date"
      name="process_date"
      id="process_date"
      class="pl-input @error('process_date') is-error @enderror"
      value="{{ old('process_date', $item && $item->process_date ? $item->process_date->format('Y-m-d') : '') }}"
    >
    @error('process_date')
      <span class="pl-error-msg">{{ $message }}</span>
    @enderror
  </div>

  <div class="pl-field">
    <label class="pl-label" for="total_plan_chicken">Total Plan Ekor</label>
    <div class="pl-input-group">
      <input
        type="number"
        name="total_plan_chicken"
        id="total_plan_chicken"
        class="pl-input @error('total_plan_chicken') is-error @enderror"
        value="{{ old('total_plan_chicken', $item ? $item->total_plan_chicken : 0) }}"
        min="0"
        placeholder="0"
      >
      <span class="pl-input-suffix">ekor</span>
    </div>
    @error('total_plan_chicken')
      <span class="pl-error-msg">{{ $message }}</span>
    @enderror
  </div>

  <div class="pl-field">
    <label class="pl-label" for="total_plan_truck">Total Plan Truk</label>
    <div class="pl-input-group">
      <input
        type="number"
        name="total_plan_truck"
        id="total_plan_truck"
        class="pl-input @error('total_plan_truck') is-error @enderror"
        value="{{ old('total_plan_truck', $item ? $item->total_plan_truck : 0) }}"
        min="0"
        placeholder="0"
      >
      <span class="pl-input-suffix">truk</span>
    </div>
    @error('total_plan_truck')
      <span class="pl-error-msg">{{ $message }}</span>
    @enderror
  </div>

</div>