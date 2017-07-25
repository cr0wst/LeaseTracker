<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/21/2017
 * Time: 1:17 PM
 */

namespace LeaseTracker\Repositories;


use Illuminate\Support\Collection;

/**
 * Implementations of this interface provide a way of working with MileEntry objects on a datasource.
 * 
 * @package LeaseTracker\Repositories
 */
interface MileEntryRepositoryInterface
{
    /**
     * Retrieve the mileage entries for a given vehicle id.
     *
     * @param int $id The ID of the vehicle to retrieve the mileage for.
     * @return Collection A collection of MileEntry objects for the vehicle.
     */
    function retrieveMileageForVehicle(int $id):Collection;

    /**
     * Delete the mile entries for the given vehicle id.
     * @param int $id The id of the vehicle for which the mile entries should be deleted.
     */
    function deleteMileageForVehicle(int $id);
}