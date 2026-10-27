<?php

namespace Tests\Unit;

use App\Models\EviscerationEfficiencyItem;
use PHPUnit\Framework\TestCase;

class EviscerationEfficiencyItemTest extends TestCase
{
    public function test_it_calculates_vent_and_nouva_percentages_from_sampling_count(): void
    {
        $item = new EviscerationEfficiencyItem([
            'sampling_count' => 80,
            'vent_not_torn' => 72,
            'viscera_not_removed' => 4,
        ]);

        $this->assertSame(90.0, $item->vent_percent);
        $this->assertSame(5.0, $item->viscera_percent);
    }

    public function test_it_summarizes_the_three_requested_count_fields(): void
    {
        $items = [
            new EviscerationEfficiencyItem(['vent_not_torn' => 8, 'viscera_not_removed' => 2, 'grit_findings' => 4]),
            new EviscerationEfficiencyItem(['vent_not_torn' => 10, 'viscera_not_removed' => 6, 'grit_findings' => 8]),
        ];

        $this->assertSame([
            'vent_not_torn' => ['total' => 18, 'average' => 9],
            'viscera_not_removed' => ['total' => 8, 'average' => 4],
            'grit_findings' => ['total' => 12, 'average' => 6],
        ], EviscerationEfficiencyItem::summarize($items));
    }
}
