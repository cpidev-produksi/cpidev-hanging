<?php

namespace App\Exports;

use App\Models\MonitorControl;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReturMatiSummaryExport implements FromCollection, WithHeadings
{
    public function __construct(private string $start, private string $end) {}

    public function headings(): array
    {
        return ['Tanggal Operasional', 'Jumlah Truk', 'Ayam Mati', 'Ayam Retur'];
    }

    public function collection(): Collection
    {
        $rows = MonitorControl::query()
            ->with('hangingForm')
            ->whereBetween('process_date', [$this->start, $this->end])
            ->get();

        $data = $rows->groupBy(fn($mc) => $mc->process_date?->toDateString() ?? '—')
            ->map(function ($items, $date) {
                $dead  = $items->sum(fn($mc) => (int)($mc->hangingForm?->dead_count ?? 0));
                $retur = $items->sum(fn($mc) => (int)($mc->hangingForm?->retur_count ?? 0));
                return [
                    'Tanggal Operasional' => $date,
                    'Jumlah Truk' => $items->count(),
                    'Ayam Mati' => $dead,
                    'Ayam Retur' => $retur,
                ];
            })
            ->sortKeys()
            ->values();

        // optional total row
        $data->push([
            'Tanggal Operasional' => 'TOTAL',
            'Jumlah Truk' => $data->sum('Jumlah Truk'),
            'Ayam Mati' => $data->sum('Ayam Mati'),
            'Ayam Retur' => $data->sum('Ayam Retur'),
        ]);

        return $data;
    }
}