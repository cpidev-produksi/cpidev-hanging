@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-slate-500 text-sm">SlaughterHouse Department</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl p-4 border">
        <div class="text-sm text-slate-500">Master Data</div>
        <div class="text-2xl font-bold">Siap</div>
    </div>
    <div class="bg-white rounded-xl p-4 border">
        <div class="text-sm text-slate-500">Kontrol Monitor</div>
        <div class="text-2xl font-bold">Siap</div>
    </div>
    <div class="bg-white rounded-xl p-4 border">
        <div class="text-sm text-slate-500">Form Hanging</div>
        <div class="text-2xl font-bold">Siap</div>
    </div>
    <div class="bg-white rounded-xl p-4 border">
        <div class="text-sm text-slate-500">Live Monitor</div>
        <div class="text-2xl font-bold">Siap</div>
    </div>
</div>
@endsection