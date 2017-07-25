<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/21/2017
 * Time: 1:16 PM
 */

namespace LeaseTracker\Repositories;

use Illuminate\Support\Collection;
use LeaseTracker\Vehicle;

/**
 * Implementations of this interface provide a way of working with Vehicle objects retrieved from a datasource.
 * 
 * @package LeaseTracker\Repositories
 */
interface VehicleRepositoryInterface
{
    /**
     * Retrieve all of the vehicles from the database.
     *
     * @return Collection The collection of vehicles.
     */
    function retrieveVehicleList():Collection;

    /**
     * Retrieve the Vehicle from the datasource.
     * @param int $id The ID for the Vehicle to retrieve.
     * @return Vehicle The specified vehicle object
     */
    function retrieveVehicle(int $id):Vehicle;

    /**
     * Retrieve the Vehicle from the datasource and delete it.
     * @param int $id The vehicle id to delete.
     * @return mixed
     */
    function delete(int $id);

    /**
     * Save the vehicle to the datasource.
     * @param Vehicle $vehicle The vehicle to save;
     * @return mixed
     */
    function save(Vehicle $vehicle);
}