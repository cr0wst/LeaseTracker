<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/21/2017
 * Time: 1:17 PM
 */

namespace LeaseTracker\Repositories;

use Illuminate\Support\Collection;
use LeaseTracker\MileEntry;

class MileEntryRepository implements MileEntryRepositoryInterface
{
    // TODO: Refactor this to use an injectable model like the VehicleRepository.

    public function retrieveMileageForVehicle(int $id):Collection
    {
        return MileEntry::where('carId', $id)->orderBy('currentMileage', 'desc')->get();
    }
    
    function deleteMileageForVehicle(int $id)
    {
        MileEntry::where('carId', $id)->delete();
    }
}