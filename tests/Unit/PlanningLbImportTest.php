<?php

namespace Tests\Unit;

use App\Imports\PlanningLbImport;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PlanningLbImportTest extends TestCase
{
    public function test_it_reads_operational_date_and_totals_from_schedule_rows(): void
    {
        $result = (new PlanningLbImport())->parseRows([
            ['A' => 'Hari/Tanggal :', 'D' => 'Rabu : 14 Oktober 2026', 'G' => null],
            ['A' => 'No', 'D' => 'NO SO', 'G' => 'EKOR'],
            ['A' => '1', 'G' => '2100'],
            ['A' => '2', 'G' => 1344],
            ['A' => 'GANTI SHIFT', 'G' => null],
        ]);

        $this->assertSame([
            'process_date' => '2026-10-14',
            'total_plan_chicken' => 3444,
            'total_plan_truck' => 2,
        ], $result);
    }

    public function test_it_rejects_a_sheet_without_valid_schedule_rows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PlanningLbImport())->parseRows([
            ['A' => 'Hari/Tanggal :', 'D' => 'Rabu : 14 Oktober 2026'],
        ]);
    }
}