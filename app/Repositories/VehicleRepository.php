<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/21/2017
 * Time: 1:17 PM
 */

namespace LeaseTracker\Repositories;

use Illuminate\Support\Collection;
use LeaseTracker\Vehicle;


/**
 * Serves as a repository for getting vehicles from a datasource.
 * 
 * @package LeaseTracker\Repositories
 */
class VehicleRepository implements VehicleRepositoryInterface
{

    // I was playing around with DI in this class.  It's a little different than the MileEntryRepository in that regard.
    /**
     * @var Vehicle $model Model instance.
     */
    private $model;

    /**
     * VehicleRepository constructor.
     * @param Vehicle $model The Vehicle model to inject.
     */
    public function __construct(Vehicle $model)
    {
        $this->model = $model;
    }

    public function retrieveVehicleList():Collection
    {
        return $this->model->all();
    }

    public function retrieveVehicle(int $id):Vehicle
    {
        return $this->model->findOrFail($id);
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function save(Vehicle $vehicle)
    {
        return $vehicle->save();
    }
}