<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 2:16 PM
 */

namespace LeaseTracker\Services\Calculation;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use LeaseTracker\DataTransfer\CostData;
use LeaseTracker\Vehicle;

class CalculationService implements CalculationServiceInterface
{
    
    public function buildCostInformation(Vehicle $vehicle, Collection $mileEntries):CostData
    {
        // This is me using CostData like it's a bean.  This is the moment where I was really contemplating going
        // with array(...).  However, there are some benefits here.  I'm able to pass the costData's properties into the other
        // methods that need them.

        // Maybe I've got too many helper methods, might be better to use a builder pattern here.
        $costData = new CostData;
        $costData->milesRemaining = $this->calculateMilesRemaining($vehicle, $mileEntries);
        $costData->milesPerMonth = $this->calculateMilesPerMonth($vehicle->total_allowable_mileage, $vehicle->months);
        $costData->monthsRemaining = $this->calculateMonthsRemaining(new Carbon($vehicle->start_date), $vehicle->months);
        $costData->predictedOverage = $this->calculatePredictedOverage($costData->milesPerMonth, $costData->monthsRemaining, $costData->milesRemaining);
        $costData->cost = $this->calculateCost($costData->predictedOverage, $vehicle->cost_per_mile);
        $costData->endDate = $this->calculateEndDate($vehicle->start_date, $vehicle->months);

        return $costData;
    }

    // A lot of these methods could probably be made private since they're really going to be specific to this individual
    // implementation.  In Java, we keep them protected because we can then use inheritence or packageing to test them.

    /**
     * Calculate the miles remaining on the lease.  Will determine the mile entry with the max number of miles and
     * subtract it from the vehicle's allocated miles (adding back in the starting mileage).  If this is above the
     * allocated number of miles, it will return the allocated number of miles.
     *
     * @param Vehicle $vehicle The vehicle in question.
     * @param Collection $mileEntries The collection of mileEntries.
     * @return int The miles remaining.
     */
    protected function calculateMilesRemaining(Vehicle $vehicle, Collection $mileEntries):int
    {
        // Note: This method and other methods are where I'm trying to learn a few things about how to handle what I would
        // normally do using method overloading.

        // In Java, I would provide calculateMilesRemaining as a method that takes either a Vehicle, or the individual parameters.
        // I haven't found any kind of best practices for this, so some of my methods lack the consistency I'd really desire to have.
        // I'm leaving this as a Vehicle hinted method because it needs multiple properties from the Vehicle object.

        // No assumptions on the order of Entries.  Ideally, it's sorted.
        $maximumMiles = $mileEntries->max('currentMileage');

        return min($vehicle->total_allowable_mileage + $vehicle->starting_mileage - $maximumMiles, $vehicle->total_allowable_mileage);
    }

    /**
     * Calculate your number of miles per month.  This is the division of $miles and $months.  It should always result
     * in an Integer, just based on how Leases are typically structured.
     *
     * @param int $miles The number of miles your lease allocates.
     * @param int $months The number of months for the term of your lease.
     * @return int The number of miles per month.
     * @throws \InvalidArgumentException if $months is equal to zero
     */
    protected function calculateMilesPerMonth(int $miles, int $months):int
    {
        if ($months > 0) {
            // Integer division appears to _not_ work like Python or C however, just based on how leases work
            // this shouldn't ever return a float.
            return $miles / $months;
        }

        // Need to throw InvalidArgumentException because a DivisionByError is not a _real_ exception.
        throw new \InvalidArgumentException('Attempted to Divide by Zero');
    }

    /**
     * Calculates the number of months remaining on the lease.  Uses Carbon date to find the difference in months.
     * The calculation does not show partial months, it will always return the ceil() of results.
     *
     * @param Carbon|string $startDate The start date as either a Carbon date object or a YYYY-MM-DD string.
     * @param int $months The number of months of the lease.
     * @return int The number of months remaining.
     * @throws \InvalidArgumentException if the $startDate is not of the correct type.
     */
    protected function calculateMonthsRemaining($startDate, int $months):int
    {
        // I really couldn't decide between having a Carbon hinted $startDate, or just a string... In my last job
        // we had strings _everywhere_ and it was harder to enforce type-safety.  PHP isn't statically typed, though,
        // so maybe it's ok!  I know we try to avoid instanceof checks whenever possible (in Java), is PHP
        // more forgiving of this?
        if ($startDate instanceof Carbon) {
            return $months - $startDate->diffInMonths(Carbon::now());
        } else if (is_string($startDate)) {
            return $months - (new Carbon($startDate))->diffInMonths(Carbon::now());
        } else {
            throw new \InvalidArgumentException('calculateMonthsRemaining first argument only accepts Carbon|string. Input was: '.$startDate);
        }
    }
    
    /**
     * Calculate the predicted overage based on the miles allowed per month, the months remaining, and the miles remaining.
     * The formula is the max of $milesPerMonth*$monthsRemaining - $milesRemaining or 0.  If you have negative overage
     * you have no overage.
     *
     * @param int $milesPerMonth The miles per month allowed on your lease (generally totalMiles/months)
     * @param int $monthsRemaining The months remaining on your lease.
     * @param int $milesRemaining The miles remaining on your lease.
     * @return int The number of miles you will be over if you stay at your initial milesPerMonth goal.
     */
    protected function calculatePredictedOverage(int $milesPerMonth, int $monthsRemaining, int $milesRemaining):int
    {
        // If you're negative miles over, you're not over.
        return max($milesPerMonth*$monthsRemaining - $milesRemaining, 0);
    }

    /**
     * Calculate the cost of the overage using the Vehicle's cost per mile and the predicted number of miles over.
     *
     * @param int $predictedOverage The predicted overage.
     * @param float $costPerMile The cost per mile.
     * @return float The predicted cost.
     */
    protected function calculateCost(int $predictedOverage, float $costPerMile):float
    {
        // Java has classes like BigDecimal to help with precision.  I'm going to convert the float to an int first
        // to try and keep precision.  Maybe this helps, maybe it doesn't.  I'd love to know a better way to do this.

        // Usually precision issues in Java only crop up when doing crazy division, this could be a non-issue.
        $centsPerMile = $costPerMile*100;
        return $predictedOverage*$centsPerMile/100;
    }

    /**
     * @param Carbon|string $startDate The start date as either a Carbon date object or a YYYY-MM-DD string.
     * @param int $months The number of months of the lease.
     * @return string A string representation of the date in YYYY-MM-DD.
     * @throws \InvalidArgumentException if the $startDate is not of the correct type.
     */
    protected function calculateEndDate($startDate, int $months):string
    {
        if ($startDate instanceof Carbon) {
            return $startDate->addMonths($months)->toDateString();
        } else if (is_string($startDate)) {
            return (new Carbon($startDate))->addMonths($months)->toDateString();
        } else {
            throw new \InvalidArgumentException('calculateEndDate first argument only accepts Carbon|string. Input was: '.$startDate);
        }
    }
}