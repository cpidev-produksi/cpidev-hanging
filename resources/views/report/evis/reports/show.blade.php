@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Detail Report Evis — {{ $report->report_date->format('d M Y') }}</div>
        <div style="display: flex; gap: 8px;">
            @if($report->isDraft())
                <a href="{{ route('report-evis.edit', $report) }}" class="topnav-link">Edit</a>
            @endif
            <a href="{{ route('report-evis.pdf', $report) }}" class="topnav-link" style="background: var(--accent); color: white; border-color: var(--accent);">Download PDF</a>
            <a href="{{ route('report-evis.index') }}" class="topnav-link">Kembali</a>
        </div>
    </div>

    <div class="panel-body">
        @php
            $freshItems = $report->items->where('category', 'fresh')->values();
            $frozenItems = $report->items->where('category', 'frozen')->values();

            $fmt = fn($v) => number_format((float)($v ?? 0), 2);
        @endphp

        {{-- Info Card --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px;">
            <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Dibuat Oleh</div>
                <div style="font-weight: 600; font-size: 13px;">{{ $report->createdBy->name }}</div>
                <div style="font-size: 12px; color: var(--text-muted);">{{ $report->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Status</div>
                @if($report->isDraft())
                    <span style="display: inline-flex; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #f5f7fc; color: var(--text-muted); border: 1px solid var(--card-border);">Draft</span>
                @else
                    <span style="display: inline-flex; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; background: rgba(16,185,129,0.08); color: var(--success); border: 1px solid rgba(16,185,129,0.2);">✓ Approved</span>
                @endif
            </div>

            <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Lokasi / Shift</div>
                <div style="font-weight: 700; font-size: 13px;">
                    {{ $report->location ?? '—' }} / {{ $report->shift ?? '—' }}
                </div>
                <div style="font-size: 12px; color: var(--text-muted);">Tanggal: {{ $report->report_date->format('d/m/Y') }}</div>
            </div>

            <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Jumlah Truk</div>
                <div style="font-weight: 700; font-size: 18px; font-variant-numeric: tabular-nums;">{{ (int)($report->truck_count ?? 0) }}</div>
            </div>

            <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Ayam Diterima (Ekor)</div>
                <div style="font-weight: 700; font-size: 18px; font-variant-numeric: tabular-nums;">{{ (int)($report->received_chicken ?? 0) }}</div>
            </div>

            <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Yield (%)</div>
                <div style="font-weight: 700; font-size: 18px; font-variant-numeric: tabular-nums;">
                    {{ $report->yield_percent !== null ? number_format((float)$report->yield_percent, 2) : '—' }}
                </div>
            </div>

            @if($report->isApproved())
                <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                    <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Disetujui Oleh</div>
                    <div style="font-weight: 600; font-size: 13px;">{{ $report->approvedBy->name }}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">{{ $report->approved_at->format('d/m/Y H:i') }}</div>
                </div>
            @endif
        </div>

        {{-- Totals Summary --}}
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 18px;">
            <div style="padding: 14px 16px; background: #f9fafc; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 6px;">TOTAL FRESH</div>
                <div style="display:flex; justify-content:space-between; gap: 12px;">
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted);">Bag</div>
                        <div style="font-weight: 800; font-variant-numeric: tabular-nums;">{{ $fmt($report->fresh_total_bag) }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted);">Kg</div>
                        <div style="font-weight: 800; font-variant-numeric: tabular-nums;">{{ $fmt($report->fresh_total_kg) }}</div>
                    </div>
                </div>
            </div>

            <div style="padding: 14px 16px; background: #f9fafc; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 6px;">TOTAL FROZEN</div>
                <div style="display:flex; justify-content:space-between; gap: 12px;">
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted);">Bag</div>
                        <div style="font-weight: 800; font-variant-numeric: tabular-nums;">{{ $fmt($report->frozen_total_bag) }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted);">Kg</div>
                        <div style="font-weight: 800; font-variant-numeric: tabular-nums;">{{ $fmt($report->frozen_total_kg) }}</div>
                    </div>
                </div>
            </div>

            <div style="padding: 14px 16px; background: #f5f7fc; border: 1px solid var(--card-border); border-radius: 10px;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 6px;">GRAND TOTAL</div>
                <div style="display:flex; justify-content:space-between; gap: 12px;">
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted);">Bag</div>
                        <div style="font-weight: 900; font-size: 16px; font-variant-numeric: tabular-nums;">{{ $fmt($report->total_bag) }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted);">Kg</div>
                        <div style="font-weight: 900; font-size: 16px; font-variant-numeric: tabular-nums;">{{ $fmt($report->total_kg) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== TABLE: FRESH =================== --}}
        <div style="margin-bottom: 10px; font-weight: 800; letter-spacing: .02em;">TABEL FRESH</div>
        <div style="overflow-x: auto; margin-bottom: 22px;">
            <table class="table" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">Material Number</th>
                        <th style="min-width: 220px;">Nama Produk</th>

                        @for($i = 1; $i <= 10; $i++)
                            <th colspan="2" style="text-align: center; min-width: 120px;">{{ $i }}</th>
                        @endfor

                        <th style="text-align: right; min-width: 110px;">Total Bag</th>
                        <th style="text-align: right; min-width: 110px;">Total Kg</th>
                    </tr>
                    <tr style="background: #f5f7fc;">
                        <th></th>
                        <th></th>
                        @for($i = 1; $i <= 10; $i++)
                            <th style="text-align: right; padding: 6px; font-size: 11px;">Bag</th>
                            <th style="text-align: right; padding: 6px; font-size: 11px;">Kg</th>
                        @endfor
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($freshItems as $item)
                        <tr>
                            <td style="font-family: monospace; font-size: 12px;">
                                {{ $item->product?->material_number ?? '—' }}
                            </td>
                            <td>{{ $item->product?->name ?? '—' }}</td>

                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $bag = $item->getAttribute("bag_$i");
                                    $kg  = $item->getAttribute("kg_$i");
                                    $bagVal = ($bag !== null && $bag !== '' && (float)$bag > 0) ? number_format((float)$bag, 2) : '';
                                    $kgVal  = ($kg  !== null && $kg  !== '' && (float)$kg  > 0) ? number_format((float)$kg,  2) : '';
                                @endphp

                                <td style="text-align: right; font-variant-numeric: tabular-nums;">{{ $bagVal }}</td>
                                <td style="text-align: right; font-variant-numeric: tabular-nums;">{{ $kgVal }}</td>
                            @endfor

                            <td style="text-align: right; font-variant-numeric: tabular-nums;">
                                {{ number_format((float)($item->total_bag ?? 0), 2) }}
                            </td>
                            <td style="text-align: right; font-variant-numeric: tabular-nums;">
                                {{ number_format((float)($item->total_kg ?? 0), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + (10*2) + 2 }}" style="text-align:center; color: var(--text-muted); padding: 16px;">
                                Tidak ada item Fresh.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr style="font-weight: 700; background: #f5f7fc;">
                        <td colspan="{{ 2 + (10*2) }}">TOTAL FRESH</td>
                        <td style="text-align: right;">{{ $fmt($report->fresh_total_bag) }}</td>
                        <td style="text-align: right;">{{ $fmt($report->fresh_total_kg) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- =================== TABLE: FROZEN =================== --}}
        <div style="margin-bottom: 10px; font-weight: 800; letter-spacing: .02em;">TABEL FROZEN</div>
        <div style="overflow-x: auto;">
            <table class="table" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">Material Number</th>
                        <th style="min-width: 220px;">Nama Produk</th>

                        @for($i = 1; $i <= 10; $i++)
                            <th colspan="2" style="text-align: center; min-width: 120px;">{{ $i }}</th>
                        @endfor

                        <th style="text-align: right; min-width: 110px;">Total Bag</th>
                        <th style="text-align: right; min-width: 110px;">Total Kg</th>
                    </tr>
                    <tr style="background: #f5f7fc;">
                        <th></th>
                        <th></th>
                        @for($i = 1; $i <= 10; $i++)
                            <th style="text-align: right; padding: 6px; font-size: 11px;">Bag</th>
                            <th style="text-align: right; padding: 6px; font-size: 11px;">Kg</th>
                        @endfor
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($frozenItems as $item)
                        <tr>
                            <td style="font-family: monospace; font-size: 12px;">
                                {{ $item->product?->material_number ?? '—' }}
                            </td>
                            <td>{{ $item->product?->name ?? '—' }}</td>

                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $bag = $item->getAttribute("bag_$i");
                                    $kg  = $item->getAttribute("kg_$i");
                                    $bagVal = ($bag !== null && $bag !== '' && (float)$bag > 0) ? number_format((float)$bag, 2) : '';
                                    $kgVal  = ($kg  !== null && $kg  !== '' && (float)$kg  > 0) ? number_format((float)$kg,  2) : '';
                                @endphp

                                <td style="text-align: right; font-variant-numeric: tabular-nums;">{{ $bagVal }}</td>
                                <td style="text-align: right; font-variant-numeric: tabular-nums;">{{ $kgVal }}</td>
                            @endfor

                            <td style="text-align: right; font-variant-numeric: tabular-nums;">
                                {{ number_format((float)($item->total_bag ?? 0), 2) }}
                            </td>
                            <td style="text-align: right; font-variant-numeric: tabular-nums;">
                                {{ number_format((float)($item->total_kg ?? 0), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + (10*2) + 2 }}" style="text-align:center; color: var(--text-muted); padding: 16px;">
                                Tidak ada item Frozen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr style="font-weight: 700; background: #f5f7fc;">
                        <td colspan="{{ 2 + (10*2) }}">TOTAL FROZEN</td>
                        <td style="text-align: right;">{{ $fmt($report->frozen_total_bag) }}</td>
                        <td style="text-align: right;">{{ $fmt($report->frozen_total_kg) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection