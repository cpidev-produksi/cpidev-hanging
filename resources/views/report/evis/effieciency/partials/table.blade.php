{{--
    Tabel hasil Efficiency Evisceration: satu baris = satu farm. Baris Total & Average = seluruh farm dalam laporan.
    Dipakai show, index (mode detail), dan pdf.
    Wajib : $report (dengan relasi items)
    Opsi  : $actions (bool) tombol Ubah/Hapus per baris, $pdf (bool) tanpa CSS bawaan
--}}
@php
    use App\Models\EviscerationEfficiencyItem as EI;

    $actions = ($actions ?? false) && $report->canManageItems();
    $isPdf = $pdf ?? false;
    $summary = EI::summarize($report->items);
    $number = fn ($value, $decimals = 0) => $value === null ? '–' : number_format($value, $decimals, ',', '');
    $lead = 6; // No, Nama Farm, Size, Waktu, Speed Line Evis, Jumlah Sampling
    $columns = $lead + 8 + ($actions ? 1 : 0);
@endphp

@unless($isPdf)
    @once
        <style>
            .tbl-scroll { overflow-x:auto; border:1px solid var(--card-border); border-radius:12px; background:#fff; }
            table.ev-data-table { width:max-content; min-width:100%; border-collapse:separate; border-spacing:0; font-size:12.5px; font-variant-numeric:tabular-nums; }
            table.ev-data-table th, table.ev-data-table td { padding:9px 11px; border-bottom:1px solid #edf0f6; background:#fff; text-align:center; white-space:nowrap; }
            table.ev-data-table tbody tr:last-child td { border-bottom:0; }
            table.ev-data-table td.l, table.ev-data-table th.l { text-align:left; }

            /* kepala tabel */
            table.ev-data-table thead th { background:#f4f6fb; color:var(--text-muted); font-size:11.5px; font-weight:500; white-space:normal; line-height:1.3; vertical-align:middle; }
            table.ev-data-table thead tr:first-child th { color:#0d3b6e; font-size:12px; font-weight:700; border-bottom:1px solid #dfe5f0; }
            table.ev-data-table thead th.pct { color:#0d3b6e; font-weight:700; }
            table.ev-data-table th.gs, table.ev-data-table td.gs { border-left:1px solid #dfe5f0; }

            /* kolom No. & Nama Farm tetap terlihat saat tabel digeser */
            table.ev-data-table .c-no { position:sticky; left:0; z-index:2; box-sizing:border-box; width:44px; min-width:44px; max-width:44px; color:var(--text-muted); }
            table.ev-data-table .c-farm { position:sticky; left:44px; z-index:2; font-weight:600; box-shadow:1px 0 0 #dfe5f0; }
            table.ev-data-table thead .c-no, table.ev-data-table thead .c-farm { z-index:3; }

            /* isi */
            table.ev-data-table tbody tr:hover td { background:#f8faff; }
            table.ev-data-table td.pct { color:#0d3b6e; font-weight:700; }
            table.ev-data-table td.ok { color:#047857; }
            table.ev-data-table td.nok { color:#b91c1c; }

            /* total & average */
            table.ev-data-table tfoot td { border-top:1px solid #dfe5f0; border-bottom:0; font-weight:700; }
            table.ev-data-table tr.total td { background:#fff7ed; }
            table.ev-data-table tr.average td { background:#ecfdf5; }
            table.ev-data-table tfoot td.sum-lbl { text-align:left; font-size:12px; position:sticky; left:0; z-index:2; box-shadow:1px 0 0 #dfe5f0; }
            table.ev-data-table td.empty-row { padding:28px 10px; color:var(--text-muted); font-weight:400; }

            .row-act { display:inline-flex; gap:6px; align-items:center; }
            .row-act form { margin:0; }
        </style>
    @endonce
@endunless

<div class="tbl-scroll">
    <table class="ev-data-table">
        <thead>
            <tr>
                <th rowspan="2" class="c-no">No.</th>
                <th rowspan="2" class="l c-farm">Nama Farm</th>
                <th rowspan="2">Size</th>
                <th rowspan="2">Waktu</th>
                <th rowspan="2" class="gs">Speed Line Evis (BPH)</th>
                <th rowspan="2">Jumlah Sampling</th>
                <th colspan="2" class="gs">Vent Opening Cutter</th>
                <th colspan="2" class="gs">Nouva</th>
                <th rowspan="2" class="gs">Sprayer</th>
                <th rowspan="2">Temuan Grit</th>
                <th colspan="2" class="gs">Debit Air</th>
                @if($actions)<th rowspan="2" class="gs">Aksi</th>@endif
            </tr>
            <tr>
                <th class="gs">Vent Tidak Sobek</th><th class="pct">Persentase</th>
                <th class="gs">Jeroan Tidak Terambil</th><th class="pct">Persentase</th>
                <th class="gs">Awal</th><th>Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report->items as $item)
                <tr>
                    <td class="c-no">{{ $loop->iteration }}</td>
                    <td class="l c-farm">{{ $item->farm_name }}</td>
                    <td>{{ $item->size_label }}</td>
                    <td>{{ $item->time_range }}</td>
                    <td class="gs">{{ $item->speed_evis ?? '–' }}</td>
                    <td>{{ $item->sampling_count ?? '–' }}</td>
                    <td class="gs">{{ $item->vent_not_torn ?? '–' }}</td>
                    <td class="pct">{{ $item->vent_percent === null ? '–' : $number($item->vent_percent, 2) . '%' }}</td>
                    <td class="gs">{{ $item->viscera_not_removed ?? '–' }}</td>
                    <td class="pct">{{ $item->viscera_percent === null ? '–' : $number($item->viscera_percent, 2) . '%' }}</td>
                    <td class="gs {{ $item->sprayer_status === 'operating' ? 'ok' : 'nok' }}">{{ $item->sprayer_status === 'operating' ? 'Beroperasi' : 'Tidak Beroperasi' }}</td>
                    <td>{{ $item->grit_findings ?? '–' }}</td>
                    <td class="gs">{{ $number($item->water_flow_start, 2) }}</td>
                    <td>{{ $number($item->water_flow_end, 2) }}</td>
                    @if($actions)
                        <td class="gs">
                            <div class="row-act">
                                <a class="df-btn sm" href="{{ route('evisceration.efficiency.items.edit', [$report, $item]) }}">Ubah</a>
                                <form method="POST" action="{{ route('evisceration.efficiency.items.destroy', [$report, $item]) }}"
                                      onsubmit="return confirm('Hapus farm {{ e($item->farm_name) }} dari laporan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="df-btn sm danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td class="empty-row" colspan="{{ $columns }}">
                        Belum ada farm pada laporan ini.@if($actions) Gunakan tombol <strong>Tambah Farm</strong> untuk mulai mengisi.@endif
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($report->items->isNotEmpty())
            <tfoot>
                @foreach(['total' => 'Total', 'average' => 'Average'] as $type => $label)
                    @php $dec = $type === 'average' ? 1 : 0; @endphp
                    <tr class="{{ $type }}">
                        @if($isPdf)
                            <td class="sum-lbl" colspan="{{ $lead }}">{{ $label }}</td>
                        @else
                            <td class="sum-lbl" colspan="2">{{ $label }}</td>
                            <td colspan="{{ $lead - 2 }}"></td>
                        @endif
                        <td class="gs">{{ $number($summary['vent_not_torn'][$type], $dec) }}</td>
                        <td></td>
                        <td class="gs">{{ $number($summary['viscera_not_removed'][$type], $dec) }}</td>
                        <td></td>
                        <td class="gs"></td>
                        <td>{{ $number($summary['grit_findings'][$type], $dec) }}</td>
                        <td class="gs" colspan="2"></td>
                        @if($actions)<td class="gs"></td>@endif
                    </tr>
                @endforeach
            </tfoot>
        @endif
    </table>
</div>