@extends('layouts.app')

@section('content')
<div class="flex items-start justify-between gap-4 mb-4">
    <div>
        <h1 class="text-xl font-bold">Form Hanging Ayam</h1>
        <div class="text-sm text-slate-500 font-mono">{{ $form->monitorControl->report_code }}</div>
    </div>

    <div class="bg-white border rounded-xl p-4 text-sm w-full max-w-xl">
        <div class="grid grid-cols-3 gap-2">
            <div>
                <div class="text-slate-500 text-xs">Lokasi</div>
                <div class="font-semibold">{{ $form->monitorControl->location }}</div>
            </div>
            <div>
                <div class="text-slate-500 text-xs">Tanggal</div>
                <div class="font-semibold">{{ $form->monitorControl->process_date->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="text-slate-500 text-xs">Shift</div>
                <div class="font-semibold uppercase">{{ $form->monitorControl->shift }}</div>
            </div>
            <div class="col-span-3 h-px bg-slate-200 my-1"></div>

            <div>
                <div class="text-slate-500 text-xs">No Truk</div>
                <div class="font-semibold">{{ $form->monitorControl->truck->no_truck }}</div>
            </div>
            <div>
                <div class="text-slate-500 text-xs">No Polisi</div>
                <div class="font-semibold">{{ $form->monitorControl->truck->plate_number }}</div>
            </div>
            <div>
                <div class="text-slate-500 text-xs">Ekspedisi</div>
                <div class="font-semibold">{{ $form->monitorControl->truck->expedition->name }}</div>
            </div>

            <div>
                <div class="text-slate-500 text-xs">Sopir</div>
                <div class="font-semibold">{{ $form->monitorControl->driver_name }}</div>
            </div>
            <div>
                <div class="text-slate-500 text-xs">Farm</div>
                <div class="font-semibold">{{ $form->monitorControl->farm->name }}</div>
            </div>
            <div>
                <div class="text-slate-500 text-xs">Size</div>
                <div class="font-semibold">{{ $form->monitorControl->size }}</div>
            </div>
        </div>
    </div>
</div>

@php
  $setCount = (int) $form->monitorControl->set_count;
@endphp

<div class="bg-white border rounded-xl overflow-auto">
    <table class="min-w-[1200px] w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="p-2 text-left w-12" rowspan="2">No</th>
                <th class="p-2 text-left w-24" rowspan="2">Shackle</th>
                <th class="p-2 text-left w-28" rowspan="2">Aturan</th>
                @for($s=1;$s<=$setCount;$s++)
                    <th class="p-2 text-center" colspan="2">SET {{ $s }}</th>
                @endfor
            </tr>
            <tr>
                @for($s=1;$s<=$setCount;$s++)
                    <th class="p-2 text-center">KOSONG</th>
                    <th class="p-2 text-center">AYAM</th>
                @endfor
            </tr>
        </thead>
        <tbody>
        @foreach($form->lines as $line)
            <tr class="border-t">
                <td class="p-2">{{ $line->line_no }}</td>
                <td class="p-2 font-semibold">{{ $line->shackle_label }}</td>
                <td class="p-2">{{ $line->rule_min }}-{{ $line->rule_max }}</td>

                @for($s=1;$s<=$setCount;$s++)
                    @php
                      $cell = $line->sets->firstWhere('set_no',$s);
                      $empty = (int)($cell?->empty_count ?? 0);
                      $ayam = 50 - $empty;
                    @endphp
                    <td class="p-2">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button"
                              class="w-8 h-8 rounded bg-rose-100 text-rose-700"
                              onclick="updateCell({{ $cell->id }}, {{ max(0, $empty-1) }})">-</button>

                            <input id="empty-{{ $cell->id }}" value="{{ $empty }}"
                                   class="w-14 text-center border rounded px-2 py-1"
                                   onchange="updateCell({{ $cell->id }}, this.value)"/>

                            <button type="button"
                              class="w-8 h-8 rounded bg-emerald-100 text-emerald-700"
                              onclick="updateCell({{ $cell->id }}, {{ min(50, $empty+1) }})">+</button>
                        </div>
                    </td>
                    <td class="p-2 text-center">
                        <span id="ayam-{{ $cell->id }}" class="font-semibold">{{ $ayam }}</span>
                    </td>
                @endfor
            </tr>
        @endforeach
        </tbody>
        <tfoot class="bg-slate-50 border-t">
            <tr>
                <td class="p-2 font-semibold" colspan="{{ 3 + ($setCount*2) }}">Ringkasan</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="mt-4 bg-white border rounded-xl p-4 text-sm space-y-2">
    <div>Total Shackle Kosong: <span id="total-kosong" class="font-bold">{{ $totalKosong }}</span> Pcs</div>
    <div>Jumlah Ayam Mati: <span class="font-bold">{{ $ayamMati }}</span> Ekor</div>
    <div>Jumlah Ayam Diterima: <span id="total-ayam" class="font-bold">{{ $totalAyam }}</span> Ekor</div>
</div>

<script>
async function updateCell(id, emptyCount) {
  emptyCount = parseInt(emptyCount, 10);
  if (Number.isNaN(emptyCount)) emptyCount = 0;
  if (emptyCount < 0) emptyCount = 0;
  if (emptyCount > 50) emptyCount = 50;

  const res = await fetch(`{{ url('/hanging-cells') }}/${id}`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ empty_count: emptyCount })
  });

  const json = await res.json();

  // update cell
  document.getElementById(`empty-${id}`).value = json.empty_count;
  document.getElementById(`ayam-${id}`).textContent = json.ayam;

  // refresh totals by reloading (simple, aman) - tahap awal:
  // Anda bisa optimasi nanti dengan hitung totals di client
  window.clearTimeout(window.__refreshTotalsTimer);
  window.__refreshTotalsTimer = setTimeout(() => window.location.reload(), 300);
}
</script>
@endsection