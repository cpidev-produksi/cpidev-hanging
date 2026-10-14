@extends('layouts.app')

@section('content')
<div class="pl-wrap">

  <div class="pl-form-header">
    <a href="{{ route('planning-lb.index') }}" class="pl-back">&larr; Kembali</a>
    <h1 class="pl-title">Upload Planning Live Birds</h1>
    <p class="pl-sub">Upload file Excel jadwal pemotongan untuk mengisi planning secara otomatis</p>
  </div>

  <div class="pl-form-card">
    <form method="POST" action="{{ route('planning-lb.store') }}" enctype="multipart/form-data" class="pl-form">
      @csrf

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
            <option value="SH01" {{ old('location') === 'SH01' ? 'selected' : '' }}>SH01</option>
            <option value="SH02" {{ old('location') === 'SH02' ? 'selected' : '' }}>SH02</option>
          </select>
          @error('location')
            <span class="pl-error-msg">{{ $message }}</span>
          @enderror
        </div>

        <div class="pl-field">
          <label class="pl-label" for="file">File Excel</label>
          <input
            type="file"
            name="file"
            id="file"
            accept=".xls,.xlsx"
            class="pl-input @error('file') is-error @enderror"
          >
          <small class="pl-help">Gunakan sheet aktif. Tanggal dibaca dari baris Hari/Tanggal, ekor dari kolom G.</small>
          @error('file')
            <span class="pl-error-msg">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="pl-form-footer">
        <a href="{{ route('planning-lb.index') }}" class="btn-filter">Batal</a>
        <button type="submit" class="btn-new">Upload Planning</button>
      </div>
    </form>
  </div>

</div>

@push('styles')
@include('planning_lb.partials.form-styles')
@endpush
@endsection