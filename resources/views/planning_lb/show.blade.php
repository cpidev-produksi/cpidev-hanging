@extends('layouts.app')

@section('content')
<div class="pl-wrap">

  <div class="pl-form-header">
    <a href="{{ route('planning-lb.index', ['date' => $item->process_date?->format('Y-m-d')]) }}" class="pl-back">&larr; Kembali</a>
    <h1 class="pl-title">Detail Planning LB</h1>
    <p class="pl-sub">{{ $item->location }} &bull; {{ $item->process_date?->format('d/m/Y') }}</p>
  </div>

  <div class="pl-form-card" style="max-width: 480px;">
    <div class="pl-detail-row">
      <span class="pl-detail-lbl">Lokasi</span>
      <span class="pl-badge pl-badge-{{ strtolower($item->location) }}">{{ $item->location }}</span>
    </div>
    <div class="pl-detail-row">
      <span class="pl-detail-lbl">Tanggal Operasional</span>
      <span class="pl-detail-val">{{ $item->process_date?->format('d/m/Y') }}</span>
    </div>
    <div class="pl-detail-row">
      <span class="pl-detail-lbl">Total Plan Ekor</span>
      <span class="pl-detail-val">{{ number_format($item->total_plan_chicken) }} ekor</span>
    </div>
    <div class="pl-detail-row">
      <span class="pl-detail-lbl">Total Plan Truk</span>
      <span class="pl-detail-val">{{ number_format($item->total_plan_truck) }} truk</span>
    </div>

    <div class="pl-form-footer" style="margin-top: 1.25rem;">
      <a href="{{ route('planning-lb.index', ['date' => $item->process_date?->format('Y-m-d')]) }}" class="btn-filter">Kembali</a>
      <a href="{{ route('planning-lb.edit', $item) }}" class="btn-new">Edit</a>
    </div>
  </div>

</div>

@push('styles')
<style>
.pl-wrap { padding: 1.5rem; max-width: 1100px; margin: 0 auto; }
.pl-form-header { margin-bottom: 1.25rem; }
.pl-back { font-size: 13px; color: #6b7280; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-bottom: .5rem; }
.pl-back:hover { color: #374151; }
.pl-title { font-size: 22px; font-weight: 500; color: #1a1a1a; }
.pl-sub   { font-size: 14px; color: #6b7280; margin-top: 4px; }

.pl-form-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
}

.pl-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #f3f4f6;
  font-size: 14px;
}
.pl-detail-row:last-of-type { border-bottom: none; }
.pl-detail-lbl { color: #6b7280; }
.pl-detail-val { font-weight: 500; color: #1a1a1a; }

.pl-badge { display: inline-flex; align-items: center; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.pl-badge-sh01 { background: #DBEAFE; color: #1E40AF; }
.pl-badge-sh02 { background: #D1FAE5; color: #065F46; }

.pl-form-footer { display: flex; gap: 8px; justify-content: flex-end; }
.btn-filter { padding: 7px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; cursor: pointer; background: #f9fafb; color: #374151; text-decoration: none; display: inline-flex; align-items: center; }
.btn-new { padding: 7px 14px; border-radius: 8px; font-size: 13px; cursor: pointer; border: none; background: #1D9E75; color: #E1F5EE; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; }
.btn-new:hover { background: #168a64; }
</style>
@endpush
@endsection