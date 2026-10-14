<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PlanningLbImport
{
    /**
     * Read the active worksheet directly from the uploaded temporary file.
     *
     * The uploaded file is never copied to application storage.
     *
     * @return array{process_date: string, total_plan_chicken: int, total_plan_truck: int}
     */
    public function read(UploadedFile $file): array
    {
        $reader = IOFactory::createReaderForFile($file->getPathname());
        $reader->setReadDataOnly(true);
        $worksheet = $reader->load($file->getPathname())->getActiveSheet();

        return $this->parseRows($worksheet->toArray(null, true, true, true));
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array{process_date: string, total_plan_chicken: int, total_plan_truck: int}
     */
    public function parseRows(array $rows): array
    {
        $processDate = null;
        $totalChicken = 0;
        $totalTruck = 0;

        foreach ($rows as $row) {
            $label = strtoupper(trim((string) ($row['A'] ?? '')));

            if ($processDate === null && str_contains($label, 'HARI/TANGGAL')) {
                $processDate = $this->parseOperationalDate($row['D'] ?? null);
            }

            $rowNumber = $row['A'] ?? null;
            $chicken = $row['G'] ?? null;

            if ($this->isInteger($rowNumber) && $this->isInteger($chicken) && (int) $chicken >= 0) {
                $totalChicken += (int) $chicken;
                $totalTruck++;
            }
        }

        if ($processDate === null) {
            throw new InvalidArgumentException('Tanggal operasional tidak ditemukan pada sheet aktif.');
        }

        if ($totalTruck === 0) {
            throw new InvalidArgumentException('Tidak ada data jadwal yang valid pada sheet aktif.');
        }

        return [
            'process_date' => $processDate,
            'total_plan_chicken' => $totalChicken,
            'total_plan_truck' => $totalTruck,
        ];
    }

    private function parseOperationalDate(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $months = [
            'JANUARI' => 1, 'FEBRUARI' => 2, 'MARET' => 3, 'APRIL' => 4,
            'MEI' => 5, 'JUNI' => 6, 'JULI' => 7, 'AGUSTUS' => 8,
            'SEPTEMBER' => 9, 'OKTOBER' => 10, 'NOVEMBER' => 11, 'DESEMBER' => 12,
        ];

        if (! preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/u', $value, $matches)) {
            return null;
        }

        $month = $months[strtoupper($matches[2])] ?? null;

        if ($month === null) {
            return null;
        }

        return Carbon::create((int) $matches[3], $month, (int) $matches[1])->toDateString();
    }

    private function isInteger(mixed $value): bool
    {
        return is_numeric($value) && (float) $value === (float) (int) $value;
    }
}