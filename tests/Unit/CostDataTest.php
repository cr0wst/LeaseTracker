<?php

namespace Tests\Unit;

use LeaseTracker\DataTransfer\CostData;
use Tests\TestCase;

/**
 * Unit tests for the CostData class.
 *
 * @package Tests\Unit
 */
class CostDataTest extends TestCase
{
    /**
     * Test to make sure that the formatter is working correctly.
     */
    public function testCostFormattedCorrectly() {
        $costData = new CostData();
        $cost = 1234.56;
        $costData->cost = $cost;

        // Normal Usage
        $this->assertEquals("$ 1,234.56", $costData->getFormattedCost());

        // Uninitialized
        $this->assertEquals("$ 0.00", (new CostData())->getFormattedCost());

        // Negative Cost
        $costData->cost = -1*$cost;
        $this->assertEquals("$ -1,234.56", $costData->getFormattedCost());
    }
}
