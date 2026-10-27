{{--
    Tabel hasil: satu baris = satu farm. Baris Total & Average = seluruh farm dalam tabel ini.
    Dipakai index, show, dan pdf.
    Wajib : $report (dengan relasi items)
    Opsi  : $rowActions (bool) tombol Ubah/Hapus per baris, $pdf (bool) tanpa CSS bawaan

    Kolom mengikuti DefeatReportItem::FIELDS. Head Pulling, Leg Cutting, dan Leg Pulling
    masing-masing terdiri dari OK, Not OK, dan % OK (OK / Jumlah Sampling).
--}}
@php
    use App\Models\DefeatReportItem as DI;

    $fields = DI::FIELDS;
    $rowActions = ($rowActions ?? false) && $report->canManageItems();
    $isPdf = $pdf ?? false;
    $sum = $report->summary();
    $lead = 4; // No, Nama Farm, Size, Waktu

    // header kelompok (colspan untuk kolom berurutan dengan group sama) + penanda kolom awal kelompok
    $groups = [];
    $starts = [];
    $prevGroup = null;
    foreach ($fields as $key => $f) {
        if ($f['group'] !== $prevGroup) {
            $groups[] = ['name' => $f['group'], 'span' => 1];
            $starts[$key] = true;
            $prevGroup = $f['group'];
        } else {
            $groups[count($groups) - 1]['span']++;
        }
    }

    // kelas CSS per kolom: awal kelompok (gs), OK (ok), Not OK (nok), persentase (pct)
    $colClass = function (string $key, array $f) use ($starts): string {
        $c = [];
        if (isset($starts[$key])) {
            $c[] = 'gs';
        }
        if (! empty($f['calc'])) {
            $c[] = 'pct';
        } elseif (str_ends_with($key, '_not_ok')) {
            $c[] = 'nok';
        } elseif (str_ends_with($key, '_ok')) {
            $c[] = 'ok';
        }

        return implode(' ', $c);
    };
@endphp

@unless($isPdf)
    @once
        <style>
            .tbl-scroll { overflow-x:auto; border:1px solid var(--card-border); border-radius:12px; background:#fff; }
            table.df-tbl { width:max-content; min-width:100%; border-collapse:separate; border-spacing:0; font-size:12.5px; font-variant-numeric:tabular-nums; }
            table.df-tbl th, table.df-tbl td { padding:9px 11px; border-bottom:1px solid #edf0f6; background:#fff; text-align:center; white-space:nowrap; }
            table.df-tbl tbody tr:last-child td { border-bottom:0; }
            table.df-tbl td.l, table.df-tbl th.l { text-align:left; }

            /* kepala tabel */
            table.df-tbl thead th { background:#f4f6fb; color:var(--text-muted); font-size:11.5px; font-weight:500; white-space:normal; line-height:1.3; vertical-align:middle; }
            table.df-tbl thead tr:first-child th { color:#0d3b6e; font-size:12px; font-weight:700; border-bottom:1px solid #dfe5f0; }
            table.df-tbl thead th.ok { color:#047857; font-weight:600; }
            table.df-tbl thead th.nok { color:#b91c1c; font-weight:600; }
            table.df-tbl thead th.pct { color:#0d3b6e; font-weight:700; }
            table.df-tbl th.gs, table.df-tbl td.gs { border-left:1px solid #dfe5f0; }

            /* kolom No. & Nama Farm tetap terlihat saat tabel digeser */
            table.df-tbl .c-no { position:sticky; left:0; z-index:2; box-sizing:border-box; width:44px; min-width:44px; max-width:44px; color:var(--text-muted); }
            table.df-tbl .c-farm { position:sticky; left:44px; z-index:2; font-weight:600; box-shadow:1px 0 0 #dfe5f0; }
            table.df-tbl thead .c-no, table.df-tbl thead .c-farm { z-index:3; }

            /* isi */
            table.df-tbl tbody tr:hover td { background:#f8faff; }
            table.df-tbl td.ok { color:#047857; }
            table.df-tbl td.nok { color:var(--text-muted); }
            table.df-tbl td.nok.has { color:#b91c1c; }
            table.df-tbl td.pct { color:#0d3b6e; font-weight:700; }

            /* total & average */
            table.df-tbl tfoot td { border-top:1px solid #dfe5f0; border-bottom:0; font-weight:700; }
            table.df-tbl tr.sum-tot td { background:#fff7ed; }
            table.df-tbl tr.sum-avg td { background:#ecfdf5; }
            table.df-tbl tr.sum-tot td.sum-lbl, table.df-tbl tr.sum-avg td.sum-lbl { text-align:left; font-size:12px; position:sticky; left:0; z-index:2; box-shadow:1px 0 0 #dfe5f0; }
            table.df-tbl tr.sum-tot td.ok, table.df-tbl tr.sum-avg td.ok { color:#047857; }
            table.df-tbl tr.sum-tot td.nok, table.df-tbl tr.sum-avg td.nok { color:#b91c1c; }
            table.df-tbl td.empty-row { padding:28px 10px; color:var(--text-muted); font-weight:400; }

            .row-act { display:inline-flex; gap:6px; align-items:center; }
            .row-act form { margin:0; }
        </style>
    @endonce
@endunless

<div class="tbl-scroll">
    <table class="df-tbl">
        <thead>
            <tr>
                <th rowspan="2" class="c-no">No.</th>
                <th rowspan="2" class="l c-farm">Nama Farm</th>
                <th rowspan="2">Size</th>
                <th rowspan="2">Waktu</th>
                @foreach($groups as $g)
                    <th colspan="{{ $g['span'] }}" class="gs">{{ $g['name'] }}</th>
                @endforeach
                @if($rowActions)<th rowspan="2" class="gs">Aksi</th>@endif
            </tr>
            <tr>
                @foreach($fields as $key => $f)
                    <th class="{{ $colClass($key, $f) }}">{{ $f['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($report->items as $it)
                <tr>
                    <td class="c-no">{{ $loop->iteration }}</td>
                    <td class="l c-farm">{{ $it->farm_name }}</td>
                    <td>{{ $it->size_label }}</td>
                    <td>{{ $it->time_range }}</td>
                    @foreach($fields as $key => $f)
                        @php $n = $it->numeric($key); @endphp
                        <td class="{{ $colClass($key, $f) }}@if(str_ends_with($key, '_not_ok') && $n > 0) has @endif">{{ $it->display($key) }}@if(! empty($f['calc']) && $n !== null)%@endif</td>
                    @endforeach
                    @if($rowActions)
                        <td class="gs">
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
                        Belum ada farm pada tabel ini.@if($rowActions) Gunakan tombol <strong>Tambah Farm</strong> untuk mulai mengisi.@endif
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($report->items->isNotEmpty())
            <tfoot>
                <tr class="sum-tot">
                    @if($isPdf)
                        <td class="sum-lbl" colspan="{{ $lead }}">Total</td>
                    @else
                        <td class="sum-lbl" colspan="2">Total</td>
                        <td colspan="{{ $lead - 2 }}"></td>
                    @endif
                    @foreach($fields as $key => $f)
                        <td class="{{ $colClass($key, $f) }}">{{ ($f['total'] ?? true) ? DI::format($key, $sum['total'][$key]) : '' }}</td>
                    @endforeach
                    @if($rowActions)<td class="gs"></td>@endif
                </tr>
                <tr class="sum-avg">
                    @if($isPdf)
                        <td class="sum-lbl" colspan="{{ $lead }}">Average</td>
                    @else
                        <td class="sum-lbl" colspan="2">Average</td>
                        <td colspan="{{ $lead - 2 }}"></td>
                    @endif
                    @foreach($fields as $key => $f)
                        <td class="{{ $colClass($key, $f) }}">{{ $f['avg'] ? DI::format($key, $sum['average'][$key], $f['avg_dec'] ?? null) : '' }}@if($f['avg'] && ! empty($f['calc']) && $sum['average'][$key] !== null)%@endif</td>
                    @endforeach
                    @if($rowActions)<td class="gs"></td>@endif
                </tr>
            </tfoot>
        @endif
    </table>
</div>
