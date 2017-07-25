<?php

namespace Tests\Unit;

use Carbon\Carbon;
use LeaseTracker\MileEntry;
use LeaseTracker\Services\Calculation\CalculationService;
use LeaseTracker\Services\Calculation\CalculationServiceInterface;
use LeaseTracker\Vehicle;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Unit testing for the CalculationService.
 *
 * Class CalculationServiceTest
 * @package Tests\Unit
 */
class CalculationServiceTest extends TestCase
{
    /**
     * @var CalculationServiceInterface $service reference to the CalculationService to be tested.
     */
    private $service;

    /**
     * Setup the test environment with the CalculationService.
     */
    public function setUp()
    {
        parent::setUp();
        $this->service = new CalculationService;
    }
    
    // This is another area where I'm not sure what the PHP community would do.  It's common in Java to leave methods
    // as default or protected so that you can put your tests in the same package (namespace) and have access to them.
    // Reading online it seems that you should only really concern yourself with exercising public methods, and let your
    // conditions push the logic into your protected/private methods.
    
    // I don't necessarily agree with this, but it could just be self-ignorance. Protected methods are inherited and by
    // not testing the protected methods correctly, you're risking the implementing class having errors because of an
    // inherited method.  Counter-point: implementing class should have its own tests for these protected methods and
    // methods not intended to be used by a public API via inheritence should be declared private.

    /**
     * Tests the Build Cost Information under "normal" circumstances.
     */
    public function testBuildCostInformation()
    {
        $vehicle = factory(Vehicle::class)->make();
        $mileEntries = factory(MileEntry::class, 10)->make();
        
        // Manually calculate each piece
        $milesRemaining = min($vehicle->total_allowable_mileage + $vehicle->starting_mileage - $mileEntries->max('currentMileage'), $vehicle->total_allowable_mileage);

        // Need to cast to an integer here because of the method hinting.
        $milesPerMonth = (int) ($vehicle->total_allowable_mileage/$vehicle->months);

        $monthsRemaining = $vehicle->months - (new Carbon($vehicle->start_date))->diffInMonths(Carbon::now());
        $predictedOverage = max($milesPerMonth*$monthsRemaining - $milesRemaining, 0);

        // Multiplying and dividing by 100 prevents floating point errors.
        $calculatedCost = $vehicle->cost_per_mile*100*$predictedOverage/100;

        $endDate = (new Carbon($vehicle->start_date))->addMonths($vehicle->months)->toDateString();


        $costData = $this->service->buildCostInformation($vehicle, $mileEntries);
        
        $this->assertEquals($milesRemaining, $costData->milesRemaining);
        $this->assertEquals($milesPerMonth, $costData->milesPerMonth);
        $this->assertEquals($predictedOverage, $costData->predictedOverage);
        $this->assertEquals($calculatedCost, $costData->cost);
        $this->assertEquals($endDate, $costData->endDate);
    }

    /**
     * Sets months to zero on the vehicle object and calculates the cost.
     *
     * @expectedException \InvalidArgumentException coming from the calculateMilesPerMonth method.
     */
    public function testCostInformationWithZeroMonths()
    {
        $this->expectException(\InvalidArgumentException::class);
        $vehicle = factory(Vehicle::class)->make();
        $vehicle->months = 0;

        $mileEntries = factory(MileEntry::class, 5)->make();

        $this->service->buildCostInformation($vehicle, $mileEntries);
    }

    /**
     * Uses reflection to expose the calculateMonthsRemaining method in order to pass a non-Carbon start date.
     * During normal operation the buildCostInformation will convert the Vehicle's date to a Carbon object.
     * While reflection is generally not something I like to do, coverage of this method is crucial for class
     * inheritance.
     */
    public function testCalculateMonthsRemainingWithString()
    {
        // Maybe the calculateMonthsRemaining method needs to be refactored instead of testing something that can't
        // be reached without reflection.  Could be an instance of YAGNI.  Could also make the method private to prevent
        // the concern with inheritance.
        $vehicle = factory(Vehicle::class)->make();
        $vehicle->start_date = "2017-01-01";

        $reflectionMethod = $this->getMonthsRemainingMethod();
        $monthsRemaining = $vehicle->months - (new Carbon($vehicle->start_date))->diffInMonths(Carbon::now());
        $monthsRemainingFromService = $reflectionMethod->invokeArgs($this->service, array($vehicle->start_date, $vehicle->months));

        $this->assertEquals($monthsRemaining, $monthsRemainingFromService);
    }

    /**
     * Uses reflection to expose the calculateMonthsRemaining method in order to pass a non-Carbon and non-String
     * start date.
     *
     * @expectedException \InvalidArgumentException if the $startDate is not a Carbon or string.
     */
    public function testCalculateMonthsRemainingInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);

        $reflectionMethod = $this->getMonthsRemainingMethod();
        $reflectionMethod->invokeArgs($this->service, array(20170101, 12));
    }

    /**
     * Calculate the end date using a Carbon instance instead of a string start date.
     */
    public function testCalculateEndDateWithCarbon() {
        $vehicle = factory(Vehicle::class)->make();

        $reflectionMethod = $this->getEndDateMethod();

        $endDate = (new Carbon($vehicle->start_date))->addMonths($vehicle->months)->toDateString();
        $endDateFromService = $reflectionMethod->invokeArgs($this->service, array(new Carbon($vehicle->start_date), $vehicle->months));

        $this->assertEquals($endDate, $endDateFromService);
    }

    /**
     * Test that the CalculateEndDate method can handle arguments that are not Carbon or string.
     *
     * @expectedException \InvalidArgumentException if the $startDate is not a Carbon or string.
     */
    public function testCalculateEndDateInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);

        $reflectionMethod = $this->getEndDateMethod();
        $reflectionMethod->invokeArgs($this->service, array(20170101, 12));
    }

    /**
     * Method for gaining access to the calculateMonthsRemaining method using reflection.
     *
     * @return ReflectionMethod The method to call.
     */
    private function getMonthsRemainingMethod() {
        $reflectionMethod = new ReflectionMethod(CalculationService::class, 'calculateMonthsRemaining');
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod;
    }

    /**
     * Method for gaining access to the calculateEndDate method using reflection.
     *
     * @return ReflectionMethod The method to call.
     */
    private function getEndDateMethod() {
        $reflectionMethod = new ReflectionMethod(CalculationService::class, 'calculateEndDate');
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod;
    }
}
