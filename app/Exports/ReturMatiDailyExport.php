<?php

namespace App\Exports;

use App\Models\MonitorControl;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReturMatiDailyExport implements FromCollection, WithHeadings, WithStyles
{
    public function __construct(private string $date) {}

    public function headings(): array
    {
        return [
            'Tanggal Operasional',
            'Lokasi',
            'Shift',
            'No Truk',
            'Report Code',
            'Plat Nomor',
            'Ayam Mati',
            'Ayam Retur',
        ];
    }

    public function collection(): Collection
    {
        $rows = MonitorControl::query()
            ->with(['plateNumber','hangingForm'])
            ->whereDate('process_date', $this->date)
            ->orderBy('location')
            ->orderBy('shift')
            ->orderByRaw('CAST(truck_no as UNSIGNED)')
            ->get();

        $data = $rows->map(function ($mc) {
            return [
                'Tanggal Operasional' => $mc->process_date?->toDateString() ?? $this->date,
                'Lokasi' => $mc->location,
                'Shift' => strtoupper((string)$mc->shift),
                'No Truk' => $mc->truck_no,
                'Report Code' => $mc->report_code,
                'Plat Nomor' => $mc->plateNumber?->plate_number ?? '—',
                'Ayam Mati' => (int)($mc->hangingForm?->dead_count ?? 0),
                'Ayam Retur' => (int)($mc->hangingForm?->retur_count ?? 0),
            ];
        });

        // Tambahkan 1 baris total di bawah
        $totalDead  = $data->sum('Ayam Mati');
        $totalRetur = $data->sum('Ayam Retur');

        $data->push([
            'Tanggal Operasional' => 'TOTAL',
            'Lokasi' => '',
            'Shift' => '',
            'No Truk' => '',
            'Report Code' => '',
            'Plat Nomor' => '',
            'Ayam Mati' => $totalDead,
            'Ayam Retur' => $totalRetur,
        ]);

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Bold header
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        // Auto width
        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}