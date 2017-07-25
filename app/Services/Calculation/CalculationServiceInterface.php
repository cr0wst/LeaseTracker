<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 2:03 PM
 */

namespace LeaseTracker\Services\Calculation;

use Illuminate\Support\Collection;
use LeaseTracker\DataTransfer\CostData;
use LeaseTracker\Vehicle;

/**
 * Provides a service for calculating cost summary information.
 * @package LeaseTracker\Services\Cost
 */
interface CalculationServiceInterface
{
    /**
     * Build the cost information for the vehicle.  Cost information could include things like, predicted miles over,
     * the cost of overage, the miles remaining on the lease, the months remaining, etc.
     *
     * @param Vehicle $vehicle The vehicle to calculate the costs for.
     * @param Collection $mileEntries A collection of MileEntry objects for working with the mileage.
     * @return CostData Transfer object containing the resulting information.
     */
    function buildCostInformation(Vehicle $vehicle, Collection $mileEntries):CostData;
}