@extends('layouts.app')

@section('content')
<div class="panel">
  <div class="panel-header">
    <div class="panel-title">Trash - File Inventory</div>
    <div>
      <a href="{{ route('inventory.index') }}" class="topnav-link">Back</a>
    </div>
  </div>
  <div class="panel-body">
    <div style="color:#6b7896;">TODO: Trash list + restore</div>
  </div>
</div>
@endsection