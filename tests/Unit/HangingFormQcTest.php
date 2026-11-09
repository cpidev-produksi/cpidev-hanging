<?php

namespace Tests\Unit;

use App\Models\HangingForm;
use PHPUnit\Framework\TestCase;

class HangingFormQcTest extends TestCase
{
    public function test_it_reports_missing_qc_fields(): void
    {
        $form = new HangingForm([
            'basket_condition' => 'kering',
            'feather_condition' => 'basah',
        ]);

        $this->assertSame(['Platform Truck'], $form->missingQcFields());
        $this->assertFalse($form->hasCompleteQc());
    }

    public function test_it_marks_qc_complete_when_all_conditions_are_filled(): void
    {
        $form = new HangingForm([
            'basket_condition' => 'kering',
            'truck_platform_condition' => 'bak_kering',
            'feather_condition' => 'basah',
        ]);

        $this->assertSame([], $form->missingQcFields());
        $this->assertTrue($form->hasCompleteQc());
    }
}
