@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">Buat Kontrol Monitor</h1>

<form method="POST" action="{{ route('monitor-controls.store') }}" class="bg-white border rounded-xl p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf

    <div>
        <label class="block text-sm font-medium mb-1">Lokasi</label>
        <select name="location" class="w-full border rounded px-3 py-2">
            <option value="SH01">SH01</option>
            <option value="SH02">SH02</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Tanggal</label>
        <input type="date" name="process_date" value="{{ old('process_date', date('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Shift</label>
        <select name="shift" class="w-full border rounded px-3 py-2">
            <option value="pagi">Pagi</option>
            <option value="malam">Malam</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Size Ayam</label>
        <select name="size" class="w-full border rounded px-3 py-2">
            @foreach($sizes as $s)
                <option value="{{ $s }}">{{ $s }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Sopir</label>
        <input name="driver_name" value="{{ old('driver_name') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nominal Ekoran Farm</label>
        <input name="farm_fee_amount" type="number" step="0.01" value="{{ old('farm_fee_amount', 0) }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Truk (No / Polisi / Ekspedisi)</label>
        <select name="truck_id" class="w-full border rounded px-3 py-2">
            @foreach($trucks as $t)
                <option value="{{ $t->id }}">
                    {{ $t->no_truck }} - {{ $t->plate_number }} ({{ $t->expedition->name }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Farm</label>
        <select name="farm_id" class="w-full border rounded px-3 py-2">
            @foreach($farms as $f)
                <option value="{{ $f->id }}">{{ $f->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2 flex gap-2 pt-2">
        <button class="px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-800">Simpan (Draft)</button>
        <a href="{{ route('monitor-controls.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
</form>
@endsection