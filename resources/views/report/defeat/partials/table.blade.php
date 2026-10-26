{{--
    Tabel hasil: satu baris = satu farm. Baris Total & Average = seluruh farm dalam tabel ini.
    Dipakai index, show, dan pdf.
    Wajib : $report (dengan relasi items)
    Opsi  : $rowActions (bool) tombol Ubah/Hapus per baris, $pdf (bool) tanpa CSS bawaan
--}}
@php
    use App\Models\DefeatReportItem as DI;

    $fields = DI::FIELDS;
    $rowActions = ($rowActions ?? false) && $report->canManageItems();
    $isPdf = $pdf ?? false;
    $sum = $report->summary();

    // header kelompok (colspan untuk kolom berurutan dengan group sama)
    $groups = [];
    foreach ($fields as $f) {
        if ($groups && end($groups)['name'] === $f['group']) {
            $groups[count($groups) - 1]['span']++;
        } else {
            $groups[] = ['name' => $f['group'], 'span' => 1];
        }
    }
    $lead = 5; // No, Nama Farm, Size, Waktu, Jumlah Sampling
    $hasSamplingCounts = $report->items->every(fn ($item) => $item->sampling_count !== null);
    $samplingTotal = $hasSamplingCounts ? $report->items->sum('sampling_count') : null;
    $samplingAverage = $hasSamplingCounts ? $report->items->avg('sampling_count') : null;
    $percentageFields = ['head_pulling', 'leg_cutting', 'leg_pulling'];
@endphp

@unless($isPdf)
    @once
        <style>
            .tbl-scroll { overflow-x:auto; }
            table.df-tbl { width:max-content; min-width:100%; border-collapse:collapse; font-size:12.5px; }
            table.df-tbl th, table.df-tbl td { padding:7px 9px; border:1px solid var(--card-border); white-space:nowrap; text-align:center; }
            table.df-tbl thead th { background:#f5f7fc; font-size:10.5px; text-transform:uppercase; letter-spacing:.04em; color:#0d3b6e; }
            table.df-tbl td.l, table.df-tbl th.l { text-align:left; }
            table.df-tbl tbody tr:hover td { background:#fafbff; }
            table.df-tbl tr.sum-tot td { background:#fff7ed; font-weight:700; }
            table.df-tbl tr.sum-avg td { background:#ecfdf5; font-weight:700; }
            table.df-tbl td.sum-lbl { text-align:right; text-transform:uppercase; letter-spacing:.04em; font-size:11px; }
            table.df-tbl td.empty-row { padding:22px 10px; color:var(--text-muted); }
            .row-act { display:inline-flex; gap:6px; align-items:center; }
            .row-act form { margin:0; }
        </style>
    @endonce
@endunless

<div class="tbl-scroll">
    <table class="df-tbl">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2" class="l">Nama Farm</th>
                <th rowspan="2">Size</th>
                <th rowspan="2">Waktu</th>
                <th rowspan="2">Jumlah Sampling</th>
                @foreach($groups as $g)
                    <th colspan="{{ $g['span'] }}">{{ $g['name'] }}</th>
                @endforeach
                @if($rowActions)<th rowspan="2">Aksi</th>@endif
            </tr>
            <tr>
                @foreach($fields as $f)
                    <th>{{ $f['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($report->items as $it)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="l" style="font-weight:600;">{{ $it->farm_name }}</td>
                    <td>{{ $it->size_label }}</td>
                    <td>{{ $it->time_range }}</td>
                    <td>{{ $it->sampling_count !== null ? number_format($it->sampling_count, 0, ',', '.') : '–' }}</td>
                    @foreach($fields as $key => $f)
                        <td>{{ $it->display($key) }}@if(in_array($key, $percentageFields, true) && $it->numeric($key) !== null)%@endif</td>
                    @endforeach
                    @if($rowActions)
                        <td>
                            <div class="row-act">
                                <a class="df-btn sm" href="{{ route('defeathering.efficiency.items.edit', [$report, $it]) }}">Ubah</a>
                                <form method="POST" action="{{ route('defeathering.efficiency.items.destroy', [$report, $it]) }}"
                                      onsubmit="return confirm('Hapus farm {{ e($it->farm_name) }} dari tabel ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="df-btn sm danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td class="empty-row" colspan="{{ $lead + count($fields) + ($rowActions ? 1 : 0) }}">
                        Belum ada farm pada tabel ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($report->items->isNotEmpty())
            <tfoot>
                <tr class="sum-tot">
                    <td class="sum-lbl" colspan="{{ $lead }}">Total</td>
                    <td>{{ $samplingTotal !== null ? number_format($samplingTotal, 0, ',', '.') : '–' }}</td>
                    @foreach($fields as $key => $f)
                        <td>{{ ($f['total'] ?? true) ? DI::format($key, $sum['total'][$key]) : '' }}@if(($f['total'] ?? true) && in_array($key, $percentageFields, true) && $sum['total'][$key] !== null)%@endif</td>
                    @endforeach
                    @if($rowActions)<td></td>@endif
                </tr>
                <tr class="sum-avg">
                    <td class="sum-lbl" colspan="{{ $lead }}">Average</td>
                    <td>{{ $samplingAverage !== null ? number_format($samplingAverage, 1, ',', '.') : '–' }}</td>
                    @foreach($fields as $key => $f)
                        <td>{{ $f['avg'] ? DI::format($key, $sum['average'][$key]) : '' }}@if($f['avg'] && in_array($key, $percentageFields, true) && $sum['average'][$key] !== null)%@endif</td>
                    @endforeach
                    @if($rowActions)<td></td>@endif
                </tr>
            </tfoot>
        @endif
    </table>
</div>