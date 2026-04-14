@extends('layouts.app')

@section('content')
<div class="pl-wrap">

  <div class="pl-form-header">
    <a href="{{ route('planning-lb.index') }}" class="pl-back">&larr; Kembali</a>
    <h1 class="pl-title">Edit Planning Live Birds</h1>
    <p class="pl-sub">Perbarui data planning — {{ $item->location ?? '' }}, {{ isset($item->process_date) ? $item->process_date->format('d/m/Y') : '' }}</p>
  </div>

  <div class="pl-form-card">
    <form method="POST" action="{{ route('planning-lb.update', ['planning_lb' => $item->id]) }}" class="pl-form">
      @csrf 
      @method('PUT')
      
      @include('planning_lb.partials.form', ['item' => $item])
      
      <div class="pl-form-footer">
        <a href="{{ route('planning-lb.index') }}" class="btn-filter">Batal</a>
        <button type="submit" class="btn-new">Update Planning</button>
      </div>
    </form>
  </div>

</div>

@push('styles')
@include('planning_lb.partials.form-styles')
@endpush
@endsection