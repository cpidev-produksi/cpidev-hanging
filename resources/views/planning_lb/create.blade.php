@extends('layouts.app')

@section('content')
<div class="pl-wrap">

  <div class="pl-form-header">
    <a href="{{ route('planning-lb.index') }}" class="pl-back">&larr; Kembali</a>
    <h1 class="pl-title">Buat Planning Live Birds</h1>
    <p class="pl-sub">Isi data planning pemotongan baru</p>
  </div>

  <div class="pl-form-card">
    <form method="POST" action="{{ route('planning-lb.store') }}" class="pl-form">
      @csrf
      @include('planning_lb.partials.form', ['item' => null])
      <div class="pl-form-footer">
        <a href="{{ route('planning-lb.index') }}" class="btn-filter">Batal</a>
        <button type="submit" class="btn-new">Simpan Planning</button>
      </div>
    </form>
  </div>

</div>

@push('styles')
@include('planning_lb.partials.form-styles')
@endpush
@endsection