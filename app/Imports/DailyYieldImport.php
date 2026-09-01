<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DailyYieldImport implements ToCollection
{
    private const HEADER_ROWS = 3;
 
    protected array $rows = [];
 
    public function collection(Collection $rows): void
    {
        foreach ($rows->skip(self::HEADER_ROWS) as $row) {
            $plant = trim((string) ($row[0] ?? ''));
 
            if ($plant === '') {
                continue; // baris kosong, lewati
            }
 
            if (strtoupper($plant) === 'AVERAGE') {
                continue; // baris rata-rata nasional, tidak disimpan
            }
 
            $this->rows[] = [
                'plant' => $plant,
                'yield_titik_0' => $this->numeric($row[1] ?? null),
                'ach_yield_h0' => $this->numeric($row[2] ?? null),
                'yield_h1' => $this->numeric($row[3] ?? null),
                'ach_yield_h1' => $this->numeric($row[4] ?? null),
                'yield_h2' => $this->numeric($row[5] ?? null),
                'ach_yield_h2' => $this->numeric($row[6] ?? null),
                'yield_h3' => $this->numeric($row[7] ?? null),
                'ach_yield_h3' => $this->numeric($row[8] ?? null),
                'yield_h4' => $this->numeric($row[9] ?? null),
                'ach_yield_h4' => $this->numeric($row[10] ?? null),
                'yield_fg' => $this->numeric($row[11] ?? null),
                'total_fg_bp' => $this->numeric($row[12] ?? null),
                'sumpo' => $this->numeric($row[13] ?? null),
                'lost' => $this->numeric($row[14] ?? null),
                'tanggal_update_terakhir' => $this->parseDate($row[15] ?? null),
            ];
        }
    }
 
    /**
     * Baris hasil parsing, siap di-insert ke tabel daily_yields.
     */
    public function getRows(): array
    {
        return $this->rows;
    }
 
    protected function numeric($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
 
        // Formula error seperti #DIV/0! (muncul di template kosong) dianggap kosong
        if (is_string($value) && str_starts_with(trim($value), '#')) {
            return null;
        }
 
        return is_numeric($value) ? (float) $value : null;
    }
 
    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }
 
        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            }
 
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
