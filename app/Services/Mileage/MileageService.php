<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 1:38 PM
 */

namespace LeaseTracker\Services\Mileage;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use LeaseTracker\Http\Requests\CreateMileageFormRequest;
use LeaseTracker\MileEntry;
use LeaseTracker\Repositories\MileEntryRepositoryInterface;
use LeaseTracker\Repositories\VehicleRepositoryInterface;
use LeaseTracker\Vehicle;


/**
 * Service for working with mileage entries.
 * @package LeaseTracker\Services\Mileage
 */
class MileageService implements MileageServiceInterface
{
    const VEHICLE_NOT_FOUND_MESSAGE = 'The selected Vehicle was not found or is no longer available.';
    const MILEAGE_UPDATED_MESSAGE = 'Mileage has been updated.';
    /**
     * @var VehicleRepositoryInterface $vehicleRepository Reference to the VehicleRepositoryInterface
     */
    private $vehicleRepository;

    /**
     * @var MileEntryRepositoryInterface $mileEntryRepository Reference to the MileEntryRepositoryInterface
     */
    private $mileEntryRepository;

    /**
     * MileageService constructor.
     * @param VehicleRepositoryInterface $vehicleRepository The vehicle repository implementation for injection.
     * @param MileEntryRepositoryInterface $mileEntryRepository The mile entry repository implementation for injection.
     */
    public function __construct(VehicleRepositoryInterface $vehicleRepository, MileEntryRepositoryInterface $mileEntryRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->mileEntryRepository = $mileEntryRepository;
    }

    public function buildCreatePage(int $vehicleId)
    {
        try {
            $vehicle = $this->vehicleRepository->retrieveVehicle($vehicleId);
        } catch (ModelNotFoundException $exception) {
            return \Redirect::route('vehicle.index')->with('message', self::VEHICLE_NOT_FOUND_MESSAGE);
        }
        $mileEntries = $this->mileEntryRepository->retrieveMileageForVehicle($vehicleId);

        return view('pages/mileage/create', ['vehicle' => $vehicle, 'mileEntries' => $mileEntries]);
    }

    public function storeMileage(CreateMileageFormRequest $request, int $id)
    {
        // Check to see if the vehicle exists to avoid dead mile entries.
        if (Vehicle::find($id)->exists()) {
            $mileEntry = new MileEntry;
            // I know what you're thinking.. this is a prime candidate for form injection.  You could totally add mileage
            // to somebody else's car because $id is part of the URL.  I'm still under the assumption that only one user
            // will really be using this thing, so I'm not going to secure it.

            // If I were going to secure it.. I would probably check to see if the user was allowed to post to this car.
            $mileEntry->carId = $id;
            $mileEntry->date = $request->get('date');
            $mileEntry->currentMileage = $request->get('currentMileage');
            $mileEntry->save();

            return \Redirect::route('mileage.create', $id)->with('message', self::MILEAGE_UPDATED_MESSAGE );
        } else {
            return \Redirect::route('vehicle.index')->with('message', self::VEHICLE_NOT_FOUND_MESSAGE);
        }
    }
}