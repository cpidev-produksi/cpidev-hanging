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
            @if($report->isApproved())
                <div style="padding: 14px 16px; background: #fafbff; border: 1px solid var(--card-border); border-radius: 10px;">
                    <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 5px;">Disetujui Oleh</div>
                    <div style="font-weight: 600; font-size: 13px;">{{ $report->approvedBy->name }}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">{{ $report->approved_at->format('d/m/Y H:i') }}</div>
                </div>
            @endif
        </div>

        {{-- Table --}}
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
                    @foreach($report->items as $item)
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
                    @endforeach
                </tbody>

                <tfoot>
                    <tr style="font-weight: 700; background: #f5f7fc;">
                        <td colspan="{{ 2 + (10*2) }}">TOTAL</td>
                        <td style="text-align: right;">{{ number_format((float)$report->total_bag, 2) }}</td>
                        <td style="text-align: right;">{{ number_format((float)$report->total_kg, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection