<?php

namespace LeaseTracker\Services\Vehicle;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use LeaseTracker\Http\Requests\CreateVehicleFormRequest;
use LeaseTracker\Repositories\MileEntryRepositoryInterface;
use LeaseTracker\Repositories\VehicleRepositoryInterface;
use LeaseTracker\Services\Calculation\CalculationServiceInterface;
use LeaseTracker\Services\VehicleImage\VehicleImageServiceInterface;
use LeaseTracker\Vehicle;

/**
 * Provides service implementations for managing vehicles.
 * @package LeaseTracker\Vehicle
 */
class VehicleService implements VehicleServiceInterface
{
    const VEHICLE_NOT_FOUND_MESSAGE = 'The selected Vehicle was not found or is no longer available.';

    const VEHICLE_CREATED_MESSAGE = 'Your vehicle has been created!';

    const VEHICLE_SAVE_ERROR_MESSAGE = 'There was an error saving your changes.';

    const VEHICLE_UPDATED_SUCCESSFULLY_MESSAGE = 'Your vehicle has been updated!';

    const VEHICLE_CANT_BE_DELETED_MESSAGE = 'Your vehicle could not be deleted at this time.';

    const VEHICLE_DELETED_MESSAGE = 'Your vehicle has been deleted!';


    // If phpDocumentor is anything like JavaDoc it will inherit documentation from the interface.

    /**
     * @var CalculationServiceInterface $calculationService a reference to the CalculationServiceInterface
     */
    private $calculationService;

    /**
     * @var VehicleImageServiceInterface $vehicleImageService a reference to the VehicleImageServiceInterface
     */
    private $vehicleImageService;

    /**
     * @var VehicleRepositoryInterface $vehicleRepository a reference to the VehicleRepositoryInterface
     */
    private $vehicleRepository;

    /**
     * @var MileEntryRepositoryInterface $mileEntryRepository a reference to the MileEntryRepositoryInterface
     */
    private $mileEntryRepository;

    /**
     * VehicleService constructor.
     * @param CalculationServiceInterface $calculationService Injectable calculation service
     * @param VehicleImageServiceInterface $vehicleImageService Injectable vehicle image service.
     * @param VehicleRepositoryInterface $vehicleRepository Injectable vehicle repository.
     * @param MileEntryRepositoryInterface $mileEntryRepository Injectable mile entry repository.
     */
    public function __construct(CalculationServiceInterface $calculationService,
                                VehicleImageServiceInterface $vehicleImageService, VehicleRepositoryInterface $vehicleRepository,
                                MileEntryRepositoryInterface $mileEntryRepository)
    {
        $this->calculationService = $calculationService;
        $this->vehicleImageService = $vehicleImageService;
        $this->vehicleRepository = $vehicleRepository;
        $this->mileEntryRepository = $mileEntryRepository;
    }

    public function buildIndexPage()
    {
        // Note: Not passing parameters here does _not_ promote singleton services, however I'm making the assumption
        // that there is only one user accessing the application.  In the future I could do a registration method and then
        // I would need to pass the state around.
        $vehicleList = $this->vehicleRepository->retrieveVehicleList();

        // Each vehicle has additional information that is not stored in the datasource.  Loop through each
        // vehicle and attach the information.
        foreach($vehicleList as $vehicle) {
            // Attach the calculations for the vehicle.
            $mileage = $this->mileEntryRepository->retrieveMileageForVehicle($vehicle->id);
            $vehicle['costData'] = $this->calculationService->buildCostInformation($vehicle, $mileage);
        }

        return view('pages/vehicle/index', ['vehicles' => $vehicleList]);
    }

    public function buildShowPage(int $id)
    {
        // I haven't written a lot of try/catch where I return as part of the catch.  Ours were usually for throwing
        // custom exceptions that the container's error handler would take care of.  However, I kind of like the idea
        // for using them for error handling.

        // It brings in the question whether or not this makes the code less readable.  Would it be better to extend
        // the try around the whole block?
        try {
            $vehicle = $this->vehicleRepository->retrieveVehicle($id);
            // If a different exception (other than ModelNotFoundException) is thrown, we can let the global handler take it.
        } catch (ModelNotFoundException $exception) {
            return Redirect::route('vehicle.index')->with('message', self::VEHICLE_NOT_FOUND_MESSAGE);
        }

        $mileEntries = $this->mileEntryRepository->retrieveMileageForVehicle($id);

        // Calculate the difference between each consecutive mile entry and attach to the mileEntries object

        // Mile Entries are already sorted descending by mileage from the repo.
        // count - 1 because the last one doesn't have a difference.
        for ($i = 0; $i < count($mileEntries) - 1; $i++) {
            $mileEntries[$i]->differenceToPrevious = $mileEntries[$i]->currentMileage - $mileEntries[$i + 1]->currentMileage;
        }

        // Attach the calculation information to the vehicle.
        $vehicle['costData'] = $this->calculationService->buildCostInformation($vehicle, $mileEntries);

        return view('pages/vehicle/show', ['vehicle' => $vehicle, 'mileEntries' => $mileEntries]);
    }

    public function buildEditPage(int $id)
    {
        try {
            $vehicle = $this->vehicleRepository->retrieveVehicle($id);
        } catch (ModelNotFoundException $exception) {
            return Redirect::route('vehicle.index')->with('message', self::VEHICLE_NOT_FOUND_MESSAGE);
        }

        return view('pages/vehicle/edit', ['vehicle' => $vehicle]);
    }

    public function buildCreatePage()
    {
        return view('pages/vehicle/create');
    }

    public function storeVehicle(CreateVehicleFormRequest $request)
    {
        $vehicle = $this->buildVehicleFromRequest($request);
        $this->vehicleRepository->save($vehicle);

        return Redirect::route('vehicle.show', array($vehicle->id))->with('message', self::VEHICLE_CREATED_MESSAGE);
    }

    public function updateVehicle(CreateVehicleFormRequest $request, int $id)
    {
        $oldVehicle = $this->vehicleRepository->retrieveVehicle($id);
        $updatedVehicle = $this->buildVehicleFromRequest($request, $oldVehicle);

        try {
            // Should this be saveOrFail to ensure an exception gets thrown, or should we check the boolean returned from save?
            $this->vehicleRepository->save($updatedVehicle);
        } catch (\Exception $exception) {
            return Redirect::back()->withInput()->withErrors(['message' => self::VEHICLE_SAVE_ERROR_MESSAGE]);
        }
            return Redirect::route('vehicle.show', array($oldVehicle->id))->with('message', self::VEHICLE_UPDATED_SUCCESSFULLY_MESSAGE);
    }

    /**
     * Build the vehicle model from the passed in request and (optionally) another vehicle object.  This method is used
     * for both the save (to get a new vehicle) and as the update, by returning an updated version of the vehicle.
     *
     * @param CreateVehicleFormRequest $request The request from the vehicle form.
     * @param Vehicle $vehicle the vehicle object on which the changes should be merged to.  If not passed in, null is used
     * and a new vehicle object will be created.
     *
     * @return Vehicle the final vehicle after creation/merge.
     */
    protected function buildVehicleFromRequest(CreateVehicleFormRequest $request, Vehicle $vehicle = null):Vehicle
    {
        if (is_null($vehicle)) {
            $vehicle = new Vehicle;
        }

        // We only want to hit the image service if the make and model has changed, saves an external call.
        if ($vehicle->make_model !== $request->get('make_model')) {
            $vehicle->make_model = $request->get('make_model');
            $vehicle->image_url = $this->vehicleImageService->retrieveImageByName($vehicle->make_model);
        }

        // We could dump the request into the Vehicle, but I kind of feel like this is more explicit.
        $vehicle->name = $request->get('name');
        $vehicle->cost_per_mile = $request->get('cost_per_mile');
        $vehicle->total_allowable_mileage = $request->get('total_allowable_mileage');
        $vehicle->months = $request->get('months');
        $vehicle->starting_mileage = $request->get('starting_mileage');
        $vehicle->start_date = $request->get('start_date');

        // Get an image for the vehicle from the image service.
        return $vehicle;
    }

    public function destroyVehicle(int $id)
    {
        // Delete the Vehicle.  If it fails we want to prevent deleting the mileage, so we'll catch any exceptions
        // and move on.
        try {
            $this->vehicleRepository->delete($id);
        } catch (\Exception $exception) {
            return \Redirect::route('vehicle.index')->with('message', self::VEHICLE_CANT_BE_DELETED_MESSAGE);
        }

        // Delete all the mileage information for the Vehicle
        $this->mileEntryRepository->deleteMileageForVehicle($id);

        return \Redirect::route('vehicle.index')->with('message', self::VEHICLE_DELETED_MESSAGE);
    }
}