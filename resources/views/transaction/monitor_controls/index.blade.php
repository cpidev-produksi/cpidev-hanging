@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Kontrol Monitor</h1>
    <a href="{{ route('monitor-controls.create') }}" class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">+ Buat</a>
</div>

<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left p-3">Report</th>
                <th class="text-left p-3">Lokasi</th>
                <th class="text-left p-3">Tanggal</th>
                <th class="text-left p-3">Shift</th>
                <th class="text-left p-3">Truk</th>
                <th class="text-left p-3">Ekspedisi</th>
                <th class="text-left p-3">Farm</th>
                <th class="text-left p-3">Status</th>
                <th class="text-right p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($items as $it)
            <tr class="border-t">
                <td class="p-3 font-mono">{{ $it->report_code }}</td>
                <td class="p-3">{{ $it->location }}</td>
                <td class="p-3">{{ $it->process_date->format('d/m/Y') }}</td>
                <td class="p-3 uppercase">{{ $it->shift }}</td>
                <td class="p-3">{{ $it->truck->no_truck }} ({{ $it->truck->plate_number }})</td>
                <td class="p-3">{{ $it->truck->expedition->name }}</td>
                <td class="p-3">{{ $it->farm->name }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $it->status==='draft' ? 'bg-slate-100' : ($it->status==='running' ? 'bg-amber-100' : 'bg-emerald-100') }}">
                        {{ $it->status }}
                    </span>
                </td>
                <td class="p-3 text-right space-x-2">
                    @if($it->status === 'draft')
                        <a href="{{ route('monitor-controls.edit',$it) }}" class="px-3 py-1 rounded border">Edit</a>
                        <form method="POST" action="{{ route('monitor-controls.start',$it) }}" class="inline">
                            @csrf
                            <button class="px-3 py-1 rounded bg-amber-600 text-white hover:bg-amber-700">Mulai Proses</button>
                        </form>
                        <form method="POST" action="{{ route('monitor-controls.destroy',$it) }}" class="inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 rounded bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
                        </form>
                    @else
                        @if($it->hangingForm)
                            <a href="{{ route('hanging-forms.show',$it->hangingForm) }}" class="px-3 py-1 rounded bg-slate-900 text-white">Buka Hanging</a>
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $items->links() }}</div>
@endsection